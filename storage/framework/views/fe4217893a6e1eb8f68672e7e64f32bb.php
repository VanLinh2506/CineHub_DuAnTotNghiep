<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Chi tiết ticket #<?php echo e($ticket['id']); ?></h5>
    <a href="?route=admin/support" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="stat-card mb-4">
            <h6 class="mb-3"><i class="fas fa-info-circle me-2"></i>Thông tin ticket</h6>
            <div class="mb-3">
                <label class="form-label text-muted">Tiêu đề</label>
                <div class="form-control-plaintext fw-bold"><?php echo e($ticket['subject']); ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Nội dung</label>
                <div class="form-control-plaintext" style="white-space:pre-wrap;background:#f8f9fa;padding:1rem;border-radius:5px;min-height:150px;">
                    <?php echo nl2br(e($ticket['message'])); ?>

                </div>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Vấn đề khách hàng báo cáo</label>
                <div class="form-control-plaintext">
                    <?php
                        $issue = 'N/A';
                        if (!empty($ticket['tags'])) {
                            $parts = explode(' - ', $ticket['tags']);
                            $issue = count($parts) > 1 ? $parts[1] : $ticket['tags'];
                        }
                    ?>
                    <span class="badge bg-info"><?php echo e($issue); ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card mb-4">
            <h6 class="mb-3"><i class="fas fa-user me-2"></i>Thông tin khách hàng</h6>
            <div class="mb-3">
                <label class="form-label text-muted">Tên</label>
                <div class="form-control-plaintext"><?php echo e($ticket['user_name']); ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Email</label>
                <div class="form-control-plaintext"><?php echo e($ticket['user_email']); ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Gói thành viên</label>
                <div class="form-control-plaintext">
                    <?php
                        $subscriptionName = $ticket['subscription_name'] ?? 'Free';
                        $subscriptionClass = match($subscriptionName) {
                            'Premium' => 'danger', 'Gold' => 'warning', 'Silver' => 'info', 'Basic' => 'secondary', default => 'dark'
                        };
                    ?>
                    <span class="badge bg-<?php echo e($subscriptionClass); ?>"><?php echo e($subscriptionName); ?></span>
                </div>
            </div>
        </div>

        <div class="stat-card mb-4">
            <h6 class="mb-3"><i class="fas fa-cog me-2"></i>Thông tin xử lý</h6>
            <div class="mb-3">
                <label class="form-label text-muted">Trạng thái</label>
                <div class="form-control-plaintext">
                    <?php
                        $statusClass = match($ticket['status']) { 'Mới'=>'primary','Đang xử lý'=>'warning','Đã giải quyết'=>'success', default=>'secondary' };
                    ?>
                    <span class="badge bg-<?php echo e($statusClass); ?> status-badge-detail" id="status-badge-detail"><?php echo e($ticket['status']); ?></span>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Độ ưu tiên</label>
                <div class="form-control-plaintext">
                    <span class="badge bg-<?php echo e(match($ticket['priority']) { 'Khẩn cấp'=>'danger','Cao'=>'warning','Trung bình'=>'info', default=>'secondary' }); ?>"><?php echo e($ticket['priority']); ?></span>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Phạm vi hỗ trợ</label>
                <div class="form-control-plaintext">
                    <span class="badge bg-<?php echo e(match($ticket['category']) { 'Lỗi mua bán vé'=>'danger','Mua bán vé'=>'warning','Lỗi về phim'=>'info','Đăng nhập/Đăng xuất'=>'primary', default=>'secondary' }); ?>"><?php echo e($ticket['category']); ?></span>
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Người xử lý</label>
                <div class="form-control-plaintext"><?php echo e($ticket['assigned_name'] ?? 'Chưa gán'); ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Thời gian tạo</label>
                <div class="form-control-plaintext"><i class="fas fa-calendar me-1"></i><?php echo e(date('d/m/Y H:i:s', strtotime($ticket['created_at']))); ?></div>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted">Cập nhật lần cuối</label>
                <div class="form-control-plaintext"><i class="fas fa-clock me-1"></i><?php echo e(date('d/m/Y H:i:s', strtotime($ticket['updated_at']))); ?></div>
            </div>
        </div>

        <div class="stat-card">
            <h6 class="mb-3"><i class="fas fa-tasks me-2"></i>Thao tác</h6>
            <?php if($ticket['status'] !== 'Đã giải quyết'): ?>
                <?php if($ticket['status'] !== 'Đang xử lý'): ?>
                    <form method="POST" action="?route=admin/support/update-status" class="mb-2 status-update-form-detail" onsubmit="return handleStatusUpdateDetail(this, 'Đang xử lý');">
                        <input type="hidden" name="ticket_id" value="<?php echo e($ticket['id']); ?>">
                        <input type="hidden" name="status" value="Đang xử lý">
                        <button type="submit" class="btn btn-warning w-100 mb-2"><i class="fas fa-spinner me-2"></i>Đang giải quyết</button>
                    </form>
                <?php endif; ?>
                <form method="POST" action="?route=admin/support/update-status" class="mb-2 status-update-form-detail" onsubmit="return handleStatusUpdateDetail(this, 'Đã giải quyết');">
                    <input type="hidden" name="ticket_id" value="<?php echo e($ticket['id']); ?>">
                    <input type="hidden" name="status" value="Đã giải quyết">
                    <button type="submit" class="btn btn-success w-100 mb-2"><i class="fas fa-check-circle me-2"></i>Đã giải quyết</button>
                </form>
            <?php endif; ?>
            <button type="button" class="btn btn-primary w-100"
                    onclick="openReplyModal(<?php echo e($ticket['id']); ?>, '<?php echo e(addslashes($ticket['user_email'])); ?>', '<?php echo e(addslashes($ticket['subject'])); ?>')">
                <i class="fas fa-reply me-2"></i>Phản hồi
            </button>
        </div>
    </div>
</div>

<!-- Reply Modal -->
<div class="modal fade" id="replyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-reply me-2"></i>Phản hồi khách hàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="?route=admin/support/reply">
                <div class="modal-body">
                    <input type="hidden" name="ticket_id" id="reply_ticket_id">
                    <div class="mb-3">
                        <label class="form-label">Gửi đến:</label>
                        <input type="email" class="form-control" id="reply_user_email" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tiêu đề ticket:</label>
                        <input type="text" class="form-control" id="reply_ticket_subject" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="reply_message" class="form-label">Nội dung phản hồi <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="reply_message" name="reply_message" rows="6" required placeholder="Nhập nội dung phản hồi..."></textarea>
                        <small class="text-muted">Nội dung này sẽ được gửi đến email của khách hàng.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane me-2"></i>Gửi phản hồi</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function openReplyModal(ticketId, userEmail, ticketSubject) {
    document.getElementById('reply_ticket_id').value = ticketId;
    document.getElementById('reply_user_email').value = userEmail;
    document.getElementById('reply_ticket_subject').value = ticketSubject;
    document.getElementById('reply_message').value = '';
    new bootstrap.Modal(document.getElementById('replyModal')).show();
}

function handleStatusUpdateDetail(form, newStatus) {
    const statusBadge = document.getElementById('status-badge-detail');
    if (!confirm('Xác nhận chuyển ticket sang trạng thái "' + newStatus + '"?')) return false;
    if (statusBadge) {
        statusBadge.textContent = newStatus;
        statusBadge.className = 'badge status-badge-detail bg-' + (newStatus === 'Đang xử lý' ? 'warning' : newStatus === 'Đã giải quyết' ? 'success' : 'primary');
        if (newStatus === 'Đã giải quyết') document.querySelectorAll('.status-update-form-detail').forEach(f => f.style.display = 'none');
    }
    return true;
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\CineHub_DuAnTotNghiep\resources\views\admin\support\view.blade.php ENDPATH**/ ?>