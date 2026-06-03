@extends('admin.moderator.layout')

@section('content')
<!-- Theater Info Card -->
<div class="row mb-4">
    <div class="col-12">
        <div class="stat-card animated-card theater-info-card" data-delay="0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-2" style="color: #333; font-weight: 600;">
                        <i class="fas fa-building text-primary me-2"></i>{{ $theater['name'] }}
                    </h5>
                    <p class="text-muted mb-1"><i class="fas fa-map-marker-alt"></i> {{ $theater['location'] ?? 'N/A' }}</p>
                    @if($theater['address'])
                        <p class="text-muted mb-1"><i class="fas fa-address-card"></i> {{ $theater['address'] }}</p>
                    @endif
                    @if($theater['phone'])
                        <p class="text-muted mb-0"><i class="fas fa-phone"></i> {{ $theater['phone'] }}</p>
                    @endif
                </div>
                <div class="text-end">
                    <span class="badge bg-success fs-6 px-3 py-2">
                        <i class="fas fa-door-open me-1"></i>{{ $theater['total_screens'] }} phòng chiếu
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    @foreach([
        ['label'=>'Tổng suất chiếu','value'=>$stats['total_showtimes'],'color'=>'primary','icon'=>'calendar-alt','key'=>'total_showtimes'],
        ['label'=>'Suất chiếu hôm nay','value'=>$stats['today_showtimes'],'color'=>'info','icon'=>'calendar-day','key'=>'today_showtimes'],
        ['label'=>'Tổng vé đã bán','value'=>$stats['total_tickets'],'color'=>'success','icon'=>'ticket-alt','key'=>'total_tickets'],
        ['label'=>'Vé hôm nay','value'=>$stats['today_tickets'],'color'=>'warning','icon'=>'ticket-alt','key'=>'today_tickets'],
    ] as $stat)
    <div class="col-md-3">
        <div class="stat-card animated-card" data-delay="{{ $loop->index * 100 + 100 }}">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">{{ $stat['label'] }}</div>
                    <div class="stat-value text-{{ $stat['color'] }} counter" data-target="{{ $stat['value'] }}">0</div>
                </div>
                <div class="stat-icon bg-{{ $stat['color'] }}"><i class="fas fa-{{ $stat['icon'] }}"></i></div>
            </div>
            <div class="stat-progress"><div class="progress-bar bg-{{ $stat['color'] }}" style="width:100%"></div></div>
        </div>
    </div>
    @endforeach
</div>

<!-- Revenue Cards -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="stat-card animated-card revenue-card" data-delay="500">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Tổng doanh thu</div>
                    <div class="stat-value text-success revenue-counter" data-target="{{ $stats['total_revenue'] }}">0₫</div>
                </div>
                <div class="stat-icon bg-success"><i class="fas fa-dollar-sign"></i></div>
            </div>
            <div class="stat-trend mt-3"><i class="fas fa-chart-line text-success"></i><small class="text-muted ms-2">Tổng doanh thu từ khi bắt đầu</small></div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card animated-card revenue-card" data-delay="600">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Doanh thu hôm nay</div>
                    <div class="stat-value text-primary revenue-counter" data-target="{{ $stats['today_revenue'] }}">0₫</div>
                </div>
                <div class="stat-icon bg-primary"><i class="fas fa-calendar-day"></i></div>
            </div>
            <div class="stat-trend mt-3"><i class="fas fa-arrow-up text-primary"></i><small class="text-muted ms-2">Doanh thu trong ngày</small></div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Revenue Chart -->
    <div class="col-md-8">
        <div class="stat-card animated-card chart-card" data-delay="700">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0" style="color: #333; font-weight: 600;"><i class="fas fa-chart-line text-primary me-2"></i>Doanh thu 7 ngày gần nhất</h5>
            </div>
            <canvas id="revenueChart" height="80"></canvas>
        </div>
    </div>
    <!-- Top Movies -->
    <div class="col-md-4">
        <div class="stat-card animated-card" data-delay="800">
            <h6 class="mb-3" style="color: #333; font-weight: 600;"><i class="fas fa-trophy text-warning me-2"></i>Top phim bán chạy</h6>
            @if(empty($topMovies))
                <p class="text-muted">Chưa có dữ liệu</p>
            @else
                <ul class="list-unstyled mb-0">
                    @foreach($topMovies as $index => $movie)
                    <li class="mb-3 pb-2 border-bottom movie-item" style="animation-delay: {{ $index * 100 }}ms;">
                        <div class="d-flex justify-content-between align-items-start mb-1">
                            <div class="d-flex align-items-center flex-grow-1">
                                <span class="badge bg-primary me-2 rank-badge">{{ $index + 1 }}</span>
                                <span class="flex-grow-1">{{ $movie['title'] }}</span>
                            </div>
                            <span class="badge bg-info">{{ $movie['ticket_count'] }} vé</span>
                        </div>
                        <div class="ms-4">
                            <small class="text-success fw-bold"><i class="fas fa-dollar-sign me-1"></i>{{ number_format($movie['revenue'] ?? 0) }}₫</small>
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
        <div class="stat-card animated-card" data-delay="900">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0" style="color: #333; font-weight: 600;"><i class="fas fa-calendar-alt text-info me-2"></i>Lịch suất chiếu sắp tới</h6>
                <a href="?route=moderator/showtimes" class="btn btn-sm btn-outline-primary"><i class="fas fa-list me-1"></i>Xem tất cả</a>
            </div>
            @if(empty($upcomingShowtimes))
                <div class="text-center py-4">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">Chưa có suất chiếu nào trong 7 ngày tới</p>
                    <a href="?route=moderator/showtimes" class="btn btn-primary mt-3"><i class="fas fa-plus me-1"></i>Thêm suất chiếu</a>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th style="width:50px;"></th><th>Phim</th><th>Ngày chiếu</th><th>Giờ chiếu</th><th>Phòng</th><th>Vé đã bán</th><th>Giá vé</th><th class="text-center">Thao tác</th></tr>
                        </thead>
                        <tbody>
                            @php $currentDate = ''; @endphp
                            @foreach($upcomingShowtimes as $showtime)
                                @php
                                    $isToday = $showtime['show_date'] === date('Y-m-d');
                                    $isTomorrow = $showtime['show_date'] === date('Y-m-d', strtotime('+1 day'));
                                    $showDate = $showtime['show_date'];
                                @endphp
                                @if($currentDate !== $showDate)
                                    @php $currentDate = $showDate; $dateLabel = $isToday ? 'Hôm nay' : ($isTomorrow ? 'Ngày mai' : date('d/m/Y', strtotime($showDate))); @endphp
                                    <tr class="table-secondary">
                                        <td colspan="8" class="fw-bold py-2">
                                            <i class="fas fa-calendar-day me-2"></i>{{ $dateLabel }}
                                            @if($isToday)<span class="badge bg-success ms-2">Hôm nay</span>@endif
                                        </td>
                                    </tr>
                                @endif
                                <tr class="showtime-row {{ $isToday ? 'table-warning-light' : '' }}">
                                    <td>
                                        @if(!empty($showtime['thumbnail']))
                                            <img src="{{ $showtime['thumbnail'] }}" alt="{{ $showtime['movie_title'] }}" class="rounded" style="width:40px;height:55px;object-fit:cover;">
                                        @else
                                            <div class="bg-secondary rounded d-flex align-items-center justify-content-center" style="width:40px;height:55px;">
                                                <i class="fas fa-film text-white"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $showtime['movie_title'] }}</div>
                                        @if(!empty($showtime['duration']))<small class="text-muted">{{ $showtime['duration'] }} phút</small>@endif
                                    </td>
                                    <td><span class="{{ $isToday ? 'text-success fw-bold' : '' }}">{{ date('d/m', strtotime($showtime['show_date'])) }}</span></td>
                                    <td><span class="badge bg-primary fs-6">{{ date('H:i', strtotime($showtime['show_time'])) }}</span></td>
                                    <td>
                                        <span class="badge bg-{{ $showtime['screen_type'] === '3D' ? 'danger' : 'secondary' }}">{{ $showtime['screen_name'] }}</span>
                                        <small class="d-block text-muted">{{ $showtime['screen_type'] }}</small>
                                    </td>
                                    <td>
                                        @php
                                            $ticketsSold = $showtime['tickets_sold'] ?? 0;
                                            $totalSeats = $showtime['total_seats'] ?? 100;
                                            $percentage = $totalSeats > 0 ? round(($ticketsSold / $totalSeats) * 100) : 0;
                                            $progressClass = $percentage >= 80 ? 'bg-success' : ($percentage >= 50 ? 'bg-warning' : 'bg-info');
                                        @endphp
                                        <div class="d-flex align-items-center">
                                            <div class="progress flex-grow-1 me-2" style="height:8px;width:60px;">
                                                <div class="progress-bar {{ $progressClass }}" style="width:{{ $percentage }}%"></div>
                                            </div>
                                            <small class="text-nowrap">{{ $ticketsSold }}/{{ $totalSeats }}</small>
                                        </div>
                                    </td>
                                    <td><span class="text-success fw-bold">{{ number_format($showtime['price']) }}₫</span></td>
                                    <td class="text-center">
                                        <a href="?route=moderator/showtimes&date={{ $showtime['show_date'] }}" class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
@keyframes fadeInUp { from { opacity:0; transform:translateY(30px); } to { opacity:1; transform:translateY(0); } }
.animated-card { animation: fadeInUp 0.6s ease-out forwards; opacity:0; transition: all 0.3s ease; }
.animated-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important; }
.stat-card { position:relative; overflow:hidden; }
.stat-card::before { content:''; position:absolute; top:0; left:0; width:100%; height:4px; background:linear-gradient(90deg,#667eea 0%,#764ba2 100%); transform:scaleX(0); transform-origin:left; transition:transform 0.3s ease; }
.stat-card:hover::before { transform:scaleX(1); }
.stat-icon { width:60px; height:60px; border-radius:12px; display:flex; align-items:center; justify-content:center; font-size:24px; color:white; transition:all 0.3s ease; }
.stat-card:hover .stat-icon { transform:scale(1.1) rotate(5deg); }
.stat-value { font-size:2rem; font-weight:700; margin-top:5px; }
.stat-label { font-size:0.9rem; color:#6c757d; font-weight:500; }
.stat-progress { margin-top:15px; height:4px; background:#e9ecef; border-radius:2px; overflow:hidden; }
.progress-bar { height:100%; transition:width 1s ease; border-radius:2px; }
.rank-badge { width:28px; height:28px; display:inline-flex; align-items:center; justify-content:center; border-radius:50%; font-weight:600; }
.showtime-row { transition:all 0.2s ease; }
.showtime-row:hover { background-color:#f8f9fa !important; transform:translateX(5px); }
.table-warning-light { background-color:rgba(255,193,7,0.1) !important; }
</style>
@endpush

@push('scripts')
<script>
function animateCounter(element, target, isRevenue = false) {
    const duration = 2000, increment = target / (duration / 16);
    let current = 0;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) { current = target; clearInterval(timer); }
        element.textContent = isRevenue
            ? new Intl.NumberFormat('vi-VN').format(Math.floor(current)) + '₫'
            : new Intl.NumberFormat('vi-VN').format(Math.floor(current));
    }, 16);
}

document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.animated-card').forEach(card => {
        setTimeout(() => { card.style.opacity = '1'; }, parseInt(card.getAttribute('data-delay')) || 0);
    });
    setTimeout(() => {
        document.querySelectorAll('.counter').forEach(el => animateCounter(el, parseInt(el.getAttribute('data-target')) || 0));
        document.querySelectorAll('.revenue-counter').forEach(el => animateCounter(el, parseInt(el.getAttribute('data-target')) || 0, true));
    }, 500);
});

function initRevenueChart() {
    if (typeof Chart === 'undefined') { setTimeout(initRevenueChart, 100); return; }
    const ctx = document.getElementById('revenueChart');
    if (!ctx) return;
    const revenueData = @json($revenueByDay);
    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(102,126,234,0.4)');
    gradient.addColorStop(1, 'rgba(118,75,162,0.1)');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: revenueData.map(item => new Date(item.date).toLocaleDateString('vi-VN', {day:'2-digit',month:'2-digit'})),
            datasets: [{ label: 'Doanh thu (₫)', data: revenueData.map(item => parseFloat(item.revenue || 0)),
                borderColor: 'rgba(102,126,234,1)', backgroundColor: gradient, borderWidth: 3, fill: true, tension: 0.5,
                pointRadius: 5, pointHoverRadius: 8, pointBackgroundColor: '#fff', pointBorderColor: 'rgba(102,126,234,1)', pointBorderWidth: 3 }]
        },
        options: { responsive: true, maintainAspectRatio: true, plugins: { legend: { display: false },
            tooltip: { callbacks: { label: ctx => 'Doanh thu: ' + new Intl.NumberFormat('vi-VN',{style:'currency',currency:'VND'}).format(ctx.parsed.y) } } },
            scales: { y: { beginAtZero: true, ticks: { callback: v => v >= 1000000 ? (v/1000000).toFixed(1)+'M₫' : v >= 1000 ? (v/1000).toFixed(0)+'K₫' : v+'₫' } }, x: { grid: { display: false } } } }
    });
}

document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', () => setTimeout(initRevenueChart, 100)) : setTimeout(initRevenueChart, 100);
</script>
@endpush
