<?php $__env->startSection('content'); ?>
<?php
    $title = 'Trang chủ';
?>

<!-- Hero Section với Slider -->
<section class="hero-section-featured">
    <?php if(!empty($sliderMovies)): ?>
        <div class="hero-slider-container">
            <div class="hero-slider" id="heroSlider">
                <?php $__currentLoopData = $sliderMovies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $featuredMovie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
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
                    ?>
                    <div class="hero-slide <?php if($index === 0): ?> active <?php endif; ?>" data-slide="<?php echo e($index); ?>">
                        <!-- Film Grain Overlay - Hiệu ứng hạt tròn -->
                        <div class="film-grain-overlay"></div>

                        <!-- Vignette Overlay - Tối viền -->
                        <div class="vignette-overlay"></div>

                        <!-- Background Image -->
                        <?php if($bgImage): ?>
                            <div class="hero-featured-bg" style="background-image: url('<?php echo e($bgImage); ?>');"></div>
                        <?php endif; ?>

                        <!-- Content Overlay -->
                        <div class="hero-featured-content">
                            <div class="container">
                                <div class="row align-items-center">
                                    <div class="col-lg-6">
                                        <!-- Title Handwritten Style -->
                                        <h1 class="hero-title-handwritten"><?php echo e($featuredMovie->title); ?></h1>

                                        <!-- Main Title -->
                                        <h2 class="hero-title-main"><?php echo e($featuredMovie->title); ?></h2>

                                        <!-- Info Badges -->
                                        <div class="hero-info-badges">
                                            <span class="badge-imdb">IMDb <?php echo e($imdbRating); ?></span>
                                            <?php if(in_array($featuredMovie->level, ['Gold', 'Premium'])): ?>
                                                <span class="badge-quality">4K</span>
                                            <?php endif; ?>
                                            <span class="badge-age">T18</span>
                                            <span class="badge-year"><?php echo e($year); ?></span>
                                            <span class="badge-duration"><?php echo e($durationText); ?></span>
                                            <span class="badge-type"><?php echo e(($featuredMovie->type ?? 'phimle') === 'phimbo' ? 'Phim bộ' : 'Phim lẻ'); ?></span>
                                        </div>

                                        <!-- Categories -->
                                        <?php if($featuredMovie->category_name): ?>
                                            <div class="hero-categories">
                                                <span class="category-tag"><?php echo e($featuredMovie->category_name); ?></span>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Description -->
                                        <?php if($featuredMovie->description): ?>
                                            <p class="hero-description">
                                                <?php
                                                    $desc = $featuredMovie->description;
                                                    echo strlen($desc) > 200 ? substr($desc, 0, 200) . '...' : $desc;
                                                ?>
                                            </p>
                                        <?php endif; ?>

                                        <!-- Action Buttons -->
                                        <div class="hero-actions">
                                            <a href="<?php echo e(route('movies.introduce', $featuredMovie->id)); ?>" class="btn-play-large">
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
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                <?php $__currentLoopData = $sliderMovies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $movie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $thumbImage = !empty($movie->thumbnail) ? $movie->thumbnail : $movie->banner;
                    ?>
                    <div class="thumbnail-item <?php if($index === 0): ?> active <?php endif; ?>"
                         onclick="goToSlide(<?php echo e($index); ?>)"
                         data-slide="<?php echo e($index); ?>">
                        <img src="<?php echo e($thumbImage); ?>" alt="<?php echo e($movie->title); ?>">
                        <div class="thumbnail-overlay">
                            <span class="thumbnail-title"><?php echo e(Str::limit($movie->title, 20)); ?></span>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    <?php endif; ?>
</section>

<!-- Movie Grid Sections -->
<div class="container">
    <?php if(!empty($latestMovies)): ?>
    <section class="movies-section">
        <div class="section-header">
            <h2 class="section-title">Phim mới cập nhật</h2>
            <a href="<?php echo e(route('movies.index')); ?>" class="view-all-link">Xem tất cả</a>
        </div>
        <!-- Style 1: Grid 5 cột -->
        <div class="movies-grid-style-1">
            <?php $__currentLoopData = $latestMovies->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('components.movie-card', ['movie' => $movie], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>
    <?php endif; ?>

    <!-- Promotion Banners Section - Vertical Layout -->
    <section class="promotion-banners-section">
        <div class="promo-banners-wrapper">
            <a href="<?php echo e(route('profile.index')); ?>" class="promo-banner-vertical">
                <img src="<?php echo e(storage_url('data/img/poster/poster_nangcap.jpg')); ?>" alt="Nâng cấp gói VIP">
                <div class="promo-overlay-vertical">
                    <h3 class="promo-title-vertical">Trải nghiệm ngay gói pro vip</h3>
                    <p class="promo-desc-vertical">thoải mái xem phim bản quyền với chất lượng lên đến 4k.</p>
                    <span class="promo-btn-vertical">Nâng cấp ngay</span>
                </div>
            </a>
            <a href="<?php echo e(route('movies.index')); ?>" class="promo-banner-vertical">
                <img src="<?php echo e(storage_url('data/img/poster/poster_datve.jpg')); ?>" alt="Đặt vé online">
                <div class="promo-overlay-vertical">
                    <h3 class="promo-title-vertical">Đặt vé online</h3>
                    <p class="promo-desc-vertical">đặt vé phim mọi lúc, mọi nơi chỉ với một bước nhấn chuột</p>
                    <span class="promo-btn-vertical">Đặt vé ngay</span>
                </div>
            </a>
        </div>
    </section>

    <?php if(!empty($phimLe)): ?>
    <section class="movies-section">
        <div class="section-header">
            <h2 class="section-title">Phim lẻ nổi bật</h2>
            <a href="<?php echo e(route('movies.index')); ?>?type=phimle" class="view-all-link">Xem tất cả</a>
        </div>
        <!-- Style 2: Horizontal Scroll -->
        <div class="movies-grid-style-2">
            <?php $__currentLoopData = $phimLe->take(5); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('components.movie-card', ['movie' => $movie], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if(!empty($phimBo)): ?>
    <section class="movies-section">
        <div class="section-header">
            <h2 class="section-title">Phim bộ nổi bật</h2>
            <a href="<?php echo e(route('movies.index')); ?>?type=phimbo" class="view-all-link">Xem tất cả</a>
        </div>
        <div class="movies-grid-style-2 featured-series-row">
            <?php $__currentLoopData = $phimBo->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php echo $__env->make('components.movie-card', ['movie' => $movie], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </section>
    <?php endif; ?>

    <?php if(!empty($topMoviesByCategory) && $topMoviesByCategory->isNotEmpty()): ?>
        <div class="ranking-heading">
            <span class="ranking-heading-kicker">Bảng xếp hạng CineHub</span>
            <h2>Top phim theo từng thể loại</h2>
            <p>Xếp hạng theo lượt xem trong tuần và điểm đánh giá.</p>
        </div>

        <?php $__currentLoopData = $topMoviesByCategory->take(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoryName => $rankedMovies): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <section class="movies-section ranking-section">
                <div class="section-header">
                    <h2 class="section-title">Top <?php echo e($categoryName); ?></h2>
                    <a href="<?php echo e(route('movies.index', ['category' => $rankedMovies->first()->category_id])); ?>" class="view-all-link">Xem tất cả</a>
                </div>
                <div class="ranking-row">
                    <?php $__currentLoopData = $rankedMovies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rank => $movie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('movies.introduce', $movie->id)); ?>" class="ranking-card">
                            <span class="ranking-number" data-rank="<?php echo e($rank + 1); ?>"><?php echo e($rank + 1); ?></span>
                            <div class="ranking-poster">
                                <?php if($movie->thumbnail): ?>
                                    <img src="<?php echo e($movie->thumbnail); ?>" alt="<?php echo e($movie->title); ?>" loading="lazy">
                                <?php else: ?>
                                    <div class="ranking-poster-empty"><i class="fas fa-film"></i></div>
                                <?php endif; ?>
                                <span class="ranking-rating"><i class="fas fa-star"></i> <?php echo e(number_format($movie->rating ?? 0, 1)); ?></span>
                            </div>
                            <div class="ranking-info">
                                <strong><?php echo e($movie->title); ?></strong>
                                <small><?php echo e($movie->watch_history_count); ?> lượt xem tuần này</small>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </section>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <div class="genre-heading">
            <span>Khám phá thêm</span>
            <h2>Mỗi thể loại, một sắc màu riêng</h2>
        </div>

        <?php $__currentLoopData = $topMoviesByCategory->skip(2); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoryName => $genreMovies): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $genreLayout = ($loop->index % 3) + 1;
                $categoryId = $genreMovies->first()->category_id;
            ?>
            <section class="movies-section genre-showcase genre-layout-<?php echo e($genreLayout); ?>">
                <div class="section-header">
                    <div>
                        <span class="genre-label">Thể loại</span>
                        <h2 class="section-title"><?php echo e($categoryName); ?></h2>
                    </div>
                    <a href="<?php echo e(route('movies.index', ['category' => $categoryId])); ?>" class="view-all-link">Khám phá tất cả</a>
                </div>

                <?php if($genreLayout === 1): ?>
                    <div class="genre-cinema-row">
                        <?php $__currentLoopData = $genreMovies->take(7); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('movies.introduce', $movie->id)); ?>" class="genre-cinema-card">
                                <div class="genre-image">
                                    <?php if($movie->thumbnail): ?><img src="<?php echo e($movie->thumbnail); ?>" alt="<?php echo e($movie->title); ?>" loading="lazy">
                                    <?php else: ?><div class="genre-image-empty"><i class="fas fa-film"></i></div><?php endif; ?>
                                    <span><?php echo e(number_format($movie->rating ?? 0, 1)); ?> <i class="fas fa-star"></i></span>
                                </div>
                                <strong><?php echo e($movie->title); ?></strong>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php elseif($genreLayout === 2): ?>
                    <?php ($featuredGenreMovie = $genreMovies->first()); ?>
                    <div class="genre-editorial">
                        <a href="<?php echo e(route('movies.introduce', $featuredGenreMovie->id)); ?>" class="genre-editorial-featured">
                            <?php if($featuredGenreMovie->banner || $featuredGenreMovie->thumbnail): ?>
                                <img src="<?php echo e($featuredGenreMovie->banner ?: $featuredGenreMovie->thumbnail); ?>" alt="<?php echo e($featuredGenreMovie->title); ?>" loading="lazy">
                            <?php else: ?>
                                <div class="genre-image-empty"><i class="fas fa-clapperboard"></i></div>
                            <?php endif; ?>
                            <div class="genre-editorial-overlay">
                                <small>Đề xuất nổi bật</small>
                                <h3><?php echo e($featuredGenreMovie->title); ?></h3>
                                <p><?php echo e(Str::limit($featuredGenreMovie->description, 95)); ?></p>
                            </div>
                        </a>
                        <div class="genre-editorial-list">
                            <?php $__currentLoopData = $genreMovies->skip(1)->take(4); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('movies.introduce', $movie->id)); ?>" class="genre-mini-card">
                                    <?php if($movie->thumbnail): ?><img src="<?php echo e($movie->thumbnail); ?>" alt="<?php echo e($movie->title); ?>" loading="lazy">
                                    <?php else: ?><div class="genre-image-empty"><i class="fas fa-film"></i></div><?php endif; ?>
                                    <div><strong><?php echo e($movie->title); ?></strong><small><i class="fas fa-star"></i> <?php echo e(number_format($movie->rating ?? 0, 1)); ?></small></div>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="genre-compact-grid">
                        <?php $__currentLoopData = $genreMovies->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('movies.introduce', $movie->id)); ?>" class="genre-compact-card">
                                <?php if($movie->thumbnail): ?><img src="<?php echo e($movie->thumbnail); ?>" alt="<?php echo e($movie->title); ?>" loading="lazy">
                                <?php else: ?><div class="genre-image-empty"><i class="fas fa-film"></i></div><?php endif; ?>
                                <div><strong><?php echo e($movie->title); ?></strong><small><?php echo e($categoryName); ?></small></div>
                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </section>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    <?php endif; ?>
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

    .featured-series-row > * {
        flex: 0 0 210px;
    }

    .ranking-heading {
        margin: 4.5rem 0 2rem;
        padding: 2rem 2.2rem;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 24px;
        background:
            radial-gradient(circle at 12% 30%, rgba(229, 9, 20, 0.22), transparent 32%),
            linear-gradient(120deg, #171717, #242124);
        box-shadow: 0 22px 50px rgba(0, 0, 0, 0.28);
    }

    .ranking-heading-kicker {
        color: #ff4b57;
        font-size: 0.78rem;
        font-weight: 800;
        letter-spacing: 0.16em;
        text-transform: uppercase;
    }

    .ranking-heading h2 {
        margin: 0.45rem 0 0.4rem;
        color: #fff;
        font-size: clamp(1.7rem, 4vw, 2.7rem);
        font-weight: 900;
    }

    .ranking-heading p {
        margin: 0;
        color: #aaa;
    }

    .ranking-section {
        padding: 1.2rem 0 0.5rem;
    }

    .ranking-row {
        display: flex;
        gap: 1.15rem;
        overflow-x: auto;
        overflow-y: hidden;
        padding: 0.5rem 0.25rem 1.5rem;
        scroll-snap-type: x proximity;
        scrollbar-width: thin;
        scrollbar-color: #e50914 rgba(255,255,255,.08);
    }

    .ranking-row::-webkit-scrollbar { height: 7px; }
    .ranking-row::-webkit-scrollbar-track { background: rgba(255,255,255,.08); border-radius: 20px; }
    .ranking-row::-webkit-scrollbar-thumb { background: linear-gradient(90deg, #e50914, #ff5060); border-radius: 20px; }

    .ranking-card {
        position: relative;
        flex: 0 0 255px;
        min-height: 330px;
        padding-left: 62px;
        color: #fff;
        text-decoration: none;
        scroll-snap-align: start;
        transition: transform .25s ease;
    }

    .ranking-card:hover {
        color: #fff;
        transform: translateY(-7px);
    }

    .ranking-number {
        position: absolute;
        left: 0;
        bottom: 62px;
        z-index: 3;
        width: 88px;
        color: #111;
        font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;
        font-size: 8.4rem;
        font-style: italic;
        line-height: .78;
        text-align: center;
        letter-spacing: -0.09em;
        -webkit-text-stroke: 3px rgba(255,255,255,.92);
        filter: drop-shadow(7px 8px 0 rgba(229,9,20,.62));
        user-select: none;
    }

    .ranking-poster {
        position: relative;
        z-index: 2;
        height: 280px;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,.13);
        border-radius: 18px;
        background: #202020;
        box-shadow: 0 18px 35px rgba(0,0,0,.42);
    }

    .ranking-poster img,
    .ranking-poster-empty {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform .35s ease;
    }

    .ranking-card:hover .ranking-poster img { transform: scale(1.06); }

    .ranking-poster-empty {
        display: grid;
        place-items: center;
        color: rgba(255,255,255,.35);
        font-size: 3.2rem;
        background: linear-gradient(145deg, #33272a, #151515 65%);
    }

    .ranking-rating {
        position: absolute;
        right: 9px;
        bottom: 9px;
        padding: 5px 9px;
        border-radius: 999px;
        background: rgba(229,9,20,.94);
        font-size: .74rem;
        font-weight: 800;
        box-shadow: 0 5px 15px rgba(0,0,0,.35);
    }

    .ranking-rating i { color: #ffd84d; }

    .ranking-info {
        position: relative;
        z-index: 4;
        padding: 0.8rem 0.25rem 0;
    }

    .ranking-info strong {
        display: block;
        overflow: hidden;
        color: #fff;
        font-size: .92rem;
        line-height: 1.3;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .ranking-info small {
        display: block;
        margin-top: .3rem;
        color: #8e8e8e;
        font-size: .72rem;
    }

    .genre-heading {
        margin: 5rem 0 2.5rem;
        text-align: center;
    }

    .genre-heading span,
    .genre-label {
        color: #ff4b57;
        font-size: .72rem;
        font-weight: 800;
        letter-spacing: .15em;
        text-transform: uppercase;
    }

    .genre-heading h2 {
        margin: .45rem 0 0;
        color: #fff;
        font-size: clamp(1.6rem, 3.5vw, 2.35rem);
        font-weight: 850;
    }

    .genre-showcase {
        position: relative;
        padding: 1.7rem;
        overflow: hidden;
        border: 1px solid rgba(255,255,255,.08);
        border-radius: 22px;
        background: linear-gradient(145deg, rgba(35,35,35,.94), rgba(19,19,19,.97));
        box-shadow: 0 20px 45px rgba(0,0,0,.24);
    }

    .genre-showcase::before {
        content: '';
        position: absolute;
        inset: 0 auto auto 0;
        width: 160px;
        height: 3px;
        background: linear-gradient(90deg, #e50914, transparent);
    }

    .genre-showcase .section-header { position: relative; z-index: 2; }

    .genre-cinema-row {
        display: flex;
        gap: 1rem;
        overflow-x: auto;
        padding: .25rem .1rem 1rem;
        scroll-snap-type: x proximity;
    }

    .genre-cinema-card {
        flex: 0 0 175px;
        color: #fff;
        text-decoration: none;
        scroll-snap-align: start;
    }

    .genre-image {
        position: relative;
        height: 245px;
        overflow: hidden;
        border-radius: 15px;
        background: #222;
        box-shadow: 0 13px 25px rgba(0,0,0,.32);
    }

    .genre-image img,
    .genre-image-empty {
        width: 100%; height: 100%; object-fit: cover;
    }

    .genre-image-empty {
        display: grid;
        place-items: center;
        color: rgba(255,255,255,.28);
        font-size: 2.4rem;
        background: linear-gradient(145deg, #40272c, #171717);
    }

    .genre-image > span {
        position: absolute;
        right: 8px; bottom: 8px;
        padding: 4px 8px;
        border-radius: 20px;
        background: rgba(0,0,0,.78);
        color: #fff;
        font-size: .72rem;
        font-weight: 800;
    }

    .genre-image > span i,
    .genre-mini-card small i { color: #ffc928; }

    .genre-cinema-card > strong {
        display: block;
        margin-top: .75rem;
        overflow: hidden;
        font-size: .86rem;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .genre-editorial {
        display: grid;
        grid-template-columns: minmax(0, 1.5fr) minmax(280px, .8fr);
        gap: 1.1rem;
    }

    .genre-editorial-featured {
        position: relative;
        min-height: 410px;
        overflow: hidden;
        border-radius: 18px;
        color: #fff;
        text-decoration: none;
    }

    .genre-editorial-featured > img,
    .genre-editorial-featured > .genre-image-empty {
        width: 100%; height: 100%; object-fit: cover;
        position: absolute; inset: 0;
    }

    .genre-editorial-overlay {
        position: absolute;
        inset: auto 0 0;
        padding: 5rem 1.7rem 1.6rem;
        background: linear-gradient(transparent, rgba(0,0,0,.96));
    }

    .genre-editorial-overlay small { color: #ff5361; font-weight: 800; }
    .genre-editorial-overlay h3 { margin: .3rem 0 .45rem; font-size: 1.65rem; }
    .genre-editorial-overlay p { margin: 0; color: #c8c8c8; font-size: .84rem; }

    .genre-editorial-list {
        display: grid;
        grid-template-rows: repeat(4, 1fr);
        gap: .7rem;
    }

    .genre-mini-card {
        display: grid;
        grid-template-columns: 74px minmax(0, 1fr);
        gap: .85rem;
        min-height: 88px;
        padding: .55rem;
        border-radius: 13px;
        background: rgba(255,255,255,.055);
        color: #fff;
        text-decoration: none;
        transition: background .2s, transform .2s;
    }

    .genre-mini-card:hover { color: #fff; background: rgba(229,9,20,.16); transform: translateX(4px); }
    .genre-mini-card img, .genre-mini-card .genre-image-empty { width: 74px; height: 76px; border-radius: 9px; object-fit: cover; }
    .genre-mini-card > div:last-child { align-self: center; min-width: 0; }
    .genre-mini-card strong { display: block; overflow: hidden; font-size: .83rem; text-overflow: ellipsis; white-space: nowrap; }
    .genre-mini-card small { display: block; margin-top: .4rem; color: #aaa; }

    .genre-compact-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: .9rem;
    }

    .genre-compact-card {
        display: grid;
        grid-template-columns: 92px minmax(0,1fr);
        min-height: 125px;
        overflow: hidden;
        border-radius: 14px;
        background: rgba(255,255,255,.055);
        color: #fff;
        text-decoration: none;
        transition: transform .22s, background .22s;
    }

    .genre-compact-card:hover { color: #fff; transform: translateY(-4px); background: rgba(255,255,255,.1); }
    .genre-compact-card img, .genre-compact-card > .genre-image-empty { width: 92px; height: 125px; object-fit: cover; }
    .genre-compact-card > div:last-child { align-self: center; min-width: 0; padding: .8rem; }
    .genre-compact-card strong { display: block; overflow: hidden; font-size: .82rem; text-overflow: ellipsis; white-space: nowrap; }
    .genre-compact-card small { display: block; margin-top: .4rem; color: #888; font-size: .7rem; }

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

        .genre-editorial { grid-template-columns: 1fr; }
        .genre-editorial-list { grid-template-columns: repeat(2, 1fr); grid-template-rows: auto; }
        .genre-compact-grid { grid-template-columns: repeat(2, minmax(0,1fr)); }
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

        .featured-series-row > * { flex-basis: 170px; }
        .ranking-heading { padding: 1.5rem; border-radius: 18px; }
        .ranking-card { flex-basis: 210px; min-height: 285px; padding-left: 48px; }
        .ranking-poster { height: 235px; }
        .ranking-number { width: 70px; bottom: 59px; font-size: 6.6rem; }
        .genre-showcase { padding: 1.15rem; border-radius: 17px; }
        .genre-cinema-card { flex-basis: 145px; }
        .genre-image { height: 205px; }
        .genre-editorial-featured { min-height: 330px; }
        .genre-editorial-list, .genre-compact-grid { grid-template-columns: 1fr; }

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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\CineHub_DuAnTotNghiep\resources\views/home/index.blade.php ENDPATH**/ ?>