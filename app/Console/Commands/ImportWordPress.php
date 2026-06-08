<?php

namespace App\Console\Commands;

use App\Services\NewsService;
use Illuminate\Console\Command;

class ImportWordPress extends Command
{
    protected $signature   = 'news:import-wp {file : Đường dẫn file XML export từ WordPress}';
    protected $description = 'Import bài viết từ WordPress XML export';

    public function handle(NewsService $newsService): int
    {
        $file = $this->argument('file');

        if (!file_exists($file)) {
            $this->error("Không tìm thấy file: {$file}");
            return 1;
        }

        $this->info("Đang đọc file: {$file}");

        $xml = simplexml_load_file($file);
        if (!$xml) {
            $this->error('File XML không hợp lệ.');
            return 1;
        }

        $posts = [];

        foreach ($xml->channel->item as $item) {
            $ns      = $item->children('wp', true);
            $content = $item->children('content', true);

            // Chỉ lấy post type = post, status = publish
            if ((string) $ns->post_type !== 'post' || (string) $ns->status !== 'publish') {
                continue;
            }

            // Lấy category đầu tiên
            $category = null;
            foreach ($item->category as $cat) {
                $category = (string) $cat;
                break;
            }

            $posts[] = [
                'wp_id'        => (string) $ns->post_id,
                'title'        => (string) $item->title,
                'slug'         => (string) $ns->post_name,
                'content'      => (string) $content->encoded,
                'published_at' => date('Y-m-d H:i:s', strtotime((string) $item->pubDate)),
                'category'     => $category,
                'thumbnail'    => null,
            ];
        }

        if (empty($posts)) {
            $this->warn('Không tìm thấy bài viết nào trong file.');
            return 0;
        }

        $this->info("Tìm thấy " . count($posts) . " bài viết. Đang import...");

        $result = $newsService->importFromWordPress($posts);

        $this->info("✅ Imported: {$result['imported']}");
        $this->warn("⏭  Skipped (đã tồn tại): {$result['skipped']}");

        return 0;
    }
}
