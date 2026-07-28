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
use App\Models\FoodItem;
use App\Services\QrCodeService;
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
        $scannedBookingIds = Ticket::whereHas('showtime.screen', function ($q) use ($theaterId) {
                $q->where('theater_id', $theaterId);
            })
            ->where('picked_up_by', $user->id)
            ->whereDate('picked_up_at', today())
            ->whereNotNull('booking_pending_id')
            ->distinct()
            ->pluck('booking_pending_id');
        $scannedBookings = Booking::whereIn('id', $scannedBookingIds)->get();
        $drinkStats = $this->summarizeBookingFood($scannedBookings, $theaterId);

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
            'drinks_delivered' => $drinkStats['quantity'],
            'drink_revenue' => $drinkStats['revenue'],
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

        if (!$bookingId && is_string($bookingCode) && str_starts_with($bookingCode, 'BOOKING-')) {
            $parts = explode('-', $bookingCode, 3);
            $bookingId = $parts[1] ?? null;
        }
        
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
        
        $updatedCount = DB::transaction(function () use ($tickets) {
            $updated = 0;
            foreach ($tickets as $ticket) {
                $lockedTicket = Ticket::lockForUpdate()->find($ticket->id);
                if (!$lockedTicket->is_picked_up) {
                    $lockedTicket->update([
                        'is_picked_up' => true,
                        'picked_up_at' => now(),
                        'picked_up_by' => Auth::id(),
                    ]);
                    $updated++;
                }
            }

            return $updated;
        });
        $tickets->each->refresh();
        $foodItems = $this->bookingFoodDetails($booking, Auth::user()->theater_id);
        
        return response()->json([
            'success' => true,
            'message' => "Đã xác nhận {$updatedCount} vé đã được lấy",
            'booking' => [
                'id' => $booking->id,
                'booking_code' => $booking->qr_code,
                'customer_name' => $booking->customer_name,
                'customer_phone' => $booking->customer_phone,
            ],
            'tickets' => $tickets->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'seat' => $ticket->seat,
                    'seat_type' => $ticket->seat_type,
                    'is_picked_up' => (bool) $ticket->is_picked_up,
                    'user_name' => $ticket->user->name ?? 'Khach le',
                    'movie_title' => $ticket->showtime->movie->title ?? 'N/A',
                    'show_date' => $ticket->showtime->show_date ?? null,
                    'show_time' => $ticket->showtime->show_time ?? null,
                    'screen_name' => $ticket->showtime->screen->screen_name ?? 'N/A',
                ];
            })->values(),
            'food_items' => $foodItems,
            'food_total' => $foodItems->sum('subtotal'),
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
        
        $foodByBooking = $tickets->getCollection()
            ->pluck('bookingPending')
            ->filter()
            ->unique('id')
            ->mapWithKeys(fn ($booking) => [$booking->id => $this->bookingFoodDetails($booking, Auth::user()->theater_id)]);

        return view('admin.counter_staff.scanned_tickets', compact('tickets', 'date', 'foodByBooking'));
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
        $foodItems = FoodItem::where('theater_id', $theaterId)
            ->where('is_active', true)
            ->orderBy('type')
            ->orderBy('name')
            ->get();
        
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
                $seatLayout = $this->normalizeSeatLayout($selectedShowtime->screen?->seat_layout_config);
            }
        }
        
        return view('admin.counter_staff.sell_ticket', compact(
            'showtimes',
            'selectedShowtime',
            'bookedSeats',
            'seatLayout',
            'date',
            'foodItems'
        ));
    }
    
    /**
     * Xử lý bán vé
     */
    public function processSale(Request $request)
    {
        $request->validate([
            'showtime_id' => 'required|exists:showtimes,id',
            'seats' => 'required|array|min:1|max:8',
            'customer_name' => 'nullable|string|max:255',
            'customer_phone' => 'nullable|string|max:20',
            'food_items' => 'nullable|array',
        ]);
        
        $showtimeId = $request->input('showtime_id');
        $seats = $this->normalizeSeatList($request->input('seats', []));
        $customerName = $request->input('customer_name', 'Khách lẻ');
        $customerPhone = $request->input('customer_phone', '');
        $foodItems = $request->input('food_items', []);
        
        $theaterId = Auth::user()->theater_id;
        
        $showtime = Showtime::with(['movie', 'screen'])
            ->whereHas('screen', function($query) use ($theaterId) {
                $query->where('theater_id', $theaterId);
            })
            ->findOrFail($showtimeId);

        if (empty($seats)) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng chọn ghế hợp lệ.'
            ]);
        }

        $seatLayout = $this->normalizeSeatLayout($showtime->screen?->seat_layout_config);
        $validSeatError = $this->validateSeatsExistInLayout($seats, $seatLayout);
        if ($validSeatError) {
            return response()->json([
                'success' => false,
                'message' => $validSeatError
            ]);
        }
        
        // Kiểm tra ghế
        $occupiedSeats = array_unique(array_merge(
            Ticket::where('showtime_id', $showtimeId)
                ->where('status', 'Đã đặt')
                ->pluck('seat')
                ->toArray(),
            SeatReservation::where('showtime_id', $showtimeId)
                ->active()
                ->pluck('seat')
                ->toArray()
        ));

        $existingSeats = Ticket::where('showtime_id', $showtimeId)
            ->where('status', 'Đã đặt')
            ->whereIn('seat', $seats)
            ->pluck('seat')
            ->toArray();

        $reservedSeats = SeatReservation::where('showtime_id', $showtimeId)
            ->active()
            ->whereIn('seat', $seats)
            ->pluck('seat')
            ->toArray();

        $unavailableSeats = array_unique(array_merge($existingSeats, $reservedSeats));
        
        if (!empty($unavailableSeats)) {
            return response()->json([
                'success' => false,
                'message' => 'Ghế ' . implode(', ', $unavailableSeats) . ' đã được đặt'
            ]);
        }

        $seatRuleError = $this->validateSeatSelection($seats, $seatLayout, $occupiedSeats);
        if ($seatRuleError) {
            return response()->json([
                'success' => false,
                'message' => $seatRuleError
            ]);
        }

        DB::beginTransaction();
        
        try {
            $bookingCode = 'COUNTER_' . uniqid() . '_' . time();
            $totalAmount = 0;
            
            foreach ($seats as $seat) {
                $seatType = $this->getSeatType($seat, $seatLayout);
                $price = $this->getSeatPrice($showtime->price, $seatType);
                $totalAmount += $price;
            }

            $foodTotal = 0;
            $filteredFoodItems = [];
            $foodItemRows = FoodItem::where('theater_id', $theaterId)
                ->where('is_active', true)
                ->whereIn('id', array_keys($foodItems))
                ->get()
                ->keyBy('id');

            foreach ($foodItems as $foodId => $quantity) {
                $quantity = min(max((int) $quantity, 0), 10);
                $food = $foodItemRows->get((int) $foodId);

                if (!$food || $quantity <= 0) {
                    continue;
                }

                $filteredFoodItems[(int) $foodId] = $quantity;
                $foodTotal += (float) $food->price * $quantity;
            }

            $totalAmount += $foodTotal;
            
            // Tạo booking
            $booking = Booking::create([
                'user_id' => Auth::id(),
                'showtime_id' => $showtimeId,
                'seats' => $seats,
                'food_items' => $filteredFoodItems,
                'total_amount' => $totalAmount,
                'status' => 'completed',
                'customer_name' => $customerName,
                'customer_phone' => $customerPhone,
                'qr_code' => $bookingCode,
            ]);
            
            // Tạo tickets
            $ticketIds = [];
            foreach ($seats as $seat) {
                $seatType = $this->getSeatType($seat, $seatLayout);
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

            foreach ($filteredFoodItems as $foodId => $quantity) {
                $food = $foodItemRows->get((int) $foodId);

                DB::table('booking_food_items')->insert([
                    'booking_pending_id' => $booking->id,
                    'food_item_id' => $food->id,
                    'quantity' => $quantity,
                    'price' => $food->price,
                    'created_at' => now(),
                ]);
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

    public function printTickets(Request $request, QrCodeService $qrCodeService)
    {
        $booking = Booking::with(['showtime.screen.theater', 'tickets.showtime.movie', 'tickets.showtime.screen'])
            ->where('id', $request->input('booking_id'))
            ->whereHas('showtime.screen', function ($query) {
                $query->where('theater_id', Auth::user()->theater_id);
            })
            ->firstOrFail();

        $tickets = $booking->tickets->map(function ($ticket) use ($qrCodeService) {
            return [
                'id' => $ticket->id,
                'movie_title' => $ticket->showtime->movie->title ?? 'N/A',
                'screen_name' => $ticket->showtime->screen->screen_name ?? 'N/A',
                'show_date' => $ticket->showtime->show_date ?? null,
                'show_time' => $ticket->showtime->show_time ?? null,
                'seat' => $ticket->seat,
                'seat_type' => $ticket->seat_type,
                'price' => $ticket->price,
                'qr_code' => $ticket->qr_code,
                'qr_image' => $qrCodeService->generateTicketQr($ticket->id, $ticket->seat, $ticket->qr_code),
            ];
        });

        return view('admin.counter_staff.print_tickets', [
            'booking' => $booking,
            'tickets' => $tickets,
            'theater' => $booking->showtime->screen->theater,
        ]);
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
    
    private function getSeatType($seat, ?array $seatLayout = null)
    {
        $row = substr($seat, 0, 1);
        $vipRows = $seatLayout['vip_rows'] ?? ['D', 'E', 'F'];
        $coupleRows = $seatLayout['couple_rows'] ?? ['J'];
        
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

    private function normalizeSeatLayout($layout): array
    {
        if (is_array($layout) && !empty($layout)) {
            return $layout;
        }

        if (is_string($layout) && trim($layout) !== '') {
            $decoded = json_decode($layout, true);

            if (is_array($decoded) && !empty($decoded)) {
                return $decoded;
            }
        }

        return $this->getDefaultSeatLayout();
    }

    private function normalizeSeatList(array $seats): array
    {
        $normalized = [];

        foreach ($seats as $seat) {
            if (!is_string($seat) && !is_numeric($seat)) {
                continue;
            }

            $seat = strtoupper(trim((string) $seat));

            if (!preg_match('/^[A-Z]\d+$/', $seat)) {
                continue;
            }

            $normalized[$seat] = $seat;
        }

        return array_values($normalized);
    }

    private function getSeatColumnsForRow(string $row, array $seatLayout): array
    {
        $rows = $seatLayout['rows'] ?? [];
        $coupleRows = $seatLayout['couple_rows'] ?? ['J'];

        if (!empty($rows) && !in_array($row, $rows, true)) {
            return [];
        }

        if (array_is_list($seatLayout)) {
            foreach ($seatLayout as $rowConfig) {
                if (($rowConfig['row'] ?? null) !== $row || empty($rowConfig['seats']) || !is_array($rowConfig['seats'])) {
                    continue;
                }

                $cols = [];
                foreach ($rowConfig['seats'] as $seat) {
                    if (($seat['type'] ?? null) === 'disabled' || (isset($seat['available']) && !$seat['available'])) {
                        continue;
                    }

                    $seatNumber = $seat['number'] ?? null;
                    if ($seatNumber) {
                        $cols[] = (int) substr($seatNumber, 1);
                    }
                }

                sort($cols);
                return array_values(array_unique($cols));
            }
        }

        $cols = $seatLayout['cols'] ?? range(1, (int) ($seatLayout['seats_per_row'] ?? 12));

        if (in_array($row, $coupleRows, true)) {
            $pairCount = max(1, (int) floor(count($cols) / 2));
            return range(1, $pairCount);
        }

        sort($cols);
        return array_values(array_unique(array_map('intval', $cols)));
    }

    private function validateSeatsExistInLayout(array $seats, array $seatLayout): ?string
    {
        foreach ($seats as $seat) {
            $row = substr($seat, 0, 1);
            $col = (int) substr($seat, 1);
            $cols = $this->getSeatColumnsForRow($row, $seatLayout);

            if (empty($cols) || !in_array($col, $cols, true)) {
                return "Ghe {$seat} khong ton tai trong so do phong chieu.";
            }
        }

        return null;
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

    private function resolveSeatGroupsForRow(string $row, array $seatLayout): array
    {
        $coupleRows = $seatLayout['couple_rows'] ?? ['J'];
        $groups = [];

        if (!in_array($row, $coupleRows, true) && isset($seatLayout['seat_groups']) && is_array($seatLayout['seat_groups'])) {
            foreach ($seatLayout['seat_groups'] as $group) {
                $groupRows = $group['rows'] ?? [];
                $groupCols = $group['cols'] ?? [];

                if (in_array($row, $groupRows, true) && !empty($groupCols)) {
                    $groups[] = array_values(array_unique(array_map('intval', $groupCols)));
                }
            }
        }

        if (!empty($groups)) {
            return $groups;
        }

        return $this->splitColsByAisles($this->getSeatColumnsForRow($row, $seatLayout));
    }

    private function validateSeatSelection(array $seats, array $seatLayout, array $bookedSeats): ?string
    {
        $seatsByRow = [];

        foreach ($seats as $seat) {
            $row = substr($seat, 0, 1);
            $seatsByRow[$row][] = (int) substr($seat, 1);
        }

        foreach ($seatsByRow as $row => $selectedCols) {
            sort($selectedCols);

            if (in_array($row, $seatLayout['couple_rows'] ?? ['J'], true)) {
                continue;
            }

            foreach ($this->resolveSeatGroupsForRow($row, $seatLayout) as $groupCols) {
                sort($groupCols);
                $selectedInGroup = array_values(array_intersect($selectedCols, $groupCols));

                if (empty($selectedInGroup)) {
                    continue;
                }

                for ($i = 0; $i < count($selectedInGroup) - 1; $i++) {
                    if ($selectedInGroup[$i + 1] - $selectedInGroup[$i] > 1) {
                        return "Khong duoc bo trong ghe o giua cum ghe hang {$row}. Vui long chon cac ghe lien ke nhau.";
                    }
                }

                $error = $this->validateNoNewSingleSeat($row, $groupCols, $selectedInGroup, $bookedSeats);
                if ($error) {
                    return $error;
                }
            }
        }

        return null;
    }

    private function validateNoNewSingleSeat(string $row, array $groupCols, array $selectedCols, array $bookedSeats): ?string
    {
        $existingSingles = $this->findSingleFreeColsInGroup($row, $groupCols, [], $bookedSeats);
        $newSingles = $this->findSingleFreeColsInGroup($row, $groupCols, $selectedCols, $bookedSeats);

        foreach ($newSingles as $col) {
            if (!in_array($col, $existingSingles, true)) {
                return "Khong duoc de le 1 ghe trong cum hang {$row} (ghe {$row}{$col}).";
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
            $occupied = in_array($seat, $bookedSeats, true) || isset($selectedLookup[$col]);

            if (!$occupied) {
                $freeRun[] = $col;
                continue;
            }

            if (count($freeRun) === 1) {
                $singles[] = $freeRun[0];
            }

            $freeRun = [];
        }

        if (count($freeRun) === 1) {
            $singles[] = $freeRun[0];
        }

        return $singles;
    }

    private function bookingFoodDetails(Booking $booking, int $theaterId)
    {
        $quantities = collect($booking->food_items ?? [])
            ->mapWithKeys(fn ($quantity, $id) => [(int) $id => max(0, (int) $quantity)])
            ->filter();
        $foods = FoodItem::where('theater_id', $theaterId)
            ->whereIn('id', $quantities->keys())
            ->get()
            ->keyBy('id');

        return $quantities->map(function ($quantity, $id) use ($foods) {
            $food = $foods->get($id);
            if (!$food) return null;
            return [
                'id' => $food->id,
                'name' => $food->name,
                'type' => $food->type,
                'quantity' => $quantity,
                'unit_price' => (float) $food->price,
                'subtotal' => (float) $food->price * $quantity,
            ];
        })->filter()->values();
    }

    private function summarizeBookingFood($bookings, int $theaterId): array
    {
        $details = $bookings->flatMap(fn ($booking) => $this->bookingFoodDetails($booking, $theaterId));

        return [
            'quantity' => (int) $details->sum('quantity'),
            'revenue' => (float) $details->sum('subtotal'),
        ];
    }
}
