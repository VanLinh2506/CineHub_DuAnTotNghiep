@extends('layouts.app')

@php
    $title = 'Quản lý vé';
@endphp

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5>{{ $title }} - {{ $theater['name'] ?? 'Theater' }}</h5>
    </div>

    <!-- Filters -->
    <form method="GET" class="mb-3">
        <input type="hidden" name="route" value="moderator/tickets">
        <div class="row g-2">
            <div class="col-md-3">
                <select name="movie_id" class="form-select">
                    <option value="">Tất cả phim</option>
                    @foreach ($movies as $movie)
                        <option value="{{ $movie['id'] }}" {{ (isset($movie_id) && $movie_id == $movie['id']) ? 'selected' : '' }}>
                            {{ $movie['title'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Tất cả trạng thái</option>
                    <option value="Đã đặt" {{ (isset($status) && $status === 'Đã đặt') ? 'selected' : '' }}>Đã đặt</option>
                    <option value="Đã hủy" {{ (isset($status) && $status === 'Đã hủy') ? 'selected' : '' }}>Đã hủy</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="fas fa-filter"></i> Lọc
                </button>
            </div>
        </div>
    </form>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">Tổng vé</div>
                <div class="stat-value text-primary">{{ number_format($stats['total'] ?? 0) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">Vé đã bán</div>
                <div class="stat-value text-success">{{ number_format($stats['sold'] ?? 0) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label">Vé hủy</div>
                <div class="stat-value text-danger">{{ number_format($stats['cancelled'] ?? 0) }}</div>
            </div>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="stat-card">
        <div class="table-responsive">
            <table class="table table-hover table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Mã vé</th>
                        <th>Khách hàng</th>
                        <th>Phim</th>
                        <th>Phòng/Ghế</th>
                        <th>Ngày/Giờ</th>
                        <th>Giá</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @if (empty($tickets))
                        <tr>
                            <td colspan="9" class="text-center text-muted">Không có vé nào</td>
                        </tr>
                    @else
                        @foreach ($tickets as $ticket)
                            <tr>
                                <td>#{{ $ticket['id'] }}</td>
                                <td><code>{{ $ticket['ticket_code'] ?? 'N/A' }}</code></td>
                                <td>{{ $ticket['user_name'] ?? 'N/A' }}</td>
                                <td>{{ $ticket['movie_title'] ?? 'N/A' }}</td>
                                <td>{{ $ticket['screen_name'] ?? 'N/A' }} - {{ $ticket['seat'] ?? 'N/A' }}</td>
                                <td>
                                    {{ \Carbon\Carbon::parse($ticket['show_date'])->format('d/m/Y') }}
                                    <br>
                                    <small>{{ \Carbon\Carbon::parse($ticket['show_time'])->format('H:i') }}</small>
                                </td>
                                <td>{{ number_format($ticket['price'] ?? 0) }}₫</td>
                                <td>
                                    <span class="badge bg-{{ ($ticket['status'] ?? '') === 'Đã đặt' ? 'success' : 'danger' }}">
                                        {{ $ticket['status'] ?? 'N/A' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ url('?route=moderator/tickets/view&id=' . $ticket['id']) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
