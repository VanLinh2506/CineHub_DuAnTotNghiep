@extends('admin.moderator.layout')

@section('content')
<div class="stat-card">
    <h5 class="mb-4"><i class="fas fa-users"></i> Quản lý nhân viên đứng quầy</h5>

    <div class="mb-3">
        <a href="?route=moderator/counterStaffCreate" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tạo nhân viên mới
        </a>
    </div>

    @if(empty($counterStaff))
        <div class="alert alert-info">
            <i class="fas fa-info-circle"></i> Chưa có nhân viên đứng quầy nào.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th><th>Tên</th><th>Email</th><th>Trạng thái</th><th>Ngày tạo</th><th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($counterStaff as $staff)
                    <tr>
                        <td>{{ $staff['id'] }}</td>
                        <td>{{ $staff['name'] }}</td>
                        <td>{{ $staff['email'] }}</td>
                        <td>
                            @if($staff['is_active'])
                                <span class="badge bg-success">Hoạt động</span>
                            @else
                                <span class="badge bg-danger">Vô hiệu hóa</span>
                            @endif
                        </td>
                        <td>{{ date('d/m/Y H:i', strtotime($staff['created_at'])) }}</td>
                        <td>
                            <a href="?route=moderator/counterStaffDelete&id={{ $staff['id'] }}"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Bạn có chắc muốn xóa nhân viên này?')">
                                <i class="fas fa-trash"></i> Xóa
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
