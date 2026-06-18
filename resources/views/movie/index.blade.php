@extends('layouts.app')

@section('content')
<section class="section">
    <br>
    <div class="container">
        <div class="filter-bar">
            <form method="GET" class="search-form" action="{{ route('movies.index') }}">
                <input type="hidden" name="route" value="movie/index">
                @if(isset($search) && $search)
                <input type="hidden" name="search" value="{{ $search }}">
                @endif

                <div class="filter-options">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label class="form-label small">Thể loại</label>
                            <select name="category" class="form-select form-select-sm">
                                <option value="">Tất cả thể loại</option>
                                @foreach($categories as $cat)
                                <option value="{{ $cat['id'] }}" {{ (isset($category_id) && $category_id == $cat['id']) ? 'selected' : '' }}>
                                    {{ $cat['name'] }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Trạng thái</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">Tất cả trạng thái</option>
                                <option value="Chiếu online" {{ (isset($status) && $status === 'Chiếu online') ? 'selected' : '' }}>Chiếu online</option>
                                <option value="Sắp chiếu" {{ (isset($status) && $status === 'Sắp chiếu') ? 'selected' : '' }}>Sắp chiếu</option>
                                <option value="Chiếu rạp" {{ (isset($status) && $status === 'Chiếu rạp') ? 'selected' : '' }}>Chiếu rạp</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Loại phim</label>
                            <select name="type" class="form-select form-select-sm">
                                <option value="">Tất cả</option>
                                <option value="phimle" {{ (isset($type) && $type === 'phimle') ? 'selected' : '' }}>Phim lẻ</option>
                                <option value="phimbo" {{ (isset($type) && $type === 'phimbo') ? 'selected' : '' }}>Phim bộ</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label small">Quốc gia</label>
                            <select name="country" class="form-select form-select-sm">
                                <option value="">Tất cả quốc gia</option>
                                @if(isset($countries) && $countries->isNotEmpty())
                                @foreach($countries as $c)
                                <option value="{{ $c }}" {{ (isset($country) && $country === $c) ? 'selected' : '' }}>
                                    {{ $c }}
                                </option>
                                @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label small">Đánh giá tối thiểu</label>
                            <select name="min_rating" class="form-select form-select-sm">
                                <option value="">Tất cả</option>
                                @foreach([9 => '9.0+', 8 => '8.0+', 7 => '7.0+', 6 => '6.0+', 5 => '5.0+'] as $val => $label)
                                <option value="{{ $val }}" {{ (isset($min_rating) && $min_rating == $val) ? 'selected' : '' }}>{{ $label }} ⭐</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mt-3">
                        <button type="submit" class="btn btn-sm btn-primary">
                            <i class="fas fa-filter"></i> Áp dụng bộ lọc
                        </button>
                        <a href="{{ route('movies.index') }}" class="btn btn-sm btn-outline-secondary">
                            <i class="fas fa-redo"></i> Xóa bộ lọc
                        </a>
                    </div>
                </div>
            </form>

            <div class="category-filter mt-3">
                <div class="category-tags">
                    <a href="{{ route('movies.index') }}"
                        class="category-tag {{ (!isset($category_id) || !$category_id) ? 'active' : '' }}">
                        <i class="fas fa-th"></i> Tất cả
                    </a>
                    @foreach($categories as $cat)
                    <a href="{{ route('movies.category', $cat['id']) }}"
                        class="category-tag {{ (isset($category_id) && $category_id == $cat['id']) ? 'active' : '' }}">
                        {{ $cat['name'] }}
                    </a>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <h2 class="section-title">
            <i class="fas fa-video"></i>
            @if($search)
            Kết quả tìm kiếm: "{{ $search }}"
            @if(!empty($movies))
            <span class="badge bg-primary">{{ count($movies) }} phim</span>
            @endif
            @elseif(isset($category_id) && $category_id)
            @php $cat = collect($categories)->firstWhere('id', $category_id); @endphp
            Phim {{ $cat['name'] ?? '' }}
            @if(!empty($movies))
            <span class="badge bg-primary">{{ count($movies) }} phim</span>
            @endif
            @else
            Tất cả phim
            @if(!empty($movies))
            <span class="badge bg-primary">{{ count($movies) }} phim</span>
            @endif
            @endif
        </h2>

        @if($search && empty($movies))
        <div class="empty-state text-center py-5">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h4>Không tìm thấy phim nào</h4>
            <p class="text-muted">Không có kết quả phù hợp với từ khóa: "<strong>{{ $search }}</strong>"</p>
            <a href="{{ route('home') }}?route=movie/index" class="btn btn-primary mt-3">
                <i class="fas fa-redo"></i> Xem tất cả phim
            </a>
        </div>
        @elseif(empty($movies))
        <div class="empty-state text-center py-5">
            <i class="fas fa-film fa-3x text-muted mb-3"></i>
            <h4>Chưa có phim nào</h4>
            <p class="text-muted">Hiện tại chưa có phim phù hợp với bộ lọc của bạn</p>
        </div>
        @else
<<<<<<< HEAD
        <div class="movie-grid">
            @foreach($movies as $movie)
            <div class="movie-card">
                @php
                // Nếu phim chiếu rạp, link đến trang đặt vé; các phim còn lại qua introduce
                $movieUrl = ($movie['status'] === 'Chiếu rạp')
                ? route('booking.index', ['movie' => $movie['id']])
                : route('movies.introduce', $movie['id']);
                @endphp
                <a href="{{ $movieUrl }}">
                    <div class="movie-thumbnail">
                        @if($movie['thumbnail'])
                        <img src="{{ $movie['thumbnail'] }}" alt="{{ $movie['title'] }}">
                        @else
                        <div class="movie-placeholder"><i class="fas fa-film"></i></div>
                        @endif
                        <div class="movie-overlay">
                            @if($movie['status'] === 'Chiếu rạp')
                            <i class="fas fa-ticket-alt"></i>
                            @else
                            <i class="fas fa-play"></i>
                            @endif
                        </div>
                        @if(($movie['type'] ?? 'phimle') === 'phimbo')
                        <div class="movie-badge" title="Số tập">
                            {{ isset($movie['episode_count']) && $movie['episode_count'] > 0 ? $movie['episode_count'] . ' tập' : '? tập' }}
                        </div>
                        @else
                        <span class="movie-level">{{ $movie['level'] }}</span>
=======
            <div class="movie-grid">
                @foreach($movies as $movie)
                <div class="movie-card">
                    @php
                        // Nếu phim chiếu rạp, link đến trang đặt vé; nếu không thì xem phim online
                        $movieUrl = ($movie['status'] === 'Chiếu rạp') 
                            ? route('home') . '?route=booking/index&movie_id=' . $movie['id']
                            : route('home') . '?route=movie/watch&id=' . $movie['id'];
                    @endphp
                    <a href="{{ $movieUrl }}">
                        <div class="movie-thumbnail">
                            @if($movie['thumbnail'])
                                <img src="{{ $movie['thumbnail'] }}" alt="{{ $movie['title'] }}">
                            @else
                                <div class="movie-placeholder"><i class="fas fa-film"></i></div>
                            @endif
                            <div class="movie-overlay">
                                @if($movie['status'] === 'Chiếu rạp')
                                    <i class="fas fa-ticket-alt"></i>
                                @else
                                    <i class="fas fa-play"></i>
                                @endif
                            </div>
                            @if(($movie['type'] ?? 'phimle') === 'phimbo')
                                <div class="movie-badge" title="Số tập">
                                    {{ isset($movie['episode_count']) && $movie['episode_count'] > 0 ? $movie['episode_count'] . ' tập' : '? tập' }}
                                </div>
                            @else
                                <span class="movie-level">{{ $movie['level'] }}</span>
                            @endif
                        </div>
                    </a>
                    <div class="movie-info">
                        <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
                            <a href="{{ $movieUrl }}" style="flex:1;text-decoration:none;color:inherit;">
                                <h3>{{ $movie['title'] }}</h3>
                            </a>
                            @if(isset($user) && $user)
                            <button class="favorite-btn-inline {{ (isset($favorites) && in_array($movie['id'], $favorites)) ? 'active' : '' }}"
                                    data-movie-id="{{ $movie['id'] }}"
                                    onclick="event.preventDefault();event.stopPropagation();toggleFavorite(this,{{ $movie['id'] }});">
                                <i class="fas fa-heart"></i>
                            </button>
                            @endif
                        </div>
                        <p class="movie-meta">
                            <span><i class="fas fa-star"></i> {{ number_format($movie['rating'], 1) }}</span>
                            @if($movie['type'] === 'phimbo')
                                <span><i class="fas fa-tv"></i> Phim bộ</span>
                            @else
                                <span><i class="fas fa-clock"></i> {{ $movie['duration'] }} phút</span>
                            @endif
                        </p>
                        <p class="movie-category">
                            <span class="movie-type-badge">{{ ($movie['type'] ?? 'phimle') === 'phimbo' ? 'Phim bộ' : 'Phim lẻ' }}</span>
                            @if($movie['category_name'])
                                <span> • {{ $movie['category_name'] ?? 'Chưa phân loại' }}</span>
                            @endif
                        </p>
                        @if($movie['description'])
                            <p class="movie-description">{{ mb_substr($movie['description'], 0, 100) }}...</p>
                        @endif
                        @if(isset($movie['status']) && $movie['status'] === 'Chiếu rạp')
                            <div class="mt-2">
                                <a href="{{ route('home') }}?route=booking/index&movie={{ $movie['id'] }}"
                                   class="btn btn-primary btn-sm w-100"
                                   style="background:#e50914;border:none;padding:8px 16px;border-radius:6px;text-decoration:none;display:inline-block;text-align:center;color:white;font-weight:500;">
                                    <i class="fas fa-ticket-alt"></i> Đặt vé xem phim
                                </a>
                            </div>
>>>>>>> parent of 7cd8d5d (sửa form đăng ký, luồng + profile)
                        @endif
                    </div>
                </a>
                <div class="movie-info">
                    <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;">
                        <a href="{{ $movieUrl }}" style="flex:1;text-decoration:none;color:inherit;">
                            <h3>{{ $movie['title'] }}</h3>
                        </a>
                        @if(isset($user) && $user)
                        <button class="favorite-btn-inline {{ (isset($favorites) && in_array($movie['id'], $favorites)) ? 'active' : '' }}"
                            data-movie-id="{{ $movie['id'] }}"
                            onclick="event.preventDefault();event.stopPropagation();toggleFavorite(this,{{ $movie['id'] }});">
                            <i class="fas fa-heart"></i>
                        </button>
                        @endif
                    </div>
                    <p class="movie-meta">
                        <span><i class="fas fa-star"></i> {{ number_format($movie['rating'], 1) }}</span>
                        @if($movie['type'] === 'phimbo')
                        <span><i class="fas fa-tv"></i> Phim bộ</span>
                        @else
                        <span><i class="fas fa-clock"></i> {{ $movie['duration'] }} phút</span>
                        @endif
                    </p>
                    <p class="movie-category">
                        <span class="movie-type-badge">{{ ($movie['type'] ?? 'phimle') === 'phimbo' ? 'Phim bộ' : 'Phim lẻ' }}</span>
                        @if($movie['category_name'])
                        <span> • {{ $movie['category_name'] ?? 'Chưa phân loại' }}</span>
                        @endif
                    </p>
                    @if($movie['description'])
                    <p class="movie-description">{{ mb_substr($movie['description'], 0, 100) }}...</p>
                    @endif
                    @if(isset($movie['status']) && $movie['status'] === 'Chiếu rạp')
                    <div class="mt-2">
                        <a href="{{ route('booking.index', ['movie' => $movie['id']]) }}"
                            class="btn btn-primary btn-sm w-100"
                            style="background:#e50914;border:none;padding:8px 16px;border-radius:100px;text-decoration:none;display:inline-block;text-align:center;color:white;font-weight:500;">
                            <i class="fas fa-ticket-alt"></i> Đặt vé xem phim
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if(isset($totalPages) && $totalPages > 1)
        @php
        $queryParams = request()->except('page');
        $paginationUrl = route('home') . '?route=movie/index' . (!empty($queryParams) ? '&' . http_build_query($queryParams) : '');
        $startPage = max(1, $page - 2);
        $endPage = min($totalPages, $page + 2);
        @endphp
        <nav aria-label="Phân trang danh sách phim" class="mt-4">
            <ul class="pagination justify-content-center">
                <li class="page-item {{ $page <= 1 ? 'disabled' : '' }}">
                    <a class="page-link" href="{{ $page > 1 ? $paginationUrl . '&page=' . ($page - 1) : '#' }}">
                        <i class="fas fa-chevron-left"></i> Trước
                    </a>
                </li>

                @if($startPage > 1)
                <li class="page-item"><a class="page-link" href="{{ $paginationUrl }}&page=1">1</a></li>
                @if($startPage > 2)
                <li class="page-item disabled"><span class="page-link">...</span></li>
                @endif
                @endif

                @for($i = $startPage; $i <= $endPage; $i++)
                    <li class="page-item {{ $i == $page ? 'active' : '' }}">
                    <a class="page-link" href="{{ $paginationUrl }}&page={{ $i }}">{{ $i }}</a>
                    </li>
                    @endfor

                    @if($endPage < $totalPages)
                        @if($endPage < $totalPages - 1)
                        <li class="page-item disabled"><span class="page-link">...</span></li>
                        @endif
                        <li class="page-item"><a class="page-link" href="{{ $paginationUrl }}&page={{ $totalPages }}">{{ $totalPages }}</a></li>
                        @endif

                        <li class="page-item {{ $page >= $totalPages ? 'disabled' : '' }}">
                            <a class="page-link" href="{{ $page < $totalPages ? $paginationUrl . '&page=' . ($page + 1) : '#' }}">
                                Sau <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
            </ul>
            <div class="text-center mt-3 text-muted">
                <small>
                    Hiển thị {{ $offset + 1 }} - {{ min($offset + $perPage, $total) }}
                    trong tổng số {{ number_format($total) }} phim
                    (Trang {{ $page }}/{{ $totalPages }})
                </small>
            </div>
        </nav>
        @endif
        @endif
    </div>
</section>

@push('scripts')
<script>
    function toggleFavorite(btn, movieId) {
        @if(!isset($user) || !$user)
        if (confirm('Vui lòng đăng nhập để thêm vào yêu thích!')) {
            window.location.href = '?route=auth/login';
        }
        return;
        @endif

        btn.disabled = true;
        fetch('?route=movie/toggleFavorite', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: 'movie_id=' + movieId
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    btn.classList.toggle('active', data.favorite);
                } else {
                    alert(data.error || 'Có lỗi xảy ra');
                }
                btn.disabled = false;
            })
            .catch(() => {
                alert('Có lỗi xảy ra khi thêm vào yêu thích');
                btn.disabled = false;
            });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const filterSelects = document.querySelectorAll('.filter-options select');
        const searchForm = document.querySelector('.search-form');
        filterSelects.forEach(select => {
            let isFirstLoad = true;
            select.addEventListener('change', function() {
                if (!isFirstLoad && searchForm) searchForm.submit();
                isFirstLoad = false;
            });
        });
    });
</script>
@endpush

@endsection
