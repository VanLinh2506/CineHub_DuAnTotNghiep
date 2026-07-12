@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-file-contract me-2"></i>Quản lý hợp đồng rạp</h2>
        <a href="{{ route('admin.contracts.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Tạo hợp đồng
        </a>
    </div>

    {{-- Dashboard thống kê --}}
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="stat-card text-center" style="border-left: 4px solid #667eea;">
                <div class="stat-label" style="font-size: 0.75rem;">TỔNG HỢP ĐỒNG</div>
                <div class="stat-value" style="font-size: 1.8rem; color: #667eea;">{{ $stats['total'] }}</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card text-center" style="border-left: 4px solid #28a745;">
                <div class="stat-label" style="font-size: 0.75rem;">ĐANG HIỆU LỰC</div>
                <div class="stat-value" style="font-size: 1.8rem; color: #28a745;">{{ $stats['active'] }}</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card text-center" style="border-left: 4px solid #ffc107;">
                <div class="stat-label" style="font-size: 0.75rem;">CHỜ HIỆU LỰC</div>
                <div class="stat-value" style="font-size: 1.8rem; color: #ffc107;">{{ $stats['pending'] }}</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card text-center" style="border-left: 4px solid #dc3545;">
                <div class="stat-label" style="font-size: 0.75rem;">ĐÃ HẾT HẠN</div>
                <div class="stat-value" style="font-size: 1.8rem; color: #dc3545;">{{ $stats['expired'] }}</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card text-center" style="border-left: 4px solid #17a2b8;">
                <div class="stat-label" style="font-size: 0.75rem;">ĐÃ GIA HẠN</div>
                <div class="stat-value" style="font-size: 1.8rem; color: #17a2b8;">{{ $stats['renewed'] }}</div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="stat-card text-center" style="border-left: 4px solid #fd7e14; {{ $stats['expiringSoon'] > 0 ? 'animation: pulse-warning 1.5s infinite;' : '' }}">
                <div class="stat-label" style="font-size: 0.75rem;">SẮP HẾT HẠN</div>
                <div class="stat-value" style="font-size: 1.8rem; color: #fd7e14;">
                    {{ $stats['expiringSoon'] }}
                    @if($stats['expiringSoon'] > 0)
                        <i class="fas fa-exclamation-triangle" style="font-size: 0.9rem;"></i>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @if($stats['expiringSoon'] > 0)
        <div class="alert" style="background: linear-gradient(135deg, #fff3cd, #ffeeba); border-left: 4px solid #fd7e14; border-radius: 10px;">
            <i class="fas fa-bell me-2" style="color: #fd7e14;"></i>
            <strong>Cảnh báo:</strong> Có <strong>{{ $stats['expiringSoon'] }}</strong> hợp đồng sẽ hết hạn trong 7 ngày tới.
            Hệ thống sẽ tự động gửi email thông báo và thu hồi quyền khi hết hạn.
        </div>
    @endif

    <form method="GET" class="stat-card mb-3">
        <div class="row g-3">
            <div class="col-md-6">
                <input type="text" name="search" class="form-control" value="{{ request('search') }}" placeholder="Tìm mã hợp đồng, rạp, đại diện...">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Tất cả trạng thái</option>
                    @foreach(['pending' => 'Chờ hiệu lực', 'active' => 'Đang hiệu lực', 'expired' => 'Hết hạn', 'renewed' => 'Đã gia hạn'] as $value => $label)
                        <option value="{{ $value }}" @selected(request('status') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-secondary w-100"><i class="fas fa-search"></i> Lọc</button>
            </div>
        </div>
    </form>

    <div class="stat-card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Mã hợp đồng</th>
                        <th>Rạp</th>
                        <th>Đại diện</th>
                        <th>Thời hạn</th>
                        <th>Còn lại</th>
                        <th>Trạng thái</th>
                        <th>PDF</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($contracts as $contract)
                        @php
                            $statusClass = ['active' => 'success', 'pending' => 'warning', 'expired' => 'danger', 'renewed' => 'info'][$contract->status] ?? 'secondary';
                            $statusLabel = ['active' => 'Đang hiệu lực', 'pending' => 'Chờ hiệu lực', 'expired' => 'Hết hạn', 'renewed' => 'Đã gia hạn'][$contract->status] ?? $contract->status;
                            $daysLeft = $contract->status === 'active' ? (int) today()->diffInDays($contract->end_date, false) : null;
                        @endphp
                        <tr>
                            <td><strong>{{ $contract->contract_code }}</strong></td>
                            <td>{{ $contract->theater->name ?? 'N/A' }}</td>
                            <td>
                                {{ $contract->representative->name ?? 'N/A' }}
                                <br><small class="text-muted">{{ $contract->representative->email ?? '' }}</small>
                            </td>
                            <td>{{ $contract->start_date->format('d/m/Y') }} - {{ $contract->end_date->format('d/m/Y') }}</td>
                            <td>
                                @if($daysLeft !== null)
                                    @if($daysLeft <= 0)
                                        <span class="badge bg-danger">Hết hạn</span>
                                    @elseif($daysLeft <= 7)
                                        <span class="badge" style="background: #fd7e14;">{{ $daysLeft }} ngày</span>
                                    @elseif($daysLeft <= 30)
                                        <span class="badge bg-warning text-dark">{{ $daysLeft }} ngày</span>
                                    @else
                                        <span class="text-muted">{{ $daysLeft }} ngày</span>
                                    @endif
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ $statusClass }}">{{ $statusLabel }}</span>
                            </td>
                            <td>
                                <a href="{{ route('admin.contracts.download', $contract) }}" class="btn btn-sm btn-outline-danger" title="Tải PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                            </td>
                            <td>
                                <a href="{{ route('admin.contracts.show', $contract) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i> Xem
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-file-contract" style="font-size: 2rem; opacity: 0.3;"></i>
                                <p class="mt-2 mb-0">Chưa có hợp đồng nào</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $contracts->links() }}
        </div>
    </div>
</div>

@push('styles')
<style>
@keyframes pulse-warning {
    0%, 100% { box-shadow: 0 2px 8px rgba(253, 126, 20, 0.15); }
    50% { box-shadow: 0 4px 20px rgba(253, 126, 20, 0.35); }
}
</style>
@endpush
@endsection
