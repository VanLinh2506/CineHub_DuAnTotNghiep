<?php

namespace App\Http\Controllers;

use App\Models\{User, Movie, Category, Theater, Ticket, Transaction, Showtime, Screen, FoodItem, Episode, Booking, Notification};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\{Auth, DB, Hash, Storage, Log, File, Schema};
use Illuminate\Support\Str;
use Carbon\Carbon;

class AdminController extends Controller
{
    // Middleware is handled by routes - no need for constructor middleware

    protected function checkIsAdmin()
    {
        $user = Auth::user();
        if ($user->role === 'moderator') {
            return redirect()->route('admin.index')->with('error', 'Bạn không có quyền truy cập chức năng này!');
        }
        return $user;
    }

    // Dashboard
    public function index()
    {
        $stats = [
            'total_users' => User::count(),
            'total_movies' => Movie::count(),
            'total_tickets' => Ticket::count(),
            'total_revenue' => Transaction::where('status', 'Thành công')->sum('amount') ?? 0,
            'today_revenue' => Transaction::where('status', 'Thành công')->whereDate('created_at', today())->sum('amount') ?? 0,
            'week_revenue' => Transaction::where('status', 'Thành công')->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->sum('amount') ?? 0,
            'month_revenue' => Transaction::where('status', 'Thành công')->whereMonth('created_at', now()->month)->sum('amount') ?? 0,
            'active_users_today' => User::whereDate('updated_at', today())->count(),
            'pending_tickets' => 0, // Placeholder
        ];

        $revenueByDay = Transaction::where('status', 'Thành công')
            ->where('created_at', '>=', now()->subDays(6))
            ->selectRaw('DATE(created_at) as date, SUM(amount) as revenue, COUNT(*) as transaction_count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        try {
            $topMovies = Movie::join('watch_history', 'movies.id', '=', 'watch_history.movie_id')
                ->selectRaw('movies.title, COUNT(watch_history.id) as view_count')
                ->groupBy('movies.id', 'movies.title')
                ->orderByDesc('view_count')
                ->limit(5)
                ->get()
                ->toArray();
        } catch (\Exception $e) {
            $topMovies = [];
        }

        return view('admin.dashboard', compact('stats', 'revenueByDay', 'topMovies'));
    }

    // Users Management
    public function users(Request $request)
    {
        $user = $this->checkIsAdmin();
        if ($user instanceof \Illuminate\Http\RedirectResponse) return $user;

        $query = User::with('subscription');
        
        if ($search = $request->search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                  ->orWhere('email', 'like', "%$search%");
            });
        }

        if ($status = $request->status) {
            $query->where('status', $status);
        }

        $users = $query->orderByDesc('created_at')->get();
        $theaters = Theater::where('is_active', 1)->orderBy('name')->get();

        return view('admin.users', compact('users', 'theaters'));
    }

    public function usersUpdatePoints(Request $request)
    {
        $user = $this->checkIsAdmin();
        if ($user instanceof \Illuminate\Http\RedirectResponse) return $user;

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'action' => 'required|in:set,add,subtract',
            'points' => 'required|integer|min:0'
        ]);

        $targetUser = User::findOrFail($request->user_id);
        $currentPoints = $targetUser->points ?? 0;
        
        $newPoints = match($request->action) {
            'set' => $request->points,
            'add' => $currentPoints + $request->points,
            'subtract' => max(0, $currentPoints - $request->points),
        };

        $targetUser->update(['points' => $newPoints]);

        return redirect()->route('admin.users.index')->with('success', "Đã cập nhật điểm thành công! Điểm hiện tại: " . number_format($newPoints));
    }

    public function usersUpdateRole(Request $request)
    {
        $user = $this->checkIsAdmin();
        if ($user instanceof \Illuminate\Http\RedirectResponse) return $user;

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:user,moderator,admin',
            'theater_id' => 'required_if:role,moderator|nullable|exists:theaters,id'
        ]);

        if ($request->user_id == Auth::id()) {
            return redirect()->route('admin.users.index')->with('error', 'Bạn không thể thay đổi vai trò của chính mình!');
        }

        $targetUser = User::findOrFail($request->user_id);
        $targetUser->update([
            'role' => $request->role,
            'theater_id' => $request->role === 'moderator' ? $request->theater_id : null
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật vai trò thành công!');
    }

    public function usersToggleStatus(Request $request)
    {
        $user = $this->checkIsAdmin();
        if ($user instanceof \Illuminate\Http\RedirectResponse) return $user;

        $request->validate([
            'user_id' => 'required|exists:users,id',
            'is_active' => 'required|boolean'
        ]);

        if ($request->user_id == Auth::id()) {
            return redirect()->route('admin.users.index')->with('error', 'Bạn không thể khóa tài khoản của chính mình!');
        }

        $targetUser = User::findOrFail($request->user_id);
        $targetUser->update([
            'is_active' => $request->is_active,
            'status' => $request->is_active ? 'active' : 'banned'
        ]);

        $statusText = $request->is_active ? 'mở khóa' : 'khóa';
        return redirect()->route('admin.users.index')->with('success', ucfirst($statusText) . ' tài khoản thành công!');
    }

    // Movies Management
    public function movies(Request $request)
    {
        $query = Movie::with('category');

        if ($search = $request->search) {
            $query->where('title', 'like', "%$search%");
        }

        if ($category_id = $request->category) {
            $query->where('category_id', $category_id);
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        $movies = $query->orderByDesc('created_at')->get();
        $categories = Category::all();

        return view('admin.movies', compact('movies', 'categories'));
    }

    public function moviesCreate()
    {
        $categories = Category::all();
        $theaters = Theater::where('is_active', 1)->orderBy('name')->get();
        return view('admin.movies.create', compact('categories', 'theaters'));
    }

    public function moviesStore(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
            'level' => 'nullable|in:Free,Basic,Silver,Gold,Premium',
            'duration' => 'nullable|integer',
            'type' => 'required|in:phimle,phimbo',
            'status' => 'nullable|string',
            'thumbnail_file' => 'nullable|image|max:5120',
            'banner_file' => 'nullable|image|max:5120',
            'trailer_file' => 'nullable|mimes:mp4,avi,mov,mkv,webm|max:102400',
        ]);

        $data = $request->only(['title', 'category_id', 'level', 'duration', 'description', 'director', 'actors', 'status', 'type', 'country', 'language', 'age_rating']);
        $data['rating'] = 0;
        $data['status_admin'] = $request->status_admin ?? 'draft';

        // Handle file uploads
        if ($request->hasFile('thumbnail_file')) {
            $data['thumbnail'] = $request->file('thumbnail_file')->store('posters', 'public');
        } else {
            $data['thumbnail'] = $request->thumbnail;
        }

        if ($request->hasFile('banner_file')) {
            $data['banner'] = $request->file('banner_file')->store('banners', 'public');
        } else {
            $data['banner'] = $request->banner;
        }

        if ($request->hasFile('trailer_file')) {
            $data['trailer_url'] = $request->file('trailer_file')->store('trailers', 'public');
        } else {
            $data['trailer_url'] = $request->trailer_url;
        }

        if ($request->hasFile('video_file')) {
            $path = $request->type === 'phimbo' ? 'phimbo' : 'phimle';
            $data['video_url'] = $request->file('video_file')->store($path, 'public');
        } else {
            $data['video_url'] = $request->video_url;
        }

        $data['normal_price'] = $request->normal_price ?? 90000;
        $data['vip_price'] = $request->vip_price ?? 120000;
        $data['couple_price'] = $request->couple_price ?? 180000;

        $movie = Movie::create($data);

        // Create showtimes if cinema movie
        if ($request->status === 'Chiếu rạp' && $request->filled(['from_date', 'to_date', 'schedule_theater_id'])) {
            $this->createShowtimes($movie, $request);
        }

        return redirect()->route('admin.movies.index')->with('success', 'Thêm phim thành công!');
    }

    public function moviesEdit($id)
    {
        $movie = Movie::with('episodes')->findOrFail($id);
        $categories = Category::all();
        $theaters = Theater::where('is_active', 1)->orderBy('name')->get();
        $showtimes = Showtime::where('movie_id', $id)->with('theater', 'screen')->get();

        return view('admin.movies.edit', compact('movie', 'categories', 'theaters', 'showtimes'));
    }

    public function moviesUpdate(Request $request, $id)
    {
        $movie = Movie::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'nullable|exists:categories,id',
        ]);

        $data = $request->only(['title', 'category_id', 'level', 'duration', 'description', 'director', 'actors', 'status', 'type', 'country', 'language', 'age_rating', 'status_admin']);

        // Handle file uploads
        if ($request->hasFile('thumbnail_file')) {
            if ($movie->thumbnail) Storage::disk('public')->delete($movie->thumbnail);
            $data['thumbnail'] = $request->file('thumbnail_file')->store('posters', 'public');
        } elseif ($request->thumbnail) {
            $data['thumbnail'] = $request->thumbnail;
        }

        if ($request->hasFile('banner_file')) {
            if ($movie->banner) Storage::disk('public')->delete($movie->banner);
            $data['banner'] = $request->file('banner_file')->store('banners', 'public');
        } elseif ($request->banner) {
            $data['banner'] = $request->banner;
        }

        if ($request->hasFile('trailer_file')) {
            if ($movie->trailer_url) Storage::disk('public')->delete($movie->trailer_url);
            $data['trailer_url'] = $request->file('trailer_file')->store('trailers', 'public');
        } elseif ($request->trailer_url) {
            $data['trailer_url'] = $request->trailer_url;
        }

        if ($request->hasFile('video_file')) {
            if ($movie->video_url) Storage::disk('public')->delete($movie->video_url);
            $path = $request->type === 'phimbo' ? 'phimbo' : 'phimle';
            $data['video_url'] = $request->file('video_file')->store($path, 'public');
        } elseif ($request->video_url) {
            $data['video_url'] = $request->video_url;
        }

        $movie->update($data);

        // Update showtimes if provided
        if ($request->status === 'Chiếu rạp' && $request->filled(['from_date', 'to_date'])) {
            $this->createShowtimes($movie, $request);
        }

        return redirect()->route('admin.movies.edit', $id)->with('success', 'Cập nhật phim thành công!');
    }

    public function moviesDelete($id)
    {
        $movie = Movie::findOrFail($id);
        
        // Delete associated files
        if ($movie->thumbnail) Storage::disk('public')->delete($movie->thumbnail);
        if ($movie->banner) Storage::disk('public')->delete($movie->banner);
        if ($movie->trailer_url) Storage::disk('public')->delete($movie->trailer_url);
        if ($movie->video_url) Storage::disk('public')->delete($movie->video_url);

        $movie->delete();

        return redirect()->route('admin.movies.index')->with('success', 'Xóa phim thành công!');
    }

    public function moviesDeleteEpisode($movieId, $id)
    {
        $episode = Episode::where('movie_id', $movieId)->where('id', $id)->firstOrFail();
        $episode->delete();

        return back()->with('success', 'Đã xóa tập phim thành công!');
    }

    public function moviesScanEpisodes()
    {
        $movies = Movie::where('type', 'phimbo')->orderBy('title')->get();
        $folders = [];
        $candidateRoots = [
            base_path('data/phim/phimbo'),
            storage_path('app/public/phimbo'),
            storage_path('app/public/episodes'),
        ];

        foreach ($candidateRoots as $root) {
            if (!File::exists($root)) {
                continue;
            }

            $directories = File::directories($root);
            if (empty($directories)) {
                $directories = [$root];
            }

            foreach ($directories as $directory) {
                $files = collect(File::files($directory))
                    ->filter(function ($file) {
                        return in_array(strtolower($file->getExtension()), ['mp4', 'avi', 'mov', 'mkv', 'webm']);
                    })
                    ->map(function ($file) {
                        return [
                            'name' => $file->getFilename(),
                            'path' => $file->getPathname(),
                            'size' => $file->getSize(),
                        ];
                    })
                    ->values()
                    ->all();

                if (empty($files)) {
                    continue;
                }

                $folders[] = [
                    'name' => basename($directory),
                    'count' => count($files),
                    'files' => $files,
                ];
            }
        }

        return view('admin.movies.scan-episodes', compact('folders', 'movies'));
    }

    public function moviesImportEpisodes(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'folder_name' => 'required|string',
        ]);

        $movie = Movie::findOrFail($request->movie_id);
        $items = $request->input('files', []);
        $imported = 0;

        foreach ($items as $item) {
            if (empty($item['import']) || empty($item['path']) || empty($item['episode_number'])) {
                continue;
            }

            Episode::updateOrCreate(
                [
                    'movie_id' => $movie->id,
                    'episode_number' => (int) $item['episode_number'],
                ],
                [
                    'title' => $item['title'] ?? ('Tập ' . (int) $item['episode_number']),
                    'video_url' => $item['path'],
                ]
            );

            $imported++;
        }

        return redirect()->route('admin.movies.edit', $movie->id)
            ->with('success', "Đã import {$imported} tập phim thành công!");
    }

    protected function createShowtimes(Movie $movie, Request $request)
    {
        $theaterIds = $request->schedule_theater_id ?? [];
        $times = $request->showtimes_time ?? [];
        $start = Carbon::parse($request->from_date);
        $end = Carbon::parse($request->to_date);

        foreach ($theaterIds as $theaterId) {
            $screens = Screen::where('theater_id', $theaterId)
                ->where('is_active', 1)
                ->limit($request->number_of_screens ?? 1)
                ->get();

            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                foreach ($times as $time) {
                    foreach ($screens as $screen) {
                        Showtime::firstOrCreate([
                            'movie_id' => $movie->id,
                            'theater_id' => $theaterId,
                            'screen_id' => $screen->id,
                            'show_date' => $date->format('Y-m-d'),
                            'show_time' => $time,
                        ], [
                            'price' => $request->default_price ?? 120000
                        ]);
                    }
                }
            }
        }
    }

    // Theaters Management
    public function theaters()
    {
        $user = Auth::user();
        
        $query = Theater::query();
        
        // Moderators can only see their theater
        if ($user->role === 'moderator' && $user->theater_id) {
            $query->where('id', $user->theater_id);
        }

        $theaters = $query->orderBy('name')->get();

        return view('admin.theaters', compact('theaters'));
    }

    public function theatersCreate()
    {
        $user = $this->checkIsAdmin();
        if ($user instanceof \Illuminate\Http\RedirectResponse) return $user;

        return view('admin.theaters.create');
    }

    public function theatersStore(Request $request)
    {
        $user = $this->checkIsAdmin();
        if ($user instanceof \Illuminate\Http\RedirectResponse) return $user;

        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string',
            'phone' => 'nullable|string',
            'address' => 'nullable|string',
            'total_screens' => 'required|integer|min:1',
        ]);

        Theater::create([
            'name' => $request->name,
            'location' => $request->location,
            'phone' => $request->phone,
            'address' => $request->address,
            'total_screens' => $request->total_screens,
            'is_active' => 1
        ]);

        return redirect()->route('admin.theaters.index')->with('success', 'Thêm rạp thành công!');
    }

    public function theatersEdit($id)
    {
        $user = Auth::user();
        $theater = Theater::findOrFail($id);

        // Check permission
        if ($user->role === 'moderator' && $user->theater_id != $id) {
            return redirect()->route('admin.theaters.index')->with('error', 'Bạn không có quyền sửa rạp này!');
        }

        return view('admin.theaters.edit', compact('theater'));
    }

    public function theatersUpdate(Request $request, $id)
    {
        $user = Auth::user();
        $theater = Theater::findOrFail($id);

        if ($user->role === 'moderator' && $user->theater_id != $id) {
            return redirect()->route('admin.theaters.index')->with('error', 'Bạn không có quyền sửa rạp này!');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'image' => 'nullable|image|max:5120',
        ]);

        $data = $request->only(['name', 'location', 'phone', 'address', 'total_screens', 'latitude', 'longitude']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        if ($request->hasFile('image')) {
            if ($theater->image) Storage::disk('public')->delete($theater->image);
            $data['image'] = $request->file('image')->store('theaters', 'public');
        }

        $theater->update($data);

        return redirect()->route('admin.theaters.index')->with('success', 'Cập nhật rạp thành công!');
    }

    public function theatersDelete($id)
    {
        $theater = Theater::findOrFail($id);
        if ($theater->image) Storage::disk('public')->delete($theater->image);
        $theater->delete();

        return redirect()->route('admin.theaters.index')->with('success', 'Xóa rạp thành công!');
    }

    // Analytics
    public function analytics(Request $request)
    {
        $period = $request->period ?? '7days';
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Calculate date range based on period
        if ($startDate && $endDate) {
            $start = Carbon::parse($startDate);
            $end = Carbon::parse($endDate);
        } else {
            switch ($period) {
                case '7days':
                    $start = now()->subDays(6)->startOfDay();
                    $end = now()->endOfDay();
                    break;
                case '30days':
                    $start = now()->subDays(29)->startOfDay();
                    $end = now()->endOfDay();
                    break;
                case 'thismonth':
                    $start = now()->startOfMonth();
                    $end = now()->endOfMonth();
                    break;
                case 'lastmonth':
                    $start = now()->subMonth()->startOfMonth();
                    $end = now()->subMonth()->endOfMonth();
                    break;
                case 'thisyear':
                    $start = now()->startOfYear();
                    $end = now()->endOfYear();
                    break;
                default:
                    $start = now()->subDays(6)->startOfDay();
                    $end = now()->endOfDay();
            }
        }

        // Get revenue data grouped by date
        $revenueData = Transaction::where('status', 'Thành công')
            ->whereBetween('created_at', [$start, $end])
            ->selectRaw('DATE(created_at) as period, SUM(amount) as revenue, COUNT(*) as transaction_count')
            ->groupBy('period')
            ->orderBy('period')
            ->get();
        
        // Safely get top movies by revenue with error handling
        try {
            $topMoviesByRevenue = Ticket::whereBetween('tickets.created_at', [$start, $end])
                ->join('showtimes', 'tickets.showtime_id', '=', 'showtimes.id')
                ->join('movies', 'showtimes.movie_id', '=', 'movies.id')
                ->selectRaw('movies.id, movies.title, movies.type, SUM(tickets.price) as revenue, COUNT(tickets.id) as ticket_count')
                ->groupBy('movies.id', 'movies.title', 'movies.type')
                ->orderByDesc('revenue')
                ->limit(10)
                ->get();
        } catch (\Exception $e) {
            Log::error('Analytics error: ' . $e->getMessage());
            $topMoviesByRevenue = collect();
        }

        $summaryStats = [
            'total_revenue' => Transaction::where('status', 'Thành công')
                ->whereBetween('created_at', [$start, $end])
                ->sum('amount') ?? 0,
            'total_transactions' => Transaction::where('status', 'Thành công')
                ->whereBetween('created_at', [$start, $end])
                ->count(),
            'total_tickets' => Ticket::whereBetween('created_at', [$start, $end])->count(),
            'avg_ticket_price' => Ticket::whereBetween('created_at', [$start, $end])->avg('price') ?? 0,
        ];

        return view('admin.analytics', compact('revenueData', 'topMoviesByRevenue', 'summaryStats', 'period'));
    }

    protected function getRevenueData($period)
    {
        $query = Transaction::where('status', 'Thành công');

        return match($period) {
            'day' => $query->where('created_at', '>=', now()->subDays(30))
                ->selectRaw('DATE(created_at) as period, SUM(amount) as revenue, COUNT(*) as transaction_count')
                ->groupBy('period')
                ->orderBy('period')
                ->get(),
            'week' => $query->where('created_at', '>=', now()->subWeeks(12))
                ->selectRaw('YEARWEEK(created_at) as period, SUM(amount) as revenue, COUNT(*) as transaction_count')
                ->groupBy('period')
                ->orderBy('period')
                ->get(),
            default => $query->where('created_at', '>=', now()->subMonths(12))
                ->selectRaw('DATE_FORMAT(created_at, "%Y-%m") as period, SUM(amount) as revenue, COUNT(*) as transaction_count')
                ->groupBy('period')
                ->orderBy('period')
                ->get(),
        };
    }

    // Tickets Management
    public function tickets(Request $request)
    {
        $user = Auth::user();
        
        $query = Ticket::with(['user', 'showtime.movie', 'showtime.theater']);

        // Filter by theater for moderators
        if ($user->role === 'moderator' && $user->theater_id) {
            $query->whereHas('showtime', function($q) use ($user) {
                $q->where('theater_id', $user->theater_id);
            });
        }

        if ($status = $request->status) {
            $query->where('status', $status);
        }

        if ($movie_id = $request->movie_id) {
            $query->whereHas('showtime', function($q) use ($movie_id) {
                $q->where('movie_id', $movie_id);
            });
        }

        $tickets = $query->orderByDesc('created_at')->paginate(20);

        $movies = Movie::where('status', 'Chiếu rạp')->get();

        return view('admin.tickets', compact('tickets', 'movies'));
    }

    // Food Items Management
    public function foodItems()
    {
        $user = Auth::user();
        
        $query = FoodItem::query();

        if ($user->role === 'moderator' && $user->theater_id) {
            $query->where('theater_id', $user->theater_id);
        }

        $foodItems = $query->orderBy('name')->get();

        return view('admin.food_items', compact('foodItems'));
    }

    public function foodItemsCreate()
    {
        return view('admin.food_items.create');
    }

    public function foodItemsEdit($id)
    {
        $foodItem = FoodItem::findOrFail($id);

        return view('admin.food_items.edit', compact('foodItem'));
    }

    public function foodItemsStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
            'type' => 'required|in:combo,drink,snack',
            'image' => 'nullable|image|max:5120',
        ]);

        $data = $request->only(['name', 'description', 'price', 'type']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        if (Auth::user()->role === 'moderator' && Auth::user()->theater_id) {
            $data['theater_id'] = Auth::user()->theater_id;
        }

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('food', 'public');
        }

        FoodItem::create($data);

        return redirect()->route('admin.foodItems.index')->with('success', 'Thêm combo/đồ ăn thành công!');
    }

    public function foodItemsUpdate(Request $request, $id)
    {
        $foodItem = FoodItem::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric|min:0',
        ]);

        $data = $request->only(['name', 'description', 'price', 'type']);
        $data['is_active'] = $request->has('is_active') ? 1 : 0;

        if ($request->hasFile('image')) {
            if ($foodItem->image) Storage::disk('public')->delete($foodItem->image);
            $data['image'] = $request->file('image')->store('food', 'public');
        }

        $foodItem->update($data);

        return redirect()->route('admin.foodItems.index')->with('success', 'Cập nhật combo/đồ ăn thành công!');
    }

    public function foodItemsDelete($id)
    {
        $foodItem = FoodItem::findOrFail($id);
        
        if ($foodItem->image) Storage::disk('public')->delete($foodItem->image);
        $foodItem->delete();

        return redirect()->route('admin.foodItems.index')->with('success', 'Xóa combo/đồ ăn thành công!');
    }

    public function theatersView($id)
    {
        $theater = Theater::with('screens')->findOrFail($id);
        $moderator = User::where('role', 'moderator')->where('theater_id', $id)->first();
        $screens = $theater->screens;

        return view('admin.theaters.view', compact('theater', 'moderator', 'screens'));
    }

    public function ticketsView($id)
    {
        $ticketModel = Ticket::with(['showtime.movie', 'showtime.theater', 'showtime.screen', 'user'])->findOrFail($id);

        $ticket = [
            'id' => $ticketModel->id,
            'status' => $ticketModel->status,
            'seat' => $ticketModel->seat ?? $ticketModel->seat_number ?? 'N/A',
            'price' => $ticketModel->price ?? 0,
            'qr_code' => $ticketModel->qr_code,
            'created_at' => $ticketModel->created_at,
            'movie_title' => optional(optional($ticketModel->showtime)->movie)->title,
            'theater_name' => optional(optional($ticketModel->showtime)->theater)->name,
            'theater_location' => optional(optional($ticketModel->showtime)->theater)->location,
            'theater_address' => optional(optional($ticketModel->showtime)->theater)->address,
            'show_date' => optional($ticketModel->showtime)->show_date,
            'show_time' => optional($ticketModel->showtime)->show_time,
            'screen_name' => optional(optional($ticketModel->showtime)->screen)->screen_name,
            'screen_type' => optional(optional($ticketModel->showtime)->screen)->screen_type,
            'user_name' => optional($ticketModel->user)->name,
            'user_email' => optional($ticketModel->user)->email,
            'user_phone' => optional($ticketModel->user)->phone,
        ];

        $supportTickets = [];

        return view('admin.tickets.view', compact('ticket', 'supportTickets'));
    }

    public function ticketsUpdateMovie(Request $request)
    {
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'max_tickets' => 'nullable|integer|min:0',
        ]);

        if (Schema::hasColumn('movies', 'max_tickets')) {
            Movie::whereKey($request->movie_id)->update([
                'max_tickets' => $request->max_tickets,
            ]);

            return back()->with('success', 'Đã cập nhật giới hạn vé cho phim.');
        }

        return back()->with('warning', 'Cơ sở dữ liệu hiện chưa có cột max_tickets nên chưa thể lưu giới hạn vé.');
    }

    // Categories Management
    public function categories()
    {
        $user = $this->checkIsAdmin();
        if ($user instanceof \Illuminate\Http\RedirectResponse) return $user;

        $categories = Category::withCount(['movies as movie_count'])->orderBy('name')->get();

        return view('admin.categories', compact('categories'));
    }

    public function categoriesStore(Request $request)
    {
        $user = $this->checkIsAdmin();
        if ($user instanceof \Illuminate\Http\RedirectResponse) return $user;

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        Category::create($request->only(['name', 'parent_id']));

        return redirect()->route('admin.categories.index')->with('success', 'Thêm thể loại thành công!');
    }

    public function categoriesUpdate(Request $request, $id)
    {
        $user = $this->checkIsAdmin();
        if ($user instanceof \Illuminate\Http\RedirectResponse) return $user;

        $category = Category::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $id,
            'parent_id' => 'nullable|exists:categories,id',
        ]);

        if ($request->parent_id == $id) {
            return redirect()->route('admin.categories.index')->with('error', 'Không thể chọn chính thể loại này làm thể loại cha!');
        }

        $category->update($request->only(['name', 'parent_id']));

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật thể loại thành công!');
    }

    public function categoriesDelete($id)
    {
        $user = $this->checkIsAdmin();
        if ($user instanceof \Illuminate\Http\RedirectResponse) return $user;

        $category = Category::findOrFail($id);

        if ($category->movies()->count() > 0) {
            return redirect()->route('admin.categories.index')->with('error', 'Không thể xóa! Có phim đang sử dụng thể loại này.');
        }

        $category->delete();

        return redirect()->route('admin.categories.index')->with('success', 'Xóa thể loại thành công!');
    }

    // Admin Logs
    public function logs(Request $request)
    {
        $query = DB::table('admin_logs')
            ->leftJoin('users', 'admin_logs.user_id', '=', 'users.id')
            ->select('admin_logs.*', 'users.name as user_name', 'users.email as user_email');

        if ($module = $request->module) {
            $query->where('admin_logs.module', $module);
        }

        if ($action = $request->action) {
            $query->where('admin_logs.action', 'like', "%$action%");
        }

        $logs = $query->orderByDesc('admin_logs.created_at')->paginate(50);

        return view('admin.logs', compact('logs'));
    }
}
