<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Category;
use App\Models\Review;
use App\Models\Comment;
use App\Models\WatchHistory;
use App\Models\Episode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MovieController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->input('search', ''));
        $categoryId = $request->input('category');
        $status = $request->input('status');
        $country = $request->input('country');
        $type = $request->input('type');
        $minRating = $request->input('min_rating');
        
        $query = Movie::with('category');
        
        // Search
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('director', 'LIKE', "%{$search}%")
                  ->orWhere('actors', 'LIKE', "%{$search}%")
                  ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }
        
        // Filters
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }
        
        if ($status) {
            $query->where('status', $status);
        } else {
            $query->where('status', '!=', 'Chiếu rạp');
        }
        
        if ($country) {
            $query->where('country', $country);
        }
        
        if ($type) {
            $query->where('type', $type);
        }
        
        if ($minRating) {
            $query->where('rating', '>=', $minRating);
        }
        
        // Pagination
        $movies = $query->orderBy('created_at', 'desc')->paginate(12);
        
        // Add episode count for phim bộ
        $movies->each(function($movie) {
            if ($movie->type === 'phimbo') {
                $movie->episode_count = $movie->episodes()->count();
            }
        });
        
        $categories = Category::orderBy('name')->get();
        $countries = Movie::select('country')
            ->whereNotNull('country')
            ->where('country', '!=', '')
            ->distinct()
            ->orderBy('country')
            ->pluck('country');
        
        // Favorites
        $favorites = [];
        if (Auth::check()) {
            $favorites = WatchHistory::where('user_id', Auth::id())
                ->where('favorite', true)
                ->pluck('movie_id')
                ->toArray();
        }
        
        return view('movie.index', compact(
            'movies',
            'categories',
            'countries',
            'search',
            'categoryId',
            'status',
            'country',
            'type',
            'minRating',
            'favorites'
        ));
    }
    
    public function watch(Request $request, $id)
    {
        $movie = Movie::with(['category', 'episodes' => function($q) {
            $q->orderBy('episode_number');
        }])->findOrFail($id);
        
        // Check login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để xem phim!');
        }
        
        $user = Auth::user();
        
        // Check access level
        $movieLevel = $movie->level ?? 'Free';
        if (!$this->checkMovieAccess($user, $movieLevel)) {
            $subscriptionName = $user->subscription->name ?? 'Free';
            return redirect()->route('movies.index')
                ->with('error', "Phim này yêu cầu gói {$movieLevel}. Gói hiện tại của bạn: {$subscriptionName}");
        }
        
        // Save watch history
        WatchHistory::updateOrCreate(
            ['user_id' => $user->id, 'movie_id' => $movie->id],
            ['created_at' => now()]
        );
        
        // Get episode if phim bộ
        $currentEpisode = null;
        $episodeId = $request->input('episode_id');
        
        if ($movie->isPhimBo() && $movie->episodes->isNotEmpty()) {
            if ($episodeId) {
                $currentEpisode = $movie->episodes->firstWhere('id', $episodeId);
            } else {
                // Auto redirect to first episode with video
                $firstEpisode = $movie->episodes->first(function($ep) {
                    return !empty($ep->video_url);
                });
                
                if ($firstEpisode) {
                    return redirect()->route('movies.watch', [
                        'id' => $movie->id,
                        'episode_id' => $firstEpisode->id
                    ]);
                }
            }
        }
        
        // Reviews and Comments
        $reviews = $movie->reviews()
            ->with('user')
            ->orderByDesc('is_pinned')
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();
        
        $comments = $movie->comments()
            ->with(['user', 'replies.user'])
            ->whereNull('parent_id')
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();
        
        // Check if user rated
        $userHasRated = false;
        $userRating = null;
        if ($user) {
            $userReview = $movie->reviews()->where('user_id', $user->id)->first();
            if ($userReview) {
                $userHasRated = true;
                $userRating = $userReview->rating;
            }
        }
        
        // Related movies
        $relatedMovies = Movie::with('category')
            ->where('category_id', $movie->category_id)
            ->where('id', '!=', $movie->id)
            ->orderByDesc('rating')
            ->limit(6)
            ->get();
        
        $isAdmin = $user && ($user->role === 'admin' || 
                   $user->roles()->whereIn('name', ['Super Admin', 'Admin'])->exists());
        
        return view('movie.watch', compact(
            'movie',
            'currentEpisode',
            'reviews',
            'comments',
            'userHasRated',
            'userRating',
            'relatedMovies',
            'isAdmin'
        ));
    }
    
    public function toggleFavorite(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'error' => 'Vui lòng đăng nhập']);
        }
        
        $movieId = $request->input('movie_id');
        if (!$movieId) {
            return response()->json(['success' => false, 'error' => 'Thiếu thông tin phim']);
        }
        
        $watchHistory = WatchHistory::firstOrCreate(
            ['user_id' => Auth::id(), 'movie_id' => $movieId]
        );
        
        $watchHistory->favorite = !$watchHistory->favorite;
        $watchHistory->save();
        
        return response()->json([
            'success' => true,
            'favorite' => $watchHistory->favorite,
            'message' => $watchHistory->favorite ? 'Đã thêm vào yêu thích' : 'Đã xóa khỏi yêu thích'
        ]);
    }
    
    private function checkMovieAccess($user, $movieLevel)
    {
        // Admin always has access
        if ($user->role === 'admin' || 
            $user->roles()->whereIn('name', ['Super Admin', 'Admin'])->exists()) {
            return true;
        }
        
        if ($movieLevel === 'Free') {
            return true;
        }
        
        $subscriptionName = $user->subscription->name ?? null;
        if (!$subscriptionName) {
            return false;
        }
        
        $levelHierarchy = [
            'Free' => 0,
            'Basic' => 1,
            'Silver' => 2,
            'Gold' => 3,
            'Premium' => 4
        ];
        
        $userLevel = $levelHierarchy[$subscriptionName] ?? 0;
        $requiredLevel = $levelHierarchy[$movieLevel] ?? 0;
        
        return $userLevel >= $requiredLevel;
    }
}
