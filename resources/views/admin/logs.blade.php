@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-history"></i> Lịch sử hoạt động</h2>
        <small style="color: #fff;">Xem lịch sử thêm, xóa, cập nhật phim, rạp, bình luận</small>
    </div>

    <!-- Filters -->
    <form method="GET" class="mb-3">
        <input type="hidden" name="route" value="admin/logs">
        <div class="row g-2">
            <div class="col-md-4">
                <label class="form-label small">Module</label>
                <select name="module" class="form-select" id="moduleFilter" onchange="this.form.submit()">
                    <option value="">Tất cả modules</option>
                    <option value="Movie" @selected(($module ?? null)==='Movie' )>Phim</option>
                    <option value="Theater" @selected(($module ?? null)==='Theater' )>Rạp</option>
                    <option value="Review" @selected(($module ?? null)==='Review' )>Bình luận</option>
                    <option value="User" @selected(($module ?? null)==='User' )>Người dùng</option>
                    <option value="System" @selected(($module ?? null)==='System' )>Hệ thống</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small">Hành động</label>
                <select name="action" class="form-select" id="actionFilter" onchange="this.form.submit()">
                    <option value="">Tất cả hành động</option>
                    <option value="Thêm" @selected(isset($action) && strpos($action, 'Thêm' ) !==false)>Thêm</option>
                    <option value="Xóa" @selected(isset($action) && strpos($action, 'Xóa' ) !==false)>Xóa</option>
                    <option value="Cập nhật" @selected(isset($action) && strpos($action, 'Cập nhật' ) !==false)>Cập nhật</option>
                    <option value="Ghim" @selected(isset($action) && strpos($action, 'Ghim' ) !==false)>Ghim/Bỏ ghim</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">&nbsp;</label>
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="fas fa-filter"></i> Lọc
                </button>
            </div>
            <div class="col-md-2">
                <label class="form-label small">&nbsp;</label>
                <a href="{{ route('admin.logs') }}" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-redo"></i> Xóa lọc
                </a>
            </div>
        </div>
    </form>

    <!-- Logs Table -->
    <div class="stat-card">
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Người dùng</th>
                        <th>Module</th>
                        <th>Hành động</th>
                        <th>Chi tiết</th>
                        <th>IP Address</th>
                        <th>Thời gian</th>
                    </tr>
                </thead>
                <tbody>
                    @if (empty($logs))
                    <tr>
                        <td colspan="7" class="text-center text-muted">Không có log nào</td>
                    </tr>
                    @else
                    @foreach ($logs as $log)
                    <tr>
                        <td>{{ data_get($log, 'id') }}</td>
                        <td>
                            <strong>{{ data_get($log, 'user_name', 'N/A') }}</strong>
                            <br><small class="text-muted">{{ data_get($log, 'user_email', 'N/A') }}</small>
                        </td>
                        <td>
                            <span class="badge bg-{{
                                        data_get($log, 'module') === 'Movie' ? 'primary' :
                                        (data_get($log, 'module') === 'Theater' ? 'success' :
                                        (data_get($log, 'module') === 'User' ? 'info' : 'warning'))
                                    }}">
                                {{ data_get($log, 'module', 'N/A') }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{
                                        strpos((string) data_get($log, 'action', ''), 'Thêm') !== false ? 'success' :
                                        (strpos((string) data_get($log, 'action', ''), 'Xóa') !== false ? 'danger' :
                                        (strpos((string) data_get($log, 'action', ''), 'Cập nhật') !== false ? 'warning' : 'secondary'))
                                    }}">
                                {{ data_get($log, 'action', 'N/A') }}
                            </span>
                        </td>
                        <td>
                            <small>{{ Str::limit(data_get($log, 'details', ''), 50) }}</small>
                        </td>
                        <td>
                            <code>{{ data_get($log, 'ip_address', 'N/A') }}</code>
                        </td>
                        <td>
                            <small>{{ \Carbon\Carbon::parse(data_get($log, 'created_at'))->format('d/m/Y H:i') }}</small>
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
