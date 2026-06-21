<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Quan ly phim</h2>
        <div>
            <a href="<?php echo e(route('admin.movies.scanEpisodes')); ?>" class="btn btn-info me-2">
                <i class="fas fa-folder-open"></i> Import tap tu folder
            </a>
            <a href="<?php echo e(route('admin.movies.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Them phim moi
            </a>
        </div>
    </div>

    <form method="GET" action="<?php echo e(route('admin.movies.index')); ?>" class="mb-3">
        <div class="row g-2">
            <div class="col-md-4">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Tim kiem phim..."
                    value="<?php echo e($search ?? ''); ?>"
                >
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Tat ca trang thai</option>
                    <option value="Chiếu online" <?php if(($status ?? '') === 'Chiếu online'): echo 'selected'; endif; ?>>Phim online</option>
                    <option value="Sắp chiếu" <?php if(($status ?? '') === 'Sắp chiếu'): echo 'selected'; endif; ?>>Phim sap chieu</option>
                    <option value="Chiếu rạp" <?php if(($status ?? '') === 'Chiếu rạp'): echo 'selected'; endif; ?>>Phim chieu rap</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="fas fa-search"></i> Tim
                </button>
            </div>
        </div>
    </form>

    <div class="stat-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Poster</th>
                        <th>Tieu de</th>
                        <th>Loai</th>
                        <th>The loai</th>
                        <th>Trang thai</th>
                        <th>Ngay chieu</th>
                        <th>Rating</th>
                        <th>Thao tac</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $movies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($movie->id); ?></td>
                            <td>
                                <?php if($movie->thumbnail): ?>
                                    <img src="<?php echo e($movie->thumbnail); ?>" alt="<?php echo e($movie->title); ?>" style="width: 60px; height: 90px; object-fit: cover; border-radius: 5px;">
                                <?php else: ?>
                                    <div class="bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 60px; height: 90px; border-radius: 5px;">
                                        <i class="fas fa-film"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?php echo e($movie->title); ?></strong>
                                <?php if($movie->director): ?>
                                    <br>
                                    <small class="text-muted">Dao dien: <?php echo e($movie->director); ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo e(($movie->type ?? 'phimle') === 'phimbo' ? 'primary' : 'secondary'); ?>">
                                    <?php echo e(($movie->type ?? 'phimle') === 'phimbo' ? 'Phim bo' : 'Phim le'); ?>

                                </span>
                            </td>
                            <td><?php echo e(optional($movie->category)->name ?? 'Chua gan'); ?></td>
                            <td>
                                <?php
                                    $statusBg = match($movie->status) {
                                        'Chiếu online' => 'success',
                                        'Chiếu rạp' => 'info',
                                        'Sắp chiếu' => 'warning',
                                        default => 'secondary',
                                    };
                                ?>
                                <span class="badge bg-<?php echo e($statusBg); ?>"><?php echo e($movie->status ?? 'Chua cap nhat'); ?></span>
                                <br>
                                <small class="text-muted"><?php echo e($movie->status_admin ?? 'draft'); ?></small>
                            </td>
                            <td><?php echo e(optional($movie->publish_date)->format('d/m/Y H:i') ?? '-'); ?></td>
                            <td><i class="fas fa-star text-warning"></i> <?php echo e(number_format($movie->rating ?? 0, 1)); ?>/10</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo e(route('admin.movies.edit', $movie->id)); ?>" class="btn btn-outline-primary" title="Sua">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?php echo e(route('movies.show', $movie->id)); ?>" class="btn btn-outline-info" title="Xem">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="<?php echo e(route('admin.movies.destroy', $movie->id)); ?>" method="POST" onsubmit="return confirm('Ban chac chan muon xoa phim <?php echo e(addslashes($movie->title)); ?>?')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-outline-danger" title="Xoa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">Khong co phim nao</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\CineHub_DuAnTotNghiep\resources\views/admin/movies.blade.php ENDPATH**/ ?>