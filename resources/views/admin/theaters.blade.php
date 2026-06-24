@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Quản lý rạp chiếu</h2>
        @if (!isset(auth()->user()['role']) || auth()->user()['role'] !== 'moderator')
            <a href="{{ route('admin.theaters.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm rạp mới
            </a>
        @endif
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
                        <div class="mt-3">
                            <a href="{{ route('admin.theaters.show', $theater['id']) }}" class="btn btn-sm btn-outline-primary w-100">
                                <i class="fas fa-eye"></i> Xem thông tin rạp
                            </a>
                            @php
                                $user = auth()->user();
                                $canEdit = false;
                                if (isset($user['role']) && $user['role'] === 'moderator' && isset($user['theater_id']) && $user['theater_id'] == $theater['id']) {
                                    $canEdit = true;
                                }
                            @endphp
                            @if ($canEdit || (isset($user['role']) && $user['role'] === 'admin'))
                                <div class="mt-2">
                                    <a href="{{ route('admin.theaters.edit', $theater['id']) }}" class="btn btn-sm btn-outline-warning w-100">
                                        <i class="fas fa-edit"></i> Chỉnh sửa
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
