<?php
    $title = 'Lịch sử bán hàng';
?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h5 class="mb-4"><?php echo e($title); ?></h5>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Tổng vé hôm nay</div>
                <div class="stat-value text-primary counter" data-target="<?php echo e($stats['today_tickets'] ?? 0); ?>">0</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Doanh thu hôm nay</div>
                <div class="stat-value text-success revenue-counter" data-target="<?php echo e($stats['today_revenue'] ?? 0); ?>">0₫</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Tổng vé tháng này</div>
                <div class="stat-value text-info counter" data-target="<?php echo e($stats['month_tickets'] ?? 0); ?>">0</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Doanh thu tháng này</div>
                <div class="stat-value text-warning revenue-counter" data-target="<?php echo e($stats['month_revenue'] ?? 0); ?>">0₫</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="mb-3">
        <input type="hidden" name="route" value="counter_staff/salesHistory">
        <div class="row g-2">
            <div class="col-md-3">
                <input type="date" name="date_from" class="form-control" placeholder="Từ ngày"
                       value="<?php echo e($date_from ?? ''); ?>">
            </div>
            <div class="col-md-3">
                <input type="date" name="date_to" class="form-control" placeholder="Đến ngày"
                       value="<?php echo e($date_to ?? ''); ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="fas fa-filter"></i> Lọc
                </button>
            </div>
            <div class="col-md-2">
                <a href="<?php echo e(url('?route=counter_staff/salesHistory')); ?>" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-redo"></i> Xóa
                </a>
            </div>
        </div>
    </form>

    <!-- Sales Table -->
    <div class="stat-card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mã vé</th>
                        <th>Khách hàng</th>
                        <th>Phim</th>
                        <th>Suất chiếu</th>
                        <th>Ghế</th>
                        <th>Giá</th>
                        <th>Ngày bán</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($sales)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">Không có bán hàng nào</td>
                        </tr>
                    <?php else: ?>
                        <?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>#<?php echo e($sale['id']); ?></td>
                                <td><code><?php echo e($sale['ticket_code']); ?></code></td>
                                <td><?php echo e($sale['user_name']); ?></td>
                                <td><?php echo e($sale['movie_title']); ?></td>
                                <td>
                                    <?php echo e(\Carbon\Carbon::parse($sale['show_date'])->format('d/m/Y')); ?>

                                    <br><small><?php echo e(\Carbon\Carbon::parse($sale['show_time'])->format('H:i')); ?></small>
                                </td>
                                <td><?php echo e($sale['seat']); ?></td>
                                <td><strong><?php echo e(number_format($sale['price'])); ?>₫</strong></td>
                                <td><?php echo e(\Carbon\Carbon::parse($sale['created_at'])->format('d/m/Y H:i')); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo e(($sale['status'] ?? '') === 'Đã đặt' ? 'success' : 'danger'); ?>">
                                        <?php echo e($sale['status'] ?? 'N/A'); ?>

                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Export -->
    <div class="mt-3">
        <a href="<?php echo e(url('?route=counter_staff/exportSales')); ?>" class="btn btn-success">
            <i class="fas fa-download"></i> Xuất Excel
        </a>
    </div>
</div>

<script>
    document.querySelectorAll('.counter').forEach(element => {
        const target = parseInt(element.dataset.target);
        let current = 0;
        const increment = target / 100;
        
        const updateCounter = () => {
            if (current < target) {
                current += increment;
                element.textContent = Math.floor(current);
                setTimeout(updateCounter, 20);
            } else {
                element.textContent = target;
            }
        };
        
        updateCounter();
    });

    document.querySelectorAll('.revenue-counter').forEach(element => {
        const target = parseInt(element.dataset.target);
        let current = 0;
        const increment = target / 100;
        
        const updateCounter = () => {
            if (current < target) {
                current += increment;
                element.textContent = Math.floor(current).toLocaleString('vi-VN') + '₫';
                setTimeout(updateCounter, 20);
            } else {
                element.textContent = target.toLocaleString('vi-VN') + '₫';
            }
        };
        
        updateCounter();
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\CineHub_DuAnTotNghiep\resources\views\counter_staff\sales_history.blade.php ENDPATH**/ ?>