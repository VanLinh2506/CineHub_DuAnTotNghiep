@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Quản lý phim</h2>
        <div>
            <a href="{{ url('?route=admin/movies/scanEpisodes') }}" class="btn btn-info me-2">
                <i class="fas fa-folder-open"></i> Import tập từ folder
            </a>
            <a href="{{ url('?route=admin/movies/create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm phim mới
            </a>
        </div>
    </div>

    <!-- Filters -->
    <form method="GET" class="mb-3">
        <input type="hidden" name="route" value="admin/movies">
        <div class="row g-2">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm phim..." 
                       value="{{ old('search') ?? ($search ?? '') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select" id="statusFilter" onchange="this.form.submit()">
                    <option value="">Tất cả trạng thái</option>
                    <option value="Chiếu online" {{ (isset($status) && $status === 'Chiếu online') ? 'selected' : '' }}>Phim online</option>
                    <option value="Sắp chiếu" {{ (isset($status) && $status === 'Sắp chiếu') ? 'selected' : '' }}>Phim sắp chiếu</option>
                    <option value="Chiếu rạp" {{ (isset($status) && $status === 'Chiếu rạp') ? 'selected' : '' }}>Phim chiếu rạp</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="fas fa-search"></i> Tìm
                </button>
            </div>
        </div>
    </form>

    <!-- Movies Table -->
    <div class="stat-card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Poster</th>
                        <th>Tiêu đề</th>
                        <th>Loại</th>
                        <th>Thể loại</th>
                        <th>Trạng thái</th>
                        <th>Rating</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @if (empty($movies))
                        <tr>
                            <td colspan="9" class="text-center text-muted">Không có phim nào</td>
                        </tr>
                    @else
                        @foreach ($movies as $m)
                            <tr>
                                <td>{{ $m['id'] }}</td>
                                <td>
                                    @if ($m['thumbnail'] ?? null)
                                        <img src="{{ $m['thumbnail'] }}" alt="" style="width: 60px; height: 90px; object-fit: cover; border-radius: 5px;">
                                    @else
                                        <div class="bg-secondary d-flex align-items-center justify-content-center" style="width: 60px; height: 90px; border-radius: 5px;">
                                            <i class="fas fa-film text-white"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $m['title'] }}</strong>
                                    @if ($m['director'] ?? null)
                                        <br><small class="text-muted">Đạo diễn: {{ $m['director'] }}</small>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $movieType = $m['type'] ?? 'phimle';
                                    @endphp
                                    @if ($movieType === 'phimbo')
                                        <span class="badge bg-primary">Phim bộ</span>
                                    @else
                                        <span class="badge bg-secondary">Phim lẻ</span>
                                    @endif
                                </td>
                                <td>{{ $m['category_name'] ?? 'N/A' }}</td>
                                <td>
                                    @php
                                        $movieStatus = $m['status'] ?? 'Sắp chiếu';
                                        $statusBg = match($movieStatus) {
                                            'Chiếu online' => 'success',
                                            'Chiếu rạp' => 'info',
                                            'Sắp chiếu' => 'warning',
                                            default => 'secondary'
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $statusBg }}">{{ $movieStatus }}</span>
                                    @if ($m['status_admin'] ?? null)
                                        <br><small class="text-muted">{{ $m['status_admin'] }}</small>
                                    @endif
                                </td>
                                <td>
                                    <i class="fas fa-star" style="color: gold;"></i> {{ $m['rating'] ?? 0 }}/10
                                </td>
                                <td>{{ \Carbon\Carbon::parse($m['created_at'])->format('d/m/Y') }}</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ url('?route=admin/movies/edit&id=' . $m['id']) }}" class="btn btn-outline-primary" title="Sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="{{ url('?route=admin/movies/view&id=' . $m['id']) }}" class="btn btn-outline-info" title="Xem chi tiết">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <button onclick="deleteMovie({{ $m['id'] }}, '{{ $m['title'] }}')" class="btn btn-outline-danger" title="Xóa">
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
    function deleteMovie(movieId, title) {
        if (confirm('Bạn chắc chắn muốn xóa phim "' + title + '"?')) {
            // Submit delete action
            window.location.href = '{{ url("?route=admin/movies/delete&id=") }}' + movieId;
        }
    }
</script>
@endsection
