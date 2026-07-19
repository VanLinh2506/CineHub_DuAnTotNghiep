<?php

namespace App\Console\Commands;

use App\Models\Movie;
use Illuminate\Console\Command;

class UpdateMovieStatus extends Command
{
    protected $signature = 'movies:update-status';
    protected $description = 'Publish scheduled online movies when their start date arrives';

    public function handle(): int
    {
        $updated = Movie::where('type', 'phimle')
            ->where('status', 'Sắp chiếu')
            ->where('scheduled_status', 'Chiếu online')
            ->whereNotNull('publish_date')
            ->where('publish_date', '<=', now())
            ->update([
                'status' => 'Chiếu online',
                'scheduled_status' => null,
            ]);

        $this->info("Published {$updated} scheduled online movie(s).");

        return self::SUCCESS;
    }
}
