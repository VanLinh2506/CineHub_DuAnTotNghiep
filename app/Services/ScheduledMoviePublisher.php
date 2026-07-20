<?php

namespace App\Services;

use App\Models\Movie;

class ScheduledMoviePublisher
{
    public function publishDue(): int
    {
        return Movie::query()
            ->where('type', 'phimle')
            ->where('status', 'Sắp chiếu')
            ->where('scheduled_status', 'Chiếu online')
            ->whereNotNull('publish_date')
            ->where('publish_date', '<=', now())
            ->update([
                'status' => 'Chiếu online',
                'scheduled_status' => null,
            ]);
    }
}
