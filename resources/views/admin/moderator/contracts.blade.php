@extends('admin.moderator.layout')

@section('content')
<div class="container-fluid">
    <div class="mb-4"><h2><i class="fas fa-file-contract me-2"></i>Hợp đồng của rạp</h2><div class="text-muted">{{ $theater->name }}</div></div>
    <div class="card border-0 shadow-sm"><div class="card-body p-0"><div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light"><tr><th>Mã hợp đồng</th><th>Thời hạn</th><th>Giá niêm yết</th><th>Đại diện</th><th>Trạng thái</th><th class="text-end">Tài liệu</th></tr></thead>
            <tbody>
            @forelse($contracts as $contract)
                @php
                    $labels = ['active' => 'Đang hiệu lực', 'pending' => 'Chờ hiệu lực', 'expired' => 'Đã hết hạn', 'renewed' => 'Đã gia hạn'];
                    $colors = ['active' => 'success', 'pending' => 'warning', 'expired' => 'danger', 'renewed' => 'info'];
                @endphp
                <tr>
                    <td><strong>{{ $contract->contract_code }}</strong></td>
                    <td>{{ $contract->start_date->format('d/m/Y') }} – {{ $contract->end_date->format('d/m/Y') }}</td>
                    <td><small>Phim bán chạy: <strong>{{ number_format($contract->bestseller_price_min) }}–{{ number_format($contract->bestseller_price_max) }}</strong><br>Phim mới: <strong>{{ number_format($contract->new_release_price_min) }}–{{ number_format($contract->new_release_price_max) }}</strong> VNĐ</small></td>
                    <td>{{ $contract->representative->name ?? 'N/A' }}<br><small class="text-muted">{{ $contract->representative->email ?? '' }}</small></td>
                    <td><span class="badge bg-{{ $colors[$contract->status] ?? 'secondary' }}">{{ $labels[$contract->status] ?? $contract->status }}</span></td>
                    <td class="text-end"><a class="btn btn-sm btn-outline-danger" href="{{ route('moderator.contracts.download', $contract) }}"><i class="fas fa-file-pdf"></i> Tải PDF</a></td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center text-muted py-5">Rạp chưa có hợp đồng nào.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div></div></div>
    <div class="mt-3">{{ $contracts->links() }}</div>
</div>
@endsection
