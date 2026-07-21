@extends('admin.counter_staff.layout')

@section('title', 'Dashboard Nhân viên')

@section('content')
<div class="container-fluid py-4">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body p-4">
                    <h2 class="mb-2">Xin chào, {{ $user->name }}!</h2>
                    <p class="mb-0">
                        <i class="fas fa-building me-2"></i>{{ $theater->name ?? 'Rạp không xác định' }}
                        <span class="ms-3"><i class="fas fa-map-marker-alt me-2"></i>{{ $theater->address ?? '' }}</span>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Vé đã bán hôm nay</h6>
                            <h3 class="mb-0">{{ number_format($todayStats['tickets_sold']) }}</h3>
                        </div>
                        <div class="icon-shape bg-gradient-success text-white rounded-circle">
                            <i class="fas fa-ticket-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Doanh thu hôm nay</h6>
                            <h3 class="mb-0">{{ number_format($todayStats['revenue']) }}₫</h3>
                        </div>
                        <div class="icon-shape bg-gradient-info text-white rounded-circle">
                            <i class="fas fa-dollar-sign fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Vé đã quét hôm nay</h6>
                            <h3 class="mb-0">{{ number_format($todayStats['tickets_scanned']) }}</h3>
                        </div>
                        <div class="icon-shape bg-gradient-warning text-white rounded-circle">
                            <i class="fas fa-qrcode fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Nước đã giao khi quét</h6>
                            <h3 class="mb-0">{{ number_format($todayStats['drinks_delivered']) }}</h3>
                            <small class="text-success">{{ number_format($todayStats['drink_revenue']) }}₫</small>
                        </div>
                        <div class="icon-shape bg-gradient-primary text-white rounded-circle"><i class="fas fa-glass-whiskey fa-2x"></i></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Thao tác nhanh</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <a href="{{ route('counter.scan') }}" class="btn btn-lg btn-primary w-100 mb-3">
                                <i class="fas fa-qrcode fa-2x d-block mb-2"></i>
                                Quét QR Code
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('counter.sell') }}" class="btn btn-lg btn-success w-100 mb-3">
                                <i class="fas fa-cash-register fa-2x d-block mb-2"></i>
                                Bán vé tại quầy
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('counter.showtimes') }}" class="btn btn-lg btn-info w-100 mb-3">
                                <i class="fas fa-film fa-2x d-block mb-2"></i>
                                Lịch chiếu
                            </a>
                        </div>
                        <div class="col-md-3">
                            <a href="{{ route('counter.sales') }}" class="btn btn-lg btn-warning w-100 mb-3">
                                <i class="fas fa-history fa-2x d-block mb-2"></i>
                                Lịch sử bán vé
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Today's Showtimes -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Lịch chiếu hôm nay</h5>
                    <a href="{{ route('counter.showtimes') }}" class="btn btn-sm btn-primary">Xem tất cả</a>
                </div>
                <div class="card-body">
                    @if($todayShowtimes->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-calendar-times fa-3x mb-3"></i>
                            <p>Không có lịch chiếu nào hôm nay</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Giờ chiếu</th>
                                        <th>Phim</th>
                                        <th>Phòng</th>
                                        <th>Ghế trống</th>
                                        <th>Giá</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($todayShowtimes as $showtime)
                                        <tr>
                                            <td>
                                                <strong>{{ date('H:i', strtotime($showtime->show_time)) }}</strong>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    @if($showtime->movie->poster)
                                                        <img src="{{ asset('storage/' . $showtime->movie->poster) }}" 
                                                             alt="{{ $showtime->movie->title }}" 
                                                             class="rounded me-2" 
                                                             style="width: 40px; height: 60px; object-fit: cover;">
                                                    @endif
                                                    <div>
                                                        <div>{{ $showtime->movie->title }}</div>
                                                        <small class="text-muted">{{ $showtime->movie->duration }} phút</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ $showtime->screen->screen_name ?? 'N/A' }}</td>
                                            <td>
                                                @php
                                                    $bookedCount = $showtime->tickets()->where('status', 'Đã đặt')->count();
                                                    $available = $showtime->screen->total_seats - $bookedCount;
                                                    $percentage = $showtime->screen->total_seats > 0 
                                                        ? ($bookedCount / $showtime->screen->total_seats) * 100 
                                                        : 0;
                                                @endphp
                                                <div>
                                                    {{ $available }} / {{ $showtime->screen->total_seats }}
                                                    <div class="progress mt-1" style="height: 5px;">
                                                        <div class="progress-bar 
                                                            @if($percentage < 50) bg-success
                                                            @elseif($percentage < 80) bg-warning
                                                            @else bg-danger
                                                            @endif" 
                                                            style="width: {{ $percentage }}%">
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{{ number_format($showtime->price) }}₫</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.icon-shape {
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.bg-gradient-primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.bg-gradient-success {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.bg-gradient-info {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.bg-gradient-warning {
    background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
}

.card {
    border: none;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
    border-radius: 0.75rem;
    margin-bottom: 1.5rem;
}

.btn-lg i {
    opacity: 0.8;
}

.btn-lg:hover i {
    opacity: 1;
    transform: scale(1.1);
    transition: all 0.3s;
}
</style>
@endsection
