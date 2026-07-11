<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminFeatureTestSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $categoryId = DB::table('categories')->orderBy('id')->value('id');
            $sourceMovie = DB::table('movies')
                ->where('status', 'Chiếu online')
                ->orderByDesc('id')
                ->first();

            for ($day = 1; $day <= 7; $day++) {
                $publishAt = now()->addDays($day)->setTime(19, 30);
                $title = sprintf('[ADMIN TEST] Online ngày +%d', $day);

                DB::table('movies')->updateOrInsert(
                    ['title' => $title],
                    [
                        'category_id' => $categoryId,
                        'level' => ['Free', 'Silver', 'Gold', 'Premium'][($day - 1) % 4],
                        'duration' => 95 + ($day * 3),
                        'description' => 'Dữ liệu phim online dùng kiểm thử lịch phát hành và quản trị nội dung.',
                        'director' => 'CineHub Test Studio',
                        'actors' => 'Diễn viên kiểm thử A, Diễn viên kiểm thử B',
                        'video_url' => $sourceMovie->video_url ?? null,
                        'trailer_url' => $sourceMovie->trailer_url ?? null,
                        'thumbnail' => $sourceMovie->thumbnail ?? null,
                        'banner' => $sourceMovie->banner ?? null,
                        'status' => 'Chiếu online',
                        'rating' => 7.0 + ($day / 10),
                        'status_admin' => 'published',
                        'publish_date' => $publishAt,
                        'geo_restriction' => null,
                        'drm_enabled' => $day % 2,
                        'country' => 'Việt Nam',
                        'language' => 'Tiếng Việt',
                        'age_rating' => $day % 3 === 0 ? 'T16' : 'T13',
                        'type' => $day % 3 === 0 ? 'phimbo' : 'phimle',
                        'max_tickets' => null,
                        'normal_price' => 90000,
                        'vip_price' => 120000,
                        'couple_price' => 180000,
                        'created_at' => now(),
                    ]
                );
            }

            $theaterId = DB::table('theaters')->orderBy('id')->value('id');
            $screen = DB::table('theater_screens')
                ->where('theater_id', $theaterId)
                ->orderBy('id')
                ->first();
            $userIds = DB::table('users')
                ->where('role', 'user')
                ->orderBy('id')
                ->limit(8)
                ->pluck('id')
                ->all();

            if (!$theaterId || !$screen || empty($userIds)) {
                throw new \RuntimeException('Cần ít nhất một rạp, một phòng chiếu và một user để tạo booking test.');
            }

            // Movies shown on /movies/theater are selected by upcoming
            // showtimes, so create one theater movie and showtime per day.
            for ($day = 1; $day <= 7; $day++) {
                $title = sprintf('[ADMIN TEST] Phim rạp ngày +%d', $day);
                $showDate = now()->addDays($day)->toDateString();
                $showTime = sprintf('%02d:30:00', 10 + $day);

                DB::table('movies')->updateOrInsert(
                    ['title' => $title],
                    [
                        'category_id' => $categoryId,
                        'level' => 'Free',
                        'duration' => 100 + ($day * 4),
                        'description' => 'Phim rạp kiểm thử suất chiếu trong 7 ngày tới.',
                        'director' => 'CineHub Test Studio',
                        'actors' => 'Test Cast A, Test Cast B',
                        'thumbnail' => $sourceMovie->thumbnail ?? null,
                        'banner' => $sourceMovie->banner ?? null,
                        'status' => 'Chiếu rạp',
                        'rating' => 7.2 + ($day / 10),
                        'status_admin' => 'published',
                        'publish_date' => now(),
                        'country' => 'Việt Nam',
                        'language' => 'Tiếng Việt',
                        'age_rating' => 'T13',
                        'type' => 'phimle',
                        'normal_price' => 90000,
                        'vip_price' => 120000,
                        'couple_price' => 180000,
                        'created_at' => now(),
                    ]
                );

                $movieId = DB::table('movies')->where('title', $title)->value('id');

                DB::table('showtimes')->updateOrInsert(
                    [
                        'screen_id' => $screen->id,
                        'show_date' => $showDate,
                        'show_time' => $showTime,
                    ],
                    [
                        'movie_id' => $movieId,
                        'theater_id' => $theaterId,
                        'price' => 90000 + ($day * 5000),
                        'available_seats' => 120,
                        'created_at' => now(),
                    ]
                );
            }

            // The user wants the original catalog on the theater page, not
            // synthetic movie cards. Remove the temporary future movies and
            // schedule existing published theater movies instead.
            $temporaryMovieIds = DB::table('movies')
                ->where('title', 'like', '[ADMIN TEST] Online ngày%')
                ->orWhere('title', 'like', '[ADMIN TEST] Phim rạp ngày%')
                ->pluck('id');

            DB::table('showtimes')->whereIn('movie_id', $temporaryMovieIds)->delete();
            DB::table('movies')->whereIn('id', $temporaryMovieIds)->delete();

            $originalTheaterMovies = DB::table('movies')
                ->where('title', 'not like', '[ADMIN TEST]%')
                ->where('status', 'Chiếu rạp')
                ->where('status_admin', 'published')
                ->orderBy('id')
                ->limit(7)
                ->pluck('id')
                ->all();

            if (empty($originalTheaterMovies)) {
                throw new \RuntimeException('Không tìm thấy phim chiếu rạp cũ để tạo lịch chiếu.');
            }

            $screens = DB::table('theater_screens')->orderBy('id')->get()->values();
            $rangeStart = now()->startOfDay();
            $rangeEnd = now()->addDays(6)->endOfDay();

            // Replace only the generated upcoming schedules for these movies;
            // historical showtimes and bookings remain untouched.
            DB::table('showtimes')
                ->whereIn('movie_id', $originalTheaterMovies)
                ->whereBetween('show_date', [$rangeStart->toDateString(), $rangeEnd->toDateString()])
                ->delete();

            foreach ($originalTheaterMovies as $movieIndex => $movieId) {
                $targetScreen = $screens[$movieIndex % $screens->count()];

                foreach (range(0, 6) as $day) {
                    $showDate = now()->addDays($day)->toDateString();
                    $hour = $day === 0 ? 23 : 10 + ($movieIndex * 2);

                    DB::table('showtimes')->updateOrInsert(
                        [
                            'screen_id' => $targetScreen->id,
                            'show_date' => $showDate,
                            'show_time' => sprintf('%02d:00:00', $hour),
                        ],
                        [
                            'movie_id' => $movieId,
                            'theater_id' => $targetScreen->theater_id,
                            'price' => 90000 + (($movieIndex % 3) * 15000),
                            'available_seats' => 120,
                            'created_at' => now(),
                        ]
                    );
                }
            }

            DB::table('movies')->updateOrInsert(
                ['title' => '[ADMIN TEST] Phim rạp lịch sử'],
                [
                    'category_id' => $categoryId,
                    'level' => 'Free',
                    'duration' => 118,
                    'description' => 'Phim dùng tạo dữ liệu booking và doanh thu lịch sử cho admin.',
                    'director' => 'CineHub Test Studio',
                    'actors' => 'Test Cast',
                    'thumbnail' => $sourceMovie->thumbnail ?? null,
                    'banner' => $sourceMovie->banner ?? null,
                    'status' => 'Chiếu rạp',
                    'rating' => 8.1,
                    'status_admin' => 'published',
                    'publish_date' => now()->subMonths(2),
                    'country' => 'Việt Nam',
                    'language' => 'Tiếng Việt',
                    'age_rating' => 'T13',
                    'type' => 'phimle',
                    'normal_price' => 90000,
                    'vip_price' => 120000,
                    'couple_price' => 180000,
                    'created_at' => now()->subMonths(2),
                ]
            );

            $historyMovieId = DB::table('movies')
                ->where('title', '[ADMIN TEST] Phim rạp lịch sử')
                ->value('id');

            for ($index = 1; $index <= 30; $index++) {
                $createdAt = now()->subDays($index)->setTime(18, 15);
                $showDate = $createdAt->toDateString();
                $showTime = sprintf('%02d:00:00', 9 + ($index % 10));

                DB::table('showtimes')->updateOrInsert(
                    [
                        'screen_id' => $screen->id,
                        'show_date' => $showDate,
                        'show_time' => $showTime,
                    ],
                    [
                        'movie_id' => $historyMovieId,
                        'theater_id' => $theaterId,
                        'price' => 90000,
                        'available_seats' => 118,
                        'created_at' => $createdAt->copy()->subDays(5),
                    ]
                );

                $showtimeId = DB::table('showtimes')
                    ->where('screen_id', $screen->id)
                    ->where('show_date', $showDate)
                    ->where('show_time', $showTime)
                    ->value('id');
                $userId = $userIds[($index - 1) % count($userIds)];
                $seats = $index % 3 === 0 ? ['D1', 'D2'] : ['A1', 'A2'];
                $total = $index % 3 === 0 ? 240000 : 180000;
                $bookingCode = sprintf('ADMIN-TEST-%s-%02d', $createdAt->format('Ymd'), $index);

                DB::table('booking_pending')->updateOrInsert(
                    ['booking_code' => $bookingCode],
                    [
                        'user_id' => $userId,
                        'customer_name' => 'Khách kiểm thử '.(($index - 1) % count($userIds) + 1),
                        'customer_phone' => '090000'.str_pad((string) $index, 4, '0', STR_PAD_LEFT),
                        'showtime_id' => $showtimeId,
                        'seats' => json_encode($seats),
                        'food_items' => null,
                        'customer_email' => 'admin-test-'.$index.'@cinehub.local',
                        'total_amount' => $total,
                        'vnp_txn_ref' => 'ADMINTEST'.$createdAt->format('Ymd').str_pad((string) $index, 2, '0', STR_PAD_LEFT),
                        'status' => 'completed',
                        'created_at' => $createdAt,
                        'expires_at' => $createdAt->copy()->addMinutes(15),
                        'qr_code' => 'ADMIN-TEST-QR-'.$index,
                    ]
                );

                $bookingId = DB::table('booking_pending')->where('booking_code', $bookingCode)->value('id');

                foreach ($seats as $seat) {
                    DB::table('tickets')->updateOrInsert(
                        ['showtime_id' => $showtimeId, 'seat' => $seat],
                        [
                            'user_id' => $userId,
                            'booking_pending_id' => $bookingId,
                            'seat_type' => str_starts_with($seat, 'D') ? 'vip' : 'normal',
                            'qr_code' => $bookingCode.'-'.$seat,
                            'price' => str_starts_with($seat, 'D') ? 120000 : 90000,
                            'status' => 'Đã đặt',
                            'is_counter_sale' => $index % 4 === 0,
                            'is_picked_up' => true,
                            'picked_up_at' => $createdAt->copy()->addMinutes(20),
                            'created_at' => $createdAt,
                        ]
                    );
                }

                DB::table('transactions')
                    ->where('type', 'ticket')
                    ->where('related_id', $bookingId)
                    ->delete();

                DB::table('transactions')->insert([
                    'user_id' => $userId,
                    'type' => 'ticket',
                    'related_id' => $bookingId,
                    'amount' => $total,
                    'method' => $index % 2 === 0 ? 'Bank' : 'Cash',
                    'status' => 'Thành công',
                    'created_at' => $createdAt,
                ]);
            }
        });

        $this->command?->info('Đã lên lịch 7 phim cũ trong 7 ngày tới và tạo 30 booking lịch sử cho admin.');
    }
}
