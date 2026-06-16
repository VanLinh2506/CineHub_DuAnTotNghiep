<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tin tức - CineHub</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 text-white min-h-screen">

<div class="max-w-6xl mx-auto px-4 py-10">

    {{-- Header --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-yellow-400">Tin tức điện ảnh</h1>
        <p class="text-gray-400 mt-1">Cập nhật mới nhất về phim, sự kiện và rạp chiếu</p>
    </div>

    {{-- Filter danh mục --}}
    @if($categories->isNotEmpty())
    <div class="flex flex-wrap gap-2 mb-8">
        <a href="{{ route('news.index') }}"
           class="px-4 py-1.5 rounded-full text-sm {{ !$categoryId ? 'bg-yellow-400 text-black font-semibold' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">
            Tất cả
        </a>
        @foreach($categories as $cat)
        <a href="{{ route('news.index', ['category' => $cat->id]) }}"
           class="px-4 py-1.5 rounded-full text-sm {{ $categoryId == $cat->id ? 'bg-yellow-400 text-black font-semibold' : 'bg-gray-800 text-gray-300 hover:bg-gray-700' }}">
            {{ $cat->name }}
            <span class="opacity-60">({{ $cat->news_count }})</span>
        </a>
        @endforeach
    </div>
    @endif

    {{-- Danh sách bài viết --}}
    @if($posts->isEmpty())
        <p class="text-gray-500 text-center py-20">Chưa có bài viết nào.</p>
    @else
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($posts as $post)
        <a href="{{ route('news.show', $post->slug) }}" class="group bg-gray-900 rounded-xl overflow-hidden hover:ring-2 hover:ring-yellow-400 transition">
            @if($post->thumbnail)
            <img src="{{ $post->thumbnail }}" alt="{{ $post->title }}"
                 class="w-full h-48 object-cover group-hover:opacity-90 transition">
            @else
            <div class="w-full h-48 bg-gray-800 flex items-center justify-center text-gray-600 text-4xl">🎬</div>
            @endif

            <div class="p-4">
                @if($post->category)
                <span class="text-xs text-yellow-400 uppercase font-semibold">{{ $post->category->name }}</span>
                @endif
                <h2 class="mt-1 font-semibold text-white group-hover:text-yellow-300 line-clamp-2 transition">
                    {{ $post->title }}
                </h2>
                <p class="mt-2 text-sm text-gray-400 line-clamp-3">{{ $post->excerpt }}</p>
                <p class="mt-3 text-xs text-gray-600">{{ $post->published_at->format('d/m/Y') }}</p>
            </div>
        </a>
        @endforeach
    </div>

    {{-- Phân trang --}}
    <div class="mt-10">
        {{ $posts->links() }}
    </div>
    @endif

</div>
</body>
</html>
