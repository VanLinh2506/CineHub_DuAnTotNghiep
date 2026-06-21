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
                        $bgImage = !empty($featuredMovie->banner) ? movie_img($featuredMovie->banner) : movie_img($featuredMovie->thumbnail);
                    @endphp
                    <div class="hero-slide @if($index === 0) active @endif" data-slide="{{ $index }}">
                        <!-- Film Grain Overlay - Hiệu ứng hạt tròn -->
                        <div class="film-grain-overlay"></div>
                        
                        <!-- Vignette Overlay - Tối viền -->
                        <div class="vignette-overlay"></div>
                        
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
                                            <a href="{{ route('movies.watch', $featuredMovie->id) }}" class="btn-play-large">
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
            
            <!-- Slider Thumbnails -->
            <div class="hero-slider-thumbnails">
                @foreach ($sliderMovies as $index => $movie)
                    @php
                        $thumbImage = !empty($movie->thumbnail) ? movie_img($movie->thumbnail) : movie_img($movie->banner);
                    @endphp
                    <div class="thumbnail-item @if($index === 0) active @endif" 
                         onclick="goToSlide({{ $index }})" 
                         data-slide="{{ $index }}">
                        <img src="{{ $thumbImage }}" alt="{{ $movie->title }}">
                        <div class="thumbnail-overlay">
                            <span class="thumbnail-title">{{ Str::limit($movie->title, 20) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</section>

<!-- Movie Grid Sections -->
<div class="container">
    @if (!empty($latestMovies))
    <section class="movies-section">
        <div class="section-header">
            <h2 class="section-title">Phim mới cập nhật</h2>
            <a href="{{ route('movies.index') }}" class="view-all-link">Xem tất cả</a>
        </div>
        <!-- Style 1: Grid 5 cột -->
        <div class="movies-grid-style-1">
            @foreach ($latestMovies->take(5) as $movie)
                @include('components.movie-card', ['movie' => $movie])
            @endforeach
        </div>
    </section>
    @endif

    <!-- Promotion Banners Section - Vertical Layout -->
    <section class="promotion-banners-section">
        <div class="promo-banners-wrapper">
            <a href="{{ route('profile.index') }}" class="promo-banner-vertical">
                <img src="{{ asset('data/img/poster/poster_nangcap.jpg') }}" alt="Nâng cấp gói VIP">
                <div class="promo-overlay-vertical">
                    <h3 class="promo-title-vertical">Trải nghiệm ngay gói pro vip</h3>
                    <p class="promo-desc-vertical">thoải mái xem phim bản quyền với chất lượng lên đến 4k.</p>
                    <span class="promo-btn-vertical">Nâng cấp ngay</span>
                </div>
            </a>
            <a href="{{ route('movies.index') }}" class="promo-banner-vertical">
                <img src="{{ asset('data/img/poster/poster_datve.jpg') }}" alt="Đặt vé online">
                <div class="promo-overlay-vertical">
                    <h3 class="promo-title-vertical">Đặt vé online</h3>
                    <p class="promo-desc-vertical">đặt vé phim mọi lúc, mọi nơi chỉ với một bước nhấn chuột</p>
                    <span class="promo-btn-vertical">Đặt vé ngay</span>
                </div>
            </a>
        </div>
    </section>
    
    @if (!empty($phimLe))
    <section class="movies-section">
        <div class="section-header">
            <h2 class="section-title">Phim lẻ nổi bật</h2>
            <a href="{{ route('movies.index') }}?type=phimle" class="view-all-link">Xem tất cả</a>
        </div>
        <!-- Style 2: Horizontal Scroll -->
        <div class="movies-grid-style-2">
            @foreach ($phimLe->take(5) as $movie)
                @include('components.movie-card', ['movie' => $movie])
            @endforeach
        </div>
    </section>
    @endif
    
    @if (!empty($phimBo))
    <section class="movies-section">
        <div class="section-header">
            <h2 class="section-title">Phim bộ nổi bật</h2>
            <a href="{{ route('movies.index') }}?type=phimbo" class="view-all-link">Xem tất cả</a>
        </div>
        <!-- Style 3: Featured + Grid -->
        <div class="movies-grid-style-3">
            @foreach ($phimBo->take(5) as $index => $movie)
                <div class="movie-item-style-3 @if($index === 0) featured @endif">
                    @include('components.movie-card', ['movie' => $movie])
                </div>
            @endforeach
        </div>
    </section>
    @endif
    
    @if (!empty($topMoviesWeek))
    <section class="movies-section">
        <div class="section-header">
            <h2 class="section-title">Top phim xem nhiều trong tuần</h2>
            <a href="{{ route('movies.index') }}" class="view-all-link">Xem tất cả</a>
        </div>
        <!-- Style 1: Grid 5 cột -->
        <div class="movies-grid-style-1">
            @foreach ($topMoviesWeek->take(5) as $movie)
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
    
    /* Style 1: Grid 5 cột đều nhau */
    .movies-grid-style-1 {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1.5rem;
    }
    
    /* Style 2: Horizontal scroll với cards to hơn */
    .movies-grid-style-2 {
        display: flex;
        gap: 2rem;
        overflow-x: auto;
        overflow-y: hidden;
        padding-bottom: 15px;
        scrollbar-width: thin;
        scrollbar-color: rgba(229, 9, 20, 0.6) rgba(255, 255, 255, 0.1);
    }
    
    .movies-grid-style-2 > * {
        flex: 0 0 220px;
    }
    
    .movies-grid-style-2::-webkit-scrollbar {
        height: 8px;
    }
    
    .movies-grid-style-2::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.1);
        border-radius: 10px;
    }
    
    .movies-grid-style-2::-webkit-scrollbar-thumb {
        background: rgba(229, 9, 20, 0.6);
        border-radius: 10px;
    }
    
    .movies-grid-style-2::-webkit-scrollbar-thumb:hover {
        background: rgba(229, 9, 20, 0.9);
    }
    
    /* Style 3: Featured first + 4 smaller */
    .movies-grid-style-3 {
        display: grid;
        grid-template-columns: 2fr 1fr 1fr;
        grid-template-rows: 1fr 1fr;
        gap: 1.5rem;
        min-height: 500px;
    }
    
    .movie-item-style-3.featured {
        grid-row: 1 / 3;
        grid-column: 1;
    }
    
    /* Promotion Banners - Vertical */
    .promotion-banners-section {
        margin: 3rem 0;
    }
    
    .promo-banners-wrapper {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 2rem;
        max-width: 66.666%;
        margin: 0 auto;
    }
    
    .promo-banner-vertical {
        position: relative;
        display: block;
        border-radius: 12px;
        overflow: hidden;
        height: 550px;
        text-decoration: none;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .promo-banner-vertical:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 35px rgba(229, 9, 20, 0.4);
    }
    
    .promo-banner-vertical img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transition: transform 0.5s ease;
    }
    
    .promo-banner-vertical:hover img {
        transform: scale(1.08);
    }
    
    .promo-overlay-vertical {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background: linear-gradient(to top, rgba(0,0,0,0.95) 0%, rgba(0,0,0,0.85) 35%, transparent 100%);
        padding: 2.5rem 1.8rem;
        color: #fff;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
    }
    
    .promo-title-vertical {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.8rem;
        text-transform: capitalize;
        line-height: 1.3;
    }
    
    .promo-desc-vertical {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.85);
        margin-bottom: 1.5rem;
        line-height: 1.6;
    }
    
    .promo-btn-vertical {
        display: inline-block;
        background: #e50914;
        color: #fff;
        padding: 0.7rem 1.8rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        align-self: flex-start;
        text-transform: capitalize;
    }
    
    .promo-banner-vertical:hover .promo-btn-vertical {
        background: #ff1f1f;
        transform: scale(1.08);
        box-shadow: 0 5px 15px rgba(229, 9, 20, 0.5);
    }
    
    @media (max-width: 1200px) {
        .movies-grid-style-1 {
            grid-template-columns: repeat(4, 1fr);
        }
        
        .movies-grid-style-3 {
            grid-template-columns: 1.5fr 1fr 1fr;
        }
    }
    
    @media (max-width: 992px) {
        .promo-banners-wrapper {
            max-width: 80%;
        }
        
        .movies-grid-style-1 {
            grid-template-columns: repeat(3, 1fr);
        }
        
        .movies-grid-style-3 {
            grid-template-columns: 1fr 1fr;
            grid-template-rows: auto auto auto;
        }
        
        .movie-item-style-3.featured {
            grid-row: 1 / 2;
            grid-column: 1 / 3;
        }
    }
    
    @media (max-width: 768px) {
        .movies-grid {
            grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
            gap: 1rem;
        }
        
        .promo-banners-wrapper {
            grid-template-columns: 1fr;
            max-width: 90%;
        }
        
        .promo-banner-vertical {
            height: 450px;
        }
        
        .movies-grid-style-1 {
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        
        .movies-grid-style-2 > * {
            flex: 0 0 160px;
        }
        
        .movies-grid-style-3 {
            grid-template-columns: 1fr;
            grid-template-rows: auto;
        }
        
        .movie-item-style-3.featured {
            grid-row: 1;
            grid-column: 1;
        }
        
        .promo-overlay-vertical {
            padding: 1.8rem 1.2rem;
        }
        
        .promo-title-vertical {
            font-size: 1.2rem;
        }
        
        .promo-desc-vertical {
            font-size: 0.8rem;
        }
    }
</style>

<script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.hero-slide');
    const thumbnails = document.querySelectorAll('.thumbnail-item');
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
        thumbnails.forEach(thumb => thumb.classList.remove('active'));
        
        if (slides[currentSlide]) {
            slides[currentSlide].classList.add('active');
        }
        if (thumbnails[currentSlide]) {
            thumbnails[currentSlide].classList.add('active');
        }
    }
    
    function changeSlide(n) {
        showSlide(currentSlide + n);
    }
    
    function goToSlide(n) {
        showSlide(n);
    }
    
    // Auto-advance slider every 5 seconds
    setInterval(() => {
        changeSlide(1);
    }, 5000);
</script>
@endsection
