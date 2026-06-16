@extends('admin.moderator.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5><i class="fas fa-users"></i> Quản lý nhân viên đứng quầy</h5>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">
        <i class="fas fa-plus"></i> Thêm nhân viên mới
    </button>
</div>

<div class="stat-card">
    @if(empty($counterStaff))
        <div class="alert alert-info text-center py-5">
            <i class="fas fa-info-circle fa-3x mb-3 d-block"></i>
            <h5>Chưa có nhân viên đứng quầy nào</h5>
            <p class="mb-3">Hãy thêm nhân viên để họ có thể bán vé tại quầy</p>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                <i class="fas fa-plus"></i> Thêm nhân viên đầu tiên
            </button>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th style="width: 60px;">ID</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Số điện thoại</th>
                        <th style="width: 150px;">Ngày tạo</th>
                        <th style="width: 150px;" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($counterStaff as $staff)
                    <tr>
                        <td><strong>#{{ $staff['id'] }}</strong></td>
                        <td>
                            <i class="fas fa-user-circle text-primary me-2"></i>
                            <strong>{{ $staff['name'] }}</strong>
                        </td>
                        <td>
                            <i class="fas fa-envelope text-muted me-2"></i>
                            {{ $staff['email'] }}
                        </td>
                        <td>
                            <i class="fas fa-phone text-muted me-2"></i>
                            {{ $staff['phone'] }}
                        </td>
                        <td>
                            <small class="text-muted">{{ $staff['created_at'] }}</small>
                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-info" 
                                    onclick="editStaff({{ json_encode($staff) }})"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editStaffModal">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('moderator.counterStaff.destroy', $staff['id']) }}" 
                                  method="POST" 
                                  class="d-inline"
                                  onsubmit="return confirm('Bạn có chắc muốn xóa nhân viên {{ $staff['name'] }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="mt-3">
            <p class="text-muted mb-0">
                <i class="fas fa-info-circle me-2"></i>
                Tổng số nhân viên: <strong>{{ count($counterStaff) }}</strong>
            </p>
        </div>
    @endif
</div>

<!-- Add Staff Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1" aria-labelledby="addStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('moderator.counterStaff.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="addStaffModalLabel">
                        <i class="fas fa-user-plus"></i> Thêm nhân viên mới
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="add_name" class="form-label">Họ tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="add_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="add_email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="add_email" name="email" required>
                        <small class="text-muted">Email sẽ dùng để đăng nhập</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="add_phone" class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control" id="add_phone" name="phone" placeholder="0xxx xxx xxx">
                    </div>
                    
                    <div class="mb-3">
                        <label for="add_password" class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="add_password" name="password" required minlength="6">
                        <small class="text-muted">Tối thiểu 6 ký tự</small>
                    </div>
                    
                    <div class="alert alert-info mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        <small>Nhân viên sẽ được gán vào rạp <strong>{{ $theater['name'] }}</strong> với vai trò đứng quầy bán vé.</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Thêm nhân viên
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Staff Modal -->
<div class="modal fade" id="editStaffModal" tabindex="-1" aria-labelledby="editStaffModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editStaffForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title" id="editStaffModalLabel">
                        <i class="fas fa-user-edit"></i> Chỉnh sửa nhân viên
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_name" class="form-label">Họ tên <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_email" class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_phone" class="form-label">Số điện thoại</label>
                        <input type="text" class="form-control" id="edit_phone" name="phone" placeholder="0xxx xxx xxx">
                    </div>
                    
                    <div class="mb-3">
                        <label for="edit_password" class="form-label">Mật khẩu mới</label>
                        <input type="password" class="form-control" id="edit_password" name="password" minlength="6">
                        <small class="text-muted">Để trống nếu không muốn đổi mật khẩu</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Cập nhật
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
function editStaff(staff) {
    document.getElementById('edit_name').value = staff.name;
    document.getElementById('edit_email').value = staff.email;
    document.getElementById('edit_phone').value = staff.phone || '';
    document.getElementById('edit_password').value = '';
    
    // Set form action to update route
    const form = document.getElementById('editStaffForm');
    form.action = '/moderator/counter-staff/' + staff.id;
}
</script>
@endpush
@endsection
