@extends('admin.moderator.layout')

@section('content')
<div class="stat-card">
    <h5 class="mb-4"><i class="fas fa-user-plus"></i> Tạo nhân viên đứng quầy mới</h5>

    <form method="POST" action="?route=moderator/counterStaffCreate">
        <div class="mb-3">
            <label class="form-label">Tên nhân viên <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="name" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email <span class="text-danger">*</span></label>
            <input type="email" class="form-control" name="email" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
            <input type="password" class="form-control" name="password" required minlength="6">
            <small class="text-muted">Mật khẩu tối thiểu 6 ký tự</small>
        </div>
        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save"></i> Tạo nhân viên
            </button>
            <a href="?route=moderator/counterStaff" class="btn btn-secondary">
                <i class="fas fa-times"></i> Hủy
            </a>
        </div>
    </form>
</div>
@endsection
