@extends('layouts.app')

@section('content')
<section class="section">
    <br>
    <div class="container">
        <div class="filter-bar">
            <form method="GET" class="search-form" action="{{ route('movies.index') }}">
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
                                <option value="{{ $cat['id'] }}" {{ (isset($categoryId) && $categoryId == $cat['id']) ? 'selected' : '' }}>
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
                                <option value="{{ $val }}" {{ (isset($minRating) && $minRating == $val) ? 'selected' : '' }}>{{ $label }} ⭐</option>
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
                        class="category-tag {{ (!isset($categoryId) || !$categoryId) ? 'active' : '' }}">
                        <i class="fas fa-th"></i> Tất cả
                    </a>
                    @foreach($categories as $cat)
                    <a href="{{ route('movies.category', $cat['id']) }}"
                        class="category-tag {{ (isset($categoryId) && $categoryId == $cat['id']) ? 'active' : '' }}">
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
            @if(!$movies->isEmpty())
            <span class="badge bg-primary">{{ count($movies) }} phim</span>
            @endif
            @elseif(isset($audienceTitle) && $audienceTitle)
            Kho phim của tôi: {{ $audienceTitle }}
            @if(!$movies->isEmpty())
            <span class="badge bg-primary">{{ count($movies) }} phim</span>
            @endif
            @elseif(isset($categoryId) && $categoryId)
            @php $cat = collect($categories)->firstWhere('id', $categoryId); @endphp
            Phim {{ $cat['name'] ?? '' }}
            @if(!$movies->isEmpty())
            <span class="badge bg-primary">{{ count($movies) }} phim</span>
            @endif
            @else
            Tất cả phim
            @if(!$movies->isEmpty())
            <span class="badge bg-primary">{{ count($movies) }} phim</span>
            @endif
            @endif
        </h2>

        @if($search && $movies->isEmpty())
        <div class="empty-state text-center py-5">
            <i class="fas fa-search fa-3x text-muted mb-3"></i>
            <h4>Không tìm thấy phim nào</h4>
            <p class="text-muted">Không có kết quả phù hợp với từ khóa: "<strong>{{ $search }}</strong>"</p>
            <a href="{{ route('movies.index') }}" class="btn btn-primary mt-3">
                <i class="fas fa-redo"></i> Xem tất cả phim
            </a>
        </div>
        @elseif($movies->isEmpty())
        <div class="empty-state text-center py-5">
            <i class="fas fa-film fa-3x text-muted mb-3"></i>
            <h4>Chưa có phim nào</h4>
            <p class="text-muted">Hiện tại chưa có phim phù hợp với bộ lọc của bạn</p>
        </div>
        @else
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
                        @php
                            $movieCategoryNames = $movie->categories->pluck('name');
                            if ($movieCategoryNames->isEmpty() && $movie->category) {
                                $movieCategoryNames = collect([$movie->category->name]);
                            }
                        @endphp
                        @if($movieCategoryNames->isNotEmpty())
                        <span> • {{ $movieCategoryNames->join(', ') }}</span>
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
        @if($movies->hasPages())
        <nav aria-label="Phân trang danh sách phim" class="movie-pagination mt-4">
            {{ $movies->withQueryString()->links('pagination::bootstrap-4') }}
            <div class="text-center mt-3 text-muted">
                <small>
                    Hiển thị {{ $movies->firstItem() }} - {{ $movies->lastItem() }}
                    trong tổng số {{ number_format($movies->total()) }} phim
                    (Trang {{ $movies->currentPage() }}/{{ $movies->lastPage() }})
                </small>
            </div>
        </nav>
        @endif
        @endif
    </div>
</section>

<style>
    .movie-pagination {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 8px;
        margin-top: 24px !important;
    }

    .movie-pagination .pagination {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .movie-pagination .page-link {
        min-width: 34px;
        height: 34px;
        padding: 0 10px;
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.14);
        background: rgba(255, 255, 255, 0.06);
        color: #f5f5f5;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        line-height: 1;
        text-decoration: none;
    }

    .movie-pagination .page-link:hover {
        background: rgba(229, 9, 20, 0.24);
        border-color: rgba(229, 9, 20, 0.5);
        color: #fff;
    }

    .movie-pagination .page-item.active .page-link {
        background: #e50914;
        border-color: #e50914;
        color: #fff;
    }

    .movie-pagination .page-item.disabled .page-link {
        opacity: 0.45;
        cursor: default;
    }

    .movie-pagination svg {
        width: 14px !important;
        height: 14px !important;
    }
</style>

@push('scripts')
<script>
    function toggleFavorite(btn, movieId) {
        @if(!isset($user) || !$user)
        if (confirm('Vui lòng đăng nhập để thêm vào yêu thích!')) {
            window.location.href = '{{ route('login') }}';
        }
        return;
        @endif

        btn.disabled = true;
        fetch('{{ route('movies.toggleFavorite') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: new URLSearchParams({ movie_id: movieId })
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
