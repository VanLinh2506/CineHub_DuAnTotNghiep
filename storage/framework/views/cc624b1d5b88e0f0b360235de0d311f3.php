<?php
    $title = 'Quản lý phòng chiếu';
?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5><?php echo e($title); ?> - <?php echo e($theater['name'] ?? 'Theater'); ?></h5>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addScreenModal">
            <i class="fas fa-plus"></i> Thêm phòng mới
        </button>
    </div>

    <!-- Filter -->
    <div class="stat-card mb-3">
        <form method="GET" class="row g-2">
            <input type="hidden" name="route" value="moderator/screens">
            <div class="col-md-4">
                <label for="movie_filter" class="form-label">Lọc theo phim</label>
                <select name="movie_id" id="movie_filter" class="form-select" onchange="this.form.submit()">
                    <option value="">Tất cả phòng</option>
                    <?php $__currentLoopData = $movies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($movie['id']); ?>" <?php echo e((isset($movie_id) && $movie_id == $movie['id']) ? 'selected' : ''); ?>>
                            <?php echo e($movie['title']); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <a href="<?php echo e(url('?route=moderator/screens')); ?>" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-redo"></i> Xóa lọc
                </a>
            </div>
        </form>
    </div>

    <div class="stat-card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên phòng</th>
                        <th>Loại phòng</th>
                        <th>Số ghế</th>
                        <th>Phim đang chiếu</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($screens)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted">Chưa có phòng chiếu nào</td>
                        </tr>
                    <?php else: ?>
                        <?php $__currentLoopData = $screens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $screen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>#<?php echo e($screen['id']); ?></td>
                                <td><strong><?php echo e($screen['name']); ?></strong></td>
                                <td>
                                    <span class="badge bg-info"><?php echo e($screen['type'] ?? 'Standard'); ?></span>
                                </td>
                                <td><?php echo e($screen['total_seats'] ?? 0); ?> ghế</td>
                                <td><?php echo e($screen['current_movie'] ?? '-'); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo e(($screen['is_active'] ?? true) ? 'success' : 'secondary'); ?>">
                                        <?php echo e(($screen['is_active'] ?? true) ? 'Hoạt động' : 'Không hoạt động'); ?>

                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo e(url('?route=moderator/screens/edit&id=' . $screen['id'])); ?>" class="btn btn-outline-primary" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?php echo e(url('?route=moderator/screens/view&id=' . $screen['id'])); ?>" class="btn btn-outline-info" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button onclick="deleteScreen(<?php echo e($screen['id']); ?>, '<?php echo e($screen['name']); ?>')" class="btn btn-outline-danger" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Screen Modal -->
    <div class="modal fade" id="addScreenModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="<?php echo e(url('?route=moderator/screens/store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-plus me-2"></i>Thêm phòng chiếu</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Tên phòng <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="VD: Phòng A1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Loại phòng</label>
                            <select name="type" class="form-select">
                                <option value="Standard">Standard</option>
                                <option value="VIP">VIP</option>
                                <option value="IMAX">IMAX</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số lượng ghế <span class="text-danger">*</span></label>
                            <input type="number" name="total_seats" class="form-control" required min="1" placeholder="VD: 132">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i>Thêm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function deleteScreen(id, name) {
        if (confirm('Bạn chắc chắn muốn xóa phòng "' + name + '"?')) {
            window.location.href = '<?php echo e(url("?route=moderator/screens/delete&id=")); ?>' + id;
        }
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\CineHub_DuAnTotNghiep\resources\views\moderator\screens.blade.php ENDPATH**/ ?>