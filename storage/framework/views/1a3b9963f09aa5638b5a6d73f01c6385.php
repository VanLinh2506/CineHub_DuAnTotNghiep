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
        <a href="<?php echo e(route('staff.tickets.scan')); ?>"
           class="bg-yellow-400 text-black font-semibold px-4 py-2 rounded-lg hover:bg-yellow-300">
            📷 Check-in
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-4 p-3 bg-green-800 rounded text-green-200"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    
    <form method="GET" class="flex flex-wrap gap-3 mb-6">
        <input type="date" name="date" value="<?php echo e($date); ?>"
               class="bg-gray-800 border border-gray-700 rounded px-3 py-2 text-white">

        <input type="text" name="search" value="<?php echo e($search); ?>" placeholder="Tìm tên, email, ghế, mã vé..."
               class="bg-gray-800 border border-gray-700 rounded px-3 py-2 text-white flex-1 min-w-48">

        <select name="status" class="bg-gray-800 border border-gray-700 rounded px-3 py-2 text-white">
            <option value="">Tất cả trạng thái</option>
            <option value="Đã đặt" <?php echo e($status === 'Đã đặt' ? 'selected' : ''); ?>>Đã đặt</option>
            <option value="Đã huỷ" <?php echo e($status === 'Đã huỷ' ? 'selected' : ''); ?>>Đã huỷ</option>
        </select>

        <button class="bg-gray-700 px-4 py-2 rounded hover:bg-gray-600">Tìm</button>
    </form>

    
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
                <?php $__empty_1 = true; $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <tr class="hover:bg-gray-900">
                    <td class="px-4 py-3 font-medium"><?php echo e($ticket->showtime->movie->title); ?></td>
                    <td class="px-4 py-3 text-gray-400"><?php echo e(substr($ticket->showtime->show_time, 0, 5)); ?></td>
                    <td class="px-4 py-3">
                        <span class="font-bold text-yellow-400"><?php echo e($ticket->seat); ?></span>
                        <span class="text-xs text-gray-500 ml-1">(<?php echo e($ticket->seat_type); ?>)</span>
                    </td>
                    <td class="px-4 py-3 text-gray-300"><?php echo e($ticket->user?->name ?? 'Khách lẻ'); ?></td>
                    <td class="px-4 py-3"><?php echo e(number_format($ticket->price)); ?>đ</td>
                    <td class="px-4 py-3">
                        <?php if($ticket->is_picked_up): ?>
                            <span class="text-green-400 text-xs">✅ <?php echo e($ticket->picked_up_at?->format('H:i')); ?></span>
                        <?php else: ?>
                            <span class="text-gray-600 text-xs">Chưa</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <tr><td colspan="6" class="px-4 py-10 text-center text-gray-500">Không tìm thấy vé nào.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div class="mt-4"><?php echo e($tickets->links()); ?></div>

</div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\CineHub_DuAnTotNghiep\resources\views/staff/tickets/index.blade.php ENDPATH**/ ?>