<?php $__env->startSection('content'); ?>
<div class="stat-card">
    <h5 class="mb-4"><i class="fas fa-users"></i> Quản lý nhân viên đứng quầy</h5>

    <div class="mb-3">
        <a href="?route=moderator/counterStaffCreate" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tạo nhân viên mới
        </a>
    </div>

    <?php if(empty($counterStaff)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Chưa có nhân viên đứng quầy nào.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th><th>Tên</th><th>Email</th><th>Trạng thái</th><th>Ngày tạo</th><th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $counterStaff; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $staff): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($staff['id']); ?></td>
                        <td><?php echo e($staff['name']); ?></td>
                        <td><?php echo e($staff['email']); ?></td>
                        <td>
                            <?php if($staff['is_active']): ?>
                                <span class="badge bg-success">Hoạt động</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Vô hiệu hóa</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e(date('d/m/Y H:i', strtotime($staff['created_at']))); ?></td>
                        <td>
                            <a href="?route=moderator/counterStaffDelete&id=<?php echo e($staff['id']); ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Bạn có chắc muốn xóa nhân viên này?')">
                                <i class="fas fa-trash"></i> Xóa
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.moderator.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\CineHub_DuAnTotNghiep\resources\views\admin\moderator\counter_staff.blade.php ENDPATH**/ ?>