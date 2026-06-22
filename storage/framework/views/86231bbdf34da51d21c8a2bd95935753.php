<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Sửa rạp</h5>
    <a href="<?php echo e(route('admin.theaters.index')); ?>" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
</div>

<div class="stat-card">
    <form method="POST" action="<?php echo e(route('admin.theaters.update', $theater['id'])); ?>">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <input type="hidden" name="id" value="<?php echo e($theater['id']); ?>">
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
                <input type="number" class="form-control" id="total_screens" name="total_screens" value="<?php echo e($theater['total_screens'] ?? 1); ?>" min="1">
            </div>
            <div class="col-md-6 mb-3">
                <div class="form-check mt-4">
                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?php echo e(($theater['is_active'] ?? 1) ? 'checked' : ''); ?>>
                    <label class="form-check-label" for="is_active">Kích hoạt</label>
                </div>
            </div>
        </div>
        <div class="d-flex justify-content-end gap-2 mt-4">
            <a href="<?php echo e(route('admin.theaters.index')); ?>" class="btn btn-secondary">Hủy</a>
            <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Cập nhật</button>
        </div>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\CineHub_DuAnTotNghiep\resources\views/admin/theaters/edit.blade.php ENDPATH**/ ?>