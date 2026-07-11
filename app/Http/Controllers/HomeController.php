<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        // Slider phim nổi bật
        $sliderMovies = Movie::with(['category', 'categories'])
            ->where('status', 'Chiếu online')
            ->where('status_admin', 'published')
            ->where(function($query) {
                $query->whereNotNull('banner')
                      ->where('banner', '!=', '')
                      ->orWhere(function($q) {
                          $q->whereNotNull('thumbnail')
                            ->where('thumbnail', '!=', '');
                      });
            })
            ->orderByRaw('CASE WHEN banner IS NOT NULL AND banner != "" THEN 1 ELSE 2 END')
            ->orderBy('rating', 'desc')
            ->inRandomOrder()
            ->limit(5)
            ->get();
        
        // Nếu không đủ 5 phim, lấy thêm
        if ($sliderMovies->count() < 5) {
            $existingIds = $sliderMovies->pluck('id')->toArray();
            $additionalMovies = Movie::with(['category', 'categories'])
                ->where('status', '!=', 'Chiếu rạp')
                ->where('status_admin', 'published')
                ->whereNotNull('thumbnail')
                ->where('thumbnail', '!=', '')
                ->whereNotIn('id', $existingIds ?: [0])
                ->orderBy('rating', 'desc')
                ->inRandomOrder()
                ->limit(5 - $sliderMovies->count())
                ->get();
            
            $sliderMovies = $sliderMovies->merge($additionalMovies)->shuffle();
        }
        
        // Phim lẻ
        $phimLe = Movie::with(['category', 'categories'])
            ->where(function($query) {
                $query->where('type', 'phimle')
                      ->orWhereNull('type');
            })
            ->where('status', '!=', 'Chiếu rạp')
            ->where('status_admin', 'published')
            ->orderBy('rating', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();
        
        // Phim bộ với số tập
        $phimBo = Movie::with(['category', 'categories'])
            ->withCount('episodes')
            ->where('type', 'phimbo')
            ->where('status', '!=', 'Chiếu rạp')
            ->where('status_admin', 'published')
            ->orderBy('rating', 'desc')
            ->orderBy('created_at', 'desc')
            ->limit(8)
            ->get();
        
        // Phim mới nhất
        $latestMovies = Movie::with(['category', 'categories'])
            ->withCount('episodes')
            ->where('status', '!=', 'Chiếu rạp')
            ->where('status_admin', 'published')
            ->orderBy('created_at', 'desc')
            ->limit(12)
            ->get();
        
        // Top phim xem nhiều trong tuần
        $topMoviesWeek = Movie::with(['category', 'categories'])
            ->withCount(['episodes', 'watchHistory' => function($query) {
                $query->where('created_at', '>=', now()->subDays(7));
            }])
            ->where('status', '!=', 'Chiếu rạp')
            ->where('status_admin', 'published')
            ->orderBy('watch_history_count', 'desc')
            ->orderBy('rating', 'desc')
            ->limit(10)
            ->get();

        // Ranking rows by genre. Weekly views are the primary signal; rating
        // keeps useful ordering while test catalogs do not have watch data.
        $topMoviesByCategory = Category::query()
            ->orderBy('id')
            ->get()
            ->mapWithKeys(function (Category $category) {
                $movies = Movie::with(['category', 'categories'])
                    ->withCount(['watchHistory' => function ($query) {
                        $query->where('created_at', '>=', now()->subDays(7));
                    }])
                    ->where('status_admin', 'published')
                    ->where(function ($query) use ($category) {
                        $query->where('category_id', $category->id)
                            ->orWhereHas('categories', function ($categoryQuery) use ($category) {
                                $categoryQuery->where('categories.id', $category->id);
                            });
                    })
                    ->orderByDesc('watch_history_count')
                    ->orderByDesc('rating')
                    ->limit(10)
                    ->get();

                return $movies->isEmpty() ? [] : [$category->name => $movies];
            });
        
        // Lấy danh sách favorites nếu đã đăng nhập
        $favorites = [];
        if ($user) {
            $favorites = DB::table('watch_history')
                ->where('user_id', $user->id)
                ->where('favorite', 1)
                ->pluck('movie_id')
                ->toArray();
        }
        
        return view('home.index', compact(
            'sliderMovies',
            'latestMovies',
            'phimLe',
            'phimBo',
            'topMoviesWeek',
            'topMoviesByCategory',
            'user',
            'favorites'
        ));
    }
}
