<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý Combo & Đồ ăn</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 text-white min-h-screen p-6">
<div class="max-w-5xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-yellow-400">Combo & Đồ ăn</h1>
        <a href="<?php echo e(route('staff.food.create')); ?>"
           class="bg-yellow-400 text-black font-semibold px-4 py-2 rounded-lg hover:bg-yellow-300">
            + Thêm món
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="mb-4 p-3 bg-green-800 rounded text-green-200"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php $__empty_1 = true; $__currentLoopData = $items; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="bg-gray-900 rounded-xl overflow-hidden">
            <?php if($item->image): ?>
                <img src="<?php echo e(Storage::url($item->image)); ?>" alt="<?php echo e($item->name); ?>"
                     class="w-full h-40 object-cover">
            <?php else: ?>
                <div class="w-full h-40 bg-gray-800 flex items-center justify-center text-4xl">🍿</div>
            <?php endif; ?>

            <div class="p-4">
                <div class="flex items-start justify-between gap-2 mb-1">
                    <h3 class="font-semibold text-white"><?php echo e($item->name); ?></h3>
                    <span class="text-xs px-2 py-0.5 rounded-full
                        <?php echo e($item->type === 'combo' ? 'bg-purple-800 text-purple-200' :
                           ($item->type === 'drink' ? 'bg-blue-800 text-blue-200' : 'bg-orange-800 text-orange-200')); ?>">
                        <?php echo e($item->type); ?>

                    </span>
                </div>
                <p class="text-yellow-400 font-bold"><?php echo e(number_format($item->price)); ?>đ</p>
                <?php if($item->description): ?>
                    <p class="text-xs text-gray-400 mt-1 line-clamp-2"><?php echo e($item->description); ?></p>
                <?php endif; ?>

                <div class="flex items-center justify-between mt-4">
                    
                    <button onclick="toggleActive(<?php echo e($item->id); ?>, this)"
                            data-active="<?php echo e($item->is_active ? '1' : '0'); ?>"
                            class="text-xs px-2 py-1 rounded <?php echo e($item->is_active ? 'bg-green-800 text-green-200' : 'bg-gray-700 text-gray-400'); ?>">
                        <?php echo e($item->is_active ? 'Đang bán' : 'Tạm dừng'); ?>

                    </button>

                    <div class="flex gap-2">
                        <a href="<?php echo e(route('staff.food.edit', $item)); ?>"
                           class="text-xs bg-gray-700 hover:bg-gray-600 px-2 py-1 rounded">Sửa</a>
                        <form method="POST" action="<?php echo e(route('staff.food.destroy', $item)); ?>"
                              onsubmit="return confirm('Xoá món này?')">
                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                            <button class="text-xs bg-red-800 hover:bg-red-700 px-2 py-1 rounded">Xoá</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <p class="col-span-3 text-center text-gray-500 py-20">Chưa có món nào.</p>
        <?php endif; ?>
    </div>

    <div class="mt-6"><?php echo e($items->links()); ?></div>

</div>

<script>
async function toggleActive(id, btn) {
    const res  = await fetch(`/staff/food/${id}/toggle`, {
        method: 'PATCH',
        headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>', 'Accept': 'application/json' },
    });
    const data = await res.json();

    btn.dataset.active = data.is_active ? '1' : '0';
    btn.textContent    = data.is_active ? 'Đang bán' : 'Tạm dừng';
    btn.className      = `text-xs px-2 py-1 rounded ${data.is_active ? 'bg-green-800 text-green-200' : 'bg-gray-700 text-gray-400'}`;
}
</script>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\CineHub_DuAnTotNghiep\resources\views/staff/food/index.blade.php ENDPATH**/ ?>