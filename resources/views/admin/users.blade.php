@extends('admin.layout')

@section('content')
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
                                        @if ($u['avatar'] ?? null)
                                            <img src="{{ $u['avatar'] }}" alt="" class="rounded-circle me-2" style="width: 40px; height: 40px;">
                                        @else
                                            <div class="bg-secondary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                <i class="fas fa-user text-white"></i>
                                            </div>
                                        @endif
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
                                </td>
                                <td>{{ \Carbon\Carbon::parse($u['created_at'])->format('d/m/Y') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ url('?route=admin/users/edit&id=' . $u['id']) }}" class="btn btn-outline-primary" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ url('?route=admin/users/view&id=' . $u['id']) }}" class="btn btn-outline-info" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button onclick="openPointsModal({{ $u['id'] }}, '{{ $u['name'] }}', {{ $u['points'] ?? 0 }})" class="btn btn-outline-success" title="Quản lý điểm">
                                            <i class="fas fa-coins"></i>
                                        </button>
                                        <button onclick="openRoleModal({{ $u['id'] }}, '{{ $u['name'] }}', '{{ $u['role'] ?? 'user' }}')" class="btn btn-outline-secondary" title="Nâng cấp vai trò">
                                            <i class="fas fa-user-shield"></i>
                                        </button>
                                        @if ($u['id'] != auth()->id())
                                            <button onclick="toggleUserStatus({{ $u['id'] }}, {{ ($u['is_active'] ?? 1) ? 0 : 1 }})" 
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

<script>
    function openPointsModal(userId, userName, points) {
        // Implementation for points modal
    }

    function openRoleModal(userId, userName, role) {
        // Implementation for role modal
    }

    function toggleUserStatus(userId, newStatus) {
        // Implementation for toggle status
    }
</script>
@endsection
