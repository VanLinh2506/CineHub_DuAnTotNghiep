<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="stat-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0"><i class="fas fa-tags me-2"></i>Quản lý thể loại</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                <i class="fas fa-plus me-1"></i> Thêm thể loại
            </button>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="60">ID</th>
                        <th>Tên thể loại</th>
                        <th>Thể loại cha</th>
                        <th width="120">Số phim</th>
                        <th width="150">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($categories)): ?>
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                Chưa có thể loại nào
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($category['id']); ?></td>
                                <td>
                                    <strong><?php echo e($category['name']); ?></strong>
                                </td>
                                <td>
                                    <?php if($category['parent_id']): ?>
                                        <?php
                                            $parentName = '';
                                            foreach ($categories as $parent) {
                                                if ($parent['id'] == $category['parent_id']) {
                                                    $parentName = $parent['name'];
                                                    break;
                                                }
                                            }
                                        ?>
                                        <?php echo e($parentName); ?>

                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?php echo e($category['movie_count'] ?? $category['movies_count'] ?? 0); ?> phim</span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary me-1" 
                                            onclick="editCategory(<?php echo json_encode($category, 15, 512) ?>)"
                                            title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if(($category['movie_count'] ?? $category['movies_count'] ?? 0) == 0): ?>
                                        <button class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteCategory(<?php echo e($category['id']); ?>, '<?php echo e($category['name']); ?>')"
                                                title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php else: ?>
                                        <button class="btn btn-sm btn-outline-secondary" disabled title="Không thể xóa - có phim đang sử dụng">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Thêm thể loại -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?php echo e(route('admin.categories.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Thêm thể loại mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên thể loại <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Nhập tên thể loại">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Thể loại cha (tùy chọn)</label>
                        <select name="parent_id" class="form-select">
                            <option value="">-- Không có --</option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cat['id']); ?>"><?php echo e($cat['name']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Lưu</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Sửa thể loại -->
<div class="modal fade" id="editCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST" id="editCategoryForm">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Sửa thể loại</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Tên thể loại <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="edit_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Thể loại cha (tùy chọn)</label>
                        <select name="parent_id" id="edit_parent_id" class="form-select">
                            <option value="">-- Không có --</option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cat['id']); ?>"><?php echo e($cat['name']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Xóa thể loại -->
<div class="modal fade" id="deleteCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="" method="POST" id="deleteCategoryForm">
                <?php echo csrf_field(); ?>
                <?php echo method_field('DELETE'); ?>
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-trash me-2"></i>Xóa thể loại</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn chắc chắn muốn xóa thể loại <strong id="delete_name"></strong>?</p>
                    <p class="text-danger"><i class="fas fa-exclamation-triangle"></i> Hành động này không thể hoàn tác.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash me-1"></i>Xóa</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editCategory(category) {
        const form = document.getElementById('editCategoryForm');
        form.action = "<?php echo e(route('admin.categories.update', ['id' => '__ID__'])); ?>".replace('__ID__', category.id);
        document.getElementById('edit_name').value = category.name;
        document.getElementById('edit_parent_id').value = category.parent_id || '';
        new bootstrap.Modal(document.getElementById('editCategoryModal')).show();
    }

    function deleteCategory(id, name) {
        const form = document.getElementById('deleteCategoryForm');
        form.action = "<?php echo e(route('admin.categories.destroy', ['id' => '__ID__'])); ?>".replace('__ID__', id);
        document.getElementById('delete_name').textContent = name;
        new bootstrap.Modal(document.getElementById('deleteCategoryModal')).show();
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\CineHub_DuAnTotNghiep\resources\views/admin/categories.blade.php ENDPATH**/ ?>