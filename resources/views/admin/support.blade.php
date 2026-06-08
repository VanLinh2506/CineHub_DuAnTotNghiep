@extends('layouts.app')

@php
    $title = 'Hỗ trợ khách hàng';
@endphp

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5>{{ $title }}</h5>
    </div>

    <!-- Filters -->
    <form method="GET" class="mb-3">
        <input type="hidden" name="route" value="admin/support">
        <div class="row g-2">
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Tất cả trạng thái</option>
                    <option value="Mới" {{ ($status ?? '') === 'Mới' ? 'selected' : '' }}>Mới</option>
                    <option value="Đang xử lý" {{ ($status ?? '') === 'Đang xử lý' ? 'selected' : '' }}>Đang xử lý</option>
                    <option value="Đã giải quyết" {{ ($status ?? '') === 'Đã giải quyết' ? 'selected' : '' }}>Đã giải quyết</option>
                    <option value="Đã đóng" {{ ($status ?? '') === 'Đã đóng' ? 'selected' : '' }}>Đã đóng</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select">
                    <option value="">Tất cả phạm vi hỗ trợ</option>
                    <option value="Mua bán vé" {{ ($category ?? '') === 'Mua bán vé' ? 'selected' : '' }}>Mua bán vé</option>
                    <option value="Lỗi mua bán vé" {{ ($category ?? '') === 'Lỗi mua bán vé' ? 'selected' : '' }}>Lỗi mua bán vé</option>
                    <option value="Lỗi về phim" {{ ($category ?? '') === 'Lỗi về phim' ? 'selected' : '' }}>Lỗi về phim</option>
                    <option value="Đăng nhập/Đăng xuất" {{ ($category ?? '') === 'Đăng nhập/Đăng xuất' ? 'selected' : '' }}>Đăng nhập/Đăng xuất</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="fas fa-filter"></i> Lọc
                </button>
            </div>
        </div>
    </form>

    <!-- Support Tickets Table -->
    <div class="stat-card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Người dùng</th>
                        <th>Gói thành viên</th>
                        <th>Tiêu đề</th>
                        <th>Trạng thái</th>
                        <th>Phạm vi hỗ trợ</th>
                        <th>Người xử lý</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @if (empty($tickets))
                        <tr>
                            <td colspan="9" class="text-center text-muted">Không có ticket nào</td>
                        </tr>
                    @else
                        @foreach ($tickets as $ticket)
                            <tr>
                                <td>#{{ $ticket['id'] }}</td>
                                <td>
                                    <strong>{{ $ticket['user_name'] ?? 'N/A' }}</strong>
                                    <br><small class="text-muted">{{ $ticket['user_email'] ?? 'N/A' }}</small>
                                </td>
                                <td>{{ $ticket['subscription_name'] ?? 'Chưa có' }}</td>
                                <td>
                                    <strong>{{ $ticket['title'] ?? 'N/A' }}</strong>
                                </td>
                                <td>
                                    <span class="badge bg-{{ 
                                        $ticket['status'] === 'Mới' ? 'warning' : 
                                        ($ticket['status'] === 'Đang xử lý' ? 'info' : 
                                        ($ticket['status'] === 'Đã giải quyết' ? 'success' : 'secondary'))
                                    }}">
                                        {{ $ticket['status'] ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>{{ $ticket['category'] ?? 'N/A' }}</td>
                                <td>{{ $ticket['admin_name'] ?? '-' }}</td>
                                <td>{{ \Carbon\Carbon::parse($ticket['created_at'])->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ url('?route=admin/support/view&id=' . $ticket['id']) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i> Xem
                                    </a>
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
