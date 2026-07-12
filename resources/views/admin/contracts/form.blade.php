<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">T&ecirc;n rạp <span class="text-danger">*</span></label>
        <select name="theater_id" class="form-select" required>
            <option value="">-- Chọn rạp --</option>
            @foreach($theaters as $theater)
                <option value="{{ $theater->id }}" @selected((string) old('theater_id', $contract->theater_id ?? '') === (string) $theater->id)>
                    {{ $theater->name }} - {{ $theater->location }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Email đại diện rạp <span class="text-danger">*</span></label>
        @if(isset($contract) && $contract)
            <input type="hidden" name="representative_user_id" value="{{ old('representative_user_id', $contract->representative_user_id) }}">
            <input type="email" class="form-control" value="{{ $contract->representative->email ?? '' }}" readonly>
        @else
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input
                    type="email"
                    name="representative_email"
                    class="form-control @error('representative_email') is-invalid @enderror"
                    value="{{ old('representative_email') }}"
                    placeholder="Nhập email người dùng"
                    autocomplete="off"
                    required
                >
            </div>
            <small class="text-muted">Nhập chính xác email tài khoản người dùng sẽ làm đại diện rạp.</small>
        @endif
    </div>

    <div class="col-md-6">
        <label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label>
        <input type="date" name="start_date" class="form-control" required value="{{ old('start_date', isset($contract) && $contract ? $contract->end_date->copy()->addDay()->format('Y-m-d') : now()->format('Y-m-d')) }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Ngày kết thúc <span class="text-danger">*</span></label>
        <input type="date" name="end_date" class="form-control" required value="{{ old('end_date', isset($contract) && $contract ? $contract->end_date->copy()->addYear()->format('Y-m-d') : now()->addYear()->format('Y-m-d')) }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Chữ ký Super Admin</label>
        <input type="text" name="super_admin_signature" class="form-control" value="{{ old('super_admin_signature', auth()->user()->name) }}">
    </div>

    <div class="col-md-6">
        <label class="form-label">Chữ ký Đại diện rạp</label>
        <input type="text" name="representative_signature" class="form-control" value="{{ old('representative_signature', $contract->representative->name ?? '') }}">
    </div>

    <div class="col-12">
        <label class="form-label">Quyền của Admin rạp</label>
        <div id="permissionsBox">
            @foreach(old('admin_permissions', $permissions) as $permission)
                <div class="input-group mb-2">
                    <input type="text" name="admin_permissions[]" class="form-control" value="{{ $permission }}">
                    <button class="btn btn-outline-danger" type="button" onclick="this.closest('.input-group').remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endforeach
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addPermissionRow()">
            <i class="fas fa-plus"></i> Thêm quyền
        </button>
    </div>

    <div class="col-12">
        <label class="form-label">Điều khoản tự động thu hồi quyền khi hết hạn</label>
        <textarea name="auto_revoke_terms" rows="4" class="form-control">{{ old('auto_revoke_terms', $terms) }}</textarea>
    </div>

    <div class="col-12 d-flex justify-content-end">
        <button class="btn btn-primary">
            <i class="fas {{ $actionIcon }}"></i> {{ $submitLabel }}
        </button>
    </div>
</div>

@push('scripts')
<script>
function addPermissionRow() {
    const wrapper = document.createElement('div');
    wrapper.className = 'input-group mb-2';
    wrapper.innerHTML = `
        <input type="text" name="admin_permissions[]" class="form-control" value="">
        <button class="btn btn-outline-danger" type="button" onclick="this.closest('.input-group').remove()">
            <i class="fas fa-times"></i>
        </button>
    `;
    document.getElementById('permissionsBox').appendChild(wrapper);
}
</script>
@endpush
