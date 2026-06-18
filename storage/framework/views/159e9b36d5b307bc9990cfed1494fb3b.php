<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5>Quản lý Combo & Đồ ăn</h5>
        <?php if(isset($theater)): ?>
            <small class="text-muted">Rạp: <strong><?php echo e($theater['name'] ?? ''); ?></strong></small>
        <?php endif; ?>
    </div>
    <a href="?route=moderator/foodItemsCreate" class="btn btn-primary">
        <i class="fas fa-plus"></i> Thêm mới
    </a>
</div>

<div class="stat-card mb-3">
    <form method="GET" class="row g-2">
        <input type="hidden" name="route" value="moderator/foodItems">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tên, mô tả..." value="<?php echo e($search ?? ''); ?>">
        </div>
        <div class="col-md-3">
            <select name="type" class="form-select" onchange="this.form.submit()">
                <option value="">Tất cả loại</option>
                <option value="combo" <?php echo e((isset($type) && $type === 'combo') ? 'selected' : ''); ?>>Combo</option>
                <option value="snack" <?php echo e((isset($type) && $type === 'snack') ? 'selected' : ''); ?>>Snack</option>
                <option value="drink" <?php echo e((isset($type) && $type === 'drink') ? 'selected' : ''); ?>>Đồ uống</option>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-secondary w-100"><i class="fas fa-search"></i> Tìm</button>
        </div>
        <div class="col-md-2">
            <a href="?route=moderator/foodItems" class="btn btn-outline-secondary w-100"><i class="fas fa-redo"></i> Xóa lọc</a>
        </div>
    </form>
</div>

<div class="stat-card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr><th>ID</th><th>Ảnh</th><th>Tên</th><th>Mô tả</th><th>Loại</th><th>Giá</th><th>Trạng thái</th><th>Thao tác</th></tr>
            </thead>
            <tbody>
                <?php if(empty($foodItems)): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                            Chưa có combo/đồ ăn nào. <a href="?route=moderator/foodItemsCreate" class="text-primary">Thêm mới ngay</a>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php $__currentLoopData = $foodItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($item['id']); ?></td>
                        <td>
                            <?php if($item['image']): ?>
                                <img src="<?php echo e($item['image']); ?>" alt="<?php echo e($item['name']); ?>" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                            <?php else: ?>
                                <div style="width: 60px; height: 60px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image text-muted"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($item['name']); ?></td>
                        <td><?php echo e($item['description'] ?? ''); ?></td>
                        <td>
                            <span class="badge bg-<?php echo e($item['type'] === 'combo' ? 'primary' : ($item['type'] === 'snack' ? 'warning' : 'info')); ?>">
                                <?php echo e($item['type'] === 'combo' ? 'Combo' : ($item['type'] === 'snack' ? 'Snack' : 'Đồ uống')); ?>

                            </span>
                        </td>
                        <td><?php echo e(number_format($item['price'])); ?>₫</td>
                        <td>
                            <span class="badge bg-<?php echo e($item['is_active'] ? 'success' : 'secondary'); ?>">
                                <?php echo e($item['is_active'] ? 'Hoạt động' : 'Ngừng bán'); ?>

                            </span>
                        </td>
                        <td>
                            <a href="?route=moderator/foodItemsEdit&id=<?php echo e($item['id']); ?>" class="btn btn-sm btn-outline-primary me-1">
                                <i class="fas fa-edit"></i> Sửa
                            </a>
                            <a href="?route=moderator/foodItemsDelete&id=<?php echo e($item['id']); ?>"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Bạn chắc chắn muốn xóa combo/đồ ăn này?')">
                                <i class="fas fa-trash"></i> Xóa
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.moderator.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\CineHub_DuAnTotNghiep\resources\views\admin\moderator\food_items.blade.php ENDPATH**/ ?>