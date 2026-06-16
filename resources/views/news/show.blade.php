<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $post->title }} - CineHub</title>
    <meta name="description" content="{{ $post->excerpt }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .prose-content h1, .prose-content h2, .prose-content h3 { color: #facc15; margin: 1.5rem 0 0.75rem; font-weight: 700; }
        .prose-content h2 { font-size: 1.4rem; }
        .prose-content h3 { font-size: 1.2rem; }
        .prose-content p  { color: #d1d5db; line-height: 1.8; margin-bottom: 1rem; }
        .prose-content a  { color: #facc15; text-decoration: underline; }
        .prose-content img { max-width: 100%; border-radius: 0.5rem; margin: 1rem 0; }
        .prose-content ul, .prose-content ol { color: #d1d5db; padding-left: 1.5rem; margin-bottom: 1rem; }
        .prose-content li { margin-bottom: 0.3rem; }
        .prose-content blockquote { border-left: 3px solid #facc15; padding-left: 1rem; color: #9ca3af; font-style: italic; }
    </style>
</head>
<body class="bg-gray-950 text-white min-h-screen">

<div class="max-w-3xl mx-auto px-4 py-10">

    {{-- Breadcrumb --}}
    <nav class="text-sm text-gray-500 mb-6">
        <a href="{{ route('news.index') }}" class="hover:text-yellow-400">Tin tức</a>
        @if($post->category)
            <span class="mx-2">/</span>
            <a href="{{ route('news.index', ['category' => $post->news_category_id]) }}" class="hover:text-yellow-400">
                {{ $post->category->name }}
            </a>
        @endif
        <span class="mx-2">/</span>
        <span class="text-gray-300">{{ Str::limit($post->title, 40) }}</span>
    </nav>

    {{-- Tiêu đề --}}
    <h1 class="text-3xl font-bold leading-tight text-white mb-4">{{ $post->title }}</h1>

    {{-- Meta --}}
    <div class="flex items-center gap-4 text-sm text-gray-500 mb-6">
        @if($post->author)
        <span>✍️ {{ $post->author->name }}</span>
        @endif
        <span>📅 {{ $post->published_at->format('d/m/Y') }}</span>
        @if($post->category)
        <span class="text-yellow-400">{{ $post->category->name }}</span>
        @endif
    </div>

    {{-- Thumbnail --}}
    @if($post->thumbnail)
    <img src="{{ $post->thumbnail }}" alt="{{ $post->title }}"
         class="w-full rounded-xl mb-8 max-h-80 object-cover">
    @endif

    {{-- Nội dung --}}
    <div class="prose-content">
        {!! $post->content !!}
    </div>

    {{-- Bài viết liên quan --}}
    @if($related->isNotEmpty())
    <div class="mt-14 border-t border-gray-800 pt-8">
        <h3 class="text-xl font-bold text-yellow-400 mb-5">Bài viết liên quan</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            @foreach($related as $item)
            <a href="{{ route('news.show', $item->slug) }}"
               class="flex gap-3 bg-gray-900 rounded-lg p-3 hover:ring-1 hover:ring-yellow-400 transition">
                @if($item->thumbnail)
                <img src="{{ $item->thumbnail }}" class="w-20 h-16 object-cover rounded flex-shrink-0">
                @endif
                <div>
                    <p class="text-sm font-medium text-white line-clamp-2">{{ $item->title }}</p>
                    <p class="text-xs text-gray-500 mt-1">{{ $item->published_at->format('d/m/Y') }}</p>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Quay lại --}}
    <div class="mt-10">
        <a href="{{ route('news.index') }}" class="text-yellow-400 hover:underline text-sm">← Quay lại tin tức</a>
    </div>

</div>
</body>
</html>
