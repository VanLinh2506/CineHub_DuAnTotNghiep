<!-- Stats Cards with Animation -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card animated-card" data-delay="0">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Tổng người dùng</div>
                    <div class="stat-value text-primary counter" data-target="<?php echo $stats['total_users']; ?>">0</div>
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
                    <div class="stat-value text-success counter" data-target="<?php echo $stats['total_movies']; ?>">0</div>
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
                    <div class="stat-value text-info counter" data-target="<?php echo $stats['total_tickets']; ?>">0</div>
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
                    <div class="stat-value text-warning revenue-counter" data-target="<?php echo $stats['total_revenue']; ?>">0₫</div>
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

<!-- Revenue Cards with Animation -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stat-card animated-card revenue-card" data-delay="400">
            <div class="stat-label">Doanh thu hôm nay</div>
            <div class="stat-value text-success revenue-counter" data-target="<?php echo $stats['today_revenue']; ?>">0₫</div>
            <div class="stat-trend">
                <i class="fas fa-arrow-up text-success"></i>
                <small class="text-muted">So với hôm qua</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card animated-card revenue-card" data-delay="500">
            <div class="stat-label">Doanh thu tuần này</div>
            <div class="stat-value text-info revenue-counter" data-target="<?php echo $stats['week_revenue']; ?>">0₫</div>
            <div class="stat-trend">
                <i class="fas fa-chart-line text-info"></i>
                <small class="text-muted">7 ngày qua</small>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card animated-card revenue-card" data-delay="600">
            <div class="stat-label">Doanh thu tháng này</div>
            <div class="stat-value text-primary revenue-counter" data-target="<?php echo $stats['month_revenue']; ?>">0₫</div>
            <div class="stat-trend">
                <i class="fas fa-calendar-alt text-primary"></i>
                <small class="text-muted">Tháng hiện tại</small>
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
    
    <!-- Quick Stats -->
    <div class="col-md-4">
        <div class="stat-card mb-3 animated-card" data-delay="800">
            <h6 class="mb-3" style="color: #333; font-weight: 600;">
                <i class="fas fa-bolt text-warning me-2"></i>Thống kê nhanh
            </h6>
            <div class="mb-3">
                <small class="text-muted d-block mb-1">Người dùng hoạt động hôm nay</small>
                <div class="fw-bold text-primary fs-4 counter" data-target="<?php echo $stats['active_users_today']; ?>">0</div>
            </div>
            <div>
                <small class="text-muted d-block mb-1">Ticket hỗ trợ chờ xử lý</small>
                <div class="fw-bold text-warning fs-4 counter" data-target="<?php echo $stats['pending_tickets']; ?>">0</div>
            </div>
        </div>
        
        <div class="stat-card animated-card" data-delay="900">
            <h6 class="mb-3" style="color: #333; font-weight: 600;">
                <i class="fas fa-trophy text-warning me-2"></i>Top phim xem nhiều
            </h6>
            <?php if (empty($topMovies)): ?>
                <p class="text-muted">Chưa có dữ liệu</p>
            <?php else: ?>
                <ul class="list-unstyled mb-0">
                    <?php foreach ($topMovies as $index => $movie): ?>
                        <li class="mb-3 pb-2 border-bottom movie-item" style="animation-delay: <?php echo ($index * 100); ?>ms;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <span class="badge bg-primary me-2 rank-badge"><?php echo $index + 1; ?></span>
                                    <span class="movie-title"><?php echo htmlspecialchars($movie['title']); ?></span>
                                </div>
                                <span class="badge bg-info"><?php echo $movie['view_count']; ?> lượt</span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
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
                        <?php if (empty($upcomingShowtimes)): ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted">Không có suất chiếu sắp tới</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($upcomingShowtimes as $index => $showtime): ?>
                                <tr class="table-row-animated" style="animation-delay: <?php echo ($index * 50); ?>ms;">
                                    <td><strong><?php echo htmlspecialchars($showtime['movie_title']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($showtime['theater_name']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($showtime['show_date'])); ?></td>
                                    <td><span class="badge bg-primary"><?php echo date('H:i', strtotime($showtime['show_time'])); ?></span></td>
                                    <td class="text-success fw-bold"><?php echo number_format($showtime['price']); ?>₫</td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
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

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
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
    margin-top: 10px;
    display: flex;
    align-items: center;
    gap: 5px;
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

.table-row-animated {
    animation: fadeInUp 0.5s ease-out forwards;
    opacity: 0;
}

.table-row-animated:hover {
    background: #f8f9fa;
    transform: scale(1.01);
    transition: all 0.3s ease;
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
