<?php $__env->startSection('content'); ?>
<div class="profile-container" style="padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
    <div class="profile-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px; border-radius: 15px; margin-bottom: 30px; color: white;">
        <div style="display: flex; align-items: center; gap: 30px;">
            <div class="avatar" style="width: 100px; height: 100px; border-radius: 50%; background: white; display: flex; align-items: center; justify-content: center; font-size: 40px; color: #667eea;">
                <?php if($user->avatar): ?>
                    <img src="<?php echo e(Storage::url($user->avatar)); ?>" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                <?php else: ?>
                    <i class="fas fa-user"></i>
                <?php endif; ?>
            </div>
            <div style="flex: 1;">
                <h1 style="font-size: 32px; margin-bottom: 10px;"><?php echo e($user->name); ?></h1>
                <p style="font-size: 16px; opacity: 0.9; margin-bottom: 5px;">
                    <i class="fas fa-envelope"></i> <?php echo e($user->email); ?>

                </p>
                <p style="font-size: 16px; opacity: 0.9;">
                    <i class="fas fa-shield-alt"></i> <?php echo e($userRole); ?>

                </p>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 36px; font-weight: bold;"><?php echo e(number_format($balance)); ?></div>
                <div style="font-size: 14px; opacity: 0.9;">Điểm tích lũy</div>
            </div>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            <i class="fas fa-check-circle"></i> <?php echo e(session('success')); ?>

        </div>
    <?php endif; ?>

    <?php if(session('error')): ?>
        <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <i class="fas fa-exclamation-circle"></i> <?php echo e(session('error')); ?>

        </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
        <!-- Subscription Card -->
        <div class="card" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h3 style="font-size: 20px; margin-bottom: 20px; color: #333;">
                <i class="fas fa-crown" style="color: #ffd700;"></i> Gói thành viên
            </h3>
            <?php if($subscription): ?>
                <div style="font-size: 24px; font-weight: bold; color: #667eea; margin-bottom: 10px;">
                    <?php echo e($subscription->name); ?>

                </div>
                <p style="color: #666; font-size: 14px;"><?php echo e($subscription->description); ?></p>
            <?php else: ?>
                <p style="color: #999;">Chưa có gói thành viên</p>
            <?php endif; ?>
        </div>

        <!-- Watch History -->
        <div class="card" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h3 style="font-size: 20px; margin-bottom: 20px; color: #333;">
                <i class="fas fa-history"></i> Lịch sử xem
            </h3>
            <div style="font-size: 32px; font-weight: bold; color: #667eea; margin-bottom: 10px;">
                <?php echo e($history->count()); ?>

            </div>
            <p style="color: #666; font-size: 14px;">Phim đã xem gần đây</p>
        </div>

        <!-- Tickets -->
        <div class="card" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h3 style="font-size: 20px; margin-bottom: 20px; color: #333;">
                <i class="fas fa-ticket-alt"></i> Vé đã đặt
            </h3>
            <div style="font-size: 32px; font-weight: bold; color: #667eea; margin-bottom: 10px;">
                <?php echo e($tickets->count()); ?>

            </div>
            <p style="color: #666; font-size: 14px;">Tổng số vé</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div style="margin-top: 40px;">
        <h3 style="font-size: 24px; margin-bottom: 20px; color: #333;">Thao tác nhanh</h3>
        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            <a href="<?php echo e(route('booking.history')); ?>" class="btn" style="display: inline-block; padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 8px; font-weight: 500;">
                <i class="fas fa-history"></i> Lịch sử đặt vé
            </a>
            <a href="<?php echo e(route('movies.index')); ?>" class="btn" style="display: inline-block; padding: 12px 24px; background: #764ba2; color: white; text-decoration: none; border-radius: 8px; font-weight: 500;">
                <i class="fas fa-film"></i> Xem phim
            </a>
            <?php if($isAdmin): ?>
                <a href="<?php echo e(route('admin.index')); ?>" class="btn" style="display: inline-block; padding: 12px 24px; background: #f093fb; color: white; text-decoration: none; border-radius: 8px; font-weight: 500;">
                    <i class="fas fa-cog"></i> Admin Panel
                </a>
            <?php endif; ?>
            <?php if($isModerator): ?>
                <a href="<?php echo e(route('moderator.index')); ?>" class="btn" style="display: inline-block; padding: 12px 24px; background: #4facfe; color: white; text-decoration: none; border-radius: 8px; font-weight: 500;">
                    <i class="fas fa-building"></i> Quản lý rạp
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Recent History -->
    <?php if($history->count() > 0): ?>
        <div style="margin-top: 40px;">
            <h3 style="font-size: 24px; margin-bottom: 20px; color: #333;">Phim xem gần đây</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 20px;">
                <?php $__currentLoopData = $history->take(6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="movie-card" style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        <?php if($item->movie): ?>
                            <a href="<?php echo e(route('movies.show', $item->movie->id)); ?>" style="text-decoration: none; color: inherit;">
                                <?php if($item->movie->poster_path): ?>
                                    <img src="<?php echo e(Storage::url($item->movie->poster_path)); ?>" alt="<?php echo e($item->movie->title); ?>" style="width: 100%; height: 225px; object-fit: cover;">
                                <?php else: ?>
                                    <div style="width: 100%; height: 225px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 40px;">
                                        <i class="fas fa-film"></i>
                                    </div>
                                <?php endif; ?>
                                <div style="padding: 10px;">
                                    <h4 style="font-size: 14px; margin-bottom: 5px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?php echo e($item->movie->title); ?>

                                    </h4>
                                    <p style="font-size: 12px; color: #999;">
                                        <?php echo e($item->created_at->diffForHumans()); ?>

                                    </p>
                                </div>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.btn:hover {
    opacity: 0.9;
    transform: translateY(-2px);
    transition: all 0.3s;
}

.movie-card:hover {
    transform: translateY(-5px);
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\CineHub_DuAnTotNghiep\resources\views\profile\simple.blade.php ENDPATH**/ ?>