<!-- Theater Info Card with Animation -->
<div class="row mb-4">
    <div class="col-12">
        <div class="stat-card animated-card theater-info-card" data-delay="0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h5 class="mb-2" style="color: #333; font-weight: 600;">
                        <i class="fas fa-building text-primary me-2"></i><?php echo htmlspecialchars($theater['name']); ?>
                    </h5>
                    <p class="text-muted mb-1">
                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($theater['location'] ?? 'N/A'); ?>
                    </p>
                    <?php if ($theater['address']): ?>
                        <p class="text-muted mb-1">
                            <i class="fas fa-address-card"></i> <?php echo htmlspecialchars($theater['address']); ?>
                        </p>
                    <?php endif; ?>
                    <?php if ($theater['phone']): ?>
                        <p class="text-muted mb-0">
                            <i class="fas fa-phone"></i> <?php echo htmlspecialchars($theater['phone']); ?>
                        </p>
                    <?php endif; ?>
                </div>
                <div class="text-end">
                    <span class="badge bg-success fs-6 px-3 py-2">
                        <i class="fas fa-door-open me-1"></i><?php echo $theater['total_screens']; ?> phòng chiếu
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards with Animation -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card animated-card" data-delay="100">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Tổng suất chiếu</div>
                    <div class="stat-value text-primary counter" data-target="<?php echo $stats['total_showtimes']; ?>">0</div>
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
                    <div class="stat-value text-info counter" data-target="<?php echo $stats['today_showtimes']; ?>">0</div>
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
                    <div class="stat-value text-success counter" data-target="<?php echo $stats['total_tickets']; ?>">0</div>
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
                    <div class="stat-label">Vé hôm nay</div>
                    <div class="stat-value text-warning counter" data-target="<?php echo $stats['today_tickets']; ?>">0</div>
                </div>
                <div class="stat-icon bg-warning">
                    <i class="fas fa-ticket-alt"></i>
                </div>
            </div>
            <div class="stat-progress">
                <div class="progress-bar bg-warning" style="width: 100%"></div>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Cards with Animation -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="stat-card animated-card revenue-card" data-delay="500">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Tổng doanh thu</div>
                    <div class="stat-value text-success revenue-counter" data-target="<?php echo $stats['total_revenue']; ?>">0₫</div>
                </div>
                <div class="stat-icon bg-success">
                    <i class="fas fa-dollar-sign"></i>
                </div>
            </div>
            <div class="stat-trend mt-3">
                <i class="fas fa-chart-line text-success"></i>
                <small class="text-muted ms-2">Tổng doanh thu từ khi bắt đầu</small>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="stat-card animated-card revenue-card" data-delay="600">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Doanh thu hôm nay</div>
                    <div class="stat-value text-primary revenue-counter" data-target="<?php echo $stats['today_revenue']; ?>">0₫</div>
                </div>
                <div class="stat-icon bg-primary">
                    <i class="fas fa-calendar-day"></i>
                </div>
            </div>
            <div class="stat-trend mt-3">
                <i class="fas fa-arrow-up text-primary"></i>
                <small class="text-muted ms-2">Doanh thu trong ngày</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Revenue Chart with Enhanced Design -->
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
    
    <!-- Top Movies -->
    <div class="col-md-4">
        <div class="stat-card animated-card" data-delay="800">
            <h6 class="mb-3" style="color: #333; font-weight: 600;">
                <i class="fas fa-trophy text-warning me-2"></i>Top phim bán chạy
            </h6>
            <?php if (empty($topMovies)): ?>
                <p class="text-muted">Chưa có dữ liệu</p>
            <?php else: ?>
                <ul class="list-unstyled mb-0">
                    <?php foreach ($topMovies as $index => $movie): ?>
                        <li class="mb-3 pb-2 border-bottom movie-item" style="animation-delay: <?php echo ($index * 100); ?>ms;">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div class="d-flex align-items-center flex-grow-1">
                                    <span class="badge bg-primary me-2 rank-badge"><?php echo $index + 1; ?></span>
                                    <span class="movie-title flex-grow-1"><?php echo htmlspecialchars($movie['title']); ?></span>
                                </div>
                                <span class="badge bg-info"><?php echo $movie['ticket_count']; ?> vé</span>
                            </div>
                            <div class="ms-4">
                                <small class="text-success fw-bold">
                                    <i class="fas fa-dollar-sign me-1"></i><?php echo number_format($movie['revenue'] ?? 0); ?>₫
                                </small>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Lịch suất chiếu sắp tới -->
<div class="row mt-4">
    <div class="col-12">
        <div class="stat-card animated-card" data-delay="900">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0" style="color: #333; font-weight: 600;">
                    <i class="fas fa-calendar-alt text-info me-2"></i>Lịch suất chiếu sắp tới
                </h6>
                <a href="?route=moderator/showtimes" class="btn btn-sm btn-outline-primary">
                    <i class="fas fa-list me-1"></i>Xem tất cả
                </a>
            </div>
            
            <?php if (empty($upcomingShowtimes)): ?>
                <div class="text-center py-4">
                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                    <p class="text-muted mb-0">Chưa có suất chiếu nào trong 7 ngày tới</p>
                    <a href="?route=moderator/showtimes" class="btn btn-primary mt-3">
                        <i class="fas fa-plus me-1"></i>Thêm suất chiếu
                    </a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 50px;"></th>
                                <th>Phim</th>
                                <th>Ngày chiếu</th>
                                <th>Giờ chiếu</th>
                                <th>Phòng</th>
                                <th>Vé đã bán</th>
                                <th>Giá vé</th>
                                <th class="text-center">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $currentDate = '';
                            foreach ($upcomingShowtimes as $showtime): 
                                $isToday = $showtime['show_date'] === date('Y-m-d');
                                $isTomorrow = $showtime['show_date'] === date('Y-m-d', strtotime('+1 day'));
                                $showDate = $showtime['show_date'];
                                
                                // Hiển thị header ngày nếu ngày thay đổi
                                if ($currentDate !== $showDate):
                                    $currentDate = $showDate;
                                    $dateLabel = $isToday ? 'Hôm nay' : ($isTomorrow ? 'Ngày mai' : date('d/m/Y', strtotime($showDate)));
                            ?>
                            <tr class="table-secondary">
                                <td colspan="8" class="fw-bold py-2">
                                    <i class="fas fa-calendar-day me-2"></i><?php echo $dateLabel; ?>
                                    <?php if ($isToday): ?>
                                        <span class="badge bg-success ms-2">Hôm nay</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endif; ?>
                            <tr class="showtime-row <?php echo $isToday ? 'table-warning-light' : ''; ?>">
                                <td>
                                    <?php if (!empty($showtime['thumbnail'])): ?>
                                        <img src="<?php echo htmlspecialchars($showtime['thumbnail']); ?>" 
                                             alt="<?php echo htmlspecialchars($showtime['movie_title']); ?>"
                                             class="rounded" style="width: 40px; height: 55px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-secondary rounded d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 55px;">
                                            <i class="fas fa-film text-white"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="fw-semibold"><?php echo htmlspecialchars($showtime['movie_title']); ?></div>
                                    <?php if (!empty($showtime['duration'])): ?>
                                        <small class="text-muted"><?php echo $showtime['duration']; ?> phút</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="<?php echo $isToday ? 'text-success fw-bold' : ''; ?>">
                                        <?php echo date('d/m', strtotime($showtime['show_date'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-primary fs-6">
                                        <?php echo date('H:i', strtotime($showtime['show_time'])); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $showtime['screen_type'] === '3D' ? 'danger' : 'secondary'; ?>">
                                        <?php echo htmlspecialchars($showtime['screen_name']); ?>
                                    </span>
                                    <small class="d-block text-muted"><?php echo $showtime['screen_type']; ?></small>
                                </td>
                                <td>
                                    <?php 
                                    $ticketsSold = $showtime['tickets_sold'] ?? 0;
                                    $totalSeats = $showtime['total_seats'] ?? 100;
                                    $percentage = $totalSeats > 0 ? round(($ticketsSold / $totalSeats) * 100) : 0;
                                    $progressClass = $percentage >= 80 ? 'bg-success' : ($percentage >= 50 ? 'bg-warning' : 'bg-info');
                                    ?>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1 me-2" style="height: 8px; width: 60px;">
                                            <div class="progress-bar <?php echo $progressClass; ?>" 
                                                 style="width: <?php echo $percentage; ?>%"></div>
                                        </div>
                                        <small class="text-nowrap"><?php echo $ticketsSold; ?>/<?php echo $totalSeats; ?></small>
                                    </div>
                                </td>
                                <td>
                                    <span class="text-success fw-bold">
                                        <?php echo number_format($showtime['price']); ?>₫
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="?route=moderator/showtimes&date=<?php echo $showtime['show_date']; ?>" 
                                       class="btn btn-sm btn-outline-info" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
/* Animation Styles */
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

@keyframes slideInLeft {
    from {
        opacity: 0;
        transform: translateX(-30px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
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

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #667eea 0%, #764ba2 100%);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.3s ease;
}

.stat-card:hover::before {
    transform: scaleX(1);
}

.theater-info-card {
    background: linear-gradient(135deg, #ffffff 0%, #e8f4f8 100%);
}

.theater-info-card:hover {
    background: linear-gradient(135deg, #ffffff 0%, #d4e8f0 100%);
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
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-progress {
    margin-top: 15px;
    height: 4px;
    background: #e9ecef;
    border-radius: 2px;
    overflow: hidden;
}

.progress-bar {
    height: 100%;
    transition: width 1s ease;
    border-radius: 2px;
}

.revenue-card {
    background: linear-gradient(135deg, #ffffff 0%, #f0f4ff 100%);
}

.revenue-card:hover {
    background: linear-gradient(135deg, #ffffff 0%, #e8f0ff 100%);
}

.stat-trend {
    display: flex;
    align-items: center;
}

.chart-card {
    background: linear-gradient(135deg, #ffffff 0%, #fafbfc 100%);
}

.chart-legend {
    display: flex;
    gap: 15px;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    color: #6c757d;
}

.legend-color {
    width: 12px;
    height: 12px;
    border-radius: 3px;
}

.movie-item {
    animation: slideInLeft 0.5s ease-out forwards;
    opacity: 0;
}

.movie-item:hover {
    background: #f8f9fa;
    padding-left: 5px;
    border-radius: 5px;
    transition: all 0.3s ease;
}

.rank-badge {
    width: 28px;
    height: 28px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    font-weight: 600;
}

/* Upcoming Showtimes Styles */
.showtime-row {
    transition: all 0.2s ease;
}

.showtime-row:hover {
    background-color: #f8f9fa !important;
    transform: translateX(5px);
}

.table-warning-light {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.table-warning-light:hover {
    background-color: rgba(255, 193, 7, 0.2) !important;
}

.progress {
    background-color: #e9ecef;
    border-radius: 4px;
}
</style>

<script>
// Counter Animation
function animateCounter(element, target, isRevenue = false) {
    const duration = 2000;
    const start = 0;
    const increment = target / (duration / 16);
    let current = start;
    
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        
        if (isRevenue) {
            element.textContent = new Intl.NumberFormat('vi-VN').format(Math.floor(current)) + '₫';
        } else {
            element.textContent = new Intl.NumberFormat('vi-VN').format(Math.floor(current));
        }
    }, 16);
}

// Initialize counters when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Animate cards
    const cards = document.querySelectorAll('.animated-card');
    cards.forEach(card => {
        const delay = parseInt(card.getAttribute('data-delay')) || 0;
        setTimeout(() => {
            card.style.opacity = '1';
        }, delay);
    });
    
    // Animate counters
    const counters = document.querySelectorAll('.counter');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target')) || 0;
        setTimeout(() => {
            animateCounter(counter, target);
        }, 500);
    });
    
    // Animate revenue counters
    const revenueCounters = document.querySelectorAll('.revenue-counter');
    revenueCounters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target')) || 0;
        setTimeout(() => {
            animateCounter(counter, target, true);
        }, 500);
    });
    
    // Animate progress bars
    const progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(bar => {
        setTimeout(() => {
            bar.style.width = bar.style.width || '100%';
        }, 800);
    });
});

// Enhanced Revenue Chart - Đợi Chart.js load xong
function initRevenueChart() {
    if (typeof Chart === 'undefined') {
        console.warn('Chart.js chưa được load, thử lại sau 100ms...');
        setTimeout(initRevenueChart, 100);
        return;
    }
    
    const ctx = document.getElementById('revenueChart');
    if (!ctx) {
        return;
    }
    
    const revenueData = <?php echo json_encode($revenueByDay); ?>;
    
    const formatDate = (dateString) => {
        const date = new Date(dateString);
        return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });
    };
    
    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(102, 126, 234, 0.4)');
    gradient.addColorStop(1, 'rgba(118, 75, 162, 0.1)');
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: revenueData.map(item => formatDate(item.date)),
            datasets: [{
                label: 'Doanh thu (₫)',
                data: revenueData.map(item => parseFloat(item.revenue || 0)),
                borderColor: 'rgba(102, 126, 234, 1)',
                backgroundColor: gradient,
                borderWidth: 3,
                fill: true,
                tension: 0.5,
                pointRadius: 5,
                pointHoverRadius: 8,
                pointBackgroundColor: '#fff',
                pointBorderColor: 'rgba(102, 126, 234, 1)',
                pointBorderWidth: 3,
                pointHoverBackgroundColor: 'rgba(102, 126, 234, 1)',
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 3,
                animation: {
                    duration: 2000,
                    easing: 'easeOutQuart'
                }
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: 'rgba(0, 0, 0, 0.8)',
                    padding: 15,
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
                            return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN', {
                                style: 'currency',
                                currency: 'VND'
                            }).format(value);
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
                                return (value / 1000000).toFixed(1) + 'M₫';
                            } else if (value >= 1000) {
                                return (value / 1000).toFixed(0) + 'K₫';
                            }
                            return value + '₫';
                        },
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        color: 'rgba(0, 0, 0, 0.05)',
                        drawBorder: false
                    }
                },
                x: {
                    grid: {
                        display: false
                    },
                    ticks: {
                        font: {
                            size: 11
                        }
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

// Khởi tạo chart khi DOM và Chart.js đã sẵn sàng
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(initRevenueChart, 100);
    });
} else {
    setTimeout(initRevenueChart, 100);
}
</script>
