<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Movie;
use App\Models\Showtime;
use Illuminate\Support\Facades\DB;

echo "=== KIỂM TRA PHIM VÀ SUẤT CHIẾU ===\n\n";

echo "1. Tổng số phim: " . Movie::count() . "\n";
echo "2. Phim có status 'Chiếu rạp': " . Movie::where('status', 'Chiếu rạp')->count() . "\n";
echo "3. Tổng số showtimes: " . Showtime::count() . "\n";

$now = now()->format('Y-m-d H:i:s');
echo "4. Thời điểm hiện tại: $now\n";

$futureShowtimes = Showtime::whereRaw("CONCAT(show_date, ' ', show_time) >= ?", [$now])->count();
echo "5. Showtimes còn hợp lệ (trong tương lai): $futureShowtimes\n";

$moviesWithFutureShowtimes = Movie::whereHas('showtimes', function($query) use ($now) {
    $query->whereRaw("CONCAT(show_date, ' ', show_time) >= ?", [$now]);
})->count();
echo "6. Phim có suất chiếu trong tương lai: $moviesWithFutureShowtimes\n\n";

// Liệt kê các phim có suất chiếu
echo "=== DANH SÁCH PHIM CÓ SUẤT CHIẾU TRONG TƯƠNG LAI ===\n";
$movies = Movie::whereHas('showtimes', function($query) use ($now) {
    $query->whereRaw("CONCAT(show_date, ' ', show_time) >= ?", [$now]);
})
->with(['showtimes' => function($q) use ($now) {
    $q->whereRaw("CONCAT(show_date, ' ', show_time) >= ?", [$now])
      ->orderBy('show_date')
      ->orderBy('show_time')
      ->limit(3);
}])
->limit(10)
->get();

foreach ($movies as $movie) {
    echo "\nPhim: {$movie->title} (ID: {$movie->id})\n";
    echo "  Status: {$movie->status}\n";
    echo "  Số suất chiếu còn lại: " . $movie->showtimes->count() . "\n";
    if ($movie->showtimes->count() > 0) {
        echo "  Suất chiếu gần nhất:\n";
        foreach ($movie->showtimes as $showtime) {
            echo "    - {$showtime->show_date} {$showtime->show_time}\n";
        }
    }
}

echo "\n=== DONE ===\n";
