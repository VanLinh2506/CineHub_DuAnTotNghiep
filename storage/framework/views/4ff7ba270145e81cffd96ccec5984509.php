<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Thông tin rạp</h5>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="stat-card">
            <h5 class="mb-3">Chỉnh sửa thông tin rạp</h5>
            <form method="POST" action="?route=moderator/theaterUpdate">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="name" class="form-label">Tên rạp <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo e($theater['name']); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="location" class="form-label">Tỉnh/Thành phố</label>
                        <input type="text" class="form-control" id="location" name="location" value="<?php echo e($theater['location'] ?? ''); ?>" placeholder="VD: Hà Nội, TP.HCM">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="address" class="form-label">Địa chỉ chi tiết</label>
                        <textarea class="form-control" id="address" name="address" rows="2" placeholder="VD: 72 Lê Thánh Tôn, Hoàn Kiếm, Hà Nội"><?php echo e($theater['address'] ?? ''); ?></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="phone" class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control" id="phone" name="phone" value="<?php echo e($theater['phone'] ?? ''); ?>" placeholder="VD: 0241234567">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="total_screens" class="form-label">Số phòng chiếu</label>
                        <input type="number" class="form-control" id="total_screens" name="total_screens" value="<?php echo e($theater['total_screens']); ?>" min="1" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Cập nhật thông tin
                </button>
            </form>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <h6 class="mb-3">Phòng chiếu</h6>
            <?php if(empty($screens)): ?>
                <p class="text-muted">Chưa có phòng chiếu nào</p>
            <?php else: ?>
                <ul class="list-unstyled">
                    <?php $__currentLoopData = $screens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $screen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <li class="mb-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-door-open"></i> <?php echo e($screen['screen_name']); ?></span>
                            <span class="badge bg-info"><?php echo e($screen['total_seats']); ?> ghế</span>
                        </div>
                    </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.moderator.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\CineHub_DuAnTotNghiep\resources\views\admin\moderator\theater.blade.php ENDPATH**/ ?>