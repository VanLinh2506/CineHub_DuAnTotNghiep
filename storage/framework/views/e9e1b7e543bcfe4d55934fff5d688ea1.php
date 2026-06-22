<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Quản lý người dùng</h2>
    </div>

    <!-- Search -->
    <form method="GET" class="mb-3">
        <input type="hidden" name="route" value="admin/users">
        <div class="row">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên hoặc email..." 
                       value="<?php echo e(old('search') ?? ($search ?? '')); ?>">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="fas fa-search"></i> Tìm
                </button>
            </div>
        </div>
    </form>

    <!-- Users Table -->
    <div class="stat-card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tên</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Gói đăng ký</th>
                        <th>Điểm</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(empty($users)): ?>
                        <tr>
                            <td colspan="9" class="text-center text-muted">Không có người dùng nào</td>
                        </tr>
                    <?php else: ?>
                        <?php $__currentLoopData = $users; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $u): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($u['id']); ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if($u['avatar'] ?? null): ?>
                                            <img src="<?php echo e($u['avatar']); ?>" alt="" class="rounded-circle me-2" style="width: 40px; height: 40px;">
                                        <?php else: ?>
                                            <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        <?php endif; ?>
                                        <?php echo e($u['name']); ?>

                                    </div>
                                </td>
                                <td><?php echo e($u['email']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo e($u['role'] === 'admin' ? 'danger' : 'secondary'); ?>">
                                        <?php echo e($u['role'] ?? 'user'); ?>

                                    </span>
                                </td>
                                <td><?php echo e($u['subscription_name'] ?? 'Chưa có'); ?></td>
                                <td>
                                    <span class="badge bg-info"><?php echo e(number_format($u['points'] ?? 0)); ?> điểm</span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo e(($u['is_active'] ?? true) ? 'success' : 'danger'); ?>">
                                        <?php echo e(($u['is_active'] ?? true) ? 'Hoạt động' : 'Bị chặn'); ?>

                                    </span>
                                </td>
                                <td><?php echo e(\Carbon\Carbon::parse($u['created_at'])->format('d/m/Y')); ?></td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button onclick="openPointsModal(<?php echo e($u['id']); ?>, '<?php echo e($u['name']); ?>', <?php echo e($u['points'] ?? 0); ?>)" class="btn btn-outline-success" title="Quản lý điểm">
                                            <i class="fas fa-coins"></i>
                                        </button>
                                        <button onclick="openRoleModal(<?php echo e($u['id']); ?>, '<?php echo e($u['name']); ?>', '<?php echo e($u['role'] ?? 'user'); ?>')" class="btn btn-outline-secondary" title="Nâng cấp vai trò">
                                            <i class="fas fa-user-shield"></i>
                                        </button>
                                        <?php if($u['id'] != auth()->id()): ?>
                                            <button onclick="toggleUserStatus(<?php echo e($u['id']); ?>, <?php echo e(($u['is_active'] ?? 1) ? 0 : 1); ?>)" 
                                                    class="btn btn-outline-<?php echo e(($u['is_active'] ?? 1) ? 'danger' : 'success'); ?>" 
                                                    title="<?php echo e(($u['is_active'] ?? 1) ? 'Khóa tài khoản' : 'Mở khóa tài khoản'); ?>">
                                                <i class="fas fa-<?php echo e(($u['is_active'] ?? 1) ? 'lock' : 'unlock'); ?>"></i>
                                            </button>
                                        <?php endif; ?>
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

<form id="updatePointsForm" method="POST" action="<?php echo e(route('admin.users.updatePoints')); ?>" class="d-none">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="user_id" id="points_user_id">
    <input type="hidden" name="action" id="points_action">
    <input type="hidden" name="points" id="points_value">
</form>

<form id="updateRoleForm" method="POST" action="<?php echo e(route('admin.users.updateRole')); ?>" class="d-none">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="user_id" id="role_user_id">
    <input type="hidden" name="role" id="role_value">
    <input type="hidden" name="theater_id" id="role_theater_id">
</form>

<form id="toggleStatusForm" method="POST" action="<?php echo e(route('admin.users.toggleStatus')); ?>" class="d-none">
    <?php echo csrf_field(); ?>
    <input type="hidden" name="user_id" id="status_user_id">
    <input type="hidden" name="is_active" id="status_value">
</form>

<script>
    function openPointsModal(userId, userName, points) {
        const action = prompt('Nhập thao tác điểm: set, add, subtract', 'add');
        if (!action) return;

        const value = prompt('Nhập số điểm:', points || 0);
        if (value === null) return;

        document.getElementById('points_user_id').value = userId;
        document.getElementById('points_action').value = action;
        document.getElementById('points_value').value = value;
        document.getElementById('updatePointsForm').submit();
    }

    function openRoleModal(userId, userName, role) {
        const newRole = prompt('Nhập vai trò mới: user, moderator, admin', role || 'user');
        if (!newRole) return;

        document.getElementById('role_user_id').value = userId;
        document.getElementById('role_value').value = newRole;
        document.getElementById('role_theater_id').value = prompt('Nhập theater_id nếu là moderator, bỏ trống nếu không cần', '') || '';
        document.getElementById('updateRoleForm').submit();
    }

    function toggleUserStatus(userId, newStatus) {
        document.getElementById('status_user_id').value = userId;
        document.getElementById('status_value').value = newStatus;
        document.getElementById('toggleStatusForm').submit();
    }
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\CineHub_DuAnTotNghiep\resources\views/admin/users.blade.php ENDPATH**/ ?>