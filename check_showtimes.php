<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\Showtime;
use Illuminate\Support\Facades\DB;

echo "=== KIỂM TRA SHOWTIMES THEO PHIM ===\n\n";

$showtimesByMovie = Showtime::select(
    'movie_id',
    DB::raw('COUNT(*) as count'),
    DB::raw('MIN(CONCAT(show_date, " ", show_time)) as first_showtime'),
    DB::raw('MAX(CONCAT(show_date, " ", show_time)) as last_showtime')
)
->groupBy('movie_id')
->orderBy('movie_id')
->get();

$now = now()->format('Y-m-d H:i:s');
echo "Thời điểm hiện tại: $now\n\n";

foreach ($showtimesByMovie as $stat) {
    echo "Movie ID {$stat->movie_id}:\n";
    echo "  Tổng: {$stat->count} showtimes\n";
    echo "  Từ: {$stat->first_showtime}\n";
    echo "  Đến: {$stat->last_showtime}\n";
    
    $futureCount = Showtime::where('movie_id', $stat->movie_id)
        ->whereRaw("CONCAT(show_date, ' ', show_time) >= ?", [$now])
        ->count();
    echo "  Showtimes trong tương lai: $futureCount\n\n";
}

echo "\n=== TẠO THÊM SHOWTIMES MỚI ===\n";
echo "Bạn cần thêm showtimes mới cho các phim khác để hiển thị trong trang booking.\n";
echo "Ví dụ: Tạo showtimes cho các ngày tới (hôm nay + 7 ngày)\n";
