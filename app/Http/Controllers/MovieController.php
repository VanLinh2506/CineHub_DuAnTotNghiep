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

        $query = Movie::with(['category', 'categories']);

        // Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('director', 'LIKE', "%{$search}%")
                    ->orWhere('actors', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('country', 'LIKE', "%{$search}%")
                    ->orWhereHas('category', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('name', 'LIKE', "%{$search}%");
                    })
                    ->orWhereHas('categories', function ($categoryQuery) use ($search) {
                        $categoryQuery->where('categories.name', 'LIKE', "%{$search}%");
                    });
            });
        }

        // Filters
        if ($categoryId) {
            $query->where(function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId)
                    ->orWhereHas('categories', function ($categoryQuery) use ($categoryId) {
                        $categoryQuery->where('categories.id', $categoryId);
                    });
            });
        }

        if ($status) {
            $query->where('status', $status);
        } else {
            $query->where('status', '!=', 'Chiáº¿u ráº¡p');
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
        $movies = $query->orderBy('created_at', 'desc')->paginate(12)->withQueryString();

        // Add episode count for phim bá»™
        $movies->each(function ($movie) {
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

    /**
     * Show movies currently in theaters
     */
    public function theater(Request $request)
    {
        // Forward to BookingController using app container
        $bookingController = app(\App\Http\Controllers\BookingController::class);
        return $bookingController->index($request);
    }

    /**
     * Show online movies
     */
    public function online(Request $request)
    {
        $query = Movie::with('category')
            ->where('status', '!=', 'Chiáº¿u ráº¡p')
            ->orderBy('created_at', 'desc');

        $movies = $query->paginate(12);

        $categories = Category::orderBy('name')->get();
        $countries = collect();
        $favorites = [];

        // Missing variables for view
        $search = '';
        $categoryId = null;
        $status = null;
        $country = null;
        $type = null;
        $minRating = null;

        return view('movie.index', compact(
            'movies',
            'categories',
            'countries',
            'favorites',
            'search',
            'categoryId',
            'status',
            'country',
            'type',
            'minRating'
        ));
    }

    /**
     * Show phim láº» (single movies)
     */
    public function phimLe(Request $request)
    {
        $query = Movie::with('category')
            ->where('type', 'phimle')
            ->orderBy('created_at', 'desc');

        $movies = $query->paginate(12);

        $categories = Category::orderBy('name')->get();
        $countries = collect();
        $favorites = [];

        // Missing variables for view
        $search = '';
        $categoryId = null;
        $status = null;
        $country = null;
        $type = 'phimle';
        $minRating = null;

        return view('movie.index', compact(
            'movies',
            'categories',
            'countries',
            'favorites',
            'search',
            'categoryId',
            'status',
            'country',
            'type',
            'minRating'
        ));
    }

    /**
     * Show phim bá»™ (series)
     */
    public function phimBo(Request $request)
    {
        $query = Movie::with('category')
            ->where('type', 'phimbo')
            ->orderBy('created_at', 'desc');

        $movies = $query->paginate(12);

        // Add episode count
        $movies->each(function ($movie) {
            $movie->episode_count = $movie->episodes()->count();
        });

        $categories = Category::orderBy('name')->get();
        $countries = collect();
        $favorites = [];

        // Missing variables for view
        $search = '';
        $categoryId = null;
        $status = null;
        $country = null;
        $type = 'phimbo';
        $minRating = null;

        return view('movie.index', compact(
            'movies',
            'categories',
            'countries',
            'favorites',
            'search',
            'categoryId',
            'status',
            'country',
            'type',
            'minRating'
        ));
    }

    /**
     * Show curated movie library groups from the top menu.
     */
    public function libraryChooser()
    {
        $libraryGroups = [
            [
                'key' => 'tre-em',
                'title' => 'Trẻ em',
                'description' => 'Phim nhẹ nhàng, vui tươi và phù hợp cho gia đình.',
                'image' => asset('storage/data/img/treem.jpg'),
            ],
            [
                'key' => 'nguoi-lon',
                'title' => 'Người lớn',
                'description' => 'Nội dung trưởng thành hơn, tình cảm và kịch tính.',
                'image' => asset('storage/data/img/nguoilon.jpg'),
            ],
            [
                'key' => 'mot-phim',
                'title' => 'Mọt phim',
                'description' => 'Danh sách dành cho người xem nghiền phim và thích phim nổi bật.',
                'image' => asset('storage/data/img/motphim.jpg'),
            ],
        ];

        return view('movie.library', compact('libraryGroups'));
    }

    /**
     * Show curated movie library groups from the top menu.
     */
    public function library(Request $request, string $audience)
    {
        $audienceLabels = [
            'tre-em' => 'Trẻ em',
            'nguoi-lon' => 'Người lớn',
            'mot-phim' => 'Mọt phim',
        ];

        abort_unless(isset($audienceLabels[$audience]), 404);

        $query = Movie::with(['category', 'categories'])
            ->where('status_admin', 'published')
            ->where('status', '!=', 'Chiáº¿u ráº¡p');

        $this->applyAudienceFilter($query, $audience);
        $this->prioritizeWatchedMovies($query);

        $movies = $query
            ->orderByDesc('rating')
            ->orderByDesc('created_at')
            ->paginate(12);

        $movies->each(function ($movie) {
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

        $favorites = [];
        if (Auth::check()) {
            $favorites = WatchHistory::where('user_id', Auth::id())
                ->where('favorite', true)
                ->pluck('movie_id')
                ->toArray();
        }

        $search = '';
        $categoryId = null;
        $status = null;
        $country = null;
        $type = null;
        $minRating = null;
        $audienceTitle = $audienceLabels[$audience];

        return view('movie.index', compact(
            'movies',
            'categories',
            'countries',
            'favorites',
            'search',
            'categoryId',
            'status',
            'country',
            'type',
            'minRating',
            'audience',
            'audienceTitle'
        ));
    }

    /**
     * Show movies by category
     */
    public function category(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $query = Movie::with('category')
            ->where(function ($q) use ($id) {
                $q->where('category_id', $id)
                    ->orWhereHas('categories', function ($categoryQuery) use ($id) {
                        $categoryQuery->where('categories.id', $id);
                    });
            })
            ->orderBy('created_at', 'desc');

        $movies = $query->paginate(12);

        $categories = Category::orderBy('name')->get();
        $countries = collect();
        $favorites = [];

        // Missing variables for view
        $search = '';
        $categoryId = $id;
        $status = null;
        $country = null;
        $type = null;
        $minRating = null;

        return view('movie.index', compact(
            'movies',
            'categories',
            'countries',
            'favorites',
            'category',
            'search',
            'categoryId',
            'status',
            'country',
            'type',
            'minRating'
        ));
    }

    /**
     * Show movie detail
     */
    public function show($id)
    {
        // The 'show' route should display the main movie page, which is the watch page.
        return $this->watch(request(), $id);
    }

    /**
     * Watch movie
     */
    public function watch(Request $request, $id)
    {
        $movie = Movie::with(['category', 'episodes' => function ($q) {
            $q->orderBy('episode_number');
        }])->findOrFail($id);

        // Check login
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lÃ²ng Ä‘Äƒng nháº­p Ä‘á»ƒ xem phim!');
        }

        $user = Auth::user();

        // Check access level
        $movieLevel = $movie->level ?? 'Free';
        if (!$this->checkMovieAccess($user, $movieLevel)) {
            $subscriptionName = $user->subscription->name ?? 'Free';
            return redirect()->route('movies.index')
                ->with('error', "Phim nÃ y yÃªu cáº§u gÃ³i {$movieLevel}. GÃ³i hiá»‡n táº¡i cá»§a báº¡n: {$subscriptionName}");
        }

        // Save watch history
        WatchHistory::updateOrCreate(
            ['user_id' => $user->id, 'movie_id' => $movie->id],
            ['created_at' => now(), 'updated_at' => now()]
        );

        // Get episode if phim bá»™
        $currentEpisode = null;
        $episodeId = $request->input('episode_id');

        if ($movie->isPhimBo() && $movie->episodes->isNotEmpty()) {
            if ($episodeId) {
                $currentEpisode = $movie->episodes->firstWhere('id', $episodeId);
            } else {
                // Auto redirect to first episode with video
                $firstEpisode = $movie->episodes->first(function ($ep) {
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

    /**
     * Watch episode (for phim bá»™)
     */
    public function watchEpisode($movieId, $episodeNumber)
    {
        $movie = Movie::with(['category', 'episodes' => function ($q) {
            $q->orderBy('episode_number');
        }])->findOrFail($movieId);

        $currentEpisode = $movie->episodes()
            ->where('episode_number', $episodeNumber)
            ->firstOrFail();

        // Redirect to watch method with episode
        return redirect()->route('movies.watch', [
            'id' => $movieId,
            'episode_id' => $currentEpisode->id
        ]);
    }

    public function toggleFavorite(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'error' => 'Vui lÃ²ng Ä‘Äƒng nháº­p']);
        }

        $movieId = $request->input('movie_id');
        if (!$movieId) {
            return response()->json(['success' => false, 'error' => 'Thiáº¿u thÃ´ng tin phim']);
        }

        $watchHistory = WatchHistory::firstOrCreate(
            ['user_id' => Auth::id(), 'movie_id' => $movieId]
        );

        $watchHistory->favorite = !$watchHistory->favorite;
        $watchHistory->save();

        return response()->json([
            'success' => true,
            'favorite' => $watchHistory->favorite,
            'message' => $watchHistory->favorite ? 'ÄÃ£ thÃªm vÃ o yÃªu thÃ­ch' : 'ÄÃ£ xÃ³a khá»i yÃªu thÃ­ch'
        ]);
    }

    private function getRatingDistribution($reviews)
    {
        $distribution = [1 => 0, 2 => 0, 3 => 0, 4 => 0, 5 => 0, 6 => 0, 7 => 0, 8 => 0, 9 => 0, 10 => 0];
        foreach ($reviews as $review) {
            $rating = (int) $review->rating;
            if (isset($distribution[$rating])) {
                $distribution[$rating]++;
            }
        }
        return $distribution;
    }

    private function checkMovieAccess($user, $movieLevel)
    {
        // Admin always has access
        if (
            $user->role === 'admin' ||
            $user->roles()->whereIn('name', ['Super Admin', 'Admin'])->exists()
        ) {
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

    private function applyAudienceFilter($query, string $audience): void
    {
        if ($audience === 'mot-phim') {
            return;
        }

        if ($audience === 'tre-em') {
            $familyComedyTitles = [
                'Home Alone (1990)',
                'Mrs. Doubtfire (1993)',
                'Groundhog Day (1993)',
            ];

            $query
                ->where(function ($ratingQuery) {
                    $ratingQuery->whereNull('age_rating')
                        ->orWhereNotIn('age_rating', ['T16', 'T18', 'C16', 'C18', 'R', 'NC-17', 'TV-MA']);
                })
                ->where(function ($q) use ($familyComedyTitles) {
                    $q->where('category_id', 5)
                        ->orWhereHas('categories', fn ($categoryQuery) => $categoryQuery->where('categories.id', 5))
                        ->orWhere(function ($comedyQuery) use ($familyComedyTitles) {
                            $comedyQuery->whereIn('title', $familyComedyTitles)
                                ->where(function ($categoryScope) {
                                    $categoryScope
                                        ->where('category_id', 3)
                                        ->orWhereHas('categories', fn ($categoryQuery) => $categoryQuery->where('categories.id', 3));
                                });
                        });
                });

            return;

            $query->where(function ($q) {
                $q->whereIn('age_rating', ['G', 'PG', 'P', 'K'])
                    ->orWhereHas('category', function ($categoryQuery) {
                        $categoryQuery->where('name', 'LIKE', '%hoáº¡t hÃ¬nh%')
                            ->orWhere('name', 'LIKE', '%thiáº¿u nhi%')
                            ->orWhere('name', 'LIKE', '%gia Ä‘Ã¬nh%')
                            ->orWhere('name', 'LIKE', '%tráº» em%');
                    })
                    ->orWhereHas('categories', function ($categoryQuery) {
                        $categoryQuery->where('name', 'LIKE', '%hoáº¡t hÃ¬nh%')
                            ->orWhere('name', 'LIKE', '%thiáº¿u nhi%')
                            ->orWhere('name', 'LIKE', '%gia Ä‘Ã¬nh%')
                            ->orWhere('name', 'LIKE', '%tráº» em%');
                    })
                    ->orWhere('title', 'LIKE', '%thiáº¿u nhi%')
                    ->orWhere('description', 'LIKE', '%thiáº¿u nhi%')
                    ->orWhere('description', 'LIKE', '%tráº» em%');
            });

            return;
        }

        if ($audience === 'nguoi-lon') {
            $query->where(function ($q) {
                $q->where('type', 'phimbo')
                    ->orWhereHas('category', function ($categoryQuery) {
                        $categoryQuery->where('name', 'LIKE', '%tÃ¬nh cáº£m%')
                            ->orWhere('name', 'LIKE', '%tÃ¢m lÃ½%')
                            ->orWhere('name', 'LIKE', '%lÃ£ng máº¡n%')
                            ->orWhere('name', 'LIKE', '%gia Ä‘Ã¬nh%');
                    })
                    ->orWhereHas('categories', function ($categoryQuery) {
                        $categoryQuery->where('name', 'LIKE', '%tÃ¬nh cáº£m%')
                            ->orWhere('name', 'LIKE', '%tÃ¢m lÃ½%')
                            ->orWhere('name', 'LIKE', '%lÃ£ng máº¡n%')
                            ->orWhere('name', 'LIKE', '%gia Ä‘Ã¬nh%');
                    })
                    ->orWhere('description', 'LIKE', '%tÃ¬nh cáº£m%')
                    ->orWhere('description', 'LIKE', '%lÃ£ng máº¡n%');
            });
        }
    }

    private function prioritizeWatchedMovies($query): void
    {
        if (!Auth::check()) {
            return;
        }

        $watchedMovieIds = WatchHistory::where('user_id', Auth::id())
            ->latest('updated_at')
            ->pluck('movie_id')
            ->unique()
            ->values()
            ->toArray();

        if (empty($watchedMovieIds)) {
            return;
        }

        $cases = collect($watchedMovieIds)
            ->map(fn ($movieId, $index) => 'WHEN ' . (int) $movieId . ' THEN ' . (int) $index)
            ->implode(' ');

        $query->orderByRaw("CASE id {$cases} ELSE 999999 END");
    }

    public function introduce($id)
    {
        $movie = Movie::with(['category', 'categories', 'episodes', 'reviews.user'])->findOrFail($id);

        // Láº¥y danh sÃ¡ch phim liÃªn quan cÃ¹ng category
        $relatedMovies = Movie::where('category_id', $movie->category_id)
            ->where('id', '!=', $id)
            ->where('status_admin', 'published')
            ->limit(6)
            ->get();

        // Thá»‘ng kÃª Ä‘Ã¡nh giÃ¡
        $ratingStats = [
            'average' => $movie->reviews->avg('rating') ?? 0,
            'count' => $movie->reviews->count(),
            'distribution' => $this->getRatingDistribution($movie->reviews)
        ];

        return view('movie.introduce', compact('movie', 'relatedMovies', 'ratingStats'));
    }
}
