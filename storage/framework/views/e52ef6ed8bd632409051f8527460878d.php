<?php
    $title = 'Thông báo';
?>

<?php $__env->startSection('content'); ?>
<section class="section">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="page-title" style="color: #fff;">
                <i class="fas fa-bell"></i> <?php echo e($title); ?>

            </h1>
            <?php if(isset($unreadCount) && $unreadCount > 0): ?>
                <form method="POST" action="<?php echo e(url('/?route=notifications/markAsRead')); ?>" style="display: inline;">
                    <?php echo csrf_field(); ?>
                    <button type="submit" class="btn btn-outline-light btn-sm">
                        <i class="fas fa-check-double"></i> Đánh dấu tất cả đã đọc
                    </button>
                </form>
            <?php endif; ?>
        </div>
        
        <div class="row">
            <div class="col-md-10 offset-md-1">
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger">
                        <?php echo e($error); ?>

                    </div>
                <?php endif; ?>

                <?php if(empty($notifications) || !isset($notifications)): ?>
                    <div class="card" style="background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2);">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-bell-slash" style="font-size: 3rem; color: rgba(255, 255, 255, 0.5); margin-bottom: 1rem;"></i>
                            <p style="color: rgba(255, 255, 255, 0.7); font-size: 1.1rem;">Chưa có thông báo nào</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="list-group">
                        <?php $__currentLoopData = $notifications; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $notification): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="card mb-3 notification-item <?php echo e($notification['is_read'] ? '' : 'unread'); ?>" 
                                 style="background: <?php echo e($notification['is_read'] ? 'rgba(255, 255, 255, 0.05)' : 'rgba(229, 9, 20, 0.1)'); ?>; 
                                        border: 1px solid <?php echo e($notification['is_read'] ? 'rgba(255, 255, 255, 0.1)' : 'rgba(229, 9, 20, 0.3)'); ?>; 
                                        cursor: pointer;"
                                 onclick="window.location.href='<?php echo e($notification['link'] ? $notification['link'] : route('notifications.index')); ?>'; markAsRead(<?php echo e($notification['id']); ?>);">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-2">
                                                <?php
                                                    $iconClass = 'fa-info-circle';
                                                    $iconColor = '#0d6efd';
                                                    switch ($notification['type']) {
                                                        case 'success':
                                                            $iconClass = 'fa-check-circle';
                                                            $iconColor = '#28a745';
                                                            break;
                                                        case 'warning':
                                                            $iconClass = 'fa-exclamation-triangle';
                                                            $iconColor = '#ffc107';
                                                            break;
                                                        case 'error':
                                                            $iconClass = 'fa-times-circle';
                                                            $iconColor = '#dc3545';
                                                            break;
                                                        case 'booking':
                                                            $iconClass = 'fa-ticket-alt';
                                                            $iconColor = '#e50914';
                                                            break;
                                                    }
                                                ?>
                                                <i class="fas <?php echo e($iconClass); ?>" style="color: <?php echo e($iconColor); ?>; margin-right: 10px; font-size: 1.2rem;"></i>
                                                <h5 class="mb-0" style="color: #fff; font-weight: <?php echo e($notification['is_read'] ? 'normal' : 'bold'); ?>;">
                                                    <?php echo e($notification['title']); ?>

                                                </h5>
                                                <?php if(!$notification['is_read']): ?>
                                                    <span class="badge bg-danger ms-2">Mới</span>
                                                <?php endif; ?>
                                            </div>
                                            <p class="mb-2" style="color: rgba(255, 255, 255, 0.8);">
                                                <?php echo e($notification['message']); ?>

                                            </p>
                                            <small style="color: rgba(255, 255, 255, 0.5);">
                                                <i class="far fa-clock"></i> <?php echo e(\Carbon\Carbon::parse($notification['created_at'])->format('d/m/Y H:i')); ?>

                                            </small>
                                        </div>
                                        <div class="ms-3">
                                            <a href="<?php echo e(url('/?route=notifications/delete&id=' . $notification['id'])); ?>" 
                                               class="btn btn-sm btn-outline-danger"
                                               onclick="event.stopPropagation(); return confirm('Bạn chắc chắn muốn xóa thông báo này?');">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<style>
    .notification-item:hover {
        transform: translateX(5px);
        transition: all 0.3s;
    }

    .notification-item.unread {
        border-left: 4px solid #e50914 !important;
    }
</style>

<script>
    function markAsRead(notificationId) {
        fetch('<?php echo e(url('/?route=notifications/markAsRead')); ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
            body: 'id=' + notificationId
        }).then(response => response.json())
          .then(data => {
              if (data.success) {
                  // Cập nhật UI
                  const item = document.querySelector('.notification-item[onclick*="' + notificationId + '"]');
                  if (item) {
                      item.classList.remove('unread');
                      item.style.background = 'rgba(255, 255, 255, 0.05)';
                      item.style.border = '1px solid rgba(255, 255, 255, 0.1)';
                      const badge = item.querySelector('.badge');
                      if (badge) badge.remove();
                      const title = item.querySelector('h5');
                      if (title) title.style.fontWeight = 'normal';
                  }
                  
                  // Cập nhật badge trên header
                  updateNotificationBadge();
              }
          });
    }

    function updateNotificationBadge() {
        fetch('<?php echo e(url('/?route=notifications/getUnreadCount')); ?>')
            .then(response => response.json())
            .then(data => {
                const badge = document.querySelector('.notification-badge');
                if (data.count > 0) {
                    if (badge) {
                        badge.textContent = data.count > 99 ? '99+' : data.count;
                    } else {
                        // Tạo badge mới nếu chưa có
                        const btn = document.querySelector('.notification-btn-fixed');
                        if (btn) {
                            const newBadge = document.createElement('span');
                            newBadge.className = 'notification-badge';
                            newBadge.textContent = data.count > 99 ? '99+' : data.count;
                            btn.appendChild(newBadge);
                        }
                    }
                } else {
                    if (badge) badge.remove();
                }
            });
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\CineHub_DuAnTotNghiep\resources\views\user\notifications\index.blade.php ENDPATH**/ ?>