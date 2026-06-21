<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Check-in vé</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 text-white min-h-screen p-6">
<div class="max-w-lg mx-auto">

    <h1 class="text-2xl font-bold text-yellow-400 mb-6">Check-in vé</h1>

    
    <div class="bg-gray-900 rounded-xl p-6 mb-6">
        <label class="block text-sm text-gray-400 mb-2">Quét QR hoặc nhập mã vé</label>
        <div class="flex gap-2">
            <input id="codeInput" type="text" autofocus placeholder="Scan QR hoặc nhập mã..."
                   class="flex-1 bg-gray-800 border border-gray-700 rounded px-3 py-2 text-white text-lg">
            <button onclick="checkIn()"
                    class="bg-yellow-400 text-black font-semibold px-5 py-2 rounded-lg hover:bg-yellow-300">
                Check-in
            </button>
        </div>
    </div>

    
    <div id="result" class="hidden"></div>

    
    <div class="mt-8">
        <a href="<?php echo e(route('staff.tickets.index')); ?>"
           class="text-sm text-gray-400 hover:text-yellow-400">→ Xem danh sách vé hôm nay</a>
    </div>

</div>

<script>
const input = document.getElementById('codeInput');
const result = document.getElementById('result');

// Tự submit khi scan QR (thường kết thúc bằng Enter)
input.addEventListener('keydown', e => { if (e.key === 'Enter') checkIn(); });

async function checkIn() {
    const code = input.value.trim();
    if (!code) return;

    result.innerHTML = '<p class="text-gray-400">Đang kiểm tra...</p>';
    result.classList.remove('hidden');

    try {
        const res = await fetch('<?php echo e(route("staff.tickets.checkin")); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>',
            },
            body: JSON.stringify({ code }),
        });

        const data = await res.json();
        renderResult(data);
    } catch (e) {
        result.innerHTML = '<div class="p-4 bg-red-900 rounded-lg text-red-200">Lỗi kết nối.</div>';
    }

    input.value = '';
    input.focus();
}

function renderResult(data) {
    if (data.success) {
        const t = data.ticket;
        result.innerHTML = `
            <div class="p-4 bg-green-900 rounded-xl border border-green-600">
                <p class="text-green-300 font-bold text-lg mb-3">✅ ${data.message}</p>
                <div class="grid grid-cols-2 gap-2 text-sm">
                    <span class="text-gray-400">Phim:</span><span>${t.movie}</span>
                    <span class="text-gray-400">Suất:</span><span>${t.show_time} — ${t.show_date}</span>
                    <span class="text-gray-400">Ghế:</span><span class="font-bold text-yellow-400">${t.seat} (${t.seat_type})</span>
                    <span class="text-gray-400">Khách:</span><span>${t.customer}</span>
                </div>
            </div>`;
    } else {
        const isWarning = data.ticket;
        result.innerHTML = `
            <div class="p-4 ${isWarning ? 'bg-yellow-900 border border-yellow-600' : 'bg-red-900 border border-red-700'} rounded-xl">
                <p class="${isWarning ? 'text-yellow-300' : 'text-red-300'} font-semibold">
                    ${isWarning ? '⚠️' : '❌'} ${data.message}
                </p>
            </div>`;
    }
}
</script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\CineHub_DuAnTotNghiep\resources\views/staff/tickets/scan.blade.php ENDPATH**/ ?>