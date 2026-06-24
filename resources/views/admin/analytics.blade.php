@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <!-- Filter -->
    <div class="stat-card mb-4">
        <form method="GET" action="{{ route('admin.analytics') }}" class="row align-items-end">
            <div class="col-md-3">
                <label class="form-label">Khoảng thời gian</label>
                <select name="period" class="form-select" onchange="this.form.submit()">
                    <option value="7days" {{ ($period ?? '7days') == '7days' ? 'selected' : '' }}>7 ngày gần nhất</option>
                    <option value="30days" {{ ($period ?? '') == '30days' ? 'selected' : '' }}>30 ngày gần nhất</option>
                    <option value="thismonth" {{ ($period ?? '') == 'thismonth' ? 'selected' : '' }}>Tháng này</option>
                    <option value="lastmonth" {{ ($period ?? '') == 'lastmonth' ? 'selected' : '' }}>Tháng trước</option>
                    <option value="thisyear" {{ ($period ?? '') == 'thisyear' ? 'selected' : '' }}>Năm nay</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">Từ ngày</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">Đến ngày</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter me-2"></i>Lọc
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Tổng doanh thu</div>
                        <div class="stat-value text-primary">{{ number_format($summaryStats['total_revenue'] ?? 0) }}đ</div>
                    </div>
                    <div class="stat-icon bg-primary">
                        <i class="fas fa-dollar-sign"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Số giao dịch</div>
                        <div class="stat-value text-success">{{ number_format($summaryStats['total_transactions'] ?? 0) }}</div>
                    </div>
                    <div class="stat-icon bg-success">
                        <i class="fas fa-receipt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Tổng số vé</div>
                        <div class="stat-value text-info">{{ number_format($summaryStats['total_tickets'] ?? 0) }}</div>
                    </div>
                    <div class="stat-icon bg-info">
                        <i class="fas fa-ticket-alt"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stat-label">Giá vé TB</div>
                        <div class="stat-value text-warning">{{ number_format($summaryStats['avg_ticket_price'] ?? 0) }}đ</div>
                    </div>
                    <div class="stat-icon bg-warning">
                        <i class="fas fa-chart-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="stat-card mb-4">
        <h5 class="mb-4">
            <i class="fas fa-chart-area me-2"></i>
            Doanh thu 
            @if(($period ?? '7days') == '7days')
                7 ngày gần nhất
            @elseif($period == '30days')
                30 ngày gần nhất
            @elseif($period == 'thismonth')
                tháng này
            @elseif($period == 'lastmonth')
                tháng trước
            @elseif($period == 'thisyear')
                năm nay
            @else
                theo thời gian đã chọn
            @endif
            <span class="badge bg-primary ms-2">Doanh thu</span>
        </h5>
        <div style="position: relative; height: 350px; max-height: 350px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Top Movies Bar Chart -->
        <div class="col-md-6">
            <div class="stat-card">
                <h5 class="mb-4">
                    <i class="fas fa-trophy me-2 text-warning"></i>Top 5 phim doanh thu cao nhất
                </h5>
                <div style="position: relative; height: 300px;">
                    <canvas id="topMoviesChart"></canvas>
                </div>
            </div>
        </div>
        
        <!-- Revenue by Movie Type Pie Chart -->
        <div class="col-md-6">
            <div class="stat-card">
                <h5 class="mb-4">
                    <i class="fas fa-chart-pie me-2 text-info"></i>Phân bố doanh thu theo loại phim
                </h5>
                <div style="position: relative; height: 300px;">
                    <canvas id="movieTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Movies Table -->
    <div class="row">
        <div class="col-md-12">
            <div class="stat-card">
                <h5 class="mb-4">
                    <i class="fas fa-list me-2 text-success"></i>Top 10 phim doanh thu cao nhất - Chi tiết
                </h5>
                @if($topMoviesByRevenue && $topMoviesByRevenue->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th width="60">#</th>
                                    <th>Tên phim</th>
                                    <th width="150">Số vé</th>
                                    <th width="200">Doanh thu</th>
                                    <th width="150">% Tổng doanh thu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    $totalRevenue = $topMoviesByRevenue->sum('revenue');
                                @endphp
                                @foreach($topMoviesByRevenue as $index => $movie)
                                <tr>
                                    <td>
                                        @if($index == 0)
                                            <i class="fas fa-crown text-warning"></i>
                                        @elseif($index == 1)
                                            <i class="fas fa-medal text-secondary"></i>
                                        @elseif($index == 2)
                                            <i class="fas fa-medal" style="color: #cd7f32;"></i>
                                        @else
                                            {{ $index + 1 }}
                                        @endif
                                    </td>
                                    <td><strong>{{ $movie->title }}</strong></td>
                                    <td>{{ number_format($movie->ticket_count) }} vé</td>
                                    <td>
                                        <strong class="text-primary">{{ number_format($movie->revenue) }}₫</strong>
                                    </td>
                                    <td>
                                        @php
                                            $percentage = $totalRevenue > 0 ? ($movie->revenue / $totalRevenue * 100) : 0;
                                        @endphp
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height: 20px;">
                                                <div class="progress-bar bg-success" role="progressbar" 
                                                     style="width: {{ $percentage }}%"
                                                     aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100">
                                                </div>
                                            </div>
                                            <span class="badge bg-success">{{ number_format($percentage, 1) }}%</span>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Chưa có dữ liệu doanh thu</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

@php
    $movieTypeRevenue = [
        ['Phim lẻ', (float) ($topMoviesByRevenue ?? collect())->where('type', 'phimle')->sum('revenue')],
        ['Phim bộ', (float) ($topMoviesByRevenue ?? collect())->where('type', 'phimbo')->sum('revenue')],
    ];
@endphp

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Revenue Chart
    const ctx = document.getElementById('revenueChart');
    if (ctx) {
        const revenueData = @json($revenueData ?? []);
        
        const formatDate = (dateString) => {
            const date = new Date(dateString);
            return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });
        };
        
        const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 350);
        gradient.addColorStop(0, 'rgba(102, 126, 234, 0.4)');
        gradient.addColorStop(1, 'rgba(118, 75, 162, 0.1)');
        
        const labels = revenueData.length > 0 
            ? revenueData.map(item => formatDate(item.period))
            : ['Chưa có dữ liệu'];
        
        const data = revenueData.length > 0 
            ? revenueData.map(item => parseFloat(item.revenue) || 0)
            : [0];
        
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Doanh thu (₫)',
                    data: data,
                    borderColor: 'rgba(102, 126, 234, 1)',
                    backgroundColor: gradient,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 6,
                    pointBackgroundColor: '#fff',
                    pointBorderColor: 'rgba(102, 126, 234, 1)',
                    pointBorderWidth: 3,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: 'rgba(102, 126, 234, 1)',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        align: 'end',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                size: 13,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        },
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed.y;
                                return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(value) + '₫';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return (value / 1000000).toFixed(1) + 'M';
                                } else if (value >= 1000) {
                                    return (value / 1000).toFixed(0) + 'K';
                                }
                                return value;
                            },
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        }
                    },
                    x: {
                        ticks: {
                            font: {
                                size: 12
                            }
                        },
                        grid: {
                            display: false
                        }
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }
    
    // Top Movies Bar Chart
    const topMoviesCtx = document.getElementById('topMoviesChart');
    if (topMoviesCtx) {
        const topMovies = @json($topMoviesByRevenue ?? collect());
        const top5Movies = topMovies.slice(0, 5);
        
        const movieLabels = top5Movies.map(movie => {
            const title = movie.title;
            return title.length > 20 ? title.substring(0, 20) + '...' : title;
        });
        const movieRevenues = top5Movies.map(movie => parseFloat(movie.revenue) || 0);
        
        const barColors = [
            'rgba(255, 193, 7, 0.8)',   // Gold
            'rgba(192, 192, 192, 0.8)',  // Silver
            'rgba(205, 127, 50, 0.8)',   // Bronze
            'rgba(102, 126, 234, 0.8)',  // Purple
            'rgba(56, 239, 125, 0.8)'    // Green
        ];
        
        new Chart(topMoviesCtx, {
            type: 'bar',
            data: {
                labels: movieLabels,
                datasets: [{
                    label: 'Doanh thu (₫)',
                    data: movieRevenues,
                    backgroundColor: barColors,
                    borderColor: barColors.map(color => color.replace('0.8', '1')),
                    borderWidth: 2,
                    borderRadius: 8,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const value = context.parsed.y;
                                return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(value) + '₫';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                if (value >= 1000000) {
                                    return (value / 1000000).toFixed(1) + 'M';
                                } else if (value >= 1000) {
                                    return (value / 1000).toFixed(0) + 'K';
                                }
                                return value;
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });
    }
    
    // Movie Type Pie Chart
    const movieTypeCtx = document.getElementById('movieTypeChart');
    if (movieTypeCtx) {
        const movieTypes = @json($movieTypeRevenue);
        
        const typeLabels = movieTypes.map(item => item[0]);
        const typeData = movieTypes.map(item => parseFloat(item[1]) || 0);
        
        new Chart(movieTypeCtx, {
            type: 'doughnut',
            data: {
                labels: typeLabels,
                datasets: [{
                    data: typeData,
                    backgroundColor: [
                        'rgba(102, 126, 234, 0.8)',
                        'rgba(56, 239, 125, 0.8)',
                        'rgba(255, 193, 7, 0.8)',
                        'rgba(244, 67, 54, 0.8)'
                    ],
                    borderColor: [
                        'rgba(102, 126, 234, 1)',
                        'rgba(56, 239, 125, 1)',
                        'rgba(255, 193, 7, 1)',
                        'rgba(244, 67, 54, 1)'
                    ],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            font: {
                                size: 13
                            },
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        padding: 12,
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? ((value / total) * 100).toFixed(1) : 0;
                                return label + ': ' + new Intl.NumberFormat('vi-VN').format(value) + '₫ (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endsection
