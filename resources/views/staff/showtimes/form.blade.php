<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($showtime) ? 'Sửa' : 'Tạo' }} suất chiếu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 text-white min-h-screen p-6">
<div class="max-w-xl mx-auto">

    <h1 class="text-2xl font-bold text-yellow-400 mb-6">
        {{ isset($showtime) ? 'Sửa suất chiếu' : 'Tạo suất chiếu mới' }}
    </h1>

    @if(session('error'))
        <div class="mb-4 p-3 bg-red-800 rounded text-red-200">{{ session('error') }}</div>
    @endif

    <form method="POST"
          action="{{ isset($showtime) ? route('staff.showtimes.update', $showtime) : route('staff.showtimes.store') }}"
          class="space-y-5 bg-gray-900 p-6 rounded-xl">
        @csrf
        @if(isset($showtime)) @method('PUT') @endif

        <div>
            <label class="block text-sm text-gray-400 mb-1">Phim</label>
            <select name="movie_id" required
                    class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 text-white">
                <option value="">-- Chọn phim --</option>
                @foreach($movies as $movie)
                <option value="{{ $movie->id }}"
                    {{ old('movie_id', $showtime->movie_id ?? '') == $movie->id ? 'selected' : '' }}>
                    {{ $movie->title }} ({{ $movie->duration }} phút)
                </option>
                @endforeach
            </select>
            @error('movie_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div>
            <label class="block text-sm text-gray-400 mb-1">Phòng chiếu</label>
            <select name="screen_id" required
                    class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 text-white">
                <option value="">-- Chọn phòng --</option>
                @foreach($screens as $screen)
                <option value="{{ $screen->id }}"
                    {{ old('screen_id', $showtime->screen_id ?? '') == $screen->id ? 'selected' : '' }}>
                    {{ $screen->screen_name }} ({{ $screen->screen_type }} • {{ $screen->total_seats }} ghế)
                </option>
                @endforeach
            </select>
            @error('screen_id') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-gray-400 mb-1">Ngày chiếu</label>
                <input type="date" name="show_date" required
                       value="{{ old('show_date', $showtime->show_date?->format('Y-m-d') ?? '') }}"
                       min="{{ today()->toDateString() }}"
                       class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 text-white">
                @error('show_date') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Giờ chiếu</label>
                <input type="time" name="show_time" required
                       value="{{ old('show_time', isset($showtime) ? substr($showtime->show_time, 0, 5) : '') }}"
                       class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 text-white">
                @error('show_time') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        <div>
            <label class="block text-sm text-gray-400 mb-1">Giá vé (VND)</label>
            <input type="number" name="price" required min="1000" step="1000"
                   value="{{ old('price', $showtime->price ?? '') }}"
                   class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 text-white">
            @error('price') <p class="text-red-400 text-xs mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="bg-yellow-400 text-black font-semibold px-6 py-2 rounded-lg hover:bg-yellow-300">
                {{ isset($showtime) ? 'Cập nhật' : 'Tạo suất chiếu' }}
            </button>
            <a href="{{ route('staff.showtimes.index') }}"
               class="bg-gray-700 px-6 py-2 rounded-lg hover:bg-gray-600">Huỷ</a>
        </div>
    </form>

</div>
</body>
</html>
