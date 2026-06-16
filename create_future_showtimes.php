<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Movie;
use App\Models\Theater;
use App\Models\Screen;
use App\Models\Showtime;
use Carbon\Carbon;

echo "=== TẠO SHOWTIMES MỚI CHO CÁC PHIM ===\n\n";

// Lấy tất cả phim có status "Chiếu rạp" hoặc phim có showtimes cũ
$movies = Movie::where('status', 'Chiếu rạp')
    ->orWhereHas('showtimes')
    ->limit(15) // Giới hạn 15 phim đầu tiên
    ->get();

echo "Tìm thấy " . $movies->count() . " phim cần tạo showtimes\n\n";

// Lấy tất cả theaters
$theaters = Theater::all();
if ($theaters->count() == 0) {
    die("Không có theater nào trong database!\n");
}

echo "Tìm thấy " . $theaters->count() . " theaters\n\n";

// Các khung giờ chiếu phổ biến
$showtimes = ['09:00:00', '12:00:00', '15:00:00', '18:00:00', '21:00:00'];

$createdCount = 0;

foreach ($movies as $movie) {
    echo "Đang tạo showtimes cho: {$movie->title} (ID: {$movie->id})\n";
    
    // Tạo showtimes cho 7 ngày tới
    for ($day = 0; $day < 7; $day++) {
        $date = Carbon::now()->addDays($day)->format('Y-m-d');
        
        // Chỉ tạo cho 2 theater đầu tiên để tránh quá nhiều
        foreach ($theaters->take(2) as $theater) {
            // Lấy screens của theater này
            $screens = Screen::where('theater_id', $theater->id)->get();
            
            if ($screens->count() == 0) {
                continue;
            }
            
            // Dùng screen khác nhau để tránh conflict
            foreach ($screens->take(2) as $screen) {
                // Tạo 2 suất chiếu mỗi ngày cho mỗi screen
                foreach (array_rand(array_flip($showtimes), min(2, count($showtimes))) as $time) {
                    // Kiểm tra xem screen có trống không (không có phim nào chiếu lúc đó)
                    $occupied = Showtime::where('screen_id', $screen->id)
                        ->where('show_date', $date)
                        ->where('show_time', $time)
                        ->exists();
                    
                    if (!$occupied) {
                        try {
                            Showtime::create([
                                'movie_id' => $movie->id,
                                'theater_id' => $theater->id,
                                'screen_id' => $screen->id,
                                'show_date' => $date,
                                'show_time' => $time,
                                'price' => 90000 // Giá mặc định
                            ]);
                            
                            $createdCount++;
                        } catch (\Exception $e) {
                            // Bỏ qua lỗi duplicate
                        }
                    }
                }
            }
        }
    }
    
    echo "  ✓ Đã tạo showtimes cho {$movie->title}\n";
}

echo "\n=== HOÀN TẤT ===\n";
echo "Đã tạo tổng cộng: $createdCount showtimes mới\n";
echo "\nBây giờ trang booking sẽ hiển thị nhiều phim hơn!\n";
