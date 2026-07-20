@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Quản lý vé</h2>
    </div>

    <!-- Thống kê tổng quan -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-primary">
                    <i class="fas fa-ticket-alt"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Tổng số vé</div>
                    <div class="stat-value">{{ number_format($overallStats['total_tickets'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-success">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Vé đã bán</div>
                    <div class="stat-value">{{ number_format($overallStats['tickets_sold'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-danger">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Vé đã hủy</div>
                    <div class="stat-value">{{ number_format($overallStats['tickets_cancelled'] ?? 0) }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="stat-icon bg-warning">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-info">
                    <div class="stat-label">Hoa hồng nền tảng (5%)</div>
                    <div class="stat-value">{{ number_format($overallStats['total_revenue'] ?? 0) }}₫</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" action="{{ route('admin.tickets.index') }}" class="mb-4">
        <div class="stat-card">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">Lọc theo phim</label>
                    <select name="movie_id" class="form-select">
                        <option value="">Tất cả phim</option>
                        @foreach ($movies ?? [] as $movie)
                            <option value="{{ $movie['id'] }}" {{ ($movie_id ?? '') == $movie['id'] ? 'selected' : '' }}>
                                {{ $movie['title'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">Lọc theo trạng thái</label>
                    <select name="status" class="form-select">
                        <option value="">Tất cả trạng thái</option>
                        <option value="Đã đặt" {{ ($status ?? '') === 'Đã đặt' ? 'selected' : '' }}>Đã đặt</option>
                        <option value="Đã hủy" {{ ($status ?? '') === 'Đã hủy' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-secondary w-100">
                        <i class="fas fa-filter"></i> Lọc
                    </button>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-redo"></i> Xóa bộ lọc
                    </a>
                </div>
            </div>
        </div>
    </form>

    <!-- Phân loại vé theo phim - Thống kê tồn kho -->
    @if (!empty($inventoryStats))
        <div class="stat-card mb-4">
            <h6 class="mb-3">
                <i class="fas fa-chart-bar me-2"></i>Phân loại vé theo phim - Thống kê tồn kho
            </h6>
            <div class="table-responsive">
                <table class="table table-hover table-sm">
                    <thead>
                        <tr>
                            <th>Phim</th>
                            <th>Số suất chiếu</th>
                            <th>Vé đã bán</th>
                            <th>Vé tồn kho</th>
                            <th>Giới hạn vé</th>
                            <th>Doanh thu</th>
                            <th>Tỷ lệ bán</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inventoryStats as $stat)
                            @php
                                $total_seats = $stat['total_showtimes'] * 132;
                                $sold = $stat['tickets_sold'] ?? 0;
                                $available = $stat['tickets_available'] ?? $total_seats;
                                $revenue = $stat['total_revenue'] ?? 0;
                                $max_tickets = $stat['max_tickets'] ?? null;
                                $sell_rate = $total_seats > 0 ? round(($sold / $total_seats) * 100, 2) : 0;
                            @endphp
                            <tr>
                                <td>
                                    <strong>{{ $stat['movie_title'] }}</strong>
                                </td>
                                <td>{{ number_format($stat['total_showtimes']) }}</td>
                                <td>
                                    <span class="badge bg-success">{{ number_format($sold) }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $available > 50 ? 'bg-info' : ($available > 20 ? 'bg-warning' : 'bg-danger') }}">
                                        {{ number_format($available) }}
                                    </span>
                                </td>
                                <td>
                                    <form method="POST" action="{{ url('?route=admin/ticketsUpdateMovie') }}" class="d-inline-flex align-items-center gap-2" style="min-width: 200px;">
                                        @csrf
                                        <input type="hidden" name="movie_id" value="{{ $stat['movie_id'] }}">
                                        <input type="number" 
                                               name="max_tickets" 
                                               class="form-control form-control-sm" 
                                               value="{{ $max_tickets !== null ? $max_tickets : '' }}" 
                                               placeholder="Không giới hạn"
                                               min="0"
                                               style="width: 120px;">
                                        <button type="submit" class="btn btn-sm btn-primary" title="Cập nhật">
                                            <i class="fas fa-save"></i>
                                        </button>
                                    </form>
                                    @if ($max_tickets !== null)
                                        <small class="text-muted d-block mt-1">
                                            <i class="fas fa-info-circle"></i> Giới hạn: {{ number_format($max_tickets) }} vé
                                        </small>
                                    @endif
                                </td>
                                <td>{{ number_format($revenue) }}₫</td>
                                <td>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="progress" style="width: 100px; height: 20px;">
                                            <div class="progress-bar" role="progressbar" style="width: {{ $sell_rate }}%;" 
                                                 aria-valuenow="{{ $sell_rate }}" aria-valuemin="0" aria-valuemax="100">
                                                {{ $sell_rate }}%
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <a href="{{ url('?route=admin/tickets&movie_id=' . $stat['movie_id']) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-list"></i> Chi tiết
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <!-- Danh sách vé -->
    <div class="stat-card">
        <h6 class="mb-3">
            <i class="fas fa-list me-2"></i>Danh sách vé
        </h6>
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mã vé</th>
                        <th>Khách hàng</th>
                        <th>Phim</th>
                        <th>Phòng chiếu</th>
                        <th>Ghế</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                        <th>Ngày đặt</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @if ($tickets->isEmpty())
                        <tr>
                            <td colspan="10" class="text-center text-muted">Không có vé nào</td>
                        </tr>
                    @else
                        @foreach ($tickets as $ticket)
                            <tr>
                                <td>{{ $ticket->id }}</td>
                                <td>
                                    <code>{{ $ticket->bookingPending?->booking_code ?: 'VE-' . str_pad((string) $ticket->id, 6, '0', STR_PAD_LEFT) }}</code>
                                </td>
                                <td>{{ $ticket->user?->name ?? $ticket->bookingPending?->customer_name ?? $ticket->bookingPending?->customer_email ?? 'Không xác định' }}</td>
                                <td>{{ $ticket->showtime?->movie?->title ?? 'Không xác định' }}</td>
                                <td>{{ $ticket->showtime?->screen?->screen_name ?? 'Không xác định' }}</td>
                                <td>{{ $ticket->seat ?? 'N/A' }}</td>
                                <td>{{ number_format((float) ($ticket->price ?? 0)) }}₫</td>
                                <td>
                                    <span class="badge bg-{{ $ticket->status === 'Đã đặt' ? 'success' : 'danger' }}">
                                        {{ $ticket->status ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>{{ optional($ticket->created_at)->format('d/m/Y H:i') }}</td>
                                <td>
                                    <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
        <div class="mt-3">{{ $tickets->withQueryString()->links() }}</div>
    </div>
</div>
@endsection
