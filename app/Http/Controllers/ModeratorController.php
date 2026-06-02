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
        $this->middleware(function ($request, $next) {
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
            
            return $next($request);
        });
    }
    
    /**
     * Dashboard
     */
    public function index()
    {
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
        $topMovies = Movie::select('movies.*')
            ->join('showtimes', 'movies.id', '=', 'showtimes.movie_id')
            ->join('theater_screens', 'showtimes.screen_id', '=', 'theater_screens.id')
            ->leftJoin('tickets', function($join) {
                $join->on('showtimes.id', '=', 'tickets.showtime_id')
                     ->where('tickets.status', 'Đã đặt');
            })
            ->where('theater_screens.theater_id', $this->theaterId)
            ->groupBy('movies.id')
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
        
        return view('admin.moderator.dashboard', compact('stats', 'revenueByDay', 'topMovies', 'upcomingShowtimes'));
    }
    
    /**
     * Permission Requests
     */
    public function permissionRequests(Request $request)
    {
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
        $date = $request->input('date', date('Y-m-d'));
        
        $showtimes = Showtime::with(['movie', 'screen'])
            ->whereHas('screen', function($q) {
                $q->where('theater_id', $this->theaterId);
            })
            ->where('show_date', $date)
            ->orderBy('show_time')
            ->get();
        
        $movies = Movie::where('status', 'Chiếu rạp')->orderBy('title')->get();
        $screens = Screen::where('theater_id', $this->theaterId)->orderBy('screen_name')->get();
        
        return view('admin.moderator.showtimes', compact('showtimes', 'movies', 'screens', 'date'));
    }
    
    public function showtimesStore(Request $request)
    {
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
        
        // Kiểm tra trùng lặp
        $existing = Showtime::where('screen_id', $request->screen_id)
            ->where('show_date', $request->show_date)
            ->where('show_time', $request->show_time)
            ->exists();
        
        if ($existing) {
            return redirect()->back()->with('error', 'Phòng chiếu đã có lịch vào giờ này!');
        }
        
        Showtime::create([
            'movie_id' => $request->movie_id,
            'theater_id' => $this->theaterId,
            'screen_id' => $request->screen_id,
            'show_date' => $request->show_date,
            'show_time' => $request->show_time,
            'price' => $request->price,
        ]);
        
        return redirect()->route('moderator.showtimes')
            ->with('success', 'Thêm lịch chiếu thành công!');
    }
    
    public function showtimesUpdate(Request $request, $id)
    {
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
        $screens = Screen::where('theater_id', $this->theaterId)
            ->withCount(['showtimes' => function($q) {
                $q->where('show_date', '>=', today());
            }])
            ->orderBy('screen_name')
            ->get();
        
        return view('admin.moderator.screens', compact('screens'));
    }
    
    public function screensStore(Request $request)
    {
        $request->validate([
            'screen_name' => 'required|string|max:255',
            'screen_type' => 'required|string',
            'total_seats' => 'required|integer|min:1',
        ]);
        
        // Kiểm tra trùng tên
        $existing = Screen::where('theater_id', $this->theaterId)
            ->where('screen_name', $request->screen_name)
            ->exists();
        
        if ($existing) {
            return redirect()->back()->with('error', 'Tên phòng đã tồn tại!');
        }
        
        Screen::create([
            'theater_id' => $this->theaterId,
            'screen_name' => $request->screen_name,
            'screen_type' => $request->screen_type,
            'total_seats' => $request->total_seats,
            'seat_layout_config' => $this->generateSeatLayout($request),
        ]);
        
        return redirect()->route('moderator.screens')
            ->with('success', 'Thêm phòng chiếu thành công!');
    }
    
    /**
     * Theater Info
     */
    public function theater()
    {
        $theater = Theater::findOrFail($this->theaterId);
        $screens = Screen::where('theater_id', $this->theaterId)->get();
        
        return view('admin.moderator.theater', compact('theater', 'screens'));
    }
    
    public function theaterUpdate(Request $request)
    {
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
        
        $stats = [
            'total' => Ticket::whereHas('showtime.screen', function($q) {
                    $q->where('theater_id', $this->theaterId);
                })->count(),
            'sold' => Ticket::whereHas('showtime.screen', function($q) {
                    $q->where('theater_id', $this->theaterId);
                })->where('status', 'Đã đặt')->count(),
            'revenue' => Ticket::whereHas('showtime.screen', function($q) {
                    $q->where('theater_id', $this->theaterId);
                })->where('status', 'Đã đặt')->sum('price'),
        ];
        
        return view('admin.moderator.tickets', compact('tickets', 'stats'));
    }
    
    /**
     * Statistics
     */
    public function statistics(Request $request)
    {
        // Revenue by movie
        $revenueByMovie = Movie::select('movies.*')
            ->join('showtimes', 'movies.id', '=', 'showtimes.movie_id')
            ->join('theater_screens', 'showtimes.screen_id', '=', 'theater_screens.id')
            ->leftJoin('tickets', function($join) {
                $join->on('showtimes.id', '=', 'tickets.showtime_id')
                     ->where('tickets.status', 'Đã đặt');
            })
            ->where('theater_screens.theater_id', $this->theaterId)
            ->groupBy('movies.id')
            ->selectRaw('COUNT(tickets.id) as ticket_count, COALESCE(SUM(tickets.price), 0) as revenue')
            ->orderByDesc('revenue')
            ->get();
        
        // Revenue by date (30 days)
        $revenueByDate = [];
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $revenue = Ticket::whereHas('showtime.screen', function($q) {
                    $q->where('theater_id', $this->theaterId);
                })
                ->where('status', 'Đã đặt')
                ->whereDate('created_at', $date)
                ->sum('price');
            
            $revenueByDate[] = ['date' => $date, 'revenue' => $revenue];
        }
        
        return view('admin.moderator.statistics', compact('revenueByMovie', 'revenueByDate'));
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
    
    private function generateSeatLayout($request)
    {
        $rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
        $cols = range(1, 12);
        
        return json_encode([
            'rows' => $rows,
            'cols' => $cols,
            'vip_rows' => ['D', 'E', 'F'],
            'couple_rows' => ['L'],
        ]);
    }
}
