<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Models\NewsCategory;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        $news = News::with('category')
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->paginate(12);

        $categories = NewsCategory::withCount('news')->get();

        return view('news.index', compact('news', 'categories'));
    }

    public function category($categoryId)
    {
        $category = NewsCategory::findOrFail($categoryId);
        
        $news = News::with('category')
            ->where('category_id', $categoryId)
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->paginate(12);

        $categories = NewsCategory::withCount('news')->get();

        return view('news.category', compact('news', 'category', 'categories'));
    }

    public function show($slug)
    {
        $newsItem = News::with('category')
            ->where('slug', $slug)
            ->where('status', 'published')
            ->firstOrFail();

        // Increment views
        $newsItem->increment('views');

        // Get related news
        $relatedNews = News::where('category_id', $newsItem->category_id)
            ->where('id', '!=', $newsItem->id)
            ->where('status', 'published')
            ->orderByDesc('published_at')
            ->limit(4)
            ->get();

        return view('news.show', compact('newsItem', 'relatedNews'));
    }
}
