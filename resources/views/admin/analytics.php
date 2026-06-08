    <style>
    /* Modern Chart Styling */
    .chart-container {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 32px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .chart-container:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .chart-title {
        font-size: 18px;
        font-weight: 600;
        color: #2d1b3d;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .chart-title i {
        color: #6c5ce7;
        font-size: 20px;
    }

    .chart-wrapper {
        position: relative;
        height: 400px;
        margin-top: 20px;
    }

    /* Stat Card Styling */
    .stat-card {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .stat-label {
        font-size: 14px;
        font-weight: 600;
        color: #6c757d;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 28px;
        font-weight: 700;
        margin: 0;
    }

    /* Table Styling */
    .stats-table-container {
        background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 32px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .stats-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .stats-table thead {
        background: linear-gradient(135deg, #6c5ce7 0%, #5a4fcf 100%);
        color: white;
    }

    .stats-table thead th {
        padding: 16px;
        font-weight: 600;
        text-align: left;
        border: none;
        font-size: 14px;
        letter-spacing: 0.5px;
    }

    .stats-table thead th:first-child {
        border-top-left-radius: 12px;
    }

    .stats-table thead th:last-child {
        border-top-right-radius: 12px;
    }

    .stats-table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid #e9ecef;
    }

    .stats-table tbody tr:hover {
        background: linear-gradient(90deg, #f8f9ff 0%, #ffffff 100%);
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(108, 92, 231, 0.1);
    }

    .stats-table tbody td {
        padding: 16px;
        vertical-align: middle;
    }

    /* Top Movies List Styling */
    .list-unstyled li {
        transition: all 0.2s ease;
        border-radius: 8px;
        padding: 8px;
    }

    .list-unstyled li:hover {
        background: linear-gradient(90deg, #f8f9ff 0%, #ffffff 100%);
        transform: translateX(4px);
    }

    .badge {
        font-size: 12px;
        padding: 6px 10px;
        border-radius: 8px;
        font-weight: 700;
    }

    /* Section Header */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
        padding-bottom: 16px;
        border-bottom: 2px solid #e9ecef;
    }

    .section-header h5, .section-header h6 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #2d1b3d;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-header h5 i, .section-header h6 i {
        color: #6c5ce7;
    }

    /* Button Styling */
    .btn-sm {
        border-radius: 8px;
        font-weight: 600;
        padding: 8px 16px;
        transition: all 0.2s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, #6c5ce7 0%, #5a4fcf 100%);
        border: none;
        box-shadow: 0 2px 8px rgba(108, 92, 231, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(108, 92, 231, 0.4);
    }

    .btn-outline-primary {
        border: 2px solid #6c5ce7;
        color: #6c5ce7;
    }

    .btn-outline-primary:hover {
        background: linear-gradient(135deg, #6c5ce7 0%, #5a4fcf 100%);
        border-color: #6c5ce7;
        color: white;
        transform: translateY(-1px);
    }
    </style>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="section-header" style="border: none; padding: 0; margin: 0;">
            <h5><i class="fas fa-chart-line"></i> Analytics & Báo cáo</h5>
        </div>
        <div>
            <a href="?route=admin/analytics&period=day" class="btn btn-sm <?php echo ($period ?? 'month') === 'day' ? 'btn-primary' : 'btn-outline-primary'; ?>">Ngày</a>
            <a href="?route=admin/analytics&period=week" class="btn btn-sm <?php echo ($period ?? 'month') === 'week' ? 'btn-primary' : 'btn-outline-primary'; ?>">Tuần</a>
            <a href="?route=admin/analytics&period=month" class="btn btn-sm <?php echo ($period ?? 'month') === 'month' ? 'btn-primary' : 'btn-outline-primary'; ?>">Tháng</a>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Tổng doanh thu</div>
                <div class="stat-value text-warning"><?php echo number_format($summaryStats['total_revenue'] ?? 0); ?>₫</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Tổng giao dịch</div>
                <div class="stat-value text-info"><?php echo number_format($summaryStats['total_transactions'] ?? 0); ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Tổng vé bán</div>
                <div class="stat-value text-success"><?php echo number_format($summaryStats['total_tickets'] ?? 0); ?></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Giá vé trung bình</div>
                <div class="stat-value text-primary"><?php echo number_format($summaryStats['avg_ticket_price'] ?? 0); ?>₫</div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="chart-container">
                <div class="chart-title">
                    <i class="fas fa-chart-line"></i>
                    <span>Biểu đồ doanh thu theo <?php echo $period === 'day' ? 'ngày' : ($period === 'week' ? 'tuần' : 'tháng'); ?></span>
                </div>
                <?php if ($period === 'month'): ?>
                    <div class="mb-3" style="text-align: right;">
                        <a href="?route=admin/analytics&period=month&month_range=current_month" 
                        class="btn btn-sm <?php echo ($monthRange ?? '12_months') === 'current_month' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                            Tháng hiện tại
                        </a>
                        <a href="?route=admin/analytics&period=month&month_range=12_months" 
                        class="btn btn-sm <?php echo ($monthRange ?? '12_months') === '12_months' ? 'btn-primary' : 'btn-outline-primary'; ?>">
                            12 tháng gần nhất
                        </a>
                    </div>
                <?php endif; ?>
                <div class="chart-wrapper">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="chart-title">
                    <i class="fas fa-trophy"></i>
                    <span>Top phim doanh thu cao</span>
                </div>
                <?php if (empty($topMoviesByRevenue)): ?>
                    <p class="text-muted">Chưa có dữ liệu</p>
                <?php else: ?>
                    <ul class="list-unstyled">
                        <?php foreach ($topMoviesByRevenue as $index => $movie): ?>
                            <li class="mb-3 pb-3 border-bottom">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="fw-bold">
                                            <span class="badge bg-secondary me-2">#<?php echo $index + 1; ?></span>
                                            <?php echo htmlspecialchars($movie['title']); ?>
                                        </div>
                                        <div class="text-success mt-1"><?php echo number_format($movie['revenue']); ?>₫</div>
                                        <small class="text-muted"><?php echo $movie['ticket_count']; ?> vé đã bán</small>
                                    </div>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Revenue by Movie This Month -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="chart-container">
                <div class="chart-title">
                    <i class="fas fa-film"></i>
                    <span>Doanh thu các phim trong tháng (<?php echo date('m/Y'); ?>)</span>
                </div>
                <div class="chart-wrapper">
                    <canvas id="revenueByMovieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue by Theater -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="chart-container">
                <div class="chart-title">
                    <i class="fas fa-building"></i>
                    <span>Doanh thu các rạp</span>
                </div>
                <div class="chart-wrapper">
                    <canvas id="revenueByTheaterChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tỷ lệ lấp đầy giữa các phim -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="chart-container">
                <div class="chart-title">
                    <i class="fas fa-chair"></i>
                    <span>So sánh tỷ lệ lấp đầy giữa các phim</span>
                </div>
                <div class="chart-wrapper">
                    <canvas id="fillRateByMovieChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue by Payment Method -->
    <div class="row mt-4">
        <div class="col-md-6">
            <div class="chart-container">
                <div class="chart-title">
                    <i class="fas fa-credit-card"></i>
                    <span>Doanh thu theo phương thức thanh toán</span>
                </div>
                <div class="chart-wrapper" style="height: 300px;">
                    <canvas id="paymentMethodChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stats-table-container">
                <div class="chart-title">
                    <i class="fas fa-list"></i>
                    <span>Chi tiết phương thức thanh toán</span>
                </div>
                <?php if (empty($revenueByMethod)): ?>
                    <p class="text-muted text-center p-4">Chưa có dữ liệu</p>
                <?php else: ?>
                    <table class="stats-table">
                        <thead>
                            <tr>
                                <th>Phương thức</th>
                                <th class="text-end">Doanh thu</th>
                                <th class="text-end">Số giao dịch</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($revenueByMethod as $method): ?>
                                <tr>
                                    <td style="font-weight: 600; color: #2d1b3d;"><?php echo htmlspecialchars($method['method']); ?></td>
                                    <td class="text-end" style="font-weight: 700; color: #28a745; font-size: 16px;"><?php echo number_format($method['revenue']); ?>₫</td>
                                    <td class="text-end" style="font-weight: 600; color: #495057;"><?php echo number_format($method['count']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
    // Đợi DOM và Chart.js load xong
    function initCharts() {
        // Kiểm tra Chart.js đã load chưa
        if (typeof Chart === 'undefined') {
            console.warn('Chart.js chưa được load, thử lại sau 100ms...');
            setTimeout(initCharts, 100);
            return;
        }
        
        console.log('Chart.js đã sẵn sàng, bắt đầu khởi tạo biểu đồ...');
        
        // Revenue Chart
        const revenueCtx = document.getElementById('revenueChart');
        if (revenueCtx) {
            const revenueData = <?php echo json_encode($revenueData ?? []); ?>;
            
            if (revenueData && revenueData.length > 0) {
                new Chart(revenueCtx, {
                    type: 'line',
                    data: {
                        labels: revenueData.map(item => item.period),
                    datasets: [{
                        label: 'Doanh thu (₫)',
                        data: revenueData.map(item => parseFloat(item.revenue || 0)),
                        borderColor: '#6c5ce7',
                        backgroundColor: 'rgba(108, 92, 231, 0.08)',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#6c5ce7',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 3,
                        pointHoverBackgroundColor: '#5a4fcf',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 3
                    }, {
                        label: 'Số giao dịch',
                        data: revenueData.map(item => parseFloat(item.transaction_count || 0)),
                        borderColor: '#fd79a8',
                        backgroundColor: 'rgba(253, 121, 168, 0.08)',
                        yAxisID: 'y1',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: false,
                        pointRadius: 5,
                        pointHoverRadius: 7,
                        pointBackgroundColor: '#fd79a8',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 3,
                        pointHoverBackgroundColor: '#e84393',
                        pointHoverBorderColor: '#fff',
                        pointHoverBorderWidth: 3
                    }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        if (context.datasetIndex === 0) {
                                            const value = context.parsed.y;
                                            return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN', {
                                                style: 'currency',
                                                currency: 'VND'
                                            }).format(value);
                                        } else {
                                            return 'Giao dịch: ' + context.parsed.y;
                                        }
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                position: 'left',
                                ticks: {
                                    callback: function(value) {
                                        if (value >= 1000000) {
                                            return (value / 1000000).toFixed(1) + 'M₫';
                                        } else if (value >= 1000) {
                                            return (value / 1000).toFixed(0) + 'K₫';
                                        }
                                        return value + '₫';
                                    }
                                },
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.02)',
                                    lineWidth: 1
                                }
                            },
                            y1: {
                                beginAtZero: true,
                                position: 'right',
                                grid: {
                                    drawOnChartArea: false,
                                },
                                ticks: {
                                    stepSize: 1
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    font: {
                                        size: 11,
                                        weight: '600'
                                    },
                                    color: '#495057'
                                }
                            }
                        }
                    }
                });
            } else {
                revenueCtx.parentElement.innerHTML = '<p class="text-muted text-center p-4">Chưa có dữ liệu doanh thu</p>';
            }
        }

        // Payment Method Chart
        const paymentCtx = document.getElementById('paymentMethodChart');
        if (paymentCtx) {
            const paymentData = <?php echo json_encode($revenueByMethod ?? []); ?>;
            
            console.log('Payment Method Data:', paymentData);
            
            if (paymentData && paymentData.length > 0) {
                const colors = [
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 206, 86, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)'
                ];
                
                new Chart(paymentCtx, {
                    type: 'doughnut',
                    data: {
                        labels: paymentData.map(item => item.method),
                        datasets: [{
                            label: 'Doanh thu (₫)',
                            data: paymentData.map(item => parseFloat(item.revenue || 0)),
                            backgroundColor: colors.slice(0, paymentData.length),
                            borderColor: colors.map(c => c.replace('0.8', '1')),
                            borderWidth: 2
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        plugins: {
                            legend: {
                                position: 'bottom',
                            },
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const value = context.parsed;
                                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                        const percentage = ((value / total) * 100).toFixed(1);
                                        return context.label + ': ' + new Intl.NumberFormat('vi-VN', {
                                            style: 'currency',
                                            currency: 'VND'
                                        }).format(value) + ' (' + percentage + '%)';
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                console.warn('Không có dữ liệu doanh thu theo phương thức thanh toán');
                paymentCtx.parentElement.innerHTML = '<p class="text-muted text-center p-4">Chưa có dữ liệu doanh thu theo phương thức thanh toán</p>';
            }
        }

        // Revenue by Movie This Month Chart
        const revenueByMovieCtx = document.getElementById('revenueByMovieChart');
        if (revenueByMovieCtx) {
            const revenueByMovieData = <?php echo json_encode($revenueByMovieThisMonth ?? []); ?>;
            
            console.log('Revenue by Movie Data:', revenueByMovieData);
            
            if (revenueByMovieData && revenueByMovieData.length > 0) {
                new Chart(revenueByMovieCtx, {
                    type: 'bar',
                    data: {
                        labels: revenueByMovieData.map(item => item.title),
                            datasets: [{
                            label: 'Doanh thu (₫)',
                            data: revenueByMovieData.map(item => parseFloat(item.revenue || 0)),
                            backgroundColor: 'rgba(108, 92, 231, 0.8)',
                            borderColor: '#6c5ce7',
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        animation: {
                            duration: 1500,
                            easing: 'easeInOutQuart'
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                backgroundColor: 'rgba(45, 27, 61, 0.95)',
                                padding: 12,
                                titleFont: {
                                    size: 14,
                                    weight: '700'
                                },
                                bodyFont: {
                                    size: 13,
                                    weight: '600'
                                },
                                borderColor: '#6c5ce7',
                                borderWidth: 2,
                                cornerRadius: 8,
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
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.02)',
                                    drawBorder: false,
                                    lineWidth: 1
                                },
                                ticks: {
                                    font: {
                                        size: 12,
                                        weight: '600'
                                    },
                                    callback: function(value) {
                                        if (value >= 1000000) {
                                            return (value / 1000000).toFixed(1) + 'M₫';
                                        } else if (value >= 1000) {
                                            return (value / 1000).toFixed(0) + 'K₫';
                                        }
                                        return value + '₫';
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45,
                                    font: {
                                        size: 11,
                                        weight: '600'
                                    },
                                    color: '#495057'
                                }
                            }
                        }
                    }
                });
            } else {
                console.warn('Không có dữ liệu doanh thu phim trong tháng');
                revenueByMovieCtx.parentElement.innerHTML = '<p class="text-muted text-center p-4">Chưa có dữ liệu doanh thu các phim trong tháng này</p>';
            }
        }

        // Revenue by Theater Chart
        const revenueByTheaterCtx = document.getElementById('revenueByTheaterChart');
        if (revenueByTheaterCtx) {
            const revenueByTheaterData = <?php echo json_encode($revenueByTheater ?? []); ?>;
            
            console.log('Revenue by Theater Data:', revenueByTheaterData);
            
            if (revenueByTheaterData && revenueByTheaterData.length > 0) {
                new Chart(revenueByTheaterCtx, {
                    type: 'bar',
                    data: {
                        labels: revenueByTheaterData.map(item => item.name),
                            datasets: [{
                            label: 'Doanh thu (₫)',
                            data: revenueByTheaterData.map(item => parseFloat(item.revenue || 0)),
                            backgroundColor: 'rgba(253, 121, 168, 0.8)',
                            borderColor: '#fd79a8',
                            borderWidth: 2,
                            borderRadius: 8,
                            borderSkipped: false
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
                                callbacks: {
                                    label: function(context) {
                                        const value = context.parsed.y;
                                        const index = context.dataIndex;
                                        const ticketCount = revenueByTheaterData[index].ticket_count || 0;
                                        return [
                                            'Doanh thu: ' + new Intl.NumberFormat('vi-VN', {
                                                style: 'currency',
                                                currency: 'VND'
                                            }).format(value),
                                            'Số vé: ' + ticketCount.toLocaleString('vi-VN')
                                        ];
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.02)',
                                    drawBorder: false,
                                    lineWidth: 1
                                },
                                ticks: {
                                    font: {
                                        size: 12,
                                        weight: '600'
                                    },
                                    callback: function(value) {
                                        if (value >= 1000000) {
                                            return (value / 1000000).toFixed(1) + 'M₫';
                                        } else if (value >= 1000) {
                                            return (value / 1000).toFixed(0) + 'K₫';
                                        }
                                        return value + '₫';
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45,
                                    font: {
                                        size: 11,
                                        weight: '600'
                                    },
                                    color: '#495057'
                                }
                            }
                        }
                    }
                });
            } else {
                console.warn('Không có dữ liệu doanh thu các rạp');
                revenueByTheaterCtx.parentElement.innerHTML = '<p class="text-muted text-center p-4">Chưa có dữ liệu doanh thu các rạp</p>';
            }
        }
        
        // So sánh tỷ lệ lấp đầy giữa các phim
        const fillRateByMovieCtx = document.getElementById('fillRateByMovieChart');
        if (fillRateByMovieCtx) {
            const fillRateData = <?php echo json_encode($fillRateByMovie ?? []); ?>;
            
            console.log('Fill Rate Data:', fillRateData);
            
            if (fillRateData && fillRateData.length > 0) {
                // Sử dụng cùng màu với biểu đồ doanh thu
                const colors = [
                    { border: 'rgba(75, 192, 192, 1)', bg: 'rgba(75, 192, 192, 0.6)' },
                    { border: 'rgba(255, 99, 132, 1)', bg: 'rgba(255, 99, 132, 0.6)' },
                    { border: 'rgba(54, 162, 235, 1)', bg: 'rgba(54, 162, 235, 0.6)' },
                    { border: 'rgba(255, 206, 86, 1)', bg: 'rgba(255, 206, 86, 0.6)' },
                    { border: 'rgba(153, 102, 255, 1)', bg: 'rgba(153, 102, 255, 0.6)' },
                    { border: 'rgba(255, 159, 64, 1)', bg: 'rgba(255, 159, 64, 0.6)' },
                    { border: 'rgba(199, 199, 199, 1)', bg: 'rgba(199, 199, 199, 0.6)' },
                    { border: 'rgba(83, 102, 255, 1)', bg: 'rgba(83, 102, 255, 0.6)' },
                    { border: 'rgba(255, 99, 255, 1)', bg: 'rgba(255, 99, 255, 0.6)' },
                    { border: 'rgba(99, 255, 132, 1)', bg: 'rgba(99, 255, 132, 0.6)' }
                ];
                
                const backgroundColors = fillRateData.map((item, index) => colors[index % colors.length].bg);
                const borderColors = fillRateData.map((item, index) => colors[index % colors.length].border);
                
                new Chart(fillRateByMovieCtx, {
                    type: 'bar',
                    data: {
                        labels: fillRateData.map(item => item.movie_title),
                        datasets: [{
                            label: 'Tỷ lệ lấp đầy (%)',
                            data: fillRateData.map(item => parseFloat(item.avg_fill_rate || 0)),
                            backgroundColor: backgroundColors,
                            borderColor: borderColors,
                            borderWidth: 1
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: true,
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.02)',
                                    drawBorder: false,
                                    lineWidth: 1
                                },
                                ticks: {
                                    font: {
                                        size: 12,
                                        weight: '600'
                                    },
                                    callback: function(value) {
                                        return value + '%';
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    maxRotation: 45,
                                    minRotation: 45,
                                    font: {
                                        size: 11,
                                        weight: '600'
                                    },
                                    color: '#495057'
                                }
                            }
                        },
                        plugins: {
                            tooltip: {
                                callbacks: {
                                    label: function(context) {
                                        const value = context.parsed.y;
                                        const index = context.dataIndex;
                                        const item = fillRateData[index];
                                        return [
                                            'Tỷ lệ lấp đầy: ' + value.toFixed(2) + '%',
                                            'Vé đã đặt: ' + item.total_booked_tickets.toLocaleString('vi-VN'),
                                            'Vé đã lấy: ' + item.total_picked_up_tickets.toLocaleString('vi-VN')
                                        ];
                                    }
                                }
                            }
                        }
                    }
                });
            } else {
                console.warn('Không có dữ liệu tỷ lệ lấp đầy');
                fillRateByMovieCtx.parentElement.innerHTML = '<p class="text-muted text-center p-4">Chưa có dữ liệu tỷ lệ lấp đầy</p>';
            }
        }
        
        // Debug: Log tất cả dữ liệu
        console.log('=== DEBUG ANALYTICS ===');
        console.log('Revenue Data:', <?php echo json_encode($revenueData ?? []); ?>);
        console.log('Revenue by Method:', <?php echo json_encode($revenueByMethod ?? []); ?>);
        console.log('Revenue by Movie This Month:', <?php echo json_encode($revenueByMovieThisMonth ?? []); ?>);
        console.log('Revenue by Theater:', <?php echo json_encode($revenueByTheater ?? []); ?>);
        console.log('Fill Rate by Movie:', <?php echo json_encode($fillRateByMovie ?? []); ?>);
        console.log('Summary Stats:', <?php echo json_encode($summaryStats ?? []); ?>);
    }

    // Khởi tạo khi DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCharts);
    } else {
        // DOM đã ready, nhưng đợi Chart.js load
        initCharts();
    }
    </script>

