<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Movie;
use App\Models\Theater;
use App\Models\Screen;
use App\Models\Showtime;
use Carbon\Carbon;

class ImportShowtimes extends Command
{
    protected $signature = 'import:showtimes {--days=7 : Number of days to generate showtimes}';
    protected $description = 'Import dense showtimes for all movies across all theaters for the next 7 days';

    // Các khung giờ chiếu phim trong ngày
    protected $timeSlots = [
        '08:00:00', '08:30:00', '09:00:00', '09:30:00',
        '10:00:00', '10:30:00', '11:00:00', '11:30:00',
        '12:00:00', '12:30:00', '13:00:00', '13:30:00',
        '14:00:00', '14:30:00', '15:00:00', '15:30:00',
        '16:00:00', '16:30:00', '17:00:00', '17:30:00',
        '18:00:00', '18:30:00', '19:00:00', '19:30:00',
        '20:00:00', '20:30:00', '21:00:00', '21:30:00',
        '22:00:00', '22:30:00', '23:00:00', '23:30:00',
    ];

    // Giá vé theo khung giờ
    protected function getPriceForTime($time)
    {
        $hour = (int) substr($time, 0, 2);
        
        // Giờ vàng (18:00 - 23:59): giá cao
        if ($hour >= 18) {
            return rand(80000, 120000);
        }
        // Giờ chiều (12:00 - 17:59): giá trung bình
        elseif ($hour >= 12) {
            return rand(60000, 90000);
        }
        // Giờ sáng (8:00 - 11:59): giá thấp
        else {
            return rand(45000, 70000);
        }
    }

    public function handle()
    {
        $days = $this->option('days');
        $this->info("Starting to import showtimes for the next {$days} days...");

        // Lấy tất cả phim đang chiếu (status = 'Chiếu rạp')
        $movies = Movie::where('status', 'Chiếu rạp')->get();
        
        if ($movies->isEmpty()) {
            $this->error('No movies with status "Chiếu rạp" found!');
            return 1;
        }

        $this->info("Found {$movies->count()} movies with status 'Chiếu rạp'");

        // Lấy tất cả rạp đang hoạt động
        $theaters = Theater::where('is_active', true)->with('screens')->get();
        
        if ($theaters->isEmpty()) {
            $this->error('No active theaters found!');
            return 1;
        }

        $this->info("Found {$theaters->count()} active theaters");

        $totalShowtimes = 0;
        $startDate = Carbon::today();

        // Tạo progress bar
        $totalOperations = $days * $theaters->count();
        $bar = $this->output->createProgressBar($totalOperations);
        $bar->start();

        // Lặp qua từng ngày
        for ($day = 0; $day < $days; $day++) {
            $currentDate = $startDate->copy()->addDays($day);
            $isWeekend = $currentDate->isWeekend();

            // Lặp qua từng rạp
            foreach ($theaters as $theater) {
                $screens = $theater->screens;
                
                if ($screens->isEmpty()) {
                    $bar->advance();
                    continue;
                }

                // Lặp qua từng phòng chiếu
                foreach ($screens as $screen) {
                    // Chọn ngẫu nhiên 3-5 phim cho mỗi phòng mỗi ngày
                    $numMovies = $isWeekend ? rand(4, 6) : rand(3, 5);
                    $selectedMovies = $movies->random(min($numMovies, $movies->count()));

                    $usedTimeSlots = [];

                    foreach ($selectedMovies as $movie) {
                        // Mỗi phim có 2-4 suất chiếu mỗi ngày
                        $numShowtimes = $isWeekend ? rand(3, 5) : rand(2, 4);

                        for ($i = 0; $i < $numShowtimes; $i++) {
                            // Chọn khung giờ ngẫu nhiên chưa được sử dụng
                            $availableSlots = array_diff($this->timeSlots, $usedTimeSlots);
                            
                            if (empty($availableSlots)) {
                                break; // Hết slot
                            }

                            $timeSlot = $availableSlots[array_rand($availableSlots)];
                            $usedTimeSlots[] = $timeSlot;

                            // Kiểm tra xem suất chiếu đã tồn tại chưa
                            $exists = Showtime::where('movie_id', $movie->id)
                                ->where('theater_id', $theater->id)
                                ->where('screen_id', $screen->id)
                                ->where('show_date', $currentDate->toDateString())
                                ->where('show_time', $timeSlot)
                                ->exists();

                            if (!$exists) {
                                Showtime::create([
                                    'movie_id' => $movie->id,
                                    'theater_id' => $theater->id,
                                    'screen_id' => $screen->id,
                                    'show_date' => $currentDate->toDateString(),
                                    'show_time' => $timeSlot,
                                    'price' => $this->getPriceForTime($timeSlot),
                                ]);

                                $totalShowtimes++;
                            }
                        }
                    }
                }

                $bar->advance();
            }
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✓ Successfully imported {$totalShowtimes} showtimes!");
        $this->info("Date range: {$startDate->toDateString()} to {$startDate->copy()->addDays($days - 1)->toDateString()}");

        return 0;
    }
}
