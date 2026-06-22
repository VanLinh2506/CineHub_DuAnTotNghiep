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

.rank-badge {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 14px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.rank-badge.gold {
    background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
    color: #8b4513;
}

.rank-badge.silver {
    background: linear-gradient(135deg, #c0c0c0 0%, #a8a8a8 100%);
    color: #4a4a4a;
}

.rank-badge.bronze {
    background: linear-gradient(135deg, #cd7f32 0%, #b87333 100%);
    color: #fff;
}

.rank-badge.other {
    background: linear-gradient(135deg, #e9ecef 0%, #dee2e6 100%);
    color: #495057;
}

.revenue-amount {
    font-size: 16px;
    font-weight: 700;
    color: #28a745;
    text-shadow: 0 1px 2px rgba(40, 167, 69, 0.2);
}

/* Progress Bar Styling */
.progress-modern {
    height: 32px;
    border-radius: 16px;
    background: #e9ecef;
    overflow: hidden;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
    position: relative;
}

.progress-bar-modern {
    height: 100%;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 13px;
    color: white;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    transition: width 0.6s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    background: linear-gradient(90deg, var(--progress-start) 0%, var(--progress-end) 100%);
}

.progress-bar-modern.excellent {
    --progress-start: #28a745;
    --progress-end: #20c997;
}

.progress-bar-modern.good {
    --progress-start: #ffc107;
    --progress-end: #ffb300;
}

.progress-bar-modern.poor {
    --progress-start: #dc3545;
    --progress-end: #c82333;
}

/* Filter Styling */
.filter-group {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
    align-items: center;
    margin-bottom: 20px;
    padding: 16px;
    background: linear-gradient(135deg, #f8f9ff 0%, #ffffff 100%);
    border-radius: 12px;
    border: 1px solid #e9ecef;
}

.filter-group label {
    font-weight: 600;
    color: #495057;
    font-size: 13px;
    margin: 0;
    white-space: nowrap;
}

.filter-group select {
    border: 2px solid #e9ecef;
    border-radius: 8px;
    padding: 8px 12px;
    font-size: 13px;
    transition: all 0.2s ease;
    background: white;
}

.filter-group select:focus {
    border-color: #6c5ce7;
    box-shadow: 0 0 0 3px rgba(108, 92, 231, 0.1);
    outline: none;
}

/* Section Headers */
.section-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 24px;
    padding-bottom: 16px;
    border-bottom: 2px solid #e9ecef;
}

.section-header h6 {
    margin: 0;
    font-size: 20px;
    font-weight: 700;
    color: #2d1b3d;
    display: flex;
    align-items: center;
    gap: 10px;
}

.section-header h6 i {
    color: #6c5ce7;
}
</style>

<div class="stat-card">
    <div class="section-header">
        <h6><i class="fas fa-chart-line"></i> Thống kê rạp</h6>
    </div>
    
    <!-- Doanh thu theo phim -->
    <div class="chart-container">
        <div class="chart-title">
            <i class="fas fa-film"></i>
            <span>Doanh thu theo phim</span>
        </div>
        <div class="chart-wrapper">
            <canvas id="revenueByMovieChart"></canvas>
        </div>
    </div>
    
    <!-- Doanh thu theo ngày -->
    <div class="chart-container">
        <div class="chart-title">
            <i class="fas fa-calendar-alt"></i>
            <span>Doanh thu theo ngày (30 ngày gần nhất)</span>
        </div>
        <div class="chart-wrapper">
            <canvas id="revenueByDateChart"></canvas>
        </div>
    </div>
    
    <!-- Bảng xếp hạng phim -->
    <div class="stats-table-container">
        <div class="chart-title">
            <i class="fas fa-trophy"></i>
            <span>Bảng xếp hạng phim theo doanh thu</span>
        </div>
        <div class="table-responsive">
            <table class="stats-table">
                <thead>
                    <tr>
                        <th style="width: 80px;">Hạng</th>
                        <th>Phim</th>
                        <th class="text-end">Doanh thu</th>
                        <th class="text-end">Số vé bán</th>
                        <th class="text-end">Giá vé TB</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($revenueByMovie)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                Chưa có dữ liệu
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($revenueByMovie as $index => $movie): ?>
                            <tr>
                                <td>
                                    <span class="rank-badge <?php 
                                        echo $index === 0 ? 'gold' : 
                                            ($index === 1 ? 'silver' : 
                                            ($index === 2 ? 'bronze' : 'other')); 
                                    ?>">
                                        #<?php echo $index + 1; ?>
                                    </span>
                                </td>
                                <td>
                                    <strong style="color: #2d1b3d; font-size: 15px;">
                                        <?php echo htmlspecialchars($movie['title']); ?>
                                    </strong>
                                </td>
                                <td class="text-end">
                                    <span class="revenue-amount">
                                        <?php echo number_format($movie['revenue']); ?>₫
                                    </span>
                                </td>
                                <td class="text-end" style="color: #6c757d; font-weight: 600;">
                                    <?php echo number_format($movie['ticket_count']); ?>
                                </td>
                                <td class="text-end" style="color: #6c757d;">
                                    <?php echo $movie['ticket_count'] > 0 ? number_format($movie['revenue'] / $movie['ticket_count']) : 0; ?>₫
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- So sánh tỷ lệ lấp đầy giữa các phim -->
    <div class="chart-container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="chart-title">
                <i class="fas fa-chart-bar"></i>
                <span>So sánh tỷ lệ lấp đầy giữa các phim</span>
            </div>
            <div class="d-flex align-items-center gap-2">
                <label class="form-label mb-0 small" style="font-weight: 600;">Lọc theo ngày:</label>
                <select id="fillRateDateFilter" class="form-select form-select-sm" style="width: 200px; border-radius: 8px;">
                    <option value="all" <?php echo (!isset($_GET['fill_rate_date']) || $_GET['fill_rate_date'] === 'all') ? 'selected' : ''; ?>>Tất cả từ trước đến nay</option>
                    <?php foreach ($availableDates as $dateRow): ?>
                        <option value="<?php echo $dateRow['show_date']; ?>" 
                                <?php echo (isset($_GET['fill_rate_date']) && $_GET['fill_rate_date'] === $dateRow['show_date']) ? 'selected' : ''; ?>>
                            <?php echo date('d/m/Y', strtotime($dateRow['show_date'])); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="chart-wrapper">
            <canvas id="fillRateComparisonChart"></canvas>
        </div>
    </div>
    
    <!-- Tỷ lệ lấp đầy ghế -->
    <div class="stats-table-container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="chart-title">
                <i class="fas fa-chair"></i>
                <span>Tỷ lệ lấp đầy ghế (Vé đã lấy / Vé đã đặt)</span>
            </div>
        </div>
        <div class="filter-group">
            <div>
                <label for="fillRateMovieFilter">Phim:</label>
                <select class="form-select form-select-sm d-inline-block" id="fillRateMovieFilter" style="width: 180px;" onchange="applyFillRateFilters()">
                    <option value="all" <?php echo ($fillRateMovieFilter ?? 'all') === 'all' ? 'selected' : ''; ?>>Tất cả phim</option>
                    <?php foreach ($availableMovies ?? [] as $movie): ?>
                        <option value="<?php echo $movie['id']; ?>" <?php echo ($fillRateMovieFilter ?? '') == $movie['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($movie['title']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="fillRateDateFilterTable">Ngày:</label>
                <select class="form-select form-select-sm d-inline-block" id="fillRateDateFilterTable" style="width: 150px;" onchange="applyFillRateFilters()">
                    <option value="all" <?php echo ($fillRateDateFilter ?? 'all') === 'all' ? 'selected' : ''; ?>>Tất cả ngày</option>
                    <?php foreach ($availableDates as $dateItem): ?>
                        <option value="<?php echo $dateItem['show_date']; ?>" <?php echo ($fillRateDateFilter ?? '') == $dateItem['show_date'] ? 'selected' : ''; ?>>
                            <?php echo date('d/m/Y', strtotime($dateItem['show_date'])); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="fillRateScreenFilter">Phòng:</label>
                <select class="form-select form-select-sm d-inline-block" id="fillRateScreenFilter" style="width: 120px;" onchange="applyFillRateFilters()">
                    <option value="all" <?php echo ($fillRateScreenFilter ?? 'all') === 'all' ? 'selected' : ''; ?>>Tất cả phòng</option>
                    <?php foreach ($availableScreens ?? [] as $screen): ?>
                        <option value="<?php echo $screen['id']; ?>" <?php echo ($fillRateScreenFilter ?? '') == $screen['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($screen['screen_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label for="fillRateTimeFilter">Khung giờ:</label>
                <select class="form-select form-select-sm d-inline-block" id="fillRateTimeFilter" style="width: 120px;" onchange="applyFillRateFilters()">
                    <option value="all" <?php echo ($fillRateTimeFilter ?? 'all') === 'all' ? 'selected' : ''; ?>>Tất cả giờ</option>
                    <?php foreach ($availableTimes ?? [] as $time): ?>
                        <option value="<?php echo $time['show_time']; ?>" <?php echo ($fillRateTimeFilter ?? '') == $time['show_time'] ? 'selected' : ''; ?>>
                            <?php echo date('H:i', strtotime($time['show_time'])); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="table-responsive">
            <table class="stats-table" id="fillRateTable">
                <thead>
                    <tr>
                        <th>Ngày chiếu</th>
                        <th>Khung giờ</th>
                        <th>Phim</th>
                        <th>Phòng</th>
                        <th>Vé đã đặt</th>
                        <th>Vé đã lấy</th>
                        <th>Tỷ lệ lấp đầy</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    if (empty($fillRateByDate)):
                    ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="fas fa-info-circle fa-2x mb-3 d-block"></i>
                                Chưa có dữ liệu tỷ lệ lấp đầy.
                                <br><small class="text-muted mt-2 d-block">Vui lòng import file <code>test_data_lotte_cinema.sql</code> để có dữ liệu test.</small>
                            </td>
                        </tr>
                    <?php 
                    else:
                        $rowCount = 0;
                        foreach ($fillRateByDate as $item): 
                            foreach ($item['screens'] as $screen): 
                                $rowCount++;
                    ?>
                            <tr class="fill-rate-row" 
                                data-date="<?php echo $item['show_date']; ?>"
                                data-movie-id="<?php echo $screen['movie_id'] ?? ''; ?>"
                                data-movie="<?php echo htmlspecialchars($screen['movie_title'] ?? ''); ?>"
                                data-screen-id="<?php echo $screen['screen_id'] ?? ''; ?>"
                                data-screen="<?php echo htmlspecialchars($screen['screen_name']); ?>"
                                data-time="<?php echo isset($screen['show_time']) ? date('H:i:s', strtotime($screen['show_time'])) : ''; ?>">
                                <td style="font-weight: 600; color: #495057;">
                                    <?php echo date('d/m/Y', strtotime($item['show_date'])); ?>
                                </td>
                                <td style="color: #6c757d;">
                                    <?php echo isset($screen['show_time']) ? date('H:i', strtotime($screen['show_time'])) : '-'; ?>
                                </td>
                                <td style="font-weight: 600; color: #2d1b3d;">
                                    <?php echo htmlspecialchars($screen['movie_title'] ?? '-'); ?>
                                </td>
                                <td style="color: #6c757d;">
                                    <?php echo htmlspecialchars($screen['screen_name']); ?>
                                </td>
                                <td class="text-center" style="font-weight: 600; color: #495057;">
                                    <?php echo $screen['booked_tickets']; ?>
                                </td>
                                <td class="text-center" style="font-weight: 600; color: #28a745;">
                                    <?php echo $screen['picked_up_tickets']; ?>
                                </td>
                                <td>
                                    <div class="progress-modern">
                                        <div class="progress-bar-modern <?php 
                                            echo $screen['fill_rate'] >= 80 ? 'excellent' : 
                                                ($screen['fill_rate'] >= 50 ? 'good' : 'poor'); 
                                        ?>" 
                                        style="width: <?php echo min($screen['fill_rate'], 100); ?>%">
                                            <?php echo number_format($screen['fill_rate'], 1); ?>%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                    <?php 
                        endforeach;
                    endforeach; 
                    endif;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// Modern color palette with gradients
const modernColors = [
    { border: '#6c5ce7', bg: 'rgba(108, 92, 231, 0.8)', gradient: 'linear-gradient(135deg, #6c5ce7 0%, #5a4fcf 100%)' },
    { border: '#00b894', bg: 'rgba(0, 184, 148, 0.8)', gradient: 'linear-gradient(135deg, #00b894 0%, #00a085 100%)' },
    { border: '#fd79a8', bg: 'rgba(253, 121, 168, 0.8)', gradient: 'linear-gradient(135deg, #fd79a8 0%, #e84393 100%)' },
    { border: '#fdcb6e', bg: 'rgba(253, 203, 110, 0.8)', gradient: 'linear-gradient(135deg, #fdcb6e 0%, #e17055 100%)' },
    { border: '#74b9ff', bg: 'rgba(116, 185, 255, 0.8)', gradient: 'linear-gradient(135deg, #74b9ff 0%, #0984e3 100%)' },
    { border: '#a29bfe', bg: 'rgba(162, 155, 254, 0.8)', gradient: 'linear-gradient(135deg, #a29bfe 0%, #6c5ce7 100%)' },
    { border: '#55efc4', bg: 'rgba(85, 239, 196, 0.8)', gradient: 'linear-gradient(135deg, #55efc4 0%, #00b894 100%)' },
    { border: '#ffeaa7', bg: 'rgba(255, 234, 167, 0.8)', gradient: 'linear-gradient(135deg, #ffeaa7 0%, #fdcb6e 100%)' },
    { border: '#fab1a0', bg: 'rgba(250, 177, 160, 0.8)', gradient: 'linear-gradient(135deg, #fab1a0 0%, #e17055 100%)' },
    { border: '#81ecec', bg: 'rgba(129, 236, 236, 0.8)', gradient: 'linear-gradient(135deg, #81ecec 0%, #00b894 100%)' }
];

// Function để init charts với retry mechanism
function initStatisticsCharts() {
    console.log('initStatisticsCharts called, Chart type:', typeof Chart);
    
    if (typeof Chart === 'undefined') {
        console.warn('Chart.js chưa được load, thử lại sau 200ms...');
        setTimeout(initStatisticsCharts, 200);
        return;
    }
    
    console.log('Chart.js đã được load, bắt đầu khởi tạo charts...');
    
    // Chart.js default configuration - kiểm tra xem có tồn tại không
    if (Chart.defaults && Chart.defaults.font) {
        Chart.defaults.font.family = "'Segoe UI', 'Roboto', 'Helvetica Neue', Arial, sans-serif";
        Chart.defaults.font.size = 12;
        Chart.defaults.font.weight = '500';
    }
    
    if (Chart.defaults && Chart.defaults.plugins && Chart.defaults.plugins.legend) {
        Chart.defaults.plugins.legend.display = true;
        Chart.defaults.plugins.legend.position = 'top';
        if (Chart.defaults.plugins.legend.labels) {
            Chart.defaults.plugins.legend.labels.usePointStyle = true;
            Chart.defaults.plugins.legend.labels.padding = 15;
            if (Chart.defaults.plugins.legend.labels.font) {
                Chart.defaults.plugins.legend.labels.font.size = 13;
                Chart.defaults.plugins.legend.labels.font.weight = '600';
            }
        }
    }
    
    // Doanh thu theo phim
    const revenueByMovieCtx = document.getElementById('revenueByMovieChart');
    if (revenueByMovieCtx) {
        const revenueByMovieData = <?php echo json_encode($revenueByMovie ?? []); ?>;
        
        console.log('Revenue by movie data:', revenueByMovieData);
        
        if (revenueByMovieData && revenueByMovieData.length > 0) {
            const movieColorMap = {};
            <?php 
            $colorIndex = 0;
            if (isset($revenueByDate) && is_array($revenueByDate)):
                foreach ($revenueByDate as $movieId => $movieData): 
            ?>
            movieColorMap['<?php echo addslashes($movieData['title']); ?>'] = <?php echo $colorIndex; ?>;
            <?php 
                    $colorIndex++;
                endforeach;
            endif;
            ?>
            
            const backgroundColors = revenueByMovieData.map((item, index) => {
                const colorIndex = movieColorMap[item.title] !== undefined ? movieColorMap[item.title] : index;
                return modernColors[colorIndex % modernColors.length].bg;
            });
            
            const borderColors = revenueByMovieData.map((item, index) => {
                const colorIndex = movieColorMap[item.title] !== undefined ? movieColorMap[item.title] : index;
                return modernColors[colorIndex % modernColors.length].border;
            });
            
            console.log('Creating revenue by movie chart...');
            try {
                new Chart(revenueByMovieCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: revenueByMovieData.map(item => item.title),
                    datasets: [{
                        label: 'Doanh thu (VNĐ)',
                        data: revenueByMovieData.map(item => parseFloat(item.revenue || 0)),
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1500,
                        easing: 'easeInOutQuart'
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
                                },
                                font: {
                                    size: 11,
                                    weight: '600'
                                },
                                color: '#6c757d'
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
                                color: '#495057',
                                maxRotation: 45,
                                minRotation: 0
                            }
                        }
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
                            displayColors: true,
                            callbacks: {
                                label: function(context) {
                                    return 'Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' đ';
                                }
                            }
                        }
                    }
                }
            });
            console.log('Revenue by movie chart created successfully');
            } catch (error) {
                console.error('Error creating revenue by movie chart:', error);
            }
        } else {
            console.warn('No revenue by movie data');
            revenueByMovieCtx.parentElement.innerHTML = '<p class="text-muted text-center p-4">Chưa có dữ liệu doanh thu theo phim</p>';
        }
    } else {
        console.error('Canvas element revenueByMovieChart not found');
    }

    // Doanh thu theo ngày
    const revenueByDateCtx = document.getElementById('revenueByDateChart');
    if (revenueByDateCtx) {
        const revenueByDateData = <?php echo json_encode($revenueByDate ?? []); ?>;
        const datesData = <?php echo json_encode($dates ?? []); ?>;
        
        if (revenueByDateData && Object.keys(revenueByDateData).length > 0 && datesData && datesData.length > 0) {
            const datasets = [];
            <?php 
            $colorIndex = 0;
            foreach ($revenueByDate as $movieId => $movieData): 
            ?>
            datasets.push({
                label: '<?php echo addslashes($movieData['title']); ?>',
                data: [<?php echo implode(',', array_column($movieData['data'], 'revenue')); ?>],
                borderColor: modernColors[<?php echo $colorIndex; ?> % modernColors.length].border,
                backgroundColor: modernColors[<?php echo $colorIndex; ?> % modernColors.length].bg.replace('0.8', '0.06').replace('0.6', '0.06'),
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointRadius: 4,
                pointHoverRadius: 6,
                pointBackgroundColor: modernColors[<?php echo $colorIndex; ?> % modernColors.length].border,
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointHoverBackgroundColor: modernColors[<?php echo $colorIndex; ?> % modernColors.length].border,
                pointHoverBorderColor: '#fff',
                pointHoverBorderWidth: 3
            });
            <?php 
            $colorIndex++;
            endforeach; 
            ?>

            console.log('Creating revenue by date chart...');
            try {
                new Chart(revenueByDateCtx.getContext('2d'), {
                type: 'line',
                data: {
                    labels: datesData.map(d => {
                        const date = new Date(d);
                        return date.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });
                    }),
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 2000,
                        easing: 'easeInOutQuart'
                    },
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                callback: function(value) {
                                    return new Intl.NumberFormat('vi-VN').format(value) + ' đ';
                                },
                                font: {
                                    size: 11,
                                    weight: '600'
                                },
                                color: '#6c757d'
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                font: {
                                    size: 11,
                                    weight: '600'
                                },
                                color: '#495057'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                padding: 15,
                                usePointStyle: true,
                                pointStyle: 'circle',
                                font: {
                                    size: 12,
                                    weight: '600'
                                },
                                color: '#495057'
                            }
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
                                    return context.dataset.label + ': ' + new Intl.NumberFormat('vi-VN').format(context.parsed.y) + ' đ';
                                }
                            }
                        }
                    }
                }
            });
            console.log('Revenue by date chart created successfully');
            } catch (error) {
                console.error('Error creating revenue by date chart:', error);
            }
        } else {
            console.warn('No revenue by date data');
            revenueByDateCtx.parentElement.innerHTML = '<p class="text-muted text-center p-4">Chưa có dữ liệu doanh thu theo ngày</p>';
        }
    } else {
        console.error('Canvas element revenueByDateChart not found');
    }
    
    // Event listener cho filter ngày tỷ lệ lấp đầy
    const fillRateDateFilter = document.getElementById('fillRateDateFilter');
    if (fillRateDateFilter) {
        fillRateDateFilter.addEventListener('change', function() {
            const selectedDate = this.value;
            const url = new URL(window.location.href);
            if (selectedDate === 'all') {
                url.searchParams.delete('fill_rate_date');
            } else {
                url.searchParams.set('fill_rate_date', selectedDate);
            }
            window.location.href = url.toString();
        });
    }
    
    // So sánh tỷ lệ lấp đầy giữa các phim
    let fillRateChart = null;
    const fillRateComparisonCtx = document.getElementById('fillRateComparisonChart');
    if (fillRateComparisonCtx) {
        const fillRateData = <?php echo json_encode($fillRateByMovieAvg ?? []); ?>;
        
        if (fillRateData && fillRateData.length > 0) {
            const backgroundColors = fillRateData.map((item, index) => modernColors[index % modernColors.length].bg);
            const borderColors = fillRateData.map((item, index) => modernColors[index % modernColors.length].border);
            
            if (fillRateChart) {
                fillRateChart.destroy();
            }
            
            console.log('Creating fill rate comparison chart...');
            try {
                fillRateChart = new Chart(fillRateComparisonCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: fillRateData.map(item => item.movie_title),
                    datasets: [{
                        label: 'Tỷ lệ lấp đầy (%)',
                        data: fillRateData.map(item => parseFloat(item.avg_fill_rate || 0)),
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    animation: {
                        duration: 1500,
                        easing: 'easeInOutQuart'
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)',
                                drawBorder: false
                            },
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                },
                                font: {
                                    size: 11,
                                    weight: '600'
                                },
                                color: '#6c757d'
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
            console.log('Fill rate comparison chart created successfully');
            } catch (error) {
                console.error('Error creating fill rate comparison chart:', error);
            }
        } else {
            console.warn('No fill rate data');
            fillRateComparisonCtx.parentElement.innerHTML = '<p class="text-muted text-center p-4">Chưa có dữ liệu tỷ lệ lấp đầy</p>';
        }
    } else {
        console.error('Canvas element fillRateComparisonChart not found');
    }
    
    console.log('Tất cả charts đã được khởi tạo!');
}

// Đợi cả DOM và Chart.js load xong
(function() {
    function waitForChartJS(callback, maxAttempts = 100) {
        let attempts = 0;
        const checkChart = setInterval(function() {
            attempts++;
            if (typeof Chart !== 'undefined' && typeof Chart.register !== 'undefined') {
                clearInterval(checkChart);
                console.log('Chart.js đã sẵn sàng sau ' + attempts + ' lần thử');
                // Đợi thêm một chút để đảm bảo Chart.js hoàn toàn sẵn sàng
                setTimeout(function() {
                    callback();
                }, 300);
            } else if (attempts >= maxAttempts) {
                clearInterval(checkChart);
                console.error('Chart.js không thể load sau ' + maxAttempts + ' lần thử');
                console.error('Chart object:', typeof Chart);
                console.error('Window.Chart:', window.Chart);
            }
        }, 50);
    }
    
    // Đợi window load xong trước
    if (window.addEventListener) {
        window.addEventListener('load', function() {
            console.log('Window loaded, waiting for Chart.js...');
            waitForChartJS(initStatisticsCharts);
        });
    } else {
        // Fallback cho IE
        window.attachEvent('onload', function() {
            console.log('Window loaded (IE), waiting for Chart.js...');
            waitForChartJS(initStatisticsCharts);
        });
    }
})();

// Hàm apply tất cả filters cho tỷ lệ lấp đầy (sử dụng AJAX)
function applyFillRateFilters() {
    const movieFilter = document.getElementById('fillRateMovieFilter')?.value || 'all';
    const dateFilter = document.getElementById('fillRateDateFilterTable')?.value || 'all';
    const screenFilter = document.getElementById('fillRateScreenFilter')?.value || 'all';
    const timeFilter = document.getElementById('fillRateTimeFilter')?.value || 'all';
    
    // Cập nhật URL mà không reload trang
    const url = new URL(window.location.href);
    url.searchParams.set('fill_rate_movie', movieFilter);
    url.searchParams.set('fill_rate_date', dateFilter);
    url.searchParams.set('fill_rate_screen', screenFilter);
    url.searchParams.set('fill_rate_time', timeFilter);
    window.history.pushState({}, '', url.toString());
    
    // Hiển thị loading
    const tableBody = document.querySelector('#fillRateTable tbody');
    if (tableBody) {
        tableBody.innerHTML = '<tr><td colspan="7" class="text-center p-4"><i class="fas fa-spinner fa-spin"></i> Đang tải...</td></tr>';
    }
    
    // Gọi API để lấy dữ liệu mới
    const apiUrl = new URL('?route=moderator/getFillRateData', window.location.origin);
    apiUrl.searchParams.set('fill_rate_movie', movieFilter);
    apiUrl.searchParams.set('fill_rate_date', dateFilter);
    apiUrl.searchParams.set('fill_rate_screen', screenFilter);
    apiUrl.searchParams.set('fill_rate_time', timeFilter);
    
    fetch(apiUrl.toString())
        .then(response => response.json())
        .then(data => {
            if (data.success && data.data) {
                updateFillRateTable(data.data);
            } else {
                if (tableBody) {
                    tableBody.innerHTML = '<tr><td colspan="7" class="text-center p-4 text-muted">Không có dữ liệu</td></tr>';
                }
            }
        })
        .catch(error => {
            console.error('Error loading fill rate data:', error);
            if (tableBody) {
                tableBody.innerHTML = '<tr><td colspan="7" class="text-center p-4 text-danger">Lỗi khi tải dữ liệu</td></tr>';
            }
        });
}

// Hàm cập nhật bảng tỷ lệ lấp đầy
function updateFillRateTable(data) {
    const tableBody = document.querySelector('#fillRateTable tbody');
    if (!tableBody) return;
    
    if (!data || data.length === 0) {
        tableBody.innerHTML = '<tr><td colspan="7" class="text-center p-4 text-muted">Chưa có dữ liệu tỷ lệ lấp đầy</td></tr>';
        return;
    }
    
    let html = '';
    let rowCount = 0;
    
    data.forEach(item => {
        item.screens.forEach(screen => {
            rowCount++;
            const fillRate = parseFloat(screen.fill_rate || 0);
            const fillRateColor = fillRate >= 80 ? '#00b894' : fillRate >= 50 ? '#fdcb6e' : '#e17055';
            const pickedUpColor = fillRate >= 80 ? '#28a745' : fillRate >= 50 ? '#ffc107' : '#dc3545';
            
            const showDate = formatDate(item.show_date);
            const showTime = screen.show_time ? formatTime(screen.show_time) : '-';
            const movieTitle = escapeHtml(screen.movie_title || '-');
            const screenName = escapeHtml(screen.screen_name);
            const bookedTickets = screen.booked_tickets || 0;
            const pickedUpTickets = screen.picked_up_tickets || 0;
            
            html += `
                <tr class="fill-rate-row" 
                    data-date="${item.show_date}"
                    data-movie-id="${screen.movie_id || ''}"
                    data-movie="${movieTitle}"
                    data-screen-id="${screen.screen_id || ''}"
                    data-screen="${screenName}"
                    data-time="${screen.show_time || ''}">
                    <td style="font-weight: 600; color: #495057;">${showDate}</td>
                    <td style="color: #6c757d;">${showTime}</td>
                    <td style="font-weight: 600; color: #2d1b3d;">${movieTitle}</td>
                    <td style="color: #6c757d;">${screenName}</td>
                    <td class="text-center" style="font-weight: 600; color: #495057;">${bookedTickets}</td>
                    <td class="text-center" style="font-weight: 600; color: ${pickedUpColor};">${pickedUpTickets}</td>
                    <td>
                        <div class="fill-rate-progress">
                            <div class="progress" style="height: 25px; border-radius: 12px; background: #e9ecef; overflow: hidden;">
                                <div class="progress-bar" role="progressbar" 
                                     style="width: ${fillRate}%; background: linear-gradient(135deg, ${fillRateColor} 0%, ${fillRateColor}dd 100%); 
                                            box-shadow: 0 2px 8px rgba(0,0,0,0.15); 
                                            display: flex; align-items: center; justify-content: center; 
                                            font-weight: 700; font-size: 12px; color: #fff; text-shadow: 0 1px 2px rgba(0,0,0,0.2);"
                                     aria-valuenow="${fillRate}" aria-valuemin="0" aria-valuemax="100">
                                    ${fillRate.toFixed(1)}%
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        });
    });
    
    tableBody.innerHTML = html;
}

// Helper functions
function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}

function formatTime(timeString) {
    if (!timeString) return '-';
    const time = timeString.split(':');
    return `${time[0]}:${time[1]}`;
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function filterByDate() {
    const selectedDate = document.getElementById('dateFilter')?.value;
    const rows = document.querySelectorAll('.fill-rate-row');
    
    rows.forEach(row => {
        if (!selectedDate || row.getAttribute('data-date') === selectedDate) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
}
</script>
