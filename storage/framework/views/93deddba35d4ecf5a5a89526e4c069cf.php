<?php $__env->startSection('content'); ?>
<!-- Date Filter -->
<div class="row mb-4">
    <div class="col-12">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0" style="color: #333; font-weight: 600;">
                    <i class="fas fa-calendar-alt text-primary me-2"></i>Lịch chiếu phim
                </h5>
                <form method="GET" class="d-flex gap-2">
                    <input type="hidden" name="route" value="counterStaff/showtimes">
                    <input type="date" name="date" value="<?php echo e($date); ?>" class="form-control" style="width: 200px;" onchange="this.form.submit()">
                </form>
            </div>
        </div>
    </div>
</div>

<?php if(empty($showtimes)): ?>
    <div class="stat-card">
        <div class="text-center py-5">
            <i class="fas fa-calendar-times text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-3 mb-0">Không có suất chiếu nào vào ngày <?php echo e(date('d/m/Y', strtotime($date))); ?></p>
        </div>
    </div>
<?php else: ?>
    <div class="row">
        <?php $__currentLoopData = $showtimes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $showtime): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="stat-card showtime-card animated-card" style="transition: all 0.3s ease;">
                    <div class="d-flex align-items-start mb-3">
                        <?php if($showtime['thumbnail']): ?>
                            <img src="<?php echo e($showtime['thumbnail']); ?>" alt="<?php echo e($showtime['movie_title']); ?>"
                                 class="me-3" style="width: 80px; height: 120px; object-fit: cover; border-radius: 8px;">
                        <?php else: ?>
                            <div class="me-3 d-flex align-items-center justify-content-center bg-light"
                                 style="width: 80px; height: 120px; border-radius: 8px;">
                                <i class="fas fa-film text-muted" style="font-size: 2rem;"></i>
                            </div>
                        <?php endif; ?>
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold" style="color: #333;"><?php echo e($showtime['movie_title']); ?></h6>
                            <small class="text-muted d-block mb-2">
                                <i class="fas fa-clock me-1"></i><?php echo e($showtime['duration'] ?? 'N/A'); ?> phút
                            </small>
                            <span class="badge bg-primary mb-2"><?php echo e($showtime['screen_type'] ?? '2D'); ?></span>
                        </div>
                    </div>
                    <div class="border-top pt-3">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block">Phòng chiếu</small>
                                <strong style="color: #333;"><i class="fas fa-door-open text-primary me-1"></i><?php echo e($showtime['screen_name']); ?></strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Giờ chiếu</small>
                                <strong style="color: #333;"><i class="fas fa-clock text-success me-1"></i><?php echo e(date('H:i', strtotime($showtime['show_time']))); ?></strong>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block">Giá vé</small>
                                <strong class="text-success" style="font-size: 1.1rem;"><?php echo e(number_format($showtime['price'])); ?>₫</strong>
                            </div>
                            <div class="col-6">
                                <?php
                                    $available = $showtime['available_seats'] ?? 0;
                                    $total = $showtime['total_seats'] ?? 0;
                                    $percentage = $total > 0 ? ($available / $total) * 100 : 0;
                                ?>
                                <small class="text-muted d-block">Ghế còn lại</small>
                                <strong style="color: #333;">
                                    <span class="<?php echo e($percentage < 20 ? 'text-danger' : ($percentage < 50 ? 'text-warning' : 'text-success')); ?>">
                                        <?php echo e($available); ?>/<?php echo e($total); ?>

                                    </span>
                                </strong>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="progress" style="height: 8px; border-radius: 4px;">
                                <div class="progress-bar <?php echo e($percentage < 20 ? 'bg-danger' : ($percentage < 50 ? 'bg-warning' : 'bg-success')); ?>"
                                     role="progressbar" style="width: <?php echo e($percentage); ?>%"
                                     aria-valuenow="<?php echo e($percentage); ?>" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted"><i class="fas fa-users me-1"></i>Đã đặt: <?php echo e($showtime['booked_seats']); ?> vé</small>
                            <span class="badge <?php echo e($percentage < 20 ? 'bg-danger' : ($percentage < 50 ? 'bg-warning' : 'bg-success')); ?>">
                                <?php echo e(number_format($percentage, 0)); ?>% còn trống
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
.showtime-card { transition: all 0.3s ease; cursor: pointer; }
.showtime-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important; }
.animated-card { animation: fadeInUp 0.5s ease-out forwards; opacity: 0; }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.animated-card').forEach((card, index) => {
        setTimeout(() => { card.style.opacity = '1'; }, index * 100);
    });
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.counter_staff.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\CineHub_DuAnTotNghiep\resources\views\admin\counter_staff\showtimes.blade.php ENDPATH**/ ?>