@extends('admin.layout')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Quan ly phim</h2>
        <div>
            <a href="{{ route('admin.movies.scanEpisodes') }}" class="btn btn-info me-2">
                <i class="fas fa-folder-open"></i> Import tap tu folder
            </a>
            <a href="{{ route('admin.movies.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Them phim moi
            </a>
        </div>
    </div>

    <form method="GET" action="{{ route('admin.movies.index') }}" class="mb-3">
        <div class="row g-2">
            <div class="col-md-4">
                <input
                    type="text"
                    name="search"
                    class="form-control"
                    placeholder="Tim kiem phim..."
                    value="{{ $search ?? '' }}"
                >
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Tat ca trang thai</option>
                    <option value="Chiếu online" @selected(($status ?? '') === 'Chiếu online')>Phim online</option>
                    <option value="Sắp chiếu" @selected(($status ?? '') === 'Sắp chiếu')>Phim sap chieu</option>
                    <option value="Chiếu rạp" @selected(($status ?? '') === 'Chiếu rạp')>Phim chieu rap</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="fas fa-search"></i> Tim
                </button>
            </div>
        </div>
    </form>

    <div class="stat-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Poster</th>
                        <th>Tieu de</th>
                        <th>Loai</th>
                        <th>The loai</th>
                        <th>Trang thai</th>
                        <th>Ngay chieu</th>
                        <th>Rating</th>
                        <th>Thao tac</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($movies as $movie)
                        <tr>
                            <td>{{ $movie->id }}</td>
                            <td>
                                @if ($movie->thumbnail)
                                    <img src="{{ $movie->thumbnail }}" alt="{{ $movie->title }}" style="width: 60px; height: 90px; object-fit: cover; border-radius: 5px;">
                                @else
                                    <div class="bg-secondary d-flex align-items-center justify-content-center text-white" style="width: 60px; height: 90px; border-radius: 5px;">
                                        <i class="fas fa-film"></i>
                                    </div>
                                @endif
                            </td>
                            <td>
                                <strong>{{ $movie->title }}</strong>
                                @if ($movie->director)
                                    <br>
                                    <small class="text-muted">Dao dien: {{ $movie->director }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-{{ ($movie->type ?? 'phimle') === 'phimbo' ? 'primary' : 'secondary' }}">
                                    {{ ($movie->type ?? 'phimle') === 'phimbo' ? 'Phim bo' : 'Phim le' }}
                                </span>
                            </td>
                            <td>{{ optional($movie->category)->name ?? 'Chua gan' }}</td>
                            <td>
                                @php
                                    $statusBg = match($movie->status) {
                                        'Chiếu online' => 'success',
                                        'Chiếu rạp' => 'info',
                                        'Sắp chiếu' => 'warning',
                                        default => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $statusBg }}">{{ $movie->status ?? 'Chua cap nhat' }}</span>
                                <br>
                                <small class="text-muted">{{ $movie->status_admin ?? 'draft' }}</small>
                            </td>
                            <td>{{ optional($movie->publish_date)->format('d/m/Y H:i') ?? '-' }}</td>
                            <td><i class="fas fa-star text-warning"></i> {{ number_format($movie->rating ?? 0, 1) }}/10</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.movies.edit', $movie->id) }}" class="btn btn-outline-primary" title="Sua">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="{{ route('movies.show', $movie->id) }}" class="btn btn-outline-info" title="Xem">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('admin.movies.destroy', $movie->id) }}" method="POST" onsubmit="return confirm('Ban chac chan muon xoa phim {{ addslashes($movie->title) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="Xoa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">Khong co phim nao</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
