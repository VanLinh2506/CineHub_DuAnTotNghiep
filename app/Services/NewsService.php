<?php

namespace App\Services;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Pagination\LengthAwarePaginator;

class NewsService
{
    /**
     * Danh sách bài viết đã published, có phân trang
     */
    public function getPublished(int $perPage = 12, ?int $categoryId = null): LengthAwarePaginator
    {
        return News::with(['category', 'author'])
            ->published()
            ->when($categoryId, fn($q) => $q->where('news_category_id', $categoryId))
            ->orderByDesc('published_at')
            ->paginate($perPage);
    }

    /**
     * Lấy bài viết theo slug
     */
    public function getBySlug(string $slug): News
    {
        return News::with(['category', 'author'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();
    }

    /**
     * Bài viết liên quan cùng danh mục
     */
    public function getRelated(News $news, int $limit = 4): \Illuminate\Database\Eloquent\Collection
    {
        return News::published()
            ->where('id', '!=', $news->id)
            ->where('news_category_id', $news->news_category_id)
            ->orderByDesc('published_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Tất cả danh mục kèm số bài viết
     */
    public function getCategories(): \Illuminate\Database\Eloquent\Collection
    {
        return NewsCategory::withCount(['news' => fn($q) => $q->published()])
            ->orderBy('name')
            ->get();
    }

    /**
     * Import từ dữ liệu WordPress (array từ XML/JSON export)
     * $posts = [['wp_id', 'title', 'content', 'slug', 'published_at', 'category'], ...]
     */
    public function importFromWordPress(array $posts): array
    {
        $imported = 0;
        $skipped  = 0;

        foreach ($posts as $post) {
            $exists = News::where('wp_id', $post['wp_id'] ?? null)
                          ->orWhere('slug', $post['slug'] ?? '')
                          ->exists();

            if ($exists) {
                $skipped++;
                continue;
            }

            $category = null;
            if (!empty($post['category'])) {
                $category = NewsCategory::firstOrCreate(
                    ['slug' => \Illuminate\Support\Str::slug($post['category'])],
                    ['name' => $post['category']]
                );
            }

            News::create([
                'wp_id'            => $post['wp_id'] ?? null,
                'title'            => $post['title'],
                'slug'             => $post['slug'] ?? \Illuminate\Support\Str::slug($post['title']),
                'content'          => $post['content'],
                'excerpt'          => $post['excerpt'] ?? null,
                'thumbnail'        => $post['thumbnail'] ?? null,
                'status'           => 'published',
                'published_at'     => $post['published_at'] ?? now(),
                'news_category_id' => $category?->id,
            ]);

            $imported++;
        }

        return ['imported' => $imported, 'skipped' => $skipped];
    }
}
