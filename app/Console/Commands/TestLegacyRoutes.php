<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestLegacyRoutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:legacy-routes {--host=http://127.0.0.1:8000}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test legacy PHP routes redirect to new Laravel routes';

    /**
     * Sample legacy routes to test
     */
    private array $testRoutes = [
        // [legacy_url, expected_redirect_location]
        ['index.php?route=home/index', '/'],
        ['index.php?route=movie/index', '/movies'],
        ['index.php?route=movie/detail&id=5', '/movies/5'],
        ['index.php?route=movie/watch&id=10', '/movies/10/watch'],
        ['index.php?route=movie/theater', '/movies/theater'],
        ['index.php?route=movie/phimle', '/movies/phim-le'],
        ['index.php?route=movie/phimbo', '/movies/phim-bo'],
        ['index.php?route=auth/login', '/login'],
        ['index.php?route=auth/register', '/register'],
        ['index.php?route=booking/selectSeats&showtimeId=20', '/booking/showtime/20'],
        ['index.php?route=booking/history', '/booking/history'],
        ['index.php?route=profile/index', '/profile'],
        ['index.php?route=profile/bookingHistory', '/profile/bookings'],
        ['index.php?route=admin/dashboard', '/admin'],
        ['index.php?route=admin/movies', '/admin/movies'],
        ['index.php?route=moderator/showtimes', '/moderator/showtimes'],
        ['index.php?route=counterStaff/scanQR', '/counter/scan'],
        ['index.php?route=news/index', '/news'],
    ];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $host = $this->option('host');
        $this->info("🧪 Testing legacy routes redirect...");
        $this->info("🌐 Host: {$host}");
        $this->newLine();

        $passed = 0;
        $failed = 0;

        foreach ($this->testRoutes as [$legacyUrl, $expectedLocation]) {
            $fullUrl = "{$host}/{$legacyUrl}";
            
            try {
                // Send request without following redirects
                $response = Http::withOptions([
                    'allow_redirects' => false,
                ])->get($fullUrl);

                $status = $response->status();
                $location = $response->header('Location');

                // Normalize location (remove host if present)
                if ($location) {
                    $location = parse_url($location, PHP_URL_PATH) ?: $location;
                    if ($query = parse_url($location, PHP_URL_QUERY)) {
                        $location .= '?' . $query;
                    }
                }

                // Check if it's a 301 redirect to expected location
                if ($status === 301 && $location === $expectedLocation) {
                    $this->components->info("✓ {$legacyUrl}");
                    $this->line("  → {$location}");
                    $passed++;
                } else {
                    $this->components->error("✗ {$legacyUrl}");
                    $this->line("  Expected: {$expectedLocation}");
                    $this->line("  Got: {$location} (Status: {$status})");
                    $failed++;
                }
            } catch (\Exception $e) {
                $this->components->error("✗ {$legacyUrl}");
                $this->line("  Error: " . $e->getMessage());
                $failed++;
            }

            $this->newLine();
        }

        // Summary
        $total = $passed + $failed;
        $this->newLine();
        $this->info("📊 Results:");
        $this->line("  Total: {$total}");
        $this->line("  Passed: {$passed} ✓");
        $this->line("  Failed: {$failed} ✗");
        $this->newLine();

        if ($failed === 0) {
            $this->components->success('🎉 All legacy routes redirect correctly!');
            return self::SUCCESS;
        } else {
            $this->components->error('❌ Some routes failed. Check the output above.');
            return self::FAILURE;
        }
    }
}
