<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e(isset($showtime) ? 'Sửa' : 'Tạo'); ?> suất chiếu</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-950 text-white min-h-screen p-6">
<div class="max-w-xl mx-auto">

    <h1 class="text-2xl font-bold text-yellow-400 mb-6">
        <?php echo e(isset($showtime) ? 'Sửa suất chiếu' : 'Tạo suất chiếu mới'); ?>

    </h1>

    <?php if(session('error')): ?>
        <div class="mb-4 p-3 bg-red-800 rounded text-red-200"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <form method="POST"
          action="<?php echo e(isset($showtime) ? route('staff.showtimes.update', $showtime) : route('staff.showtimes.store')); ?>"
          class="space-y-5 bg-gray-900 p-6 rounded-xl">
        <?php echo csrf_field(); ?>
        <?php if(isset($showtime)): ?> <?php echo method_field('PUT'); ?> <?php endif; ?>

        <div>
            <label class="block text-sm text-gray-400 mb-1">Phim</label>
            <select name="movie_id" required
                    class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 text-white">
                <option value="">-- Chọn phim --</option>
                <?php $__currentLoopData = $movies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($movie->id); ?>"
                    <?php echo e(old('movie_id', $showtime->movie_id ?? '') == $movie->id ? 'selected' : ''); ?>>
                    <?php echo e($movie->title); ?> (<?php echo e($movie->duration); ?> phút)
                </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['movie_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div>
            <label class="block text-sm text-gray-400 mb-1">Phòng chiếu</label>
            <select name="screen_id" required
                    class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 text-white">
                <option value="">-- Chọn phòng --</option>
                <?php $__currentLoopData = $screens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $screen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($screen->id); ?>"
                    <?php echo e(old('screen_id', $showtime->screen_id ?? '') == $screen->id ? 'selected' : ''); ?>>
                    <?php echo e($screen->screen_name); ?> (<?php echo e($screen->screen_type); ?> • <?php echo e($screen->total_seats); ?> ghế)
                </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <?php $__errorArgs = ['screen_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm text-gray-400 mb-1">Ngày chiếu</label>
                <input type="date" name="show_date" required
                       value="<?php echo e(old('show_date', isset($showtime) ? $showtime->show_date?->format('Y-m-d') : '')); ?>"
                       min="<?php echo e(today()->toDateString()); ?>"
                       class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 text-white">
                <?php $__errorArgs = ['show_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
            <div>
                <label class="block text-sm text-gray-400 mb-1">Giờ chiếu</label>
                <input type="time" name="show_time" required
                       value="<?php echo e(old('show_time', isset($showtime) ? substr($showtime->show_time, 0, 5) : '')); ?>"
                       class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 text-white">
                <?php $__errorArgs = ['show_time'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <div>
            <label class="block text-sm text-gray-400 mb-1">Giá vé (VND)</label>
            <input type="number" name="price" required min="1000" step="1000"
                   value="<?php echo e(old('price', isset($showtime) ? $showtime->price : '')); ?>"
                   class="w-full bg-gray-800 border border-gray-700 rounded px-3 py-2 text-white">
            <?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <p class="text-red-400 text-xs mt-1"><?php echo e($message); ?></p> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="bg-yellow-400 text-black font-semibold px-6 py-2 rounded-lg hover:bg-yellow-300">
                <?php echo e(isset($showtime) ? 'Cập nhật' : 'Tạo suất chiếu'); ?>

            </button>
            <a href="<?php echo e(route('staff.showtimes.index')); ?>"
               class="bg-gray-700 px-6 py-2 rounded-lg hover:bg-gray-600">Huỷ</a>
        </div>
    </form>

</div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\CineHub_DuAnTotNghiep\resources\views/staff/showtimes/form.blade.php ENDPATH**/ ?>