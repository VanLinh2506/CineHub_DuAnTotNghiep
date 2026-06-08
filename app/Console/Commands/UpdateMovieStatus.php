<?php

namespace App\Console\Commands;

use App\Models\Movie;
use Illuminate\Console\Command;

class UpdateMovieStatus extends Command
{
    protected $signature = 'movies:update-status';
    protected $description = 'Update movie status based on release date and showtimes';

    public function handle()
    {
        $updated = 0;
        
        // Update movies that should be showing now
        $moviesStarting = Movie::where('status', 'Sắp chiếu')
            ->where('release_date', '<=', now())
            ->get();
            
        foreach ($moviesStarting as $movie) {
            $movie->update(['status' => 'Đang chiếu']);
            $updated++;
        }
        
        // Update movies that have passed (no more showtimes)
        $moviesEnding = Movie::where('status', 'Đang chiếu')
            ->whereDoesntHave('showtimes', function($query) {
                $query->where('show_date', '>=', now()->toDateString());
            })
            ->get();
            
        foreach ($moviesEnding as $movie) {
            $movie->update(['status' => 'Đã chiếu']);
            $updated++;
        }
        
        $this->info("Updated {$updated} movie status(es).");
        
        return 0;
    }
}
