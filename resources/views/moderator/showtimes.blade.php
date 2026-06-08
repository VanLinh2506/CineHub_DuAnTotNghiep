@extends('layouts.app')

@php
    $title = 'Quản lý suất chiếu';
@endphp

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5>{{ $title }} - {{ $theater['name'] ?? 'Theater' }}</h5>
        <a href="{{ url('?route=moderator/showtimes/create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Thêm suất chiếu
        </a>
    </div>

    <!-- Filters -->
    <form method="GET" class="mb-3">
        <input type="hidden" name="route" value="moderator/showtimes">
        <div class="row g-2">
            <div class="col-md-4">
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
                <select name="screen_id" class="form-select">
                    <option value="">Tất cả phòng</option>
                    @foreach ($screens as $screen)
                        <option value="{{ $screen['id'] }}" {{ (isset($screen_id) && $screen_id == $screen['id']) ? 'selected' : '' }}>
                            {{ $screen['name'] }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="fas fa-filter"></i> Lọc
                </button>
            </div>
        </div>
    </form>

    <!-- Showtimes Table -->
    <div class="stat-card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Phim</th>
                        <th>Phòng</th>
                        <th>Ngày</th>
                        <th>Giờ</th>
                        <th>Giá vé</th>
                        <th>Vé đã bán</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @if (empty($showtimes))
                        <tr>
                            <td colspan="9" class="text-center text-muted">Không có suất chiếu nào</td>
                        </tr>
                    @else
                        @foreach ($showtimes as $showtime)
                            <tr>
                                <td>#{{ $showtime['id'] }}</td>
                                <td><strong>{{ $showtime['movie_title'] }}</strong></td>
                                <td>{{ $showtime['screen_name'] }}</td>
                                <td>{{ \Carbon\Carbon::parse($showtime['show_date'])->format('d/m/Y') }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ \Carbon\Carbon::parse($showtime['show_time'])->format('H:i') }}</span>
                                </td>
                                <td>{{ number_format($showtime['price'] ?? 0) }}₫</td>
                                <td>
                                    <span class="badge bg-info">{{ $showtime['tickets_sold'] ?? 0 }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ ($showtime['status'] ?? 'active') === 'active' ? 'success' : 'secondary' }}">
                                        {{ ($showtime['status'] ?? 'active') === 'active' ? 'Hoạt động' : 'Đã đóng' }}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ url('?route=moderator/showtimes/edit&id=' . $showtime['id']) }}" class="btn btn-outline-primary" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button onclick="deleteShowtime({{ $showtime['id'] }})" class="btn btn-outline-danger" title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function deleteShowtime(id) {
        if (confirm('Bạn chắc chắn muốn xóa suất chiếu này?')) {
            window.location.href = '{{ url("?route=moderator/showtimes/delete&id=") }}' + id;
        }
    }
</script>
@endsection
