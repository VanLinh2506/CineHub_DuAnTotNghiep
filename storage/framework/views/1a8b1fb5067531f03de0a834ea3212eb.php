<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Quản lý phim</h2>
        <div>
            <a href="<?php echo e(route('admin.movies.scanEpisodes')); ?>" class="btn btn-info me-2">
                <i class="fas fa-folder-open"></i> Import tập từ folder
            </a>
            <a href="<?php echo e(route('admin.movies.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm phim mới
            </a>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="mb-3">
        <input type="hidden" name="route" value="admin/movies">
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm phim..." 
                       value="<?php echo e(old('search') ?? ($search ?? '')); ?>">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select" id="statusFilter" onchange="this.form.submit()">
                    <option value="">Tất cả trạng thái</option>
                    <option value="Chiếu online" <?php echo e((isset($status) && $status === 'Chiếu online') ? 'selected' : ''); ?>>Phim online</option>
                    <option value="Sắp chiếu" <?php echo e((isset($status) && $status === 'Sắp chiếu') ? 'selected' : ''); ?>>Phim sắp chiếu</option>
                    <option value="Chiếu rạp" <?php echo e((isset($status) && $status === 'Chiếu rạp') ? 'selected' : ''); ?>>Phim chiếu rạp</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="fas fa-search"></i> Tìm
                </button>
            </div>
        </div>
    </form>

    <!-- Movies Table -->
    <div class="stat-card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Poster</th>
                        <th>Tiêu đề</th>
                        <th>Loại</th>
                        <th>Thể loại</th>
                        <th>Trạng thái</th>
                        <th>Rating</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($movies)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">Không có phim nào</td>
                        </tr>
                    <?php else: ?>
                        <?php $__currentLoopData = $movies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($m['id']); ?></td>
                                <td>
                                    <?php if($m['thumbnail'] ?? null): ?>
                                        <img src="<?php echo e($m['thumbnail']); ?>" alt="" style="width: 60px; height: 90px; object-fit: cover; border-radius: 5px;">
                                    <?php else: ?>
                                        <div class="bg-secondary d-flex align-items-center justify-content-center" style="width: 60px; height: 90px; border-radius: 5px;">
                                            <i class="fas fa-film text-white"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?php echo e($m['title']); ?></strong>
                                    <?php if($m['director'] ?? null): ?>
                                        <br><small class="text-muted">Đạo diễn: <?php echo e($m['director']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                        $movieType = $m['type'] ?? 'phimle';
                                    ?>
                                    <?php if($movieType === 'phimbo'): ?>
                                        <span class="badge bg-primary">Phim bộ</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Phim lẻ</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e($m['category_name'] ?? 'N/A'); ?></td>
                                <td>
                                    <?php
                                        $movieStatus = $m['status'] ?? 'Sắp chiếu';
                                        $statusBg = match($movieStatus) {
                                            'Chiếu online' => 'success',
                                            'Chiếu rạp' => 'info',
                                            'Sắp chiếu' => 'warning',
                                            default => 'secondary'
                                        };
                                    ?>
                                    <span class="badge bg-<?php echo e($statusBg); ?>"><?php echo e($movieStatus); ?></span>
                                    <?php if($m['status_admin'] ?? null): ?>
                                        <br><small class="text-muted"><?php echo e($m['status_admin']); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <i class="fas fa-star" style="color: gold;"></i> <?php echo e($m['rating'] ?? 0); ?>/10
                                </td>
                                <td><?php echo e(\Carbon\Carbon::parse($m['created_at'])->format('d/m/Y')); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo e(route('admin.movies.edit', $m['id'])); ?>" class="btn btn-outline-primary" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?php echo e(route('movies.show', $m['id'])); ?>" class="btn btn-outline-info" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form method="POST" action="<?php echo e(route('admin.movies.destroy', $m['id'])); ?>" class="d-inline" onsubmit="return confirm('Bạn chắc chắn muốn xóa phim &quot;<?php echo e($m['title']); ?>&quot;?')">
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

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\CineHub_DuAnTotNghiep\resources\views/admin/movies.blade.php ENDPATH**/ ?>