<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Quản lý Combo & Đồ ăn</h2>
        <a href="<?php echo e(route('admin.foodItems.create')); ?>" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm mới
        </a>
    </div>

    <!-- Filters -->
    <div class="stat-card mb-3">
        <form method="GET" class="row g-2">
            <input type="hidden" name="route" value="admin/foodItems">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm tên, mô tả..." 
                       value="<?php echo e(htmlspecialchars($search ?? '')); ?>">
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
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="fas fa-search"></i> Tìm
                </button>
            </div>
            <div class="col-md-2">
                <a href="<?php echo e(route('admin.foodItems.index')); ?>" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-redo"></i> Xóa lọc
                </a>
            </div>
        </form>
    </div>

    <!-- Food Items Table -->
    <div class="stat-card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ảnh</th>
                        <th>Tên</th>
                        <th>Mô tả</th>
                        <th>Loại</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($foodItems)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">Chưa có combo/đồ ăn nào</td>
                        </tr>
                    <?php else: ?>
                        <?php $__currentLoopData = $foodItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($item['id']); ?></td>
                                <td>
                                    <?php if($item['image'] ?? null): ?>
                                        <img src="<?php echo e($item['image']); ?>" 
                                             alt="<?php echo e($item['name']); ?>" 
                                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                                    <?php else: ?>
                                        <div style="width: 60px; height: 60px; background: #f0f0f0; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($item['name']); ?></td>
                                <td><?php echo e($item['description'] ?? ''); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo e($item['type'] === 'combo' ? 'primary' : 
                                        ($item['type'] === 'snack' ? 'warning' : 'info')); ?>">
                                        <?php echo e($item['type'] === 'combo' ? 'Combo' : ($item['type'] === 'snack' ? 'Snack' : 'Đồ uống')); ?>

                                    </span>
                                </td>
                                <td><?php echo e(number_format($item['price'] ?? 0)); ?>₫</td>
                                <td>
                                    <span class="badge bg-<?php echo e(($item['is_available'] ?? true) ? 'success' : 'danger'); ?>">
                                        <?php echo e(($item['is_available'] ?? true) ? 'Có sẵn' : 'Tạm hết'); ?>

                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo e(route('admin.foodItems.edit', $item['id'])); ?>" class="btn btn-outline-primary" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="<?php echo e(route('admin.foodItems.destroy', $item['id'])); ?>" class="d-inline" onsubmit="return confirm('Bạn chắc chắn muốn xóa &quot;<?php echo e($item['name']); ?>&quot;?')">
                                            <?php echo csrf_field(); ?>
                                            <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-outline-danger" title="Xóa">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\CineHub_DuAnTotNghiep\resources\views/admin/food_items.blade.php ENDPATH**/ ?>