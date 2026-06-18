@extends('layouts.app')

@php
$title = $movie->title;
$averageRating = number_format($ratingStats['average'], 1);
$totalReviews = $ratingStats['count'];

// Tính thời lượng
$duration = $movie->duration ?? 0;
$hours = floor($duration / 60);
$minutes = $duration % 60;
$durationText = $hours > 0 ? "{$hours}h " : '';
$durationText .= $minutes > 0 ? "{$minutes}m" : '';
if (!$durationText) $durationText = 'N/A';

// Lấy background
$bgImage = !empty($movie->banner) ? $movie->banner : $movie->thumbnail;
@endphp

@section('content')
<style>
    .movie-intro-page {
        background: #141414;
        min-height: 100vh;
        color: #fff;
    }

    /* Hero Section */
    .intro-hero {
        position: relative;
        min-height: 600px;
        display: flex;
        align-items: center;
        padding: 120px 0 60px;
        overflow: hidden;
    }

    .intro-hero-bg {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-size: cover;
        background-position: center top;
        z-index: 0;
    }

    .intro-hero-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(to right, rgba(20, 20, 20, 1) 0%, rgba(20, 20, 20, 0.8) 50%, rgba(20, 20, 20, 0.4) 100%);
        z-index: 1;
    }

    .intro-hero-bottom {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 200px;
        background: linear-gradient(to top, #141414 0%, transparent 100%);
        z-index: 1;
    }

    .intro-hero-content {
        position: relative;
        z-index: 2;
        width: 100%;
    }

    /* Poster */
    .intro-poster-wrapper {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.6);
        background: #2a2a2a;
    }

    .intro-poster {
        width: 100%;
        aspect-ratio: 2/3;
        object-fit: cover;
        display: block;
    }

    .intro-poster-placeholder {
        width: 100%;
        aspect-ratio: 2/3;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #666;
        font-size: 3rem;
        background: #2a2a2a;
    }

    /* Movie Info */
    .intro-title {
        font-size: 2.8rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }

    .intro-meta-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .intro-tag {
        padding: 4px 12px;
        border-radius: 4px;
        font-size: 0.85rem;
        font-weight: 600;
    }

    .tag-rating {
        background: #e50914;
        color: #fff;
    }

    .tag-year,
    .tag-duration,
    .tag-language,
    .tag-quality {
        background: rgba(255, 255, 255, 0.1);
        color: #ccc;
        border: 1px solid rgba(255, 255, 255, 0.15);
    }

    .tag-age {
        background: #ff6b00;
        color: #fff;
    }

    .tag-type {
        background: #2196F3;
        color: #fff;
    }

    .tag-level {
        background: #ffc107;
        color: #000;
    }

    .intro-category {
        color: #e50914;
        font-weight: 500;
        margin-bottom: 1rem;
    }

    .intro-category a {
        color: #e50914;
        text-decoration: none;
    }

    .intro-category a:hover {
        color: #ff1f1f;
    }

    .intro-description {
        font-size: 1rem;
        line-height: 1.7;
        color: #ccc;
        margin-bottom: 1.5rem;
        max-width: 650px;
    }

    /* Action Buttons */
    .intro-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .btn-intro-watch {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 12px 32px;
        background: #e50914;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-size: 1.05rem;
        font-weight: 600;
        text-decoration: none;
        transition: background 0.3s;
    }

    .btn-intro-watch:hover {
        background: #ff1f1f;
        color: #fff;
    }

    .btn-intro-fav {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 12px 24px;
        background: rgba(255, 255, 255, 0.08);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 6px;
        font-size: 1rem;
        text-decoration: none;
        transition: all 0.3s;
    }

    .btn-intro-fav:hover {
        background: rgba(229, 9, 20, 0.2);
        border-color: #e50914;
    }

    .btn-intro-fav.active {
        background: rgba(229, 9, 20, 0.2);
        border-color: #e50914;
        color: #e50914;
    }

    /* Detail Info Table */
    .intro-detail-section {
        padding: 60px 0;
    }

    .intro-section-title {
        font-size: 1.6rem;
        font-weight: 600;
        margin-bottom: 1.5rem;
        position: relative;
        padding-left: 16px;
    }

    .intro-section-title::before {
        content: '';
        position: absolute;
        left: 0;
        top: 0;
        bottom: 0;
        width: 4px;
        background: #e50914;
        border-radius: 2px;
    }

    .intro-detail-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 1.2rem;
        margin-bottom: 2rem;
    }

    .intro-detail-item {
        padding: 1rem;
        background: rgba(255, 255, 255, 0.04);
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.06);
    }

    .intro-detail-label {
        font-size: 0.8rem;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.4rem;
    }

    .intro-detail-value {
        font-size: 0.95rem;
        color: #eee;
        font-weight: 500;
    }

    /* Trailer */
    .intro-trailer {
        margin: 2rem 0;
        border-radius: 12px;
        overflow: hidden;
        background: #000;
    }

    .intro-trailer video {
        width: 100%;
        max-height: 500px;
        display: block;
    }

    .intro-trailer-placeholder {
        aspect-ratio: 16/9;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        background: #1a1a1a;
        color: #666;
        gap: 1rem;
    }

    .intro-trailer-placeholder i {
        font-size: 3rem;
    }

    /* Episode List (for phim bộ) */
    .intro-episodes {
        margin: 2rem 0;
    }

    .episodes-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
    }

    .episode-card {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 8px;
        padding: 1rem;
        transition: all 0.3s;
        text-decoration: none;
        color: #fff;
    }

    .episode-card:hover {
        background: rgba(229, 9, 20, 0.1);
        border-color: #e50914;
        transform: translateY(-2px);
    }

    .episode-number {
        font-size: 0.75rem;
        color: #e50914;
        font-weight: 700;
        text-transform: uppercase;
        margin-bottom: 0.3rem;
    }

    .episode-title {
        font-size: 0.9rem;
        font-weight: 500;
        color: #ddd;
    }

    /* Rating Section */
    .intro-rating-section {
        margin: 2rem 0;
    }

    .rating-overview {
        display: flex;
        align-items: center;
        gap: 2rem;
        padding: 2rem;
        background: rgba(255, 255, 255, 0.04);
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.06);
        margin-bottom: 1.5rem;
    }

    .rating-average {
        text-align: center;
        min-width: 120px;
    }

    .rating-average-number {
        font-size: 3.5rem;
        font-weight: 700;
        color: #e50914;
        line-height: 1;
    }

    .rating-average-label {
        font-size: 0.9rem;
        color: #888;
        margin-top: 0.3rem;
    }

    .rating-bars {
        flex: 1;
    }

    .rating-bar-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.3rem;
    }

    .rating-bar-label {
        font-size: 0.8rem;
        color: #999;
        width: 20px;
        text-align: right;
    }

    .rating-bar-track {
        flex: 1;
        height: 6px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 3px;
        overflow: hidden;
    }

    .rating-bar-fill {
        height: 100%;
        background: #e50914;
        border-radius: 3px;
        transition: width 0.5s ease;
    }

    .rating-bar-count {
        font-size: 0.75rem;
        color: #888;
        width: 25px;
    }

    /* Reviews */
    .intro-reviews {
        margin: 2rem 0;
    }

    .review-card {
        padding: 1rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.06);
    }

    .review-card:last-child {
        border-bottom: none;
    }

    .review-user {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 0.5rem;
    }

    .review-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #333;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #999;
        font-size: 0.9rem;
        overflow: hidden;
    }

    .review-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .review-username {
        font-weight: 600;
        font-size: 0.9rem;
    }

    .review-stars {
        color: #ffc107;
        font-size: 0.85rem;
    }

    .review-comment {
        color: #bbb;
        font-size: 0.9rem;
        line-height: 1.5;
        margin-left: 3.25rem;
    }

    /* Related Movies */
    .intro-related {
        padding: 40px 0 60px;
    }

    .related-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 1.2rem;
    }

    .related-item {
        text-decoration: none;
        color: #fff;
        border-radius: 8px;
        overflow: hidden;
        background: #1a1a1a;
        transition: transform 0.3s;
    }

    .related-item:hover {
        transform: translateY(-6px);
    }

    .related-img {
        width: 100%;
        aspect-ratio: 2/3;
        object-fit: cover;
        display: block;
    }

    .related-img-placeholder {
        width: 100%;
        aspect-ratio: 2/3;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #2a2a2a;
        color: #555;
        font-size: 2rem;
    }

    .related-info {
        padding: 0.6rem;
    }

    .related-title {
        font-size: 0.85rem;
        font-weight: 500;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .related-rating {
        font-size: 0.75rem;
        color: #999;
        margin-top: 0.2rem;
    }

    @media (max-width: 768px) {
        .intro-hero {
            min-height: auto;
            padding: 100px 0 40px;
        }

        .intro-title {
            font-size: 1.8rem;
        }

        .intro-poster-wrapper {
            max-width: 250px;
            margin: 0 auto 2rem;
        }

        .intro-meta-tags {
            gap: 0.5rem;
        }

        .rating-overview {
            flex-direction: column;
            gap: 1rem;
        }

        .related-grid {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
        }
    }
</style>

<div class="movie-intro-page">
    <!-- Hero Section -->
    <section class="intro-hero">
        @if ($bgImage)
        <div class="intro-hero-bg" style="background-image: url('{{ $bgImage }}');"></div>
        @endif
        <div class="intro-hero-overlay"></div>
        <div class="intro-hero-bottom"></div>

        <div class="container">
            <div class="intro-hero-content">
                <div class="row">
                    <div class="col-lg-3 col-md-4 mb-4 mb-md-0">
                        <div class="intro-poster-wrapper">
                            @if ($movie->thumbnail)
                            <img src="{{ $movie->thumbnail }}" alt="{{ $movie->title }}" class="intro-poster">
                            @else
                            <div class="intro-poster-placeholder">
                                <i class="fas fa-image"></i>
                            </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-lg-9 col-md-8">
                        <h1 class="intro-title">{{ $movie->title }}</h1>

                        @if ($movie->description)
                        <p class="intro-description">{{ Str::limit($movie->description, 300) }}</p>
                        @endif

                        <div class="intro-meta-tags">
                            @if ($averageRating > 0)
                            <span class="intro-tag tag-rating">
                                <i class="fas fa-star" style="font-size:0.7rem;"></i> {{ $averageRating }}
                            </span>
                            @endif
                            @if ($movie->publish_date)
                            <span class="intro-tag tag-year">{{ date('Y', strtotime($movie->publish_date)) }}</span>
                            @endif
                            <span class="intro-tag tag-duration">
                                <i class="far fa-clock"></i> {{ $durationText }}
                            </span>
                            @if ($movie->age_rating)
                            <span class="intro-tag tag-age">{{ $movie->age_rating }}</span>
                            @endif
                            @if ($movie->language)
                            <span class="intro-tag tag-language">{{ $movie->language }}</span>
                            @endif
                            <span class="intro-tag tag-type">
                                {{ ($movie->type ?? 'phimle') === 'phimbo' ? 'Phim bộ' : 'Phim lẻ' }}
                            </span>
                            @if ($movie->level && $movie->level !== 'Free')
                            <span class="intro-tag tag-level">{{ $movie->level }}</span>
                            @endif
                        </div>

                        @if ($movie->category)
                        <div class="intro-category">
                            <i class="fas fa-tag"></i>
                            <a href="{{ route('movies.category', $movie->category->id) }}">{{ $movie->category->name }}</a>
                        </div>
                        @endif

                        <div class="intro-actions">
                            @if ($movie->isPhimBo())
                            <a href="{{ route('movies.watchEpisode', [$movie->id, 1]) }}" class="btn-intro-watch">
                                <i class="fas fa-play"></i> Xem ngay
                            </a>
                            @else
                            <a href="{{ route('movies.watch', $movie->id) }}" class="btn-intro-watch">
                                <i class="fas fa-play"></i> Xem ngay
                            </a>
                            @endif

                            @if ($movie->hasTrailer())
                            <a href="{{ $movie->trailer_url_full }}" target="_blank" class="btn-intro-fav">
                                <i class="fas fa-video"></i> Trailer
                            </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Detail Info -->
    <section class="intro-detail-section">
        <div class="container">
            <!-- Cast & Crew -->
            <h2 class="intro-section-title">Thông tin phim</h2>
            <div class="intro-detail-grid">
                @if ($movie->director)
                <div class="intro-detail-item">
                    <div class="intro-detail-label">Đạo diễn</div>
                    <div class="intro-detail-value">{{ $movie->director }}</div>
                </div>
                @endif

                @if ($movie->country)
                <div class="intro-detail-item">
                    <div class="intro-detail-label">Quốc gia</div>
                    <div class="intro-detail-value">{{ $movie->country }}</div>
                </div>
                @endif

                @if ($movie->language)
                <div class="intro-detail-item">
                    <div class="intro-detail-label">Ngôn ngữ</div>
                    <div class="intro-detail-value">{{ $movie->language }}</div>
                </div>
                @endif

                <div class="intro-detail-item">
                    <div class="intro-detail-label">Thể loại</div>
                    <div class="intro-detail-value">{{ $movie->category->name ?? 'N/A' }}</div>
                </div>

                <div class="intro-detail-item">
                    <div class="intro-detail-label">Thời lượng</div>
                    <div class="intro-detail-value">{{ $durationText }}</div>
                </div>

                @if ($movie->publish_date)
                <div class="intro-detail-item">
                    <div class="intro-detail-label">Năm phát hành</div>
                    <div class="intro-detail-value">{{ date('Y', strtotime($movie->publish_date)) }}</div>
                </div>
                @endif
            </div>

            @if ($movie->cast || $movie->actors)
            <div class="intro-detail-item" style="margin-bottom:1.5rem;">
                <div class="intro-detail-label">Diễn viên</div>
                <div class="intro-detail-value">{{ $movie->cast ?? $movie->actors }}</div>
            </div>
            @endif

            <!-- Full Description -->
            @if ($movie->description && strlen($movie->description) > 300)
            <div class="intro-full-description" style="background:rgba(255,255,255,0.04); border-radius:8px; padding:1.5rem; margin-bottom:2rem; border:1px solid rgba(255,255,255,0.06);">
                <h3 style="font-size:1rem; color:#e50914; margin-bottom:0.8rem;">Nội dung phim</h3>
                <p style="color:#bbb; line-height:1.7; margin:0;">{{ $movie->description }}</p>
            </div>
            @endif

            <!-- Trailer -->
            @if ($movie->hasTrailer())
            <h2 class="intro-section-title">Trailer</h2>
            <div class="intro-trailer">
                <video controls preload="metadata" poster="{{ $movie->thumbnail }}">
                    <source src="{{ $movie->trailer_url_full }}" type="video/mp4">
                    Trình duyệt của bạn không hỗ trợ video.
                </video>
            </div>
            @endif

            <!-- Episodes (phim bộ) -->
            @if ($movie->isPhimBo() && $movie->episodes->isNotEmpty())
            <h2 class="intro-section-title">
                Danh sách tập
                <span style="font-size:0.9rem; color:#888; font-weight:400;">
                    ({{ $movie->episodes->count() }} tập)
                </span>
            </h2>
            <div class="intro-episodes">
                <div class="episodes-grid">
                    @foreach ($movie->episodes as $episode)
                    <a href="{{ route('movies.watch', ['id' => $movie->id, 'episode_id' => $episode->id]) }}" class="episode-card">
                        <div class="episode-number">Tập {{ $episode->episode_number }}</div>
                        <div class="episode-title">{{ $episode->title ?: 'Tập ' . $episode->episode_number }}</div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif

            <!-- Rating & Reviews -->
            <h2 class="intro-section-title">
                Đánh giá
                @if ($totalReviews > 0)
                <span style="font-size:0.9rem; color:#888; font-weight:400;">({{ $totalReviews }} đánh giá)</span>
                @endif
            </h2>
            <div class="intro-rating-section">
                <div class="rating-overview">
                    <div class="rating-average">
                        <div class="rating-average-number">
                            @if ($averageRating > 0)
                            {{ $averageRating }}
                            @else
                            --
                            @endif
                        </div>
                        <div class="rating-average-label">
                            @if ($totalReviews > 0)
                            /10 ({{ $totalReviews }} đánh giá)
                            @else
                            Chưa có đánh giá
                            @endif
                        </div>
                    </div>

                    @if ($totalReviews > 0)
                    <div class="rating-bars">
                        @for ($star = 10; $star >= 1; $star--)
                        @php
                        $count = $ratingStats['distribution'][$star] ?? 0;
                        $percent = $totalReviews > 0 ? ($count / $totalReviews) * 100 : 0;
                        @endphp
                        <div class="rating-bar-item">
                            <span class="rating-bar-label">{{ $star }}</span>
                            <div class="rating-bar-track">
                                <div class="rating-bar-fill" style="width: {{ $percent }}%;"></div>
                            </div>
                            <span class="rating-bar-count">{{ $count }}</span>
                        </div>
                        @endfor
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Related Movies -->
    @if ($relatedMovies->isNotEmpty())
    <section class="intro-related" style="background: #111;">
        <div class="container">
            <h2 class="intro-section-title">Phim liên quan</h2>
            <div class="related-grid">
                @foreach ($relatedMovies as $related)
                <a href="{{ route('movies.introduce', $related->id) }}" class="related-item">
                    @if ($related->thumbnail)
                    <img src="{{ $related->thumbnail }}" alt="{{ $related->title }}" class="related-img">
                    @else
                    <div class="related-img-placeholder">
                        <i class="fas fa-image"></i>
                    </div>
                    @endif
                    <div class="related-info">
                        <div class="related-title">{{ $related->title }}</div>
                        @if ($related->rating)
                        <div class="related-rating">
                            <i class="fas fa-star" style="color:#ffc107; font-size:0.7rem;"></i>
                            {{ number_format($related->rating, 1) }}
                        </div>
                        @endif
                    </div>
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif
</div>
@endsection
