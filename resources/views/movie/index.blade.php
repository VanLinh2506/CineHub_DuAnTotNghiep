@extends('layouts.app')

@section('content')
@php
    $featuredMovie = !empty($isUpcoming)
        ? $movies->getCollection()->first()
        : $movies->getCollection()->sortByDesc(fn($movie) => $movie->rating ?? 0)->first();
    $contextTitle = 'Tất cả phim';

    if (!empty($isUpcoming)) {
        $contextTitle = 'Phim sắp chiếu';
    } elseif (!empty($search)) {
        $contextTitle = 'Kết quả tìm kiếm';
    } elseif (!empty($audienceTitle)) {
        $contextTitle = 'Kho phim ' . $audienceTitle;
    } elseif (!empty($categoryId)) {
        $contextCategory = collect($categories)->firstWhere('id', $categoryId);
        $contextTitle = 'Phim ' . ($contextCategory['name'] ?? '');
    } elseif (!empty($type) && $type === 'phimle') {
        $contextTitle = 'Phim lẻ';
    } elseif (!empty($type) && $type === 'phimbo') {
        $contextTitle = 'Phim bộ';
    }
@endphp

@if($featuredMovie)
<section class="movie-topic-hero">
    <div class="container">
        <article class="movie-topic-banner">
            <div class="movie-topic-backdrop" style="background-image: url('{{ $featuredMovie->thumbnail }}');"></div>
            <div class="movie-topic-content">
                <span class="movie-topic-eyebrow">
                    <i class="fas fa-fire"></i> Nổi bật trong {{ $contextTitle }}
                </span>
                <h1>{{ $featuredMovie->title }}</h1>
                <p>{{ \Illuminate\Support\Str::limit(strip_tags($featuredMovie->description ?? 'Bộ phim đang được quan tâm trong chủ đề này.'), 150) }}</p>
                <div class="movie-topic-meta">
                    @if($featuredMovie->rating)
                    <span><i class="fas fa-star"></i> {{ number_format($featuredMovie->rating, 1) }}/10</span>
                    @endif
                    <span>{{ ($featuredMovie->type ?? 'phimle') === 'phimbo' ? 'Phim bộ' : 'Phim lẻ' }}</span>
                    @if($featuredMovie->country)
                    <span>{{ $featuredMovie->country }}</span>
                    @endif
                </div>
                <div class="movie-topic-actions">
                    <a href="{{ route('movies.introduce', $featuredMovie->id) }}" class="btn-topic-primary">
                        <i class="fas fa-play"></i> Xem chi tiết
                    </a>
                    <a href="{{ route('movies.index') }}" class="btn-topic-secondary">
                        Tất cả phim
                    </a>
                </div>
            </div>
            <a href="{{ route('movies.introduce', $featuredMovie->id) }}" class="movie-topic-poster">
                @if($featuredMovie->thumbnail)
                <img src="{{ $featuredMovie->thumbnail }}" alt="{{ $featuredMovie->title }}">
                @else
                <div><i class="fas fa-film"></i></div>
                @endif
            </a>
        </article>
    </div>
</section>
@endif

<section class="section">
    <div class="container">
        @if(($type ?? null) === 'phimbo' && !empty($continueWatchingSeries) && $continueWatchingSeries->isNotEmpty())
        <section class="series-resume-section">
            <div class="series-resume-heading">
                <div>
                    <span>Dành riêng cho bạn</span>
                    <h2><i class="fas fa-history"></i> Đang xem</h2>
                </div>
                <small>Lưu vị trí xem trong 30 ngày</small>
            </div>
            <div class="series-resume-list">
                @foreach($continueWatchingSeries as $history)
                    @php
                        $resumeMovie = $history->movie;
                        $resumeEpisode = $history->episode;
                        $highestWatchedEpisode = $watchProgressByMovie->get($history->movie_id)?->episode?->episode_number
                            ?? $resumeEpisode?->episode_number;
                        $resumeMaxEpisode = (int) ($resumeMovie->episodes_count ?? $resumeMovie->total_episodes ?? 0);
                        $resumeMinute = max(1, (int) floor($history->last_time / 60));
                    @endphp
                    <a class="series-resume-card" href="{{ route('movies.watch', array_filter(['id' => $history->movie_id, 'episode_id' => $history->episode_id])) }}">
                        <div class="series-resume-poster">
                            <img src="{{ $resumeMovie->thumbnail }}" alt="{{ $resumeMovie->title }}" loading="lazy">
                            <span><i class="fas fa-play"></i></span>
                            @if($highestWatchedEpisode)
                            <b>{{ $highestWatchedEpisode }}/{{ $resumeMaxEpisode }} tập</b>
                            @endif
                        </div>
                        <strong>{{ $resumeMovie->title }}</strong>
                        <small>
                            @if($resumeEpisode)Tập {{ $resumeEpisode->episode_number }} · @endif
                            tiếp tục từ phút {{ $resumeMinute }}
                        </small>
                    </a>
                @endforeach
            </div>
        </section>
        @endif

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
            @continue(empty($isUpcoming) && ($movie['status'] ?? null) !== 'Chiếu online')
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
                            <span class="movie-play-control">
                                @if($movie['status'] === 'Chiếu rạp')
                                <i class="fas fa-ticket-alt"></i>
                                @else
                                <i class="fas fa-play"></i>
                                @endif
                            </span>
                        </div>
                        @if($movie['rating'])
                        <span class="movie-rating-badge">
                            <i class="fas fa-star"></i> {{ number_format($movie['rating'], 1) }}
                        </span>
                        @endif
                        @if(!empty($isUpcoming) && $movie->publish_date)
                        <span class="upcoming-date-badge">
                            <i class="far fa-calendar-alt"></i> {{ $movie->publish_date->format('d/m/Y H:i') }}
                        </span>
                        @endif
                        @if(($movie['status'] ?? null) !== 'Chiếu rạp')
                        <span class="movie-level">{{ $movie['level'] ?? 'Free' }}</span>
                        @endif
                        @if(($movie['type'] ?? 'phimle') === 'phimbo')
                        @php
                            $movieProgress = isset($watchProgressByMovie) ? $watchProgressByMovie->get($movie['id']) : null;
                            $watchedEpisode = $movieProgress?->episode?->episode_number;
                            $maximumEpisode = (int) ($movie['episode_count'] ?? $movie['total_episodes'] ?? 0);
                        @endphp
                        <div class="movie-badge" title="Tiến độ tập phim">
                            {{ $watchedEpisode ? $watchedEpisode . '/' : '' }}{{ $maximumEpisode }} tập
                        </div>
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
                    @if(!empty($isUpcoming))
                    @php $alreadyInterested = in_array($movie['id'], $interestedMovieIds ?? []); @endphp
                    <button type="button"
                        class="interest-button {{ $alreadyInterested ? 'interested' : '' }}"
                        data-url="{{ route('movies.interest', $movie['id']) }}"
                        onclick="markInterested(this)"
                        {{ $alreadyInterested ? 'disabled' : '' }}>
                        <i class="{{ $alreadyInterested ? 'fas' : 'far' }} fa-bell"></i>
                        <span>{{ $alreadyInterested ? 'Đã quan tâm' : 'Quan tâm' }}</span>
                        <b>{{ (int) ($movie->interests_count ?? 0) }}</b>
                    </button>
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
        @if(($type ?? null) === 'phimle' && empty($isUpcoming))
            @include('components.upcoming-movies-strip')
        @endif
    </div>
</section>

<style>
    .movie-grid {
        grid-template-columns: repeat(auto-fill, minmax(176px, 1fr));
        gap: 22px;
        align-items: stretch;
    }

    .movie-card {
        position: relative;
        overflow: hidden;
        border-radius: 18px;
        border: 1px solid rgba(255, 255, 255, 0.08);
        background: #151515;
        box-shadow: 0 14px 28px rgba(0, 0, 0, 0.22);
        transition: transform 0.22s ease, border-color 0.22s ease, box-shadow 0.22s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .movie-card:hover {
        transform: translateY(-6px);
        border-color: rgba(229, 9, 20, 0.42);
        box-shadow: 0 22px 40px rgba(0, 0, 0, 0.34);
    }

    .movie-thumbnail {
        aspect-ratio: 2 / 3;
        padding-top: 0;
        background: #202020;
    }

    .movie-thumbnail::after {
        content: "";
        position: absolute;
        inset: auto 0 0;
        height: 42%;
        background: linear-gradient(180deg, transparent, rgba(0, 0, 0, 0.72));
        pointer-events: none;
        z-index: 1;
    }

    .movie-thumbnail img {
        position: static;
        display: block;
        transition: transform 0.34s ease, filter 0.34s ease;
    }

    .movie-card:hover .movie-thumbnail img {
        transform: scale(1.035);
        filter: saturate(1.06) contrast(1.03);
    }

    .movie-overlay {
        background: rgba(0, 0, 0, 0.28);
        backdrop-filter: blur(1px);
        z-index: 3;
    }

    .movie-play-control {
        width: 54px;
        height: 54px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #e50914;
        color: #fff;
        box-shadow: 0 12px 24px rgba(229, 9, 20, 0.34);
        transform: scale(0.88);
        transition: transform 0.22s ease, background 0.22s ease;
    }

    .movie-card:hover .movie-play-control {
        transform: scale(1);
    }

    .movie-play-control i {
        font-size: 20px;
        margin-left: 2px;
    }

    .movie-rating-badge,
    .movie-level,
    .movie-badge {
        position: absolute;
        z-index: 4;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        min-height: 28px;
        padding: 0 9px;
        border-radius: 999px;
        font-size: 12px;
        font-weight: 800;
        line-height: 1;
        color: #fff;
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.26);
    }

    .movie-rating-badge {
        right: 10px;
        bottom: 10px;
        background: rgba(229, 9, 20, 0.94);
    }

    .movie-rating-badge i {
        color: #ffd166;
        font-size: 11px;
    }

    .movie-level {
        top: 10px;
        left: 10px;
        right: auto;
        background: rgba(15, 15, 15, 0.78);
        border: 1px solid rgba(255, 255, 255, 0.14);
        backdrop-filter: blur(8px);
    }

    .movie-badge {
        top: 10px;
        right: 10px;
        left: auto;
        background: rgba(15, 15, 15, 0.78);
        border: 1px solid rgba(255, 255, 255, 0.14);
        backdrop-filter: blur(8px);
    }

    .movie-info {
        padding: 13px 14px 15px;
        display: flex;
        flex: 1;
        flex-direction: column;
    }

    .movie-info h3 {
        margin: 0;
        color: #fff;
        font-size: 15px;
        line-height: 1.35;
        white-space: normal;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        min-height: 40px;
    }

    .movie-meta {
        gap: 10px;
        margin: 9px 0 0;
        color: rgba(255, 255, 255, 0.68);
        font-size: 13px;
    }

    .movie-meta span:first-child {
        display: none;
    }

    .movie-category {
        margin: 8px 0 0;
        color: rgba(255, 255, 255, 0.62);
        font-size: 12px;
        line-height: 1.45;
        min-height: 42px;
        display: -webkit-box;
        overflow: hidden;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
    }

    .movie-type-badge {
        color: #ffdadc;
        background: rgba(229, 9, 20, 0.15);
        border: 1px solid rgba(229, 9, 20, 0.26);
        padding: 3px 8px;
        border-radius: 999px;
        font-weight: 700;
    }

    .movie-description {
        display: none;
    }

    .favorite-btn-inline {
        top: 12px;
        right: 12px;
        left: auto;
        width: 34px;
        height: 34px;
        background: rgba(10, 10, 10, 0.62);
        border: 1px solid rgba(255, 255, 255, 0.18);
        color: rgba(255, 255, 255, 0.76);
        backdrop-filter: blur(8px);
    }

    @media (max-width: 576px) {
        .movie-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 14px;
        }

        .movie-info {
            padding: 11px;
        }

        .movie-info h3 {
            font-size: 14px;
        }
    }

    .movie-topic-hero {
        padding: 96px 0 12px;
        background:
            radial-gradient(circle at 18% 0%, rgba(229, 9, 20, 0.22), transparent 34%),
            linear-gradient(180deg, rgba(255, 255, 255, 0.04), transparent);
    }

    .movie-topic-banner {
        position: relative;
        display: grid;
        grid-template-columns: minmax(0, 1fr) 170px;
        gap: 22px;
        min-height: 260px;
        overflow: hidden;
        border-radius: 18px;
        border: 1px solid rgba(255, 255, 255, 0.12);
        background: #141414;
        box-shadow: 0 18px 42px rgba(0, 0, 0, 0.28);
        isolation: isolate;
    }

    .movie-topic-backdrop {
        position: absolute;
        inset: 0;
        background-size: cover;
        background-position: center;
        opacity: 0.36;
        filter: saturate(1.1);
        z-index: -2;
    }

    .movie-topic-banner::before {
        content: "";
        position: absolute;
        inset: 0;
        background:
            linear-gradient(90deg, rgba(8, 8, 8, 0.96) 0%, rgba(15, 15, 15, 0.78) 48%, rgba(15, 15, 15, 0.32) 100%),
            linear-gradient(0deg, rgba(0, 0, 0, 0.36), transparent 58%);
        z-index: -1;
    }

    .movie-topic-content {
        display: flex;
        flex-direction: column;
        justify-content: center;
        gap: 12px;
        padding: 28px 0 28px 32px;
        color: #fff;
    }

    .movie-topic-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        width: fit-content;
        padding: 7px 12px;
        border-radius: 999px;
        background: rgba(229, 9, 20, 0.16);
        color: #ffdbdf;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
    }

    .movie-topic-content h1 {
        max-width: 680px;
        margin: 0;
        color: #fff;
        font-size: clamp(28px, 4vw, 46px);
        line-height: 1.05;
        font-weight: 800;
    }

    .movie-topic-content p {
        max-width: 690px;
        margin: 0;
        color: rgba(255, 255, 255, 0.78);
        font-size: 15px;
        line-height: 1.6;
    }

    .movie-topic-meta,
    .movie-topic-actions {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 10px;
    }

    .movie-topic-meta span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 10px;
        border-radius: 999px;
        background: rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.88);
        font-size: 13px;
        font-weight: 600;
    }

    .movie-topic-meta i {
        color: #ffd166;
    }

    .btn-topic-primary,
    .btn-topic-secondary {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        min-height: 42px;
        padding: 0 18px;
        border-radius: 999px;
        font-weight: 700;
        text-decoration: none;
        transition: transform 0.18s ease, background 0.18s ease, border-color 0.18s ease;
    }

    .btn-topic-primary {
        background: #e50914;
        color: #fff;
        border: 1px solid #e50914;
    }

    .btn-topic-secondary {
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.18);
    }

    .btn-topic-primary:hover,
    .btn-topic-secondary:hover {
        color: #fff;
        transform: translateY(-1px);
    }

    .btn-topic-secondary:hover {
        background: rgba(255, 255, 255, 0.14);
        border-color: rgba(255, 255, 255, 0.32);
    }

    .movie-topic-poster {
        align-self: center;
        justify-self: end;
        width: 150px;
        aspect-ratio: 2 / 3;
        margin-right: 28px;
        overflow: hidden;
        border-radius: 14px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        background: rgba(255, 255, 255, 0.08);
        box-shadow: 0 16px 28px rgba(0, 0, 0, 0.36);
    }

    .movie-topic-poster img,
    .movie-topic-poster div {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255, 255, 255, 0.65);
        font-size: 38px;
    }

    @media (max-width: 768px) {
        .movie-topic-hero {
            padding-top: 82px;
        }

        .movie-topic-banner {
            grid-template-columns: 1fr;
            min-height: auto;
        }

        .movie-topic-content {
            padding: 24px;
        }

        .movie-topic-poster {
            display: none;
        }
    }

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

    .series-resume-section { margin: 0 0 34px; padding: 22px; border: 1px solid rgba(255,255,255,.09); border-radius: 20px; background: linear-gradient(135deg,rgba(229,9,20,.12),rgba(18,18,18,.96) 42%); }
    .series-resume-heading { display:flex; align-items:end; justify-content:space-between; gap:18px; margin-bottom:18px; }
    .series-resume-heading span { color:#ff6971; font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.1em; }
    .series-resume-heading h2 { margin:4px 0 0; color:#fff; font-size:24px; }
    .series-resume-heading small { color:rgba(255,255,255,.56); }
    .series-resume-list { display:grid; grid-auto-flow:column; grid-auto-columns:minmax(190px,230px); gap:15px; overflow-x:auto; padding-bottom:5px; scrollbar-width:thin; }
    .series-resume-card { display:grid; gap:6px; color:#fff; text-decoration:none; min-width:0; }
    .series-resume-poster { position:relative; aspect-ratio:16/9; overflow:hidden; border-radius:13px; background:#222; }
    .series-resume-poster::after { content:""; position:absolute; inset:40% 0 0; background:linear-gradient(transparent,rgba(0,0,0,.85)); }
    .series-resume-poster img { width:100%; height:100%; object-fit:cover; transition:transform .25s ease; }
    .series-resume-card:hover img { transform:scale(1.04); }
    .series-resume-poster span { position:absolute; z-index:2; inset:0; margin:auto; width:42px; height:42px; display:grid; place-items:center; border-radius:50%; background:#e50914; box-shadow:0 8px 24px rgba(229,9,20,.4); }
    .series-resume-poster b { position:absolute; z-index:2; right:9px; bottom:8px; padding:5px 8px; border-radius:999px; background:rgba(0,0,0,.78); font-size:11px; }
    .series-resume-card > strong { overflow:hidden; text-overflow:ellipsis; white-space:nowrap; font-size:14px; }
    .series-resume-card > small { color:rgba(255,255,255,.62); }
    .upcoming-date-badge { position:absolute; z-index:4; left:10px; bottom:10px; padding:7px 9px; border-radius:999px; background:rgba(15,15,15,.9); color:#fff; font-size:11px; font-weight:800; backdrop-filter:blur(8px); }
    .interest-button { width:100%; margin-top:12px; padding:10px 14px; display:flex; align-items:center; justify-content:center; gap:8px; border:1px solid #e50914; border-radius:999px; background:#e50914; color:#fff; font-weight:800; cursor:pointer; }
    .interest-button b { min-width:24px; padding:2px 6px; border-radius:999px; background:rgba(0,0,0,.22); font-size:11px; }
    .interest-button.interested { border-color:rgba(255,255,255,.16); background:rgba(255,255,255,.08); color:rgba(255,255,255,.72); cursor:default; }

    @media (max-width: 576px) {
        .series-resume-section { padding:16px; }
        .series-resume-heading { align-items:start; flex-direction:column; gap:5px; }
        .series-resume-list { grid-auto-columns:78%; }
    }
</style>

@push('scripts')
<script>
    function markInterested(button) {
        @guest
        window.location.href = @json(route('login'));
        return;
        @endguest

        if (button.disabled) return;
        button.disabled = true;
        fetch(button.dataset.url, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': @json(csrf_token())
            }
        })
        .then(response => response.ok ? response.json() : Promise.reject())
        .then(data => {
            button.classList.add('interested');
            button.querySelector('i').className = 'fas fa-bell';
            button.querySelector('span').textContent = 'Đã quan tâm';
            button.querySelector('b').textContent = data.count;
        })
        .catch(() => {
            button.disabled = false;
            alert('Không thể lưu quan tâm lúc này.');
        });
    }

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

</script>
@endpush

@endsection
