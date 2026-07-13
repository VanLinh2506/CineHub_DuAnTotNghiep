<div class="row g-3">
    @if(!isset($contract) || !$contract)
        <div class="col-12">
            <div class="p-3 border rounded bg-light">
                <label class="form-label fw-bold">Tải hợp đồng PDF</label>
                <div class="input-group">
                    <input type="file" name="contract_pdf" id="contractPdf" class="form-control" accept="application/pdf">
                    <button type="button" class="btn btn-outline-primary" id="extractPdfButton">Nhận diện thông tin</button>
                </div>
                <small class="text-muted">PDF tối đa 10 MB. Hệ thống đọc tên rạp và thời hạn để điền vào biểu mẫu.</small>
                <div id="pdfExtractMessage" class="mt-2"></div>
            </div>
        </div>
    @endif

    <div class="col-md-6">
        <label class="form-label">Tên rạp <span class="text-danger">*</span></label>
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
        <label class="form-label">Đại diện/Admin rạp <span class="text-danger">*</span></label>
        @if(isset($contract) && $contract)
            <input type="hidden" name="representative_user_id" value="{{ old('representative_user_id', $contract->representative_user_id) }}">
            <input type="email" class="form-control" value="{{ $contract->representative->email ?? '' }}" readonly>
        @else
            <div class="form-check form-switch mb-2">
                <input type="hidden" name="create_admin" value="0">
                <input class="form-check-input" type="checkbox" name="create_admin" value="1" id="createAdmin" @checked(old('create_admin'))>
                <label class="form-check-label" for="createAdmin">Tạo tài khoản Admin rạp mới</label>
            </div>
            <input type="email" name="representative_email" class="form-control" value="{{ old('representative_email') }}" placeholder="Email người dùng có sẵn">
            <small class="text-muted">Nhập email có sẵn hoặc bật tùy chọn tạo Admin mới.</small>
        @endif
    </div>

    @if(!isset($contract) || !$contract)
        <div class="col-12" id="newAdminFields" style="display:none">
            <div class="row g-3 p-3 border rounded">
                <div class="col-md-4"><label class="form-label">Tên Admin rạp</label><input type="text" name="admin_name" class="form-control" value="{{ old('admin_name') }}"></div>
                <div class="col-md-4"><label class="form-label">Email đăng nhập</label><input type="email" name="admin_email" class="form-control" value="{{ old('admin_email') }}"></div>
                <div class="col-md-4"><label class="form-label">Mật khẩu (để trống để tự sinh)</label><input type="password" name="admin_password" class="form-control" minlength="8"></div>
            </div>
        </div>
    @endif

    <div class="col-md-6"><label class="form-label">Ngày bắt đầu <span class="text-danger">*</span></label><input type="date" name="start_date" class="form-control" required value="{{ old('start_date', isset($contract) && $contract ? $contract->end_date->copy()->addDay()->format('Y-m-d') : now()->format('Y-m-d')) }}"></div>
    <div class="col-md-6"><label class="form-label">Ngày kết thúc <span class="text-danger">*</span></label><input type="date" name="end_date" class="form-control" required value="{{ old('end_date', isset($contract) && $contract ? $contract->end_date->copy()->addYear()->format('Y-m-d') : now()->addYear()->format('Y-m-d')) }}"></div>
    <div class="col-12"><h6 class="mb-0">Bảng giá niêm yết cho suất chiếu</h6><small class="text-muted">Giá gốc mỗi vé; giá suất chiếu phải nằm trong khoảng của hợp đồng.</small></div>
    <div class="col-md-3"><label class="form-label">Phim bán chạy - từ</label><input type="number" name="bestseller_price_min" class="form-control" min="0" step="1000" required value="{{ old('bestseller_price_min', $contract->bestseller_price_min ?? 90000) }}"></div>
    <div class="col-md-3"><label class="form-label">Phim bán chạy - đến</label><input type="number" name="bestseller_price_max" class="form-control" min="0" step="1000" required value="{{ old('bestseller_price_max', $contract->bestseller_price_max ?? 100000) }}"></div>
    <div class="col-md-3"><label class="form-label">Phim mới phát hành - từ</label><input type="number" name="new_release_price_min" class="form-control" min="0" step="1000" required value="{{ old('new_release_price_min', $contract->new_release_price_min ?? 100000) }}"></div>
    <div class="col-md-3"><label class="form-label">Phim mới phát hành - đến</label><input type="number" name="new_release_price_max" class="form-control" min="0" step="1000" required value="{{ old('new_release_price_max', $contract->new_release_price_max ?? 120000) }}"></div>
    <div class="col-md-6"><label class="form-label">Chữ ký Super Admin</label><input type="text" name="super_admin_signature" class="form-control" value="{{ old('super_admin_signature', auth()->user()->name) }}"></div>
    <div class="col-md-6"><label class="form-label">Chữ ký Đại diện rạp</label><input type="text" name="representative_signature" class="form-control" value="{{ old('representative_signature', $contract->representative->name ?? '') }}"></div>

    <div class="col-12">
        <label class="form-label">Quyền của Admin rạp</label>
        <div id="permissionsBox">
            @foreach(old('admin_permissions', $permissions) as $permission)
                <div class="input-group mb-2"><input type="text" name="admin_permissions[]" class="form-control" value="{{ $permission }}"><button class="btn btn-outline-danger" type="button" onclick="this.closest('.input-group').remove()"><i class="fas fa-times"></i></button></div>
            @endforeach
        </div>
        <button type="button" class="btn btn-sm btn-outline-primary" onclick="addPermissionRow()"><i class="fas fa-plus"></i> Thêm quyền</button>
    </div>
    <div class="col-12"><label class="form-label">Điều khoản tự động thu hồi quyền khi hết hạn</label><textarea name="auto_revoke_terms" rows="4" class="form-control">{{ old('auto_revoke_terms', $terms) }}</textarea></div>
    <div class="col-12 d-flex justify-content-end"><button class="btn btn-primary"><i class="fas {{ $actionIcon }}"></i> {{ $submitLabel }}</button></div>
</div>

@push('scripts')
<script>
function addPermissionRow() {
    const row = document.createElement('div');
    row.className = 'input-group mb-2';
    row.innerHTML = '<input type="text" name="admin_permissions[]" class="form-control"><button class="btn btn-outline-danger" type="button" onclick="this.closest(\'.input-group\').remove()"><i class="fas fa-times"></i></button>';
    document.getElementById('permissionsBox').appendChild(row);
}

const createAdmin = document.getElementById('createAdmin');
if (createAdmin) {
    const toggle = () => {
        document.getElementById('newAdminFields').style.display = createAdmin.checked ? '' : 'none';
        document.querySelector('[name="representative_email"]').disabled = createAdmin.checked;
    };
    createAdmin.addEventListener('change', toggle);
    toggle();
}

const extractButton = document.getElementById('extractPdfButton');
if (extractButton) extractButton.addEventListener('click', async () => {
    const file = document.getElementById('contractPdf').files[0];
    const message = document.getElementById('pdfExtractMessage');
    if (!file) return message.innerHTML = '<span class="text-danger">Vui lòng chọn file PDF.</span>';
    extractButton.disabled = true;
    message.innerHTML = '<span class="text-info">Đang đọc hợp đồng...</span>';
    const body = new FormData();
    body.append('contract_pdf', file);
    body.append('_token', document.querySelector('input[name="_token"]').value);
    try {
        const response = await fetch(@json(route('admin.contracts.extract-pdf')), { method: 'POST', body });
        const result = await response.json();
        if (!response.ok) throw new Error(result.message || 'Không thể đọc PDF');
        if (result.needs_ocr) return message.innerHTML = '<span class="text-warning">PDF là bản scan ảnh; cần OCR để nhận diện.</span>';
        if (result.theater_id) document.querySelector('[name="theater_id"]').value = result.theater_id;
        if (result.start_date) document.querySelector('[name="start_date"]').value = result.start_date;
        if (result.end_date) document.querySelector('[name="end_date"]').value = result.end_date;
        message.innerHTML = '<span class="text-success">Đã nhận diện. Hãy kiểm tra lại trước khi lưu.</span>';
    } catch (error) {
        message.innerHTML = `<span class="text-danger">${error.message}</span>`;
    } finally { extractButton.disabled = false; }
});
</script>
@endpush
