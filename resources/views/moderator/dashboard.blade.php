@extends('layouts.app')

@php
    $title = 'Moderator Dashboard';
@endphp

@section('content')
<div class="container">
    <!-- Theater Info Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="stat-card animated-card theater-info-card" data-delay="0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-2" style="color: #333; font-weight: 600;">
                            <i class="fas fa-building text-primary me-2"></i>{{ $theater['name'] ?? 'Theater' }}
                        </h5>
                        <p class="text-muted mb-1">
                            <i class="fas fa-map-marker-alt"></i> {{ $theater['location'] ?? 'N/A' }}
                        </p>
                        @if ($theater['address'] ?? null)
                            <p class="text-muted mb-1">
                                <i class="fas fa-address-card"></i> {{ $theater['address'] }}
                            </p>
                        @endif
                        @if ($theater['phone'] ?? null)
                            <p class="text-muted mb-0">
                                <i class="fas fa-phone"></i> {{ $theater['phone'] }}
                            </p>
                        @endif
                    </div>
                    <div class="text-end">
                        <span class="badge bg-success fs-6 px-3 py-2">
                            <i class="fas fa-door-open me-1"></i>{{ $theater['total_screens'] ?? 0 }} phòng chiếu
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card animated-card" data-delay="100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Tổng suất chiếu</div>
                        <div class="stat-value text-primary counter" data-target="{{ $stats['total_showtimes'] ?? 0 }}">0</div>
                    </div>
                    <div class="stat-icon bg-primary">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                </div>
                <div class="stat-progress">
                    <div class="progress-bar bg-primary" style="width: 100%"></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card animated-card" data-delay="200">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Suất chiếu hôm nay</div>
                        <div class="stat-value text-info counter" data-target="{{ $stats['today_showtimes'] ?? 0 }}">0</div>
                    </div>
                    <div class="stat-icon bg-info">
                        <i class="fas fa-calendar-day"></i>
                    </div>
                </div>
                <div class="stat-progress">
                    <div class="progress-bar bg-info" style="width: 100%"></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card animated-card" data-delay="300">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Tổng vé đã bán</div>
                        <div class="stat-value text-success counter" data-target="{{ $stats['total_tickets'] ?? 0 }}">0</div>
                    </div>
                    <div class="stat-icon bg-success">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                </div>
                <div class="stat-progress">
                    <div class="progress-bar bg-success" style="width: 100%"></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card animated-card" data-delay="400">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Doanh thu</div>
                        <div class="stat-value text-warning revenue-counter" data-target="{{ $stats['total_revenue'] ?? 0 }}">0₫</div>
                    </div>
                    <div class="stat-icon bg-warning">
                        <i class="fas fa-money-bill-wave"></i>
                    </div>
                </div>
                <div class="stat-progress">
                    <div class="progress-bar bg-warning" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-md-3">
            <a href="{{ url('?route=moderator/showtimes') }}" class="stat-card" style="text-decoration: none; color: inherit;">
                <div class="text-center">
                    <div class="mb-3"><i class="fas fa-film fa-3x text-primary"></i></div>
                    <h6>Quản lý suất chiếu</h6>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ url('?route=moderator/screens') }}" class="stat-card" style="text-decoration: none; color: inherit;">
                <div class="text-center">
                    <div class="mb-3"><i class="fas fa-door-open fa-3x text-success"></i></div>
                    <h6>Quản lý phòng chiếu</h6>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ url('?route=moderator/tickets') }}" class="stat-card" style="text-decoration: none; color: inherit;">
                <div class="text-center">
                    <div class="mb-3"><i class="fas fa-ticket-alt fa-3x text-info"></i></div>
                    <h6>Quản lý vé</h6>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ url('?route=moderator/food_items') }}" class="stat-card" style="text-decoration: none; color: inherit;">
                <div class="text-center">
                    <div class="mb-3"><i class="fas fa-utensils fa-3x text-warning"></i></div>
                    <h6>Quản lý combo</h6>
                </div>
            </a>
        </div>
    </div>

    <!-- Recent Showtimes -->
    <div class="row">
        <div class="col-12">
            <div class="stat-card animated-card" data-delay="500">
                <h6 class="mb-3">
                    <i class="fas fa-calendar-check text-success me-2"></i>Suất chiếu gần đây
                </h6>
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Phim</th>
                                <th>Phòng chiếu</th>
                                <th>Ngày/Giờ</th>
                                <th>Vé bán</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (empty($recentShowtimes))
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Không có suất chiếu nào</td>
                                </tr>
                            @else
                                @foreach ($recentShowtimes as $showtime)
                                    <tr>
                                        <td><strong>{{ $showtime['movie_title'] }}</strong></td>
                                        <td>{{ $showtime['screen_name'] }}</td>
                                        <td>{{ \Carbon\Carbon::parse($showtime['show_date'])->format('d/m/Y') }} {{ \Carbon\Carbon::parse($showtime['show_time'])->format('H:i') }}</td>
                                        <td><span class="badge bg-info">{{ $showtime['tickets_sold'] ?? 0 }}</span></td>
                                        <td>
                                            <span class="badge bg-{{ $showtime['status'] === 'active' ? 'success' : 'secondary' }}">
                                                {{ $showtime['status'] === 'active' ? 'Hoạt động' : 'Đóng' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .theater-info-card {
        border-left: 4px solid #667eea;
    }
</style>

<script>
    document.querySelectorAll('.counter').forEach(element => {
        const target = parseInt(element.dataset.target);
        let current = 0;
        const increment = target / 100;
        
        const updateCounter = () => {
            if (current < target) {
                current += increment;
                element.textContent = Math.floor(current);
                setTimeout(updateCounter, 20);
            } else {
                element.textContent = target;
            }
        };
        
        updateCounter();
    });

    document.querySelectorAll('.revenue-counter').forEach(element => {
        const target = parseInt(element.dataset.target);
        let current = 0;
        const increment = target / 100;
        
        const updateCounter = () => {
            if (current < target) {
                current += increment;
                element.textContent = Math.floor(current).toLocaleString('vi-VN') + '₫';
                setTimeout(updateCounter, 20);
            } else {
                element.textContent = target.toLocaleString('vi-VN') + '₫';
            }
        };
        
        updateCounter();
    });
</script>
@endsection
