<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Hỗ trợ khách hàng</h2>
    </div>

    <!-- Filters -->
    <form method="GET" class="mb-3">
        <input type="hidden" name="route" value="admin/support">
        <div class="row g-2">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Tất cả trạng thái</option>
                    <option value="Mới" <?php echo e(($status ?? '') === 'Mới' ? 'selected' : ''); ?>>Mới</option>
                    <option value="Đang xử lý" <?php echo e(($status ?? '') === 'Đang xử lý' ? 'selected' : ''); ?>>Đang xử lý</option>
                    <option value="Đã giải quyết" <?php echo e(($status ?? '') === 'Đã giải quyết' ? 'selected' : ''); ?>>Đã giải quyết</option>
                    <option value="Đã đóng" <?php echo e(($status ?? '') === 'Đã đóng' ? 'selected' : ''); ?>>Đã đóng</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select">
                    <option value="">Tất cả phạm vi hỗ trợ</option>
                    <option value="Mua bán vé" <?php echo e(($category ?? '') === 'Mua bán vé' ? 'selected' : ''); ?>>Mua bán vé</option>
                    <option value="Lỗi mua bán vé" <?php echo e(($category ?? '') === 'Lỗi mua bán vé' ? 'selected' : ''); ?>>Lỗi mua bán vé</option>
                    <option value="Lỗi về phim" <?php echo e(($category ?? '') === 'Lỗi về phim' ? 'selected' : ''); ?>>Lỗi về phim</option>
                    <option value="Đăng nhập/Đăng xuất" <?php echo e(($category ?? '') === 'Đăng nhập/Đăng xuất' ? 'selected' : ''); ?>>Đăng nhập/Đăng xuất</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="fas fa-filter"></i> Lọc
                </button>
            </div>
        </div>
    </form>

    <!-- Support Tickets Table -->
    <div class="stat-card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Người dùng</th>
                        <th>Gói thành viên</th>
                        <th>Tiêu đề</th>
                        <th>Trạng thái</th>
                        <th>Phạm vi hỗ trợ</th>
                        <th>Người xử lý</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($tickets)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">Không có ticket nào</td>
                        </tr>
                    <?php else: ?>
                        <?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>#<?php echo e($ticket['id']); ?></td>
                                <td>
                                    <strong><?php echo e($ticket['user_name'] ?? 'N/A'); ?></strong>
                                    <br><small class="text-muted"><?php echo e($ticket['user_email'] ?? 'N/A'); ?></small>
                                </td>
                                <td><?php echo e($ticket['subscription_name'] ?? 'Chưa có'); ?></td>
                                <td>
                                    <strong><?php echo e($ticket['title'] ?? 'N/A'); ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo e($ticket['status'] === 'Mới' ? 'warning' : 
                                        ($ticket['status'] === 'Đang xử lý' ? 'info' : 
                                        ($ticket['status'] === 'Đã giải quyết' ? 'success' : 'secondary'))); ?>">
                                        <?php echo e($ticket['status'] ?? 'N/A'); ?>

                                    </span>
                                </td>
                                <td><?php echo e($ticket['category'] ?? 'N/A'); ?></td>
                                <td><?php echo e($ticket['admin_name'] ?? '-'); ?></td>
                                <td><?php echo e(\Carbon\Carbon::parse($ticket['created_at'])->format('d/m/Y H:i')); ?></td>
                                <td>
                                    <a href="<?php echo e(url('?route=admin/support/view&id=' . $ticket['id'])); ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> Xem
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

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\CineHub_DuAnTotNghiep\resources\views\admin\support.blade.php ENDPATH**/ ?>