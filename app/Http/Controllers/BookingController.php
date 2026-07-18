<?php

namespace App\Http\Controllers;

use App\Events\SeatMapChanged;
use App\Models\Movie;
use App\Models\Theater;
use App\Models\Showtime;
use App\Models\Screen;
use App\Models\Ticket;
use App\Models\Booking;
use App\Models\FoodItem;
use App\Models\SeatReservation;
use App\Models\Transaction;
use App\Models\User;
use App\Services\VNPayService;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BookingController extends Controller
{
    private const SEAT_RESERVATION_MINUTES = 10;
    private const MAX_SEAT_RESELECTIONS = 2;
    private const BOOKED_TICKET_STATUS = 'Đã đặt';
    private const RESERVATION_CONFLICT_MESSAGE = 'Ghế này vừa được người khác chọn. Vui lòng chọn ghế khác.';
    private const RESERVATION_SYSTEM_ERROR_MESSAGE = 'Không thể giữ ghế do lỗi hệ thống. Vui lòng thử lại.';

    public function __construct(protected VNPayService $vnpay) {}

    public function createVnpayPayment(Request $request)
    {
        return $this->processBooking($request);
    }

    public function saveLocation(Request $request)
    {
        $data = $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        session([
            'user_latitude' => (float) $data['latitude'],
            'user_longitude' => (float) $data['longitude'],
        ]);

        return response()->json(['success' => true]);
    }

    public function vnpayCallback(Request $request)
    {
        $vnpTxnRef = $request->input('vnp_TxnRef');

        if (!$vnpTxnRef) {
            return redirect()->route('home')->with('error', 'Khong tim thay ma giao dich VNPay.');
        }

        $booking = Booking::where('vnp_txn_ref', $vnpTxnRef)->first();

        if (!$booking) {
            return redirect()->route('home')->with('error', 'Khong tim thay booking cho giao dich nay.');
        }

        if ($booking->status === 'completed') {
            return redirect()->route('booking.confirmation', $booking->id)
                ->with('success', 'Giao dich VNPay da duoc xac nhan truoc do.');
        }

        if (!$this->vnpay->verifyCallback($request)) {
            if ($booking->status === 'pending') {
                $booking->update(['status' => 'cancelled']);
                $releasedSeats = $this->releaseSeatReservations(
                    $booking->showtime_id,
                    $booking->seats ?? [],
                    $booking->user_id
                );
                if ($releasedSeats > 0) {
                    $seatStatus = $this->getSeatStatusData($booking->showtime_id, $booking->user_id);
                    $this->dispatchSeatMapChanged(
                        $booking->showtime_id,
                        'released',
                        $seatStatus['bookedSeats'],
                        $seatStatus['reservedSeats'],
                        $booking->seats ?? [],
                        $booking->user_id
                    );
                }
            }

            return redirect()->route('booking.index', ['showtime_id' => $booking->showtime_id])
                ->with('error', 'Chữ ký VNPay không hợp lệ. Vui lòng chọn lại ghế.');
        }

        if ($request->input('vnp_ResponseCode') !== '00') {
            if ($booking->status === 'pending') {
                $booking->update(['status' => 'cancelled']);
            }

            session()->forget(['pending_booking_id', 'showtime_id']);

            $message = $request->input('vnp_ResponseCode') === '24'
                ? 'Bạn đã hủy thanh toán. Ghế vẫn được giữ trong thời gian còn lại.'
                : 'Thanh toán chưa thành công. Ghế vẫn được giữ trong thời gian còn lại.';

            return redirect()->route('booking.index', ['showtime_id' => $booking->showtime_id])
                ->with('error', $message);
        }

        $callbackAmount = ((int) $request->input('vnp_Amount', 0)) / 100;
        if ((float) $booking->total_amount !== (float) $callbackAmount) {
            Log::warning('VNPay amount mismatch', [
                'booking_id' => $booking->id,
                'txn_ref' => $vnpTxnRef,
                'booking_amount' => $booking->total_amount,
                'callback_amount' => $callbackAmount,
            ]);

            if ($booking->status === 'pending') {
                $booking->update(['status' => 'cancelled']);
                $releasedSeats = $this->releaseSeatReservations(
                    $booking->showtime_id,
                    $booking->seats ?? [],
                    $booking->user_id
                );
                if ($releasedSeats > 0) {
                    $seatStatus = $this->getSeatStatusData($booking->showtime_id, $booking->user_id);
                    $this->dispatchSeatMapChanged(
                        $booking->showtime_id,
                        'released',
                        $seatStatus['bookedSeats'],
                        $seatStatus['reservedSeats'],
                        $booking->seats ?? [],
                        $booking->user_id
                    );
                }
            }

            return redirect()->route('booking.index', ['showtime_id' => $booking->showtime_id])
                ->with('error', 'So tien thanh toan khong khop voi booking.');
        }

        try {
            $this->finalizeBooking($booking, 'VNPay');

            session()->forget(['pending_booking_id', 'showtime_id']);

            return redirect()->route('booking.confirmation', $booking->id)
                ->with('success', 'Dat ve thanh cong! Quet ma QR de check ve.');
        } catch (\Exception $e) {
            Log::error('Complete booking error', [
                'txn_ref' => $vnpTxnRef,
                'booking_id' => $booking->id,
                'message' => $e->getMessage(),
            ]);

            if ($booking->fresh()?->status === 'pending') {
                $booking->update(['status' => 'cancelled']);
                $releasedSeats = $this->releaseSeatReservations(
                    $booking->showtime_id,
                    $booking->seats ?? [],
                    $booking->user_id
                );
                if ($releasedSeats > 0) {
                    $seatStatus = $this->getSeatStatusData($booking->showtime_id, $booking->user_id);
                    $this->dispatchSeatMapChanged(
                        $booking->showtime_id,
                        'released',
                        $seatStatus['bookedSeats'],
                        $seatStatus['reservedSeats'],
                        $booking->seats ?? [],
                        $booking->user_id
                    );
                }
            }

            return redirect()->route('booking.index', ['showtime_id' => $booking->showtime_id])
                ->with('error', 'Co loi xay ra khi hoan tat dat ve.');
        }
    }
    
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
        if ($seatLayout === null) {
            return [];
        }
        
        $groups = [];
        $coupleRows = $seatLayout['couple_rows'] ?? ['J'];
        
        if (isset($seatLayout['seat_groups']) && is_array($seatLayout['seat_groups'])) {
            foreach ($seatLayout['seat_groups'] as $group) {
                $groupRows = $group['rows'] ?? [];
                $groupCols = $group['cols'] ?? [];
                
                if (in_array($row, $groupRows) && !empty($groupCols)) {
                    $groups[] = ['cols' => $groupCols];
                }
            }
        } elseif (isset($seatLayout['cols']) && is_array($seatLayout['cols'])) {
            if (in_array($row, $coupleRows, true)) {
                $groups = [
                    ['cols' => [1, 2, 3]],
                    ['cols' => [4, 5, 6]],
                ];
            } else {
                $groups = [
                    ['cols' => [1, 2, 3, 4, 5, 6]],
                    ['cols' => [7, 8, 9, 10, 11, 12]],
                ];
            }
        }

        if (empty($groups)) {
            if (in_array($row, $coupleRows, true)) {
                $groups = [
                    ['cols' => [1, 2, 3]],
                    ['cols' => [4, 5, 6]],
                ];
            } else {
                $groups = [
                    ['cols' => [1, 2, 3, 4, 5, 6]],
                    ['cols' => [7, 8, 9, 10, 11, 12]],
                ];
            }
        }
        
        return $groups;
    }

    private function splitColsByAisles(array $cols): array
    {
        sort($cols);
        $groups = [[]];

        foreach ($cols as $col) {
            if (!empty($groups[count($groups) - 1]) && $col - end($groups[count($groups) - 1]) > 1) {
                $groups[] = [];
            }
            $groups[count($groups) - 1][] = $col;
        }

        return array_values(array_filter($groups));
    }

    private function resolveSeatGroupsForRow(string $row, ?array $seatLayout, array $coupleRows): array
    {
        $groups = $this->getSeatGroupsInRow($row, $seatLayout);
        if (!empty($groups)) {
            return array_values(array_filter(array_map(
                static fn ($group) => array_values(array_unique($group['cols'] ?? [])),
                $groups
            )));
        }

        $rowCols = $this->getAllColumnsInRow($row, $seatLayout);
        if (!empty($rowCols)) {
            return $this->splitColsByAisles($rowCols);
        }

        if (in_array($row, $coupleRows, true)) {
            return [[1, 2, 3], [4, 5, 6]];
        }

        return [[1, 2, 3, 4, 5, 6], [7, 8, 9, 10, 11, 12]];
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

        if (array_is_list($seatLayout)) {
            foreach ($seatLayout as $rowConfig) {
                if (($rowConfig['row'] ?? null) !== $row || empty($rowConfig['seats']) || !is_array($rowConfig['seats'])) {
                    continue;
                }

                foreach ($rowConfig['seats'] as $seat) {
                    if (($seat['type'] ?? null) === 'disabled' || (isset($seat['available']) && !$seat['available'])) {
                        continue;
                    }

                    $seatNumber = $seat['number'] ?? null;
                    if (!$seatNumber) {
                        continue;
                    }

                    $allCols[] = (int) substr($seatNumber, 1);
                }
            }

            sort($allCols);
            return array_values(array_unique($allCols));
        }
        
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
        $myReservedSeats = [];
        $seatLayout = null;
        $screenInfo = null;
        $theaterInfo = null;
        
        // If showtime selected, get all info
        if ($selectedShowtimeId) {
            $showtime = Showtime::with(['movie.category', 'movie.categories', 'theater', 'screen'])->find($selectedShowtimeId);
            
            if ($showtime) {
                // Auto-populate from showtime
                $selectedMovieId = $showtime->movie_id;
                $selectedTheater = $showtime->theater_id;
                $selectedDate = Carbon::parse($showtime->show_date)->toDateString();
                
                $movie = $showtime->movie;
                $theaterInfo = $showtime->theater;
                $screenInfo = $showtime->screen;

                $seatStatus = $this->getSeatStatusData($selectedShowtimeId, Auth::id());
                $bookedSeats = $seatStatus['bookedSeats'];
                $reservedSeats = $seatStatus['reservedSeats'];
                $myReservedSeats = $seatStatus['myReservedSeats'];
                
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
            $movie = $movie ?? Movie::with(['category', 'categories'])->find($selectedMovieId);
            
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
        $vnpayConfigured = $this->isVnpayConfigured();
        
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
            'basePrice', 'screenSurcharge', 'vnpayConfigured', 'myReservedSeats'
        ));
    }

    private function isVnpayConfigured(): bool
    {
        return (string) config('services.vnpay.tmn_code') !== ''
            && (string) config('services.vnpay.hash_secret') !== '';
    }

    private function normalizeSeatList(array $seats): array
    {
        $normalized = [];

        foreach ($seats as $seat) {
            if (!is_string($seat) && !is_numeric($seat)) {
                continue;
            }

            $seat = Str::upper(trim((string) $seat));

            if (!preg_match('/^[A-Z]\d+$/', $seat)) {
                continue;
            }

            $normalized[$seat] = $seat;
        }

        return array_values($normalized);
    }

    private function cleanupExpiredSeatReservations(?int $showtimeId = null, bool $broadcast = false): array
    {
        $expiredReservations = SeatReservation::query()
            ->when($showtimeId, fn ($query) => $query->where('showtime_id', $showtimeId))
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get(['id', 'showtime_id', 'seat', 'user_id']);

        if ($expiredReservations->isEmpty()) {
            return [];
        }

        SeatReservation::query()
            ->whereIn('id', $expiredReservations->pluck('id')->filter()->all())
            ->delete();

        if ($broadcast) {
            foreach ($expiredReservations->groupBy('showtime_id') as $expiredShowtimeId => $reservations) {
                $seatStatus = $this->getSeatStatusData((int) $expiredShowtimeId, null, false);

                $this->dispatchSeatMapChanged(
                    (int) $expiredShowtimeId,
                    'expired',
                    $seatStatus['bookedSeats'],
                    $seatStatus['reservedSeats'],
                    $reservations->pluck('seat')->filter()->unique()->values()->all(),
                    null,
                    false
                );
            }
        }

        return $expiredReservations->pluck('seat')->filter()->unique()->values()->all();
    }

    private function getSeatStatusData(int $showtimeId, ?int $currentUserId = null, bool $cleanupExpired = true): array
    {
        if ($cleanupExpired) {
            $this->cleanupExpiredSeatReservations($showtimeId, true);
        }

        $bookedSeats = Ticket::where('showtime_id', $showtimeId)
            ->where('status', self::BOOKED_TICKET_STATUS)
            ->pluck('seat')
            ->filter()
            ->unique()
            ->values()
            ->all();

        $reservationRows = SeatReservation::query()
            ->where('showtime_id', $showtimeId)
            ->active()
            ->get(['seat', 'user_id', 'session_id', 'expires_at']);

        $reservedSeats = $reservationRows->pluck('seat')->filter()->unique()->values()->all();
        $myReservedSeats = [];

        if ($currentUserId) {
            $myReservedSeats = $reservationRows
                ->where('user_id', $currentUserId)
                ->pluck('seat')
                ->filter()
                ->unique()
                ->values()
                ->all();
        }

        $otherReservedSeats = $currentUserId
            ? array_values(array_diff($reservedSeats, $myReservedSeats))
            : $reservedSeats;

        return [
            'bookedSeats' => $bookedSeats,
            'reservedSeats' => $reservedSeats,
            'myReservedSeats' => $myReservedSeats,
            'otherReservedSeats' => $otherReservedSeats,
        ];
    }

    private function getShowtimeStartAt(Showtime $showtime): Carbon
    {
        return Carbon::parse($showtime->show_date)->setTimeFromTimeString($showtime->show_time);
    }

    private function seatReselectionState(int $used): array
    {
        $normalizedUsed = min(max($used, 0), self::MAX_SEAT_RESELECTIONS);

        return [
            'used' => $normalizedUsed,
            'remaining' => self::MAX_SEAT_RESELECTIONS - $normalizedUsed,
            'max' => self::MAX_SEAT_RESELECTIONS,
        ];
    }

    private function touchBookingRoomSession(Showtime $showtime, ?User $user): array
    {
        $now = now();
        $limitSeconds = self::SEAT_RESERVATION_MINUTES * 60;

        if (!$user || !$showtime->screen_id) {
            return [
                'allowed' => true,
                'remainingSeconds' => $limitSeconds,
                'message' => null,
                'trackingId' => null,
                'seatReselection' => $this->seatReselectionState(0),
            ];
        }

        $screenId = (int) $showtime->screen_id;
        $showtimeId = (int) $showtime->id;

        $activeBan = DB::table('booking_session_tracking')
            ->where('user_id', $user->id)
            ->where('showtime_id', $showtimeId)
            ->where('screen_id', $screenId)
            ->where('is_banned', 1)
            ->where(function ($query) use ($now) {
                $query->whereNull('ban_until')
                    ->orWhere('ban_until', '>', $now);
            })
            ->orderByDesc('id')
            ->first();

        if ($activeBan) {
            return [
                'allowed' => false,
                'remainingSeconds' => 0,
                'message' => 'Ban da het 10 phut dat ve cho phong nay. Vui long chon suat chieu/phong khac.',
                'trackingId' => (int) $activeBan->id,
                'seatReselection' => $this->seatReselectionState((int) ($activeBan->seat_reselection_count ?? 0)),
            ];
        }

        $tracking = DB::table('booking_session_tracking')
            ->where('user_id', $user->id)
            ->where('showtime_id', $showtimeId)
            ->where('screen_id', $screenId)
            ->whereNull('session_end')
            ->orderByDesc('id')
            ->first();

        if (!$tracking) {
            $trackingId = DB::table('booking_session_tracking')->insertGetId([
                'user_id' => $user->id,
                'showtime_id' => $showtimeId,
                'screen_id' => $screenId,
                'session_start' => $now,
                'session_end' => null,
                'total_duration_seconds' => 0,
                'violation_count' => 0,
                'seat_reselection_count' => 0,
                'is_banned' => 0,
                'ban_until' => null,
                'created_at' => $now,
            ]);

            $tracking = DB::table('booking_session_tracking')->where('id', $trackingId)->first();
        }

        $sessionStart = Carbon::parse($tracking->session_start);
        $elapsedSeconds = max(0, $sessionStart->diffInSeconds($now, false));
        $remainingSeconds = max(0, $limitSeconds - $elapsedSeconds);

        if ($elapsedSeconds >= $limitSeconds) {
            $banUntil = $this->getShowtimeStartAt($showtime);
            if ($banUntil->lessThanOrEqualTo($now)) {
                $banUntil = $now->copy()->addMinutes(self::SEAT_RESERVATION_MINUTES);
            }

            DB::table('booking_session_tracking')
                ->where('id', $tracking->id)
                ->update([
                    'session_end' => $now,
                    'total_duration_seconds' => $elapsedSeconds,
                    'violation_count' => ((int) $tracking->violation_count) + 1,
                    'is_banned' => 1,
                    'ban_until' => $banUntil,
                ]);

            $heldSeats = SeatReservation::query()
                ->where('showtime_id', $showtimeId)
                ->where('user_id', $user->id)
                ->pluck('seat')
                ->filter()
                ->values()
                ->all();

            $releasedSeats = $this->releaseSeatReservations($showtimeId, $heldSeats, $user->id);
            if ($releasedSeats > 0) {
                $seatStatus = $this->getSeatStatusData($showtimeId, $user->id);
                $this->dispatchSeatMapChanged(
                    $showtimeId,
                    'expired',
                    $seatStatus['bookedSeats'],
                    $seatStatus['reservedSeats'],
                    $heldSeats,
                    $user->id
                );
            }

            return [
                'allowed' => false,
                'remainingSeconds' => 0,
                'message' => 'Ban da het 10 phut dat ve cho phong nay. Vui long chon suat chieu/phong khac.',
                'trackingId' => (int) $tracking->id,
                'seatReselection' => $this->seatReselectionState((int) ($tracking->seat_reselection_count ?? 0)),
            ];
        }

        DB::table('booking_session_tracking')
            ->where('id', $tracking->id)
            ->update([
                'total_duration_seconds' => $elapsedSeconds,
            ]);

        return [
            'allowed' => true,
            'remainingSeconds' => $remainingSeconds,
            'message' => null,
            'trackingId' => (int) $tracking->id,
            'seatReselection' => $this->seatReselectionState((int) ($tracking->seat_reselection_count ?? 0)),
        ];
    }

    private function getReservationTimerData(int $showtimeId, ?int $currentUserId = null, ?array $seats = null): array
    {
        $serverNow = now();

        if (!$currentUserId) {
            return [
                'serverNow' => $serverNow->toIso8601String(),
                'reservationExpiresAt' => null,
                'remainingSeconds' => 0,
            ];
        }

        $query = SeatReservation::query()
            ->where('showtime_id', $showtimeId)
            ->where('user_id', $currentUserId)
            ->active();

        if ($seats !== null) {
            $normalizedSeats = $this->normalizeSeatList($seats);
            if (empty($normalizedSeats)) {
                return [
                    'serverNow' => $serverNow->toIso8601String(),
                    'reservationExpiresAt' => null,
                    'remainingSeconds' => 0,
                ];
            }

            $query->whereIn('seat', $normalizedSeats);
        }

        $expiresAt = $query->orderBy('expires_at')->value('expires_at');
        $expiresAt = $expiresAt ? Carbon::parse($expiresAt) : null;

        return [
            'serverNow' => $serverNow->toIso8601String(),
            'reservationExpiresAt' => $expiresAt?->toIso8601String(),
            'remainingSeconds' => $expiresAt ? max(0, $serverNow->diffInSeconds($expiresAt, false)) : 0,
        ];
    }

    private function dispatchSeatMapChanged(
        int $showtimeId,
        string $action,
        array $bookedSeats,
        array $reservedSeats,
        array $seats = [],
        ?int $userId = null,
        bool $toOthers = true,
        array $timer = []
    ): void {
        $event = new SeatMapChanged(
            showtimeId: $showtimeId,
            action: $action,
            seats: $this->normalizeSeatList($seats),
            bookedSeats: $this->normalizeSeatList($bookedSeats),
            reservedSeats: $this->normalizeSeatList($reservedSeats),
            userId: $userId,
            timer: $timer,
        );

        $broadcastEvent = function () use ($event, $toOthers): void {
            try {
                $broadcast = broadcast($event);

                if ($toOthers) {
                    $broadcast->toOthers();
                }

                // PendingBroadcast dispatches in its destructor. Trigger it
                // inside the try block so a stopped Reverb server never
                // interrupts seat loading/reservation.
                unset($broadcast);
            } catch (\Throwable $exception) {
                Log::warning('Seat realtime broadcast unavailable; continuing without websocket.', [
                    'showtime_id' => $event->showtimeId,
                    'action' => $event->action,
                    'error' => $exception->getMessage(),
                ]);
            }
        };

        if (DB::transactionLevel() > 0) {
            DB::afterCommit($broadcastEvent);
        } else {
            $broadcastEvent();
        }
    }

    private function reserveSeatsForUser(
        int $showtimeId,
        array $seats,
        User $user,
        ?string $sessionId = null,
        int $ttlMinutes = self::SEAT_RESERVATION_MINUTES
    ): array {
        $normalizedSeats = $this->normalizeSeatList($seats);

        if (empty($normalizedSeats)) {
            return [];
        }

        $sessionId = $sessionId ?: session()->getId();
        $expiresAt = now()->addMinutes($ttlMinutes);

        try {
            return DB::transaction(function () use ($showtimeId, $normalizedSeats, $user, $sessionId, $expiresAt) {
                $this->cleanupExpiredSeatReservations($showtimeId);

                $bookedSeats = Ticket::where('showtime_id', $showtimeId)
                    ->where('status', self::BOOKED_TICKET_STATUS)
                    ->whereIn('seat', $normalizedSeats)
                    ->pluck('seat')
                    ->toArray();

                if (!empty($bookedSeats)) {
                    throw new \RuntimeException(self::RESERVATION_CONFLICT_MESSAGE);
                }

                $existingReservations = SeatReservation::query()
                    ->where('showtime_id', $showtimeId)
                    ->whereIn('seat', $normalizedSeats)
                    ->lockForUpdate()
                    ->get()
                    ->keyBy('seat');

                foreach ($normalizedSeats as $seat) {
                    $existing = $existingReservations->get($seat);

                    if ($existing && (int) $existing->user_id !== (int) $user->id) {
                        throw new \RuntimeException(self::RESERVATION_CONFLICT_MESSAGE);
                    }

                    $payload = [
                        'user_id' => $user->id,
                        'session_id' => $sessionId,
                    ];

                    if ($existing) {
                        $existing->update($payload);
                        continue;
                    }

                    SeatReservation::create($payload + [
                        'showtime_id' => $showtimeId,
                        'seat' => $seat,
                        'reserved_at' => now(),
                        'expires_at' => $expiresAt,
                    ]);
                }

                return SeatReservation::query()
                    ->where('showtime_id', $showtimeId)
                    ->where('user_id', $user->id)
                    ->whereIn('seat', $normalizedSeats)
                    ->active()
                    ->pluck('seat')
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();
            });
        } catch (QueryException $e) {
            Log::warning('Seat reservation query error', [
                'showtime_id' => $showtimeId,
                'user_id' => $user->id,
                'message' => $e->getMessage(),
            ]);

            throw new \RuntimeException(self::RESERVATION_SYSTEM_ERROR_MESSAGE);
        }
    }

    private function releaseSeatReservations(
        int $showtimeId,
        array $seats,
        ?int $userId = null
    ): int {
        $normalizedSeats = $this->normalizeSeatList($seats);

        if (empty($normalizedSeats)) {
            return 0;
        }

        $query = SeatReservation::query()
            ->where('showtime_id', $showtimeId)
            ->whereIn('seat', $normalizedSeats);

        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        return (int) $query->delete();
    }

    private function extendSeatReservations(
        int $showtimeId,
        array $seats,
        int $userId,
        ?string $sessionId = null,
        int $ttlMinutes = self::SEAT_RESERVATION_MINUTES
    ): int {
        $normalizedSeats = $this->normalizeSeatList($seats);

        if (empty($normalizedSeats)) {
            return 0;
        }

        $payload = [
            'session_id' => $sessionId ?: session()->getId(),
            'reserved_at' => now(),
            'expires_at' => now()->addMinutes($ttlMinutes),
        ];

        return SeatReservation::query()
            ->where('showtime_id', $showtimeId)
            ->where('user_id', $userId)
            ->whereIn('seat', $normalizedSeats)
            ->active()
            ->update($payload);
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

        $roomSession = $this->touchBookingRoomSession($showtime, Auth::user());
        if (!$roomSession['allowed']) {
            return response()->json([
                'error' => 'booking_room_time_expired',
                'message' => $roomSession['message'],
                'roomRemainingSeconds' => 0,
            ], 403);
        }
        
        $seatStatus = $this->getSeatStatusData($showtimeId, Auth::id());
        $bookedSeats = $seatStatus['bookedSeats'];
        $reservedSeats = $seatStatus['reservedSeats'];
        $myReservedSeats = $seatStatus['myReservedSeats'];
        $timer = $this->getReservationTimerData((int) $showtimeId, Auth::id());
        
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
            'reservedSeats' => $reservedSeats,
            'myReservedSeats' => $myReservedSeats,
            'serverNow' => $timer['serverNow'],
            'reservationExpiresAt' => $timer['reservationExpiresAt'],
            'remainingSeconds' => $timer['remainingSeconds'],
            'roomRemainingSeconds' => $roomSession['remainingSeconds'],
            'seatReselection' => $roomSession['seatReselection'],
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

    public function reserveSeats(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $data = $request->validate([
            'showtime_id' => 'required|exists:showtimes,id',
            'seats' => 'required|array|min:1|max:8',
        ]);

        try {
            $showtime = Showtime::with('screen')->findOrFail((int) $data['showtime_id']);
            $validationError = $this->validateSeatSelection($data['seats'], (int) $data['showtime_id']);
            if ($validationError) {
                return response()->json([
                    'success' => false,
                    'message' => $validationError,
                    'error' => 'invalid_seat_selection',
                ], 422);
            }

            $roomSession = $this->touchBookingRoomSession($showtime, Auth::user());
            if (!$roomSession['allowed']) {
                return response()->json([
                    'success' => false,
                    'message' => $roomSession['message'],
                    'error' => 'booking_room_time_expired',
                ], 403);
            }

            $reservedSeats = $this->reserveSeatsForUser(
                (int) $data['showtime_id'],
                $data['seats'],
                Auth::user(),
                session()->getId()
            );

            $seatStatus = $this->getSeatStatusData((int) $data['showtime_id'], Auth::id());
            $timer = $this->getReservationTimerData((int) $data['showtime_id'], Auth::id(), $reservedSeats);
            $this->dispatchSeatMapChanged(
                (int) $data['showtime_id'],
                'selected',
                $seatStatus['bookedSeats'],
                $seatStatus['reservedSeats'],
                $reservedSeats,
                Auth::id(),
                true,
                $timer
            );
            $this->dispatchSeatMapChanged(
                (int) $data['showtime_id'],
                'timer',
                $seatStatus['bookedSeats'],
                $seatStatus['reservedSeats'],
                $reservedSeats,
                Auth::id(),
                false,
                $timer
            );

            return response()->json([
                'success' => true,
                'message' => 'Ghế đã được giữ chỗ.',
                'reservedSeats' => $seatStatus['reservedSeats'],
                'myReservedSeats' => $seatStatus['myReservedSeats'],
                'bookedSeats' => $seatStatus['bookedSeats'],
                'lockedSeats' => $reservedSeats,
                'serverNow' => $timer['serverNow'],
                'reservationExpiresAt' => $timer['reservationExpiresAt'],
                'remainingSeconds' => $timer['remainingSeconds'],
                'roomRemainingSeconds' => $roomSession['remainingSeconds'],
                'seatReselection' => $roomSession['seatReselection'],
            ]);
        } catch (\RuntimeException $e) {
            $status = $e->getMessage() === self::RESERVATION_SYSTEM_ERROR_MESSAGE ? 500 : 409;

            return response()->json([
                'success' => false,
                'message' => $e->getMessage() ?: self::RESERVATION_CONFLICT_MESSAGE,
            ], $status);
        }
    }

    public function releaseReservedSeats(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $data = $request->validate([
            'showtime_id' => 'required|exists:showtimes,id',
            'seats' => 'required|array|min:1',
            'reselection' => 'sometimes|boolean',
        ]);

        $showtimeId = (int) $data['showtime_id'];
        $isReselection = (bool) ($data['reselection'] ?? false);
        $seatReselection = null;

        if ($isReselection) {
            $showtime = Showtime::with('screen')->findOrFail($showtimeId);
            $roomSession = $this->touchBookingRoomSession($showtime, Auth::user());

            if (!$roomSession['allowed']) {
                return response()->json([
                    'success' => false,
                    'message' => $roomSession['message'],
                    'error' => 'booking_room_time_expired',
                    'seatReselection' => $roomSession['seatReselection'],
                ], 403);
            }

            if (!$roomSession['trackingId']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không thể xác định phiên chọn ghế.',
                    'error' => 'seat_reselection_session_missing',
                    'seatReselection' => $roomSession['seatReselection'],
                ], 409);
            }

            $result = DB::transaction(function () use ($roomSession, $showtimeId, $data) {
                $tracking = DB::table('booking_session_tracking')
                    ->where('id', $roomSession['trackingId'])
                    ->lockForUpdate()
                    ->first();

                if (!$tracking) {
                    return [
                        'allowed' => false,
                        'released' => 0,
                        'message' => 'Không thể xác định phiên chọn ghế.',
                        'error' => 'seat_reselection_session_missing',
                        'seatReselection' => $this->seatReselectionState(0),
                    ];
                }

                $used = (int) ($tracking->seat_reselection_count ?? 0);
                $currentState = $this->seatReselectionState($used);

                if ($currentState['remaining'] <= 0) {
                    return [
                        'allowed' => false,
                        'released' => 0,
                        'message' => 'Bạn đã sử dụng hết 2 lần chọn lại ghế.',
                        'error' => 'seat_reselection_limit_reached',
                        'seatReselection' => $currentState,
                    ];
                }

                $released = $this->releaseSeatReservations(
                    $showtimeId,
                    $data['seats'],
                    Auth::id()
                );

                if ($released <= 0) {
                    return [
                        'allowed' => false,
                        'released' => 0,
                        'message' => 'Ghế giữ chỗ đã hết hạn hoặc không còn tồn tại.',
                        'error' => 'seat_reservation_missing',
                        'seatReselection' => $currentState,
                    ];
                }

                $used++;
                DB::table('booking_session_tracking')
                    ->where('id', $tracking->id)
                    ->update(['seat_reselection_count' => $used]);

                return [
                    'allowed' => true,
                    'released' => $released,
                    'message' => null,
                    'error' => null,
                    'seatReselection' => $this->seatReselectionState($used),
                ];
            });

            if (!$result['allowed']) {
                return response()->json([
                    'success' => false,
                    'message' => $result['message'],
                    'error' => $result['error'],
                    'seatReselection' => $result['seatReselection'],
                ], 409);
            }

            $released = $result['released'];
            $seatReselection = $result['seatReselection'];
        } else {
            $released = $this->releaseSeatReservations(
                $showtimeId,
                $data['seats'],
                Auth::id()
            );
        }

        $seatStatus = $this->getSeatStatusData($showtimeId, Auth::id());
        if ($released > 0) {
            $this->dispatchSeatMapChanged(
                $showtimeId,
                'released',
                $seatStatus['bookedSeats'],
                $seatStatus['reservedSeats'],
                $data['seats'],
                Auth::id()
            );
        }

        $response = [
            'success' => true,
            'released' => $released,
            'reservedSeats' => $seatStatus['reservedSeats'],
            'myReservedSeats' => $seatStatus['myReservedSeats'],
            'serverNow' => now()->toIso8601String(),
            'reservationExpiresAt' => null,
            'remainingSeconds' => 0,
        ];

        if ($seatReselection !== null) {
            $response['seatReselection'] = $seatReselection;
        }

        return response()->json($response);
    }

    public function extendReservedSeats(Request $request)
    {
        if (!Auth::check()) {
            return response()->json(['success' => false, 'message' => 'Unauthenticated'], 401);
        }

        $data = $request->validate([
            'showtime_id' => 'required|exists:showtimes,id',
            'seats' => 'required|array|min:1',
        ]);

        $extended = $this->extendSeatReservations(
            (int) $data['showtime_id'],
            $data['seats'],
            Auth::id(),
            session()->getId()
        );

        $seatStatus = $this->getSeatStatusData((int) $data['showtime_id'], Auth::id());

        return response()->json([
            'success' => true,
            'extended' => $extended,
            'reservedSeats' => $seatStatus['reservedSeats'],
            'myReservedSeats' => $seatStatus['myReservedSeats'],
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
            'payment_method' => 'required|in:vnpay,wallet',
        ]);
        
        $user = Auth::user();
        $showtimeId = $request->input('showtime_id');
        $seats = $this->normalizeSeatList($request->input('seats', []));
        $customerEmail = $request->input('customer_email');
        $foodItems = $request->input('food_items', []);
        $paymentMethod = $request->input('payment_method', 'vnpay');
        
        // Validate seats với logic phức tạp
        $validationError = $this->validateSeatSelection($seats, $showtimeId);
        if ($validationError) {
            return redirect()->back()->with('error', $validationError);
        }
        
        // Get showtime
        $showtime = Showtime::with(['movie', 'screen'])->findOrFail($showtimeId);

        $roomSession = $this->touchBookingRoomSession($showtime, $user);
        if (!$roomSession['allowed']) {
            return redirect()->route('booking.index', [
                'movie' => $showtime->movie_id,
                'theater' => $showtime->theater_id,
                'date' => Carbon::parse($showtime->show_date)->toDateString(),
            ])->with('error', $roomSession['message']);
        }
        
        // Check if showtime has passed
        try {
            if ($showtime->show_date && $showtime->show_time) {
                // show_date is already a Carbon instance, just need to set the time
                $showtimeDateTime = Carbon::parse($showtime->show_date)->setTimeFromTimeString($showtime->show_time);
                if ($showtimeDateTime->isPast()) {
                    return redirect()->back()->with('error', 'Suất chiếu đã qua!');
                }
            }
        } catch (\Exception $e) {
            Log::error('Error parsing showtime date: ' . $e->getMessage(), [
                'show_date' => $showtime->show_date,
                'show_time' => $showtime->show_time,
                'error' => $e->getMessage()
            ]);
            // Continue even if date parsing fails - don't block booking
        }
        
        if (empty($seats)) {
            return redirect()->back()->withInput()->with('error', 'Vui lòng chọn ghế hợp lệ.');
        }

        $activeReservationSeats = SeatReservation::query()
            ->where('showtime_id', $showtimeId)
            ->where('user_id', $user->id)
            ->whereIn('seat', $seats)
            ->active()
            ->pluck('seat')
            ->toArray();

        if (count($activeReservationSeats) !== count($seats)) {
            $releasedSeats = $this->releaseSeatReservations($showtimeId, $seats, $user->id);
            if ($releasedSeats > 0) {
                $seatStatus = $this->getSeatStatusData($showtimeId, $user->id);
                $this->dispatchSeatMapChanged(
                    $showtimeId,
                    'released',
                    $seatStatus['bookedSeats'],
                    $seatStatus['reservedSeats'],
                    $seats,
                    $user->id
                );
            }

            return redirect()->back()->withInput()->with('error', 'Ghế của bạn đã hết hạn hoặc chưa được xác nhận. Vui lòng chọn lại ghế.');
        }

        $reservationExpiresAt = SeatReservation::query()
            ->where('showtime_id', $showtimeId)
            ->where('user_id', $user->id)
            ->whereIn('seat', $seats)
            ->active()
            ->orderBy('expires_at')
            ->value('expires_at');

        $reservationExpiresAt = $reservationExpiresAt ? Carbon::parse($reservationExpiresAt) : now();

        // Check seats availability
        $bookedSeatsCount = Ticket::where('showtime_id', $showtimeId)
            ->whereIn('seat', $seats)
            ->where('status', self::BOOKED_TICKET_STATUS)
            ->count();
        
        if ($bookedSeatsCount > 0) {
            $releasedSeats = $this->releaseSeatReservations($showtimeId, $seats, $user->id);
            if ($releasedSeats > 0) {
                $seatStatus = $this->getSeatStatusData($showtimeId, $user->id);
                $this->dispatchSeatMapChanged(
                    $showtimeId,
                    'released',
                    $seatStatus['bookedSeats'],
                    $seatStatus['reservedSeats'],
                    $seats,
                    $user->id
                );
            }

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
                'total_amount' => $totalAmount,
                'vnp_txn_ref' => $vnpTxnRef,
                'status' => 'pending',
                'expires_at' => $reservationExpiresAt,
            ]);
            
            // Save to session for VNPay
            session([
                'pending_booking_id' => $booking->id,
                'showtime_id' => $showtimeId,
            ]);

            SeatReservation::query()
                ->where('showtime_id', $showtimeId)
                ->where('user_id', $user->id)
                ->whereIn('seat', $seats)
                ->active()
                ->update([
                    'session_id' => session()->getId(),
                ]);

            if ($paymentMethod === 'wallet') {
                $lockedUser = User::whereKey($user->id)->lockForUpdate()->firstOrFail();

                if (($lockedUser->points ?? 0) < $totalAmount) {
                    DB::rollBack();
                    return redirect()->back()->withInput()->with('error', 'Số dư ví CineHub không đủ để thanh toán vé này.');
                }

                $lockedUser->decrement('points', (int) $totalAmount);

                $this->finalizeBooking($booking, 'Wallet', $seatPrices, $basePrice, $screenSurcharge);

                DB::commit();

                session()->forget(['pending_booking_id', 'showtime_id']);

                return redirect()->route('booking.confirmation', $booking->id)
                    ->with('success', 'Đặt vé thành công bằng ví CineHub! Quét mã QR để check vé.');
            }

            if (!$this->isVnpayConfigured()) {
                DB::rollBack();
                return redirect()->back()->withInput()->with(
                    'error',
                    'VNPay chưa được cấu hình. Vui lòng chọn Ví CineHub hoặc thêm VNPAY_TMN_CODE và VNPAY_HASH_SECRET vào file .env.'
                );
            }
            
            // Redirect to VNPay
            $orderInfo = 'Thanh toan ve xem phim ' . $showtime->movie->title;
            Log::info('Creating VNPay payment URL');
            
            try {
                $paymentUrl = $this->vnpay->createBookingPaymentUrl(
                    $booking,
                    $orderInfo,
                    config('services.vnpay.return_url') ?: route('payment.vnpay.callback'),
                    $request->ip()
                );
            } catch (\Throwable $vnpayError) {
                DB::rollBack();
                Log::error('VNPay URL creation failed', ['message' => $vnpayError->getMessage()]);

                return redirect()->back()->withInput()->with(
                    'error',
                    'Không tạo được liên kết VNPay: ' . $vnpayError->getMessage()
                );
            }
            
            Log::info('VNPay payment URL created:', ['url' => $paymentUrl]);
            Log::info('Booking created successfully', ['booking_id' => $booking->id, 'vnp_txn_ref' => $vnpTxnRef]);
            Log::info('Redirecting to VNPay...');
            
            // Debug: Also store in session
            session(['last_payment_url' => $paymentUrl]);

            DB::commit();

            return redirect()->away($paymentUrl);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Booking error: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()->withInput()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
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

        $seatLayout = null;
        $bookedSeats = [];

        if ($showtime_id) {
            try {
                $seatStatus = $this->getSeatStatusData($showtime_id, Auth::id());
                $bookedSeats = array_values(array_unique(array_merge(
                    $seatStatus['bookedSeats'],
                    $seatStatus['otherReservedSeats']
                )));

                $showtime = Showtime::find($showtime_id);
                if ($showtime && $showtime->screen_id) {
                    $screen = Screen::find($showtime->screen_id);
                    if ($screen && $screen->seat_layout_config) {
                        $seatLayout = is_string($screen->seat_layout_config)
                            ? json_decode($screen->seat_layout_config, true)
                            : $screen->seat_layout_config;
                    }
                }

                if (!$seatLayout) {
                    $seatLayout = [
                        'rows' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'],
                        'cols' => range(1, 12),
                        'vip_rows' => ['D', 'E', 'F'],
                        'couple_rows' => ['J'],
                    ];
                }
            } catch (\Exception $e) {
                Log::error('ERROR getting seat layout: ' . $e->getMessage());
                $seatLayout = [
                    'rows' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'],
                    'cols' => range(1, 12),
                    'vip_rows' => ['D', 'E', 'F'],
                    'couple_rows' => ['J'],
                ];
            }
        }

        $coupleRows = $seatLayout['couple_rows'] ?? ['J'];
        $seatsByRow = [];

        foreach ($seats as $seat) {
            $seat = Str::upper(trim((string) $seat));
            if (!preg_match('/^[A-Z]\d+$/', $seat)) {
                continue;
            }

            $row = substr($seat, 0, 1);
            $col = (int) substr($seat, 1);
            $seatsByRow[$row][] = $col;
        }

        return $this->validateSeatSelectionSimple($seatsByRow, $seatLayout, $bookedSeats, $coupleRows);
        
        // Lấy danh sách ghế đã được đặt
        $bookedSeats = [];
        $seatLayout = null;
        
        if ($showtime_id) {
            try {
                $bookedSeats = Ticket::where('showtime_id', $showtime_id)
                    ->where('status', self::BOOKED_TICKET_STATUS)
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

        return $this->validateSeatSelectionSimple($seatsByRow, $seatLayout, $bookedSeats, $coupleRows);
        
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

    private function validateSeatSelectionSimple(array $seatsByRow, ?array $seatLayout, array $bookedSeats, array $coupleRows): ?string
    {
        foreach ($seatsByRow as $row => $selectedCols) {
            sort($selectedCols);

            if (in_array($row, $coupleRows, true)) {
                continue;
            }

            $groups = $this->resolveSeatGroupsForRow($row, $seatLayout, $coupleRows);

            foreach ($groups as $groupCols) {
                sort($groupCols);
                $selectedInGroup = array_values(array_intersect($selectedCols, $groupCols));
                if (empty($selectedInGroup)) {
                    continue;
                }

                if (count($selectedInGroup) === 1) {
                    $singleSeatError = $this->validateSingleSeat(
                        $row,
                        $selectedInGroup[0],
                        $groupCols,
                        min($groupCols),
                        max($groupCols),
                        $bookedSeats
                    );
                    if ($singleSeatError) {
                        return $singleSeatError;
                    }
                }

                for ($i = 0; $i < count($selectedInGroup) - 1; $i++) {
                    if ($selectedInGroup[$i + 1] - $selectedInGroup[$i] > 1) {
                        $hasFreeGap = false;
                        for ($gapCol = $selectedInGroup[$i] + 1; $gapCol < $selectedInGroup[$i + 1]; $gapCol++) {
                            $gapSeat = $row . $gapCol;
                            if (!in_array($gapSeat, $bookedSeats, true)) {
                                $hasFreeGap = true;
                                break;
                            }
                        }
                        if ($hasFreeGap) {
                            return "Khong duoc bo trong ghe o giua cum ghe hang {$row}. Vui long chon cac ghe lien ke nhau trong cung mot cum.";
                        }
                    }
                }

                $groupError = $this->validateGroupOrphanSeats($row, $groupCols, $selectedInGroup, $bookedSeats);
                if ($groupError) {
                    return $groupError;
                }
            }
        }

        return null;
    }

    private function splitGroupColsByBooked(array $groupCols, string $row, array $bookedSeats): array
    {
        $segments = [];
        $current = [];

        foreach ($groupCols as $col) {
            $seat = $row . $col;
            if (in_array($seat, $bookedSeats, true)) {
                if (!empty($current)) {
                    $segments[] = $current;
                    $current = [];
                }
                continue;
            }
            $current[] = $col;
        }

        if (!empty($current)) {
            $segments[] = $current;
        }

        return !empty($segments) ? $segments : [$groupCols];
    }

    private function countFreeSeatsFromEdge(array $segmentCols, string $row, array $selectedCols, array $bookedSeats, bool $fromLeft): int
    {
        $count = 0;
        $cols = $fromLeft ? $segmentCols : array_reverse($segmentCols);
        $selectedLookup = array_fill_keys($selectedCols, true);

        foreach ($cols as $col) {
            $seat = $row . $col;
            if (in_array($seat, $bookedSeats, true) || isset($selectedLookup[$col])) {
                break;
            }
            $count++;
        }

        return $count;
    }

    private function isSelectionAnchoredToBooked(array $selectedCols, string $row, array $bookedSeats, string $side): bool
    {
        if (empty($selectedCols)) {
            return false;
        }

        $minSel = min($selectedCols);
        $maxSel = max($selectedCols);

        if ($side === 'left') {
            return in_array($row . ($minSel - 1), $bookedSeats, true);
        }

        return in_array($row . ($maxSel + 1), $bookedSeats, true);
    }

    private function isSelectionTouchingSegmentEdge(array $selectedCols, array $segmentCols, string $side): bool
    {
        if (empty($selectedCols) || empty($segmentCols)) {
            return false;
        }

        $minSel = min($selectedCols);
        $maxSel = max($selectedCols);
        $minSeg = min($segmentCols);
        $maxSeg = max($segmentCols);

        return $side === 'left' ? $minSel === $minSeg : $maxSel === $maxSeg;
    }

    private function validateGroupOrphanSeats(string $row, array $groupCols, array $selectedCols, array $bookedSeats): ?string
    {
        if (count($groupCols) < 2 || empty($selectedCols)) {
            return null;
        }

        sort($groupCols);
        sort($selectedCols);

        $segments = $this->splitGroupColsByBooked($groupCols, $row, $bookedSeats);

        foreach ($segments as $segmentCols) {
            sort($segmentCols);
            $selectedInSegment = array_values(array_intersect($selectedCols, $segmentCols));

            if (empty($selectedInSegment)) {
                continue;
            }

            if (count($selectedInSegment) > 2) {
                $leftFree = $this->countFreeSeatsFromEdge($segmentCols, $row, $selectedCols, $bookedSeats, true);
                $rightFree = $this->countFreeSeatsFromEdge($segmentCols, $row, $selectedCols, $bookedSeats, false);
                $anchoredLeft = $this->isSelectionAnchoredToBooked($selectedInSegment, $row, $bookedSeats, 'left');
                $anchoredRight = $this->isSelectionAnchoredToBooked($selectedInSegment, $row, $bookedSeats, 'right');
                $touchesLeft = $this->isSelectionTouchingSegmentEdge($selectedInSegment, $segmentCols, 'left') || $anchoredLeft;
                $touchesRight = $this->isSelectionTouchingSegmentEdge($selectedInSegment, $segmentCols, 'right') || $anchoredRight;

                if (!$touchesRight && $leftFree === 1) {
                    return "Khi dat hon 2 ghe o hang {$row}, phai de trong 0 hoac it nhat 2 ghe o dau cum. Hien dang de le 1 ghe.";
                }

                if (!$touchesLeft && $rightFree === 1) {
                    return "Khi dat hon 2 ghe o hang {$row}, phai de trong 0 hoac it nhat 2 ghe o cuoi cum. Hien dang de le 1 ghe.";
                }
            }

            $existingSingles = $this->findSingleFreeColsInGroup($row, $segmentCols, [], $bookedSeats);
            $newSingles = $this->findSingleFreeColsInGroup($row, $segmentCols, $selectedCols, $bookedSeats);

            foreach ($newSingles as $col) {
                if (in_array($col, $existingSingles, true)) {
                    continue;
                }

                if ($this->isInvalidSingleOrphanCol((int) $col, $segmentCols, $row, $selectedInSegment, $bookedSeats)) {
                    return "Khong duoc de le 1 ghe trong cum hang {$row} (ghe {$row}{$col}). Vui long chon them ghe do, chon tu phia ghe da dat, hoac chua toi thieu 2 ghe trong lien tiep.";
                }
            }
        }

        return null;
    }

    private function findSingleFreeColsInGroup(string $row, array $groupCols, array $selectedCols, array $bookedSeats): array
    {
        $selectedLookup = array_fill_keys($selectedCols, true);
        $singles = [];
        $freeRun = [];

        foreach ($groupCols as $col) {
            $seat = $row . $col;
            $occupied = in_array($seat, $bookedSeats, true)
                || (!empty($selectedLookup) && isset($selectedLookup[$col]));

            if (!$occupied) {
                $freeRun[] = $col;
                continue;
            }

            if (count($freeRun) === 1) {
                $singles[] = (string) $freeRun[0];
            }
            $freeRun = [];
        }

        if (count($freeRun) === 1) {
            $singles[] = (string) $freeRun[0];
        }

        return $singles;
    }

    private function isInvalidSingleOrphanCol(int $col, array $groupCols, string $row, array $selectedCols, array $bookedSeats): bool
    {
        $idx = array_search($col, $groupCols, true);
        if ($idx === false) {
            return false;
        }

        $leftCol = $idx > 0 ? $groupCols[$idx - 1] : null;
        $rightCol = $idx < count($groupCols) - 1 ? $groupCols[$idx + 1] : null;

        $isOccupied = function (?int $seatCol) use ($row, $bookedSeats, $selectedCols): bool {
            if ($seatCol === null) {
                return false;
            }

            $seat = $row . $seatCol;
            return in_array($seat, $bookedSeats, true) || in_array($seatCol, $selectedCols, true);
        };

        $leftOcc = $isOccupied($leftCol);
        $rightOcc = $isOccupied($rightCol);

        if ($leftOcc && $rightOcc) {
            return true;
        }

        $minSel = min($selectedCols);
        $maxSel = max($selectedCols);

        if (!$leftOcc && $rightOcc && $col < $minSel) {
            if ($maxSel === max($groupCols)) {
                return false;
            }
            if (in_array($row . ($maxSel + 1), $bookedSeats, true)) {
                return false;
            }
            if (count($selectedCols) >= 2) {
                return true;
            }
        }

        if ($leftOcc && !$rightOcc && $col > $maxSel) {
            if ($minSel === min($groupCols)) {
                return false;
            }
            if (in_array($row . ($minSel - 1), $bookedSeats, true)) {
                return false;
            }
            if (count($selectedCols) >= 2) {
                $leftSeat = $row . $leftCol;
                if (in_array($leftSeat, $bookedSeats, true)) {
                    return false;
                }
                if (in_array($leftCol, $selectedCols, true)) {
                    return true;
                }
            }
        }

        return false;
    }

    private function validateNoSingleSeatAtRowStart(string $row, array $selectedCols, ?array $seatLayout, array $bookedSeats): ?string
    {
        $rowCols = $this->getAllColumnsInRow($row, $seatLayout);
        if (empty($rowCols)) {
            $rowCols = range(1, max($selectedCols));
        }

        sort($rowCols);

        if (count($rowCols) < 2) {
            return null;
        }

        $selectedLookup = array_fill_keys($selectedCols, true);
        $leftFree = 0;

        foreach ($rowCols as $col) {
            $seat = $row . $col;
            if (in_array($seat, $bookedSeats, true) || isset($selectedLookup[$col])) {
                break;
            }
            $leftFree++;
        }

        if ($leftFree === 1) {
            return "Khi dat hon 2 ghe o hang {$row}, khong duoc de le 1 ghe o dau hang. Vui long chon them ghe do hoac chua toi thieu 2 ghe trong o dau hang.";
        }

        return null;
    }

    private function validateNoSingleSeatAtRowEnds($row, array $selectedCols, ?array $seatLayout, array $bookedSeats): ?string
    {
        sort($selectedCols);

        $rowCols = $this->getAllColumnsInRow($row, $seatLayout);
        if (empty($rowCols)) {
            $rowCols = range(1, max($selectedCols));
        }

        sort($rowCols);

        if (count($rowCols) < 2) {
            return null;
        }

        $selectedLookup = array_fill_keys($selectedCols, true);

        $leftFree = 0;
        foreach ($rowCols as $col) {
            $seat = $row . $col;
            if (in_array($seat, $bookedSeats, true) || isset($selectedLookup[$col])) {
                break;
            }
            $leftFree++;
        }

        $rightFree = 0;
        for ($i = count($rowCols) - 1; $i >= 0; $i--) {
            $col = $rowCols[$i];
            $seat = $row . $col;
            if (in_array($seat, $bookedSeats, true) || isset($selectedLookup[$col])) {
                break;
            }
            $rightFree++;
        }

        if ($leftFree === 1 || $rightFree === 1) {
            return "Khong duoc de le 1 ghe o dau hoac cuoi hang {$row}. Vui long chon sat dau hang hoac chua toi thieu 2 ghe.";
        }

        return null;
    }
    
    /**
     * Booking success page with QR codes
     */
    public function confirmation($bookingId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $booking = Booking::with(['showtime.movie', 'showtime.theater', 'showtime.screen', 'tickets'])
            ->where('id', $bookingId)
            ->where('user_id', Auth::id())
            ->where('status', 'completed')
            ->firstOrFail();

        return view('booking.confirmation', compact('booking'));
    }

    /**
     * My tickets page
     */
    public function myTickets()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // Get all bookings for this user (completed bookings only)
        $bookings = Booking::with(['showtime.movie', 'showtime.theater', 'showtime.screen', 'tickets'])
            ->where('user_id', Auth::id())
            ->where('status', 'completed')
            ->orderByDesc('created_at')
            ->paginate(20);
        
        return view('booking.my-tickets', compact('bookings'));
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

    private function finalizeBooking(
        Booking $booking,
        string $paymentMethod,
        array $seatPrices = [],
        ?float $basePrice = null,
        ?float $screenSurcharge = null
    ): void {
        $finalize = function () use ($booking, $paymentMethod, $seatPrices, $basePrice, $screenSurcharge) {
            $lockedBooking = Booking::whereKey($booking->id)->lockForUpdate()->firstOrFail();

            if ($lockedBooking->status === 'completed') {
                return;
            }

            if ($lockedBooking->expires_at && Carbon::parse($lockedBooking->expires_at)->lte(now())) {
                $lockedBooking->update(['status' => 'cancelled']);
                throw new \RuntimeException('Thời gian giữ ghế đã hết. Vui lòng chọn lại ghế.');
            }

            $activeReservationCount = SeatReservation::query()
                ->where('showtime_id', $lockedBooking->showtime_id)
                ->where('user_id', $lockedBooking->user_id)
                ->whereIn('seat', $lockedBooking->seats ?? [])
                ->active()
                ->count();

            if ($activeReservationCount !== count($lockedBooking->seats ?? [])) {
                $lockedBooking->update(['status' => 'cancelled']);
                throw new \RuntimeException('Thời gian giữ ghế đã hết. Vui lòng chọn lại ghế.');
            }

            $showtime = Showtime::with('screen')->findOrFail($lockedBooking->showtime_id);
            $basePrice = $basePrice ?? $showtime->price;
            $screenSurcharge = $screenSurcharge ?? $this->getScreenTypeSurcharge($showtime->screen->screen_type ?? '2D');

            foreach ($lockedBooking->seats as $seat) {
                $existingTicket = Ticket::where('booking_pending_id', $lockedBooking->id)
                    ->where('seat', $seat)
                    ->first();

                if ($existingTicket) {
                    continue;
                }

                $seatType = $this->getSeatType($seat);
                $price = $seatPrices[$seat] ?? $this->calculateSeatPrice($basePrice, $screenSurcharge, $seatType);

                Ticket::create([
                    'user_id' => $lockedBooking->user_id,
                    'showtime_id' => $lockedBooking->showtime_id,
                    'booking_pending_id' => $lockedBooking->id,
                    'seat' => $seat,
                    'seat_type' => $seatType,
                    'price' => $price,
                    'qr_code' => 'TICKET_' . Str::random(24),
                    'status' => self::BOOKED_TICKET_STATUS,
                ]);
            }

            Transaction::firstOrCreate(
                [
                    'type' => 'ticket',
                    'related_id' => $lockedBooking->id,
                ],
                [
                    'user_id' => $lockedBooking->user_id,
                    'amount' => $lockedBooking->total_amount,
                    'method' => $this->mapTransactionMethod($paymentMethod),
                    'status' => 'Thành công',
                ]
            );

            SeatReservation::query()
                ->where('showtime_id', $lockedBooking->showtime_id)
                ->whereIn('seat', $lockedBooking->seats ?? [])
                ->delete();

            $lockedBooking->update([
                'status' => 'completed',
                'qr_code' => $lockedBooking->qr_code ?: ('BOOKING_' . Str::random(24)),
            ]);

            DB::afterCommit(function () use ($lockedBooking): void {
                $seatStatus = $this->getSeatStatusData($lockedBooking->showtime_id, $lockedBooking->user_id);

                $this->dispatchSeatMapChanged(
                    $lockedBooking->showtime_id,
                    'paid',
                    $seatStatus['bookedSeats'],
                    $seatStatus['reservedSeats'],
                    $lockedBooking->seats ?? [],
                    $lockedBooking->user_id
                );
            });

            $this->createBookingNotification($lockedBooking->fresh(['showtime.movie']));
        };

        if (DB::transactionLevel() > 0) {
            $finalize();
        } else {
            DB::transaction($finalize);
        }
    }

    private function createBookingNotification(Booking $booking): void
    {
        $showtime = $booking->showtime;
        $movieTitle = $showtime?->movie?->title ?? 'phim';
        $seatList = implode(', ', $booking->seats ?? []);
        $seatCount = count($booking->seats ?? []);

        DB::table('notifications')->insert([
            'user_id' => $booking->user_id,
            'type' => 'success',
            'title' => 'Đặt vé thành công',
            'message' => "Bạn đã đặt thành công {$seatCount} vé xem phim \"{$movieTitle}\" tại ghế {$seatList}. Quét mã QR để check vé.",
            'link' => route('booking.history'),
            'is_read' => 0,
            'created_at' => now(),
        ]);
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

    private function mapTransactionMethod(string $method): string
    {
        return match (strtolower($method)) {
            'wallet' => 'Bank',
            'vnpay', 'momo' => 'Momo',
            'zalopay' => 'ZaloPay',
            'stripe' => 'Stripe',
            'cash' => 'Cash',
            'bank' => 'Bank',
            default => 'Bank',
        };
    }
}
