<?php $__env->startSection('content'); ?>
<?php $__env->startPush('styles'); ?>
<style>
.chart-container { background:linear-gradient(135deg,#fff 0%,#f8f9fa 100%); border-radius:16px; padding:24px; margin-bottom:32px; box-shadow:0 4px 20px rgba(0,0,0,0.08); border:1px solid rgba(0,0,0,0.05); transition:transform 0.3s ease,box-shadow 0.3s ease; }
.chart-container:hover { transform:translateY(-2px); box-shadow:0 8px 30px rgba(0,0,0,0.12); }
.chart-title { font-size:18px; font-weight:600; color:#2d1b3d; margin-bottom:20px; display:flex; align-items:center; gap:10px; }
.chart-title i { color:#6c5ce7; font-size:20px; }
.chart-wrapper { position:relative; height:400px; margin-top:20px; }
.stats-table-container { background:linear-gradient(135deg,#fff 0%,#f8f9fa 100%); border-radius:16px; padding:24px; margin-bottom:32px; box-shadow:0 4px 20px rgba(0,0,0,0.08); border:1px solid rgba(0,0,0,0.05); }
.stats-table { width:100%; border-collapse:separate; border-spacing:0; }
.stats-table thead { background:linear-gradient(135deg,#6c5ce7 0%,#5a4fcf 100%); color:white; }
.stats-table thead th { padding:16px; font-weight:600; text-align:left; border:none; font-size:14px; }
.stats-table tbody tr { transition:all 0.2s ease; border-bottom:1px solid #e9ecef; }
.stats-table tbody tr:hover { background:linear-gradient(90deg,#f8f9ff 0%,#fff 100%); }
.stats-table tbody td { padding:16px; vertical-align:middle; }
.rank-badge { display:inline-flex; align-items:center; justify-content:center; width:40px; height:40px; border-radius:10px; font-weight:700; font-size:14px; }
.rank-badge.gold { background:linear-gradient(135deg,#f6d365 0%,#fda085 100%); color:#8b4513; }
.rank-badge.silver { background:linear-gradient(135deg,#c0c0c0 0%,#a8a8a8 100%); color:#4a4a4a; }
.rank-badge.bronze { background:linear-gradient(135deg,#cd7f32 0%,#b87333 100%); color:#fff; }
.rank-badge.other { background:linear-gradient(135deg,#e9ecef 0%,#dee2e6 100%); color:#495057; }
.revenue-amount { font-size:16px; font-weight:700; color:#28a745; }
.progress-modern { height:32px; border-radius:16px; background:#e9ecef; overflow:hidden; }
.progress-bar-modern { height:100%; border-radius:16px; display:flex; align-items:center; justify-content:center; font-weight:700; font-size:13px; color:white; }
.progress-bar-modern.excellent { background:linear-gradient(90deg,#28a745,#20c997); }
.progress-bar-modern.good { background:linear-gradient(90deg,#ffc107,#ffb300); }
.progress-bar-modern.poor { background:linear-gradient(90deg,#dc3545,#c82333); }
.filter-group { display:flex; gap:12px; flex-wrap:wrap; align-items:center; margin-bottom:20px; padding:16px; background:linear-gradient(135deg,#f8f9ff 0%,#fff 100%); border-radius:12px; border:1px solid #e9ecef; }
.filter-group > div { flex: 1 1 180px; min-width: 0; }
.filter-group .form-select { width: 100% !important; max-width: 100% !important; }
.section-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; padding-bottom:16px; border-bottom:2px solid #e9ecef; }
.section-header h6 { margin:0; font-size:20px; font-weight:700; color:#2d1b3d; display:flex; align-items:center; gap:10px; }
.section-header h6 i { color:#6c5ce7; }
@media (max-width: 768px) {
    .chart-container,
    .stats-table-container {
        padding: 16px;
    }

    .section-header,
    .d-flex.justify-content-between.align-items-center.mb-3,
    .d-flex.justify-content-between.align-items-center.mb-4 {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 12px;
    }

    .filter-group {
        flex-direction: column;
        align-items: stretch;
    }

    .filter-group > div {
        width: 100%;
    }

    #fillRateDateFilter,
    #fillRateMovieFilter,
    #fillRateDateFilterTable,
    #fillRateScreenFilter,
    #fillRateTimeFilter {
        width: 100% !important;
    }

    .chart-wrapper {
        height: 300px;
    }
}
</style>
<?php $__env->stopPush(); ?>

<div class="stat-card">
    <div class="section-header">
        <h6><i class="fas fa-chart-line"></i> Thống kê rạp</h6>
    </div>

    <!-- Doanh thu theo phim -->
    <div class="chart-container">
        <div class="chart-title"><i class="fas fa-film"></i><span>Doanh thu theo phim</span></div>
        <div class="chart-wrapper"><canvas id="revenueByMovieChart"></canvas></div>
    </div>

    <!-- Doanh thu theo ngày -->
    <div class="chart-container">
        <div class="chart-title"><i class="fas fa-calendar-alt"></i><span>Doanh thu theo ngày (30 ngày gần nhất)</span></div>
        <div class="chart-wrapper"><canvas id="revenueByDateChart"></canvas></div>
    </div>

    <!-- Bảng xếp hạng phim -->
    <div class="stats-table-container">
        <div class="chart-title"><i class="fas fa-trophy"></i><span>Bảng xếp hạng phim theo doanh thu</span></div>
        <div class="table-responsive">
            <table class="stats-table">
                <thead>
                    <tr><th style="width:80px;">Hạng</th><th>Phim</th><th class="text-end">Doanh thu</th><th class="text-end">Số vé bán</th><th class="text-end">Giá vé TB</th></tr>
                </thead>
                <tbody>
                    <?php if(empty($revenueByMovie)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4"><i class="fas fa-inbox fa-2x mb-2 d-block"></i>Chưa có dữ liệu</td></tr>
                    <?php else: ?>
                        <?php $__currentLoopData = $revenueByMovie; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $movie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <span class="rank-badge <?php echo e($index === 0 ? 'gold' : ($index === 1 ? 'silver' : ($index === 2 ? 'bronze' : 'other'))); ?>">#<?php echo e($index + 1); ?></span>
                            </td>
                            <td><strong style="color:#2d1b3d;font-size:15px;"><?php echo e($movie['title']); ?></strong></td>
                            <td class="text-end"><span class="revenue-amount"><?php echo e(number_format($movie['revenue'])); ?>₫</span></td>
                            <td class="text-end" style="color:#6c757d;font-weight:600;"><?php echo e(number_format($movie['ticket_count'])); ?></td>
                            <td class="text-end" style="color:#6c757d;"><?php echo e($movie['ticket_count'] > 0 ? number_format($movie['revenue'] / $movie['ticket_count']) : 0); ?>₫</td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- So sánh tỷ lệ lấp đầy -->
    <div class="chart-container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="chart-title"><i class="fas fa-chart-bar"></i><span>So sánh tỷ lệ lấp đầy giữa các phim</span></div>
            <div class="d-flex align-items-center gap-2">
                <label class="form-label mb-0 small" style="font-weight:600;">Lọc theo ngày:</label>
                <select id="fillRateDateFilter" class="form-select form-select-sm" style="width:200px;border-radius:8px;">
                    <option value="all" <?php echo e((!isset($_GET['fill_rate_date']) || $_GET['fill_rate_date'] === 'all') ? 'selected' : ''); ?>>Tất cả từ trước đến nay</option>
                    <?php $__currentLoopData = $availableDates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dateRow): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($dateRow['show_date']); ?>" <?php echo e((isset($_GET['fill_rate_date']) && $_GET['fill_rate_date'] === $dateRow['show_date']) ? 'selected' : ''); ?>>
                            <?php echo e(date('d/m/Y', strtotime($dateRow['show_date']))); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
        <div class="chart-wrapper"><canvas id="fillRateComparisonChart"></canvas></div>
    </div>

    <!-- Tỷ lệ lấp đầy ghế -->
    <div class="stats-table-container">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="chart-title"><i class="fas fa-chair"></i><span>Tỷ lệ lấp đầy ghế (Vé đã lấy / Vé đã đặt)</span></div>
        </div>
        <div class="filter-group">
            <div>
                <label>Phim:</label>
                <select class="form-select form-select-sm d-inline-block" id="fillRateMovieFilter" style="width:180px;" onchange="applyFillRateFilters()">
                    <option value="all" <?php echo e(($fillRateMovieFilter ?? 'all') === 'all' ? 'selected' : ''); ?>>Tất cả phim</option>
                    <?php $__currentLoopData = $availableMovies ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($movie['id']); ?>" <?php echo e(($fillRateMovieFilter ?? '') == $movie['id'] ? 'selected' : ''); ?>><?php echo e($movie['title']); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label>Ngày:</label>
                <select class="form-select form-select-sm d-inline-block" id="fillRateDateFilterTable" style="width:150px;" onchange="applyFillRateFilters()">
                    <option value="all" <?php echo e(($fillRateDateFilter ?? 'all') === 'all' ? 'selected' : ''); ?>>Tất cả ngày</option>
                    <?php $__currentLoopData = $availableDates; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dateItem): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($dateItem['show_date']); ?>" <?php echo e(($fillRateDateFilter ?? '') == $dateItem['show_date'] ? 'selected' : ''); ?>>
                            <?php echo e(date('d/m/Y', strtotime($dateItem['show_date']))); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label>Phòng:</label>
                <select class="form-select form-select-sm d-inline-block" id="fillRateScreenFilter" style="width:120px;" onchange="applyFillRateFilters()">
                    <option value="all" <?php echo e(($fillRateScreenFilter ?? 'all') === 'all' ? 'selected' : ''); ?>>Tất cả phòng</option>
                    <?php $__currentLoopData = $availableScreens ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $screen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($screen['id']); ?>" <?php echo e(($fillRateScreenFilter ?? '') == $screen['id'] ? 'selected' : ''); ?>><?php echo e($screen['screen_name']); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div>
                <label>Khung giờ:</label>
                <select class="form-select form-select-sm d-inline-block" id="fillRateTimeFilter" style="width:120px;" onchange="applyFillRateFilters()">
                    <option value="all" <?php echo e(($fillRateTimeFilter ?? 'all') === 'all' ? 'selected' : ''); ?>>Tất cả giờ</option>
                    <?php $__currentLoopData = $availableTimes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $time): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($time['show_time']); ?>" <?php echo e(($fillRateTimeFilter ?? '') == $time['show_time'] ? 'selected' : ''); ?>>
                            <?php echo e(date('H:i', strtotime($time['show_time']))); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
        </div>
        <div class="table-responsive">
            <table class="stats-table" id="fillRateTable">
                <thead>
                    <tr><th>Ngày chiếu</th><th>Khung giờ</th><th>Phim</th><th>Phòng</th><th>Vé đã đặt</th><th>Vé đã lấy</th><th>Tỷ lệ lấp đầy</th></tr>
                </thead>
                <tbody>
                    <?php if(empty($fillRateByDate)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-5"><i class="fas fa-info-circle fa-2x mb-3 d-block"></i>Chưa có dữ liệu tỷ lệ lấp đầy.</td></tr>
                    <?php else: ?>
                        <?php $__currentLoopData = $fillRateByDate; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php $__currentLoopData = $item['screens']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $screen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr class="fill-rate-row"
                                data-date="<?php echo e($item['show_date']); ?>"
                                data-movie-id="<?php echo e($screen['movie_id'] ?? ''); ?>"
                                data-screen-id="<?php echo e($screen['screen_id'] ?? ''); ?>"
                                data-time="<?php echo e(isset($screen['show_time']) ? date('H:i:s', strtotime($screen['show_time'])) : ''); ?>">
                                <td style="font-weight:600;color:#495057;"><?php echo e(date('d/m/Y', strtotime($item['show_date']))); ?></td>
                                <td style="color:#6c757d;"><?php echo e(isset($screen['show_time']) ? date('H:i', strtotime($screen['show_time'])) : '-'); ?></td>
                                <td style="font-weight:600;color:#2d1b3d;"><?php echo e($screen['movie_title'] ?? '-'); ?></td>
                                <td style="color:#6c757d;"><?php echo e($screen['screen_name']); ?></td>
                                <td class="text-center" style="font-weight:600;color:#495057;"><?php echo e($screen['booked_tickets']); ?></td>
                                <td class="text-center" style="font-weight:600;color:#28a745;"><?php echo e($screen['picked_up_tickets']); ?></td>
                                <td>
                                    <div class="progress-modern">
                                        <div class="progress-bar-modern <?php echo e($screen['fill_rate'] >= 80 ? 'excellent' : ($screen['fill_rate'] >= 50 ? 'good' : 'poor')); ?>"
                                             style="width: <?php echo e(min($screen['fill_rate'], 100)); ?>%">
                                            <?php echo e(number_format($screen['fill_rate'], 1)); ?>%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const modernColors = [
    {border:'#6c5ce7',bg:'rgba(108,92,231,0.8)'},{border:'#00b894',bg:'rgba(0,184,148,0.8)'},
    {border:'#fd79a8',bg:'rgba(253,121,168,0.8)'},{border:'#fdcb6e',bg:'rgba(253,203,110,0.8)'},
    {border:'#74b9ff',bg:'rgba(116,185,255,0.8)'},{border:'#a29bfe',bg:'rgba(162,155,254,0.8)'},
    {border:'#55efc4',bg:'rgba(85,239,196,0.8)'},{border:'#ffeaa7',bg:'rgba(255,234,167,0.8)'},
    {border:'#fab1a0',bg:'rgba(250,177,160,0.8)'},{border:'#81ecec',bg:'rgba(129,236,236,0.8)'}
];

function initStatisticsCharts() {
    if (typeof Chart === 'undefined') { setTimeout(initStatisticsCharts, 200); return; }

    // Revenue by movie
    const revenueByMovieCtx = document.getElementById('revenueByMovieChart');
    if (revenueByMovieCtx) {
        const data = <?php echo json_encode($revenueByMovie ?? [], 15, 512) ?>;
        if (data && data.length > 0) {
            new Chart(revenueByMovieCtx.getContext('2d'), {
                type: 'bar',
                data: { labels: data.map(i => i.title), datasets: [{ label: 'Doanh thu (VNĐ)', data: data.map(i => parseFloat(i.revenue || 0)),
                    backgroundColor: data.map((_,i) => modernColors[i % modernColors.length].bg),
                    borderColor: data.map((_,i) => modernColors[i % modernColors.length].border),
                    borderWidth:2, borderRadius:8, borderSkipped:false }] },
                options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{display:false},
                    tooltip:{ callbacks:{ label: ctx => 'Doanh thu: ' + new Intl.NumberFormat('vi-VN').format(ctx.parsed.y) + ' đ' } } },
                    scales:{ y:{ beginAtZero:true, ticks:{ callback: v => v>=1000000?(v/1000000).toFixed(1)+'M₫':v>=1000?(v/1000).toFixed(0)+'K₫':v+'₫' } }, x:{ grid:{display:false} } } }
            });
        } else { revenueByMovieCtx.parentElement.innerHTML = '<p class="text-muted text-center p-4">Chưa có dữ liệu doanh thu theo phim</p>'; }
    }

    // Revenue by date
    const revenueByDateCtx = document.getElementById('revenueByDateChart');
    if (revenueByDateCtx) {
        const revenueByDateData = <?php echo json_encode($revenueByDate ?? [], 15, 512) ?>;
        const datesData = <?php echo json_encode($dates ?? [], 15, 512) ?>;
        if (revenueByDateData && Object.keys(revenueByDateData).length > 0 && datesData && datesData.length > 0) {
            const datasets = [];
            <?php $colorIndex = 0; foreach ($revenueByDate as $movieId => $movieData): ?>
            datasets.push({ label: <?php echo json_encode($movieData['title'], 15, 512) ?>,
                data: [<?php echo implode(',', array_column($movieData['data'], 'revenue')); ?>],
                borderColor: modernColors[<?php echo e($colorIndex); ?> % modernColors.length].border,
                backgroundColor: modernColors[<?php echo e($colorIndex); ?> % modernColors.length].bg.replace('0.8','0.06'),
                borderWidth:3, fill:true, tension:0.4, pointRadius:4, pointHoverRadius:6,
                pointBackgroundColor: modernColors[<?php echo e($colorIndex); ?> % modernColors.length].border,
                pointBorderColor:'#fff', pointBorderWidth:2 });
            <?php $colorIndex++; endforeach; ?>
            new Chart(revenueByDateCtx.getContext('2d'), {
                type: 'line',
                data: { labels: datesData.map(d => new Date(d).toLocaleDateString('vi-VN',{day:'2-digit',month:'2-digit'})), datasets },
                options: { responsive:true, maintainAspectRatio:false, interaction:{mode:'index',intersect:false},
                    scales:{ y:{ beginAtZero:true, ticks:{ callback: v => new Intl.NumberFormat('vi-VN').format(v)+' đ' } }, x:{ grid:{display:false} } },
                    plugins:{ tooltip:{ callbacks:{ label: ctx => ctx.dataset.label+': '+new Intl.NumberFormat('vi-VN').format(ctx.parsed.y)+' đ' } } } }
            });
        } else { revenueByDateCtx.parentElement.innerHTML = '<p class="text-muted text-center p-4">Chưa có dữ liệu doanh thu theo ngày</p>'; }
    }

    // Fill rate filter
    document.getElementById('fillRateDateFilter')?.addEventListener('change', function() {
        const url = new URL(window.location.href);
        this.value === 'all' ? url.searchParams.delete('fill_rate_date') : url.searchParams.set('fill_rate_date', this.value);
        window.location.href = url.toString();
    });

    // Fill rate comparison chart
    const fillRateComparisonCtx = document.getElementById('fillRateComparisonChart');
    if (fillRateComparisonCtx) {
        const fillRateData = <?php echo json_encode($fillRateByMovieAvg ?? [], 15, 512) ?>;
        if (fillRateData && fillRateData.length > 0) {
            new Chart(fillRateComparisonCtx.getContext('2d'), {
                type: 'bar',
                data: { labels: fillRateData.map(i => i.movie_title), datasets: [{ label: 'Tỷ lệ lấp đầy (%)',
                    data: fillRateData.map(i => parseFloat(i.avg_fill_rate || 0)),
                    backgroundColor: fillRateData.map((_,i) => modernColors[i % modernColors.length].bg),
                    borderColor: fillRateData.map((_,i) => modernColors[i % modernColors.length].border),
                    borderWidth:2, borderRadius:8, borderSkipped:false }] },
                options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{display:false},
                    tooltip:{ callbacks:{ label: ctx => { const item = fillRateData[ctx.dataIndex];
                        return ['Tỷ lệ lấp đầy: '+ctx.parsed.y.toFixed(2)+'%','Vé đã đặt: '+item.total_booked_tickets,'Vé đã lấy: '+item.total_picked_up_tickets]; } } } },
                    scales:{ y:{ beginAtZero:true, max:100, ticks:{ callback: v => v+'%' } }, x:{ grid:{display:false}, ticks:{maxRotation:45,minRotation:45} } } }
            });
        } else { fillRateComparisonCtx.parentElement.innerHTML = '<p class="text-muted text-center p-4">Chưa có dữ liệu tỷ lệ lấp đầy</p>'; }
    }
}

(function() {
    function waitForChartJS(cb, maxAttempts = 100) {
        let attempts = 0;
        const check = setInterval(() => {
            attempts++;
            if (typeof Chart !== 'undefined' && typeof Chart.register !== 'undefined') { clearInterval(check); setTimeout(cb, 300); }
            else if (attempts >= maxAttempts) { clearInterval(check); }
        }, 50);
    }
    window.addEventListener('load', () => waitForChartJS(initStatisticsCharts));
})();

function applyFillRateFilters() {
    const movieFilter = document.getElementById('fillRateMovieFilter')?.value || 'all';
    const dateFilter = document.getElementById('fillRateDateFilterTable')?.value || 'all';
    const screenFilter = document.getElementById('fillRateScreenFilter')?.value || 'all';
    const timeFilter = document.getElementById('fillRateTimeFilter')?.value || 'all';
    const url = new URL(window.location.href);
    url.searchParams.set('fill_rate_movie', movieFilter);
    url.searchParams.set('fill_rate_date', dateFilter);
    url.searchParams.set('fill_rate_screen', screenFilter);
    url.searchParams.set('fill_rate_time', timeFilter);
    window.history.pushState({}, '', url.toString());
    const tableBody = document.querySelector('#fillRateTable tbody');
    if (tableBody) tableBody.innerHTML = '<tr><td colspan="7" class="text-center p-4"><i class="fas fa-spinner fa-spin"></i> Đang tải...</td></tr>';
    const apiUrl = new URL('?route=moderator/getFillRateData', window.location.origin);
    ['fill_rate_movie|'+movieFilter,'fill_rate_date|'+dateFilter,'fill_rate_screen|'+screenFilter,'fill_rate_time|'+timeFilter].forEach(p => {
        const [k,v] = p.split('|'); apiUrl.searchParams.set(k, v);
    });
    fetch(apiUrl.toString()).then(r => r.json()).then(data => {
        if (data.success && data.data) updateFillRateTable(data.data);
        else if (tableBody) tableBody.innerHTML = '<tr><td colspan="7" class="text-center p-4 text-muted">Không có dữ liệu</td></tr>';
    }).catch(() => { if (tableBody) tableBody.innerHTML = '<tr><td colspan="7" class="text-center p-4 text-danger">Lỗi khi tải dữ liệu</td></tr>'; });
}

function updateFillRateTable(data) {
    const tableBody = document.querySelector('#fillRateTable tbody');
    if (!tableBody) return;
    if (!data || data.length === 0) { tableBody.innerHTML = '<tr><td colspan="7" class="text-center p-4 text-muted">Chưa có dữ liệu</td></tr>'; return; }
    let html = '';
    data.forEach(item => {
        item.screens.forEach(screen => {
            const fillRate = parseFloat(screen.fill_rate || 0);
            const color = fillRate >= 80 ? '#00b894' : fillRate >= 50 ? '#fdcb6e' : '#e17055';
            html += `<tr><td>${formatDate(item.show_date)}</td><td>${screen.show_time?formatTime(screen.show_time):'-'}</td>
                <td><strong>${escapeHtml(screen.movie_title||'-')}</strong></td><td>${escapeHtml(screen.screen_name)}</td>
                <td class="text-center">${screen.booked_tickets||0}</td><td class="text-center" style="color:${color}">${screen.picked_up_tickets||0}</td>
                <td><div class="progress" style="height:25px;border-radius:12px;overflow:hidden;"><div class="progress-bar" role="progressbar" style="width:${fillRate}%;background:${color};display:flex;align-items:center;justify-content:center;font-weight:700;font-size:12px;color:#fff;" aria-valuenow="${fillRate}" aria-valuemin="0" aria-valuemax="100">${fillRate.toFixed(1)}%</div></div></td></tr>`;
        });
    });
    tableBody.innerHTML = html;
}

function formatDate(d) { if (!d) return ''; const dt = new Date(d); return `${String(dt.getDate()).padStart(2,'0')}/${String(dt.getMonth()+1).padStart(2,'0')}/${dt.getFullYear()}`; }
function formatTime(t) { if (!t) return '-'; return t.split(':').slice(0,2).join(':'); }
function escapeHtml(text) { const d = document.createElement('div'); d.textContent = text; return d.innerHTML; }
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.moderator.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\CineHub_DuAnTotNghiep\resources\views/admin/moderator/statistics.blade.php ENDPATH**/ ?>