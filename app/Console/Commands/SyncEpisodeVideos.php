<?php

namespace App\Console\Commands;

use App\Models\Movie;
use App\Models\Episode;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SyncEpisodeVideos extends Command
{
    protected $signature = 'episodes:sync-videos {movie_id?} {--folder=} {--list}';
    protected $description = 'Quét folder phim bộ và sync video URLs cho episodes';

    public function handle()
    {
        // Scan videos from storage/app/public only.
        $storagePath = storage_path('app/public/data/phim/phimbo');
        
        // Kiểm tra xem folder nào có videos
        $baseVideoPath = null;
        $urlPrefix = null;
        
        if (File::isDirectory($storagePath) && !empty(File::directories($storagePath))) {
            $baseVideoPath = $storagePath;
            $urlPrefix = 'data/phim/phimbo'; // Sẽ được truy cập qua /storage/data/phim/phimbo
            $this->line("Sử dụng: storage/app/public/data/phim/phimbo");
        } else {
            $this->error('Không tìm thấy folder phim bộ nào!');
            $this->line('Đã kiểm tra:');
            $this->line('  - ' . $storagePath);
            return 1;
        }
        
        // Option --list: Liệt kê tất cả folders và movies
        if ($this->option('list')) {
            return $this->listFoldersAndMovies($baseVideoPath, $urlPrefix);
        }
        
        $movieId = $this->argument('movie_id');
        $customFolder = $this->option('folder');
        
        if ($movieId) {
            $movies = Movie::where('id', $movieId)->where('type', 'phimbo')->get();
        } else {
            $movies = Movie::where('type', 'phimbo')->get();
        }
        
        if ($movies->isEmpty()) {
            $this->error('Không tìm thấy phim bộ nào!');
            return 1;
        }
        
        $this->info('Bắt đầu quét ' . $movies->count() . ' phim bộ...');
        $this->newLine();
        
        foreach ($movies as $movie) {
            $this->info("Đang xử lý: {$movie->title} (ID: {$movie->id})");
            
            // Nếu có --folder thì dùng folder custom, không thì tạo slug
            if ($customFolder) {
                $folderName = $customFolder;
            } else {
                // Kiểm tra xem movie có video_url không, extract folder từ đó
                if (!empty($movie->video_url)) {
                    // Ví dụ: data/phim/phimbo/phamnhantutien/tap_1.mp4
                    if (preg_match('#data/phim/phimbo/([^/]+)/#', $movie->video_url, $matches)) {
                        $folderName = $matches[1];
                        $this->line("  → Tìm thấy folder từ video_url: {$folderName}");
                    } else {
                        $folderName = $this->createSlug($movie->title);
                    }
                } else {
                    $folderName = $this->createSlug($movie->title);
                }
            }
            
            $movieFolder = $baseVideoPath . '/' . $folderName;
            
            // Kiểm tra folder tồn tại
            if (!File::isDirectory($movieFolder)) {
                $this->warn("  → Không tìm thấy folder: {$movieFolder}");
                $this->line("  → Gợi ý: Chạy 'php artisan episodes:sync-videos --list' để xem tất cả folders");
                $this->line("  → Hoặc chạy: php artisan episodes:sync-videos {$movie->id} --folder=ten_folder_thuc_te");
                continue;
            }
            
            // Quét tất cả file tap_*.mp4
            $videoFiles = glob($movieFolder . '/tap_*.mp4');
            
            if (empty($videoFiles)) {
                $this->warn("  → Không tìm thấy file video tap_*.mp4");
                continue;
            }
            
            $this->line("  → Tìm thấy " . count($videoFiles) . " tập");
            
            // Xử lý từng file
            $synced = 0;
            foreach ($videoFiles as $filePath) {
                $fileName = basename($filePath);
                
                // Extract episode number từ tap_1.mp4, tap_2.mp4, etc
                if (preg_match('/tap_(\d+)\.mp4$/i', $fileName, $matches)) {
                    $episodeNumber = (int) $matches[1];
                    
                    // Tạo relative path - LƯU Ý: không thêm /storage/ vì storage_url() sẽ xử lý
                    $videoUrl = $urlPrefix . '/' . $folderName . '/' . $fileName;
                    
                    // Tìm hoặc tạo episode
                    $episode = Episode::firstOrCreate(
                        [
                            'movie_id' => $movie->id,
                            'episode_number' => $episodeNumber,
                        ],
                        [
                            'title' => 'Tập ' . $episodeNumber,
                            'video_url' => $videoUrl,
                        ]
                    );
                    
                    // Cập nhật video_url nếu đã tồn tại
                    if ($episode->wasRecentlyCreated) {
                        $this->line("    ✓ Tạo mới tập {$episodeNumber}: {$videoUrl}");
                        $synced++;
                    } else if ($episode->video_url !== $videoUrl) {
                        $episode->update(['video_url' => $videoUrl]);
                        $this->line("    ✓ Cập nhật tập {$episodeNumber}: {$videoUrl}");
                        $synced++;
                    } else {
                        $this->line("    - Tập {$episodeNumber} đã đúng");
                    }
                }
            }
            
            if ($synced > 0) {
                $this->info("  → Đã sync {$synced} tập");
            }
            
            $this->newLine();
        }
        
        $this->info('✓ Hoàn tất!');
        return 0;
    }
    
    /**
     * Liệt kê tất cả folders và movies để user có thể match
     */
    private function listFoldersAndMovies($baseVideoPath, $urlPrefix)
    {
        $this->info('=== DANH SÁCH FOLDERS VÀ PHIM BỘ ===');
        $this->newLine();
        
        // List folders
        $this->info('Folders trong ' . $baseVideoPath . ':');
        if (File::isDirectory($baseVideoPath)) {
            $folders = File::directories($baseVideoPath);
            if (empty($folders)) {
                $this->warn('  → Không có folder nào');
            } else {
                foreach ($folders as $folder) {
                    $folderName = basename($folder);
                    $videoCount = count(glob($folder . '/tap_*.mp4'));
                    $this->line("  → {$folderName} ({$videoCount} video)");
                }
            }
        } else {
            $this->error('  → Folder không tồn tại!');
        }
        
        $this->newLine();
        
        // List movies
        $this->info('Phim bộ trong database:');
        $movies = Movie::where('type', 'phimbo')->get();
        if ($movies->isEmpty()) {
            $this->warn('  → Không có phim bộ nào');
        } else {
            foreach ($movies as $movie) {
                $episodeCount = $movie->episodes()->count();
                $this->line("  → ID {$movie->id}: {$movie->title} ({$episodeCount} tập trong DB)");
                if (!empty($movie->video_url)) {
                    $this->line("     Video URL: {$movie->video_url}");
                }
            }
        }
        
        $this->newLine();
        $this->info('Hướng dẫn:');
        $this->line('1. Sync tất cả: php artisan episodes:sync-videos');
        $this->line('2. Sync 1 phim: php artisan episodes:sync-videos {movie_id}');
        $this->line('3. Sync với folder tùy chỉnh: php artisan episodes:sync-videos {movie_id} --folder=ten_folder');
        
        return 0;
    }
    
    /**
     * Tạo slug từ tên phim
     */
    private function createSlug($title)
    {
        // Remove Vietnamese accents
        $slug = $this->removeVietnameseAccents($title);
        
        // Lowercase and replace spaces with nothing or dash
        $slug = strtolower($slug);
        $slug = preg_replace('/[^a-z0-9]+/', '', $slug);
        
        return $slug;
    }
    
    /**
     * Remove Vietnamese accents
     */
    private function removeVietnameseAccents($str)
    {
        $vietnameseMap = [
            'à' => 'a', 'á' => 'a', 'ả' => 'a', 'ã' => 'a', 'ạ' => 'a',
            'ă' => 'a', 'ằ' => 'a', 'ắ' => 'a', 'ẳ' => 'a', 'ẵ' => 'a', 'ặ' => 'a',
            'â' => 'a', 'ầ' => 'a', 'ấ' => 'a', 'ẩ' => 'a', 'ẫ' => 'a', 'ậ' => 'a',
            'đ' => 'd',
            'è' => 'e', 'é' => 'e', 'ẻ' => 'e', 'ẽ' => 'e', 'ẹ' => 'e',
            'ê' => 'e', 'ề' => 'e', 'ế' => 'e', 'ể' => 'e', 'ễ' => 'e', 'ệ' => 'e',
            'ì' => 'i', 'í' => 'i', 'ỉ' => 'i', 'ĩ' => 'i', 'ị' => 'i',
            'ò' => 'o', 'ó' => 'o', 'ỏ' => 'o', 'õ' => 'o', 'ọ' => 'o',
            'ô' => 'o', 'ồ' => 'o', 'ố' => 'o', 'ổ' => 'o', 'ỗ' => 'o', 'ộ' => 'o',
            'ơ' => 'o', 'ờ' => 'o', 'ớ' => 'o', 'ở' => 'o', 'ỡ' => 'o', 'ợ' => 'o',
            'ù' => 'u', 'ú' => 'u', 'ủ' => 'u', 'ũ' => 'u', 'ụ' => 'u',
            'ư' => 'u', 'ừ' => 'u', 'ứ' => 'u', 'ử' => 'u', 'ữ' => 'u', 'ự' => 'u',
            'ỳ' => 'y', 'ý' => 'y', 'ỷ' => 'y', 'ỹ' => 'y', 'ỵ' => 'y',
        ];
        
        return strtr($str, $vietnameseMap);
    }
}
