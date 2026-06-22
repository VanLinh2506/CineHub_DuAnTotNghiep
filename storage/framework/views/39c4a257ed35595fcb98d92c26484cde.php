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
                    <option value="Movie" <?php if(($module ?? null)==='Movie' ): echo 'selected'; endif; ?>>Phim</option>
                    <option value="Theater" <?php if(($module ?? null)==='Theater' ): echo 'selected'; endif; ?>>Rạp</option>
                    <option value="Review" <?php if(($module ?? null)==='Review' ): echo 'selected'; endif; ?>>Bình luận</option>
                    <option value="User" <?php if(($module ?? null)==='User' ): echo 'selected'; endif; ?>>Người dùng</option>
                    <option value="System" <?php if(($module ?? null)==='System' ): echo 'selected'; endif; ?>>Hệ thống</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small">Hành động</label>
                <select name="action" class="form-select" id="actionFilter" onchange="this.form.submit()">
                    <option value="">Tất cả hành động</option>
                    <option value="Thêm" <?php if(isset($action) && strpos($action, 'Thêm' ) !==false): echo 'selected'; endif; ?>>Thêm</option>
                    <option value="Xóa" <?php if(isset($action) && strpos($action, 'Xóa' ) !==false): echo 'selected'; endif; ?>>Xóa</option>
                    <option value="Cập nhật" <?php if(isset($action) && strpos($action, 'Cập nhật' ) !==false): echo 'selected'; endif; ?>>Cập nhật</option>
                    <option value="Ghim" <?php if(isset($action) && strpos($action, 'Ghim' ) !==false): echo 'selected'; endif; ?>>Ghim/Bỏ ghim</option>
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
                <a href="<?php echo e(route('admin.logs')); ?>" class="btn btn-outline-secondary w-100">
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
                        <td><?php echo e(data_get($log, 'id')); ?></td>
                        <td>
                            <strong><?php echo e(data_get($log, 'user_name', 'N/A')); ?></strong>
                            <br><small class="text-muted"><?php echo e(data_get($log, 'user_email', 'N/A')); ?></small>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo e(data_get($log, 'module') === 'Movie' ? 'primary' :
                                        (data_get($log, 'module') === 'Theater' ? 'success' :
                                        (data_get($log, 'module') === 'User' ? 'info' : 'warning'))); ?>">
                                <?php echo e(data_get($log, 'module', 'N/A')); ?>

                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo e(strpos((string) data_get($log, 'action', ''), 'Thêm') !== false ? 'success' :
                                        (strpos((string) data_get($log, 'action', ''), 'Xóa') !== false ? 'danger' :
                                        (strpos((string) data_get($log, 'action', ''), 'Cập nhật') !== false ? 'warning' : 'secondary'))); ?>">
                                <?php echo e(data_get($log, 'action', 'N/A')); ?>

                            </span>
                        </td>
                        <td>
                            <small><?php echo e(Str::limit(data_get($log, 'details', ''), 50)); ?></small>
                        </td>
                        <td>
                            <code><?php echo e(data_get($log, 'ip_address', 'N/A')); ?></code>
                        </td>
                        <td>
                            <small><?php echo e(\Carbon\Carbon::parse(data_get($log, 'created_at'))->format('d/m/Y H:i')); ?></small>
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

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\CineHub_DuAnTotNghiep\resources\views/admin/logs.blade.php ENDPATH**/ ?>