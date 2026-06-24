<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Showtime;
use Carbon\Carbon;

class ClearShowtimes extends Command
{
    protected $signature = 'showtimes:clear {--all : Clear all showtimes} {--future : Clear future showtimes only}';
    protected $description = 'Clear showtimes from database';

    public function handle()
    {
        if ($this->option('all')) {
            if ($this->confirm('Are you sure you want to delete ALL showtimes?')) {
                $count = Showtime::count();
                Showtime::truncate();
                $this->info("✓ Deleted {$count} showtimes");
            }
        } elseif ($this->option('future')) {
            if ($this->confirm('Are you sure you want to delete all FUTURE showtimes?')) {
                $count = Showtime::where('show_date', '>=', Carbon::today()->toDateString())->count();
                Showtime::where('show_date', '>=', Carbon::today()->toDateString())->delete();
                $this->info("✓ Deleted {$count} future showtimes");
            }
        } else {
            $this->error('Please specify --all or --future option');
            return 1;
        }

        return 0;
    }
}
