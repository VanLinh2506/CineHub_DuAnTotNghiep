<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Movie;

class PrepareMoviesForShowtime extends Command
{
    protected $signature = 'movies:prepare-showtime {--all : Set all movies to showing status}';
    protected $description = 'Prepare movies by setting their status to showing';

    public function handle()
    {
        $this->info('Checking movie status distribution...');
        
        $statusCounts = Movie::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get();

        $this->table(['Status', 'Count'], $statusCounts->map(function($item) {
            return [$item->status ?? 'NULL', $item->count];
        }));

        if ($this->option('all')) {
            $this->info('Setting all movies to "showing" status...');
            
            $updated = Movie::whereIn('status', ['coming_soon', 'completed', null])
                ->update(['status' => 'showing']);

            $this->info("Updated {$updated} movies to showing status");
        } else {
            $this->info('Setting only active movies to "showing" status...');
            
            // Chỉ cập nhật các phim có release_date trong quá khứ hoặc null
            $updated = Movie::where(function($query) {
                $query->where('release_date', '<=', now())
                      ->orWhereNull('release_date');
            })
            ->whereIn('status', ['coming_soon', 'completed', null])
            ->update(['status' => 'showing']);

            $this->info("Updated {$updated} movies to showing status");
        }

        $this->newLine();
        $this->info('Updated status distribution:');
        
        $newStatusCounts = Movie::selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get();

        $this->table(['Status', 'Count'], $newStatusCounts->map(function($item) {
            return [$item->status ?? 'NULL', $item->count];
        }));

        $totalShowing = Movie::where('status', 'showing')->count();
        $this->info("✓ Total movies ready for showtimes: {$totalShowing}");

        return 0;
    }
}
