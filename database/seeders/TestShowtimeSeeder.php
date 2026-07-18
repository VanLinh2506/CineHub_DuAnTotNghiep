<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TestShowtimeSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $movies = DB::table('movies')
                ->whereIn('id', [1, 2, 3, 4, 5])
                ->orderBy('id')
                ->pluck('id')
                ->values();

            if ($movies->count() < 5) {
                $movies = DB::table('movies')
                    ->where('status_admin', 'published')
                    ->orderBy('id')
                    ->limit(5)
                    ->pluck('id')
                    ->values();
            }

            $screens = DB::table('theater_screens')
                ->where('is_active', 1)
                ->whereIn('id', [1, 2, 3, 9])
                ->orderBy('id')
                ->get()
                ->values();

            if ($movies->isEmpty() || $screens->isEmpty()) {
                throw new \RuntimeException('Need at least one movie and one active screen to create test showtimes.');
            }

            $slots = [
                [0, 0, 1, '09:30:00', 75000],
                [1, 1, 1, '13:00:00', 85000],
                [2, 2, 1, '18:30:00', 95000],
                [3, 3, 1, '21:15:00', 110000],
                [4, 0, 2, '10:00:00', 75000],
                [0, 1, 2, '15:30:00', 90000],
                [1, 2, 2, '19:45:00', 105000],
                [2, 3, 3, '20:30:00', 110000],
            ];

            foreach ($slots as [$movieIndex, $screenIndex, $dayOffset, $showTime, $price]) {
                $screen = $screens[$screenIndex % $screens->count()];
                $movieId = $movies[$movieIndex % $movies->count()];
                $showDate = now()->addDays($dayOffset)->toDateString();

                DB::table('showtimes')->updateOrInsert(
                    [
                        'screen_id' => $screen->id,
                        'show_date' => $showDate,
                        'show_time' => $showTime,
                    ],
                    [
                        'movie_id' => $movieId,
                        'theater_id' => $screen->theater_id,
                        'price' => $price,
                        'available_seats' => $screen->total_seats,
                        'contract_price_type' => null,
                        'created_at' => now(),
                    ]
                );
            }
        });

        $this->command?->info('Created 8 test showtimes for the next 3 days.');
    }
}
