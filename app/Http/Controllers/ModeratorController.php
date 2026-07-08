<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Theater;
use App\Models\Screen;
use App\Models\Showtime;
use App\Models\Movie;
use App\Models\Ticket;
use App\Models\FoodItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ModeratorController extends Controller
{
    private $theaterId;
    
    public function __construct()
    {
        // Không sử dụng middleware trong constructor nữa
    }
    
    protected function checkPermission()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập');
        }
        
        $user = Auth::user();
        
        // Kiểm tra quyền moderator
        if (!$this->isModerator($user)) {
            return redirect()->route('home')->with('error', 'Bạn không có quyền truy cập!');
        }
        
        // Lấy theater_id
        $this->theaterId = $user->theater_id;
        
        if (!$this->theaterId) {
            return redirect()->route('home')->with('error', 'Bạn chưa được gán rạp!');
        }
        
        return null; // Không có lỗi
    }
    
    /**
     * Dashboard
     */
    public function index()
    {
        if ($error = $this->checkPermission()) return $error;
        
        // Get theater info
        $theater = Theater::findOrFail($this->theaterId);
        
        $stats = [
            'total_showtimes' => Showtime::whereHas('screen', function($q) {
                $q->where('theater_id', $this->theaterId);
            })->count(),
            
            'today_showtimes' => Showtime::whereHas('screen', function($q) {
                $q->where('theater_id', $this->theaterId);
            })->whereDate('show_date', today())->count(),
            
            'total_tickets' => Ticket::whereHas('showtime.screen', function($q) {
                $q->where('theater_id', $this->theaterId);
            })->count(),
            
            'today_tickets' => Ticket::whereHas('showtime.screen', function($q) {
                $q->where('theater_id', $this->theaterId);
            })->whereDate('created_at', today())->count(),
            
            'total_revenue' => Ticket::whereHas('showtime.screen', function($q) {
                $q->where('theater_id', $this->theaterId);
            })->where('status', 'Đã đặt')->sum('price'),
            
            'today_revenue' => Ticket::whereHas('showtime.screen', function($q) {
                $q->where('theater_id', $this->theaterId);
            })->where('status', 'Đã đặt')->whereDate('created_at', today())->sum('price'),
        ];
        
        // Doanh thu 7 ngày
        $revenueByDay = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $revenue = Ticket::whereHas('showtime.screen', function($q) {
                    $q->where('theater_id', $this->theaterId);
                })
                ->where('status', 'Đã đặt')
                ->whereDate('created_at', $date)
                ->sum('price');
            
            $revenueByDay[] = ['date' => $date, 'revenue' => $revenue];
        }
        
        // Top phim
        $topMovies = Movie::join('showtimes', 'movies.id', '=', 'showtimes.movie_id')
            ->join('theater_screens', 'showtimes.screen_id', '=', 'theater_screens.id')
            ->leftJoin('tickets', function($join) {
                $join->on('showtimes.id', '=', 'tickets.showtime_id')
                     ->where('tickets.status', 'Đã đặt');
            })
            ->where('theater_screens.theater_id', $this->theaterId)
            ->groupBy('movies.id', 'movies.title')
            ->select('movies.id', 'movies.title')
            ->selectRaw('COUNT(tickets.id) as ticket_count, COALESCE(SUM(tickets.price), 0) as revenue')
            ->orderByDesc('ticket_count')
            ->limit(5)
            ->get();
        
        // Upcoming showtimes
        $upcomingShowtimes = Showtime::with(['movie', 'screen'])
            ->whereHas('screen', function($q) {
                $q->where('theater_id', $this->theaterId);
            })
            ->where('show_date', '>=', today())
            ->where('show_date', '<=', now()->addDays(7))
            ->withCount(['tickets' => function($q) {
                $q->where('status', 'Đã đặt');
            }])
            ->orderBy('show_date')
            ->orderBy('show_time')
            ->limit(10)
            ->get();
        
        return view('admin.moderator.dashboard', compact('theater', 'stats', 'revenueByDay', 'topMovies', 'upcomingShowtimes'));
    }
    
    /**
     * Permission Requests
     */
    public function permissionRequests(Request $request)
    {
        if ($error = $this->checkPermission()) return $error;
        
        $requests = DB::table('moderator_permission_requests as mpr')
            ->leftJoin('users as u1', 'mpr.requested_by', '=', 'u1.id')
            ->leftJoin('users as u2', 'mpr.target_user_id', '=', 'u2.id')
            ->leftJoin('theaters as t', 'mpr.theater_id', '=', 't.id')
            ->where('mpr.theater_id', $this->theaterId)
            ->where('mpr.status', 'pending')
            ->select('mpr.*', 'u1.name as requested_by_name', 'u2.name as target_user_name', 't.name as theater_name')
            ->orderByDesc('mpr.created_at')
            ->get();
        
        $selectedRequest = null;
        if ($requestId = $request->input('id')) {
            $selectedRequest = DB::table('moderator_permission_requests as mpr')
                ->leftJoin('users as u1', 'mpr.requested_by', '=', 'u1.id')
                ->leftJoin('users as u2', 'mpr.target_user_id', '=', 'u2.id')
                ->leftJoin('theaters as t', 'mpr.theater_id', '=', 't.id')
                ->where('mpr.id', $requestId)
                ->where('mpr.theater_id', $this->theaterId)
                ->select('mpr.*', 'u1.name as requested_by_name', 'u1.email as requested_by_email',
                         'u2.name as target_user_name', 'u2.email as target_user_email', 't.name as theater_name')
                ->first();
        }
        
        return view('admin.moderator.permission_requests', compact('requests', 'selectedRequest'));
    }
    
    public function handlePermissionRequest(Request $request)
    {
        if ($error = $this->checkPermission()) return $error;
        
        $requestId = $request->input('request_id');
        $action = $request->input('action');
        
        $permissionRequest = DB::table('moderator_permission_requests')
            ->where('id', $requestId)
            ->where('theater_id', $this->theaterId)
            ->where('status', 'pending')
            ->where('moderator_id', Auth::id())
            ->first();
        
        if (!$permissionRequest) {
            return redirect()->route('moderator.permission-requests')
                ->with('error', 'Yêu cầu không hợp lệ!');
        }
        
        if ($action === 'approve') {
            $newData = json_decode($permissionRequest->new_data, true);
            
            User::where('id', $permissionRequest->target_user_id)
                ->update([
                    'role' => $newData['role'],
                    'theater_id' => $newData['theater_id'] ?? null,
                ]);
            
            DB::table('moderator_permission_requests')
                ->where('id', $requestId)
                ->update(['status' => 'approved', 'responded_at' => now()]);
            
            return redirect()->route('moderator.permission-requests')
                ->with('success', 'Đã chấp nhận yêu cầu!');
        } else {
            DB::table('moderator_permission_requests')
                ->where('id', $requestId)
                ->update(['status' => 'rejected', 'responded_at' => now()]);
            
            return redirect()->route('moderator.permission-requests')
                ->with('success', 'Đã từ chối yêu cầu!');
        }
    }
    
    /**
     * Showtimes Management
     */
    public function showtimes(Request $request)
    {
        if ($error = $this->checkPermission()) return $error;
        
        $theater = Theater::findOrFail($this->theaterId);
        $date = $request->input('date');
        $showAll = $request->has('all'); // Kiểm tra xem có yêu cầu xem tất cả không
        
        $showtimesQuery = Showtime::with(['movie', 'screen'])
            ->whereHas('screen', function($q) {
                $q->where('theater_id', $this->theaterId);
            });
        
        // Xử lý filter
        if ($date) {
            // Nếu có date cụ thể, lọc theo ngày đó
            $showtimesQuery->where('show_date', $date);
        } elseif ($showAll) {
            // Nếu nhấn "Tất cả", hiển thị tất cả lịch chiếu (không filter)
            // Không thêm điều kiện where nào
        } else {
            // Mặc định: chỉ hiển thị lịch chiếu từ hôm nay trở đi
            $showtimesQuery->where('show_date', '>=', now()->toDateString());
        }
        
        // Sắp xếp theo ngày gần nhất, rồi theo giờ chiếu
        $showtimes = $showtimesQuery
            ->orderBy('show_date', 'asc')
            ->orderBy('show_time', 'asc')
            ->get();
        
        $movies = Movie::where('status', 'Chiếu rạp')->orderBy('title')->get();
        $screens = Screen::where('theater_id', $this->theaterId)->orderBy('screen_name')->get();
        
        return view('admin.moderator.showtimes', compact('theater', 'showtimes', 'movies', 'screens', 'date'));
    }
    
    public function showtimesStore(Request $request)
    {
        \Log::info('showtimesStore called', ['user_id' => Auth::id(), 'request' => $request->all()]);
        
        if ($error = $this->checkPermission()) {
            \Log::warning('Permission check failed', ['theater_id' => $this->theaterId]);
            return $error;
        }
        
        \Log::info('Permission OK, theater_id: ' . $this->theaterId);
        
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'screen_id' => 'required|exists:theater_screens,id',
            'show_date' => 'required|date',
            'show_time' => 'required',
            'price' => 'required|numeric|min:0',
        ]);
        
        // Kiểm tra screen thuộc theater
        $screen = Screen::where('id', $request->screen_id)
            ->where('theater_id', $this->theaterId)
            ->firstOrFail();
        
        // Lấy thông tin phim để biết thời lượng
        $movie = Movie::findOrFail($request->movie_id);
        $movieDuration = $movie->duration ?? 120; // Mặc định 120 phút nếu không có
        
        // Tính thời gian kết thúc (thời lượng phim + 15 phút dọn dẹp)
        $showTime = \Carbon\Carbon::parse($request->show_date . ' ' . $request->show_time);
        $endTime = $showTime->copy()->addMinutes($movieDuration + 15);
        
        // Kiểm tra xem có suất chiếu nào trùng lặp không
        $conflicts = Showtime::where('screen_id', $request->screen_id)
            ->where('show_date', $request->show_date)
            ->get()
            ->filter(function($showtime) use ($showTime, $endTime) {
                $existingStart = \Carbon\Carbon::parse($showtime->show_date . ' ' . $showtime->show_time);
                
                // Lấy thời lượng phim của suất chiếu đang có
                $existingMovie = Movie::find($showtime->movie_id);
                $existingDuration = $existingMovie->duration ?? 120;
                $existingEnd = $existingStart->copy()->addMinutes($existingDuration + 15);
                
                // Kiểm tra xem có trùng lặp không
                return ($showTime < $existingEnd && $endTime > $existingStart);
            });
        
        if ($conflicts->count() > 0) {
            $conflictTime = $conflicts->first();
            $conflictMovie = Movie::find($conflictTime->movie_id);
            return redirect()->back()->with('error', 
                'Khung giờ này bị trùng với suất chiếu phim "' . $conflictMovie->title . '" (' . 
                \Carbon\Carbon::parse($conflictTime->show_time)->format('H:i') . '). ' .
                'Vui lòng chọn giờ khác!'
            );
        }
        
        Showtime::create([
            'movie_id' => $request->movie_id,
            'theater_id' => $this->theaterId,
            'screen_id' => $request->screen_id,
            'show_date' => $request->show_date,
            'show_time' => $request->show_time,
            'price' => $request->price,
            'available_seats' => $screen->total_seats, // Set available seats based on screen capacity
        ]);
        
        return redirect()->route('moderator.showtimes.index')
            ->with('success', 'Thêm lịch chiếu thành công!');
    }
    
    public function showtimesUpdate(Request $request, $id)
    {
        if ($error = $this->checkPermission()) return $error;
        
        $showtime = Showtime::whereHas('screen', function($q) {
                $q->where('theater_id', $this->theaterId);
            })->findOrFail($id);
        
        $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'screen_id' => 'required|exists:theater_screens,id',
            'show_date' => 'required|date',
            'show_time' => 'required',
            'price' => 'required|numeric|min:0',
        ]);
        
        $showtime->update($request->only(['movie_id', 'screen_id', 'show_date', 'show_time', 'price']));
        
        return redirect()->route('moderator.showtimes')
            ->with('success', 'Cập nhật lịch chiếu thành công!');
    }
    
    public function showtimesDelete($id)
    {
        if ($error = $this->checkPermission()) return $error;
        
        $showtime = Showtime::whereHas('screen', function($q) {
                $q->where('theater_id', $this->theaterId);
            })->findOrFail($id);
        
        $showtime->delete();
        
        return redirect()->route('moderator.showtimes')
            ->with('success', 'Xóa lịch chiếu thành công!');
    }
    
    /**
     * Screens Management
     */
    public function screens()
    {
        if ($error = $this->checkPermission()) return $error;
        
        $theater = Theater::findOrFail($this->theaterId);
        $screens = Screen::where('theater_id', $this->theaterId)
            ->withCount(['showtimes' => function($q) {
                $q->where('show_date', '>=', today());
            }])
            ->orderBy('screen_name')
            ->get();
        
        // Add current movies for each screen
        $screens = $screens->map(function($screen) {
            $screenArray = $screen->toArray();
            
            // Get distinct movies currently showing in this screen (from today onwards)
            $currentMovies = Movie::join('showtimes', 'movies.id', '=', 'showtimes.movie_id')
                ->where('showtimes.screen_id', $screen->id)
                ->where('showtimes.show_date', '>=', today())
                ->select('movies.id', 'movies.title')
                ->distinct()
                ->orderBy('movies.title')
                ->get()
                ->map(function($item) {
                    return ['id' => $item->id, 'title' => $item->title];
                })
                ->toArray();
            
            $screenArray['current_movies'] = $currentMovies;
            return $screenArray;
        });
        
        // Get movies for filter dropdown
        $movies = Movie::join('showtimes', 'movies.id', '=', 'showtimes.movie_id')
            ->join('theater_screens', 'showtimes.screen_id', '=', 'theater_screens.id')
            ->where('theater_screens.theater_id', $this->theaterId)
            ->where('showtimes.show_date', '>=', today()) // Only future showtimes
            ->select('movies.id', 'movies.title')
            ->distinct()
            ->orderBy('movies.title')
            ->get()
            ->map(function($item) {
                return ['id' => $item->id, 'title' => $item->title];
            })
            ->toArray();
        
        return view('admin.moderator.screens', [
            'theater' => $theater->toArray(),
            'screens' => $screens->toArray(),
            'movies' => $movies
        ]);
    }
    
    public function screensStore(Request $request)
    {
        if ($error = $this->checkPermission()) return $error;
        
        $request->validate([
            'screen_name' => 'required|string|max:255',
            'screen_type' => 'required|string',
            'num_groups' => 'required|integer|min:1',
            'seats_per_group_row' => 'required|integer|min:1',
            'num_rows' => 'required|integer|min:1',
            'num_vip_rows' => 'required|integer|min:0',
        ]);
        
        // Kiểm tra trùng tên
        $existing = Screen::where('theater_id', $this->theaterId)
            ->where('screen_name', $request->screen_name)
            ->exists();
        
        if ($existing) {
            return redirect()->back()->with('error', 'Tên phòng đã tồn tại!');
        }
        
        $totalSeats = (int) $request->num_groups * (int) $request->seats_per_group_row * (int) $request->num_rows;

        Screen::create([
            'theater_id' => $this->theaterId,
            'screen_name' => $request->screen_name,
            'screen_type' => $request->screen_type,
            'total_seats' => $totalSeats,
            'seat_layout_config' => $this->generateSeatLayout($request),
        ]);
        
        return redirect()->route('moderator.screens')
            ->with('success', 'Thêm phòng chiếu thành công!');
    }
    
    public function screensEdit($id)
    {
        if ($error = $this->checkPermission()) return $error;
        
        $screen = Screen::where('id', $id)
            ->where('theater_id', $this->theaterId)
            ->firstOrFail();
        
        // seat_layout_config already cast to array in model
        $layout = $screen->seat_layout_config ?: [];
        
        return view('admin.moderator.screen_edit', [
            'screen' => $screen->toArray(),
            'layout' => $layout
        ]);
    }
    
    public function screensUpdate(Request $request, $id)
    {
        if ($error = $this->checkPermission()) return $error;
        
        $screen = Screen::where('id', $id)
            ->where('theater_id', $this->theaterId)
            ->firstOrFail();
        
        $request->validate([
            'screen_name' => 'required|string|max:255',
            'screen_type' => 'required|string',
            'num_groups' => 'required|integer|min:1',
            'seats_per_group_row' => 'required|integer|min:1',
            'num_rows' => 'required|integer|min:1',
            'num_vip_rows' => 'required|integer|min:0',
        ]);

        $totalSeats = (int) $request->num_groups * (int) $request->seats_per_group_row * (int) $request->num_rows;
        
        $screen->update([
            'screen_name' => $request->screen_name,
            'screen_type' => $request->screen_type,
            'seat_layout_config' => $this->generateSeatLayout($request),
            'total_seats' => $totalSeats,
        ]);
        
        return redirect()->route('moderator.screens.index')
            ->with('success', 'Cập nhật cấu hình phòng chiếu thành công!');
    }
    
    /**
     * Theater Info
     */
    public function theater()
    {
        if ($error = $this->checkPermission()) return $error;
        
        $theater = Theater::findOrFail($this->theaterId);
        $screens = Screen::where('theater_id', $this->theaterId)->get();
        
        return view('admin.moderator.theater', compact('theater', 'screens'));
    }
    
    public function theaterUpdate(Request $request)
    {
        if ($error = $this->checkPermission()) return $error;
        
        $theater = Theater::findOrFail($this->theaterId);
        
        $request->validate([
            'name' => 'required|string|max:255',
            'location' => 'nullable|string',
            'address' => 'nullable|string',
            'phone' => 'nullable|string',
        ]);
        
        $theater->update($request->only(['name', 'location', 'address', 'phone']));
        
        return redirect()->route('moderator.theater')
            ->with('success', 'Cập nhật thông tin rạp thành công!');
    }
    
    /**
     * Tickets Management
     */
    public function tickets(Request $request)
    {
        if ($error = $this->checkPermission()) return $error;
        
        $theater = Theater::findOrFail($this->theaterId);
        $query = Ticket::with(['showtime.movie', 'showtime.screen', 'user'])
            ->whereHas('showtime.screen', function($q) {
                $q->where('theater_id', $this->theaterId);
            });
        
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        
        if ($movieId = $request->input('movie_id')) {
            $query->whereHas('showtime', function($q) use ($movieId) {
                $q->where('movie_id', $movieId);
            });
        }
        
        $tickets = $query->orderByDesc('created_at')->paginate(20);
        $tickets->getCollection()->transform(function (Ticket $ticket) {
            $showtime = $ticket->showtime;
            $user = $ticket->user;

            return [
                'id' => $ticket->id,
                'user_name' => $user?->name ?? 'N/A',
                'user_email' => $user?->email ?? '',
                'movie_title' => $showtime?->movie?->title ?? 'N/A',
                'screen_name' => $showtime?->screen?->screen_name ?? 'N/A',
                'show_date' => $showtime?->show_date,
                'show_time' => $showtime?->show_time,
                'seat' => $ticket->seat,
                'price' => $ticket->price,
                'status' => $ticket->status,
                'created_at' => $ticket->created_at,
            ];
        });
        
        $stats = [
            'total' => Ticket::whereHas('showtime.screen', function($q) {
                    $q->where('theater_id', $this->theaterId);
                })->count(),
            'sold' => Ticket::whereHas('showtime.screen', function($q) {
                    $q->where('theater_id', $this->theaterId);
                })->where('status', 'Đã đặt')->count(),
            'cancelled' => Ticket::whereHas('showtime.screen', function($q) {
                    $q->where('theater_id', $this->theaterId);
                })->where('status', 'Đã hủy')->count(),
            'pending' => Ticket::whereHas('showtime.screen', function($q) {
                    $q->where('theater_id', $this->theaterId);
                })->where('status', 'Chờ thanh toán')->count(),
            'revenue' => Ticket::whereHas('showtime.screen', function($q) {
                    $q->where('theater_id', $this->theaterId);
                })->where('status', 'Đã đặt')->sum('price'),
        ];
        
        // Get movies for filter dropdown
        $movies = Movie::join('showtimes', 'movies.id', '=', 'showtimes.movie_id')
            ->join('theater_screens', 'showtimes.screen_id', '=', 'theater_screens.id')
            ->where('theater_screens.theater_id', $this->theaterId)
            ->select('movies.id', 'movies.title')
            ->distinct()
            ->orderBy('movies.title')
            ->get()
            ->map(function($item) {
                return ['id' => $item->id, 'title' => $item->title];
            })
            ->toArray();
        
        $movie_id = $request->input('movie_id');
        $status = $request->input('status');
        
        return view('admin.moderator.tickets', compact('theater', 'tickets', 'stats', 'movies', 'movie_id', 'status'));
    }
    
    /**
     * Statistics
     */
    public function statistics(Request $request)
    {
        if ($error = $this->checkPermission()) return $error;
        
        $theater = Theater::findOrFail($this->theaterId);
        
        // Revenue by movie
        $revenueByMovie = Movie::join('showtimes', 'movies.id', '=', 'showtimes.movie_id')
            ->join('theater_screens', 'showtimes.screen_id', '=', 'theater_screens.id')
            ->leftJoin('tickets', function($join) {
                $join->on('showtimes.id', '=', 'tickets.showtime_id')
                     ->where('tickets.status', 'Đã đặt');
            })
            ->where('theater_screens.theater_id', $this->theaterId)
            ->groupBy('movies.id', 'movies.title')
            ->select('movies.id', 'movies.title')
            ->selectRaw('COUNT(tickets.id) as ticket_count, COALESCE(SUM(tickets.price), 0) as revenue')
            ->orderByDesc('revenue')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'title' => $item->title,
                    'ticket_count' => $item->ticket_count,
                    'revenue' => $item->revenue
                ];
            })
            ->toArray();
        
        // Revenue by date (30 days) - grouped by movie
        $dates = [];
        $revenueByDateRaw = [];
        
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $dates[] = $date;
            
            // Get revenue per movie for this date
            $dailyRevenue = Movie::join('showtimes', 'movies.id', '=', 'showtimes.movie_id')
                ->join('theater_screens', 'showtimes.screen_id', '=', 'theater_screens.id')
                ->leftJoin('tickets', function($join) use ($date) {
                    $join->on('showtimes.id', '=', 'tickets.showtime_id')
                         ->where('tickets.status', 'Đã đặt')
                         ->whereDate('tickets.created_at', $date);
                })
                ->where('theater_screens.theater_id', $this->theaterId)
                ->whereDate('showtimes.show_date', '<=', $date)
                ->groupBy('movies.id', 'movies.title')
                ->select('movies.id', 'movies.title')
                ->selectRaw('COALESCE(SUM(tickets.price), 0) as revenue')
                ->get();
            
            foreach ($dailyRevenue as $movieRevenue) {
                if (!isset($revenueByDateRaw[$movieRevenue->id])) {
                    $revenueByDateRaw[$movieRevenue->id] = [
                        'title' => $movieRevenue->title,
                        'data' => []
                    ];
                }
                $revenueByDateRaw[$movieRevenue->id]['data'][] = [
                    'date' => $date,
                    'revenue' => $movieRevenue->revenue
                ];
            }
        }
        
        $revenueByDate = $revenueByDateRaw;
        
        // Get available dates from showtimes
        $availableDates = Showtime::whereHas('screen', function($q) {
                $q->where('theater_id', $this->theaterId);
            })
            ->select('show_date')
            ->distinct()
            ->orderBy('show_date', 'desc')
            ->get()
            ->map(function($item) {
                return ['show_date' => $item->show_date];
            })
            ->toArray();
        
        // Get available movies
        $availableMovies = Movie::join('showtimes', 'movies.id', '=', 'showtimes.movie_id')
            ->join('theater_screens', 'showtimes.screen_id', '=', 'theater_screens.id')
            ->where('theater_screens.theater_id', $this->theaterId)
            ->select('movies.id', 'movies.title')
            ->distinct()
            ->orderBy('movies.title')
            ->get()
            ->map(function($item) {
                return ['id' => $item->id, 'title' => $item->title];
            })
            ->toArray();
        
        // Get available screens
        $availableScreens = Screen::where('theater_id', $this->theaterId)
            ->select('id', 'screen_name')
            ->orderBy('screen_name')
            ->get()
            ->map(function($item) {
                return ['id' => $item->id, 'screen_name' => $item->screen_name];
            })
            ->toArray();
        
        // Get available times
        $availableTimes = Showtime::whereHas('screen', function($q) {
                $q->where('theater_id', $this->theaterId);
            })
            ->select('show_time')
            ->distinct()
            ->orderBy('show_time')
            ->get()
            ->map(function($item) {
                return ['show_time' => $item->show_time];
            })
            ->toArray();
        
        // Get fill rate filters from request
        $fillRateMovieFilter = $request->input('fill_rate_movie', 'all');
        $fillRateDateFilter = $request->input('fill_rate_date', 'all');
        $fillRateScreenFilter = $request->input('fill_rate_screen', 'all');
        $fillRateTimeFilter = $request->input('fill_rate_time', 'all');
        
        // Fill rate by date and screen
        $fillRateQuery = Showtime::with(['movie', 'screen'])
            ->whereHas('screen', function($q) {
                $q->where('theater_id', $this->theaterId);
            });
        
        // Apply filters
        if ($fillRateDateFilter !== 'all') {
            $fillRateQuery->where('show_date', $fillRateDateFilter);
        }
        if ($fillRateMovieFilter !== 'all') {
            $fillRateQuery->where('movie_id', $fillRateMovieFilter);
        }
        if ($fillRateScreenFilter !== 'all') {
            $fillRateQuery->where('screen_id', $fillRateScreenFilter);
        }
        if ($fillRateTimeFilter !== 'all') {
            $fillRateQuery->where('show_time', $fillRateTimeFilter);
        }
        
        $fillRateData = $fillRateQuery->get()
            ->groupBy('show_date')
            ->map(function($showtimes, $date) {
                $screens = $showtimes->map(function($showtime) {
                    $bookedTickets = Ticket::where('showtime_id', $showtime->id)
                        ->where('status', 'Đã đặt')
                        ->count();
                    
                    $pickedUpTickets = Ticket::where('showtime_id', $showtime->id)
                        ->where('status', 'Đã đặt')
                        ->where('is_picked_up', true)
                        ->count();
                    
                    $fillRate = $bookedTickets > 0 ? ($pickedUpTickets / $bookedTickets) * 100 : 0;
                    
                    return [
                        'showtime_id' => $showtime->id,
                        'movie_id' => $showtime->movie_id,
                        'movie_title' => $showtime->movie ? $showtime->movie->title : 'N/A',
                        'screen_id' => $showtime->screen_id,
                        'screen_name' => $showtime->screen ? $showtime->screen->screen_name : 'N/A',
                        'show_time' => $showtime->show_time,
                        'booked_tickets' => $bookedTickets,
                        'picked_up_tickets' => $pickedUpTickets,
                        'fill_rate' => round($fillRate, 2)
                    ];
                });
                
                return [
                    'show_date' => $date,
                    'screens' => $screens->values()->toArray()
                ];
            })
            ->values()
            ->toArray();
        
        $fillRateByDate = $fillRateData;
        
        // Fill rate average by movie
        $fillRateByMovieAvg = Movie::join('showtimes', 'movies.id', '=', 'showtimes.movie_id')
            ->join('theater_screens', 'showtimes.screen_id', '=', 'theater_screens.id')
            ->where('theater_screens.theater_id', $this->theaterId)
            ->select('movies.id as movie_id', 'movies.title as movie_title')
            ->selectRaw('
                COUNT(DISTINCT showtimes.id) as total_showtimes,
                COALESCE(SUM(CASE WHEN tickets.status = "Đã đặt" THEN 1 ELSE 0 END), 0) as total_booked_tickets,
                COALESCE(SUM(CASE WHEN tickets.status = "Đã đặt" AND tickets.is_picked_up = 1 THEN 1 ELSE 0 END), 0) as total_picked_up_tickets
            ')
            ->leftJoin('tickets', 'showtimes.id', '=', 'tickets.showtime_id')
            ->groupBy('movies.id', 'movies.title')
            ->get()
            ->map(function($item) {
                $avgFillRate = $item->total_booked_tickets > 0 
                    ? ($item->total_picked_up_tickets / $item->total_booked_tickets) * 100 
                    : 0;
                
                return [
                    'movie_id' => $item->movie_id,
                    'movie_title' => $item->movie_title,
                    'total_booked_tickets' => $item->total_booked_tickets,
                    'total_picked_up_tickets' => $item->total_picked_up_tickets,
                    'avg_fill_rate' => round($avgFillRate, 2)
                ];
            })
            ->toArray();
        
        return view('admin.moderator.statistics', compact(
            'theater', 
            'revenueByMovie', 
            'revenueByDate', 
            'dates',
            'availableDates',
            'availableMovies',
            'availableScreens',
            'availableTimes',
            'fillRateByDate',
            'fillRateByMovieAvg',
            'fillRateMovieFilter',
            'fillRateDateFilter',
            'fillRateScreenFilter',
            'fillRateTimeFilter'
        ));
    }
    
    // Helper methods
    private function isModerator($user)
    {
        if ($user->role === 'moderator') {
            return true;
        }
        
        if ($user->roles && $user->roles->contains(function($role) {
            return in_array($role->name, ['Moderator', 'Theater Manager']);
        })) {
            return true;
        }
        
        return false;
    }
    
    /**
     * Get available time slots for a screen on a specific date
     */
    public function getAvailableTimeSlots(Request $request)
    {
        if ($error = $this->checkPermission()) return $error;
        
        $screenId = $request->screen_id;
        $date = $request->date;
        $movieId = $request->movie_id;
        
        if (!$screenId || !$date || !$movieId) {
            return response()->json(['slots' => []]);
        }
        
        // Lấy thông tin phim
        $movie = Movie::find($movieId);
        if (!$movie) {
            return response()->json(['slots' => []]);
        }
        
        $movieDuration = $movie->duration ?? 120;
        
        // Lấy tất cả suất chiếu trong ngày
        $existingShowtimes = Showtime::where('screen_id', $screenId)
            ->where('show_date', $date)
            ->with('movie')
            ->orderBy('show_time')
            ->get();
        
        // Tạo danh sách khung giờ có thể (từ 8:00 đến 23:00, mỗi 30 phút)
        $possibleSlots = [];
        $startHour = 8;
        $endHour = 23;
        
        for ($hour = $startHour; $hour <= $endHour; $hour++) {
            foreach ([0, 30] as $minute) {
                if ($hour == $endHour && $minute > 0) break;
                
                $timeSlot = sprintf('%02d:%02d', $hour, $minute);
                $slotStart = \Carbon\Carbon::parse($date . ' ' . $timeSlot);
                $slotEnd = $slotStart->copy()->addMinutes($movieDuration + 15);
                
                // Kiểm tra xem khung giờ này có trùng với suất chiếu nào không
                $isAvailable = true;
                foreach ($existingShowtimes as $showtime) {
                    $existingStart = \Carbon\Carbon::parse($showtime->show_date . ' ' . $showtime->show_time);
                    $existingDuration = $showtime->movie->duration ?? 120;
                    $existingEnd = $existingStart->copy()->addMinutes($existingDuration + 15);
                    
                    if ($slotStart < $existingEnd && $slotEnd > $existingStart) {
                        $isAvailable = false;
                        break;
                    }
                }
                
                if ($isAvailable) {
                    $possibleSlots[] = [
                        'time' => $timeSlot,
                        'label' => $timeSlot . ' (kết thúc lúc ' . $slotEnd->format('H:i') . ')'
                    ];
                }
            }
        }
        
        return response()->json(['slots' => $possibleSlots]);
    }
    
    private function generateSeatLayout($request)
    {
        $rowLetters = range('A', 'Z');
        $numRows = max((int) $request->input('num_rows', 12), 1);
        $numGroups = max((int) $request->input('num_groups', 1), 1);
        $seatsPerGroupRow = max((int) $request->input('seats_per_group_row', 12), 1);
        $numVipRows = max((int) $request->input('num_vip_rows', 3), 0);
        $hasCoupleRow = $request->input('has_couple_row', '1') !== '0';

        $rows = array_slice($rowLetters, 0, $numRows);
        $totalCols = $numGroups * $seatsPerGroupRow;
        $cols = range(1, $totalCols);

        $vipRowsStart = max((int) floor(($numRows - min($numVipRows, $numRows)) / 2), 0);
        $vipRows = array_slice($rows, $vipRowsStart, min($numVipRows, $numRows));
        $coupleRows = $hasCoupleRow && !empty($rows) ? [end($rows)] : [];

        $seatGroups = [];
        for ($group = 0; $group < $numGroups; $group++) {
            $start = ($group * $seatsPerGroupRow) + 1;
            $seatGroups[] = [
                'rows' => $rows,
                'cols' => range($start, $start + $seatsPerGroupRow - 1),
            ];
        }
        
        return json_encode([
            'rows' => $rows,
            'cols' => $cols,
            'seat_groups' => $seatGroups,
            'vip_rows' => $vipRows,
            'couple_rows' => $coupleRows,
            'layout_type' => 'grouped',
        ]);
    }
    
    /**
     * Counter Staff Management
     */
    public function counterStaff()
    {
        if ($error = $this->checkPermission()) return $error;
        
        $theater = Theater::findOrFail($this->theaterId);
        
        // Get counter staff for this theater (users with theater_id and role = 'user')
        $counterStaff = User::where('theater_id', $this->theaterId)
            ->where('role', 'user') // Counter staff là user có theater_id
            ->orderBy('name')
            ->get()
            ->map(function($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone ?? 'N/A',
                    'created_at' => $user->created_at->format('d/m/Y H:i'),
                ];
            })
            ->toArray();
        
        return view('admin.moderator.counter_staff', compact('theater', 'counterStaff'));
    }
    
    public function counterStaffStore(Request $request)
    {
        if ($error = $this->checkPermission()) return $error;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'phone' => 'nullable|string|max:20',
        ]);
        
        try {
            User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => $request->password, // Laravel 11 auto-hashes if cast is set
                'phone' => $request->phone,
                'role' => 'user', // Counter staff là user có theater_id
                'theater_id' => $this->theaterId,
                'is_active' => true,
                'status' => 'active',
            ]);
            
            return redirect()->route('moderator.counterStaff')
                ->with('success', 'Thêm nhân viên thành công!');
        } catch (\Exception $e) {
            \Log::error('Error creating counter staff: ' . $e->getMessage());
            return redirect()->back()
                ->withInput()
                ->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    public function counterStaffUpdate(Request $request, $id)
    {
        if ($error = $this->checkPermission()) return $error;
        
        $staff = User::where('id', $id)
            ->where('theater_id', $this->theaterId)
            ->where('role', 'user') // Counter staff là user có theater_id
            ->firstOrFail();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
            'phone' => 'nullable|string|max:20',
        ]);
        
        $data = $request->only(['name', 'email', 'phone']);
        
        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:6']);
            $data['password'] = $request->password; // Auto-hashed by model
        }
        
        $staff->update($data);
        
        return redirect()->route('moderator.counterStaff')
            ->with('success', 'Cập nhật nhân viên thành công!');
    }
    
    public function counterStaffDelete($id)
    {
        if ($error = $this->checkPermission()) return $error;
        
        $staff = User::where('id', $id)
            ->where('theater_id', $this->theaterId)
            ->where('role', 'user') // Counter staff là user có theater_id
            ->firstOrFail();
        
        $staff->delete();
        
        return redirect()->route('moderator.counterStaff')
            ->with('success', 'Xóa nhân viên thành công!');
    }
    
    /**
     * Food Items Management
     */
    public function foodItems()
    {
        if ($error = $this->checkPermission()) return $error;
        
        $theater = Theater::findOrFail($this->theaterId);
        
        // Get food items for this theater
        $foodItems = FoodItem::where('theater_id', $this->theaterId)
            ->orderBy('category')
            ->orderBy('name')
            ->get()
            ->map(function($item) {
                return [
                    'id' => $item->id,
                    'name' => $item->name,
                    'category' => $item->category,
                    'price' => $item->price,
                    'description' => $item->description,
                    'image' => $item->image,
                    'is_available' => $item->is_available,
                ];
            })
            ->toArray();
        
        return view('admin.moderator.food_items', compact('theater', 'foodItems'));
    }
    
    public function foodItemsStore(Request $request)
    {
        if ($error = $this->checkPermission()) return $error;
        
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:combo,drink,snack,other',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);
        
        $data = $request->only(['name', 'category', 'price', 'description']);
        $data['theater_id'] = $this->theaterId;
        $data['is_available'] = $request->has('is_available');
        
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('food_items', 'public');
            $data['image'] = $imagePath;
        }
        
        FoodItem::create($data);
        
        return redirect()->route('moderator.foodItems')
            ->with('success', 'Thêm đồ ăn thành công!');
    }
    
    public function foodItemsUpdate(Request $request, $id)
    {
        if ($error = $this->checkPermission()) return $error;
        
        $foodItem = FoodItem::where('id', $id)
            ->where('theater_id', $this->theaterId)
            ->firstOrFail();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:combo,drink,snack,other',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|max:2048',
        ]);
        
        $data = $request->only(['name', 'category', 'price', 'description']);
        $data['is_available'] = $request->has('is_available');
        
        if ($request->hasFile('image')) {
            // Delete old image
            if ($foodItem->image) {
                Storage::disk('public')->delete($foodItem->image);
            }
            $imagePath = $request->file('image')->store('food_items', 'public');
            $data['image'] = $imagePath;
        }
        
        $foodItem->update($data);
        
        return redirect()->route('moderator.foodItems')
            ->with('success', 'Cập nhật đồ ăn thành công!');
    }
    
    public function foodItemsDelete($id)
    {
        if ($error = $this->checkPermission()) return $error;
        
        $foodItem = FoodItem::where('id', $id)
            ->where('theater_id', $this->theaterId)
            ->firstOrFail();
        
        // Delete image if exists
        if ($foodItem->image) {
            Storage::disk('public')->delete($foodItem->image);
        }
        
        $foodItem->delete();
        
        return redirect()->route('moderator.foodItems')
            ->with('success', 'Xóa đồ ăn thành công!');
    }
}
