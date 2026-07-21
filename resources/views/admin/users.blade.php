@extends('admin.layout')

@push('styles')
<style>
    .users-page { width: 100%; max-width: none; padding-inline: 0; }
    .users-page .stat-card { padding: 16px; overflow: hidden; }
    .users-page .table-responsive { width: 100%; overflow-x: auto; }
    .users-page .users-table { width: 100%; min-width: 1050px; }
    .users-page .users-table th, .users-page .users-table td { padding: 12px 10px; white-space: nowrap; }
    .users-page .users-table td:nth-child(2), .users-page .users-table td:nth-child(3) { white-space: normal; }
    .users-page .action-buttons { display: flex; flex-wrap: nowrap; gap: 4px; }
    .users-page .action-buttons .btn { padding: 5px 9px; }
</style>
@endpush

@section('content')
<div class="container-fluid users-page">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Quản lý người dùng</h2>
    </div>

    <!-- Search -->
    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-3">
        <div class="row">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên hoặc email..." 
                       value="{{ old('search') ?? ($search ?? '') }}">
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
            <table class="table table-hover users-table">
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
                    @if (empty($users))
                        <tr>
                            <td colspan="9" class="text-center text-muted">Không có người dùng nào</td>
                        </tr>
                    @else
                        @foreach ($users as $u)
                            <tr>
                                <td>{{ $u['id'] }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="{{ $u['avatar_url'] }}" alt="Ảnh đại diện {{ $u['name'] }}" class="rounded-circle me-2 flex-shrink-0" width="40" height="40" style="width:40px;height:40px;object-fit:cover;object-position:center;background:#747f88;" onerror="this.onerror=null;this.src='{{ asset('images/default-avatar.svg') }}';">
                                        {{ $u['name'] }}
                                    </div>
                                </td>
                                <td>{{ $u['email'] }}</td>
                                <td>
                                    <span class="badge bg-{{ $u['role'] === 'admin' ? 'danger' : 'secondary' }}">
                                        {{ $u['role'] ?? 'user' }}
                                    </span>
                                </td>
                                <td>{{ $u['subscription_name'] ?? 'Chưa có' }}</td>
                                <td>
                                    <span class="badge bg-info">{{ number_format($u['points'] ?? 0) }} điểm</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ ($u['is_active'] ?? true) ? 'success' : 'danger' }}">
                                        {{ ($u['is_active'] ?? true) ? 'Hoạt động' : 'Bị chặn' }}
                                    </span>
                                    @if(!($u['is_active'] ?? true) && !empty($u['ban_reason']))
                                        <div class="small text-danger mt-1" style="max-width:180px;white-space:normal" title="{{ $u['ban_reason'] }}">
                                            {{ \Illuminate\Support\Str::limit($u['ban_reason'], 55) }}
                                        </div>
                                    @endif
                                </td>
                                <td>{{ \Carbon\Carbon::parse($u['created_at'])->format('d/m/Y') }}</td>
                                <td>
                                    <div class="action-buttons">
                                        <button onclick="openPointsModal({{ $u['id'] }}, '{{ $u['name'] }}', {{ $u['points'] ?? 0 }})" class="btn btn-outline-success" title="Quản lý điểm">
                                            <i class="fas fa-coins"></i>
                                        </button>
                                        <button onclick="openRoleModal({{ $u['id'] }}, '{{ $u['name'] }}', '{{ $u['role'] ?? 'user' }}')" class="btn btn-outline-secondary" title="Nâng cấp vai trò">
                                            <i class="fas fa-user-shield"></i>
                                        </button>
                                        @if ($u['id'] != auth()->id())
                                            <button onclick="openStatusModal({{ $u['id'] }}, @js($u['name']), {{ ($u['is_active'] ?? 1) ? 0 : 1 }})"
                                                    class="btn btn-outline-{{ ($u['is_active'] ?? 1) ? 'danger' : 'success' }}" 
                                                    title="{{ ($u['is_active'] ?? 1) ? 'Khóa tài khoản' : 'Mở khóa tài khoản' }}">
                                                <i class="fas fa-{{ ($u['is_active'] ?? 1) ? 'lock' : 'unlock' }}"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Account Status Modal -->
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog"><div class="modal-content">
        <div class="modal-header"><h5 class="modal-title" id="statusModalTitle">Khóa tài khoản</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
        <form method="POST" action="{{ route('admin.users.toggleStatus') }}" id="statusForm">@csrf
            <input type="hidden" name="user_id" id="statusUserId">
            <input type="hidden" name="is_active" id="statusValue">
            <div class="modal-body">
                <p id="statusDescription" class="mb-3"></p>
                <div id="banReasonGroup">
                    <label for="banReason" class="form-label">Lý do khóa <span class="text-danger">*</span></label>
                    <textarea name="reason" id="banReason" class="form-control" rows="4" minlength="10" maxlength="1000" placeholder="Nêu rõ hành vi vi phạm hoặc nguyên nhân khóa tài khoản..."></textarea>
                    <small class="text-muted">Tối thiểu 10 ký tự. Lý do sẽ được thông báo cho người dùng.</small>
                </div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button><button type="submit" class="btn" id="statusSubmitButton">Xác nhận</button></div>
        </form>
    </div></div>
</div>

<!-- Points Management Modal -->
<div class="modal fade" id="pointsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quản lý điểm - <span id="pointsUserName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="pointsForm" method="POST" action="{{ route('admin.users.updatePoints') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="pointsUserId" name="user_id">
                    <div class="mb-3">
                        <label class="form-label">Điểm hiện tại</label>
                        <input type="text" id="currentPoints" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Thao tác</label>
                        <select name="action" id="pointsAction" class="form-select" required>
                            <option value="set">Đặt số điểm</option>
                            <option value="add">Thêm điểm</option>
                            <option value="subtract">Trừ điểm</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số điểm</label>
                        <input type="number" name="points" class="form-control" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Role Management Modal -->
<div class="modal fade" id="roleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nâng cấp vai trò - <span id="roleUserName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="roleForm" method="POST" action="{{ route('admin.users.updateRole') }}">
                @csrf
                <div class="modal-body">
                    <input type="hidden" id="roleUserId" name="user_id">
                    <div class="mb-3">
                        <label class="form-label">Vai trò hiện tại</label>
                        <input type="text" id="currentRole" class="form-control" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Vai trò mới</label>
                        <select name="role" id="newRole" class="form-select" required onchange="toggleTheaterSelect()">
                            <option value="user">User</option>
                            <option value="moderator">Moderator</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="mb-3" id="theaterSelectContainer" style="display: none;">
                        <label class="form-label">Chọn rạp quản lý <span class="text-danger">*</span></label>
                        <select name="theater_id" id="theaterSelect" class="form-select">
                            <option value="">-- Chọn rạp --</option>
                            @if(isset($theaters) && !empty($theaters))
                                @foreach ($theaters as $theater)
                                    <option value="{{ $theater['id'] }}">{{ $theater['name'] }} - {{ $theater['location'] ?? '' }}</option>
                                @endforeach
                            @endif
                        </select>
                        <small class="text-muted">Moderator chỉ có thể quản lý rạp được gán</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Cập nhật</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openPointsModal(userId, userName, currentPoints) {
    document.getElementById('pointsUserId').value = userId;
    document.getElementById('pointsUserName').textContent = userName;
    document.getElementById('currentPoints').value = currentPoints.toLocaleString('vi-VN');
    document.getElementById('pointsForm').querySelector('input[name="points"]').value = '';
    document.getElementById('pointsAction').value = 'set';
    
    const modal = new bootstrap.Modal(document.getElementById('pointsModal'));
    modal.show();
}

function openRoleModal(userId, userName, currentRole) {
    document.getElementById('roleUserId').value = userId;
    document.getElementById('roleUserName').textContent = userName;
    document.getElementById('currentRole').value = currentRole;
    document.getElementById('newRole').value = currentRole;
    document.getElementById('theaterSelect').value = '';
    toggleTheaterSelect();
    
    const modal = new bootstrap.Modal(document.getElementById('roleModal'));
    modal.show();
}

function toggleTheaterSelect() {
    const roleSelect = document.getElementById('newRole');
    const theaterContainer = document.getElementById('theaterSelectContainer');
    const theaterSelect = document.getElementById('theaterSelect');
    
    if (roleSelect.value === 'moderator') {
        theaterContainer.style.display = 'block';
        theaterSelect.required = true;
    } else {
        theaterContainer.style.display = 'none';
        theaterSelect.required = false;
        theaterSelect.value = '';
    }
}

function openStatusModal(userId, userName, newStatus) {
    const locking = Number(newStatus) === 0;
    document.getElementById('statusUserId').value = userId;
    document.getElementById('statusValue').value = newStatus;
    document.getElementById('statusModalTitle').textContent = locking ? 'Khóa tài khoản' : 'Mở khóa tài khoản';
    document.getElementById('statusDescription').textContent = locking
        ? `Bạn đang khóa tài khoản “${userName}”. Vui lòng nhập lý do rõ ràng.`
        : `Xác nhận mở khóa tài khoản “${userName}”?`;
    const reasonGroup = document.getElementById('banReasonGroup');
    const reason = document.getElementById('banReason');
    reasonGroup.style.display = locking ? 'block' : 'none';
    reason.required = locking;
    reason.value = '';
    const submit = document.getElementById('statusSubmitButton');
    submit.textContent = locking ? 'Khóa tài khoản' : 'Mở khóa';
    submit.className = `btn ${locking ? 'btn-danger' : 'btn-success'}`;
    new bootstrap.Modal(document.getElementById('statusModal')).show();
}
</script>
@endsection
