@extends('admin.counter_staff.layout')

@section('content')
<div class="sales-history-container">
    <h2><i class="fas fa-history"></i> Lịch sử bán vé</h2>

    <!-- Thống kê hôm nay -->
    <div class="today-stats">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-ticket-alt"></i></div>
            <div class="stat-info">
                <span class="stat-value">{{ number_format($todayStats['ticket_count'] ?? 0) }}</span>
                <span class="stat-label">Vé đã bán hôm nay</span>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
            <div class="stat-info">
                <span class="stat-value">{{ number_format($todayStats['total_revenue'] ?? 0) }} đ</span>
                <span class="stat-label">Doanh thu hôm nay</span>
            </div>
        </div>
    </div>

    <!-- Bộ lọc -->
    <div class="filter-section">
        <form method="GET" action="{{ route('counter.sales') }}" class="filter-form">
            <div class="filter-group">
                <label>Ngày:</label>
                <input type="date" name="date" value="{{ $date }}" class="form-control">
            </div>
            <div class="filter-group">
                <label>Tìm kiếm:</label>
                <input type="text" name="search" value="{{ $search }}" placeholder="Tên KH, SĐT, ghế..." class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-search"></i> Lọc
            </button>
            @if($date || $search)
            <a href="{{ route('counter.sales') }}" class="btn btn-secondary">
                <i class="fas fa-times"></i> Xóa lọc
            </a>
            @endif
        </form>
    </div>

    @if($sales->isEmpty())
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <p>Chưa có vé nào được bán</p>
        </div>
    @else
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
                    @foreach($sales as $sale)
                    <tr>
                        <td>
                            <div class="datetime">
                                <span class="date">{{ $sale->created_at->format('d/m/Y') }}</span>
                                <span class="time">{{ $sale->created_at->format('H:i') }}</span>
                            </div>
                        </td>
                        <td><strong>{{ $sale->showtime->movie->title }}</strong></td>
                        <td>
                            <div class="showtime-info">
                                <span>{{ date('d/m', strtotime($sale->showtime->show_date)) }}</span>
                                <span>{{ date('H:i', strtotime($sale->showtime->show_time)) }}</span>
                                <small>{{ $sale->showtime->screen->screen_name ?? 'N/A' }}</small>
                            </div>
                        </td>
                        <td>
                            <span class="seat-badge {{ $sale->seat_type }}">{{ $sale->seat }}</span>
                        </td>
                        <td>
                            <div class="customer-info">
                                <span>{{ $sale->bookingPending->customer_name ?? 'Khách lẻ' }}</span>
                                @if($sale->bookingPending && $sale->bookingPending->customer_phone)
                                <small>{{ $sale->bookingPending->customer_phone }}</small>
                                @endif
                            </div>
                        </td>
                        <td><span class="price">{{ number_format($sale->price) }} đ</span></td>
                        <td>
                            <a href="{{ route('counter.print', ['booking_id' => $sale->booking_pending_id]) }}" target="_blank" class="btn btn-sm btn-print">
                                <i class="fas fa-print"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $sales->links() }}

        <div class="total-info">Tổng: <strong>{{ number_format($sales->total()) }}</strong> vé</div>
    @endif
</div>

<style>
.sales-history-container { padding: 20px; }
.today-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 30px; }
.stat-card { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 15px; padding: 20px; display: flex; align-items: center; gap: 15px; color: #fff; }
.stat-icon { width: 60px; height: 60px; background: rgba(255,255,255,0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 24px; }
.stat-info { display: flex; flex-direction: column; }
.stat-value { font-size: 24px; font-weight: bold; }
.stat-label { font-size: 14px; opacity: 0.9; }
.filter-section { background: #2a2a2a; border-radius: 10px; padding: 20px; margin-bottom: 20px; }
.filter-form { display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap; }
.filter-group { display: flex; flex-direction: column; gap: 5px; }
.filter-group label { color: #aaa; font-size: 14px; }
.form-control { padding: 10px 15px; border: 1px solid #444; border-radius: 5px; background: #333; color: #fff; min-width: 150px; }
.btn { padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; font-weight: 500; display: inline-flex; align-items: center; gap: 8px; }
.btn-primary { background: #e50914; color: #fff; }
.btn-secondary { background: #666; color: #fff; }
.btn-sm { padding: 5px 10px; font-size: 14px; }
.btn-print { background: #4CAF50; color: #fff; }
.empty-state { text-align: center; padding: 50px; color: #aaa; }
.empty-state i { font-size: 48px; margin-bottom: 15px; }
.sales-table-wrapper { overflow-x: auto; }
.sales-table { width: 100%; border-collapse: collapse; background: #2a2a2a; border-radius: 10px; overflow: hidden; }
.sales-table th, .sales-table td { padding: 15px; text-align: left; border-bottom: 1px solid #444; }
.sales-table th { background: #333; color: #fff; font-weight: 600; }
.sales-table td { color: #ddd; }
.datetime { display: flex; flex-direction: column; }
.datetime .date { font-weight: 500; }
.datetime .time { font-size: 12px; color: #888; }
.showtime-info { display: flex; flex-direction: column; font-size: 14px; }
.showtime-info small { color: #888; }
.seat-badge { display: inline-block; padding: 5px 12px; border-radius: 20px; font-weight: bold; font-size: 14px; }
.seat-badge.normal { background: #4a4a4a; color: #fff; }
.seat-badge.vip { background: #ffd700; color: #333; }
.seat-badge.couple { background: #ff69b4; color: #fff; }
.customer-info { display: flex; flex-direction: column; }
.customer-info small { color: #888; font-size: 12px; }
.price { font-weight: bold; color: #4CAF50; }
.pagination { display: flex; justify-content: center; gap: 5px; margin-top: 20px; }
.page-link { padding: 8px 15px; background: #333; color: #fff; text-decoration: none; border-radius: 5px; }
.page-link.active { background: #e50914; }
.total-info { text-align: center; margin-top: 15px; color: #aaa; }
</style>
@endsection
