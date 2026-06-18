<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-history"></i> Lịch sử hoạt động</h2>
        <small style="color: #fff;">Xem lịch sử thêm, xóa, cập nhật phim, rạp, bình luận</small>
    </div>

    <!-- Filters -->
    <form method="GET" class="mb-3">
        <input type="hidden" name="route" value="admin/logs">
        <div class="row g-2">
            <div class="col-md-4">
                <label class="form-label small">Module</label>
                <select name="module" class="form-select" id="moduleFilter" onchange="this.form.submit()">
                    <option value="">Tất cả modules</option>
                    <option value="Movie" <?php echo e((isset($module) && $module === 'Movie') ? 'selected' : ''); ?>>Phim</option>
                    <option value="Theater" <?php echo e((isset($module) && $module === 'Theater') ? 'selected' : ''); ?>>Rạp</option>
                    <option value="Review" <?php echo e((isset($module) && $module === 'Review') ? 'selected' : ''); ?>>Bình luận</option>
                    <option value="User" <?php echo e((isset($module) && $module === 'User') ? 'selected' : ''); ?>>Người dùng</option>
                    <option value="System" <?php echo e((isset($module) && $module === 'System') ? 'selected' : ''); ?>>Hệ thống</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small">Hành động</label>
                <select name="action" class="form-select" id="actionFilter" onchange="this.form.submit()">
                    <option value="">Tất cả hành động</option>
                    <option value="Thêm" <?php echo e((isset($action) && strpos($action, 'Thêm') !== false) ? 'selected' : ''); ?>>Thêm</option>
                    <option value="Xóa" <?php echo e((isset($action) && strpos($action, 'Xóa') !== false) ? 'selected' : ''); ?>>Xóa</option>
                    <option value="Cập nhật" <?php echo e((isset($action) && strpos($action, 'Cập nhật') !== false) ? 'selected' : ''); ?>>Cập nhật</option>
                    <option value="Ghim" <?php echo e((isset($action) && strpos($action, 'Ghim') !== false) ? 'selected' : ''); ?>>Ghim/Bỏ ghim</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">&nbsp;</label>
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="fas fa-filter"></i> Lọc
                </button>
            </div>
            <div class="col-md-2">
                <label class="form-label small">&nbsp;</label>
                <a href="<?php echo e(url('?route=admin/logs')); ?>" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-redo"></i> Xóa lọc
                </a>
            </div>
        </div>
    </form>

    <!-- Logs Table -->
    <div class="stat-card">
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Người dùng</th>
                        <th>Module</th>
                        <th>Hành động</th>
                        <th>Chi tiết</th>
                        <th>IP Address</th>
                        <th>Thời gian</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($logs)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">Không có log nào</td>
                        </tr>
                    <?php else: ?>
                        <?php $__currentLoopData = $logs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($log['id']); ?></td>
                                <td>
                                    <strong><?php echo e($log['user_name'] ?? 'N/A'); ?></strong>
                                    <br><small class="text-muted"><?php echo e($log['user_email'] ?? 'N/A'); ?></small>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo e($log['module'] === 'Movie' ? 'primary' : 
                                        ($log['module'] === 'Theater' ? 'success' : 
                                        ($log['module'] === 'User' ? 'info' : 'warning'))); ?>">
                                        <?php echo e($log['module'] ?? 'N/A'); ?>

                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo e(strpos($log['action'], 'Thêm') !== false ? 'success' : 
                                        (strpos($log['action'], 'Xóa') !== false ? 'danger' : 
                                        (strpos($log['action'], 'Cập nhật') !== false ? 'warning' : 'secondary'))); ?>">
                                        <?php echo e($log['action'] ?? 'N/A'); ?>

                                    </span>
                                </td>
                                <td>
                                    <small><?php echo e(Str::limit($log['details'] ?? '', 50)); ?></small>
                                </td>
                                <td>
                                    <code><?php echo e($log['ip_address'] ?? 'N/A'); ?></code>
                                </td>
                                <td>
                                    <small><?php echo e(\Carbon\Carbon::parse($log['created_at'])->format('d/m/Y H:i')); ?></small>
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

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\CineHub_DuAnTotNghiep\resources\views\admin\logs.blade.php ENDPATH**/ ?>