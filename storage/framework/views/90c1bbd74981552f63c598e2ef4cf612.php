<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Quản lý rạp chiếu</h2>
        <?php if(!isset(auth()->user()['role']) || auth()->user()['role'] !== 'moderator'): ?>
            <a href="<?php echo e(url('?route=admin/theaters/create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm rạp mới
            </a>
        <?php endif; ?>
    </div>

    <div class="row">
        <?php if(empty($theaters)): ?>
            <div class="col-12">
                <div class="stat-card text-center">
                    <p class="text-muted">Chưa có rạp nào</p>
                </div>
            </div>
        <?php else: ?>
            <?php $__currentLoopData = $theaters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $theater): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="col-md-4 mb-3">
                    <div class="stat-card">
                        <?php if(!empty($theater['image'])): ?>
                            <div class="mb-3" style="width: 100%; height: 200px; overflow: hidden; border-radius: 8px; margin-bottom: 15px;">
                                <img src="<?php echo e($theater['image']); ?>" 
                                     alt="<?php echo e($theater['name']); ?>" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        <?php else: ?>
                            <div class="mb-3" style="width: 100%; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                                <i class="fas fa-building" style="font-size: 3rem; color: rgba(255,255,255,0.5);"></i>
                            </div>
                        <?php endif; ?>
                        <h6 style="color: #000; font-weight: bold; margin-bottom: 10px;"><?php echo e($theater['name']); ?></h6>
                        <p class="mb-2" style="color: #000;">
                            <i class="fas fa-map-marker-alt"></i> <?php echo e($theater['location'] ?? 'N/A'); ?>

                        </p>
                        <?php if($theater['phone'] ?? null): ?>
                            <p class="mb-2" style="color: #000;">
                                <i class="fas fa-phone"></i> <?php echo e($theater['phone']); ?>

                            </p>
                        <?php endif; ?>
                        <div class="mt-3">
                            <a href="<?php echo e(url('?route=admin/theaters/view&id=' . $theater['id'])); ?>" class="btn btn-sm btn-outline-primary w-100">
                                <i class="fas fa-eye"></i> Xem thông tin rạp
                            </a>
                            <?php
                                $user = auth()->user();
                                $canEdit = false;
                                if (isset($user['role']) && $user['role'] === 'moderator' && isset($user['theater_id']) && $user['theater_id'] == $theater['id']) {
                                    $canEdit = true;
                                }
                            ?>
                            <?php if($canEdit || (isset($user['role']) && $user['role'] === 'admin')): ?>
                                <div class="mt-2">
                                    <a href="<?php echo e(url('?route=admin/theaters/edit&id=' . $theater['id'])); ?>" class="btn btn-sm btn-outline-warning w-100">
                                        <i class="fas fa-edit"></i> Chỉnh sửa
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\CineHub_DuAnTotNghiep\resources\views\admin\theaters.blade.php ENDPATH**/ ?>