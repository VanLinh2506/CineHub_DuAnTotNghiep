<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpdateViewRoutes extends Command
{
    protected $signature = 'views:update-routes {--dry-run : Preview changes without applying them}';
    protected $description = 'Update all views to use Laravel named routes instead of legacy ?route= URLs';

    /**
     * Route replacement mapping
     * Pattern => Replacement
     */
    private array $replacements = [
        // HOME & SEARCH
        "url('/')" => "route('home')",
        'url("/")' => 'route("home")',
        "url('/?route=home/index')" => "route('home')",
        'url("/?route=home/index")' => 'route("home")',
        
        // MOVIES
        "url('/?route=movie/index')" => "route('movies.index')",
        'url("/?route=movie/index")' => 'route("movies.index")',
        "url('/?route=movie/theater')" => "route('movies.theater')",
        'url("/?route=movie/theater")' => 'route("movies.theater")',
        "url('/?route=movie/online')" => "route('movies.online')",
        'url("/?route=movie/online")' => 'route("movies.online")',
        "url('/?route=movie/phimle')" => "route('movies.phimle')",
        'url("/?route=movie/phimle")' => 'route("movies.phimle")',
        "url('/?route=movie/phimbo')" => "route('movies.phimbo')",
        'url("/?route=movie/phimbo")' => 'route("movies.phimbo")',
        
        // AUTH
        "url('/?route=auth/login')" => "route('login')",
        'url("/?route=auth/login")' => 'route("login")',
        "url('/?route=auth/register')" => "route('register')",
        'url("/?route=auth/register")' => 'route("register")',
        "url('/?route=auth/logout')" => "route('logout')",
        'url("/?route=auth/logout")' => 'route("logout")',
        "url('/?route=auth/forgotPassword')" => "route('password.request')",
        'url("/?route=auth/forgotPassword")' => 'route("password.request")',
        
        // BOOKING
        "url('/?route=booking/index')" => "route('movies.theater')",
        'url("/?route=booking/index")' => 'route("movies.theater")',
        "url('/?route=booking/history')" => "route('booking.history')",
        'url("/?route=booking/history")' => 'route("booking.history")',
        "url('/?route=booking/myTickets')" => "route('booking.history')",
        'url("/?route=booking/myTickets")' => 'route("booking.history")',
        
        // PROFILE
        "url('/?route=profile/index')" => "route('profile.index')",
        'url("/?route=profile/index")' => 'route("profile.index")',
        "url('/?route=profile/bookingHistory')" => "route('profile.bookings')",
        'url("/?route=profile/bookingHistory")' => 'route("profile.bookings")',
        "url('/?route=profile/watchHistory')" => "route('profile.watchHistory')",
        'url("/?route=profile/watchHistory")' => 'route("profile.watchHistory")',
        
        // NOTIFICATIONS
        "url('/?route=notifications/index')" => "route('notifications.index')",
        'url("/?route=notifications/index")' => 'route("notifications.index")',
        
        // ADMIN
        "url('/?route=admin/index')" => "route('admin.index')",
        'url("/?route=admin/index")' => 'route("admin.index")',
        "url('/?route=admin/dashboard')" => "route('admin.index')",
        'url("/?route=admin/dashboard")' => 'route("admin.index")',
        "url('/?route=admin/analytics')" => "route('admin.analytics')",
        'url("/?route=admin/analytics")' => 'route("admin.analytics")',
        "url('/?route=admin/users')" => "route('admin.users.index')",
        'url("/?route=admin/users")' => 'route("admin.users.index")',
        "url('/?route=admin/movies')" => "route('admin.movies.index')",
        'url("/?route=admin/movies")' => 'route("admin.movies.index")',
        "url('/?route=admin/categories')" => "route('admin.categories.index')",
        'url("/?route=admin/categories")' => 'route("admin.categories.index")',
        "url('/?route=admin/theaters')" => "route('admin.theaters.index')",
        'url("/?route=admin/theaters")' => 'route("admin.theaters.index")',
        "url('/?route=admin/tickets')" => "route('admin.tickets.index')",
        'url("/?route=admin/tickets")' => 'route("admin.tickets.index")',
        "url('/?route=admin/logs')" => "route('admin.logs')",
        'url("/?route=admin/logs")' => 'route("admin.logs")',
        
        // MODERATOR
        "url('/?route=moderator/index')" => "route('moderator.index')",
        'url("/?route=moderator/index")' => 'route("moderator.index")',
        "url('/?route=moderator/dashboard')" => "route('moderator.index')",
        'url("/?route=moderator/dashboard")' => 'route("moderator.index")',
        "url('/?route=moderator/showtimes')" => "route('moderator.showtimes.index')",
        'url("/?route=moderator/showtimes")' => 'route("moderator.showtimes.index")',
        "url('/?route=moderator/screens')" => "route('moderator.screens.index')",
        'url("/?route=moderator/screens")' => 'route("moderator.screens.index")',
        "url('/?route=moderator/tickets')" => "route('moderator.tickets')",
        'url("/?route=moderator/tickets")' => 'route("moderator.tickets")',
        "url('/?route=moderator/revenue')" => "route('moderator.revenue')",
        'url("/?route=moderator/revenue")' => 'route("moderator.revenue")',
        
        // COUNTER STAFF
        "url('/?route=counterStaff/scanQR')" => "route('counter.scan')",
        'url("/?route=counterStaff/scanQR")' => 'route("counter.scan")',
        "url('/?route=counterStaff/index')" => "route('counter.index')",
        'url("/?route=counterStaff/index")' => 'route("counter.index")',
        
        // NEWS
        "url('/?route=news/index')" => "route('news.index')",
        'url("/?route=news/index")' => 'route("news.index")',
    ];

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $viewsPath = resource_path('views');
        
        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - No files will be modified');
            $this->newLine();
        }

        $this->info('🔄 Updating view routes...');
        $this->newLine();

        $files = $this->getBladeFiles($viewsPath);
        $totalFiles = count($files);
        $modifiedFiles = 0;
        $totalReplacements = 0;

        $progressBar = $this->output->createProgressBar($totalFiles);
        $progressBar->start();

        foreach ($files as $file) {
            $content = File::get($file);
            $originalContent = $content;
            $fileReplacements = 0;

            foreach ($this->replacements as $search => $replace) {
                $count = 0;
                $content = str_replace($search, $replace, $content, $count);
                $fileReplacements += $count;
            }

            if ($content !== $originalContent) {
                if (!$isDryRun) {
                    File::put($file, $content);
                }
                $modifiedFiles++;
                $totalReplacements += $fileReplacements;
                
                $relativePath = str_replace($viewsPath . DIRECTORY_SEPARATOR, '', $file);
                $this->newLine();
                $this->line("  ✓ {$relativePath} ({$fileReplacements} replacements)");
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        // Summary
        $this->info('📊 Summary:');
        $this->line("  Total files scanned: {$totalFiles}");
        $this->line("  Files modified: {$modifiedFiles}");
        $this->line("  Total replacements: {$totalReplacements}");
        $this->newLine();

        if ($isDryRun) {
            $this->warn('⚠️  This was a DRY RUN. Run without --dry-run to apply changes.');
            return self::SUCCESS;
        }

        if ($modifiedFiles > 0) {
            $this->components->success('✅ Views updated successfully!');
            $this->newLine();
            $this->info('Next steps:');
            $this->line('  1. Clear view cache: php artisan view:clear');
            $this->line('  2. Test the application');
            $this->line('  3. Check git diff to review changes');
        } else {
            $this->components->info('ℹ️  No views needed updating.');
        }

        return self::SUCCESS;
    }

    /**
     * Get all Blade files recursively
     */
    private function getBladeFiles(string $directory): array
    {
        $files = [];
        
        foreach (File::allFiles($directory) as $file) {
            if ($file->getExtension() === 'php' && str_ends_with($file->getFilename(), '.blade.php')) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }
}
