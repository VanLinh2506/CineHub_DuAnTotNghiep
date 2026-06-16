<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\{Movie, Episode, User, Theater, FoodItem};

class MigrateStoragePaths extends Command
{
    protected $signature = 'storage:migrate-paths {--dry-run : Run without making changes}';
    protected $description = 'Migrate old data/ paths to new storage/ paths in database';

    protected $pathReplacements = [
        'data/img/posters/' => 'posters/',
        'data/img/banners/' => 'banners/',
        'data/img/avatars/' => 'avatars/',
        'data/img/theaters/' => 'theaters/',
        'data/img/food/' => 'food/',
        'data/phim/phimle/' => 'movies/phimle/',
        'data/phim/phimbo/' => 'movies/phimbo/',
        'data/phim/trailers/' => 'movies/trailers/',
        '../data/img/posters/' => 'posters/',
        '../data/img/banners/' => 'banners/',
        '../data/img/avatars/' => 'avatars/',
        '../data/img/theaters/' => 'theaters/',
        '../data/img/food/' => 'food/',
        '../data/phim/phimle/' => 'movies/phimle/',
        '../data/phim/phimbo/' => 'movies/phimbo/',
        '../data/phim/trailers/' => 'movies/trailers/',
    ];

    public function handle()
    {
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        } else {
            if (!$this->confirm('This will update paths in the database. Continue?')) {
                $this->error('Aborted!');
                return 1;
            }
        }

        $this->info('Starting path migration...');
        $this->newLine();

        // Migrate Movies
        $this->migrateMovies($dryRun);
        
        // Migrate Episodes
        $this->migrateEpisodes($dryRun);
        
        // Migrate Users
        $this->migrateUsers($dryRun);
        
        // Migrate Theaters
        $this->migrateTheaters($dryRun);
        
        // Migrate Food Items
        $this->migrateFoodItems($dryRun);

        $this->newLine();
        if ($dryRun) {
            $this->info('✅ Dry run completed. Run without --dry-run to apply changes.');
        } else {
            $this->info('✅ Migration completed successfully!');
        }

        return 0;
    }

    protected function migrateMovies($dryRun)
    {
        $this->info('📽️  Migrating Movies...');
        
        $movies = Movie::whereNotNull('thumbnail')
            ->orWhereNotNull('banner')
            ->orWhereNotNull('video_url')
            ->orWhereNotNull('trailer_url')
            ->get();

        $updated = 0;
        
        foreach ($movies as $movie) {
            $changes = [];
            
            if ($newPath = $this->convertPath($movie->thumbnail)) {
                $changes['thumbnail'] = $newPath;
            }
            
            if ($newPath = $this->convertPath($movie->banner)) {
                $changes['banner'] = $newPath;
            }
            
            if ($newPath = $this->convertPath($movie->video_url)) {
                $changes['video_url'] = $newPath;
            }
            
            if ($newPath = $this->convertPath($movie->trailer_url)) {
                $changes['trailer_url'] = $newPath;
            }

            if (!empty($changes)) {
                if (!$dryRun) {
                    $movie->update($changes);
                }
                $updated++;
                
                if ($dryRun) {
                    $this->line("  Would update Movie #{$movie->id}: " . implode(', ', array_keys($changes)));
                }
            }
        }
        
        $this->info("  ✓ Movies: {$updated} records " . ($dryRun ? 'would be ' : '') . "updated");
    }

    protected function migrateEpisodes($dryRun)
    {
        $this->info('📺 Migrating Episodes...');
        
        $episodes = Episode::whereNotNull('video_url')->get();
        $updated = 0;
        
        foreach ($episodes as $episode) {
            if ($newPath = $this->convertPath($episode->video_url)) {
                if (!$dryRun) {
                    $episode->update(['video_url' => $newPath]);
                }
                $updated++;
                
                if ($dryRun) {
                    $this->line("  Would update Episode #{$episode->id}");
                }
            }
        }
        
        $this->info("  ✓ Episodes: {$updated} records " . ($dryRun ? 'would be ' : '') . "updated");
    }

    protected function migrateUsers($dryRun)
    {
        $this->info('👤 Migrating Users...');
        
        $users = User::whereNotNull('avatar')->get();
        $updated = 0;
        
        foreach ($users as $user) {
            if ($newPath = $this->convertPath($user->avatar)) {
                if (!$dryRun) {
                    $user->update(['avatar' => $newPath]);
                }
                $updated++;
                
                if ($dryRun) {
                    $this->line("  Would update User #{$user->id}");
                }
            }
        }
        
        $this->info("  ✓ Users: {$updated} records " . ($dryRun ? 'would be ' : '') . "updated");
    }

    protected function migrateTheaters($dryRun)
    {
        $this->info('🎭 Migrating Theaters...');
        
        $theaters = Theater::whereNotNull('image')->get();
        $updated = 0;
        
        foreach ($theaters as $theater) {
            if ($newPath = $this->convertPath($theater->image)) {
                if (!$dryRun) {
                    $theater->update(['image' => $newPath]);
                }
                $updated++;
                
                if ($dryRun) {
                    $this->line("  Would update Theater #{$theater->id}");
                }
            }
        }
        
        $this->info("  ✓ Theaters: {$updated} records " . ($dryRun ? 'would be ' : '') . "updated");
    }

    protected function migrateFoodItems($dryRun)
    {
        $this->info('🍿 Migrating Food Items...');
        
        $foodItems = FoodItem::whereNotNull('image')->get();
        $updated = 0;
        
        foreach ($foodItems as $foodItem) {
            if ($newPath = $this->convertPath($foodItem->image)) {
                if (!$dryRun) {
                    $foodItem->update(['image' => $newPath]);
                }
                $updated++;
                
                if ($dryRun) {
                    $this->line("  Would update FoodItem #{$foodItem->id}");
                }
            }
        }
        
        $this->info("  ✓ Food Items: {$updated} records " . ($dryRun ? 'would be ' : '') . "updated");
    }

    protected function convertPath($path)
    {
        if (empty($path)) {
            return null;
        }

        // Skip if already http URL
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return null;
        }

        // Skip if already converted (doesn't contain 'data/')
        if (!str_contains($path, 'data/')) {
            return null;
        }

        $originalPath = $path;
        
        foreach ($this->pathReplacements as $old => $new) {
            if (str_starts_with($path, $old)) {
                $path = str_replace($old, $new, $path);
                return $path;
            }
        }
        
        return null;
    }
}
