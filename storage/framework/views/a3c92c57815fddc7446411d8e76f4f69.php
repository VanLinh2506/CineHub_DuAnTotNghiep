<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Quản lý vé</h2>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Tổng số vé</div>
                    <div class="stat-value"><?php echo e(number_format($overallStats['total_tickets'] ?? 0)); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Vé đã bán</div>
                    <div class="stat-value"><?php echo e(number_format($overallStats['tickets_sold'] ?? 0)); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-danger">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Vé đã hủy</div>
                    <div class="stat-value"><?php echo e(number_format($overallStats['tickets_cancelled'] ?? 0)); ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Doanh thu</div>
                    <div class="stat-value"><?php echo e(number_format($overallStats['total_revenue'] ?? 0)); ?>₫</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="mb-4">
        <input type="hidden" name="route" value="admin/tickets">
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
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="fas fa-filter"></i> Lọc
                    </button>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <a href="<?php echo e(url('?route=admin/tickets')); ?>" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-redo"></i> Xóa bộ lọc
                    </a>
                </div>
            </div>
        </div>
    </form>

    <!-- Phân loại vé theo phim - Thống kê tồn kho -->
    <?php if(!empty($inventoryStats)): ?>
        <div class="stat-card mb-4">
            <h6 class="mb-3">
                <i class="fas fa-chart-bar me-2"></i>Phân loại vé theo phim - Thống kê tồn kho
            </h6>
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Phim</th>
                            <th>Số suất chiếu</th>
                            <th>Vé đã bán</th>
                            <th>Vé tồn kho</th>
                            <th>Giới hạn vé</th>
                            <th>Doanh thu</th>
                            <th>Tỷ lệ bán</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $inventoryStats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $total_seats = $stat['total_showtimes'] * 132;
                                $sold = $stat['tickets_sold'] ?? 0;
                                $available = $stat['tickets_available'] ?? $total_seats;
                                $revenue = $stat['total_revenue'] ?? 0;
                                $max_tickets = $stat['max_tickets'] ?? null;
                                $sell_rate = $total_seats > 0 ? round(($sold / $total_seats) * 100, 2) : 0;
                            ?>
                            <tr>
                                <td>
                                    <strong><?php echo e($stat['movie_title']); ?></strong>
                                </td>
                                <td><?php echo e(number_format($stat['total_showtimes'])); ?></td>
                                <td>
                                    <span class="badge bg-success"><?php echo e(number_format($sold)); ?></span>
                                </td>
                                <td>
                                    <span class="badge <?php echo e($available > 50 ? 'bg-info' : ($available > 20 ? 'bg-warning' : 'bg-danger')); ?>">
                                        <?php echo e(number_format($available)); ?>

                                    </span>
                                </td>
                                <td>
                                    <form method="POST" action="<?php echo e(url('?route=admin/ticketsUpdateMovie')); ?>" class="d-inline-flex align-items-center gap-2" style="min-width: 200px;">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="movie_id" value="<?php echo e($stat['movie_id']); ?>">
                                        <input type="number" 
                                               name="max_tickets" 
                                               class="form-control form-control-sm" 
                                               value="<?php echo e($max_tickets !== null ? $max_tickets : ''); ?>" 
                                               placeholder="Không giới hạn"
                                               min="0"
                                               style="width: 120px;">
                                        <button type="submit" class="btn btn-sm btn-primary" title="Cập nhật">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </form>
                                    <?php if($max_tickets !== null): ?>
                                        <small class="text-muted d-block mt-1">
                                            <i class="fas fa-info-circle"></i> Giới hạn: <?php echo e(number_format($max_tickets)); ?> vé
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e(number_format($revenue)); ?>₫</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress" style="width: 100px; height: 20px;">
                                            <div class="progress-bar" role="progressbar" style="width: <?php echo e($sell_rate); ?>%;" 
                                                 aria-valuenow="<?php echo e($sell_rate); ?>" aria-valuemin="0" aria-valuemax="100">
                                                <?php echo e($sell_rate); ?>%
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="<?php echo e(url('?route=admin/tickets&movie_id=' . $stat['movie_id'])); ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-list"></i> Chi tiết
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>

    <!-- Danh sách vé -->
    <div class="stat-card">
        <h6 class="mb-3">
            <i class="fas fa-list me-2"></i>Danh sách vé
        </h6>
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mã vé</th>
                        <th>Khách hàng</th>
                        <th>Phim</th>
                        <th>Phòng chiếu</th>
                        <th>Ghế</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                        <th>Ngày đặt</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($tickets)): ?>
                        <tr>
                            <td colspan="10" class="text-center text-muted">Không có vé nào</td>
                        </tr>
                    <?php else: ?>
                        <?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($ticket['id']); ?></td>
                                <td>
                                    <code><?php echo e($ticket['ticket_code'] ?? 'N/A'); ?></code>
                                </td>
                                <td><?php echo e($ticket['user_name'] ?? 'N/A'); ?></td>
                                <td><?php echo e($ticket['movie_title'] ?? 'N/A'); ?></td>
                                <td><?php echo e($ticket['screen_name'] ?? 'N/A'); ?></td>
                                <td><?php echo e($ticket['seat'] ?? 'N/A'); ?></td>
                                <td><?php echo e(number_format($ticket['price'] ?? 0)); ?>₫</td>
                                <td>
                                    <span class="badge bg-<?php echo e(($ticket['status'] ?? '') === 'Đã đặt' ? 'success' : 'danger'); ?>">
                                        <?php echo e($ticket['status'] ?? 'N/A'); ?>

                                    </span>
                                </td>
                                <td><?php echo e(\Carbon\Carbon::parse($ticket['created_at'])->format('d/m/Y H:i')); ?></td>
                                <td>
                                    <a href="<?php echo e(url('?route=admin/tickets/view&id=' . $ticket['id'])); ?>" class="btn btn-sm btn-outline-info">
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

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\CineHub_DuAnTotNghiep\resources\views\admin\tickets.blade.php ENDPATH**/ ?>