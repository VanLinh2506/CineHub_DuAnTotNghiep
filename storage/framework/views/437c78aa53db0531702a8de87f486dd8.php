<?php
    $title = 'Quản lý suất chiếu';
?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5><?php echo e($title); ?> - <?php echo e($theater['name'] ?? 'Theater'); ?></h5>
        <a href="<?php echo e(url('?route=moderator/showtimes/create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm suất chiếu
        </a>
    </div>

    <!-- Filters -->
    <form method="GET" class="mb-3">
        <input type="hidden" name="route" value="moderator/showtimes">
        <div class="row g-2">
            <div class="col-md-4">
                <select name="movie_id" class="form-select">
                    <option value="">Tất cả phim</option>
                    <?php $__currentLoopData = $movies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($movie['id']); ?>" <?php echo e((isset($movie_id) && $movie_id == $movie['id']) ? 'selected' : ''); ?>>
                            <?php echo e($movie['title']); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <select name="screen_id" class="form-select">
                    <option value="">Tất cả phòng</option>
                    <?php $__currentLoopData = $screens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $screen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($screen['id']); ?>" <?php echo e((isset($screen_id) && $screen_id == $screen['id']) ? 'selected' : ''); ?>>
                            <?php echo e($screen['name']); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="fas fa-filter"></i> Lọc
                </button>
            </div>
        </div>
    </form>

    <!-- Showtimes Table -->
    <div class="stat-card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Phim</th>
                        <th>Phòng</th>
                        <th>Ngày</th>
                        <th>Giờ</th>
                        <th>Giá vé</th>
                        <th>Vé đã bán</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($showtimes)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">Không có suất chiếu nào</td>
                        </tr>
                    <?php else: ?>
                        <?php $__currentLoopData = $showtimes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $showtime): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>#<?php echo e($showtime['id']); ?></td>
                                <td><strong><?php echo e($showtime['movie_title']); ?></strong></td>
                                <td><?php echo e($showtime['screen_name']); ?></td>
                                <td><?php echo e(\Carbon\Carbon::parse($showtime['show_date'])->format('d/m/Y')); ?></td>
                                <td>
                                    <span class="badge bg-primary"><?php echo e(\Carbon\Carbon::parse($showtime['show_time'])->format('H:i')); ?></span>
                                </td>
                                <td><?php echo e(number_format($showtime['price'] ?? 0)); ?>₫</td>
                                <td>
                                    <span class="badge bg-info"><?php echo e($showtime['tickets_sold'] ?? 0); ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo e(($showtime['status'] ?? 'active') === 'active' ? 'success' : 'secondary'); ?>">
                                        <?php echo e(($showtime['status'] ?? 'active') === 'active' ? 'Hoạt động' : 'Đã đóng'); ?>

                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo e(url('?route=moderator/showtimes/edit&id=' . $showtime['id'])); ?>" class="btn btn-outline-primary" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deleteShowtime(<?php echo e($showtime['id']); ?>)" class="btn btn-outline-danger" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function deleteShowtime(id) {
        if (confirm('Bạn chắc chắn muốn xóa suất chiếu này?')) {
            window.location.href = '<?php echo e(url("?route=moderator/showtimes/delete&id=")); ?>' + id;
        }
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\CineHub_DuAnTotNghiep\resources\views\moderator\showtimes.blade.php ENDPATH**/ ?>