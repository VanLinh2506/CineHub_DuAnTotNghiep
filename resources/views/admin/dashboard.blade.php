@extends('layouts.app')

@php
    $title = 'Admin Dashboard';
@endphp

@section('content')
<div class="container">
    <!-- Stats Cards with Animation -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card animated-card" data-delay="0">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Tổng người dùng</div>
                        <div class="stat-value text-primary counter" data-target="{{ $stats['total_users'] ?? 0 }}">0</div>
                    </div>
                    <div class="stat-icon bg-primary">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="stat-progress">
                    <div class="progress-bar bg-primary" style="width: 100%"></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card animated-card" data-delay="100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Tổng phim</div>
                        <div class="stat-value text-success counter" data-target="{{ $stats['total_movies'] ?? 0 }}">0</div>
                    </div>
                    <div class="stat-icon bg-success">
                        <i class="fas fa-film"></i>
                    </div>
                </div>
                <div class="stat-progress">
                    <div class="progress-bar bg-success" style="width: 100%"></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card animated-card" data-delay="200">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Vé đã bán</div>
                        <div class="stat-value text-info counter" data-target="{{ $stats['total_tickets'] ?? 0 }}">0</div>
                    </div>
                    <div class="stat-icon bg-info">
                        <i class="fas fa-ticket-alt"></i>
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
                        <div class="stat-label">Tổng doanh thu</div>
                        <div class="stat-value text-warning revenue-counter" data-target="{{ $stats['total_revenue'] ?? 0 }}">0₫</div>
                    </div>
                    <div class="stat-icon bg-warning">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
                <div class="stat-progress">
                    <div class="progress-bar bg-warning" style="width: 100%"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card animated-card revenue-card" data-delay="400">
                <div class="stat-label">Doanh thu hôm nay</div>
                <div class="stat-value text-success revenue-counter" data-target="{{ $stats['today_revenue'] ?? 0 }}">0₫</div>
                <div class="stat-trend">
                    <i class="fas fa-arrow-up text-success"></i>
                    <small class="text-muted">So với hôm qua</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card animated-card revenue-card" data-delay="500">
                <div class="stat-label">Doanh thu tuần này</div>
                <div class="stat-value text-info revenue-counter" data-target="{{ $stats['week_revenue'] ?? 0 }}">0₫</div>
                <div class="stat-trend">
                    <i class="fas fa-chart-line text-info"></i>
                    <small class="text-muted">7 ngày qua</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card animated-card revenue-card" data-delay="600">
                <div class="stat-label">Doanh thu tháng này</div>
                <div class="stat-value text-primary revenue-counter" data-target="{{ $stats['month_revenue'] ?? 0 }}">0₫</div>
                <div class="stat-trend">
                    <i class="fas fa-calendar-alt text-primary"></i>
                    <small class="text-muted">Tháng hiện tại</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts & Quick Stats -->
    <div class="row">
        <div class="col-md-8">
            <div class="stat-card animated-card chart-card" data-delay="700">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0" style="color: #333; font-weight: 600;">
                        <i class="fas fa-chart-line text-primary me-2"></i>Doanh thu 7 ngày gần nhất
                    </h5>
                    <div class="chart-legend">
                        <span class="legend-item">
                            <span class="legend-color" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);"></span>
                            Doanh thu
                        </span>
                    </div>
                </div>
                <canvas id="revenueChart" height="80"></canvas>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="col-md-4">
            <div class="stat-card mb-3 animated-card" data-delay="800">
                <h6 class="mb-3" style="color: #333; font-weight: 600;">
                    <i class="fas fa-bolt text-warning me-2"></i>Thống kê nhanh
                </h6>
                <div class="mb-3">
                    <small class="text-muted d-block mb-1">Người dùng hoạt động hôm nay</small>
                    <div class="fw-bold text-primary fs-4 counter" data-target="{{ $stats['active_users_today'] ?? 0 }}">0</div>
                </div>
                <div>
                    <small class="text-muted d-block mb-1">Ticket hỗ trợ chờ xử lý</small>
                    <div class="fw-bold text-warning fs-4 counter" data-target="{{ $stats['pending_tickets'] ?? 0 }}">0</div>
                </div>
            </div>
            
            <div class="stat-card animated-card" data-delay="900">
                <h6 class="mb-3" style="color: #333; font-weight: 600;">
                    <i class="fas fa-trophy text-warning me-2"></i>Top phim xem nhiều
                </h6>
                @if (empty($topMovies))
                    <p class="text-muted">Chưa có dữ liệu</p>
                @else
                    <ul class="list-unstyled mb-0">
                        @foreach ($topMovies as $index => $movie)
                            <li class="mb-3 pb-2 border-bottom movie-item" style="animation-delay: {{ $index * 100 }}ms;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <span class="badge bg-primary me-2 rank-badge">{{ $index + 1 }}</span>
                                        <span class="movie-title">{{ $movie['title'] }}</span>
                                    </div>
                                    <span class="badge bg-info">{{ $movie['view_count'] ?? 0 }} lượt</span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>

    <!-- Upcoming Showtimes -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="stat-card animated-card" data-delay="1000">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0" style="color: #333; font-weight: 600;">
                        <i class="fas fa-calendar-check text-success me-2"></i>Suất chiếu sắp tới
                    </h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Phim</th>
                                <th>Rạp</th>
                                <th>Ngày</th>
                                <th>Giờ</th>
                                <th>Giá vé</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (empty($upcomingShowtimes))
                                <tr>
                                    <td colspan="5" class="text-center text-muted">Không có suất chiếu sắp tới</td>
                                </tr>
                            @else
                                @foreach ($upcomingShowtimes as $index => $showtime)
                                    <tr class="table-row-animated" style="animation-delay: {{ $index * 50 }}ms;">
                                        <td><strong>{{ $showtime['movie_title'] }}</strong></td>
                                        <td>{{ $showtime['theater_name'] }}</td>
                                        <td>{{ \Carbon\Carbon::parse($showtime['show_date'])->format('d/m/Y') }}</td>
                                        <td><span class="badge bg-primary">{{ \Carbon\Carbon::parse($showtime['show_time'])->format('H:i') }}</span></td>
                                        <td class="text-success fw-bold">{{ number_format($showtime['price']) }}₫</td>
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
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animated-card {
        animation: fadeInUp 0.6s ease-out forwards;
        opacity: 0;
        transition: all 0.3s ease;
    }

    .animated-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }

    .stat-card {
        position: relative;
        overflow: hidden;
        border: none;
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        transition: all 0.3s ease;
    }

    .stat-card:hover .stat-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        margin-top: 5px;
        transition: all 0.3s ease;
    }

    .stat-label {
        font-size: 0.9rem;
        color: #6c757d;
        font-weight: 500;
    }

    .stat-progress {
        margin-top: 15px;
        height: 4px;
        background: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-bar {
        height: 100%;
        transition: width 0.6s ease;
    }

    .revenue-trend {
        margin-top: 10px;
        font-size: 0.85rem;
    }

    .rank-badge {
        font-size: 0.8rem;
        min-width: 28px;
        text-align: center;
    }

    .movie-item {
        transition: all 0.3s ease;
    }

    .movie-item:hover {
        transform: translateX(5px);
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Counter animations
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

    // Revenue counter with currency formatting
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

    // Chart initialization (placeholder)
    // const ctx = document.getElementById('revenueChart');
    // if (ctx) {
    //     new Chart(ctx, {
    //         type: 'line',
    //         data: {
    //             labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
    //             datasets: [{
    //                 label: 'Revenue',
    //                 data: [12000, 15000, 18000, 17000, 22000, 25000, 28000],
    //                 borderColor: '#667eea',
    //                 backgroundColor: 'rgba(102, 126, 234, 0.1)',
    //                 tension: 0.4,
    //                 fill: true
    //             }]
    //         }
    //     });
    // }
</script>
@endsection
