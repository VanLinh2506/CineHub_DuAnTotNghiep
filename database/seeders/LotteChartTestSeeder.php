<?php

namespace Database\Seeders;

use App\Models\Movie;
use App\Models\Screen;
use App\Models\Theater;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LotteChartTestSeeder extends Seeder
{
    private const BOOKING_PREFIX = 'LOTTE-CHART-TEST-';

    /**
     * Seed 14 days of completed Lotte Cinema HCMC bookings for moderator charts.
     * The prefix makes it safe to run repeatedly without duplicating test data.
     */
    public function run(): void
    {
        if (DB::table('booking_pending')->where('booking_code', 'like', self::BOOKING_PREFIX . '%')->exists()) {
            $this->command?->info('Dữ liệu test biểu đồ Lotte TP.HCM đã tồn tại, không tạo trùng.');

            return;
        }

        $theater = Theater::query()
            ->where('name', 'Lotte Cinema')
            ->where('location', 'Hồ Chí Minh')
            ->firstOrFail();

        $screens = Screen::query()
            ->where('theater_id', $theater->id)
            ->orderBy('id')
            ->take(3)
            ->get(['id', 'total_seats']);
        $movies = Movie::query()
            ->where('status', 'Chiếu rạp')
            ->orderBy('id')
            ->take(3)
            ->get(['id']);
        $userId = User::query()->value('id');

        if ($screens->count() < 3 || $movies->count() < 3 || !$userId) {
            throw new \RuntimeException('Thiếu phòng chiếu, phim hoặc người dùng để tạo dữ liệu test.');
        }

        DB::transaction(function () use ($theater, $screens, $movies, $userId): void {
            for ($dayOffset = 13; $dayOffset >= 0; $dayOffset--) {
                $bookingDate = now()->subDays($dayOffset)->startOfDay();

                for ($slot = 0; $slot < 3; $slot++) {
                    $screen = $screens[$slot];
                    $movie = $movies[($dayOffset + $slot) % $movies->count()];
                    $showTime = sprintf('%02d:00:00', 13 + ($slot * 3));
                    $ticketPrice = 90000 + ($slot * 15000);
                    $createdAt = $bookingDate->copy()->setTime(9 + $slot, 15, 0);
                    $ticketCount = 4 + (($dayOffset * 2 + $slot * 3) % 9);
                    $pickedUpCount = max(1, (int) floor($ticketCount * (0.45 + (($dayOffset + $slot) % 5) * 0.1)));

                    $showtimeId = DB::table('showtimes')->insertGetId([
                        'movie_id' => $movie->id,
                        'theater_id' => $theater->id,
                        'theater_contract_id' => null,
                        'screen_id' => $screen->id,
                        'show_date' => $bookingDate->toDateString(),
                        'show_time' => $showTime,
                        'price' => $ticketPrice,
                        'contract_price_type' => null,
                        'available_seats' => $screen->total_seats ?? 144,
                        'created_at' => $createdAt,
                    ]);

                    $seats = collect(range(1, $ticketCount))
                        ->map(fn (int $seat) => 'B' . $seat)
                        ->all();
                    $foodAmount = $slot === 1 ? 45000 : 0;
                    $bookingId = DB::table('booking_pending')->insertGetId([
                        'user_id' => $userId,
                        'customer_name' => 'Khách test biểu đồ',
                        'customer_phone' => '0900000000',
                        'showtime_id' => $showtimeId,
                        'seats' => json_encode($seats),
                        'food_items' => json_encode($foodAmount ? [['name' => 'Bắp rang test', 'quantity' => 1, 'price' => $foodAmount]] : []),
                        'customer_email' => 'chart-test@lotte.local',
                        'total_amount' => ($ticketPrice * $ticketCount) + $foodAmount,
                        'vnp_txn_ref' => self::BOOKING_PREFIX . 'TXN-' . $bookingDate->format('Ymd') . '-' . $slot,
                        'booking_code' => self::BOOKING_PREFIX . $bookingDate->format('Ymd') . '-' . $slot,
                        'status' => 'completed',
                        'created_at' => $createdAt,
                        'expires_at' => null,
                        'qr_code' => null,
                    ]);

                    $tickets = [];
                    foreach ($seats as $index => $seat) {
                        $isPickedUp = $index < $pickedUpCount;
                        $tickets[] = [
                            'user_id' => $userId,
                            'showtime_id' => $showtimeId,
                            'booking_pending_id' => $bookingId,
                            'seat' => $seat,
                            'seat_type' => 'normal',
                            'qr_code' => self::BOOKING_PREFIX . 'TICKET-' . $bookingDate->format('Ymd') . '-' . $slot . '-' . ($index + 1),
                            'price' => $ticketPrice,
                            'status' => 'Đã đặt',
                            'is_counter_sale' => false,
                            'sold_by' => null,
                            'is_picked_up' => $isPickedUp,
                            'picked_up_at' => $isPickedUp ? $createdAt->copy()->addHours(6) : null,
                            'picked_up_by' => $isPickedUp ? $userId : null,
                            'created_at' => $createdAt,
                        ];
                    }

                    DB::table('tickets')->insert($tickets);
                }
            }
        });

        $this->command?->info('Đã tạo 42 suất chiếu, đơn hoàn tất và vé test cho biểu đồ Lotte TP.HCM.');
    }
}
