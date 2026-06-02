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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class BookingController extends Controller
{
    /**
     * Booking homepage - Select movie, theater, date, time
     */
    public function index(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Vui lòng đăng nhập để đặt vé');
        }
        
        $selectedMovieId = $request->input('movie');
        $selectedTheater = $request->input('theater');
        $selectedDate = $request->input('date', date('Y-m-d'));
        $selectedShowtimeId = $request->input('showtime_id');
        
        // Get all theater movies
        $allMovies = Movie::where('status', 'Chiếu rạp')->orderBy('title')->get();
        
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
                if ($screenInfo && $screenInfo->seat_layout_config) {
                    $seatLayout = json_decode($screenInfo->seat_layout_config, true);
                }
            }
        }
        
        // Get theaters for selected movie
        if ($selectedMovieId) {
            $movie = Movie::find($selectedMovieId);
            $theaters = Theater::whereHas('showtimes', function($q) use ($selectedMovieId) {
                    $q->where('movie_id', $selectedMovieId);
                })->get();
        }
        
        // Get showtimes
        if ($selectedMovieId && $selectedTheater && $selectedDate) {
            $showtimes = Showtime::with('screen')
                ->where('movie_id', $selectedMovieId)
                ->where('theater_id', $selectedTheater)
                ->where('show_date', $selectedDate)
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
        
        return view('booking.index', compact(
            'allMovies', 'theaters', 'showtimes', 'movie', 'selectedMovieId',
            'selectedTheater', 'selectedDate', 'selectedShowtimeId', 'dates',
            'bookedSeats', 'reservedSeats', 'seatLayout', 'normalPrice', 'vipPrice',
            'couplePrice', 'foodItems', 'screenInfo', 'theaterInfo', 'screenType',
            'basePrice', 'screenSurcharge'
        ));
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
            return $this->redirectToVNPay($booking, $showtime);
            
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
        $vnpResponseCode = $request->input('vnp_ResponseCode');
        $vnpTxnRef = $request->input('vnp_TxnRef');
        
        $pendingBookingId = session('pending_booking_id');
        $showtimeId = session('showtime_id');
        
        if (!$pendingBookingId) {
            return redirect()->route('home')->with('error', 'Không tìm thấy booking!');
        }
        
        $booking = Booking::find($pendingBookingId);
        
        if (!$booking || $booking->vnp_txn_ref !== $vnpTxnRef) {
            return redirect()->route('home')->with('error', 'Booking không hợp lệ!');
        }
        
        if ($vnpResponseCode === '00') {
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
        } else {
            // Payment failed
            $booking->update(['status' => 'cancelled']);
            
            return redirect()->route('booking.index', ['showtime_id' => $showtimeId])
                ->with('error', 'Thanh toán thất bại!');
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
    
    private function redirectToVNPay($booking, $showtime)
    {
        $vnpUrl = "https://sandbox.vnpayment.vn/paymentv2/vpcpay.html";
        $vnpTmnCode = config('services.vnpay.tmn_code', 'FK2JNB94');
        $vnpHashSecret = config('services.vnpay.hash_secret', '6CJXOQ0GAO04RL7SOVVX2BB5AHW5ORGL');
        $vnpReturnUrl = route('booking.vnpay-return');
        
        $inputData = [
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnpTmnCode,
            "vnp_Amount" => $booking->total_amount * 100,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => request()->ip(),
            "vnp_Locale" => "vn",
            "vnp_OrderInfo" => "Thanh toan ve xem phim {$showtime->movie->title}",
            "vnp_OrderType" => "other",
            "vnp_ReturnUrl" => $vnpReturnUrl,
            "vnp_TxnRef" => $booking->vnp_txn_ref,
            "vnp_ExpireDate" => date('YmdHis', strtotime('+15 minutes')),
        ];
        
        ksort($inputData);
        $query = "";
        $hashdata = "";
        $i = 0;
        
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }
        
        $vnpUrl = $vnpUrl . "?" . $query;
        $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnpHashSecret);
        $vnpUrl .= 'vnp_SecureHash=' . $vnpSecureHash;
        
        return redirect($vnpUrl);
    }
}
