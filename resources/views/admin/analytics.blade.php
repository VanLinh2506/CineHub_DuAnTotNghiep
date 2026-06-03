@extends('layouts.app')

@php
    $title = 'Phân tích doanh thu';
@endphp

@section('content')
<div class="container">
    <!-- Stat Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Tổng doanh thu</div>
                <div class="stat-value text-primary">{{ number_format($totalRevenue ?? 0) }}₫</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Vé bán hôm nay</div>
                <div class="stat-value text-success">{{ number_format($todayTickets ?? 0) }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Doanh thu hôm nay</div>
                <div class="stat-value text-info">{{ number_format($todayRevenue ?? 0) }}₫</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Trung bình/vé</div>
                <div class="stat-value text-warning">{{ number_format($avgPrice ?? 0) }}₫</div>
            </div>
        </div>
    </div>

    <!-- Revenue Chart -->
    <div class="stat-card mb-4">
        <h6 class="mb-3">
            <i class="fas fa-chart-line me-2"></i>Doanh thu 30 ngày gần nhất
        </h6>
        <div style="position: relative; height: 400px;">
            <canvas id="revenueChart"></canvas>
        </div>
    </div>

    <!-- Top Movies Chart -->
    <div class="row">
        <div class="col-md-6">
            <div class="stat-card">
                <h6 class="mb-3">
                    <i class="fas fa-film me-2"></i>Phim bán chạy nhất
                </h6>
                @if (!empty($topMovies))
                    <div style="position: relative; height: 300px;">
                        <canvas id="topMoviesChart"></canvas>
                    </div>
                @else
                    <p class="text-muted">Chưa có dữ liệu</p>
                @endif
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card">
                <h6 class="mb-3">
                    <i class="fas fa-percent me-2"></i>Phân bố theo rạp
                </h6>
                @if (!empty($theaterDistribution))
                    <div style="position: relative; height: 300px;">
                        <canvas id="theaterChart"></canvas>
                    </div>
                @else
                    <p class="text-muted">Chưa có dữ liệu</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Statistics Table -->
    <div class="stat-card mt-4">
        <h6 class="mb-3">
            <i class="fas fa-table me-2"></i>Chi tiết thống kê từng rạp
        </h6>
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>Rạp</th>
                        <th>Vé bán</th>
                        <th>Doanh thu</th>
                        <th>Trung bình/vé</th>
                        <th>Tỷ lệ</th>
                    </tr>
                </thead>
                <tbody>
                    @if (empty($theaterStats))
                        <tr>
                            <td colspan="5" class="text-center text-muted">Chưa có dữ liệu</td>
                        </tr>
                    @else
                        @foreach ($theaterStats as $stat)
                            @php
                                $avgPerTicket = $stat['ticket_count'] > 0 ? $stat['revenue'] / $stat['ticket_count'] : 0;
                                $totalRevenue = array_sum(array_column($theaterStats, 'revenue'));
                                $percentage = $totalRevenue > 0 ? ($stat['revenue'] / $totalRevenue) * 100 : 0;
                            @endphp
                            <tr>
                                <td><strong>{{ $stat['theater_name'] }}</strong></td>
                                <td>{{ number_format($stat['ticket_count']) }}</td>
                                <td>{{ number_format($stat['revenue']) }}₫</td>
                                <td>{{ number_format($avgPerTicket) }}₫</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress" style="width: 100px; height: 20px;">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $percentage }}%;">
                                            </div>
                                        </div>
                                        <span>{{ round($percentage, 1) }}%</span>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Placeholder for charts - implement with actual data
    // Charts would need proper data passed from controller
</script>
@endsection
