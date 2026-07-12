@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">{{ $contract->contract_code }}</h2>
        <div class="btn-group">
            <a href="{{ route('admin.contracts.download', $contract) }}" class="btn btn-danger">
                <i class="fas fa-file-pdf"></i> Tải PDF
            </a>
            <a href="{{ route('admin.contracts.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Danh sách
            </a>
        </div>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="fas fa-exclamation-circle me-2"></i>{{ $errors->first() }}
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-5">
            <div class="stat-card">
                <h5 class="mb-3">Thông tin hợp đồng</h5>
                @php
                    $statusClass = ['active' => 'success', 'pending' => 'warning', 'expired' => 'danger', 'renewed' => 'info'][$contract->status] ?? 'secondary';
                    $statusLabel = ['active' => 'Đang hiệu lực', 'pending' => 'Chờ hiệu lực', 'expired' => 'Hết hạn', 'renewed' => 'Đã gia hạn'][$contract->status] ?? $contract->status;
                @endphp
                <p><strong>Trạng thái:</strong> <span class="badge bg-{{ $statusClass }}">{{ $statusLabel }}</span></p>
                <p><strong>Rạp:</strong> {{ $contract->theater->name ?? 'N/A' }}</p>
                <p><strong>Đại diện:</strong> {{ $contract->representative->name ?? 'N/A' }} ({{ $contract->representative->email ?? '' }})</p>
                <p><strong>Ngày bắt đầu:</strong> {{ $contract->start_date->format('d/m/Y') }}</p>
                <p><strong>Ngày kết thúc:</strong> {{ $contract->end_date->format('d/m/Y') }}</p>
                <p><strong>Super Admin:</strong> {{ $contract->superAdmin->name ?? 'N/A' }}</p>
                @if($contract->revoked_at)
                    <p><strong>Ngày thu hồi:</strong> {{ $contract->revoked_at->format('d/m/Y H:i') }}</p>
                @endif
            </div>
        </div>

        <div class="col-lg-7">
            <div class="stat-card">
                <h5 class="mb-3">Gia hạn hợp đồng</h5>
                <form method="POST" action="{{ route('admin.contracts.renew', $contract) }}">
                    @csrf
                    @include('admin.contracts.form', [
                        'submitLabel' => 'Gia hạn và sinh PDF mới',
                        'actionIcon' => 'fa-rotate',
                        'contract' => $contract,
                        'theaters' => \App\Models\Theater::where('is_active', 1)->orderBy('name')->get(),
                        'users' => \App\Models\User::where('id', $contract->representative_user_id)->get(),
                        'permissions' => old('admin_permissions', $contract->admin_permissions ?: []),
                        'terms' => old('auto_revoke_terms', $contract->auto_revoke_terms),
                    ])
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
