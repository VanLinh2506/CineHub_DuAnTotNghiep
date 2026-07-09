@php
    $movieUrl = route('movies.introduce', $movie->id);
@endphp

<div class="movie-card">
    <a href="{{ $movieUrl }}" class="movie-card-image">
        @if ($movie->thumbnail)
        <img src="{{ $movie->thumbnail }}" alt="{{ $movie->title }}" class="movie-poster">
        @else
        <div class="movie-poster-placeholder">
            <i class="fas fa-image"></i>
        </div>
        @endif
        <div class="movie-card-overlay">
            <span class="play-button" aria-hidden="true">
                <i class="fas fa-play"></i>
            </span>
        </div>
        @if ($movie->rating)
        <span class="rating-badge">
            <i class="fas fa-star"></i> {{ number_format($movie->rating, 1) }}
        </span>
        @endif
    </a>
    <div class="movie-card-info">
        <h3 class="movie-title">
            <a href="{{ $movieUrl }}">{{ $movie->title }}</a>
        </h3>
        <p class="movie-meta">
            @php
                $cardCategoryNames = $movie->categories->pluck('name');
                if ($cardCategoryNames->isEmpty() && $movie->category) {
                    $cardCategoryNames = collect([$movie->category->name]);
                }
            @endphp
            {{ $cardCategoryNames->take(2)->join(', ') }}
        </p>
    </div>
</div>

<style>
    .movie-card {
        background: #151515;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 14px 28px rgba(0, 0, 0, 0.22);
        transition: transform 0.22s ease, border-color 0.22s ease, box-shadow 0.22s ease;
    }

    .movie-card:hover {
        transform: translateY(-6px);
        border-color: rgba(229, 9, 20, 0.42);
        box-shadow: 0 22px 40px rgba(0, 0, 0, 0.34);
    }

    .movie-card-image {
        position: relative;
        width: 100%;
        aspect-ratio: 2 / 3;
        overflow: hidden;
        background: #202020;
        display: block;
    }

    .movie-card-image::after {
        content: "";
        position: absolute;
        inset: auto 0 0;
        height: 42%;
        background: linear-gradient(180deg, transparent, rgba(0, 0, 0, 0.72));
        pointer-events: none;
        z-index: 1;
    }

    .movie-poster {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.34s ease, filter 0.34s ease;
    }

    .movie-card:hover .movie-poster {
        transform: scale(1.035);
        filter: saturate(1.06) contrast(1.03);
    }

    .movie-poster-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #666;
        font-size: 2rem;
    }

    .movie-card-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.28);
        backdrop-filter: blur(1px);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        z-index: 3;
        transition: opacity 0.22s ease;
    }

    .movie-card:hover .movie-card-overlay {
        opacity: 1;
    }

    .play-button {
        width: 50px;
        height: 50px;
        background: #e50914;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        box-shadow: 0 12px 24px rgba(229, 9, 20, 0.34);
        text-decoration: none;
        transform: scale(0.88);
        transition: transform 0.22s ease, background 0.22s ease;
    }

    .movie-card:hover .play-button {
        transform: scale(1);
    }

    .rating-badge {
        position: absolute;
        right: 10px;
        bottom: 10px;
        z-index: 4;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        min-height: 28px;
        padding: 0 9px;
        border-radius: 999px;
        background: rgba(229, 9, 20, 0.94);
        color: #fff;
        font-size: 12px;
        font-weight: 800;
        line-height: 1;
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.26);
    }

    .rating-badge i {
        color: #ffd166;
        font-size: 11px;
    }

    .movie-card-info {
        padding: 13px 14px 15px;
    }

    .movie-title {
        min-height: 40px;
        font-size: 15px;
        line-height: 1.35;
        color: #fff;
        margin: 0;
        font-weight: 700;
        overflow: hidden;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
    }

    .movie-title a {
        color: inherit;
        text-decoration: none;
    }

    .movie-meta {
        min-height: 18px;
        margin: 8px 0 0;
        font-size: 12px;
        line-height: 1.45;
        color: rgba(255, 255, 255, 0.62);
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>
