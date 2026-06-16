<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpdateViewRoutesAdvanced extends Command
{
    protected $signature = 'views:update-routes-advanced {--dry-run : Preview changes without applying them}';
    protected $description = 'Update complex route patterns in views (with parameters)';

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $viewsPath = resource_path('views');
        
        if ($isDryRun) {
            $this->warn('🔍 DRY RUN MODE - No files will be modified');
            $this->newLine();
        }

        $this->info('🔄 Updating complex route patterns...');
        $this->newLine();

        $files = $this->getBladeFiles($viewsPath);
        $totalFiles = count($files);
        $modifiedFiles = 0;
        $totalReplacements = 0;

        foreach ($files as $file) {
            $content = File::get($file);
            $originalContent = $content;
            $fileReplacements = 0;

            // Pattern 1: movie/index with type parameter
            $content = preg_replace_callback(
                '/url\([\'"]\/\?route=movie\/index&type=(phimle|phimbo)[\'"]  \)/',
                function ($matches) use (&$fileReplacements) {
                    $fileReplacements++;
                    return "route('movies.{$matches[1]}')";
                },
                $content
            );

            // Pattern 2: movie/index with category parameter
            $content = preg_replace_callback(
                '/url\([\'"]\/\?route=movie\/index&category=[\'"]\s*\.\s*\$?(\w+(?:\[[\'"]\w+[\'"]\])?(?:->id)?)\s*\.\s*[\'"][\'"]\)/',
                function ($matches) use (&$fileReplacements) {
                    $fileReplacements++;
                    $var = $matches[1];
                    // Handle $cat->id, $cat['id'], $category->id, etc.
                    if (str_contains($var, '->id') || str_contains($var, "['id']") || str_contains($var, '["id"]')) {
                        $var = preg_replace('/(->id|\[[\'"]id[\'"]\])/', '', $var);
                        return "route('movies.category', {$var}->id)";
                    }
                    return "route('movies.category', {$var})";
                },
                $content
            );

            // Pattern 3: movie/index with country parameter
            $content = preg_replace_callback(
                '/url\([\'"]\/\?route=movie\/index&country=[\'"]\s*\.\s*urlencode\(([^)]+)\)\s*\.\s*[\'"][\'"]\)/',
                function ($matches) use (&$fileReplacements) {
                    $fileReplacements++;
                    $var = trim($matches[1]);
                    return "route('movies.index', ['country' => {$var}])";
                },
                $content
            );

            // Pattern 4: movie/detail with id
            $content = preg_replace_callback(
                '/url\([\'"]\/\?route=movie\/detail&id=[\'"]\s*\.\s*\$?(\w+(?:->id)?)\s*\.\s*[\'"][\'"]\)/',
                function ($matches) use (&$fileReplacements) {
                    $fileReplacements++;
                    $var = $matches[1];
                    return "route('movies.show', {$var})";
                },
                $content
            );

            // Pattern 5: booking/selectSeats with showtimeId
            $content = preg_replace_callback(
                '/url\([\'"]\/\?route=booking\/selectSeats&showtimeId=[\'"]\s*\.\s*\$?(\w+(?:->id)?)\s*\.\s*[\'"][\'"]\)/',
                function ($matches) use (&$fileReplacements) {
                    $fileReplacements++;
                    $var = $matches[1];
                    return "route('booking.selectSeats', {$var})";
                },
                $content
            );

            // Pattern 6: Simple form actions with POST
            $patterns = [
                "url('/?route=profile/update')" => "route('profile.update')",
                'url("/?route=profile/update")' => 'route("profile.update")',
                "url('/?route=profile/changePassword')" => "route('profile.updatePassword')",
                'url("/?route=profile/changePassword")' => 'route("profile.updatePassword")',
                "url('/?route=booking/confirmPayment')" => "route('booking.create')",
                'url("/?route=booking/confirmPayment")' => 'route("booking.create")',
            ];

            foreach ($patterns as $search => $replace) {
                if (str_contains($content, $search)) {
                    $content = str_replace($search, $replace, $content);
                    $fileReplacements++;
                }
            }

            if ($content !== $originalContent) {
                if (!$isDryRun) {
                    File::put($file, $content);
                }
                $modifiedFiles++;
                $totalReplacements += $fileReplacements;
                
                $relativePath = str_replace($viewsPath . DIRECTORY_SEPARATOR, '', $file);
                $this->line("  ✓ {$relativePath} ({$fileReplacements} replacements)");
            }
        }

        $this->newLine();

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
            $this->components->success('✅ Complex patterns updated successfully!');
        } else {
            $this->components->info('ℹ️  No complex patterns found.');
        }

        return self::SUCCESS;
    }

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
