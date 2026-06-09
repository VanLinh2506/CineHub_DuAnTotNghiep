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
use App\Models\User;
use App\Services\VNPayService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function __construct(protected VNPayService $vnpay) {}
    
    /**
     * Get day name in Vietnamese
     */
    private function getDayName($day)
    {
        $days = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
        return $days[$day] ?? '';
    }
    
    /**
     * Validate single seat selection (khi đặt 1 ghế)
     */
    private function validateSingleSeat($row, $selectedCol, $groupCols, $minColInGroup, $maxColInGroup, $bookedSeats)
    {
        // Quy tắc 1: Không được chọn ghế ngay sát ghế ngoài cùng (ghế thứ 2 từ đầu hoặc từ cuối)
        // NHƯNG: Nếu ghế ngoài cùng đã được đặt rồi, thì cho phép đặt ghế ngay sát nó
        
        // Kiểm tra ghế ngoài cùng bên trái đã được đặt chưa
        $leftmostSeat = $row . $minColInGroup;
        $isLeftmostBooked = in_array($leftmostSeat, $bookedSeats);
        
        // Kiểm tra ghế ngoài cùng bên phải đã được đặt chưa
        $rightmostSeat = $row . $maxColInGroup;
        $isRightmostBooked = in_array($rightmostSeat, $bookedSeats);
        
        // Chặn ghế thứ 2 từ đầu (bên trái) - chỉ chặn nếu ghế ngoài cùng bên trái chưa được đặt
        if ($selectedCol == $minColInGroup + 1 && !$isLeftmostBooked) {
            Log::warning("Row $row: Validation FAILED - Không được chọn ghế ngay sát ghế ngoài cùng bên trái");
            return "Không được chọn ghế ngay sát ghế ngoài cùng bên trái! Vui lòng chọn ghế ngoài cùng hoặc ghế khác.";
        }
        
        // Chặn ghế thứ 2 từ cuối (bên phải) - chỉ chặn nếu ghế ngoài cùng bên phải chưa được đặt
        if ($selectedCol == $maxColInGroup - 1 && !$isRightmostBooked) {
            Log::warning("Row $row: Validation FAILED - Không được chọn ghế ngay sát ghế ngoài cùng bên phải");
            return "Không được chọn ghế ngay sát ghế ngoài cùng bên phải! Vui lòng chọn ghế ngoài cùng hoặc ghế khác.";
        }
        
        // Quy tắc 2: Nếu giữa 2 ghế đã đặt có >= 3 ghế trống, không được đặt ghế ở giữa
        $nearestBookedLeft = null;
        for ($checkCol = $selectedCol - 1; $checkCol >= $minColInGroup; $checkCol--) {
            if (!in_array($checkCol, $groupCols)) continue;
            $checkSeat = $row . $checkCol;
            if (in_array($checkSeat, $bookedSeats)) {
                $nearestBookedLeft = $checkCol;
                break;
            }
        }
        
        $nearestBookedRight = null;
        for ($checkCol = $selectedCol + 1; $checkCol <= $maxColInGroup; $checkCol++) {
            if (!in_array($checkCol, $groupCols)) continue;
            $checkSeat = $row . $checkCol;
            if (in_array($checkSeat, $bookedSeats)) {
                $nearestBookedRight = $checkCol;
                break;
            }
        }
        
        if ($nearestBookedLeft !== null && $nearestBookedRight !== null) {
            $gapBetweenBooked = $nearestBookedRight - $nearestBookedLeft - 1;
            
            if ($gapBetweenBooked >= 3) {
                $distanceFromLeft = $selectedCol - $nearestBookedLeft;
                $distanceFromRight = $nearestBookedRight - $selectedCol;
                
                if ($distanceFromLeft > 1 && $distanceFromRight > 1) {
                    Log::warning("Row $row: Validation FAILED - Đặt 1 vé giữa 2 ghế đã đặt có >= 3 ghế trống");
                    return "Không được đặt ghế ở giữa khi giữa 2 ghế đã đặt có 3 ghế trống trở lên! Vui lòng chọn ghế ngay sát một trong hai ghế đã đặt hoặc chọn ghế khác.";
                }
            }
        }
        
        return null; // OK
    }
    
    /**
     * Get seat groups in a row from seat layout
     */
    private function getSeatGroupsInRow($row, $seatLayout)
    {
        if (!$seatLayout) {
            return [];
        }
        
        $groups = [];
        
        if (isset($seatLayout['seat_groups']) && is_array($seatLayout['seat_groups'])) {
            foreach ($seatLayout['seat_groups'] as $group) {
                $groupRows = $group['rows'] ?? [];
                $groupCols = $group['cols'] ?? [];
                
                if (in_array($row, $groupRows) && !empty($groupCols)) {
                    $groups[] = ['cols' => $groupCols];
                }
            }
        } elseif (isset($seatLayout['cols']) && is_array($seatLayout['cols'])) {
            $groups[] = ['cols' => $seatLayout['cols']];
        }
        
        return $groups;
    }
    
    /**
     * Get all columns in a row from seat layout
     */
    private function getAllColumnsInRow($row, $seatLayout)
    {
        if (!$seatLayout) {
            return [];
        }
        
        $allCols = [];
        
        if (isset($seatLayout['seat_groups']) && is_array($seatLayout['seat_groups'])) {
            foreach ($seatLayout['seat_groups'] as $group) {
                $groupRows = $group['rows'] ?? [];
                $groupCols = $group['cols'] ?? [];
                
                if (in_array($row, $groupRows)) {
                    foreach ($groupCols as $col) {
                        if (!in_array($col, $allCols)) {
                            $allCols[] = $col;
                        }
                    }
                }
            }
        } elseif (isset($seatLayout['cols']) && is_array($seatLayout['cols'])) {
            $allCols = $seatLayout['cols'];
        }
        
        sort($allCols);
        return $allCols;
    }
    
    /**
     * Booking homepage - Select movie, theater, date, time
     */
    public function index(Request $request)
    {
        // Allow browsing without login
        $selectedMovieId = $request->input('movie');
        $selectedTheater = $request->input('theater');
        $selectedDate = $request->input('date', date('Y-m-d'));
        $selectedShowtimeId = $request->input('showtime_id');
        
        // Get user location from session
        $userLat = session('user_latitude');
        $userLng = session('user_longitude');
        
        // Get only movies with upcoming showtimes (phim đang chiếu = phim có suất chiếu trong tương lai)
        $allMovies = Movie::whereHas('showtimes', function($query) {
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
        
        // If showtime selected, get all info
        if ($selectedShowtimeId) {
            $showtime = Showtime::with(['movie', 'theater', 'screen'])->find($selectedShowtimeId);
            
            if ($showtime) {
                // Auto-populate from showtime
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
                
                // Get reserved seats (if table exists)
                try {
                    $reservedSeats = DB::table('seat_reservations')
                        ->where('showtime_id', $selectedShowtimeId)
                        ->where('expires_at', '>', now())
                        ->pluck('seat')
                        ->toArray();
                } catch (\Exception $e) {
                    $reservedSeats = [];
                }
                
                // Get seat layout
                if ($screenInfo && $screenInfo->seat_layout_config) {
                    $seatLayoutData = $screenInfo->seat_layout_config;
                    if (is_string($seatLayoutData)) {
                        $seatLayout = json_decode($seatLayoutData, true);
                    } else {
                        $seatLayout = $seatLayoutData;
                    }
                }
            }
        }
        
        // Get theaters for selected movie
        if ($selectedMovieId) {
            $movie = $movie ?? Movie::find($selectedMovieId);
            
            $theaters = Theater::whereHas('showtimes', function($q) use ($selectedMovieId) {
                    $q->where('movie_id', $selectedMovieId)
                      ->where(DB::raw("CONCAT(show_date, ' ', show_time)"), '>=', now()->format('Y-m-d H:i:s'));
                })
                ->select('theaters.*')
                ->when($userLat && $userLng, function($query) use ($userLat, $userLng) {
                    // Calculate distance if user location available
                    $query->selectRaw("
                        (6371 * acos(cos(radians(?)) * cos(radians(latitude)) * 
                        cos(radians(longitude) - radians(?)) + sin(radians(?)) * 
                        sin(radians(latitude)))) AS distance
                    ", [$userLat, $userLng, $userLat])
                    ->orderBy('distance');
                })
                ->get();
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
        
        // Get food items
        $foodItems = FoodItem::where('is_active', true)->get();
        
        // Calculate prices
        $basePrice = 90000;
        $screenType = '2D';
        
        if ($selectedShowtimeId && isset($showtime)) {
            $basePrice = $showtime->price;
            $screenType = $screenInfo->screen_type ?? '2D';
        }
        
        $screenSurcharge = $this->getScreenTypeSurcharge($screenType);
        $normalPrice = $basePrice + $screenSurcharge;
        $vipPrice = $basePrice + $screenSurcharge + ($basePrice * 0.3);
        $couplePrice = $basePrice + $screenSurcharge + ($basePrice * 0.5);
        
        // Generate dates (7 days)
        $dates = collect(range(0, 6))->map(function($i) {
            $date = now()->addDays($i);
            return [
                'value' => $date->toDateString(),
                'label' => $date->format('d/m'),
                'day_name' => $this->getDayName($date->dayOfWeek),
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
        
        Log::info('GetShowtimesByDate called', [
            'movie_id' => $movieId,
            'theater_id' => $theaterId,
            'date' => $date,
            'now' => now()->format('Y-m-d H:i:s')
        ]);
        
        if (!$movieId || !$theaterId || !$date) {
            Log::warning('Missing parameters for getShowtimesByDate');
            return response()->json(['showtimes' => [], 'error' => 'Missing parameters']);
        }
        
        // Debug: Check total showtimes in DB
        $totalShowtimes = Showtime::where('movie_id', $movieId)
            ->where('theater_id', $theaterId)
            ->where('show_date', $date)
            ->count();
        
        Log::info('Total showtimes for date (before time filter)', ['count' => $totalShowtimes]);
        
        $showtimes = Showtime::with('screen')
            ->where('movie_id', $movieId)
            ->where('theater_id', $theaterId)
            ->where('show_date', $date)
            ->where(DB::raw("CONCAT(show_date, ' ', show_time)"), '>=', now()->format('Y-m-d H:i:s'))
            ->orderBy('show_time')
            ->get();
        
        Log::info('Showtimes after time filter', [
            'count' => $showtimes->count(),
            'showtimes' => $showtimes->pluck('id', 'show_time')
        ]);
        
        $result = $showtimes->map(function($showtime) {
            return [
                'id' => $showtime->id,
                'show_time' => date('H:i', strtotime($showtime->show_time)),
                'screen_name' => $showtime->screen->screen_name ?? 'N/A',
                'screen_type' => $showtime->screen->screen_type ?? '2D',
                'price' => $showtime->price,
            ];
        });
        
        Log::info('Returning showtimes', ['count' => $result->count()]);
        
        return response()->json(['showtimes' => $result]);
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
        
        // Validate seats với logic phức tạp
        $validationError = $this->validateSeatSelection($seats, $showtimeId);
        if ($validationError) {
            return redirect()->back()->with('error', $validationError);
        }
        
        // Get showtime
        $showtime = Showtime::with(['movie', 'screen'])->findOrFail($showtimeId);
        
        // Check if showtime has passed
        $showtimeDateTime = Carbon::parse($showtime->show_date . ' ' . $showtime->show_time);
        if ($showtimeDateTime->isPast()) {
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
     * Validate seat selection với logic phức tạp từ controller cũ
     */
    private function validateSeatSelection($seats, $showtime_id = null)
    {
        if (empty($seats)) {
            return null;
        }
        
        $seatCount = count($seats);
        
        // Lấy danh sách ghế đã được đặt
        $bookedSeats = [];
        $seatLayout = null;
        
        if ($showtime_id) {
            try {
                $bookedSeats = Ticket::where('showtime_id', $showtime_id)
                    ->where('status', 'Đã đặt')
                    ->pluck('seat')
                    ->toArray();
                
                // Lấy seat layout
                $showtime = Showtime::find($showtime_id);
                
                if ($showtime && $showtime->screen_id) {
                    $screen = Screen::find($showtime->screen_id);
                    if ($screen && $screen->seat_layout_config) {
                        $seatLayout = is_string($screen->seat_layout_config) 
                            ? json_decode($screen->seat_layout_config, true) 
                            : $screen->seat_layout_config;
                    }
                }
                
                // Nếu không có layout, tạo layout mặc định
                if (!$seatLayout) {
                    $seatLayout = [
                        'rows' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'],
                        'cols' => range(1, 12),
                        'vip_rows' => ['D', 'E', 'F'],
                        'couple_rows' => ['J']
                    ];
                }
            } catch (\Exception $e) {
                Log::error("ERROR getting seat layout: " . $e->getMessage());
                $seatLayout = [
                    'rows' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'],
                    'cols' => range(1, 12),
                    'vip_rows' => ['D', 'E', 'F'],
                    'couple_rows' => ['J']
                ];
            }
        }
        
        // Lấy danh sách hàng ghế đôi
        $coupleRows = $seatLayout['couple_rows'] ?? ['J'];
        
        // Sắp xếp ghế theo hàng và cột
        $seatsByRow = [];
        foreach ($seats as $seat) {
            $row = substr($seat, 0, 1);
            $col = (int)substr($seat, 1);
            if (!isset($seatsByRow[$row])) {
                $seatsByRow[$row] = [];
            }
            $seatsByRow[$row][] = $col;
        }
        
        // Kiểm tra từng hàng
        foreach ($seatsByRow as $row => $cols) {
            sort($cols);
            
            // BỎ QUA VALIDATION CHO HÀNG GHẾ ĐÔI
            if (in_array($row, $coupleRows)) {
                Log::info("Row $row: Bỏ qua validation - Đây là hàng ghế đôi");
                continue;
            }
            
            // Kiểm tra không bỏ trống ghế ở giữa
            if (count($cols) > 1) {
                for ($i = 0; $i < count($cols) - 1; $i++) {
                    $gap = $cols[$i + 1] - $cols[$i];
                    if ($gap > 1) {
                        return "Không được bỏ trống ghế ở giữa! Các ghế phải liền kề nhau.";
                    }
                }
            }
            
            // Lấy danh sách các nhóm ghế trong hàng này
            $seatGroupsInRow = $this->getSeatGroupsInRow($row, $seatLayout);
            
            if (empty($seatGroupsInRow)) {
                $allColsInRow = $this->getAllColumnsInRow($row, $seatLayout);
                if (!empty($allColsInRow)) {
                    $seatGroupsInRow = [['cols' => $allColsInRow]];
                } else if (!empty($cols)) {
                    $minCol = min($cols);
                    $maxCol = max($cols);
                    $fallbackCols = range($minCol, $maxCol);
                    $seatGroupsInRow = [['cols' => $fallbackCols]];
                }
            }
            
            // Kiểm tra từng nhóm ghế
            foreach ($seatGroupsInRow as $group) {
                $groupCols = $group['cols'] ?? [];
                if (empty($groupCols)) continue;
                
                sort($groupCols);
                
                $selectedColsInGroup = array_intersect($cols, $groupCols);
                if (empty($selectedColsInGroup)) continue;
                
                $selectedColsInGroup = array_values($selectedColsInGroup);
                sort($selectedColsInGroup);
                $selectedSeatCountInGroup = count($selectedColsInGroup);
                
                $minColInGroup = min($groupCols);
                $maxColInGroup = max($groupCols);
                $selectedMinCol = min($selectedColsInGroup);
                $selectedMaxCol = max($selectedColsInGroup);
                
                // Đếm tổng số ghế AVAILABLE trong nhóm
                $totalAvailableInGroup = 0;
                foreach ($groupCols as $col) {
                    $checkSeat = $row . $col;
                    if (!in_array($checkSeat, $bookedSeats)) {
                        $totalAvailableInGroup++;
                    }
                }
                
                // Kiểm tra xem có chọn ít nhất 1 trong 2 ghế ngoài cùng không
                $hasFirstSeat = in_array($minColInGroup, $selectedColsInGroup);
                $hasLastSeat = in_array($maxColInGroup, $selectedColsInGroup);
                
                // Kiểm tra riêng cho trường hợp đặt 1 vé
                if ($selectedSeatCountInGroup == 1) {
                    $singleSeatError = $this->validateSingleSeat($row, $selectedMinCol, $groupCols, $minColInGroup, $maxColInGroup, $bookedSeats);
                    if ($singleSeatError) {
                        return $singleSeatError;
                    }
                }
                
                // Nếu đặt từ đầu hàng - OK
                if ($hasFirstSeat || $hasLastSeat) {
                    continue;
                }
                
                // Tìm ghế đã đặt gần nhất
                $nearestBookedSeatLeft = null;
                for ($checkCol = $selectedMinCol - 1; $checkCol >= $minColInGroup; $checkCol--) {
                    if (!in_array($checkCol, $groupCols)) continue;
                    $checkSeat = $row . $checkCol;
                    if (in_array($checkSeat, $bookedSeats)) {
                        $nearestBookedSeatLeft = $checkCol;
                        break;
                    }
                }
                
                $nearestBookedSeatRight = null;
                for ($checkCol = $selectedMaxCol + 1; $checkCol <= $maxColInGroup; $checkCol++) {
                    if (!in_array($checkCol, $groupCols)) continue;
                    $checkSeat = $row . $checkCol;
                    if (in_array($checkSeat, $bookedSeats)) {
                        $nearestBookedSeatRight = $checkCol;
                        break;
                    }
                }
                
                $isAdjacentToBookedLeft = ($nearestBookedSeatLeft !== null && $selectedMinCol == $nearestBookedSeatLeft + 1);
                $isAdjacentToBookedRight = ($nearestBookedSeatRight !== null && $selectedMaxCol == $nearestBookedSeatRight - 1);
                
                // Nếu đặt ngay sau ghế đã đặt - OK
                if ($isAdjacentToBookedLeft || $isAdjacentToBookedRight) {
                    continue;
                }
                
                // Đếm số ghế available ở hai đầu
                $startPoint = ($nearestBookedSeatLeft !== null) ? $nearestBookedSeatLeft : $minColInGroup;
                $countStart = ($nearestBookedSeatLeft !== null) ? $nearestBookedSeatLeft + 1 : $minColInGroup;
                
                $availableSeatsAtStart = 0;
                for ($checkCol = $countStart; $checkCol < $selectedMinCol; $checkCol++) {
                    if (!in_array($checkCol, $groupCols)) continue;
                    $checkSeat = $row . $checkCol;
                    if (in_array($checkSeat, $bookedSeats)) break;
                    $availableSeatsAtStart++;
                }
                
                $endPoint = ($nearestBookedSeatRight !== null) ? $nearestBookedSeatRight : $maxColInGroup;
                $countEnd = ($nearestBookedSeatRight !== null) ? $nearestBookedSeatRight - 1 : $maxColInGroup;
                
                $availableSeatsAtEnd = 0;
                for ($checkCol = $selectedMaxCol + 1; $checkCol <= $countEnd; $checkCol++) {
                    if (!in_array($checkCol, $groupCols)) continue;
                    $checkSeat = $row . $checkCol;
                    if (in_array($checkSeat, $bookedSeats)) break;
                    $availableSeatsAtEnd++;
                }
                
                // Áp dụng quy tắc validation
                $halfOfAvailable = floor($totalAvailableInGroup / 2);
                
                if ($selectedSeatCountInGroup >= $halfOfAvailable) {
                    return "Khi đặt từ $halfOfAvailable vé trở lên trong nhóm có $totalAvailableInGroup ghế trống, bắt buộc phải đặt từ đầu hàng (chọn ít nhất 1 trong 2 ghế ngoài cùng)!";
                } else {
                    $minRequiredAtEnds = ($selectedSeatCountInGroup == 1) ? 1 : 2;
                    
                    if ($availableSeatsAtStart < $minRequiredAtEnds || $availableSeatsAtEnd < $minRequiredAtEnds) {
                        if ($selectedSeatCountInGroup == 1 && ($availableSeatsAtStart >= 2 || $availableSeatsAtEnd >= 2)) {
                            // OK
                        } else {
                            return "Khi đặt $selectedSeatCountInGroup vé trong nhóm có $totalAvailableInGroup ghế trống mà không đặt từ đầu hàng, phải để lại ít nhất $minRequiredAtEnds ghế kể từ ghế ngoài cùng ở cả hai đầu hàng!";
                        }
                    }
                }
            }
        }
        
        return null; // Valid
    }
    
    /**
     * Process booking and create pending booking (OLD METHOD - KEEPING FOR REFERENCE)
     */
    private function processBookingOld(Request $request)
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
