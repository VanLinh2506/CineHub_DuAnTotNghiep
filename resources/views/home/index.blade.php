@extends('layouts.app')

@section('content')
@php
    $title = 'Trang chủ';
@endphp

<!-- Hero Section với Slider -->
<section class="hero-section-featured">
    @if (!empty($sliderMovies))
        <div class="hero-slider-container">
            <div class="hero-slider" id="heroSlider">
                @foreach ($sliderMovies as $index => $featuredMovie)
                    @php
                        $year = $featuredMovie->created_at ? date('Y', strtotime($featuredMovie->created_at)) : date('Y');
                        $duration = $featuredMovie->duration ?? 0;
                        $hours = floor($duration / 60);
                        $minutes = $duration % 60;
                        $durationText = $hours > 0 ? "{$hours}h " : '';
                        $durationText .= $minutes > 0 ? "{$minutes}m" : '';
                        if (!$durationText) $durationText = 'N/A';
                        if (($featuredMovie->type ?? 'phimle') === 'phimbo') {
                            $durationText = 'Phim bộ';
                        }
                        $imdbRating = number_format($featuredMovie->rating * 1.1, 1);
                        $bgImage = !empty($featuredMovie->banner) ? $featuredMovie->banner : $featuredMovie->thumbnail;
                    @endphp
                    <div class="hero-slide @if($index === 0) active @endif" data-slide="{{ $index }}">
                        <!-- Background Image -->
                        @if ($bgImage)
                            <div class="hero-featured-bg" style="background-image: url('{{ $bgImage }}');"></div>
                        @endif
                        
                        <!-- Content Overlay -->
                        <div class="hero-featured-content">
                            <div class="container">
                                <div class="row align-items-center">
                                    <div class="col-lg-6">
                                        <!-- Title Handwritten Style -->
                                        <h1 class="hero-title-handwritten">{{ $featuredMovie->title }}</h1>
                                        
                                        <!-- Main Title -->
                                        <h2 class="hero-title-main">{{ $featuredMovie->title }}</h2>
                                        
                                        <!-- Info Badges -->
                                        <div class="hero-info-badges">
                                            <span class="badge-imdb">IMDb {{ $imdbRating }}</span>
                                            @if (in_array($featuredMovie->level, ['Gold', 'Premium']))
                                                <span class="badge-quality">4K</span>
                                            @endif
                                            <span class="badge-age">T18</span>
                                            <span class="badge-year">{{ $year }}</span>
                                            <span class="badge-duration">{{ $durationText }}</span>
                                            <span class="badge-type">{{ ($featuredMovie->type ?? 'phimle') === 'phimbo' ? 'Phim bộ' : 'Phim lẻ' }}</span>
                                        </div>
                                        
                                        <!-- Categories -->
                                        @if ($featuredMovie->category_name)
                                            <div class="hero-categories">
                                                <span class="category-tag">{{ $featuredMovie->category_name }}</span>
                                            </div>
                                        @endif
                                        
                                        <!-- Description -->
                                        @if ($featuredMovie->description)
                                            <p class="hero-description">
                                                @php
                                                    $desc = $featuredMovie->description;
                                                    echo strlen($desc) > 200 ? substr($desc, 0, 200) . '...' : $desc;
                                                @endphp
                                            </p>
                                        @endif
                                        
                                        <!-- Action Buttons -->
                                        <div class="hero-actions">
                                            <a href="{{ url('?route=movie/watch&id=' . $featuredMovie->id) }}" class="btn-play-large">
                                                <i class="fas fa-play"></i>
                                            </a>
                                            <button class="btn-action-icon" title="Yêu thích">
                                                <i class="fas fa-heart"></i>
                                            </button>
                                            <button class="btn-action-icon" title="Thông tin">
                                                <i class="fas fa-info-circle"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            <!-- Slider Controls -->
            <button class="hero-slider-prev" onclick="changeSlide(-1)">
                <i class="fas fa-chevron-left"></i>
            </button>
            <button class="hero-slider-next" onclick="changeSlide(1)">
                <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    @endif
</section>

<!-- Movie Grid Sections -->
<div class="container">
    @if (!empty($newMovies))
    <section class="movies-section">
        <div class="section-header">
            <h2 class="section-title">Phim mới cập nhật</h2>
            <a href="{{ url('/?route=movie/index') }}" class="view-all-link">Xem tất cả</a>
        </div>
        <div class="movies-grid">
            @foreach ($newMovies as $movie)
                @include('components.movie-card', ['movie' => $movie])
            @endforeach
        </div>
    </section>
    @endif
    
    @if (!empty($topRatedMovies))
    <section class="movies-section">
        <div class="section-header">
            <h2 class="section-title">Phim được đánh giá cao</h2>
            <a href="{{ url('/?route=movie/index') }}" class="view-all-link">Xem tất cả</a>
        </div>
        <div class="movies-grid">
            @foreach ($topRatedMovies as $movie)
                @include('components.movie-card', ['movie' => $movie])
            @endforeach
        </div>
    </section>
    @endif
    
    @if (!empty($tvSeriesMovies))
    <section class="movies-section">
        <div class="section-header">
            <h2 class="section-title">Phim bộ nổi bật</h2>
            <a href="{{ url('/?route=movie/index&type=phimbo') }}" class="view-all-link">Xem tất cả</a>
        </div>
        <div class="movies-grid">
            @foreach ($tvSeriesMovies as $movie)
                @include('components.movie-card', ['movie' => $movie])
            @endforeach
        </div>
    </section>
    @endif
</div>

<style>
    .movies-section {
        margin-bottom: 3rem;
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }
    
    .section-title {
        font-size: 1.5rem;
        color: #fff;
        font-weight: 600;
        margin: 0;
    }
    
    .view-all-link {
        color: #e50914;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.3s;
    }
    
    .view-all-link:hover {
        color: #ff1f1f;
    }
    
    .movies-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 1.5rem;
    }
    
    @media (max-width: 768px) {
        .movies-grid {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 1rem;
        }
    }
</style>

<script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.hero-slide');
    const totalSlides = slides.length;
    
    function showSlide(n) {
        if (totalSlides === 0) return;
        
        if (n >= totalSlides) {
            currentSlide = 0;
        } else if (n < 0) {
            currentSlide = totalSlides - 1;
        } else {
            currentSlide = n;
        }
        
        slides.forEach(slide => slide.classList.remove('active'));
        if (slides[currentSlide]) {
            slides[currentSlide].classList.add('active');
        }
    }
    
    function changeSlide(n) {
        showSlide(currentSlide + n);
    }
    
    // Auto-advance slider every 5 seconds
    setInterval(() => {
        changeSlide(1);
    }, 5000);
</script>
@endsection
