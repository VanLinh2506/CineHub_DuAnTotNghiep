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
            <span class="play-button">
                <i class="fas fa-play"></i>
            </span>
        </div>
        <div class="movie-card-badge">
            @if ($movie->rating)
            <span class="rating-badge">{{ number_format($movie->rating, 1) }}</span>
            @endif
        </div>
    </a>
    <div class="movie-card-info">
        <h3 class="movie-title">
            <a href="{{ $movieUrl }}">{{ $movie->title }}</a>
        </h3>
        <p class="movie-meta">
            @if ($movie->category)
            {{ $movie->category->name }}
            @endif
        </p>
    </div>
</div>

<style>
    .movie-card {
        background: #1a1a1a;
        border-radius: 8px;
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
    }

    .movie-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }

    .movie-card-image {
        position: relative;
        width: 100%;
        aspect-ratio: 9 / 13;
        overflow: hidden;
        background: #2a2a2a;
    }

    .movie-poster {
        width: 100%;
        height: 100%;
        object-fit: cover;
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
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .movie-card:hover .movie-card-overlay {
        opacity: 1;
    }

    .play-button {
        width: 50px;
        height: 50px;
        background: #e50914;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        transition: background 0.3s;
        text-decoration: none;
    }

    .play-button:hover {
        background: #ff1f1f;
    }

    .movie-card-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        z-index: 10;
    }

    .rating-badge {
        background: #e50914;
        color: white;
        padding: 4px 8px;
        border-radius: 4px;
        font-weight: bold;
        font-size: 0.8rem;
    }

    .movie-card-info {
        padding: 0.75rem;
    }

    .movie-title {
        font-size: 0.9rem;
        color: #fff;
        margin: 0 0 0.25rem 0;
        font-weight: 600;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .movie-title a {
        color: inherit;
        text-decoration: none;
    }

    .movie-meta {
        font-size: 0.75rem;
        color: #999;
        margin: 0;
    }
</style>
