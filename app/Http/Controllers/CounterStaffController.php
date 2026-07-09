<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Theater;
use App\Models\Showtime;
use App\Models\Ticket;
use App\Models\Booking;
use App\Models\SeatReservation;
use App\Models\Screen;
use App\Models\Movie;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CounterStaffController extends Controller
{
    public function __construct()
    {
        // Middleware đã được apply ở routes, không cần thêm ở đây nữa
        // Chỉ cần lấy theater_id trong mỗi method
    }
    
    /**
     * Dashboard cho counter staff
     */
    public function index()
    {
        $user = Auth::user();
        $theaterId = $user->theater_id;
        $theater = Theater::find($theaterId);
        
        if (!$theater) {
            return redirect()->route('home')->with('error', 'Rạp không tồn tại!');
        }
        
        // Thống kê hôm nay
        $todayStats = [
            'tickets_sold' => Ticket::where('is_counter_sale', true)
                ->where('sold_by', $user->id)
                ->whereDate('created_at', today())
                ->count(),
            
            'revenue' => Ticket::where('is_counter_sale', true)
                ->where('sold_by', $user->id)
                ->whereDate('created_at', today())
                ->sum('price'),
            
            'tickets_scanned' => Ticket::whereHas('showtime.screen', function($q) use ($theaterId) {
                    $q->where('theater_id', $theaterId);
                })
                ->where('is_picked_up', true)
                ->whereDate('picked_up_at', today())
                ->count(),
        ];
        
        // Lịch chiếu hôm nay
        $todayShowtimes = Showtime::with(['movie', 'screen'])
            ->whereHas('screen', function($q) use ($theaterId) {
                $q->where('theater_id', $theaterId);
            })
            ->whereDate('show_date', today())
            ->orderBy('show_time')
            ->limit(10)
            ->get();
        
        return view('admin.counter_staff.dashboard', compact('user', 'theater', 'todayStats', 'todayShowtimes'));
    }
    
    /**
     * Trang quét QR code
     */
    public function scanQR()
    {
        $user = Auth::user();
        return view('admin.counter_staff.scan_qr', [
            'theaterId' => $user->theater_id,
            'title' => 'Quét QR Code vé',
        ]);
    }
    
    /**
     * Xử lý quét QR code và xác nhận vé
     */
    public function verifyTicket(Request $request)
    {
        $bookingId = $request->input('booking_id');
        $bookingCode = $request->input('booking_code');
        
        if (!$bookingId && !$bookingCode) {
            return response()->json(['success' => false, 'message' => 'Thiếu thông tin booking']);
        }
        
        // Tìm booking
        $booking = null;
        if ($bookingId) {
            $booking = Booking::find($bookingId);
        } elseif ($bookingCode) {
            $booking = Booking::where('qr_code', $bookingCode)->first();
        }
        
        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy booking']);
        }
        
        // Lấy tickets
        $tickets = Ticket::with(['showtime.movie', 'showtime.screen', 'user'])
            ->where('booking_pending_id', $booking->id)
            ->where('status', 'Đã đặt')
            ->get();
        
        if ($tickets->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy vé']);
        }
        
        // Kiểm tra theater
        $firstTicket = $tickets->first();
        if ($firstTicket->showtime->screen->theater_id != Auth::user()->theater_id) {
            return response()->json(['success' => false, 'message' => 'Vé không thuộc rạp của bạn']);
        }
        
        // Xác nhận vé
        $updatedCount = 0;
        foreach ($tickets as $ticket) {
            if (!$ticket->is_picked_up) {
                $ticket->update([
                    'is_picked_up' => true,
                    'picked_up_at' => now(),
                    'picked_up_by' => Auth::id(),
                ]);
                $updatedCount++;
            }
        }
        
        return response()->json([
            'success' => true,
            'message' => "Đã xác nhận {$updatedCount} vé đã được lấy",
            'booking' => $booking,
            'tickets' => $tickets,
            'updated_count' => $updatedCount,
        ]);
    }
    
    /**
     * Xem danh sách vé đã quét
     */
    public function scannedTickets(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $page = $request->input('page', 1);
        
        $tickets = Ticket::with(['showtime.movie', 'showtime.screen', 'user', 'bookingPending'])
            ->whereHas('showtime.screen', function($query) {
                $query->where('theater_id', Auth::user()->theater_id);
            })
            ->where('picked_up_by', Auth::id())
            ->whereDate('picked_up_at', $date)
            ->orderByDesc('picked_up_at')
            ->paginate(20);
        
        return view('admin.counter_staff.scanned_tickets', compact('tickets', 'date'));
    }
    
    /**
     * Xem lịch chiếu phim
     */
    public function showtimes(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        
        $theaterId = Auth::user()->theater_id;
        
        $showtimes = Showtime::with(['movie', 'screen', 'tickets' => function($query) {
                $query->where('status', 'Đã đặt');
            }])
            ->whereHas('screen', function($query) use ($theaterId) {
                $query->where('theater_id', $theaterId);
            })
            ->where('show_date', $date)
            ->orderBy('show_time')
            ->get()
            ->map(function($showtime) {
                $bookedSeats = $showtime->tickets->count();
                $reservedSeats = SeatReservation::where('showtime_id', $showtime->id)
                    ->active()
                    ->count();

                $showtime->booked_seats = $bookedSeats;
                $showtime->reserved_seats = $reservedSeats;
                $showtime->available_seats = max(0, $showtime->screen->total_seats - $bookedSeats - $reservedSeats);
                return $showtime;
            });
        
        $theater = Theater::find($theaterId);
        
        return view('admin.counter_staff.showtimes', compact('showtimes', 'theater', 'date'));
    }
    
    /**
     * Trang bán vé trực tiếp
     */
    public function sellTicket(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        $showtimeId = $request->input('showtime_id');
        $theaterId = Auth::user()->theater_id;
        
        $showtimes = Showtime::with(['movie', 'screen', 'tickets' => function($query) {
                $query->where('status', 'Đã đặt');
            }])
            ->whereHas('screen', function($query) use ($theaterId) {
                $query->where('theater_id', $theaterId);
            })
            ->where('show_date', $date)
            ->orderBy('show_time')
            ->get()
            ->map(function($showtime) {
                $bookedSeats = $showtime->tickets->count();
                $reservedSeats = SeatReservation::where('showtime_id', $showtime->id)
                    ->active()
                    ->count();

                $showtime->booked_seats = $bookedSeats;
                $showtime->reserved_seats = $reservedSeats;
                $showtime->available_seats = max(0, $showtime->screen->total_seats - $bookedSeats - $reservedSeats);
                return $showtime;
            });
        
        $selectedShowtime = null;
        $bookedSeats = [];
        $seatLayout = null;
        
        if ($showtimeId) {
            $selectedShowtime = Showtime::with(['movie', 'screen'])
                ->whereHas('screen', function($query) use ($theaterId) {
                    $query->where('theater_id', $theaterId);
                })
                ->find($showtimeId);
            
            if ($selectedShowtime) {
                // Ghế đã đặt
                $bookedSeats = Ticket::where('showtime_id', $showtimeId)
                    ->where('status', 'Đã đặt')
                    ->pluck('seat')
                    ->toArray();
                
                // Ghế đang giữ theo server timer.
                $reservedSeats = SeatReservation::where('showtime_id', $showtimeId)
                    ->active()
                    ->pluck('seat')
                    ->toArray();
                
                $bookedSeats = array_unique(array_merge($bookedSeats, $reservedSeats));
                
                // Seat layout
                $seatLayout = $selectedShowtime->screen->seat_layout_config 
                    ? json_decode($selectedShowtime->screen->seat_layout_config, true)
                    : $this->getDefaultSeatLayout();
            }
        }
        
        return view('admin.counter_staff.sell_ticket', compact(
            'showtimes',
            'selectedShowtime',
            'bookedSeats',
            'seatLayout',
            'date'
        ));
    }
    
    /**
     * Xử lý bán vé
     */
    public function processSale(Request $request)
    {
        $request->validate([
            'showtime_id' => 'required|exists:showtimes,id',
            'seats' => 'required|array|min:1',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
        ]);
        
        $showtimeId = $request->input('showtime_id');
        $seats = $request->input('seats');
        $customerName = $request->input('customer_name', 'Khách lẻ');
        $customerPhone = $request->input('customer_phone', '');
        
        $theaterId = Auth::user()->theater_id;
        
        $showtime = Showtime::with(['movie', 'screen'])
            ->whereHas('screen', function($query) use ($theaterId) {
                $query->where('theater_id', $theaterId);
            })
            ->findOrFail($showtimeId);
        
        // Kiểm tra ghế
        $existingSeats = Ticket::where('showtime_id', $showtimeId)
            ->where('status', 'Đã đặt')
            ->whereIn('seat', $seats)
            ->pluck('seat')
            ->toArray();
        
        if (!empty($existingSeats)) {
            return response()->json([
                'success' => false,
                'message' => 'Ghế ' . implode(', ', $existingSeats) . ' đã được đặt'
            ]);
        }
        
        DB::beginTransaction();
        
        try {
            $bookingCode = 'COUNTER_' . uniqid() . '_' . time();
            $totalAmount = 0;
            
            foreach ($seats as $seat) {
                $seatType = $this->getSeatType($seat);
                $price = $this->getSeatPrice($showtime->price, $seatType);
                $totalAmount += $price;
            }
            
            // Tạo booking
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'showtime_id' => $showtimeId,
                'seats' => $seats,
                'total_amount' => $totalAmount,
                'status' => 'completed',
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'qr_code' => $bookingCode,
            ]);
            
            // Tạo tickets
            $ticketIds = [];
            foreach ($seats as $seat) {
                $seatType = $this->getSeatType($seat);
                $price = $this->getSeatPrice($showtime->price, $seatType);
                $qrCode = 'TICKET_COUNTER_' . uniqid() . '_' . $booking->id . '_' . $seat;
                
                $ticket = Ticket::create([
                    'user_id' => Auth::id(),
                    'showtime_id' => $showtimeId,
                    'booking_pending_id' => $booking->id,
                    'seat' => $seat,
                    'seat_type' => $seatType,
                    'price' => $price,
                    'qr_code' => $qrCode,
                    'status' => 'Đã đặt',
                    'is_counter_sale' => true,
                    'sold_by' => Auth::id(),
                ]);
                
                $ticketIds[] = $ticket->id;
            }
            
            // Tạo transaction
            Transaction::create([
                'user_id' => Auth::id(),
                'type' => 'ticket',
                'related_id' => $booking->id,
                'amount' => $totalAmount,
                'method' => 'Tiền mặt',
                'status' => 'Thành công',
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => 'Bán vé thành công',
                'booking_id' => $booking->id,
                'ticket_ids' => $ticketIds,
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Counter sale error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Có lỗi xảy ra: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Lịch sử bán vé
     */
    public function salesHistory(Request $request)
    {
        $date = $request->input('date', '');
        $search = $request->input('search', '');
        
        $query = Ticket::with(['showtime.movie', 'showtime.screen', 'bookingPending'])
            ->where('is_counter_sale', true)
            ->where('sold_by', Auth::id());
        
        if ($date) {
            $query->whereDate('created_at', $date);
        }
        
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->whereHas('bookingPending', function($bq) use ($search) {
                    $bq->where('customer_name', 'like', "%{$search}%")
                      ->orWhere('customer_phone', 'like', "%{$search}%");
                })->orWhere('seat', 'like', "%{$search}%");
            });
        }
        
        $sales = $query->orderByDesc('created_at')->paginate(20);
        
        // Thống kê hôm nay
        $todayStats = Ticket::where('is_counter_sale', true)
            ->where('sold_by', Auth::id())
            ->whereDate('created_at', now()->toDateString())
            ->selectRaw('COUNT(*) as ticket_count, COALESCE(SUM(price), 0) as total_revenue')
            ->first();
        
        return view('admin.counter_staff.sales_history', compact('sales', 'todayStats', 'date', 'search'));
    }
    
    // Helper methods
    private function isCounterStaff($user)
    {
        // Counter staff: role = 'user' VÀ có theater_id hợp lệ (không empty và là số)
        $isCounterStaff = $user->role === 'user' && 
                         !empty($user->theater_id) && 
                         $user->theater_id != '' &&
                         is_numeric($user->theater_id);
        
        // Moderator cũng có quyền
        $isModerator = $user->role === 'moderator' && 
                      !empty($user->theater_id) && 
                      $user->theater_id != '';
        
        // Admin có quyền
        $isAdmin = $user->role === 'admin';
        
        return $isCounterStaff || $isModerator || $isAdmin;
    }
    
    private function getSeatType($seat)
    {
        $row = substr($seat, 0, 1);
        $vipRows = ['D', 'E', 'F'];
        $coupleRows = ['J'];
        
        if (in_array($row, $coupleRows)) return 'couple';
        if (in_array($row, $vipRows)) return 'vip';
        return 'normal';
    }
    
    private function getSeatPrice($basePrice, $seatType)
    {
        return match($seatType) {
            'vip' => $basePrice * 1.5,
            'couple' => $basePrice * 2.5,
            default => $basePrice,
        };
    }
    
    private function getDefaultSeatLayout()
    {
        return [
            'rows' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'],
            'seats_per_row' => 12,
            'vip_rows' => ['D', 'E', 'F'],
            'couple_rows' => ['J'],
        ];
    }
}
