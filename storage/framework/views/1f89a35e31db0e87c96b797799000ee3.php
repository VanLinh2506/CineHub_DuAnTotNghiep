<?php $__env->startSection('content'); ?>
<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-primary me-3"><i class="fas fa-ticket-alt"></i></div>
                <div class="stat-info">
                    <div class="stat-label">Tổng số vé</div>
                    <div class="stat-value"><?php echo e(number_format($stats['total'])); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-success me-3"><i class="fas fa-check-circle"></i></div>
                <div class="stat-info">
                    <div class="stat-label">Vé đã bán</div>
                    <div class="stat-value"><?php echo e(number_format($stats['sold'])); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-danger me-3"><i class="fas fa-times-circle"></i></div>
                <div class="stat-info">
                    <div class="stat-label">Vé đã hủy</div>
                    <div class="stat-value"><?php echo e(number_format($stats['cancelled'])); ?></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-warning me-3"><i class="fas fa-clock"></i></div>
                <div class="stat-info">
                    <div class="stat-label">Chờ thanh toán</div>
                    <div class="stat-value"><?php echo e(number_format($stats['pending'])); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(isset($stats['revenue']) && $stats['revenue'] > 0): ?>
<div class="row mb-4">
    <div class="col-md-12">
        <div class="stat-card">
            <div class="d-flex align-items-center">
                <div class="stat-icon bg-info me-3"><i class="fas fa-money-bill-wave"></i></div>
                <div class="stat-info">
                    <div class="stat-label">Tổng doanh thu</div>
                    <div class="stat-value"><?php echo e(number_format($stats['revenue'])); ?>₫</div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Filters -->
<form method="GET" class="mb-4">
    <input type="hidden" name="route" value="moderator/tickets">
    <div class="stat-card">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label">Lọc theo phim</label>
                <select name="movie_id" class="form-select">
                    <option value="">Tất cả phim</option>
                    <?php $__currentLoopData = $movies ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($movie['id']); ?>" <?php echo e(($movie_id ?? '') == $movie['id'] ? 'selected' : ''); ?>>
                            <?php echo e($movie['title']); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Lọc theo trạng thái</label>
                <select name="status" class="form-select">
                    <option value="">Tất cả trạng thái</option>
                    <option value="Đã đặt" <?php echo e(($status ?? '') === 'Đã đặt' ? 'selected' : ''); ?>>Đã đặt</option>
                    <option value="Đã hủy" <?php echo e(($status ?? '') === 'Đã hủy' ? 'selected' : ''); ?>>Đã hủy</option>
                    <option value="Chờ thanh toán" <?php echo e(($status ?? '') === 'Chờ thanh toán' ? 'selected' : ''); ?>>Chờ thanh toán</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-secondary w-100"><i class="fas fa-filter"></i> Lọc</button>
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <a href="?route=moderator/tickets" class="btn btn-outline-secondary w-100"><i class="fas fa-redo"></i> Xóa bộ lọc</a>
            </div>
        </div>
    </div>
</form>

<div class="stat-card">
    <h6 class="mb-3"><i class="fas fa-list me-2"></i>Danh sách vé</h6>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th><th>Khách hàng</th><th>Phim</th><th>Ngày/Giờ</th>
                    <th>Ghế</th><th>Giá</th><th>Trạng thái</th><th>Ngày đặt</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($tickets)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2"></i><br>Không có vé nào
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>#<?php echo e($ticket['id']); ?></td>
                        <td>
                            <div><?php echo e($ticket['user_name']); ?></div>
                            <small class="text-muted"><?php echo e($ticket['user_email']); ?></small>
                        </td>
                        <td><?php echo e($ticket['movie_title']); ?></td>
                        <td>
                            <div><?php echo e(date('d/m/Y', strtotime($ticket['show_date']))); ?></div>
                            <small class="text-muted"><?php echo e(date('H:i', strtotime($ticket['show_time']))); ?></small>
                        </td>
                        <td><span class="badge bg-info"><?php echo e($ticket['seat']); ?></span></td>
                        <td><?php echo e(number_format($ticket['price'])); ?>₫</td>
                        <td>
                            <span class="badge bg-<?php echo e($ticket['status'] === 'Đã đặt' ? 'success' : ($ticket['status'] === 'Đã hủy' ? 'danger' : 'warning')); ?>">
                                <?php echo e($ticket['status']); ?>

                            </span>
                        </td>
                        <td><?php echo e(date('d/m/Y H:i', strtotime($ticket['created_at']))); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <?php if(isset($total_pages) && $total_pages > 1): ?>
        <nav class="mt-3">
            <ul class="pagination justify-content-center">
                <?php for($i = 1; $i <= $total_pages; $i++): ?>
                    <li class="page-item <?php echo e(($page ?? 1) == $i ? 'active' : ''); ?>">
                        <a class="page-link"
                           href="?route=moderator/tickets&page=<?php echo e($i); ?>&status=<?php echo e(urlencode($status ?? '')); ?>&movie_id=<?php echo e(urlencode($movie_id ?? '')); ?>">
                            <?php echo e($i); ?>

                        </a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.moderator.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\CineHub_DuAnTotNghiep\resources\views\admin\moderator\tickets.blade.php ENDPATH**/ ?>