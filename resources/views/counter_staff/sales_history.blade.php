@extends('layouts.app')

@php
    $title = 'Lịch sử bán hàng';
@endphp

@section('content')
<div class="container">
    <h5 class="mb-4">{{ $title }}</h5>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Tổng vé hôm nay</div>
                <div class="stat-value text-primary counter" data-target="{{ $stats['today_tickets'] ?? 0 }}">0</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Doanh thu hôm nay</div>
                <div class="stat-value text-success revenue-counter" data-target="{{ $stats['today_revenue'] ?? 0 }}">0₫</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Tổng vé tháng này</div>
                <div class="stat-value text-info counter" data-target="{{ $stats['month_tickets'] ?? 0 }}">0</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-label">Doanh thu tháng này</div>
                <div class="stat-value text-warning revenue-counter" data-target="{{ $stats['month_revenue'] ?? 0 }}">0₫</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="mb-3">
        <input type="hidden" name="route" value="counter_staff/salesHistory">
        <div class="row g-2">
            <div class="col-md-3">
                <input type="date" name="date_from" class="form-control" placeholder="Từ ngày"
                       value="{{ $date_from ?? '' }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="date_to" class="form-control" placeholder="Đến ngày"
                       value="{{ $date_to ?? '' }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="fas fa-filter"></i> Lọc
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ url('?route=counter_staff/salesHistory') }}" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-redo"></i> Xóa
                </a>
            </div>
        </div>
    </form>

    <!-- Sales Table -->
    <div class="stat-card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mã vé</th>
                        <th>Khách hàng</th>
                        <th>Phim</th>
                        <th>Suất chiếu</th>
                        <th>Ghế</th>
                        <th>Giá</th>
                        <th>Ngày bán</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    @if (empty($sales))
                        <tr>
                            <td colspan="9" class="text-center text-muted">Không có bán hàng nào</td>
                        </tr>
                    @else
                        @foreach ($sales as $sale)
                            <tr>
                                <td>#{{ $sale['id'] }}</td>
                                <td><code>{{ $sale['ticket_code'] }}</code></td>
                                <td>{{ $sale['user_name'] }}</td>
                                <td>{{ $sale['movie_title'] }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($sale['show_date'])->format('d/m/Y') }}
                                    <br><small>{{ \Carbon\Carbon::parse($sale['show_time'])->format('H:i') }}</small>
                                </td>
                                <td>{{ $sale['seat'] }}</td>
                                <td><strong>{{ number_format($sale['price']) }}₫</strong></td>
                                <td>{{ \Carbon\Carbon::parse($sale['created_at'])->format('d/m/Y H:i') }}</td>
                                <td>
                                    <span class="badge bg-{{ ($sale['status'] ?? '') === 'Đã đặt' ? 'success' : 'danger' }}">
                                        {{ $sale['status'] ?? 'N/A' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Export -->
    <div class="mt-3">
        <a href="{{ url('?route=counter_staff/exportSales') }}" class="btn btn-success">
            <i class="fas fa-download"></i> Xuất Excel
        </a>
    </div>
</div>

<script>
    document.querySelectorAll('.counter').forEach(element => {
        const target = parseInt(element.dataset.target);
        let current = 0;
        const increment = target / 100;
        
        const updateCounter = () => {
            if (current < target) {
                current += increment;
                element.textContent = Math.floor(current);
                setTimeout(updateCounter, 20);
            } else {
                element.textContent = target;
            }
        };
        
        updateCounter();
    });

    document.querySelectorAll('.revenue-counter').forEach(element => {
        const target = parseInt(element.dataset.target);
        let current = 0;
        const increment = target / 100;
        
        const updateCounter = () => {
            if (current < target) {
                current += increment;
                element.textContent = Math.floor(current).toLocaleString('vi-VN') + '₫';
                setTimeout(updateCounter, 20);
            } else {
                element.textContent = target.toLocaleString('vi-VN') + '₫';
            }
        };
        
        updateCounter();
    });
</script>
@endsection
