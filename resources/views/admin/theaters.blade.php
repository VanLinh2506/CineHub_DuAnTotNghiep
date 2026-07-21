@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h2 class="mb-1">Danh sách rạp chiếu</h2>
            <p class="text-muted mb-0">Admin tối cao chỉ xem. Thông tin và quyền vận hành rạp được quản lý trong hợp đồng.</p>
        </div>
        <a href="{{ route('admin.contracts.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-file-contract"></i> Quản lý hợp đồng
        </a>
    </div>

    <div class="row">
        @if (empty($theaters))
            <div class="col-12">
                <div class="stat-card text-center">
                    <p class="text-muted">Chưa có rạp nào</p>
                </div>
            </div>
        @else
            @foreach ($theaters as $theater)
                @php $contract = $theater->contracts->first(); @endphp
                <div class="col-md-4 mb-3">
                    <div class="stat-card">
                        @if (!empty($theater['image']))
                            <div class="mb-3" style="width: 100%; height: 200px; overflow: hidden; border-radius: 8px; margin-bottom: 15px;">
                                <img src="{{ $theater['image'] }}" 
                                     alt="{{ $theater['name'] }}" 
                                     style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        @else
                            <div class="mb-3" style="width: 100%; height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                                <i class="fas fa-building" style="font-size: 3rem; color: rgba(255,255,255,0.5);"></i>
                            </div>
                        @endif
                        <h6 style="color: #000; font-weight: bold; margin-bottom: 10px;">{{ $theater['name'] }}</h6>
                        <p class="mb-2" style="color: #000;">
                            <i class="fas fa-map-marker-alt"></i> {{ $theater['location'] ?? 'N/A' }}
                        </p>
                        @if ($theater['phone'] ?? null)
                            <p class="mb-2" style="color: #000;">
                                <i class="fas fa-phone"></i> {{ $theater['phone'] }}
                            </p>
                        @endif
                        <p class="mb-2" style="color:#000;">
                            <i class="fas fa-file-contract"></i>
                            {{ $contract ? $contract->contract_code : 'Chưa có hợp đồng' }}
                        </p>
                        @if($contract)
                            <p class="mb-2 text-muted small">
                                Hiệu lực: {{ $contract->start_date->format('d/m/Y') }} – {{ $contract->end_date->format('d/m/Y') }}
                            </p>
                        @endif
                        <div class="mt-3">
                            <a href="{{ route('admin.theaters.show', $theater['id']) }}" class="btn btn-sm btn-outline-primary w-100">
                                <i class="fas fa-eye"></i> Xem thông tin rạp
                            </a>
                            @if ($contract)
                                <div class="mt-2">
                                    <a href="{{ route('admin.contracts.show', $contract) }}" class="btn btn-sm btn-outline-secondary w-100">
                                        <i class="fas fa-file-contract"></i> Xem hợp đồng
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>
</div>
@endsection
