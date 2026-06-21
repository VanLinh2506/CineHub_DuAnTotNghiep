<?php $__env->startSection('content'); ?>
<div class="stat-card">
    <h5 class="mb-4"><i class="fas fa-ticket-alt"></i> Vé đã quét hôm nay</h5>

    <div class="mb-3">
        <form method="GET" class="d-flex gap-2">
            <input type="hidden" name="route" value="counterStaff/scannedTickets">
            <input type="date" name="date" class="form-control" value="<?php echo e($date); ?>" style="max-width: 200px;">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Xem
            </button>
        </form>
    </div>

    <?php if(empty($tickets)): ?>
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Chưa có vé nào được quét trong ngày này.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Booking Code</th>
                        <th>Khách hàng</th>
                        <th>Phim</th>
                        <th>Ngày chiếu</th>
                        <th>Phòng</th>
                        <th>Ghế</th>
                        <th>Thời gian quét</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($ticket['booking_code'] ?? 'N/A'); ?></td>
                        <td><?php echo e($ticket['user_name']); ?></td>
                        <td><?php echo e($ticket['movie_title']); ?></td>
                        <td><?php echo e(date('d/m/Y H:i', strtotime($ticket['show_date'] . ' ' . $ticket['show_time']))); ?></td>
                        <td><?php echo e($ticket['screen_name']); ?></td>
                        <td><?php echo e($ticket['seat']); ?></td>
                        <td><?php echo e(date('d/m/Y H:i:s', strtotime($ticket['picked_up_at']))); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
            </table>
        </div>

        <?php if($total_pages > 1): ?>
            <nav>
                <ul class="pagination justify-content-center">
                    <?php for($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?php echo e($i == $page ? 'active' : ''); ?>">
                            <a class="page-link" href="?route=counterStaff/scannedTickets&page=<?php echo e($i); ?>&date=<?php echo e(urlencode($date)); ?>"><?php echo e($i); ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.counter_staff.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\CineHub_DuAnTotNghiep\resources\views\admin\counter_staff\scanned_tickets.blade.php ENDPATH**/ ?>