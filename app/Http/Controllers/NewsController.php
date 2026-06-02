<?php

namespace App\Http\Controllers;

use App\Services\NewsService;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function __construct(protected NewsService $news) {}

    /**
     * Danh sách tin tức
     * GET /tin-tuc
     */
    public function index(Request $request)
    {
        $categoryId = $request->query('category');

        $posts      = $this->news->getPublished(12, $categoryId);
        $categories = $this->news->getCategories();

        return view('news.index', compact('posts', 'categories', 'categoryId'));
    }

    /**
     * Chi tiết bài viết
     * GET /tin-tuc/{slug}
     */
    public function show(string $slug)
    {
        $post    = $this->news->getBySlug($slug);
        $related = $this->news->getRelated($post);

        return view('news.show', compact('post', 'related'));
    }
}
