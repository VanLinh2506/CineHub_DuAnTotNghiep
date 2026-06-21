<?php
    $title = 'Quản lý vé';
?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5><?php echo e($title); ?> - <?php echo e($theater['name'] ?? 'Theater'); ?></h5>
    </div>

    <!-- Filters -->
    <form method="GET" class="mb-3">
        <input type="hidden" name="route" value="moderator/tickets">
        <div class="row g-2">
            <div class="col-md-3">
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
                <select name="status" class="form-select">
                    <option value="">Tất cả trạng thái</option>
                    <option value="Đã đặt" <?php echo e((isset($status) && $status === 'Đã đặt') ? 'selected' : ''); ?>>Đã đặt</option>
                    <option value="Đã hủy" <?php echo e((isset($status) && $status === 'Đã hủy') ? 'selected' : ''); ?>>Đã hủy</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="fas fa-filter"></i> Lọc
                </button>
            </div>
        </div>
    </form>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">Tổng vé</div>
                <div class="stat-value text-primary"><?php echo e(number_format($stats['total'] ?? 0)); ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">Vé đã bán</div>
                <div class="stat-value text-success"><?php echo e(number_format($stats['sold'] ?? 0)); ?></div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">Vé hủy</div>
                <div class="stat-value text-danger"><?php echo e(number_format($stats['cancelled'] ?? 0)); ?></div>
            </div>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="stat-card">
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mã vé</th>
                        <th>Khách hàng</th>
                        <th>Phim</th>
                        <th>Phòng/Ghế</th>
                        <th>Ngày/Giờ</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($tickets)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">Không có vé nào</td>
                        </tr>
                    <?php else: ?>
                        <?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>#<?php echo e($ticket['id']); ?></td>
                                <td><code><?php echo e($ticket['ticket_code'] ?? 'N/A'); ?></code></td>
                                <td><?php echo e($ticket['user_name'] ?? 'N/A'); ?></td>
                                <td><?php echo e($ticket['movie_title'] ?? 'N/A'); ?></td>
                                <td><?php echo e($ticket['screen_name'] ?? 'N/A'); ?> - <?php echo e($ticket['seat'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php echo e(\Carbon\Carbon::parse($ticket['show_date'])->format('d/m/Y')); ?>

                                    <br>
                                    <small><?php echo e(\Carbon\Carbon::parse($ticket['show_time'])->format('H:i')); ?></small>
                                </td>
                                <td><?php echo e(number_format($ticket['price'] ?? 0)); ?>₫</td>
                                <td>
                                    <span class="badge bg-<?php echo e(($ticket['status'] ?? '') === 'Đã đặt' ? 'success' : 'danger'); ?>">
                                        <?php echo e($ticket['status'] ?? 'N/A'); ?>

                                    </span>
                                </td>
                                <td>
                                    <a href="<?php echo e(url('?route=moderator/tickets/view&id=' . $ticket['id'])); ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\CineHub_DuAnTotNghiep\resources\views\moderator\tickets.blade.php ENDPATH**/ ?>