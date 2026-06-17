<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh sách vé</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 text-white min-h-screen p-6">
<div class="max-w-6xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-yellow-400">Danh sách vé</h1>
        <a href="{{ route('staff.tickets.scan') }}"
           class="bg-yellow-400 text-black font-semibold px-4 py-2 rounded-lg hover:bg-yellow-300">
            📷 Check-in
        </a>
    </div>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-800 rounded text-green-200">{{ session('success') }}</div>
    @endif

    {{-- Filters --}}
    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <input type="date" name="date" value="{{ $date }}"
               class="bg-gray-800 border border-gray-700 rounded px-3 py-2 text-white">

        <input type="text" name="search" value="{{ $search }}" placeholder="Tìm tên, email, ghế, mã vé..."
               class="bg-gray-800 border border-gray-700 rounded px-3 py-2 text-white flex-1 min-w-48">

        <select name="status" class="bg-gray-800 border border-gray-700 rounded px-3 py-2 text-white">
            <option value="">Tất cả trạng thái</option>
            <option value="Đã đặt" {{ $status === 'Đã đặt' ? 'selected' : '' }}>Đã đặt</option>
            <option value="Đã huỷ" {{ $status === 'Đã huỷ' ? 'selected' : '' }}>Đã huỷ</option>
        </select>

        <button class="bg-gray-700 px-4 py-2 rounded hover:bg-gray-600">Tìm</button>
    </form>

    {{-- Table --}}
    <div class="overflow-x-auto rounded-xl">
        <table class="w-full text-sm">
            <thead class="bg-gray-800 text-gray-400 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3 text-left">Phim</th>
                    <th class="px-4 py-3 text-left">Suất</th>
                    <th class="px-4 py-3 text-left">Ghế</th>
                    <th class="px-4 py-3 text-left">Khách hàng</th>
                    <th class="px-4 py-3 text-left">Giá</th>
                    <th class="px-4 py-3 text-left">Check-in</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800">
                @forelse($tickets as $ticket)
                <tr class="hover:bg-gray-900">
                    <td class="px-4 py-3 font-medium">{{ $ticket->showtime->movie->title }}</td>
                    <td class="px-4 py-3 text-gray-400">{{ substr($ticket->showtime->show_time, 0, 5) }}</td>
                    <td class="px-4 py-3">
                        <span class="font-bold text-yellow-400">{{ $ticket->seat }}</span>
                        <span class="text-xs text-gray-500 ml-1">({{ $ticket->seat_type }})</span>
                    </td>
                    <td class="px-4 py-3 text-gray-300">{{ $ticket->user?->name ?? 'Khách lẻ' }}</td>
                    <td class="px-4 py-3">{{ number_format($ticket->price) }}đ</td>
                    <td class="px-4 py-3">
                        @if($ticket->is_picked_up)
                            <span class="text-green-400 text-xs">✅ {{ $ticket->picked_up_at?->format('H:i') }}</span>
                        @else
                            <span class="text-gray-600 text-xs">Chưa</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500">Không tìm thấy vé nào.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $tickets->links() }}</div>

</div>
</body>
</html>
