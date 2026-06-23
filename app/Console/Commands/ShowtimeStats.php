<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Showtime;
use App\Models\Theater;
use Carbon\Carbon;

class ShowtimeStats extends Command
{
    protected $signature = 'showtimes:stats {--days=7 : Number of days to show stats}';
    protected $description = 'Display showtime statistics';

    public function handle()
    {
        $days = $this->option('days');
        $startDate = Carbon::today();
        $endDate = $startDate->copy()->addDays($days - 1);

        $this->info("Showtime Statistics");
        $this->info("Date range: {$startDate->toDateString()} to {$endDate->toDateString()}");
        $this->newLine();

        // Total showtimes
        $total = Showtime::where('show_date', '>=', $startDate->toDateString())
            ->where('show_date', '<=', $endDate->toDateString())
            ->count();

        $this->info("Total showtimes: {$total}");
        $this->newLine();

        // By date
        $this->info("Showtimes by date:");
        $byDate = Showtime::where('show_date', '>=', $startDate->toDateString())
            ->where('show_date', '<=', $endDate->toDateString())
            ->selectRaw('show_date, count(*) as count')
            ->groupBy('show_date')
            ->orderBy('show_date')
            ->get();

        $this->table(['Date', 'Showtimes'], $byDate->map(function($item) {
            return [$item->show_date, $item->count];
        }));

        // By theater
        $this->newLine();
        $this->info("Showtimes by theater:");
        $byTheater = Showtime::where('show_date', '>=', $startDate->toDateString())
            ->where('show_date', '<=', $endDate->toDateString())
            ->join('theaters', 'showtimes.theater_id', '=', 'theaters.id')
            ->selectRaw('theaters.name, count(*) as count')
            ->groupBy('theaters.id', 'theaters.name')
            ->orderBy('count', 'desc')
            ->get();

        $this->table(['Theater', 'Showtimes'], $byTheater->map(function($item) {
            return [$item->name, $item->count];
        }));

        // Price range
        $this->newLine();
        $priceStats = Showtime::where('show_date', '>=', $startDate->toDateString())
            ->where('show_date', '<=', $endDate->toDateString())
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price, AVG(price) as avg_price')
            ->first();

        $this->info("Price statistics:");
        $this->line("  Min: " . number_format($priceStats->min_price) . " VND");
        $this->line("  Max: " . number_format($priceStats->max_price) . " VND");
        $this->line("  Avg: " . number_format($priceStats->avg_price) . " VND");

        return 0;
    }
}
