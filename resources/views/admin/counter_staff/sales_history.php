<?php
// Lấy base URL
if (!class_exists('UrlHelper')) {
    require_once __DIR__ . '/../../../core/UrlHelper.php';
}
$baseUrl = UrlHelper::getBaseUrl();
?>

<div class="sales-history-container">
    <h2><i class="fas fa-history"></i> Lịch sử bán vé</h2>
    
    <!-- Thống kê hôm nay -->
    <div class="today-stats">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-ticket-alt"></i></div>
            <div class="stat-info">
                <span class="stat-value"><?php echo number_format($todayStats['ticket_count'] ?? 0); ?></span>
                <span class="stat-label">Vé đã bán hôm nay</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
            <div class="stat-info">
                <span class="stat-value"><?php echo number_format($todayStats['total_revenue'] ?? 0); ?> đ</span>
                <span class="stat-label">Doanh thu hôm nay</span>
            </div>
        </div>
    </div>
    
    <!-- Bộ lọc -->
    <div class="filter-section">
        <form method="GET" action="" class="filter-form">
            <input type="hidden" name="route" value="counterStaff/salesHistory">
            
            <div class="filter-group">
                <label>Ngày:</label>
                <input type="date" name="date" value="<?php echo htmlspecialchars($date); ?>" class="form-control">
            </div>
            
            <div class="filter-group">
                <label>Tìm kiếm:</label>
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Tên KH, SĐT, ghế..." class="form-control">
            </div>
            
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Lọc
            </button>
            
            <?php if ($date || $search): ?>
            <a href="?route=counterStaff/salesHistory" class="btn btn-secondary">
                <i class="fas fa-times"></i> Xóa lọc
            </a>
            <?php endif; ?>
        </form>
    </div>
    
    <!-- Danh sách vé đã bán -->
    <?php if (empty($sales)): ?>
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>Chưa có vé nào được bán</p>
        </div>
    <?php else: ?>
        <div class="sales-table-wrapper">
            <table class="sales-table">
                <thead>
                    <tr>
                        <th>Thời gian</th>
                        <th>Phim</th>
                        <th>Suất chiếu</th>
                        <th>Ghế</th>
                        <th>Khách hàng</th>
                        <th>Giá vé</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales as $sale): ?>
                    <tr>
                        <td>
                            <div class="datetime">
                                <span class="date"><?php echo date('d/m/Y', strtotime($sale['created_at'])); ?></span>
                                <span class="time"><?php echo date('H:i', strtotime($sale['created_at'])); ?></span>
                            </div>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($sale['movie_title']); ?></strong>
                        </td>
                        <td>
                            <div class="showtime-info">
                                <span><?php echo date('d/m', strtotime($sale['show_date'])); ?></span>
                                <span><?php echo date('H:i', strtotime($sale['show_time'])); ?></span>
                                <small><?php echo htmlspecialchars($sale['screen_name']); ?></small>
                            </div>
                        </td>
                        <td>
                            <span class="seat-badge <?php echo $sale['seat_type']; ?>">
                                <?php echo htmlspecialchars($sale['seat']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="customer-info">
                                <span><?php echo htmlspecialchars($sale['customer_name'] ?? 'Khách lẻ'); ?></span>
                                <?php if (!empty($sale['customer_phone'])): ?>
                                <small><?php echo htmlspecialchars($sale['customer_phone']); ?></small>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <span class="price"><?php echo number_format($sale['price']); ?> đ</span>
                        </td>
                        <td>
                            <a href="?route=counterStaff/printTickets&booking_id=<?php echo $sale['booking_id']; ?>" 
                               target="_blank" class="btn btn-sm btn-print">
                                <i class="fas fa-print"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Phân trang -->
        <?php if ($total_pages > 1): ?>
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="?route=counterStaff/salesHistory&page=<?php echo $i; ?>&date=<?php echo urlencode($date); ?>&search=<?php echo urlencode($search); ?>" 
                   class="page-link <?php echo $i == $page ? 'active' : ''; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
        <?php endif; ?>
        
        <div class="total-info">
            Tổng: <strong><?php echo number_format($total); ?></strong> vé
        </div>
    <?php endif; ?>
</div>

<style>
.sales-history-container {
    padding: 20px;
}

.today-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
    color: #fff;
}

.stat-icon {
    width: 60px;
    height: 60px;
    background: rgba(255,255,255,0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
}

.stat-info {
    display: flex;
    flex-direction: column;
}

.stat-value {
    font-size: 24px;
    font-weight: bold;
}

.stat-label {
    font-size: 14px;
    opacity: 0.9;
}

.filter-section {
    background: #2a2a2a;
    border-radius: 10px;
    padding: 20px;
    margin-bottom: 20px;
}

.filter-form {
    display: flex;
    gap: 15px;
    align-items: flex-end;
    flex-wrap: wrap;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.filter-group label {
    color: #aaa;
    font-size: 14px;
}

.form-control {
    padding: 10px 15px;
    border: 1px solid #444;
    border-radius: 5px;
    background: #333;
    color: #fff;
    min-width: 150px;
}

.btn {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary {
    background: #e50914;
    color: #fff;
}

.btn-secondary {
    background: #666;
    color: #fff;
}

.btn-sm {
    padding: 5px 10px;
    font-size: 14px;
}

.btn-print {
    background: #4CAF50;
    color: #fff;
}

.empty-state {
    text-align: center;
    padding: 50px;
    color: #aaa;
}

.empty-state i {
    font-size: 48px;
    margin-bottom: 15px;
}

.sales-table-wrapper {
    overflow-x: auto;
}

.sales-table {
    width: 100%;
    border-collapse: collapse;
    background: #2a2a2a;
    border-radius: 10px;
    overflow: hidden;
}

.sales-table th,
.sales-table td {
    padding: 15px;
    text-align: left;
    border-bottom: 1px solid #444;
}

.sales-table th {
    background: #333;
    color: #fff;
    font-weight: 600;
}

.sales-table td {
    color: #ddd;
}

.datetime {
    display: flex;
    flex-direction: column;
}

.datetime .date {
    font-weight: 500;
}

.datetime .time {
    font-size: 12px;
    color: #888;
}

.showtime-info {
    display: flex;
    flex-direction: column;
    font-size: 14px;
}

.showtime-info small {
    color: #888;
}

.seat-badge {
    display: inline-block;
    padding: 5px 12px;
    border-radius: 20px;
    font-weight: bold;
    font-size: 14px;
}

.seat-badge.normal {
    background: #4a4a4a;
    color: #fff;
}

.seat-badge.vip {
    background: #ffd700;
    color: #333;
}

.seat-badge.couple {
    background: #ff69b4;
    color: #fff;
}

.customer-info {
    display: flex;
    flex-direction: column;
}

.customer-info small {
    color: #888;
    font-size: 12px;
}

.price {
    font-weight: bold;
    color: #4CAF50;
}

.pagination {
    display: flex;
    justify-content: center;
    gap: 5px;
    margin-top: 20px;
}

.page-link {
    padding: 8px 15px;
    background: #333;
    color: #fff;
    text-decoration: none;
    border-radius: 5px;
}

.page-link.active {
    background: #e50914;
}

.total-info {
    text-align: center;
    margin-top: 15px;
    color: #aaa;
}
</style>
