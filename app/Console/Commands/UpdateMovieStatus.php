<?php

namespace App\Console\Commands;

use App\Services\ScheduledMoviePublisher;
use Illuminate\Console\Command;

class UpdateMovieStatus extends Command
{
    protected $signature = 'movies:update-status';
    protected $description = 'Publish scheduled online movies when their start date arrives';

    public function handle(ScheduledMoviePublisher $publisher): int
    {
        $updated = $publisher->publishDue();

        $this->info("Published {$updated} scheduled online movie(s).");

        return self::SUCCESS;
    }
}
