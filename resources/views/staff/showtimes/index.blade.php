<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý suất chiếu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 text-white min-h-screen p-6">
<div class="max-w-5xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-yellow-400">Suất chiếu</h1>
        <a href="{{ route('staff.showtimes.create') }}"
           class="bg-yellow-400 text-black font-semibold px-4 py-2 rounded-lg hover:bg-yellow-300">
            + Tạo suất chiếu
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-800 rounded text-green-200">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 p-3 bg-red-800 rounded text-red-200">{{ session('error') }}</div>
    @endif

    {{-- Filter ngày --}}
    <form method="GET" class="mb-6 flex gap-3 items-center">
        <input type="date" name="date" value="{{ $date }}"
               class="bg-gray-800 text-white border border-gray-700 rounded px-3 py-2">
        <button class="bg-gray-700 px-4 py-2 rounded hover:bg-gray-600">Xem</button>
    </form>

    {{-- Nhóm theo phòng --}}
    @forelse($showtimes->groupBy('screen_id') as $screenId => $group)
    <div class="mb-6">
        <h2 class="text-sm font-semibold text-gray-400 uppercase mb-3">
            {{ $group->first()->screen->screen_name }}
            ({{ $group->first()->screen->screen_type }})
        </h2>
        <div class="space-y-2">
            @foreach($group as $show)
            <div class="flex items-center justify-between bg-gray-900 rounded-lg px-4 py-3">
                <div class="flex items-center gap-4">
                    <span class="text-xl font-bold text-yellow-400">{{ substr($show->show_time, 0, 5) }}</span>
                    <div>
                        <p class="font-medium">{{ $show->movie->title }}</p>
                        <p class="text-xs text-gray-400">
                            {{ $show->movie->duration }} phút •
                            {{ number_format($show->price) }}đ •
                            {{ $show->tickets->count() }} vé đã đặt
                        </p>
                    </div>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('staff.showtimes.edit', $show) }}"
                       class="text-sm bg-gray-700 hover:bg-gray-600 px-3 py-1 rounded">Sửa</a>
                    <form method="POST" action="{{ route('staff.showtimes.destroy', $show) }}"
                          onsubmit="return confirm('Xoá suất chiếu này?')">
                        @csrf @method('DELETE')
                        <button class="text-sm bg-red-800 hover:bg-red-700 px-3 py-1 rounded">Xoá</button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @empty
        <p class="text-gray-500 text-center py-20">Chưa có suất chiếu nào ngày {{ $date }}.</p>
    @endforelse

</div>
</body>
</html>
