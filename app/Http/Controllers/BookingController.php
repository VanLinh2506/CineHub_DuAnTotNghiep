<?php

namespace App\Http\Controllers;

use App\Models\Movie;
use App\Models\Theater;
use App\Models\Showtime;
use App\Models\Screen;
use App\Models\Ticket;
use App\Models\Booking;
use App\Models\FoodItem;
use App\Models\Transaction;
use App\Services\VNPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    public function __construct(protected VNPayService $vnpay) {}
    /**
     * Booking homepage - Select movie, theater, date, time
     */
    public function index(Request $request)
    {
        // Only require auth for actual booking submission, not for browsing
        $selectedMovieId = $request->input('movie');
        $selectedTheater = $request->input('theater');
        $selectedDate = $request->input('date', date('Y-m-d'));
        $selectedShowtimeId = $request->input('showtime_id');
        
        // Get only movies that have upcoming showtimes (actually showing in theaters)
        $allMovies = Movie::where('status', 'Chiếu rạp')
            ->whereHas('showtimes', function($query) {
                $query->where(DB::raw("CONCAT(show_date, ' ', show_time)"), '>=', now()->format('Y-m-d H:i:s'));
            })
            ->orderBy('title')
            ->get();
        
        $theaters = [];
        $showtimes = [];
        $movie = null;
        $bookedSeats = [];
        $reservedSeats = [];
        $seatLayout = null;
        $screenInfo = null;
        $theaterInfo = null;
        $foodItems = FoodItem::where('is_active', true)->get();
        
        // If showtime selected, get booking info
        if ($selectedShowtimeId) {
            $showtime = Showtime::with(['movie', 'theater', 'screen'])->find($selectedShowtimeId);
            
            if ($showtime) {
                // Check if showtime is still valid (not in the past)
                $showtimeDateTime = $showtime->show_date . ' ' . $showtime->show_time;
                if ($showtimeDateTime < now()->format('Y-m-d H:i:s')) {
                    // Showtime has passed, redirect back to movie selection
                    return redirect()->route('booking.index')->with('error', 'Lịch chiếu đã qua. Vui lòng chọn lịch chiếu khác.');
                }
                
                $selectedMovieId = $showtime->movie_id;
                $selectedTheater = $showtime->theater_id;
                $selectedDate = $showtime->show_date;
                
                $movie = $showtime->movie;
                $theaterInfo = $showtime->theater;
                $screenInfo = $showtime->screen;
                
                // Get booked seats
                $bookedSeats = Ticket::where('showtime_id', $selectedShowtimeId)
                    ->where('status', 'Đã đặt')
                    ->pluck('seat')
                    ->toArray();
                
                // Get seat layout
                if ($screenInfo && isset($screenInfo->seat_layout_config)) {
                    $seatLayoutData = $screenInfo->seat_layout_config;
                    
                    // Only decode if it's a string
                    if (is_string($seatLayoutData) && !empty($seatLayoutData)) {
                        try {
                            $decoded = json_decode($seatLayoutData, true);
                            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                $seatLayout = $decoded;
                            } else {
                                Log::warning('Invalid JSON in seat_layout_config for screen: ' . $screenInfo->id);
                                $seatLayout = null;
                            }
                        } catch (\Exception $e) {
                            Log::error('Failed to decode seat layout: ' . $e->getMessage());
                            $seatLayout = null;
                        }
                    } else {
                        // Not a string or empty
                        $seatLayout = null;
                    }
                } else {
                    $seatLayout = null;
                }
            } else {
                // Showtime not found, redirect back
                return redirect()->route('booking.index')->with('error', 'Lịch chiếu không tồn tại.');
            }
        }
        
        // Get theaters for selected movie
        if ($selectedMovieId) {
            $movie = Movie::find($selectedMovieId);
            
            $theaters = Theater::whereHas('showtimes', function($q) use ($selectedMovieId) {
                    $q->where('movie_id', $selectedMovieId)
                      ->where(DB::raw("CONCAT(show_date, ' ', show_time)"), '>=', now()->format('Y-m-d H:i:s'));
                })->get();
        }
        
        // Get showtimes
        if ($selectedMovieId && $selectedTheater && $selectedDate) {
            $showtimes = Showtime::with('screen')
                ->where('movie_id', $selectedMovieId)
                ->where('theater_id', $selectedTheater)
                ->where('show_date', $selectedDate)
                ->where(DB::raw("CONCAT(show_date, ' ', show_time)"), '>=', now()->format('Y-m-d H:i:s'))
                ->orderBy('show_time')
                ->get();
        }
        
        // Calculate prices
        $basePrice = $selectedShowtimeId && isset($showtime) ? $showtime->price : 90000;
        $screenType = $screenInfo->screen_type ?? '2D';
        $screenSurcharge = $this->getScreenTypeSurcharge($screenType);
        
        $normalPrice = $basePrice + $screenSurcharge;
        $vipPrice = $basePrice + $screenSurcharge + ($basePrice * 0.3);
        $couplePrice = $basePrice + $screenSurcharge + ($basePrice * 0.5);
        
        // Generate dates (next 7 days)
        $dates = collect(range(0, 6))->map(function($i) {
            $date = now()->addDays($i);
            return [
                'value' => $date->toDateString(),
                'label' => $date->format('d/m'),
                'day_name' => $date->isoFormat('dddd'),
                'is_today' => $i === 0,
            ];
        });
        
        // Add selected_movie variable for view
        $selected_movie = $selectedMovieId;
        $selected_showtime_id = $selectedShowtimeId;
        $selected_theater = $selectedTheater;
        $selected_date = $selectedDate;
        
        return view('booking.index', compact(
            'allMovies', 'theaters', 'showtimes', 'movie', 'selectedMovieId',
            'selectedTheater', 'selectedDate', 'selectedShowtimeId', 'dates',
            'bookedSeats', 'reservedSeats', 'seatLayout', 'normalPrice', 'vipPrice',
            'couplePrice', 'foodItems', 'screenInfo', 'theaterInfo', 'screenType',
            'basePrice', 'screenSurcharge', 'selected_movie', 'selected_showtime_id',
            'selected_theater', 'selected_date'
        ));
    }
    
    /**
     * API: Get seat map and booked seats for a showtime
     */
    public function getSeatMap(Request $request)
    {
        $showtimeId = $request->input('showtime_id');
        
        if (!$showtimeId) {
            return response()->json(['error' => 'Showtime ID required'], 400);
        }
        
        $showtime = Showtime::with('screen')->find($showtimeId);
        
        if (!$showtime) {
            return response()->json(['error' => 'Showtime not found'], 404);
        }
        
        // Get booked seats
        $bookedSeats = Ticket::where('showtime_id', $showtimeId)
            ->where('status', 'Đã đặt')
            ->pluck('seat')
            ->toArray();
        
        // Get seat layout
        $seatLayout = null;
        if ($showtime->screen && isset($showtime->screen->seat_layout_config)) {
            $seatLayoutData = $showtime->screen->seat_layout_config;
            
            if (is_string($seatLayoutData)) {
                try {
                    $seatLayout = json_decode($seatLayoutData, true);
                } catch (\Exception $e) {
                    $seatLayout = null;
                }
            } elseif (is_array($seatLayoutData)) {
                $seatLayout = $seatLayoutData;
            }
        }
        
        // Calculate prices
        $basePrice = $showtime->price;
        $screenType = $showtime->screen->screen_type ?? '2D';
        $screenSurcharge = $this->getScreenTypeSurcharge($screenType);
        
        $normalPrice = $basePrice + $screenSurcharge;
        $vipPrice = $normalPrice * 1.3;
        $couplePrice = $normalPrice * 1.5;
        
        return response()->json([
            'layout' => $seatLayout,
            'bookedSeats' => $bookedSeats,
            'prices' => [
                'base' => $basePrice,
                'normal' => $normalPrice,
                'vip' => $vipPrice,
                'couple' => $couplePrice,
            ],
            'screen' => [
                'name' => $showtime->screen->screen_name ?? '',
                'type' => $screenType,
            ],
        ]);
    }
    
    /**
     * API: Get showtimes by movie, theater, and date
     */
    public function getShowtimesByDate(Request $request)
    {
        $movieId = $request->input('movie_id');
        $theaterId = $request->input('theater_id');
        $date = $request->input('date');
        
        if (!$movieId || !$theaterId || !$date) {
            return response()->json(['showtimes' => []]);
        }
        
        $showtimes = Showtime::with('screen')
            ->where('movie_id', $movieId)
            ->where('theater_id', $theaterId)
            ->where('show_date', $date)
            ->where(DB::raw("CONCAT(show_date, ' ', show_time)"), '>=', now()->format('Y-m-d H:i:s'))
            ->orderBy('show_time')
            ->get()
            ->map(function($showtime) {
                return [
                    'id' => $showtime->id,
                    'show_time' => date('H:i', strtotime($showtime->show_time)),
                    'screen_name' => $showtime->screen->name ?? 'N/A',
                    'screen_type' => $showtime->screen->screen_type ?? '2D',
                    'price' => $showtime->price,
                ];
            });
        
        return response()->json(['showtimes' => $showtimes]);
    }
    
    /**
     * Process booking and create pending booking
     */
    public function processBooking(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $request->validate([
            'showtime_id' => 'required|exists:showtimes,id',
            'seats' => 'required|array|min:1|max:8',
            'customer_email' => 'required|email',
            'food_items' => 'nullable|array',
        ]);
        
        $user = Auth::user();
        $showtimeId = $request->input('showtime_id');
        $seats = $request->input('seats');
        $customerEmail = $request->input('customer_email');
        $foodItems = $request->input('food_items', []);
        
        // Validate seats
        $validationError = $this->validateSeatSelection($seats, $showtimeId);
        if ($validationError) {
            return redirect()->back()->with('error', $validationError);
        }
        
        // Get showtime
        $showtime = Showtime::with(['movie', 'screen'])->findOrFail($showtimeId);
        
        // Check if showtime has passed
        if ($showtime->show_date < now()->toDateString() || 
            ($showtime->show_date === now()->toDateString() && $showtime->show_time < now()->toTimeString())) {
            return redirect()->back()->with('error', 'Suất chiếu đã qua!');
        }
        
        // Check seats availability
        $bookedSeatsCount = Ticket::where('showtime_id', $showtimeId)
            ->whereIn('seat', $seats)
            ->where('status', 'Đã đặt')
            ->count();
        
        if ($bookedSeatsCount > 0) {
            return redirect()->back()->with('error', 'Một số ghế đã được đặt!');
        }
        
        DB::beginTransaction();
        
        try {
            // Calculate total amount
            $basePrice = $showtime->price;
            $screenType = $showtime->screen->screen_type ?? '2D';
            $screenSurcharge = $this->getScreenTypeSurcharge($screenType);
            
            $totalAmount = 0;
            $seatPrices = [];
            
            foreach ($seats as $seat) {
                $seatType = $this->getSeatType($seat);
                $price = $this->calculateSeatPrice($basePrice, $screenSurcharge, $seatType);
                $totalAmount += $price;
                $seatPrices[$seat] = $price;
            }
            
            // Add food items price
            $foodTotal = 0;
            $filteredFoodItems = [];
            
            foreach ($foodItems as $foodId => $quantity) {
                if ($quantity > 0) {
                    $food = FoodItem::find($foodId);
                    if ($food) {
                        $foodTotal += $food->price * $quantity;
                        $filteredFoodItems[$foodId] = $quantity;
                    }
                }
            }
            
            $totalAmount += $foodTotal;
            
            // Create pending booking
            $vnpTxnRef = 'BKG' . $user->id . '_' . time();
            
            $booking = Booking::create([
                'user_id' => $user->id,
                'showtime_id' => $showtimeId,
                'seats' => $seats,
                'food_items' => $filteredFoodItems,
                'customer_email' => $customerEmail,
                'customer_name' => $user->name,
                'customer_phone' => $user->phone ?? '',
                'total_amount' => $totalAmount,
                'vnp_txn_ref' => $vnpTxnRef,
                'status' => 'pending',
                'expires_at' => now()->addMinutes(10),
            ]);
            
            // Save to session for VNPay
            session([
                'pending_booking_id' => $booking->id,
                'showtime_id' => $showtimeId,
            ]);
            
            DB::commit();
            
            // Redirect to VNPay
            $orderInfo = 'Thanh toan ve xem phim ' . $showtime->movie->title;
            $paymentUrl = $this->vnpay->createPaymentUrl($booking, $orderInfo, $request->ip());

            return redirect($paymentUrl);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
    
    /**
     * VNPay return handler
     */
    public function vnpayReturn(Request $request)
    {
        $vnpTxnRef        = $request->input('vnp_TxnRef');
        $pendingBookingId = session('pending_booking_id');
        $showtimeId       = session('showtime_id');

        if (!$pendingBookingId) {
            return redirect()->route('home')->with('error', 'Không tìm thấy booking!');
        }

        $booking = Booking::find($pendingBookingId);

        if (!$booking || $booking->vnp_txn_ref !== $vnpTxnRef) {
            return redirect()->route('home')->with('error', 'Booking không hợp lệ!');
        }

        if (!$this->vnpay->isPaymentSuccess($request)) {
            $booking->update(['status' => 'cancelled']);
            return redirect()->route('booking.index', ['showtime_id' => $showtimeId])
                ->with('error', 'Thanh toán thất bại hoặc chữ ký không hợp lệ!');
        }
        // Payment success
        DB::beginTransaction();
        
        try {
            $showtime = Showtime::with('screen')->findOrFail($booking->showtime_id);
            $seats = $booking->seats;
            $basePrice = $showtime->price;
            $screenType = $showtime->screen->screen_type ?? '2D';
            $screenSurcharge = $this->getScreenTypeSurcharge($screenType);
            
            // Create tickets
            foreach ($seats as $seat) {
                $seatType = $this->getSeatType($seat);
                $price = $this->calculateSeatPrice($basePrice, $screenSurcharge, $seatType);
                $qrCode = 'TICKET_' . uniqid() . '_' . time();
                
                Ticket::create([
                    'user_id' => $booking->user_id,
                    'showtime_id' => $booking->showtime_id,
                    'booking_pending_id' => $booking->id,
                    'seat' => $seat,
                    'seat_type' => $seatType,
                    'price' => $price,
                    'qr_code' => $qrCode,
                    'status' => 'Đã đặt',
                ]);
            }
            
            // Create transaction
            Transaction::create([
                'user_id' => $booking->user_id,
                'type' => 'ticket',
                'related_id' => $booking->id,
                'amount' => $booking->total_amount,
                'method' => 'VNPay',
                'status' => 'Thành công',
            ]);
            
            // Update booking
            $booking->update([
                'status' => 'completed',
                'qr_code' => 'BOOKING_' . uniqid() . '_' . $booking->id,
            ]);
            
            DB::commit();
            
            session()->forget(['pending_booking_id', 'showtime_id']);
            
            return redirect()->route('booking.my-tickets')
                ->with('success', 'Đặt vé thành công!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Complete booking error: ' . $e->getMessage());
            
            $booking->update(['status' => 'cancelled']);
            
            return redirect()->route('booking.index')
                ->with('error', 'Có lỗi xảy ra khi hoàn tất đặt vé');
        }
    }
    
    /**
     * My tickets page
     */
    public function myTickets()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        $tickets = Ticket::with(['showtime.movie', 'showtime.theater', 'showtime.screen', 'bookingPending'])
            ->where('user_id', Auth::id())
            ->orderByDesc('created_at')
            ->paginate(20);
        
        return view('booking.my-tickets', compact('tickets'));
    }
    
    /**
     * View ticket QR code/PDF
     */
    public function viewTicket($bookingId)
    {
        $booking = Booking::with(['showtime.movie', 'showtime.theater', 'showtime.screen', 'tickets'])
            ->where('id', $bookingId)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        
        return view('booking.view-ticket', compact('booking'));
    }
    
    // Helper methods
    private function validateSeatSelection($seats, $showtimeId)
    {
        // Simplified validation
        if (count($seats) > 8) {
            return 'Chỉ được đặt tối đa 8 vé';
        }
        
        // Check for gaps
        $seatsByRow = [];
        foreach ($seats as $seat) {
            $row = substr($seat, 0, 1);
            $col = (int)substr($seat, 1);
            $seatsByRow[$row][] = $col;
        }
        
        foreach ($seatsByRow as $row => $cols) {
            sort($cols);
            for ($i = 0; $i < count($cols) - 1; $i++) {
                if ($cols[$i + 1] - $cols[$i] > 1) {
                    return 'Các ghế phải liền kề nhau';
                }
            }
        }
        
        return null;
    }
    
    private function getSeatType($seat)
    {
        $row = substr($seat, 0, 1);
        $vipRows = ['D', 'E', 'F'];
        $coupleRows = ['J', 'K', 'L'];
        
        if (in_array($row, $coupleRows)) return 'couple';
        if (in_array($row, $vipRows)) return 'vip';
        return 'normal';
    }
    
    private function calculateSeatPrice($basePrice, $screenSurcharge, $seatType)
    {
        $total = $basePrice + $screenSurcharge;
        
        return match($seatType) {
            'vip' => $total * 1.3,
            'couple' => $total * 1.5,
            default => $total,
        };
    }
    
    private function getScreenTypeSurcharge($screenType)
    {
        return match($screenType) {
            'IMAX' => 50000,
            '3D' => 30000,
            '4DX' => 70000,
            default => 0,
        };
    }
}
