<?php
$current_page = 'booking';
$title = 'Đặt Vé Xem Phim';
$meta_description = $movie ? 'Đặt vé xem phim ' . htmlspecialchars($movie['title']) . ' tại CineHub. Chọn rạp, ngày, giờ và ghế ngồi phù hợp cho bạn.' : 'Đặt vé xem phim tại CineHub. Xem phim tại rạp với giá cả hợp lý và dịch vụ chất lượng.';
$meta_keywords = 'đặt vé xem phim, vé xem phim online, mua vé xem phim, CineHub' . ($movie ? ', ' . htmlspecialchars($movie['title']) : '');
$meta_og_title = $title . ' - CineHub';
$meta_og_description = $meta_description;
$meta_og_image = ($movie && $movie['thumbnail']) ? $movie['thumbnail'] : null;
?>

<section class="booking-page-section">
    <div class="container-fluid px-4">
        <div class="row g-4">
            <!-- Left Column: Movie Info -->
            <div class="col-lg-5">
                <?php if ($movie): ?>
                    <article class="booking-movie-info" itemscope itemtype="https://schema.org/Movie">
                        <!-- Movie Poster -->
                        <div class="movie-poster-large mb-4">
                            <?php if ($movie['thumbnail']): ?>
                                <img id="img-moviee" src="<?php echo htmlspecialchars($movie['thumbnail']); ?>"
                                    alt="<?php echo htmlspecialchars($movie['title']); ?>"
                                    class="img-fluid rounded"
                                    itemprop="image">
                            <?php else: ?>
                                <div class="poster-placeholder">
                                    <i class="fas fa-film fa-5x"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="background_film_blur">
                            <img src="<?php echo htmlspecialchars($movie['thumbnail']); ?>" alt="">
                        </div>

                        <style>
                            #img-moviee {
                                img {
                                    border: 2px solid white;
                                }
                            }

                            .booking-movie-info {
                                position: relative;
                                /* tạo vùng z-index */
                                z-index: 2;
                            }

                            .background_film_blur {
                                position: absolute;
                                inset: 0;
                                width: 100%;
                                height: 100%;
                                z-index: 0;
                                background-color: black;
                                border: 2px solid white;
                                border-radius: 14px;
                            }

                            .background_film_blur img {
                                width: 100%;
                                height: 100%;
                                object-fit: cover;
                                filter: blur(4px);
                                opacity: 0.3;
                            }

                            .movie-poster-large {
                                position: relative;
                                z-index: 2;
                            }

                            .movie-poster-large img {
                                border: 2px solid white;
                                width: 100%;
                                border-radius: 10px;
                                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
                            }

                            .movie-title-booking,
                            .movie-rating,
                            .movie-description,
                            .movie-categories,
                            .btn-trailer {
                                position: relative;
                                z-index: 3;
                                /* cao nhất */
                                color: white;
                            }

                            /* User Location Badge Styles */
                            .user-location-badge {
                                display: flex;
                                align-items: center;
                                padding: 8px 16px;
                                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                color: white;
                                border-radius: 20px;
                                font-size: 13px;
                                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
                                animation: slideInRight 0.5s ease-out;
                            }

                            /* Theater Button Styles - Cùng width và căn trái */
                            .theater-btn {
                                min-width: 350px !important;
                                width: 430px !important;
                                text-align: left !important;
                                display: flex !important;
                                align-items: center !important;
                                justify-content: flex-start !important;
                                flex-wrap: nowrap !important;
                                white-space: nowrap !important;
                            }

                            .theater-btn i {
                                flex-shrink: 0;
                            }

                            .theater-btn span {
                                flex: 1;
                                min-width: 0;
                                overflow: hidden;
                                text-overflow: ellipsis;
                            }

                            #moreTheatersBtn {
                                justify-content: space-between !important;
                            }

                            #moreTheatersBtn .ms-auto {
                                margin-left: auto !important;
                                flex-shrink: 0;
                            }

                            /* More Theaters Dropdown Styles */
                            .more-theater-item {
                                display: block;
                                padding: 12px 20px;
                                text-decoration: none;
                                color: #333;
                                border-bottom: 1px solid #eee;
                                transition: background 0.2s;
                                text-align: left;
                            }

                            .more-theater-item:hover {
                                background: #f8f9fa;
                                color: #e50914;
                            }

                            .more-theater-item.active {
                                background: #fff5f5;
                                color: #e50914;
                                font-weight: bold;
                            }

                            .more-theater-item:last-child {
                                border-bottom: none;
                            }

                            /* Theaters List - Căn trái */
                            .theaters-list {
                                justify-content: flex-start !important;
                                align-items: flex-start !important;
                                position: relative !important;
                                z-index: 1 !important;
                            }

                            /* More Theaters Container - Đảm bảo dropdown hiển thị trên cùng */
                            .more-theaters-container {
                                position: relative !important;
                                z-index: 10000 !important;
                            }

                            #moreTheatersList {
                                z-index: 10002 !important;
                                position: absolute !important;
                            }

                            /* Khi dropdown mở, thêm padding-bottom cho booking-step để không bị che */
                            .booking-step.dropdown-open {
                                padding-bottom: 450px !important;
                                transition: padding-bottom 0.3s ease;
                            }

                            .user-location-badge i {
                                animation: pulse 2s infinite;
                            }

                            @keyframes slideInRight {
                                from {
                                    opacity: 0;
                                    transform: translateX(20px);
                                }

                                to {
                                    opacity: 1;
                                    transform: translateX(0);
                                }
                            }

                            @keyframes pulse {

                                0%,
                                100% {
                                    opacity: 1;
                                }

                                50% {
                                    opacity: 0.6;
                                }
                            }

                            /* Improved Booking Step Styles */
                            .booking-step {
                                background: rgba(255, 255, 255, 0.05);
                                padding: 20px;
                                border-radius: 12px;
                                border: 1px solid rgba(255, 255, 255, 0.1);
                                backdrop-filter: blur(10px);
                                transition: all 0.3s ease;
                                position: relative;
                                z-index: 1;
                                overflow: visible !important;
                                height: auto !important;
                                min-height: auto !important;
                            }

                            .booking-step:hover {
                                background: rgba(255, 255, 255, 0.08);
                                border-color: rgba(255, 255, 255, 0.2);
                                transform: translateY(-2px);
                                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
                            }

                            .booking-label {
                                color: #fff;
                                font-weight: 600;
                                font-size: 16px;
                                margin-bottom: 15px;
                                display: flex;
                                align-items: center;
                            }

                            /* Improved Theater Button Styles */
                            .theater-btn {
                                padding: 12px 20px;
                                border: 2px solid #ddd;
                                border-radius: 10px;
                                text-decoration: none;
                                color: #333;
                                background: white;
                                transition: all 0.3s ease;
                                font-weight: normal;
                                position: relative;
                                overflow: hidden;
                            }

                            .theater-btn::before {
                                content: '';
                                position: absolute;
                                top: 0;
                                left: -100%;
                                width: 100%;
                                height: 100%;
                                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
                                transition: left 0.5s;
                            }

                            .theater-btn:hover::before {
                                left: 100%;
                            }

                            .theater-btn:hover {
                                transform: translateY(-3px);
                                box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
                                border-color: #e50914;
                            }

                            .theater-btn.active {
                                border-color: #e50914;
                                color: #e50914;
                                background: linear-gradient(135deg, #fff5f5 0%, #ffe5e5 100%);
                                font-weight: bold;
                                box-shadow: 0 4px 15px rgba(229, 9, 20, 0.3);
                            }

                            /* Improved Dropdown Styles */
                            .dropdown .btn-outline-secondary {
                                border: 2px solid #ddd;
                                border-radius: 10px;
                                transition: all 0.3s ease;
                            }

                            .dropdown .btn-outline-secondary:hover {
                                border-color: #667eea;
                                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                color: white;
                                transform: translateY(-2px);
                                box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
                            }

                            .dropdown-menu {
                                border-radius: 10px;
                                border: 1px solid rgba(0, 0, 0, 0.1);
                                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
                                padding: 8px;
                            }

                            .dropdown-item {
                                border-radius: 6px;
                                margin: 2px 0;
                                transition: all 0.2s ease;
                            }

                            .dropdown-item:hover {
                                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                color: white;
                                transform: translateX(5px);
                            }

                            /* Improved Booking Form Container */
                            .booking-form-container {
                                background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
                                padding: 30px;
                                border-radius: 15px;
                                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
                            }

                            .booking-header {
                                text-align: center;
                                margin-bottom: 30px;
                                padding-bottom: 20px;
                                border-bottom: 2px solid rgba(255, 255, 255, 0.1);
                            }

                            .booking-form-title {
                                color: #fff;
                                font-size: 28px;
                                font-weight: bold;
                                margin-bottom: 10px;
                                background: linear-gradient(135deg, #fff 0%, #e50914 100%);
                                -webkit-background-clip: text;
                                -webkit-text-fill-color: transparent;
                                background-clip: text;
                            }

                            .booking-subtitle {
                                color: rgba(255, 255, 255, 0.7);
                                font-size: 14px;
                            }

                            /* Food Items Luxury Styles */
                            .food-items-luxury-section {
                                position: relative;
                                z-index: 3;
                            }

                            .food-luxury-card {
                                position: relative;
                                overflow: hidden;
                            }

                            .food-luxury-card::before {
                                content: '';
                                position: absolute;
                                top: 0;
                                left: 0;
                                right: 0;
                                bottom: 0;
                                background: linear-gradient(135deg, rgba(212, 175, 55, 0.05) 0%, transparent 50%);
                                pointer-events: none;
                                z-index: 0;
                            }

                            .food-item-luxury-card {
                                position: relative;
                            }

                            .food-item-luxury-card:hover {
                                transform: translateY(-3px);
                                border-color: rgba(212, 175, 55, 0.5) !important;
                                box-shadow: 0 10px 30px rgba(212, 175, 55, 0.2);
                            }

                            .food-item-luxury-card:hover .food-item-luxury-glow {
                                opacity: 1;
                            }

                            .food-toggle-luxury:hover {
                                background: rgba(212, 175, 55, 0.2) !important;
                                border-color: #d4af37 !important;
                                transform: scale(1.05);
                            }

                            .food-qty-btn:hover {
                                background: rgba(212, 175, 55, 0.3) !important;
                                border-color: #d4af37 !important;
                                transform: scale(1.1);
                            }

                            .food-qty-btn:active {
                                transform: scale(0.95);
                            }

                            .food-quantity-input-luxury:focus {
                                outline: none;
                                border-color: #d4af37 !important;
                                box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
                                background: rgba(255, 255, 255, 0.15) !important;
                            }

                            .food-items-luxury-grid::-webkit-scrollbar {
                                width: 6px;
                            }

                            .food-items-luxury-grid::-webkit-scrollbar-track {
                                background: rgba(255, 255, 255, 0.05);
                                border-radius: 10px;
                            }

                            .food-items-luxury-grid::-webkit-scrollbar-thumb {
                                background: rgba(212, 175, 55, 0.5);
                                border-radius: 10px;
                            }

                            .food-items-luxury-grid::-webkit-scrollbar-thumb:hover {
                                background: rgba(212, 175, 55, 0.7);
                            }
                        </style>
                        <!-- Movie Title -->
                        <h1 class="movie-title-booking mb-3" itemprop="name"><?php echo htmlspecialchars($movie['title']); ?></h1>

                        <!-- IMDb Rating -->
                        <div class="movie-rating mb-3" itemprop="aggregateRating" itemscope itemtype="https://schema.org/AggregateRating">
                            <i class="fas fa-star text-warning"></i>
                            <span class="imdb-rating">
                                IMDb <span itemprop="ratingValue"><?php echo number_format($movie['rating'] * 1.1, 1); ?></span>
                            </span>
                            <?php if (isset($movie['duration']) && $movie['duration']): ?>
                                <span class="movie-duration ms-3" style="color: white; font-size: 0.95rem;">
                                    <i class="fas fa-clock"></i> <?php echo $movie['duration']; ?> phút
                                </span>
                            <?php endif; ?>
                        </div>

                        <!-- Categories -->
                        <?php if ($movie['category_name']): ?>
                            <div class="movie-categories mb-3">
                                <span class="category-badge" itemprop="genre"><?php echo htmlspecialchars($movie['category_name']); ?></span>
                            </div>
                        <?php endif; ?>

                        <!-- Description -->
                        <?php if ($movie['description']): ?>
                            <p class="movie-description mb-4" itemprop="description">
                                <?php echo htmlspecialchars($movie['description']); ?>
                            </p>
                        <?php endif; ?>

                        <!-- Trailer Button -->
                        <?php if ($movie['trailer_url']): ?>
                            <a href="<?php echo htmlspecialchars($movie['trailer_url']); ?>"
                                target="_blank"
                                class="btn-trailer"
                                rel="noopener noreferrer"
                                aria-label="Xem trailer phim <?php echo htmlspecialchars($movie['title']); ?>">
                                <i class="fas fa-play me-2"></i> Xem Trailer
                            </a>
                        <?php endif; ?>
                    </article>

                    <!-- Combo & Đồ ăn Section - Luxury Design (Góc trái phía dưới) - Hidden, will show in modal -->
                    <?php if ($selected_showtime_id && !empty($foodItems)): ?>
                        <div class="food-items-luxury-section" style="display: none; margin-top: 3rem; position: relative; z-index: 3;">
                            <div class="food-luxury-card" style="background: linear-gradient(135deg, rgba(26, 26, 46, 0.95) 0%, rgba(22, 33, 62, 0.95) 100%); border: 2px solid rgba(212, 175, 55, 0.3); border-radius: 20px; padding: 25px; box-shadow: 0 15px 50px rgba(0, 0, 0, 0.5); backdrop-filter: blur(10px);">
                                <div class="food-luxury-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid rgba(212, 175, 55, 0.2);">
                                    <h3 style="color: #d4af37; font-size: 1.5rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 10px;">
                                        <i class="fas fa-utensils" style="color: #d4af37;"></i>
                                        <span>Combo & Đồ ăn</span>
                                    </h3>
                                    <button type="button" class="food-toggle-luxury" id="foodToggleBtnLuxury" style="background: transparent; border: 1px solid rgba(212, 175, 55, 0.5); color: #d4af37; padding: 8px 15px; border-radius: 10px; cursor: pointer; transition: all 0.3s;">
                                        <i class="fas fa-chevron-down" id="foodToggleIconLuxury"></i>
                                    </button>
                                </div>

                                <div class="food-items-luxury-grid" id="foodItemsGridLuxury" style="display: none; grid-template-columns: 1fr; gap: 15px; max-height: 500px; overflow-y: auto; padding-right: 10px;">
                                    <?php foreach ($foodItems as $item): ?>
                                        <div class="food-item-luxury-card" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(212, 175, 55, 0.2); border-radius: 15px; padding: 20px; transition: all 0.3s; position: relative; overflow: hidden;">
                                            <div class="food-item-luxury-glow" style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, transparent, #d4af37, transparent); opacity: 0; transition: opacity 0.3s;"></div>

                                            <div class="food-item-luxury-content" style="display: flex; gap: 15px;">
                                                <!-- Food Image -->
                                                <div class="food-item-luxury-image" style="flex-shrink: 0;">
                                                    <?php if ($item['image']): ?>
                                                        <img src="<?php echo htmlspecialchars($item['image']); ?>"
                                                            alt="<?php echo htmlspecialchars($item['name']); ?>"
                                                            style="width: 100px; height: 100px; object-fit: cover; border-radius: 12px; border: 2px solid rgba(212, 175, 55, 0.3); box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);">
                                                    <?php else: ?>
                                                        <div style="width: 100px; height: 100px; background: linear-gradient(135deg, rgba(212, 175, 55, 0.2), rgba(212, 175, 55, 0.1)); border-radius: 12px; border: 2px solid rgba(212, 175, 55, 0.3); display: flex; align-items: center; justify-content: center;">
                                                            <i class="fas fa-utensils" style="font-size: 2rem; color: rgba(212, 175, 55, 0.5);"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>

                                                <!-- Food Info -->
                                                <div class="food-item-luxury-info" style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                                                    <div>
                                                        <h4 style="color: #fff; font-size: 1.1rem; font-weight: 600; margin: 0 0 8px 0; display: flex; align-items: center; gap: 8px; flex-wrap: wrap;">
                                                            <?php echo htmlspecialchars($item['name']); ?>
                                                            <span class="food-type-badge" style="font-size: 0.7rem; padding: 3px 8px; border-radius: 6px; background: <?php
                                                                                                                                                                        echo $item['type'] === 'combo' ? 'rgba(212, 175, 55, 0.2)' : ($item['type'] === 'snack' ? 'rgba(255, 193, 7, 0.2)' : 'rgba(23, 162, 184, 0.2)');
                                                                                                                                                                        ?>; color: <?php
                                                                echo $item['type'] === 'combo' ? '#d4af37' : ($item['type'] === 'snack' ? '#ffc107' : '#17a2b8');
                                                                ?>; border: 1px solid <?php
                                                                            echo $item['type'] === 'combo' ? 'rgba(212, 175, 55, 0.5)' : ($item['type'] === 'snack' ? 'rgba(255, 193, 7, 0.5)' : 'rgba(23, 162, 184, 0.5)');
                                                                            ?>;">
                                                                <?php
                                                                echo $item['type'] === 'combo' ? 'Combo' : ($item['type'] === 'snack' ? 'Snack' : 'Đồ uống');
                                                                ?>
                                                            </span>
                                                        </h4>
                                                        <?php if ($item['description']): ?>
                                                            <p style="color: rgba(255, 255, 255, 0.7); font-size: 0.9rem; margin: 0 0 10px 0; line-height: 1.4;">
                                                                <?php echo htmlspecialchars($item['description']); ?>
                                                            </p>
                                                        <?php endif; ?>
                                                    </div>

                                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 15px; flex-wrap: wrap;">
                                                        <div class="food-item-luxury-price" style="color: #d4af37; font-size: 1.3rem; font-weight: 700; text-shadow: 0 2px 10px rgba(212, 175, 55, 0.3);">
                                                            <?php echo number_format($item['price']); ?>₫
                                                        </div>

                                                        <div class="food-item-luxury-quantity" style="display: flex; align-items: center; gap: 10px;">
                                                            <button type="button" class="food-qty-btn" data-action="decrease" data-item-id="<?php echo $item['id']; ?>" style="width: 35px; height: 35px; border-radius: 8px; border: 1px solid rgba(212, 175, 55, 0.5); background: rgba(212, 175, 55, 0.1); color: #d4af37; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                                                <i class="fas fa-minus"></i>
                                                            </button>
                                                            <input type="number"
                                                                name="food_items[<?php echo $item['id']; ?>]"
                                                                value="0"
                                                                min="0"
                                                                max="10"
                                                                class="food-quantity-input-luxury"
                                                                data-price="<?php echo $item['price']; ?>"
                                                                data-item-id="<?php echo $item['id']; ?>"
                                                                style="width: 60px; text-align: center; padding: 8px; border-radius: 8px; border: 1px solid rgba(212, 175, 55, 0.5); background: rgba(255, 255, 255, 0.1); color: #fff; font-weight: 600; font-size: 1rem;">
                                                            <button type="button" class="food-qty-btn" data-action="increase" data-item-id="<?php echo $item['id']; ?>" style="width: 35px; height: 35px; border-radius: 8px; border: 1px solid rgba(212, 175, 55, 0.5); background: rgba(212, 175, 55, 0.1); color: #d4af37; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; font-weight: bold;">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="booking-movie-info booking-movie-empty">
                        <div class="empty-movie-state">
                            <div class="empty-icon-wrapper">
                                <i class="fas fa-film"></i>
                            </div>
                            <h3 class="empty-title">Vui lòng chọn phim để đặt vé</h3>
                            <p class="empty-description">
                                Chọn một bộ phim từ danh sách bên phải để xem thông tin chi tiết và đặt vé xem phim tại rạp.
                            </p>
                            <div class="empty-features">
                                <div class="feature-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Chọn rạp và suất chiếu</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Chọn ghế ngồi ưa thích</span>
                                </div>
                                <div class="feature-item">
                                    <i class="fas fa-check-circle"></i>
                                    <span>Thanh toán nhanh chóng</span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>


            <!-- Right Column: Booking Form -->
            <div class="col-lg-7">
                <div class="booking-form-container">
                    <header class="booking-header">
                        <h2 class="booking-form-title">Đặt vé xem phim</h2>
                        <p class="booking-subtitle">Chọn phim, rạp, ngày giờ và ghế ngồi của bạn</p>
                    </header>

                    <!-- Reservation Timer - Hiển thị ngay khi chọn showtime -->
                    <?php if ($selected_showtime_id): ?>
                        <div id="reservation-timer" class="reservation-timer mb-3" style="display: block;">
                            <div class="alert alert-warning" style="margin: 0 -2.5rem 1rem -2.5rem; padding: 1rem 2.5rem; border-radius: 0; width: calc(100% + 5rem); border-left: none; border-right: none;">
                                <i class="fas fa-clock me-2"></i>
                                <strong>Thời gian giữ ghế:</strong>
                                <span id="timer-countdown">10:00</span>
                                <small class="ms-2">(Bạn có 10 phút để hoàn tất thanh toán)</small>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Movies List - chỉ hiển thị khi chưa chọn phim -->
                    <?php if (empty($selected_movie)): ?>
                        <div class="booking-step mb-4">
                            <label class="booking-label">
                                <i class="fas fa-film me-2"></i>Danh sách phim đang chiếu
                            </label>
                            <?php if (empty($allMovies)): ?>
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Hiện tại chưa có phim nào đang chiếu rạp. Vui lòng quay lại sau!
                                </div>
                            <?php else: ?>
                                <div class="movies-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; margin-top: 15px;">
                                    <?php foreach ($allMovies as $m): ?>
                                        <a href="?route=booking/index&movie=<?php echo $m['id']; ?>"
                                            class="movie-card-booking"
                                            onclick="sessionStorage.setItem('bookingScrollPos', window.pageYOffset || document.documentElement.scrollTop); return true;"
                                            style="display: block; text-decoration: none; border: 2px solid #ddd; border-radius: 8px; overflow: hidden; transition: all 0.3s; background: white; cursor: pointer; position: relative; z-index: 1;"
                                            onmouseover="this.style.borderColor='#e50914'; this.style.transform='translateY(-5px)'; this.style.boxShadow='0 5px 15px rgba(0,0,0,0.2)';"
                                            onmouseout="this.style.borderColor='#ddd'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                            <?php if ($m['thumbnail']): ?>
                                                <img src="<?php echo htmlspecialchars($m['thumbnail']); ?>"
                                                    alt="<?php echo htmlspecialchars($m['title']); ?>"
                                                    style="width: 100%; height: 200px; object-fit: cover; pointer-events: none;">
                                            <?php else: ?>
                                                <div style="width: 100%; height: 200px; background: #f0f0f0; display: flex; align-items: center; justify-content: center; pointer-events: none;">
                                                    <i class="fas fa-film" style="font-size: 48px; color: #999;"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div style="padding: 10px; pointer-events: none;">
                                                <h4 style="margin: 0; font-size: 14px; color: #333; font-weight: bold; text-align: center; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                    <?php echo htmlspecialchars($m['title']); ?>
                                                </h4>
                                                <?php if ($m['rating']): ?>
                                                    <div style="text-align: center; margin-top: 5px;">
                                                        <i class="fas fa-star text-warning" style="font-size: 12px;"></i>
                                                        <span style="font-size: 12px; color: #666;"><?php echo number_format($m['rating'], 1); ?></span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <!-- Theater Selection for selected movie -->
                        <?php if (empty($theaters)): ?>
                            <div class="alert alert-warning mb-4">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Hiện tại chưa có rạp nào có suất chiếu phim này. Vui lòng liên hệ quản trị viên!
                            </div>
                        <?php else: ?>
                            <div class="booking-step mb-4">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <label class="booking-label mb-0">
                                        <i class="fas fa-building me-2"></i>Chọn rạp cho phim này
                                    </label>
                                    <div id="userLocationDisplay" class="user-location-badge" style="display: none;">
                                        <i class="fas fa-map-marker-alt text-danger me-2"></i>
                                        <span id="locationText">Đang lấy vị trí...</span>
                                        <button type="button" class="btn btn-sm btn-link text-white p-0 ms-2" onclick="getUserLocation()" title="Làm mới vị trí">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </div>
                                </div>
                                <?php
                                // Lấy tỉnh của người dùng từ session/cookie (nếu có)
                                $userProvince = $_SESSION['user_province'] ?? $_COOKIE['user_province'] ?? null;

                                // Phân loại rạp: cùng tỉnh và khác tỉnh
                                $sameProvinceTheaters = [];
                                $otherProvinceTheaters = [];

                                // Hàm lấy tỉnh từ location string
                                $getProvinceFromLocation = function ($location) {
                                    if (empty($location)) return null;
                                    // Tỉnh thường ở cuối, có thể là "Tỉnh X" hoặc "Thành phố X"
                                    $parts = explode(',', $location);
                                    if (!empty($parts)) {
                                        $lastPart = trim(end($parts));
                                        // Loại bỏ "Tỉnh" hoặc "Thành phố" nếu có
                                        $lastPart = preg_replace('/^(Tỉnh|Thành phố)\s+/i', '', $lastPart);
                                        return $lastPart;
                                    }
                                    return null;
                                };

                                foreach ($theaters as $theater) {
                                    $theaterProvince = $getProvinceFromLocation($theater['location'] ?? '');

                                    // Nếu có vị trí người dùng và tỉnh khớp, cho vào cùng tỉnh
                                    if (
                                        $userProvince && $theaterProvince &&
                                        (stripos($userProvince, $theaterProvince) !== false ||
                                            stripos($theaterProvince, $userProvince) !== false)
                                    ) {
                                        $sameProvinceTheaters[] = $theater;
                                    } else if (!$userProvince) {
                                        // Nếu chưa có vị trí, tất cả rạp đều hiển thị
                                        $sameProvinceTheaters[] = $theater;
                                    } else {
                                        // Rạp khác tỉnh
                                        $otherProvinceTheaters[] = $theater;
                                    }
                                }

                                // Nếu không có rạp cùng tỉnh, hiển thị tất cả
                                if (empty($sameProvinceTheaters) && !empty($theaters)) {
                                    $sameProvinceTheaters = $theaters;
                                    $otherProvinceTheaters = [];
                                }

                                // Chỉ hiển thị 1-2 rạp gần nhất, còn lại cho vào "Xem thêm"
                                // Nếu có hơn 2 rạp tổng cộng, chỉ hiển thị 2 rạp đầu
                                $totalTheaters = count($theaters);
                                if ($totalTheaters > 2) {
                                    $displayTheaters = array_slice($sameProvinceTheaters, 0, 2); // Chỉ lấy 2 rạp đầu
                                    $remainingSameProvince = array_slice($sameProvinceTheaters, 2); // Các rạp cùng tỉnh còn lại
                                    $moreTheaters = array_merge($remainingSameProvince, $otherProvinceTheaters); // Gộp tất cả rạp còn lại
                                } else {
                                    // Nếu có 2 rạp trở xuống, hiển thị tất cả
                                    $displayTheaters = $sameProvinceTheaters;
                                    $moreTheaters = $otherProvinceTheaters;
                                }
                                ?>
                                <div class="theaters-list" role="group" aria-label="Danh sách rạp chiếu phim" style="display: flex; flex-wrap: wrap; gap: 10px; margin-top: 15px; align-items: flex-start;">
                                    <?php foreach ($displayTheaters as $theater): ?>
                                        <a href="?route=booking/index&movie=<?php echo $selected_movie; ?>&theater=<?php echo $theater['id']; ?>"
                                            class="theater-btn <?php echo $selected_theater == $theater['id'] ? 'active' : ''; ?>"
                                            data-theater-id="<?php echo $theater['id']; ?>"
                                            data-movie-id="<?php echo $selected_movie; ?>"
                                            onclick="event.preventDefault(); selectTheater(<?php echo $theater['id']; ?>, <?php echo $selected_movie; ?>); return false;"
                                            aria-pressed="<?php echo $selected_theater == $theater['id'] ? 'true' : 'false'; ?>"
                                            style="padding: 12px 20px; border: 2px solid <?php echo $selected_theater == $theater['id'] ? '#e50914' : '#ddd'; ?>; border-radius: 8px; text-decoration: none; color: <?php echo $selected_theater == $theater['id'] ? '#e50914' : '#333'; ?>; background: <?php echo $selected_theater == $theater['id'] ? '#fff5f5' : 'white'; ?>; transition: all 0.3s; font-weight: <?php echo $selected_theater == $theater['id'] ? 'bold' : 'normal'; ?>; position: relative; cursor: pointer;">
                                            <i class="fas fa-map-marker-alt me-2"></i>
                                            <?php echo htmlspecialchars($theater['name']); ?>
                                            <?php if (!empty($theater['location'])): ?>
                                                <span style="font-size: 12px; color: #666;"> - <?php echo htmlspecialchars($theater['location']); ?></span>
                                            <?php endif; ?>
                                            <?php if (isset($theater['distance']) && $theater['distance'] !== null): ?>
                                                <span style="font-size: 11px; color: #28a745; font-weight: bold; margin-left: 5px;">
                                                    <i class="fas fa-route"></i> <?php echo number_format($theater['distance'], 1); ?> km
                                                </span>
                                            <?php endif; ?>
                                        </a>
                                    <?php endforeach; ?>

                                    <?php if (!empty($moreTheaters) && count($moreTheaters) > 0): ?>
                                        <div class="more-theaters-container" style="position: relative; display: inline-block; z-index: 10000;">
                                            <button type="button" class="btn btn-outline-secondary theater-btn" id="moreTheatersBtn" onclick="toggleMoreTheaters()" style="padding: 12px 20px; border: 2px solid #ddd; border-radius: 8px; cursor: pointer; background: white; color: #333; min-width: 350px; width: 430px; position: relative; z-index: 10001;">
                                                <i class="fas fa-ellipsis-h me-2"></i>
                                                <span>Xem thêm (<?php echo count($moreTheaters); ?>)</span>
                                                <i class="fas fa-chevron-down ms-auto" id="moreTheatersIcon" style="margin-left: auto;"></i>
                                            </button>
                                            <div id="moreTheatersList" style="display: none; position: absolute; top: 100%; left: 0; margin-top: 5px; background: white; border: 2px solid #ddd; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.3); z-index: 10002; min-width: 350px; width: 430px; max-height: 400px; overflow-y: auto;">
                                                <?php foreach ($moreTheaters as $theater): ?>
                                                    <a class="more-theater-item <?php echo $selected_theater == $theater['id'] ? 'active' : ''; ?>"
                                                        href="?route=booking/index&movie=<?php echo $selected_movie; ?>&theater=<?php echo $theater['id']; ?>"
                                                        data-theater-id="<?php echo $theater['id']; ?>"
                                                        data-movie-id="<?php echo $selected_movie; ?>"
                                                        onclick="event.preventDefault(); selectTheater(<?php echo $theater['id']; ?>, <?php echo $selected_movie; ?>); toggleMoreTheaters(); return false;"
                                                        style="display: block; padding: 12px 20px; text-decoration: none; color: #333; border-bottom: 1px solid #eee; transition: background 0.2s; cursor: pointer;">
                                                        <i class="fas fa-map-marker-alt me-2" style="color: #e50914;"></i>
                                                        <strong><?php echo htmlspecialchars($theater['name']); ?></strong>
                                                        <?php if (!empty($theater['location'])): ?>
                                                            <span class="text-muted" style="font-size: 12px;"> - <?php echo htmlspecialchars($theater['location']); ?></span>
                                                        <?php endif; ?>
                                                        <?php if (isset($theater['distance']) && $theater['distance'] !== null): ?>
                                                            <span class="text-success ms-2" style="font-size: 11px; font-weight: bold;">
                                                                <i class="fas fa-route"></i> <?php echo number_format($theater['distance'], 1); ?> km
                                                            </span>
                                                        <?php endif; ?>
                                                    </a>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Date Selection - chỉ hiển thị khi đã chọn rạp -->
                        <?php if ($selected_theater): ?>
                            <div class="booking-step mb-4" id="date-selection-section">
                                <label class="booking-label">
                                    <i class="fas fa-calendar-alt me-2"></i>Chọn ngày
                                </label>
                                <div class="dates-scroll" role="group" aria-label="Chọn ngày chiếu">
                                    <?php foreach ($dates as $dateItem): ?>
                                        <a href="?route=booking/index&movie=<?php echo $selected_movie; ?>&theater=<?php echo $selected_theater; ?>&date=<?php echo $dateItem['value']; ?>"
                                            class="date-btn <?php echo $selected_date == $dateItem['value'] ? 'active' : ''; ?>"
                                            data-date="<?php echo $dateItem['value']; ?>"
                                            data-movie-id="<?php echo $selected_movie; ?>"
                                            data-theater-id="<?php echo $selected_theater; ?>"
                                            onclick="event.preventDefault(); selectDate('<?php echo $dateItem['value']; ?>', <?php echo $selected_movie; ?>, <?php echo $selected_theater; ?>); return false;"
                                            aria-pressed="<?php echo $selected_date == $dateItem['value'] ? 'true' : 'false'; ?>"
                                            aria-label="Chọn ngày <?php echo $dateItem['label']; ?>"
                                            style="cursor: pointer;">
                                            <span class="date-day"><?php echo $dateItem['day_name']; ?></span>
                                            <span class="date-number"><?php echo $dateItem['label']; ?></span>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Time Selection - chỉ hiển thị khi đã chọn ngày và rạp -->
                        <?php if ($selected_date && $selected_theater): ?>
                            <div class="booking-step mb-4">
                                <label class="booking-label">
                                    <i class="fas fa-clock me-2"></i>Chọn giờ chiếu
                                </label>
                                <div class="times-grid" role="group" aria-label="Chọn giờ chiếu phim">
                                    <?php if (empty($showtimes)): ?>
                                        <div class="no-showtimes">
                                            <i class="fas fa-clock"></i>
                                            <p>Không có suất chiếu nào trong ngày này</p>
                                        </div>
                                    <?php else: ?>
                                        <?php foreach ($showtimes as $showtime): ?>
                                            <a href="?route=booking/index&movie=<?php echo $selected_movie; ?>&theater=<?php echo $selected_theater; ?>&date=<?php echo $selected_date; ?>&showtime_id=<?php echo $showtime['id']; ?>"
                                                class="time-btn <?php echo $selected_showtime_id == $showtime['id'] ? 'active' : ''; ?>"
                                                data-showtime-id="<?php echo $showtime['id']; ?>"
                                                data-movie-id="<?php echo $selected_movie; ?>"
                                                data-theater-id="<?php echo $selected_theater; ?>"
                                                data-date="<?php echo $selected_date; ?>"
                                                onclick="event.preventDefault(); selectShowtime(<?php echo $showtime['id']; ?>, <?php echo $selected_movie; ?>, <?php echo $selected_theater; ?>, '<?php echo $selected_date; ?>'); return false;"
                                                aria-pressed="<?php echo $selected_showtime_id == $showtime['id'] ? 'true' : 'false'; ?>"
                                                aria-label="Chọn suất chiếu lúc <?php echo date('H:i', strtotime($showtime['show_time'])); ?>"
                                                style="cursor: pointer;">
                                                <?php echo date('H:i', strtotime($showtime['show_time'])); ?>
                                                <?php
                                                $st_type = $showtime['screen_type'] ?? '2D';
                                                $st_colors = ['2D' => '#4CAF50', '3D' => '#2196F3', 'IMAX' => '#9C27B0', '4DX' => '#FF5722'];
                                                $st_color = $st_colors[$st_type] ?? '#4CAF50';
                                                ?>
                                                <span class="time-screen-type" style="background: <?php echo $st_color; ?>; padding: 1px 5px; border-radius: 3px; font-size: 10px; margin-left: 4px;"><?php echo $st_type; ?></span>
                                                <span class="time-price"><?php echo number_format($showtime['price']); ?>₫</span>
                                            </a>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Thông tin rạp và phòng chiếu - Hiển thị khi chọn showtime -->
                        <?php if ($selected_showtime_id && ($screenInfo || $theaterInfo)): ?>
                            <div class="theater-screen-info mb-3" style="background: #1a1a1a; padding: 15px; border-radius: 8px; border: 1px solid #333;">
                                <div style="display: flex; align-items: center; gap: 20px; flex-wrap: wrap;">
                                    <?php if ($theaterInfo): ?>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <i class="fas fa-building" style="color: #e50914; font-size: 18px;"></i>
                                            <span style="color: #fff; font-weight: 600; font-size: 16px;">
                                                <?php echo htmlspecialchars($theaterInfo['name']); ?>
                                                <?php if ($theaterInfo['location']): ?>
                                                    <span style="color: #999; font-weight: normal; font-size: 14px;">- <?php echo htmlspecialchars($theaterInfo['location']); ?></span>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                    <?php if ($screenInfo): ?>
                                        <div style="display: flex; align-items: center; gap: 8px;">
                                            <i class="fas fa-door-open" style="color: #28a745; font-size: 18px;"></i>
                                            <span style="color: #fff; font-weight: 600; font-size: 16px;">
                                                <?php echo htmlspecialchars($screenInfo['screen_name']); ?>
                                            </span>
                                            <?php
                                            $screenTypeDisplay = $screenInfo['screen_type'] ?? '2D';
                                            $screenTypeColors = ['2D' => '#4CAF50', '3D' => '#2196F3', 'IMAX' => '#9C27B0', '4DX' => '#FF5722'];
                                            $stColor = $screenTypeColors[$screenTypeDisplay] ?? '#4CAF50';
                                            ?>
                                            <span style="background: <?php echo $stColor; ?>; color: #fff; padding: 3px 10px; border-radius: 4px; font-size: 12px; font-weight: 600;">
                                                <?php echo $screenTypeDisplay; ?>
                                            </span>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Seat Selection -->
                        <?php if ($selected_showtime_id): ?>
                            <?php
                            // Debug: Log booked seats passed to view
                            error_log("View - bookedSeats passed to view: " . print_r($bookedSeats ?? [], true));
                            error_log("View - reservedSeats passed to view: " . print_r($reservedSeats ?? [], true));
                            error_log("View - showtime_id: $selected_showtime_id");

                            // Đảm bảo bookedSeats là array
                            if (!is_array($bookedSeats)) {
                                $bookedSeats = [];
                            }
                            if (!is_array($reservedSeats)) {
                                $reservedSeats = [];
                            }

                            // Double check: Query lại từ BookingModel để đảm bảo lấy dữ liệu mới nhất
                            try {
                                require_once __DIR__ . '/../../models/BookingModel.php';
                                $bookingModel = new BookingModel();
                                $directQuery = $bookingModel->getBookedSeats($selected_showtime_id);
                                $directBookedSeats = array_column($directQuery, 'seat');
                                error_log("View - Direct database query for showtime $selected_showtime_id - seats: " . implode(', ', $directBookedSeats));

                                // Ưu tiên dữ liệu từ database (mới nhất) thay vì merge
                                // Vì có thể controller chưa cập nhật kịp
                                if (!empty($directBookedSeats)) {
                                    $bookedSeats = $directBookedSeats;
                                    error_log("View - Using direct query result as bookedSeats: " . implode(', ', $bookedSeats));
                                } else {
                                    // Nếu direct query rỗng nhưng controller có dữ liệu, vẫn dùng controller
                                    error_log("View - Direct query empty, using controller data: " . implode(', ', $bookedSeats));
                                }
                            } catch (Exception $e) {
                                error_log("Error in direct query: " . $e->getMessage());
                                // Nếu có lỗi, vẫn dùng dữ liệu từ controller
                            }

                            $showtime = null;
                            foreach ($showtimes as $st) {
                                if ($st['id'] == $selected_showtime_id) {
                                    $showtime = $st;
                                    break;
                                }
                            }
                            ?>
                            <div class="booking-step mb-4">
                                <label class="booking-label">
                                    <i class="fas fa-chair me-2"></i>Chọn ghế ngồi
                                </label>

                                <!-- Screen -->
                                <div class="cinema-screen mb-3" aria-label="Màn hình rạp chiếu phim">
                                    <div class="screen-text">MÀN HÌNH</div>
                                </div>

                                <!-- Seat Map -->
                                <form method="POST"
                                    action="?route=booking/process-booking"
                                    id="booking-form"
                                    aria-label="Form đặt vé xem phim"
                                    onsubmit="return validateBookingForm(event);">
                                    <input type="hidden" name="showtime_id" value="<?php echo $selected_showtime_id; ?>">

                                    <!-- Seat Validation Error Message - Bên trái khung ghế -->
                                    <div id="seat-validation-error" style="display: none; margin-bottom: 1rem; padding: 15px; background: rgba(255, 107, 107, 0.15); border-left: 4px solid #ff6b6b; border-radius: 8px; color: #ff6b6b; box-shadow: 0 2px 8px rgba(255, 107, 107, 0.2);">
                                        <div style="display: flex; align-items: center; gap: 10px;">
                                            <i class="fas fa-exclamation-triangle" style="font-size: 1.5rem;"></i>
                                            <div style="flex: 1;">
                                                <strong style="display: block; margin-bottom: 5px;">Lỗi đặt ghế:</strong>
                                                <span id="seat-validation-error-text"></span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="seat-map-container" role="group" aria-label="Bản đồ ghế ngồi trong rạp" <?php
                                                                                                                        // Tính số ghế tối đa để set data attribute
                                                                                                                        $maxSeats = 0;
                                                                                                                        if (isset($seat_groups) && is_array($seat_groups)) {
                                                                                                                            $tempRowColsMap = [];
                                                                                                                            foreach ($seat_groups as $group) {
                                                                                                                                $groupRows = $group['rows'] ?? [];
                                                                                                                                $groupCols = $group['cols'] ?? [];
                                                                                                                                foreach ($groupRows as $row) {
                                                                                                                                    if (!isset($tempRowColsMap[$row])) {
                                                                                                                                        $tempRowColsMap[$row] = [];
                                                                                                                                    }
                                                                                                                                    foreach ($groupCols as $col) {
                                                                                                                                        if (!in_array($col, $tempRowColsMap[$row])) {
                                                                                                                                            $tempRowColsMap[$row][] = $col;
                                                                                                                                        }
                                                                                                                                    }
                                                                                                                                }
                                                                                                                            }
                                                                                                                            foreach ($tempRowColsMap as $row => $cols) {
                                                                                                                                $count = count($cols);
                                                                                                                                if ($count > $maxSeats) {
                                                                                                                                    $maxSeats = $count;
                                                                                                                                }
                                                                                                                            }
                                                                                                                        } elseif (isset($cols)) {
                                                                                                                            $maxSeats = count($cols);
                                                                                                                        }
                                                                                                                        if ($maxSeats > 0) {
                                                                                                                            echo ' data-seats-per-row="' . $maxSeats . '" style="--seats-count: ' . $maxSeats . ';"';
                                                                                                                        }
                                                                                                                        ?>>
                                        <?php
                                        // Lấy seat layout từ config hoặc dùng default
                                        $layout = $seatLayout ?? [
                                            'rows' => ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'],
                                            'cols' => [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12],
                                            'vip_rows' => ['C', 'D', 'E'], // Chỉ hàng C, D, E là VIP (nằm ở giữa rạp)
                                            'couple_rows' => ['L'], // Hàng cuối là ghế đôi
                                            'normal_price' => 120000,
                                            'vip_price' => 180000,
                                            'couple_price' => 240000
                                        ];

                                        $rows = $layout['rows'] ?? ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L'];
                                        $cols = $layout['cols'] ?? range(1, 12);
                                        $vip_rows = $layout['vip_rows'] ?? [];
                                        $couple_rows = $layout['couple_rows'] ?? [];

                                        // Chỉ tự tính vip_rows nếu layout không có (rỗng)
                                        if (empty($vip_rows) && !empty($rows) && count($rows) >= 3) {
                                            $totalRows = count($rows);
                                            // Tính toán vị trí bắt đầu để có 3 hàng ở giữa
                                            $middleStartIndex = floor(($totalRows - 3) / 2);
                                            $vip_rows = array_slice($rows, $middleStartIndex, 3); // Lấy 3 hàng ở giữa

                                            // Loại bỏ hàng cuối khỏi vip_rows nếu có (vì hàng cuối là ghế đôi)
                                            $lastRow = end($rows);
                                            $vip_rows = array_filter($vip_rows, function ($row) use ($lastRow) {
                                                return $row !== $lastRow;
                                            });
                                            $vip_rows = array_values($vip_rows); // Reset keys

                                            // Nếu sau khi loại bỏ hàng cuối, còn ít hơn 3 hàng, lấy thêm hàng từ phía trên
                                            if (count($vip_rows) < 3 && $middleStartIndex > 0) {
                                                $needed = 3 - count($vip_rows);
                                                $additionalRows = array_slice($rows, max(0, $middleStartIndex - $needed), $needed);
                                                $vip_rows = array_merge($additionalRows, $vip_rows);
                                                $vip_rows = array_unique($vip_rows);
                                                $vip_rows = array_values($vip_rows);
                                            }
                                        }

                                        // Nếu không có config couple_rows, đặt hàng cuối là ghế đôi
                                        if (empty($couple_rows) && !empty($rows)) {
                                            $couple_rows = [end($rows)];
                                        }

                                        // Đảm bảo hàng cuối cùng luôn là ghế đôi (cho tất cả các phòng)
                                        if (!empty($rows)) {
                                            $lastRow = end($rows);
                                            if (!in_array($lastRow, $couple_rows)) {
                                                $couple_rows[] = $lastRow;
                                            }
                                            // Loại bỏ hàng cuối khỏi vip_rows nếu có
                                            $vip_rows = array_filter($vip_rows, function ($row) use ($lastRow) {
                                                return $row !== $lastRow;
                                            });
                                        }

                                        // Kiểm tra và loại bỏ các hàng ghế đôi không hợp lệ (ít hơn 2 ghế)
                                        $validCoupleRows = [];
                                        foreach ($couple_rows as $coupleRow) {
                                            // Kiểm tra số ghế trong hàng
                                            $rowSeatCount = 0;
                                            if (isset($seat_groups) && is_array($seat_groups)) {
                                                // Đếm số ghế trong hàng từ seat_groups
                                                foreach ($seat_groups as $group) {
                                                    $groupRows = $group['rows'] ?? [];
                                                    $groupCols = $group['cols'] ?? [];
                                                    if (in_array($coupleRow, $groupRows)) {
                                                        $rowSeatCount += count($groupCols);
                                                    }
                                                }
                                            } else {
                                                // Layout tiêu chuẩn: đếm từ $cols
                                                $rowSeatCount = count($cols);
                                            }

                                            // Chỉ thêm vào danh sách nếu có ít nhất 2 ghế
                                            if ($rowSeatCount >= 2) {
                                                $validCoupleRows[] = $coupleRow;
                                            }
                                        }
                                        $couple_rows = $validCoupleRows;

                                        require_once __DIR__ . '/../../models/BookingModel.php';
                                        $bookingModel = new BookingModel();

                                        // Kiểm tra xem có layout phức tạp không (có seat_groups hoặc row_configs)
                                        $layout_type = $layout['layout_type'] ?? 'standard';
                                        $seat_groups = $layout['seat_groups'] ?? null;
                                        $row_configs = $layout['row_configs'] ?? null;

                                        // Nếu có row_configs (custom_groups), chuyển đổi thành seat_groups
                                        if ($layout_type === 'custom_groups' && $row_configs && is_array($row_configs)) {
                                            // Lấy VIP config từ layout
                                            $vip_cols_start = $layout['vip_cols_start'] ?? 9;
                                            $vip_cols_end = $layout['vip_cols_end'] ?? 20;
                                            $vip_rows_config = $layout['vip_rows'] ?? [];

                                            // Override vip_rows từ config
                                            if (!empty($vip_rows_config)) {
                                                $vip_rows = $vip_rows_config;
                                            }
                                        }

                                        // Nếu có seat_groups hoặc row_configs, render theo layout phức tạp
                                        if (($seat_groups && is_array($seat_groups)) || ($row_configs && is_array($row_configs))) {
                                            // Tạo map để lưu các cột của mỗi hàng từ các nhóm
                                            $rowColsMap = [];
                                            $rowGroupsMap = []; // Lưu thông tin nhóm cho mỗi hàng
                                            $maxSeatsPerRow = 0;

                                            // Nếu có row_configs, xử lý theo cấu trúc mới
                                            if ($row_configs && is_array($row_configs)) {
                                                foreach ($row_configs as $row => $config) {
                                                    $rowColsMap[$row] = [];
                                                    $rowGroupsMap[$row] = $config['groups'] ?? [];

                                                    foreach ($config['groups'] ?? [] as $group) {
                                                        foreach ($group['cols'] ?? [] as $col) {
                                                            if (!in_array($col, $rowColsMap[$row])) {
                                                                $rowColsMap[$row][] = $col;
                                                            }
                                                        }
                                                    }
                                                }
                                            } elseif ($seat_groups && is_array($seat_groups)) {
                                                // Xử lý seat_groups cũ
                                                foreach ($seat_groups as $group) {
                                                    $groupRows = $group['rows'] ?? [];
                                                    $groupCols = $group['cols'] ?? [];

                                                    foreach ($groupRows as $row) {
                                                        if (!isset($rowColsMap[$row])) {
                                                            $rowColsMap[$row] = [];
                                                        }
                                                        // Thêm các cột từ nhóm này vào hàng
                                                        foreach ($groupCols as $col) {
                                                            if (!in_array($col, $rowColsMap[$row])) {
                                                                $rowColsMap[$row][] = $col;
                                                            }
                                                        }
                                                    }
                                                }
                                            }

                                            // Tính số ghế tối đa trong một hàng (bao gồm cả separators)
                                            foreach ($rowColsMap as $row => $cols) {
                                                $seatCount = count($cols);
                                                // Đếm số nhóm trong hàng này để tính separators
                                                $groupCount = 0;

                                                if (isset($rowGroupsMap[$row]) && !empty($rowGroupsMap[$row])) {
                                                    // Sử dụng row_configs
                                                    $groupCount = count($rowGroupsMap[$row]);
                                                } elseif ($seat_groups && is_array($seat_groups)) {
                                                    // Sử dụng seat_groups cũ
                                                    $prevGroupIndex = null;
                                                    foreach ($seat_groups as $groupIndex => $group) {
                                                        $groupRows = $group['rows'] ?? [];
                                                        $groupCols = $group['cols'] ?? [];
                                                        if (in_array($row, $groupRows)) {
                                                            $hasSeatsInRow = false;
                                                            foreach ($groupCols as $col) {
                                                                if (in_array($col, $cols)) {
                                                                    $hasSeatsInRow = true;
                                                                    break;
                                                                }
                                                            }
                                                            if ($hasSeatsInRow && $prevGroupIndex !== $groupIndex) {
                                                                $groupCount++;
                                                                $prevGroupIndex = $groupIndex;
                                                            }
                                                        }
                                                    }
                                                }

                                                // Mỗi separator chiếm khoảng 1.5rem (tương đương ~1.5 ghế)
                                                $totalWidth = $seatCount + ($groupCount > 1 ? ($groupCount - 1) * 1.5 : 0);
                                                if ($totalWidth > $maxSeatsPerRow) {
                                                    $maxSeatsPerRow = ceil($totalWidth);
                                                }
                                            }

                                            // Sắp xếp các hàng và cột
                                            ksort($rowColsMap);

                                            // Tính toán 3 hàng ở giữa rạp làm VIP (tự động, không cố định)
                                            $allRows = array_keys($rowColsMap);
                                            // Chỉ tự tính vip_rows nếu layout không có (rỗng)
                                            if (empty($vip_rows) && !empty($allRows) && count($allRows) >= 3) {
                                                $totalRows = count($allRows);
                                                // Tính toán vị trí bắt đầu để có 3 hàng ở giữa
                                                $middleStartIndex = floor(($totalRows - 3) / 2);
                                                $middleRows = array_slice($allRows, $middleStartIndex, 3); // Lấy 3 hàng ở giữa

                                                // Loại bỏ hàng cuối khỏi vip_rows nếu có (vì hàng cuối là ghế đôi)
                                                $lastRowInMap = end($allRows);
                                                $middleRows = array_filter($middleRows, function ($row) use ($lastRowInMap) {
                                                    return $row !== $lastRowInMap;
                                                });
                                                $middleRows = array_values($middleRows); // Reset keys

                                                // Nếu sau khi loại bỏ hàng cuối, còn ít hơn 3 hàng, lấy thêm hàng từ phía trên
                                                if (count($middleRows) < 3 && $middleStartIndex > 0) {
                                                    $needed = 3 - count($middleRows);
                                                    $additionalRows = array_slice($allRows, max(0, $middleStartIndex - $needed), $needed);
                                                    $middleRows = array_merge($additionalRows, $middleRows);
                                                    $middleRows = array_unique($middleRows);
                                                    $middleRows = array_values($middleRows);
                                                }

                                                $vip_rows = $middleRows;
                                            }

                                            // Xác định hàng cuối cùng và đảm bảo nó là ghế đôi
                                            if (!empty($allRows)) {
                                                $lastRowInMap = end($allRows);
                                                // Kiểm tra số ghế trong hàng cuối
                                                $lastRowSeatCount = isset($rowColsMap[$lastRowInMap]) ? count($rowColsMap[$lastRowInMap]) : 0;

                                                // Chỉ đánh dấu là ghế đôi nếu có ít nhất 2 ghế
                                                if ($lastRowSeatCount >= 2) {
                                                    if (!in_array($lastRowInMap, $couple_rows)) {
                                                        $couple_rows[] = $lastRowInMap;
                                                    }
                                                    // Loại bỏ hàng cuối khỏi vip_rows nếu có
                                                    $vip_rows = array_filter($vip_rows, function ($row) use ($lastRowInMap) {
                                                        return $row !== $lastRowInMap;
                                                    });
                                                } else {
                                                    // Nếu hàng cuối có ít hơn 2 ghế, loại bỏ khỏi couple_rows nếu có
                                                    $couple_rows = array_filter($couple_rows, function ($row) use ($lastRowInMap) {
                                                        return $row !== $lastRowInMap;
                                                    });
                                                }
                                            }

                                            // Loại bỏ các hàng ghế đôi không hợp lệ (ít hơn 2 ghế) khỏi danh sách
                                            $validCoupleRows = [];
                                            foreach ($couple_rows as $coupleRow) {
                                                if (isset($rowColsMap[$coupleRow]) && count($rowColsMap[$coupleRow]) >= 2) {
                                                    $validCoupleRows[] = $coupleRow;
                                                }
                                            }
                                            $couple_rows = $validCoupleRows;

                                            foreach ($rowColsMap as $row => $cols) {
                                                // Sắp xếp cột theo thứ tự trong row_configs hoặc seat_groups
                                                $sortedCols = [];
                                                $colToGroupMap = [];

                                                if (isset($rowGroupsMap[$row]) && !empty($rowGroupsMap[$row])) {
                                                    // Sử dụng row_configs
                                                    foreach ($rowGroupsMap[$row] as $groupIndex => $group) {
                                                        foreach ($group['cols'] ?? [] as $col) {
                                                            if (in_array($col, $cols) && !in_array($col, $sortedCols)) {
                                                                $sortedCols[] = $col;
                                                                $colToGroupMap[$col] = $groupIndex;
                                                            }
                                                        }
                                                    }
                                                } elseif ($seat_groups && is_array($seat_groups)) {
                                                    // Sử dụng seat_groups cũ
                                                    foreach ($seat_groups as $groupIndex => $group) {
                                                        $groupRows = $group['rows'] ?? [];
                                                        $groupCols = $group['cols'] ?? [];
                                                        if (in_array($row, $groupRows)) {
                                                            foreach ($groupCols as $col) {
                                                                if (in_array($col, $cols) && !in_array($col, $sortedCols)) {
                                                                    $sortedCols[] = $col;
                                                                    $colToGroupMap[$col] = $groupIndex;
                                                                }
                                                            }
                                                        }
                                                    }
                                                }

                                                // Kiểm tra VIP theo cột (cho custom_groups)
                                                $vip_cols_start = $layout['vip_cols_start'] ?? null;
                                                $vip_cols_end = $layout['vip_cols_end'] ?? null;

                                                $isVipRow = in_array($row, $vip_rows);
                                                $isCoupleRow = in_array($row, $couple_rows);

                                                echo '<div class="seat-row ' . ($isCoupleRow ? 'couple-seat-row' : '') . ($isVipRow ? ' vip-row' : '') . '">';
                                                echo '<span class="row-label">' . $row . '</span>';
                                                echo '<div class="seats-in-row">';

                                                // Render các cột đã sắp xếp với khoảng cách giữa các nhóm
                                                if ($isCoupleRow) {
                                                    // Render ghế đôi cho hàng cuối
                                                    $prevGroupIndex = null;
                                                    for ($i = 0; $i < count($sortedCols); $i += 2) {
                                                        if ($i + 1 < count($sortedCols)) {
                                                            $col1 = $sortedCols[$i];
                                                            $col2 = $sortedCols[$i + 1];
                                                            $currentGroupIndex1 = $colToGroupMap[$col1] ?? null;
                                                            $currentGroupIndex2 = $colToGroupMap[$col2] ?? null;

                                                            // Thêm khoảng cách nếu chuyển sang nhóm mới
                                                            if ($prevGroupIndex !== null && $currentGroupIndex1 !== $prevGroupIndex) {
                                                                echo '<span class="seat-group-separator" style="width: 1.5rem; display: inline-block;"></span>';
                                                            }

                                                            $seat1 = $row . $col1;
                                                            $seat2 = $row . $col2;
                                                            $isBooked1 = in_array($seat1, $bookedSeats ?? []);
                                                            $isBooked2 = in_array($seat2, $bookedSeats ?? []);
                                                            $isReserved1 = in_array($seat1, $reservedSeats ?? []);
                                                            $isReserved2 = in_array($seat2, $reservedSeats ?? []);
                                                            $isBooked = $isBooked1 || $isBooked2;
                                                            $isReserved = $isReserved1 || $isReserved2;

                                                            $seatClass = 'available';
                                                            if ($isBooked) {
                                                                $seatClass = 'booked';
                                                            } elseif ($isReserved) {
                                                                $seatClass = 'reserved';
                                                            }

                                                            echo '<label class="seat-label couple-seat ' . $seatClass . '" data-seat="' . $seat1 . '" title="Ghế đôi ' . $col1 . '-' . $col2 . '">';
                                                            if (!$isBooked && !$isReserved) {
                                                                echo '<input type="checkbox" name="seats[]" value="' . $seat1 . '" class="seat-checkbox couple-seat-checkbox" data-couple-seat="' . $seat2 . '">';
                                                                echo '<input type="checkbox" name="seats[]" value="' . $seat2 . '" class="seat-checkbox couple-seat-checkbox" data-couple-seat="' . $seat1 . '">';
                                                            }
                                                            echo '<span class="seat-number">' . $col1 . '-' . $col2 . '</span>';
                                                            echo '<span class="couple-icon"><i class="fas fa-heart"></i></span>';
                                                            echo '</label>';

                                                            $prevGroupIndex = $currentGroupIndex2 ?? $currentGroupIndex1;
                                                        } else {
                                                            // Nếu số ghế lẻ, render ghế cuối cùng như ghế đơn
                                                            $col = $sortedCols[$i];
                                                            $currentGroupIndex = $colToGroupMap[$col] ?? null;

                                                            if ($prevGroupIndex !== null && $currentGroupIndex !== $prevGroupIndex) {
                                                                echo '<span class="seat-group-separator" style="width: 1.5rem; display: inline-block;"></span>';
                                                            }

                                                            $seat = $row . $col;
                                                            $isBooked = in_array($seat, $bookedSeats ?? []);
                                                            $isReserved = in_array($seat, $reservedSeats ?? []);

                                                            $seatClass = 'available';
                                                            if ($isBooked) {
                                                                $seatClass = 'booked';
                                                            } elseif ($isReserved) {
                                                                $seatClass = 'reserved';
                                                            }

                                                            if ($isVipRow) {
                                                                $seatClass .= ' vip-seat';
                                                            }

                                                            echo '<label class="seat-label ' . $seatClass . '" data-seat="' . $seat . '" data-seat-type="' . ($isVipRow ? 'vip' : 'normal') . '">';
                                                            if (!$isBooked && !$isReserved) {
                                                                echo '<input type="checkbox" name="seats[]" value="' . $seat . '" class="seat-checkbox" data-seat-type="' . ($isVipRow ? 'vip' : 'normal') . '">';
                                                            }
                                                            echo '<span class="seat-number">' . $col . '</span>';
                                                            if ($isVipRow) {
                                                                echo '<span class="seat-icon vip-icon" title="Ghế VIP"><i class="fas fa-crown"></i></span>';
                                                            } else {
                                                                echo '<span class="seat-icon normal-icon" title="Ghế thường"><i class="fas fa-chair"></i></span>';
                                                            }
                                                            echo '</label>';

                                                            $prevGroupIndex = $currentGroupIndex;
                                                        }
                                                    }
                                                } else {
                                                    // Render ghế đơn
                                                    $prevGroupIndex = null;
                                                    foreach ($sortedCols as $index => $col) {
                                                        $currentGroupIndex = $colToGroupMap[$col] ?? null;

                                                        // Thêm khoảng cách nếu chuyển sang nhóm mới
                                                        if ($prevGroupIndex !== null && $currentGroupIndex !== $prevGroupIndex) {
                                                            echo '<span class="seat-group-separator" style="width: 1.5rem; display: inline-block;"></span>';
                                                        }

                                                        $seat = $row . $col;
                                                        $isBooked = in_array($seat, $bookedSeats ?? []);
                                                        $isReserved = in_array($seat, $reservedSeats ?? []);

                                                        // Kiểm tra VIP: theo hàng VÀ theo cột (nếu có config)
                                                        $isVipSeat = $isVipRow;
                                                        if ($vip_cols_start !== null && $vip_cols_end !== null) {
                                                            // VIP chỉ khi thuộc hàng VIP VÀ cột trong khoảng VIP
                                                            $isVipSeat = $isVipRow && ($col >= $vip_cols_start && $col <= $vip_cols_end);
                                                        }

                                                        $seatClass = 'available';
                                                        if ($isBooked) {
                                                            $seatClass = 'booked';
                                                        } elseif ($isReserved) {
                                                            $seatClass = 'reserved';
                                                        }

                                                        if ($isVipSeat) {
                                                            $seatClass .= ' vip-seat';
                                                        }

                                                        $seatType = $isVipSeat ? 'vip' : 'normal';
                                                        echo '<label class="seat-label ' . $seatClass . '" data-seat="' . $seat . '" data-seat-type="' . $seatType . '">';
                                                        if (!$isBooked && !$isReserved) {
                                                            echo '<input type="checkbox" name="seats[]" value="' . $seat . '" class="seat-checkbox" data-seat-type="' . $seatType . '">';
                                                        }
                                                        echo '<span class="seat-number">' . $col . '</span>';
                                                        if ($isVipSeat) {
                                                            echo '<span class="seat-icon vip-icon" title="Ghế VIP"><i class="fas fa-crown"></i></span>';
                                                        } else {
                                                            echo '<span class="seat-icon normal-icon" title="Ghế thường"><i class="fas fa-chair"></i></span>';
                                                        }
                                                        echo '</label>';

                                                        $prevGroupIndex = $currentGroupIndex;
                                                    }
                                                }

                                                echo '</div>';
                                                echo '</div>';
                                            }
                                        } else {
                                            // Layout tiêu chuẩn
                                            foreach ($rows as $row) {
                                                $isVipRow = in_array($row, $vip_rows);
                                                $isCoupleRow = in_array($row, $couple_rows);

                                                echo '<div class="seat-row ' . ($isCoupleRow ? 'couple-seat-row' : '') . ($isVipRow ? ' vip-row' : '') . '">';
                                                echo '<span class="row-label">' . $row . '</span>';
                                                echo '<div class="seats-in-row">';

                                                // Nếu là hàng ghế đôi
                                                if ($isCoupleRow) {
                                                    // Tạo ghế đôi từ các ghế liên tiếp
                                                    for ($i = 0; $i < count($cols); $i += 2) {
                                                        if ($i + 1 < count($cols)) {
                                                            $col1 = $cols[$i];
                                                            $col2 = $cols[$i + 1];
                                                            $seat1 = $row . $col1;
                                                            $seat2 = $row . $col2;
                                                            $isBooked1 = in_array($seat1, $bookedSeats ?? []);
                                                            $isBooked2 = in_array($seat2, $bookedSeats ?? []);
                                                            $isReserved1 = in_array($seat1, $reservedSeats ?? []);
                                                            $isReserved2 = in_array($seat2, $reservedSeats ?? []);
                                                            $isBooked = $isBooked1 || $isBooked2;
                                                            $isReserved = $isReserved1 || $isReserved2;

                                                            $seatClass = 'available';
                                                            if ($isBooked) {
                                                                $seatClass = 'booked';
                                                            } elseif ($isReserved) {
                                                                $seatClass = 'reserved';
                                                            }

                                                            echo '<label class="seat-label couple-seat ' . $seatClass . '" data-seat="' . $seat1 . '" title="Ghế đôi ' . $col1 . '-' . $col2 . '">';
                                                            if (!$isBooked && !$isReserved) {
                                                                echo '<input type="checkbox" name="seats[]" value="' . $seat1 . '" class="seat-checkbox couple-seat-checkbox" data-couple-seat="' . $seat2 . '">';
                                                                echo '<input type="checkbox" name="seats[]" value="' . $seat2 . '" class="seat-checkbox couple-seat-checkbox" data-couple-seat="' . $seat1 . '">';
                                                            }
                                                            echo '<span class="seat-number">' . $col1 . '-' . $col2 . '</span>';
                                                            echo '<span class="couple-icon"><i class="fas fa-heart"></i></span>';
                                                            echo '</label>';
                                                        }
                                                    }
                                                } else {
                                                    // Các hàng ghế đơn
                                                    foreach ($cols as $col) {
                                                        $seat = $row . $col;
                                                        $isBooked = in_array($seat, $bookedSeats ?? []);
                                                        $isReserved = in_array($seat, $reservedSeats ?? []);

                                                        $seatClass = 'available';
                                                        if ($isBooked) {
                                                            $seatClass = 'booked';
                                                        } elseif ($isReserved) {
                                                            $seatClass = 'reserved';
                                                        }

                                                        if ($isVipRow) {
                                                            $seatClass .= ' vip-seat';
                                                        }

                                                        echo '<label class="seat-label ' . $seatClass . '" data-seat="' . $seat . '" data-seat-type="' . ($isVipRow ? 'vip' : 'normal') . '">';
                                                        if (!$isBooked && !$isReserved) {
                                                            echo '<input type="checkbox" name="seats[]" value="' . $seat . '" class="seat-checkbox" data-seat-type="' . ($isVipRow ? 'vip' : 'normal') . '">';
                                                        }
                                                        echo '<span class="seat-number">' . $col . '</span>';
                                                        if ($isVipRow) {
                                                            echo '<span class="seat-icon vip-icon" title="Ghế VIP"><i class="fas fa-crown"></i></span>';
                                                        } else {
                                                            echo '<span class="seat-icon normal-icon" title="Ghế thường"><i class="fas fa-chair"></i></span>';
                                                        }
                                                        echo '</label>';
                                                    }
                                                }

                                                echo '</div>';
                                                echo '</div>';
                                            }
                                        }
                                        ?>
                                    </div>

                                    <!-- Seat Legend -->
                                    <div class="seat-legend mt-3 mb-3" role="group" aria-label="Chú thích trạng thái ghế">
                                        <div class="legend-item">
                                            <span class="legend-seat available" aria-label="Ghế trống"></span>
                                            <span>Ghế trống</span>
                                        </div>
                                        <div class="legend-item">
                                            <span class="legend-seat selected" aria-label="Ghế đang chọn"></span>
                                            <span>Ghế đang chọn</span>
                                        </div>
                                        <div class="legend-item">
                                            <span class="legend-seat reserved" aria-label="Ghế đang chọn (người khác)"></span>
                                            <span>Ghế đang chọn (người khác)</span>
                                        </div>
                                        <div class="legend-item">
                                            <span class="legend-seat booked" aria-label="Ghế đã bán"></span>
                                            <span>Ghế đã bán</span>
                                        </div>
                                        <div class="legend-item">
                                            <span class="legend-seat vip-seat" aria-label="Ghế VIP"></span>
                                            <span>Ghế VIP <i class="fas fa-crown" style="color: #ffd700; margin-left: 5px;"></i></span>
                                        </div>
                                        <div class="legend-item">
                                            <span class="legend-seat available" aria-label="Ghế thường"></span>
                                            <span>Ghế thường <i class="fas fa-chair" style="color: #666; margin-left: 5px;"></i></span>
                                        </div>
                                        <div class="legend-item">
                                            <span class="legend-seat couple-seat" aria-label="Ghế đôi"></span>
                                            <span>Ghế đôi</span>
                                        </div>
                                    </div>

                                    <!-- Price Info -->
                                    <?php if (isset($selected_showtime_id) && $selected_showtime_id): ?>
                                        <div class="price-info-section mb-3" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border-radius: 12px; padding: 15px; border: 1px solid rgba(255,255,255,0.1);">
                                            <h6 style="color: #ffd700; margin-bottom: 12px; font-size: 14px;">
                                                <i class="fas fa-info-circle me-2"></i>Thông tin giá vé
                                            </h6>
                                            <div style="display: grid; gap: 8px; font-size: 13px;">
                                                <div style="display: flex; justify-content: space-between; color: #ccc;">
                                                    <span>Loại phòng:</span>
                                                    <span style="color: #fff; font-weight: 600;">
                                                        <?php
                                                        $screenTypeDisplay = isset($screenType) ? $screenType : '2D';
                                                        $screenTypeColors = ['2D' => '#4CAF50', '3D' => '#2196F3', 'IMAX' => '#9C27B0', '4DX' => '#FF5722'];
                                                        $color = $screenTypeColors[$screenTypeDisplay] ?? '#4CAF50';
                                                        ?>
                                                        <span style="background: <?php echo $color; ?>; padding: 2px 8px; border-radius: 4px;"><?php echo $screenTypeDisplay; ?></span>
                                                    </span>
                                                </div>
                                                <div style="display: flex; justify-content: space-between; color: #ccc;">
                                                    <span>Giá cơ bản:</span>
                                                    <span style="color: #fff;"><?php echo number_format(isset($basePrice) ? $basePrice : 90000, 0, ',', '.'); ?>đ</span>
                                                </div>
                                                <?php if (isset($screenSurcharge) && $screenSurcharge > 0): ?>
                                                    <div style="display: flex; justify-content: space-between; color: #ccc;">
                                                        <span>Phụ phí <?php echo $screenTypeDisplay; ?>:</span>
                                                        <span style="color: #ffd700;">+<?php echo number_format($screenSurcharge, 0, ',', '.'); ?>đ</span>
                                                    </div>
                                                <?php endif; ?>
                                                <hr style="border-color: rgba(255,255,255,0.1); margin: 8px 0;">
                                                <div style="display: flex; justify-content: space-between; color: #ccc;">
                                                    <span><i class="fas fa-chair me-1"></i> Ghế thường:</span>
                                                    <span style="color: #4CAF50; font-weight: 600;"><?php echo number_format(isset($normalPrice) ? $normalPrice : 90000, 0, ',', '.'); ?>đ</span>
                                                </div>
                                                <div style="display: flex; justify-content: space-between; color: #ccc;">
                                                    <span><i class="fas fa-crown me-1" style="color: #ffd700;"></i> Ghế VIP (+30%):</span>
                                                    <span style="color: #ffd700; font-weight: 600;"><?php echo number_format(isset($vipPrice) ? $vipPrice : 120000, 0, ',', '.'); ?>đ</span>
                                                </div>
                                                <div style="display: flex; justify-content: space-between; color: #ccc;">
                                                    <span><i class="fas fa-couch me-1" style="color: #e91e63;"></i> Ghế đôi (+50%/ghế):</span>
                                                    <span style="color: #e91e63; font-weight: 600;"><?php echo number_format(isset($couplePrice) ? $couplePrice : 135000, 0, ',', '.'); ?>đ</span>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>

                                    <!-- Total Price -->
                                    <div class="total-price-section mb-4" role="status" aria-live="polite">
                                        <div class="total-info">
                                            <span class="total-label">Tổng tiền:</span>
                                            <span class="total-seats" id="total-seats">0 ghế</span>
                                        </div>
                                        <span class="total-amount" id="total-amount" aria-label="Tổng số tiền phải thanh toán">0₫</span>
                                    </div>

                                    <!-- Email Input -->
                                    <div class="email-input-container-booking mb-4" id="email-container" style="display: none;">
                                        <div class="form-group">
                                            <label for="customer_email" class="form-label-booking">
                                                <i class="fas fa-envelope me-2"></i> Email nhận vé <span class="required">*</span>
                                            </label>
                                            <input
                                                type="email"
                                                id="customer_email"
                                                name="customer_email"
                                                class="form-control-booking"
                                                placeholder="Nhập email của bạn để nhận vé"
                                                value="<?php echo isset($user['email']) ? htmlspecialchars($user['email']) : ''; ?>"
                                                data-user-email="<?php echo isset($user['email']) ? htmlspecialchars($user['email']) : ''; ?>"
                                                required>
                                            <small class="form-text-booking">Vé và QR code sẽ được gửi đến email này sau khi thanh toán. Bạn có thể thay đổi email nếu cần.</small>
                                        </div>
                                    </div>

                                    <!-- Submit Button -->
                                    <button type="button"
                                        class="btn-booking-submit"
                                        id="submit-btn"
                                        onclick="openFoodModal()"
                                        disabled
                                        aria-label="Xác nhận đặt vé">
                                        <i class="fas fa-ticket-alt me-2"></i>
                                        Đặt vé ngay
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Support Section -->
                    <div class="booking-step mb-4 mt-4">
                        <div class="support-section">
                            <button type="button" class="btn-support-toggle" id="supportToggleBtn" onclick="toggleSupportForm()">
                                <i class="fas fa-headset me-2"></i>
                                <span>Cần hỗ trợ?</span>
                            </button>

                            <div class="support-form-container" id="supportFormContainer" style="display: none;">
                                <div class="support-form-header">
                                    <h5><i class="fas fa-headset me-2"></i>Gửi yêu cầu hỗ trợ</h5>
                                    <button type="button" class="btn-close-support" onclick="toggleSupportForm()" aria-label="Đóng form hỗ trợ">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>

                                <form method="POST" action="?route=booking/submit-support" class="support-form" id="supportForm">
                                    <div class="mb-3">
                                        <label for="support-issue" class="form-label">Mục vấn đề <span class="text-danger">*</span></label>
                                        <select class="form-select" id="support-issue" name="issue" required>
                                            <option value="">-- Chọn mục vấn đề --</option>
                                            <option value="Lỗi thanh toán">Lỗi thanh toán</option>
                                            <option value="Không nhận được vé">Không nhận được vé</option>
                                            <option value="Vấn đề về ghế ngồi">Vấn đề về ghế ngồi</option>
                                            <option value="Hủy/Đổi vé">Hủy/Đổi vé</option>
                                            <option value="Lỗi hệ thống">Lỗi hệ thống</option>
                                            <option value="Thông tin rạp chiếu">Thông tin rạp chiếu</option>
                                            <option value="Khác">Khác</option>
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="support-message" class="form-label">Nội dung <span class="text-danger">*</span></label>
                                        <textarea class="form-control"
                                            id="support-message"
                                            name="message"
                                            rows="5"
                                            placeholder="Mô tả chi tiết vấn đề bạn gặp phải..."
                                            required></textarea>
                                        <small class="text-muted">Vui lòng mô tả chi tiết để chúng tôi có thể hỗ trợ bạn tốt nhất.</small>
                                    </div>

                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane me-2"></i>Gửi yêu cầu
                                        </button>
                                        <button type="button" class="btn btn-secondary" onclick="toggleSupportForm()">
                                            Hủy
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Structured Data for SEO -->
<?php if ($movie): ?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "Movie",
            "name": "<?php echo htmlspecialchars($movie['title']); ?>",
            <?php if ($movie['thumbnail']): ?> "image": "<?php echo htmlspecialchars($movie['thumbnail']); ?>",
            <?php endif; ?>
            <?php if ($movie['description']): ?> "description": "<?php echo htmlspecialchars(substr($movie['description'], 0, 200)); ?>",
            <?php endif; ?> "aggregateRating": {
                "@type": "AggregateRating",
                "ratingValue": "<?php echo number_format($movie['rating'] * 1.1, 1); ?>",
                "bestRating": "10"
            },
            <?php if ($movie['category_name']): ?> "genre": "<?php echo htmlspecialchars($movie['category_name']); ?>",
            <?php endif; ?> "offers": {
                "@type": "Offer",
                "availability": "https://schema.org/InStock",
                "priceCurrency": "VND",
                "category": "Movie Tickets"
            }
        }
    </script>
<?php endif; ?>

<!-- Modal Combo & Đồ ăn -->
<?php if ($selected_showtime_id && !empty($foodItems)): ?>
    <div id="foodModal" class="food-modal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.8); z-index: 9999; overflow-y: auto; backdrop-filter: blur(5px);">
        <div class="food-modal-content" style="position: relative; max-width: 800px; margin: 2rem auto; background: linear-gradient(135deg, rgba(26, 26, 46, 0.98) 0%, rgba(22, 33, 62, 0.98) 100%); border: 2px solid rgba(212, 175, 55, 0.5); border-radius: 20px; padding: 30px; box-shadow: 0 20px 60px rgba(0, 0, 0, 0.8); z-index: 10000; pointer-events: auto;">
            <!-- Modal Header -->
            <div class="food-modal-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 25px; padding-bottom: 20px; border-bottom: 2px solid rgba(212, 175, 55, 0.3);">
                <h2 style="color: #d4af37; font-size: 2rem; font-weight: 700; margin: 0; display: flex; align-items: center; gap: 12px;">
                    <i class="fas fa-utensils" style="color: #d4af37;"></i>
                    <span>Combo & Đồ ăn</span>
                </h2>
                <button type="button" onclick="closeFoodModal()" style="background: transparent; border: 1px solid rgba(212, 175, 55, 0.5); color: #d4af37; width: 40px; height: 40px; border-radius: 10px; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; font-size: 1.2rem;">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Modal Body - Food Items (Always visible, no toggle) -->
            <div class="food-modal-body" style="max-height: 60vh; overflow-y: auto; padding-right: 10px; margin-bottom: 25px;">
                <div class="food-items-luxury-grid-modal" style="display: grid; grid-template-columns: 1fr; gap: 20px;">
                    <?php foreach ($foodItems as $item): ?>
                        <div class="food-item-luxury-card-modal" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(212, 175, 55, 0.2); border-radius: 15px; padding: 20px; transition: all 0.3s; position: relative; overflow: hidden;">
                            <div class="food-item-luxury-glow" style="position: absolute; top: 0; left: 0; right: 0; height: 3px; background: linear-gradient(90deg, transparent, #d4af37, transparent); opacity: 0; transition: opacity 0.3s;"></div>

                            <div class="food-item-luxury-content" style="display: flex; gap: 15px;">
                                <!-- Food Image -->
                                <div class="food-item-luxury-image" style="flex-shrink: 0;">
                                    <?php if ($item['image']): ?>
                                        <img src="<?php echo htmlspecialchars($item['image']); ?>"
                                            alt="<?php echo htmlspecialchars($item['name']); ?>"
                                            style="width: 120px; height: 120px; object-fit: cover; border-radius: 12px; border: 2px solid rgba(212, 175, 55, 0.3); box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);">
                                    <?php else: ?>
                                        <div style="width: 120px; height: 120px; background: linear-gradient(135deg, rgba(212, 175, 55, 0.2), rgba(212, 175, 55, 0.1)); border-radius: 12px; border: 2px solid rgba(212, 175, 55, 0.3); display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-utensils" style="font-size: 2.5rem; color: rgba(212, 175, 55, 0.5);"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Food Info -->
                                <div class="food-item-luxury-info" style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                                    <div>
                                        <h4 style="color: #fff; font-size: 1.2rem; font-weight: 600; margin: 0 0 10px 0; display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
                                            <?php echo htmlspecialchars($item['name']); ?>
                                            <span class="food-type-badge" style="font-size: 0.75rem; padding: 4px 10px; border-radius: 6px; background: <?php
                                                                                                                                                        echo $item['type'] === 'combo' ? 'rgba(212, 175, 55, 0.2)' : ($item['type'] === 'snack' ? 'rgba(255, 193, 7, 0.2)' : 'rgba(23, 162, 184, 0.2)');
                                                                                                                                                        ?>; color: <?php
                                                echo $item['type'] === 'combo' ? '#d4af37' : ($item['type'] === 'snack' ? '#ffc107' : '#17a2b8');
                                                ?>; border: 1px solid <?php
                                                            echo $item['type'] === 'combo' ? 'rgba(212, 175, 55, 0.5)' : ($item['type'] === 'snack' ? 'rgba(255, 193, 7, 0.5)' : 'rgba(23, 162, 184, 0.5)');
                                                            ?>;">
                                                <?php
                                                echo $item['type'] === 'combo' ? 'Combo' : ($item['type'] === 'snack' ? 'Snack' : 'Đồ uống');
                                                ?>
                                            </span>
                                        </h4>
                                        <?php if ($item['description']): ?>
                                            <p style="color: rgba(255, 255, 255, 0.7); font-size: 0.95rem; margin: 0 0 12px 0; line-height: 1.5;">
                                                <?php echo htmlspecialchars($item['description']); ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>

                                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 15px; flex-wrap: wrap;">
                                        <div class="food-item-luxury-price" style="color: #d4af37; font-size: 1.5rem; font-weight: 700; text-shadow: 0 2px 10px rgba(212, 175, 55, 0.3);">
                                            <?php echo number_format($item['price']); ?>₫
                                        </div>

                                        <div class="food-item-luxury-quantity" style="display: flex; align-items: center; gap: 12px;">
                                            <button type="button" class="food-qty-btn-modal" data-action="decrease" data-item-id="<?php echo $item['id']; ?>" style="width: 40px; height: 40px; border-radius: 10px; border: 1px solid rgba(212, 175, 55, 0.5); background: rgba(212, 175, 55, 0.1); color: #d4af37; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.1rem;">
                                                <i class="fas fa-minus"></i>
                                            </button>
                                            <input type="number"
                                                name="food_items_modal[<?php echo $item['id']; ?>]"
                                                id="food_qty_modal_<?php echo $item['id']; ?>"
                                                value="0"
                                                min="0"
                                                max="10"
                                                class="food-quantity-input-modal"
                                                data-price="<?php echo $item['price']; ?>"
                                                data-item-id="<?php echo $item['id']; ?>"
                                                style="width: 70px; text-align: center; padding: 10px; border-radius: 10px; border: 1px solid rgba(212, 175, 55, 0.5); background: rgba(255, 255, 255, 0.1); color: #fff; font-weight: 600; font-size: 1.1rem;">
                                            <button type="button" class="food-qty-btn-modal" data-action="increase" data-item-id="<?php echo $item['id']; ?>" style="width: 40px; height: 40px; border-radius: 10px; border: 1px solid rgba(212, 175, 55, 0.5); background: rgba(212, 175, 55, 0.1); color: #d4af37; cursor: pointer; transition: all 0.3s; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.1rem;">
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="food-modal-footer" style="display: flex; align-items: center; justify-content: space-between; gap: 15px; padding-top: 20px; border-top: 2px solid rgba(212, 175, 55, 0.3);">
                <div style="color: #fff; font-size: 1.1rem;">
                    <span>Tổng tiền combo & đồ ăn: </span>
                    <span id="modal-food-total" style="color: #d4af37; font-weight: 700; font-size: 1.3rem;">0₫</span>
                </div>
                <div style="display: flex; gap: 10px;">
                    <button type="button" onclick="closeFoodModal()" style="padding: 12px 30px; border-radius: 10px; border: 1px solid rgba(212, 175, 55, 0.5); background: transparent; color: #d4af37; cursor: pointer; transition: all 0.3s; font-weight: 600;">
                        Bỏ qua
                    </button>
                    <button type="button"
                        id="confirm-food-btn"
                        onclick="confirmFoodSelection()"
                        style="padding: 12px 30px; border-radius: 10px; border: none; background: linear-gradient(135deg, #d4af37, #b8941f); color: #000; cursor: pointer; transition: all 0.3s; font-weight: 700; box-shadow: 0 5px 15px rgba(212, 175, 55, 0.3); pointer-events: auto; position: relative; z-index: 10000;">
                        Xác nhận
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .food-modal-body::-webkit-scrollbar {
            width: 8px;
        }

        .food-modal-body::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }

        .food-modal-body::-webkit-scrollbar-thumb {
            background: rgba(212, 175, 55, 0.5);
            border-radius: 10px;
        }

        .food-modal-body::-webkit-scrollbar-thumb:hover {
            background: rgba(212, 175, 55, 0.7);
        }

        .food-item-luxury-card-modal:hover .food-item-luxury-glow {
            opacity: 1;
        }

        .food-qty-btn-modal:hover {
            background: rgba(212, 175, 55, 0.3) !important;
            border-color: #d4af37 !important;
            transform: scale(1.1);
        }

        .food-qty-btn-modal:active {
            transform: scale(0.95);
        }

        .food-quantity-input-modal:focus {
            outline: none;
            border-color: #d4af37 !important;
            box-shadow: 0 0 0 3px rgba(212, 175, 55, 0.2);
            background: rgba(255, 255, 255, 0.15) !important;
        }
    </style>
<?php endif; ?>

<script>
    // Function để enable các nút chọn đồ uống - Global scope
    function updateFoodButtonsState(enabled) {
        // Enable các nút tăng/giảm số lượng đồ uống
        document.querySelectorAll('.food-qty-btn, .food-qty-btn-modal').forEach(function(btn) {
            btn.disabled = !enabled;
            if (enabled) {
                btn.style.opacity = '1';
                btn.style.cursor = 'pointer';
            } else {
                btn.style.opacity = '0.5';
                btn.style.cursor = 'not-allowed';
            }
        });

        // Enable các input số lượng đồ uống
        document.querySelectorAll('.food-quantity-input, .food-quantity-input-luxury, .food-quantity-input-modal').forEach(function(input) {
            input.disabled = !enabled;
            if (enabled) {
                input.style.opacity = '1';
                input.style.cursor = 'text';
            } else {
                input.style.opacity = '0.5';
                input.style.cursor = 'not-allowed';
            }
        });
    }

    // Modal functions - Global scope để có thể gọi từ onclick
    function openFoodModal() {
        // Kiểm tra xem có ghế được chọn không
        const form = document.getElementById('booking-form');
        if (!form) {
            alert('Lỗi: Không tìm thấy form đặt vé!');
            return;
        }

        const selectedSeats = form.querySelectorAll('input[name="seats[]"]:checked');
        if (selectedSeats.length === 0) {
            alert('Vui lòng chọn ít nhất một ghế trước khi đặt vé!');
            return;
        }

        // Lấy danh sách ghế đã chọn (loại bỏ duplicate)
        var seatValues = Array.from(selectedSeats).map(function(cb) {
            return cb.value;
        });
        var selectedSeatValues = [];
        for (var i = 0; i < seatValues.length; i++) {
            if (selectedSeatValues.indexOf(seatValues[i]) === -1) {
                selectedSeatValues.push(seatValues[i]);
            }
        }

        // Validate ghế TRƯỚC KHI cho phép chọn đồ uống - BẮT BUỘC phải pass
        // ĐỢI function validate được định nghĩa (có thể chưa load xong)
        if (typeof validateSeatSelection !== 'function') {
            console.error('validateSeatSelection function not found - Cannot validate seats');
            alert('⚠️ Hệ thống đang tải, vui lòng đợi vài giây rồi thử lại!');
            return; // DỪNG LẠI, KHÔNG MỞ MODAL
        }

        console.log('=== Validating seats before opening food modal ===');
        console.log('Selected seats:', selectedSeatValues);
        const validationError = validateSeatSelection(selectedSeatValues);

        if (validationError) {
            // Hiển thị lỗi bên trái khung ghế ngồi
            const errorMessageEl = document.getElementById('seat-validation-error');
            const errorTextEl = document.getElementById('seat-validation-error-text');
            if (errorMessageEl && errorTextEl) {
                errorTextEl.textContent = validationError;
                errorMessageEl.style.display = 'block';
                // Scroll đến thông báo lỗi (bên trái khung ghế)
                errorMessageEl.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
            // Hiển thị alert rõ ràng hơn
            alert('⚠️ Lỗi chọn ghế!\n\n' + validationError + '\n\nVui lòng chọn lại ghế theo đúng quy định.');
            console.error('Validation FAILED - Cannot open food modal');
            return; // DỪNG LẠI, KHÔNG MỞ MODAL
        }

        console.log('Validation PASSED - Opening food modal');

        // Ẩn thông báo lỗi nếu có
        const errorMessageEl = document.getElementById('seat-validation-error');
        if (errorMessageEl) {
            errorMessageEl.style.display = 'none';
        }

        // Hiển thị form đồ ăn uống khi ghế hợp lệ
        const foodSection = document.querySelector('.food-items-luxury-section');
        if (foodSection) {
            foodSection.style.display = 'block';
            // Scroll đến form đồ ăn uống
            foodSection.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }

        // MỞ MODAL CHỈ KHI VALIDATION PASS
        const modal = document.getElementById('foodModal');
        if (modal) {
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
            // Sync values from form to modal
            syncFoodToModal();
            updateModalFoodTotal();
            // Enable các nút trong modal vì ghế đã được validate
            if (typeof updateFoodButtonsState === 'function') {
                updateFoodButtonsState(true);
            }
        } else {
            // Nếu không có modal (không có food items), submit form luôn
            form.submit();
        }
    }

    function closeFoodModal() {
        const modal = document.getElementById('foodModal');
        if (modal) {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }
    }

    function syncFoodToModal() {
        // Copy values from form inputs to modal inputs
        document.querySelectorAll('.food-quantity-input-luxury').forEach(function(input) {
            const itemId = input.getAttribute('data-item-id');
            const modalInput = document.getElementById('food_qty_modal_' + itemId);
            if (modalInput) {
                modalInput.value = input.value || 0;
            }
        });
    }

    function syncModalToForm() {
        // Copy values from modal inputs to form inputs
        document.querySelectorAll('.food-quantity-input-modal').forEach(function(modalInput) {
            const itemId = modalInput.getAttribute('data-item-id');
            const formInput = document.querySelector(`.food-quantity-input-luxury[data-item-id="${itemId}"]`);
            if (formInput) {
                formInput.value = modalInput.value || 0;
            }
        });
    }

    function updateModalFoodTotal() {
        let total = 0;
        document.querySelectorAll('.food-quantity-input-modal').forEach(function(input) {
            const quantity = parseInt(input.value) || 0;
            const price = parseFloat(input.getAttribute('data-price')) || 0;
            total += quantity * price;
        });
        const totalElement = document.getElementById('modal-food-total');
        if (totalElement) {
            totalElement.textContent = total.toLocaleString('vi-VN') + '₫';
        }
    }

    // Đảm bảo function này ở global scope và có thể click được
    window.confirmFoodSelection = function() {
        console.log('confirmFoodSelection called');

        try {
            // Sync modal values to form
            if (typeof syncModalToForm === 'function') {
                syncModalToForm();
            }

            // Tạo hidden inputs cho food_items để đảm bảo được gửi khi submit
            const form = document.getElementById('booking-form');
            if (form) {
                // Xóa hidden inputs cũ nếu có
                form.querySelectorAll('input[name^="food_items"][type="hidden"]').forEach(el => el.remove());

                // Tạo hidden inputs mới từ modal values
                document.querySelectorAll('.food-quantity-input-modal').forEach(function(modalInput) {
                    const itemId = modalInput.getAttribute('data-item-id');
                    const quantity = parseInt(modalInput.value) || 0;
                    if (quantity > 0) {
                        const hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'food_items[' + itemId + ']';
                        hiddenInput.value = quantity;
                        form.appendChild(hiddenInput);
                        console.log('Added hidden input for food_items[' + itemId + '] = ' + quantity);
                    }
                });
            }

            // Update total in main form
            if (typeof updateSelection === 'function') {
                updateSelection();
            }

            // Hiển thị danh sách đã chọn (vé + combo) và tổng tiền
            if (typeof showBookingSummary === 'function') {
                showBookingSummary();
            }

            // Close modal
            if (typeof closeFoodModal === 'function') {
                closeFoodModal();
            }
        } catch (error) {
            console.error('Error in confirmFoodSelection:', error);
            alert('Có lỗi xảy ra khi xác nhận đơn hàng. Vui lòng thử lại.');
        }
    };

    // Alias để tương thích với onclick
    function confirmFoodSelection() {
        if (typeof window.confirmFoodSelection === 'function') {
            window.confirmFoodSelection();
        } else {
            console.error('window.confirmFoodSelection is not defined');
            alert('Lỗi: Function xác nhận chưa được tải. Vui lòng tải lại trang.');
        }
    }

    // Đảm bảo function này ở global scope
    window.showBookingSummary = function() {
        console.log('showBookingSummary called');

        // Lấy thông tin ghế đã chọn
        const form = document.getElementById('booking-form');
        if (!form) {
            console.error('Booking form not found');
            return;
        }

        const selectedSeats = form.querySelectorAll('input[name="seats[]"]:checked');
        const seatValues = Array.from(selectedSeats).map(cb => cb.value);
        const uniqueSeats = [];
        for (let i = 0; i < seatValues.length; i++) {
            if (uniqueSeats.indexOf(seatValues[i]) === -1) {
                uniqueSeats.push(seatValues[i]);
            }
        }

        // Tính tiền ghế
        // Lấy giá từ biến global hoặc từ DOM
        const defaultNormalPrice = typeof normalPrice !== 'undefined' ? normalPrice : (typeof window.normalPrice !== 'undefined' ? window.normalPrice : 120000);
        const defaultVipPrice = typeof vipPrice !== 'undefined' ? vipPrice : (typeof window.vipPrice !== 'undefined' ? window.vipPrice : 180000);
        const defaultCouplePrice = typeof couplePrice !== 'undefined' ? couplePrice : (typeof window.couplePrice !== 'undefined' ? window.couplePrice : 240000);

        let seatTotal = 0;
        uniqueSeats.forEach(function(seat) {
            // Sử dụng function getSeatPrice nếu có, nếu không thì dùng giá mặc định
            let seatPrice = defaultNormalPrice;

            if (typeof getSeatPrice === 'function') {
                try {
                    seatPrice = getSeatPrice(seat);
                } catch (e) {
                    console.warn('Error calling getSeatPrice:', e);
                }
            } else if (typeof window.getSeatPrice === 'function') {
                try {
                    seatPrice = window.getSeatPrice(seat);
                } catch (e) {
                    console.warn('Error calling window.getSeatPrice:', e);
                }
            } else {
                // Fallback: Kiểm tra từ DOM
                const label = document.querySelector('.seat-label[data-seat="' + seat + '"]');
                if (label) {
                    if (label.classList.contains('couple-seat')) {
                        seatPrice = defaultCouplePrice / 2;
                    } else if (label.classList.contains('vip-seat')) {
                        seatPrice = defaultVipPrice;
                    } else {
                        seatPrice = defaultNormalPrice;
                    }
                }
            }

            seatTotal += seatPrice;
        });

        console.log('Calculated seat total:', seatTotal, 'for seats:', uniqueSeats);

        // Lấy thông tin combo đã chọn
        const foodItems = [];
        let foodTotal = 0;

        // Lấy từ modal inputs (ưu tiên)
        document.querySelectorAll('.food-quantity-input-modal').forEach(function(input) {
            const quantity = parseInt(input.value) || 0;
            if (quantity > 0) {
                const itemId = input.getAttribute('data-item-id');
                const itemPrice = parseFloat(input.getAttribute('data-price')) || 0;

                // Tìm tên combo từ DOM (từ card chứa input này)
                let itemName = 'Combo/Đồ ăn';
                const card = input.closest('.food-item-luxury-card-modal');
                if (card) {
                    const nameElement = card.querySelector('h4');
                    if (nameElement) {
                        // Lấy text từ h4, loại bỏ badge "Combo"
                        itemName = nameElement.textContent.trim();
                        const badge = nameElement.querySelector('.food-type-badge');
                        if (badge) {
                            itemName = itemName.replace(badge.textContent.trim(), '').trim();
                        }
                    }
                }

                const itemTotal = quantity * itemPrice;
                foodTotal += itemTotal;
                foodItems.push({
                    name: itemName,
                    quantity: quantity,
                    price: itemPrice,
                    total: itemTotal
                });
            }
        });

        // Nếu không có trong modal, lấy từ form inputs
        if (foodItems.length === 0) {
            document.querySelectorAll('.food-quantity-input-luxury').forEach(function(input) {
                const quantity = parseInt(input.value) || 0;
                if (quantity > 0) {
                    const itemId = input.getAttribute('data-item-id');
                    const itemPrice = parseFloat(input.getAttribute('data-price')) || 0;

                    // Tìm tên combo từ DOM
                    let itemName = 'Combo/Đồ ăn';
                    const card = input.closest('.food-item-luxury-card');
                    if (card) {
                        const nameElement = card.querySelector('h4');
                        if (nameElement) {
                            itemName = nameElement.textContent.trim();
                            const badge = nameElement.querySelector('.food-type-badge');
                            if (badge) {
                                itemName = itemName.replace(badge.textContent.trim(), '').trim();
                            }
                        }
                    }

                    const itemTotal = quantity * itemPrice;
                    foodTotal += itemTotal;
                    foodItems.push({
                        name: itemName,
                        quantity: quantity,
                        price: itemPrice,
                        total: itemTotal
                    });
                }
            });
        }

        const grandTotal = seatTotal + foodTotal;

        // Tạo hoặc cập nhật summary section
        let summarySection = document.getElementById('booking-summary-section');
        if (!summarySection) {
            summarySection = document.createElement('div');
            summarySection.id = 'booking-summary-section';
            summarySection.className = 'booking-summary-section';
            summarySection.style.cssText = 'background: linear-gradient(135deg, rgba(26, 26, 46, 0.95) 0%, rgba(22, 33, 62, 0.95) 100%); border: 2px solid rgba(212, 175, 55, 0.3); border-radius: 20px; padding: 25px; margin-top: 20px; box-shadow: 0 15px 50px rgba(0, 0, 0, 0.5);';

            // Chèn vào trước nút submit
            const submitBtn = document.getElementById('submit-btn');
            if (submitBtn && submitBtn.parentNode) {
                submitBtn.parentNode.insertBefore(summarySection, submitBtn);
            } else {
                form.appendChild(summarySection);
            }
        }

        // Tạo HTML cho summary
        let html = '<div class="booking-summary-header" style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid rgba(212, 175, 55, 0.2);">';
        html += '<h4 style="color: #d4af37; margin: 0; font-size: 20px; font-weight: bold;"><i class="fas fa-receipt"></i> Tóm tắt đơn hàng</h4>';
        html += '</div>';

        // Danh sách vé
        html += '<div class="summary-tickets" style="margin-bottom: 20px;">';
        html += '<h5 style="color: #fff; margin-bottom: 15px; font-size: 16px;"><i class="fas fa-ticket-alt"></i> Vé xem phim (' + uniqueSeats.length + ' vé)</h5>';
        html += '<div style="background: rgba(255, 255, 255, 0.05); border-radius: 10px; padding: 15px;">';
        html += '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">';
        html += '<span style="color: #fff;">Ghế: <strong style="color: #d4af37;">' + uniqueSeats.join(', ') + '</strong></span>';
        html += '<span style="color: #d4af37; font-weight: bold; font-size: 16px;">' + seatTotal.toLocaleString('vi-VN') + '₫</span>';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        // Danh sách combo/đồ ăn
        if (foodItems.length > 0) {
            html += '<div class="summary-food" style="margin-bottom: 20px;">';
            html += '<h5 style="color: #fff; margin-bottom: 15px; font-size: 16px;"><i class="fas fa-utensils"></i> Combo & Đồ ăn</h5>';
            html += '<div style="background: rgba(255, 255, 255, 0.05); border-radius: 10px; padding: 15px;">';
            foodItems.forEach(function(item) {
                html += '<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px; padding-bottom: 10px; border-bottom: 1px solid rgba(255, 255, 255, 0.1);">';
                html += '<div>';
                html += '<span style="color: #fff; font-weight: bold;">' + item.name + '</span>';
                html += '<span style="color: #999; font-size: 13px; margin-left: 10px;">x' + item.quantity + '</span>';
                html += '</div>';
                html += '<span style="color: #d4af37; font-weight: bold;">' + item.total.toLocaleString('vi-VN') + '₫</span>';
                html += '</div>';
            });
            html += '<div style="display: flex; justify-content: space-between; align-items: center; margin-top: 15px; padding-top: 15px; border-top: 2px solid rgba(212, 175, 55, 0.3);">';
            html += '<span style="color: #fff; font-weight: bold;">Tổng tiền combo & đồ ăn:</span>';
            html += '<span style="color: #d4af37; font-weight: bold; font-size: 18px;">' + foodTotal.toLocaleString('vi-VN') + '₫</span>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
        }

        // Tổng tiền thanh toán
        html += '<div class="summary-total" style="background: rgba(212, 175, 55, 0.1); border: 2px solid rgba(212, 175, 55, 0.5); border-radius: 10px; padding: 20px; margin-top: 20px;">';
        html += '<div style="display: flex; justify-content: space-between; align-items: center;">';
        html += '<span style="color: #fff; font-size: 18px; font-weight: bold;">Tổng thanh toán:</span>';
        html += '<span style="color: #d4af37; font-size: 24px; font-weight: bold;">' + grandTotal.toLocaleString('vi-VN') + '₫</span>';
        html += '</div>';
        html += '</div>';

        // Phương thức thanh toán
        html += '<div class="payment-method-section" style="margin-top: 25px;">';
        html += '<h5 style="color: #fff; margin-bottom: 15px; font-size: 16px;"><i class="fas fa-wallet me-2"></i>Chọn phương thức thanh toán</h5>';
        html += '<div style="display: flex; flex-direction: column; gap: 12px;">';

        // Option 1: Thanh toán từ ví
        var userPoints = <?php echo isset($user['points']) ? intval($user['points']) : 0; ?>;
        var canPayWithWallet = userPoints >= grandTotal;
        html += '<label class="payment-option" style="display: flex; align-items: center; padding: 15px 20px; background: ' + (canPayWithWallet ? 'rgba(40, 167, 69, 0.1)' : 'rgba(108, 117, 125, 0.1)') + '; border: 2px solid ' + (canPayWithWallet ? 'rgba(40, 167, 69, 0.5)' : 'rgba(108, 117, 125, 0.3)') + '; border-radius: 12px; cursor: ' + (canPayWithWallet ? 'pointer' : 'not-allowed') + '; transition: all 0.3s; opacity: ' + (canPayWithWallet ? '1' : '0.6') + ';">';
        html += '<input type="radio" name="payment_method" value="wallet" ' + (canPayWithWallet ? '' : 'disabled') + ' style="width: 20px; height: 20px; margin-right: 15px; accent-color: #28a745;">';
        html += '<div style="flex: 1;">';
        html += '<div style="display: flex; align-items: center; gap: 10px;">';
        html += '<i class="fas fa-wallet" style="font-size: 24px; color: ' + (canPayWithWallet ? '#28a745' : '#6c757d') + ';"></i>';
        html += '<div>';
        html += '<span style="color: #fff; font-weight: bold; font-size: 16px;">Thanh toán từ ví</span>';
        html += '<div style="color: ' + (canPayWithWallet ? '#28a745' : '#dc3545') + '; font-size: 13px;">Số dư: ' + userPoints.toLocaleString('vi-VN') + '₫' + (canPayWithWallet ? '' : ' (Không đủ)') + '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</label>';

        // Option 2: VNPay
        html += '<label class="payment-option" style="display: flex; align-items: center; padding: 15px 20px; background: rgba(0, 123, 255, 0.1); border: 2px solid rgba(0, 123, 255, 0.5); border-radius: 12px; cursor: pointer; transition: all 0.3s;">';
        html += '<input type="radio" name="payment_method" value="vnpay" checked style="width: 20px; height: 20px; margin-right: 15px; accent-color: #007bff;">';
        html += '<div style="flex: 1;">';
        html += '<div style="display: flex; align-items: center; gap: 10px;">';
        html += '<img src="https://vnpay.vn/s1/statics.vnpay.vn/2023/9/06ncktiwd6dc1694418196384.png" alt="VNPay" style="height: 28px;">';
        html += '<div>';
        html += '<span style="color: #fff; font-weight: bold; font-size: 16px;">VNPay</span>';
        html += '<div style="color: #aaa; font-size: 13px;">Thẻ ATM, Visa, MasterCard, QR Code</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</label>';

        html += '</div>';
        html += '</div>';

        // Nút thanh toán
        html += '<div style="margin-top: 25px; text-align: center;">';
        html += '<button type="button" onclick="submitBookingWithPayment()" style="background: linear-gradient(135deg, #d4af37 0%, #b8941f 100%); color: #000; border: none; padding: 18px 50px; border-radius: 12px; font-size: 20px; font-weight: bold; cursor: pointer; box-shadow: 0 5px 20px rgba(212, 175, 55, 0.5); transition: all 0.3s; width: 100%; max-width: 400px;" onmouseover="this.style.transform=\'scale(1.05)\'; this.style.boxShadow=\'0 8px 25px rgba(212, 175, 55, 0.7)\';" onmouseout="this.style.transform=\'scale(1)\'; this.style.boxShadow=\'0 5px 20px rgba(212, 175, 55, 0.5)\';">';
        html += '<i class="fas fa-credit-card me-2"></i> Thanh toán';
        html += '</button>';
        html += '</div>';

        summarySection.innerHTML = html;
        summarySection.style.display = 'block';

        // Thêm event listener cho payment options
        const paymentOptions = summarySection.querySelectorAll('.payment-option');
        paymentOptions.forEach(function(option) {
            const radio = option.querySelector('input[type="radio"]');
            if (radio) {
                // Highlight option đã được chọn mặc định
                if (radio.checked) {
                    option.style.boxShadow = '0 0 15px rgba(0, 123, 255, 0.5)';
                    option.style.transform = 'scale(1.02)';
                }

                radio.addEventListener('change', function() {
                    // Reset tất cả options
                    paymentOptions.forEach(function(opt) {
                        opt.style.boxShadow = 'none';
                        opt.style.transform = 'scale(1)';
                    });
                    // Highlight option được chọn
                    if (this.checked) {
                        if (this.value === 'wallet') {
                            option.style.boxShadow = '0 0 15px rgba(40, 167, 69, 0.5)';
                        } else {
                            option.style.boxShadow = '0 0 15px rgba(0, 123, 255, 0.5)';
                        }
                        option.style.transform = 'scale(1.02)';
                    }
                });
            }
        });

        // Ẩn nút "Đặt vé ngay" gốc
        const submitBtn = document.getElementById('submit-btn');
        if (submitBtn) {
            submitBtn.style.display = 'none';
        }

        // Scroll đến summary
        summarySection.scrollIntoView({
            behavior: 'smooth',
            block: 'start'
        });

        console.log('Booking summary displayed:', {
            seats: uniqueSeats,
            seatTotal: seatTotal,
            foodItems: foodItems,
            foodTotal: foodTotal,
            grandTotal: grandTotal
        });
    };

    // Alias để tương thích
    function showBookingSummary() {
        if (typeof window.showBookingSummary === 'function') {
            window.showBookingSummary();
        } else {
            console.error('window.showBookingSummary is not defined');
        }
    }

    // Đảm bảo function này ở global scope
    window.submitBookingForm = function() {
        const form = document.getElementById('booking-form');
        if (form) {
            console.log('Submitting form with food items...');
            form.submit();
        } else {
            console.error('Booking form not found');
            alert('Lỗi: Không tìm thấy form đặt vé. Vui lòng tải lại trang.');
        }
    };

    // Submit với phương thức thanh toán đã chọn
    window.submitBookingWithPayment = function() {
        const form = document.getElementById('booking-form');
        if (!form) {
            console.error('Booking form not found');
            alert('Lỗi: Không tìm thấy form đặt vé. Vui lòng tải lại trang.');
            return;
        }

        // Lấy phương thức thanh toán đã chọn
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked');
        if (!paymentMethod) {
            alert('Vui lòng chọn phương thức thanh toán!');
            return;
        }

        // Thêm hidden input cho payment_method vào form
        let paymentInput = form.querySelector('input[name="payment_method"]');
        if (!paymentInput) {
            paymentInput = document.createElement('input');
            paymentInput.type = 'hidden';
            paymentInput.name = 'payment_method';
            form.appendChild(paymentInput);
        }
        paymentInput.value = paymentMethod.value;

        console.log('Submitting form with payment method:', paymentMethod.value);
        form.submit();
    };

    // Alias để tương thích
    function submitBookingForm() {
        if (typeof window.submitBookingForm === 'function') {
            window.submitBookingForm();
        }
    }

    function submitBookingWithPayment() {
        if (typeof window.submitBookingWithPayment === 'function') {
            window.submitBookingWithPayment();
        }
    }

    // Lấy vị trí người dùng và lưu vào session/cookie
    function getUserLocation() {
        const locationDisplay = document.getElementById('userLocationDisplay');
        const locationText = document.getElementById('locationText');

        if (locationDisplay) {
            locationDisplay.style.display = 'flex';
            locationText.textContent = 'Đang lấy vị trí...';
        }

        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    // Lưu vào cookie (30 ngày)
                    document.cookie = `user_latitude=${lat}; path=/; max-age=${30 * 24 * 60 * 60}`;
                    document.cookie = `user_longitude=${lng}; path=/; max-age=${30 * 24 * 60 * 60}`;

                    // Hiển thị tọa độ tạm thời
                    if (locationText) {
                        locationText.textContent = 'Đang lấy địa chỉ...';
                    }

                    // Lấy địa chỉ thực tế từ tọa độ (Reverse Geocoding)
                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1&accept-language=vi`, {
                            headers: {
                                'User-Agent': 'CineHub Booking System'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            let address = '';
                            if (data.address) {
                                // Tạo địa chỉ từ các thành phần
                                const addr = data.address;
                                const parts = [];

                                if (addr.road) parts.push(addr.road);
                                if (addr.house_number) parts.push(addr.house_number);
                                if (addr.suburb || addr.neighbourhood) parts.push(addr.suburb || addr.neighbourhood);
                                if (addr.city || addr.town || addr.village) parts.push(addr.city || addr.town || addr.village);
                                if (addr.state || addr.province) parts.push(addr.state || addr.province);

                                address = parts.length > 0 ? parts.join(', ') : data.display_name || `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                            } else {
                                address = data.display_name || `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                            }

                            // Hiển thị địa chỉ
                            if (locationText) {
                                locationText.textContent = address;
                            }

                            // Lưu địa chỉ vào cookie
                            document.cookie = `user_address=${encodeURIComponent(address)}; path=/; max-age=${30 * 24 * 60 * 60}`;

                            // Lưu tỉnh/thành phố vào cookie
                            let province = '';
                            if (data.address) {
                                province = data.address.state || data.address.province || data.address.city || '';
                                if (province) {
                                    document.cookie = `user_province=${encodeURIComponent(province)}; path=/; max-age=${30 * 24 * 60 * 60}`;
                                }
                            }

                            // Gửi lên server để lưu vào session
                            fetch('?route=booking/saveLocation', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify({
                                    latitude: lat,
                                    longitude: lng,
                                    province: province
                                })
                            }).then(() => {
                                // Reload trang để cập nhật danh sách rạp
                                setTimeout(() => {
                                    window.location.reload();
                                }, 500);
                            }).catch(err => {
                                console.log('Error saving location:', err);
                                // Vẫn reload dù có lỗi
                                setTimeout(() => {
                                    window.location.reload();
                                }, 500);
                            });
                        })
                        .catch(err => {
                            console.log('Error getting address:', err);
                            // Fallback: hiển thị tọa độ nếu không lấy được địa chỉ
                            if (locationText) {
                                locationText.textContent = `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                            }
                        });

                    // Kiểm tra xem vị trí có thay đổi không
                    const savedLat = getCookie('user_latitude');
                    const savedLng = getCookie('user_longitude');
                    const locationChanged = !savedLat || !savedLng ||
                        Math.abs(parseFloat(savedLat) - lat) > 0.001 ||
                        Math.abs(parseFloat(savedLng) - lng) > 0.001;

                    // Lấy tỉnh từ địa chỉ (nếu có)
                    let province = '';
                    if (data.address) {
                        province = data.address.state || data.address.province || data.address.city || '';
                    }

                    // Gửi lên server để lưu vào session
                    fetch('?route=booking/saveLocation', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            latitude: lat,
                            longitude: lng,
                            province: province
                        })
                    }).then(() => {
                        // Chỉ reload nếu vị trí thay đổi và đang chọn rạp
                        if (locationChanged && window.location.search.includes('theater=') && !sessionStorage.getItem('locationReloaded')) {
                            sessionStorage.setItem('locationReloaded', 'true');
                            window.location.reload();
                        }
                    }).catch(err => console.log('Error saving location:', err));
                },
                function(error) {
                    console.log('Geolocation error:', error);
                    if (locationText) {
                        locationText.textContent = 'Không thể lấy vị trí';
                    }
                }, {
                    enableHighAccuracy: true,
                    timeout: 5000,
                    maximumAge: 0
                }
            );
        } else {
            if (locationText) {
                locationText.textContent = 'Trình duyệt không hỗ trợ';
            }
        }
    }

    // Helper function để lấy cookie
    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
        return null;
    }

    // Toggle hiển thị danh sách rạp "Xem thêm"
    function toggleMoreTheaters() {
        const list = document.getElementById('moreTheatersList');
        const icon = document.getElementById('moreTheatersIcon');
        const bookingStep = document.querySelector('.booking-step');

        if (list && icon) {
            const isVisible = list.style.display !== 'none';
            list.style.display = isVisible ? 'none' : 'block';
            icon.style.transform = isVisible ? 'rotate(0deg)' : 'rotate(180deg)';
            icon.style.transition = 'transform 0.3s ease';

            // Thêm/remove padding-bottom cho booking-step khi dropdown mở/đóng
            if (bookingStep) {
                if (isVisible) {
                    // Đóng dropdown - remove padding
                    bookingStep.style.paddingBottom = '';
                    bookingStep.classList.remove('dropdown-open');
                } else {
                    // Mở dropdown - thêm padding để không bị che
                    bookingStep.style.paddingBottom = '450px';
                    bookingStep.classList.add('dropdown-open');
                }
            }
        }
    }

    // Đóng danh sách "Xem thêm" khi click ra ngoài
    document.addEventListener('click', function(event) {
        const container = document.querySelector('.more-theaters-container');
        const list = document.getElementById('moreTheatersList');
        const bookingStep = document.querySelector('.booking-step');

        if (container && list && !container.contains(event.target)) {
            list.style.display = 'none';
            const icon = document.getElementById('moreTheatersIcon');
            if (icon) {
                icon.style.transform = 'rotate(0deg)';
            }
            // Remove padding khi đóng dropdown
            if (bookingStep) {
                bookingStep.style.paddingBottom = '';
                bookingStep.classList.remove('dropdown-open');
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        // Chỉ lấy vị trí nếu chưa có trong cookie hoặc chưa reload
        const savedLat = getCookie('user_latitude');
        const savedLng = getCookie('user_longitude');

        if (savedLat && savedLng) {
            // Hiển thị vị trí đã lưu
            const locationDisplay = document.getElementById('userLocationDisplay');
            const locationText = document.getElementById('locationText');
            if (locationDisplay && locationText) {
                locationDisplay.style.display = 'flex';

                // Kiểm tra xem có địa chỉ đã lưu không
                const savedAddress = getCookie('user_address');
                if (savedAddress) {
                    locationText.textContent = decodeURIComponent(savedAddress);
                } else {
                    // Nếu chưa có địa chỉ, lấy lại từ tọa độ
                    locationText.textContent = 'Đang lấy địa chỉ...';
                    const lat = parseFloat(savedLat);
                    const lng = parseFloat(savedLng);

                    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&addressdetails=1&accept-language=vi`, {
                            headers: {
                                'User-Agent': 'CineHub Booking System'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            let address = '';
                            if (data.address) {
                                const addr = data.address;
                                const parts = [];

                                if (addr.road) parts.push(addr.road);
                                if (addr.house_number) parts.push(addr.house_number);
                                if (addr.suburb || addr.neighbourhood) parts.push(addr.suburb || addr.neighbourhood);
                                if (addr.city || addr.town || addr.village) parts.push(addr.city || addr.town || addr.village);
                                if (addr.state || addr.province) parts.push(addr.state || addr.province);

                                address = parts.length > 0 ? parts.join(', ') : data.display_name || `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                            } else {
                                address = data.display_name || `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                            }

                            locationText.textContent = address;
                            document.cookie = `user_address=${encodeURIComponent(address)}; path=/; max-age=${30 * 24 * 60 * 60}`;
                        })
                        .catch(err => {
                            console.log('Error getting address:', err);
                            locationText.textContent = `${lat.toFixed(4)}, ${lng.toFixed(4)}`;
                        });
                }
            }
        } else {
            // Chỉ lấy vị trí mới nếu chưa có
            getUserLocation();
        }

        // Xóa flag reload sau 2 giây để cho phép reload lại nếu cần
        setTimeout(() => {
            sessionStorage.removeItem('locationReloaded');
        }, 2000);

        // Tự động điều chỉnh kích thước ghế dựa trên số lượng
        function adjustSeatSize() {
            const seatMapContainer = document.querySelector('.seat-map-container');
            if (!seatMapContainer) return;

            const seatsPerRow = parseInt(seatMapContainer.getAttribute('data-seats-per-row')) || 0;
            if (seatsPerRow === 0) return;

            // QUAN TRỌNG: Xóa tất cả inline styles width/height trước để CSS có thể áp dụng
            const seatLabels = seatMapContainer.querySelectorAll('.seat-label:not(.couple-seat)');

            // Nếu <= 15 ghế, CSS đã xử lý, chỉ cần điều chỉnh font size và icon, KHÔNG override width/height
            if (seatsPerRow <= 15) {
                // Xóa inline styles để CSS có thể áp dụng
                seatLabels.forEach(label => {
                    label.style.width = '';
                    label.style.height = '';
                    label.style.minWidth = '';
                    label.style.minHeight = '';
                    label.style.maxWidth = '';
                    label.style.maxHeight = '';
                });

                // Tính toán kích thước ghế đôi dựa trên kích thước ghế đơn
                const firstRegularSeat = seatMapContainer.querySelector('.seat-label:not(.couple-seat)');
                if (firstRegularSeat) {
                    const regularSeatWidth = firstRegularSeat.offsetWidth || 40; // Fallback 40px
                    const regularSeatHeight = firstRegularSeat.offsetHeight || 40; // Fallback 40px
                    const seatGap = 6.4; // 0.4rem = 6.4px
                    const coupleSeatWidth = (regularSeatWidth * 2) + seatGap;

                    const coupleSeats = seatMapContainer.querySelectorAll('.seat-label.couple-seat');
                    coupleSeats.forEach(label => {
                        label.style.width = coupleSeatWidth + 'px';
                        label.style.height = regularSeatHeight + 'px';
                        label.style.minWidth = coupleSeatWidth + 'px';
                        label.style.maxWidth = coupleSeatWidth + 'px';
                        label.style.minHeight = regularSeatHeight + 'px';
                        label.style.maxHeight = regularSeatHeight + 'px';
                    });
                }

                const seatNumbers = seatMapContainer.querySelectorAll('.seat-number');
                const seatIcons = seatMapContainer.querySelectorAll('.seat-icon');

                if (seatsPerRow < 12) {
                    // < 12 ghế: font size lớn hơn
                    seatNumbers.forEach(num => {
                        num.style.fontSize = '1.0rem';
                    });
                    seatIcons.forEach(icon => {
                        icon.style.fontSize = '0.55rem';
                    });
                } else {
                    // <= 15 ghế: font size vừa
                    seatNumbers.forEach(num => {
                        num.style.fontSize = '0.9rem';
                    });
                    seatIcons.forEach(icon => {
                        icon.style.fontSize = '0.5rem';
                    });
                }

                return; // CSS đã xử lý kích thước, không cần tính toán thêm
            }

            // Với > 15 ghế, tính toán động như cũ
            const containerWidth = seatMapContainer.offsetWidth - 60; // 60px cho padding và row-label
            const gap = 0.25; // Giảm gap xuống 0.25rem để tiết kiệm không gian
            const separatorWidth = 1.0; // Giảm separator xuống 1.0rem để tiết kiệm không gian

            // Đếm số separators (ước tính dựa trên số nhóm)
            const firstRow = seatMapContainer.querySelector('.seat-row');
            if (firstRow) {
                const separators = firstRow.querySelectorAll('.seat-group-separator');
                const separatorCount = separators.length;
                const seatCount = firstRow.querySelectorAll('.seat-label').length;

                // Tính toán kích thước ghế
                const totalGapWidth = (seatCount - 1) * gap * 16; // Convert rem to px (1rem = 16px)
                const totalSeparatorWidth = separatorCount * separatorWidth * 16;
                const availableWidth = containerWidth - totalGapWidth - totalSeparatorWidth;
                // Giảm kích thước tối đa xuống 14px để ghế nhỏ hơn, có thể xem hết ghế trong rạp
                const seatSize = Math.max(8, Math.min(14, availableWidth / seatCount));
                const seatGap = 6.4; // 0.4rem = 6.4px (giả sử 1rem = 16px)

                // Áp dụng kích thước (chỉ cho ghế thường, không phải ghế đôi)
                const seatLabels = seatMapContainer.querySelectorAll('.seat-label:not(.couple-seat)');
                seatLabels.forEach(label => {
                    label.style.width = seatSize + 'px';
                    label.style.height = seatSize + 'px';
                    label.style.minWidth = Math.max(8, seatSize * 0.7) + 'px';
                    label.style.minHeight = Math.max(8, seatSize * 0.7) + 'px';
                });

                // Tính toán và áp dụng kích thước cho ghế đôi (width = 2 * seatSize + gap)
                const coupleSeats = seatMapContainer.querySelectorAll('.seat-label.couple-seat');
                const coupleSeatWidth = (seatSize * 2) + seatGap;
                coupleSeats.forEach(label => {
                    label.style.width = coupleSeatWidth + 'px';
                    label.style.height = seatSize + 'px';
                    label.style.minWidth = coupleSeatWidth + 'px';
                    label.style.maxWidth = coupleSeatWidth + 'px';
                    label.style.minHeight = seatSize + 'px';
                    label.style.maxHeight = seatSize + 'px';
                });

                // Điều chỉnh font size của số ghế
                const seatNumbers = seatMapContainer.querySelectorAll('.seat-number');
                const fontSize = Math.max(4, Math.min(7, seatSize * 0.42));
                seatNumbers.forEach(num => {
                    num.style.fontSize = fontSize + 'px';
                });

                // Điều chỉnh icon size
                const seatIcons = seatMapContainer.querySelectorAll('.seat-icon');
                const iconSize = Math.max(2.5, Math.min(5.5, seatSize * 0.28));
                seatIcons.forEach(icon => {
                    icon.style.fontSize = iconSize + 'px';
                });
            }
        }

        // Gọi hàm điều chỉnh khi DOM sẵn sàng và khi resize
        setTimeout(adjustSeatSize, 100);
        window.addEventListener('resize', adjustSeatSize);

        // Khôi phục scroll position sau khi reload
        const savedScrollPos = sessionStorage.getItem('bookingScrollPos');
        if (savedScrollPos) {
            window.scrollTo(0, parseInt(savedScrollPos));
            sessionStorage.removeItem('bookingScrollPos');
        }

        // Timer system - Global để có thể sử dụng ở mọi nơi
        // Chỉ có 1 timer duy nhất khi chọn showtime, không reset khi chọn ghế
        let showtimeTimer = null;
        let showtimeStartTime = Date.now();
        const SHOWTIME_DURATION = 10 * 60 * 1000; // 10 minutes

        // Hàm attachCheckboxListeners để đảm bảo checkbox có thể click
        function attachCheckboxListeners() {
            const allCheckboxes = document.querySelectorAll('.seat-checkbox');
            allCheckboxes.forEach(function(checkbox) {
                // Đảm bảo checkbox có thể click được
                checkbox.style.pointerEvents = 'auto';
                checkbox.style.cursor = 'pointer';

                // Thêm click listener trực tiếp
                checkbox.addEventListener('click', function(e) {
                    // Cho phép checkbox tự xử lý click
                    e.stopPropagation();
                }, true);
            });
        }

        // Gọi attachCheckboxListeners để đảm bảo checkbox có thể click
        attachCheckboxListeners();

        // Kiểm tra xem có showtime được chọn từ URL không
        const urlParams = new URLSearchParams(window.location.search);
        const showtimeIdFromUrl = urlParams.get('showtime_id');
        const theaterIdFromUrl = urlParams.get('theater');
        console.log('Showtime ID from URL:', showtimeIdFromUrl);
        console.log('Theater ID from URL:', theaterIdFromUrl);

        if (showtimeIdFromUrl) {
            // Kiểm tra xem có thời gian bắt đầu đã lưu không (TRƯỚC KHI set mới)
            const savedStartTime = sessionStorage.getItem('showtimeStartTime');
            const savedShowtimeId = sessionStorage.getItem('selectedShowtimeId');

            // Chỉ so sánh showtime_id để tránh reset khi reload trang
            // Nếu showtime_id thay đổi hoặc chưa có timer, tạo mới
            if (!savedStartTime || showtimeIdFromUrl !== savedShowtimeId) {
                showtimeStartTime = Date.now();
                sessionStorage.setItem('showtimeStartTime', showtimeStartTime.toString());
                sessionStorage.setItem('selectedShowtimeId', showtimeIdFromUrl);
                console.log('Created new timer start time for showtime:', showtimeIdFromUrl);
            } else {
                // Sử dụng timer hiện có, KHÔNG reset khi reload
                showtimeStartTime = parseInt(savedStartTime);
                console.log('Using existing timer for showtime:', showtimeIdFromUrl, 'remaining:', Math.floor((SHOWTIME_DURATION - (Date.now() - showtimeStartTime)) / 1000), 'seconds');
            }

            // Đợi DOM load xong rồi khởi động timer NGAY
            setTimeout(function() {
                const timerElement = document.getElementById('reservation-timer');
                if (timerElement) {
                    console.log('Timer element found, starting timer...');
                    startShowtimeTimer();
                } else {
                    console.error('Timer element not found in DOM! Retrying...');
                    // Thử lại sau 500ms
                    setTimeout(function() {
                        startShowtimeTimer();
                    }, 500);
                }
            }, 100);
        }

        const checkboxes = document.querySelectorAll('.seat-checkbox');
        const totalAmountSpan = document.getElementById('total-amount');
        const totalSeatsSpan = document.getElementById('total-seats');
        const submitBtn = document.getElementById('submit-btn');
        const pricePerSeat = <?php echo (isset($showtime) && $showtime && isset($showtime['price'])) ? (int)$showtime['price'] : 0; ?>;

        // Seat layout config from PHP
        const seatLayout = <?php
                            if (isset($seatLayout) && $seatLayout !== null) {
                                echo json_encode($seatLayout, JSON_HEX_APOS | JSON_HEX_QUOT);
                            } else {
                                echo 'null';
                            }
                            ?>;
        // Giá ghế từ PHP (đã tính từ movie hoặc seatLayout)
        // Đảm bảo các biến giá ở global scope
        window.normalPrice = <?php echo isset($normalPrice) ? (int)$normalPrice : 120000; ?>;
        window.vipPrice = <?php echo isset($vipPrice) ? (int)$vipPrice : 180000; ?>;
        window.couplePrice = <?php echo isset($couplePrice) ? (int)$couplePrice : 240000; ?>;

        // Alias để tương thích
        const normalPrice = window.normalPrice;
        const vipPrice = window.vipPrice;
        const couplePrice = window.couplePrice;

        // Timer variables đã được khai báo ở trên

        // Kiểm tra vị trí đã lưu khi trang load
        const savedLocation = localStorage.getItem('userLocation');
        if (savedLocation) {
            try {
                const location = JSON.parse(savedLocation);
                const now = Date.now();
                // Nếu vị trí còn mới (dưới 1 giờ), hiển thị lại
                if (now - location.timestamp < 3600000) {
                    const locationInfo = document.getElementById('location-info');
                    const locationText = document.getElementById('location-text');
                    if (locationInfo && locationText) {
                        locationInfo.style.display = 'block';
                        locationText.innerHTML = `
                        <span class="text-info">
                            <i class="fas fa-map-marker-alt me-2"></i>
                            Vị trí đã lưu: ${location.lat.toFixed(6)}, ${location.lng.toFixed(6)}
                        </span>
                    `;
                        getAddressFromCoordinates(location.lat, location.lng);
                    }
                }
            } catch (e) {
                console.log('Error loading saved location:', e);
            }
        }

        // Sử dụng event delegation để xử lý tất cả checkbox (kể cả được thêm sau)
        const seatMapContainer = document.querySelector('.seat-map-container');
        if (seatMapContainer) {
            console.log('Seat map container found, attaching event listeners...');

            // Xử lý click vào label để trigger checkbox
            seatMapContainer.addEventListener('click', function(e) {
                try {
                    // Nếu click trực tiếp vào checkbox, để nó tự xử lý
                    if (e.target.type === 'checkbox' && e.target.classList.contains('seat-checkbox')) {
                        return; // Checkbox tự xử lý
                    }

                    // Tìm label gần nhất
                    const label = e.target.closest('.seat-label');
                    if (!label) return;

                    // Bỏ qua nếu ghế đã được đặt
                    if (label.classList.contains('booked') || label.classList.contains('reserved')) {
                        return;
                    }

                    // Tìm checkbox trong label
                    const checkbox = label.querySelector('.seat-checkbox');
                    if (!checkbox || checkbox.disabled) return;

                    // Ngăn chặn default behavior
                    e.preventDefault();
                    e.stopPropagation();

                    // Toggle checkbox
                    checkbox.checked = !checkbox.checked;

                    // Trigger change event
                    const changeEvent = new Event('change', {
                        bubbles: true
                    });
                    checkbox.dispatchEvent(changeEvent);
                } catch (error) {
                    console.error('Error handling seat click:', error);
                }
            });

            // Xử lý change event để validate sau khi checkbox được checked/unchecked
            seatMapContainer.addEventListener('change', function(e) {
                if (e.target.classList.contains('seat-checkbox')) {
                    const checkbox = e.target;
                    console.log('Checkbox changed:', checkbox.value, 'checked:', checkbox.checked);

                    // Nếu đang check
                    if (checkbox.checked) {
                        const selectedSeats = getSelectedSeats();
                        console.log('Selected seats:', selectedSeats);

                        // Chỉ kiểm tra giới hạn 8 vé khi chọn ghế
                        // Validation về quy tắc chọn ghế sẽ được kiểm tra khi nhấn nút thanh toán
                        if (selectedSeats.length > 8) {
                            checkbox.checked = false;
                            alert('Bạn chỉ có thể đặt tối đa 8 vé một lần!');
                            updateSelection();
                            return;
                        }
                    }

                    // Xử lý ghế đôi: khi chọn 1 ghế trong cặp thì tự động chọn ghế còn lại
                    if (checkbox.classList.contains('couple-seat-checkbox')) {
                        const coupleSeatId = checkbox.getAttribute('data-couple-seat');
                        const coupleCheckbox = document.querySelector('input[value="' + coupleSeatId + '"].couple-seat-checkbox');
                        if (coupleCheckbox && checkbox.checked) {
                            coupleCheckbox.checked = true;
                            // Trigger change event cho ghế đôi
                            const changeEvent = new Event('change', {
                                bubbles: true
                            });
                            coupleCheckbox.dispatchEvent(changeEvent);
                        } else if (coupleCheckbox && !checkbox.checked) {
                            coupleCheckbox.checked = false;
                            const changeEvent = new Event('change', {
                                bubbles: true
                            });
                            coupleCheckbox.dispatchEvent(changeEvent);
                        }
                    }

                    updateSelection();
                }
            });

            // Thêm event listener trực tiếp cho checkbox để đảm bảo change event được trigger
            seatMapContainer.addEventListener('change', function(e) {
                if (e.target.classList.contains('seat-checkbox')) {
                    // Change event đã được trigger tự động, không cần làm gì thêm
                }
            });

            // Gọi lại sau khi DOM được cập nhật
            setTimeout(attachCheckboxListeners, 100);
            setTimeout(attachCheckboxListeners, 500);
            setTimeout(attachCheckboxListeners, 1000);

            // Debug: Log số lượng checkbox
            setTimeout(function() {
                const allCheckboxes = document.querySelectorAll('.seat-checkbox');
                console.log('Total checkboxes found:', allCheckboxes.length);
                var availableCheckboxes = document.querySelectorAll('.seat-checkbox:not(:disabled)');
                console.log('Available checkboxes:', availableCheckboxes.length);

                // Kiểm tra pointer-events của checkbox
                allCheckboxes.forEach(function(cb) {
                    const style = window.getComputedStyle(cb);
                    console.log('Checkbox pointer-events:', style.pointerEvents, 'z-index:', style.zIndex);
                });
            }, 1000);

        } else {
            console.error('Seat map container not found!');
        }

        // Food items quantity change handler (both old and luxury)
        document.querySelectorAll('.food-quantity-input, .food-quantity-input-luxury').forEach(function(input) {
            input.addEventListener('change', function() {
                updateSelection();
            });
        });

        // Helper functions
        function getSelectedSeats() {
            const allCheckboxes = document.querySelectorAll('.seat-checkbox:checked');
            var values = Array.from(allCheckboxes).map(function(cb) {
                return cb.value;
            });
            var uniqueValues = [];
            for (var i = 0; i < values.length; i++) {
                if (uniqueValues.indexOf(values[i]) === -1) {
                    uniqueValues.push(values[i]);
                }
            }
            return uniqueValues;
        }

        // Đảm bảo function này ở global scope
        window.getSeatPrice = function(seat) {
            const label = document.querySelector('.seat-label[data-seat="' + seat + '"]');
            if (!label) return normalPrice || 120000;

            if (label.classList.contains('couple-seat')) {
                // Giá ghế đôi là giá cho cả cặp, mỗi ghế tính nửa giá
                return (couplePrice || 240000) / 2;
            } else if (label.classList.contains('vip-seat')) {
                return vipPrice || 180000;
            }
            return normalPrice || 120000;
        };

        // Alias để tương thích
        function getSeatPrice(seat) {
            if (typeof window.getSeatPrice === 'function') {
                return window.getSeatPrice(seat);
            }
            return normalPrice || 120000;
        }

        // Hàm xác định các nhóm ghế trong một hàng dựa vào khoảng cách giữa các ghế
        function getSeatGroupsInRow(row, rowSeats) {
            if (rowSeats.length === 0) return [];

            const sortedSeats = rowSeats.sort((a, b) => a - b);
            const groups = [];
            let currentGroup = [sortedSeats[0]];

            // Tìm khoảng cách lớn (>= 2) để phân chia nhóm
            for (let i = 1; i < sortedSeats.length; i++) {
                const gap = sortedSeats[i] - sortedSeats[i - 1];
                if (gap >= 2) {
                    // Khoảng cách lớn, bắt đầu nhóm mới
                    groups.push({
                        cols: currentGroup
                    });
                    currentGroup = [sortedSeats[i]];
                } else {
                    currentGroup.push(sortedSeats[i]);
                }
            }

            // Thêm nhóm cuối cùng
            if (currentGroup.length > 0) {
                groups.push({
                    cols: currentGroup
                });
            }

            return groups;
        }

        // Đảm bảo function này có thể truy cập từ global scope
        window.validateSeatSelection = function(seats) {
            if (seats.length === 0) return null;
            // Áp dụng validation cho cả trường hợp đặt 1 ghế

            const totalSeatCount = seats.length;

            // Group seats by row
            const seatsByRow = {};
            seats.forEach(function(seat) {
                const row = seat.charAt(0);
                const col = parseInt(seat.substring(1));
                if (!seatsByRow[row]) {
                    seatsByRow[row] = [];
                }
                seatsByRow[row].push(col);
            });

            // Validate each row
            for (const row in seatsByRow) {
                const cols = seatsByRow[row].sort((a, b) => a - b);

                // Kiểm tra không bỏ trống ghế ở giữa - KHÔNG cho phép gap giữa các ghế đã chọn (chỉ khi có >= 2 ghế)
                if (cols.length > 1) {
                    for (let i = 0; i < cols.length - 1; i++) {
                        const gap = cols[i + 1] - cols[i];
                        if (gap > 1) {
                            // Có gap ở giữa các ghế đã chọn
                            return 'Không được bỏ trống ghế ở giữa! Các ghế phải liền kề nhau. Vui lòng chọn các ghế liền kề.';
                        }
                    }
                }

                // Lấy danh sách tất cả các ghế trong hàng từ DOM để xác định các nhóm
                const rowSeats = [];
                const seatLabels = document.querySelectorAll(`.seat-label[data-seat^="${row}"]`);
                seatLabels.forEach(function(label) {
                    const seatValue = label.getAttribute('data-seat');
                    if (seatValue && seatValue.startsWith(row)) {
                        const col = parseInt(seatValue.substring(1));
                        if (!isNaN(col)) {
                            rowSeats.push(col);
                        }
                    }
                });

                if (rowSeats.length === 0) continue;

                // Xác định các nhóm ghế từ DOM (dựa vào khoảng cách giữa các ghế)
                const seatGroups = getSeatGroupsInRow(row, rowSeats);

                if (seatGroups.length === 0) {
                    // Nếu không xác định được nhóm, coi toàn bộ hàng là một nhóm
                    seatGroups.push({
                        cols: rowSeats.sort((a, b) => a - b)
                    });
                }

                // Kiểm tra từng nhóm ghế trong hàng
                for (let g = 0; g < seatGroups.length; g++) {
                    const group = seatGroups[g];
                    const groupCols = group.cols.sort((a, b) => a - b);

                    // Lọc các ghế được chọn thuộc nhóm này
                    const selectedColsInGroup = cols.filter(col => groupCols.indexOf(col) !== -1);
                    if (selectedColsInGroup.length === 0) continue; // Không có ghế nào được chọn trong nhóm này

                    const selectedSeatCountInGroup = selectedColsInGroup.length;

                    // Áp dụng validation cho cả trường hợp đặt 1 ghế

                    const minColInGroup = Math.min(...groupCols);
                    const maxColInGroup = Math.max(...groupCols);
                    const selectedMinCol = Math.min(...selectedColsInGroup);
                    const selectedMaxCol = Math.max(...selectedColsInGroup);

                    console.log(`Row ${row}, Group [${groupCols.join(',')}]: selectedCols=[${selectedColsInGroup.join(',')}], selectedSeatCount=${selectedSeatCountInGroup}`);

                    // Đếm tổng số ghế AVAILABLE trong nhóm (chưa bị đặt) - cần đếm trước để áp dụng quy tắc
                    let totalAvailableInGroup = 0;
                    groupCols.forEach(function(col) {
                        const checkSeat = row + col;
                        const seatLabel = document.querySelector(`.seat-label[data-seat="${checkSeat}"]`);
                        if (seatLabel && !seatLabel.classList.contains('booked') && !seatLabel.classList.contains('reserved')) {
                            totalAvailableInGroup++;
                        }
                    });

                    // Nếu không có ghế available trong nhóm, bỏ qua
                    if (totalAvailableInGroup === 0) continue;

                    // Kiểm tra xem có chọn ít nhất 1 trong 2 ghế ngoài cùng của nhóm không
                    const hasFirstSeat = selectedColsInGroup.indexOf(minColInGroup) !== -1;
                    const hasLastSeat = selectedColsInGroup.indexOf(maxColInGroup) !== -1;

                    console.log(`Row ${row}, Group: minCol=${minColInGroup}, maxCol=${maxColInGroup}, hasFirstSeat=${hasFirstSeat}, hasLastSeat=${hasLastSeat}, totalAvailableInGroup=${totalAvailableInGroup}`);

                    // Tìm ghế đã đặt gần nhất bên trái của selectedMinCol (hoặc ghế ngoài cùng nếu không có)
                    let nearestBookedSeatLeft = null;
                    for (let checkCol = selectedMinCol - 1; checkCol >= minColInGroup; checkCol--) {
                        if (groupCols.indexOf(checkCol) === -1) continue;
                        const checkSeat = row + checkCol;
                        const seatLabel = document.querySelector(`.seat-label[data-seat="${checkSeat}"]`);
                        if (seatLabel && (seatLabel.classList.contains('booked') || seatLabel.classList.contains('reserved'))) {
                            nearestBookedSeatLeft = checkCol;
                            break;
                        }
                    }
                    const startPoint = (nearestBookedSeatLeft !== null) ? nearestBookedSeatLeft : minColInGroup;

                    // Đếm số ghế AVAILABLE từ điểm đầu (ghế đã đặt gần nhất hoặc ghế ngoài cùng) đến ghế được chọn đầu tiên
                    // Lưu ý: Trong khoảng này phải không có ghế nào đã đặt
                    let availableSeatsAtStart = 0;
                    // Nếu startPoint là ghế đã đặt, bắt đầu đếm từ ghế tiếp theo
                    const countStart = (nearestBookedSeatLeft !== null) ? nearestBookedSeatLeft + 1 : minColInGroup;
                    for (let checkCol = countStart; checkCol < selectedMinCol; checkCol++) {
                        if (groupCols.indexOf(checkCol) === -1) continue;
                        const checkSeat = row + checkCol;
                        const seatLabel = document.querySelector(`.seat-label[data-seat="${checkSeat}"]`);
                        // Nếu gặp ghế đã đặt trong khoảng này, dừng đếm
                        if (seatLabel && (seatLabel.classList.contains('booked') || seatLabel.classList.contains('reserved'))) {
                            break;
                        }
                        // Chỉ đếm nếu ghế này available
                        if (seatLabel) {
                            availableSeatsAtStart++;
                            console.log(`Row ${row}, Group: Found available seat at start: ${checkSeat} (từ điểm đầu ${startPoint} đến ghế được chọn ${selectedMinCol})`);
                        }
                    }

                    // Tìm ghế đã đặt gần nhất bên phải của selectedMaxCol (hoặc ghế ngoài cùng nếu không có)
                    let nearestBookedSeatRight = null;
                    for (let checkCol = selectedMaxCol + 1; checkCol <= maxColInGroup; checkCol++) {
                        if (groupCols.indexOf(checkCol) === -1) continue;
                        const checkSeat = row + checkCol;
                        const seatLabel = document.querySelector(`.seat-label[data-seat="${checkSeat}"]`);
                        if (seatLabel && (seatLabel.classList.contains('booked') || seatLabel.classList.contains('reserved'))) {
                            nearestBookedSeatRight = checkCol;
                            break;
                        }
                    }
                    const endPoint = (nearestBookedSeatRight !== null) ? nearestBookedSeatRight : maxColInGroup;

                    // Đếm số ghế AVAILABLE từ ghế được chọn cuối cùng đến điểm cuối (ghế đã đặt gần nhất hoặc ghế ngoài cùng)
                    // Lưu ý: Trong khoảng này phải không có ghế nào đã đặt
                    let availableSeatsAtEnd = 0;
                    // Nếu endPoint là ghế đã đặt, kết thúc đếm trước ghế đó
                    const countEnd = (nearestBookedSeatRight !== null) ? nearestBookedSeatRight - 1 : maxColInGroup;
                    for (let checkCol = selectedMaxCol + 1; checkCol <= countEnd; checkCol++) {
                        if (groupCols.indexOf(checkCol) === -1) continue;
                        const checkSeat = row + checkCol;
                        const seatLabel = document.querySelector(`.seat-label[data-seat="${checkSeat}"]`);
                        // Nếu gặp ghế đã đặt trong khoảng này, dừng đếm
                        if (seatLabel && (seatLabel.classList.contains('booked') || seatLabel.classList.contains('reserved'))) {
                            break;
                        }
                        // Chỉ đếm nếu ghế này available
                        if (seatLabel) {
                            availableSeatsAtEnd++;
                            console.log(`Row ${row}, Group: Found available seat at end: ${checkSeat} (từ ghế được chọn ${selectedMaxCol} đến điểm cuối ${endPoint})`);
                        }
                    }

                    // Debug log
                    console.log(`Row ${row}, Group: totalColsInGroup=${groupCols.length}, totalAvailableInGroup=${totalAvailableInGroup}, selectedSeatCount=${selectedSeatCountInGroup}`);
                    console.log(`Row ${row}, Group: nearestBookedSeatLeft=${nearestBookedSeatLeft !== null ? nearestBookedSeatLeft : 'null'}, nearestBookedSeatRight=${nearestBookedSeatRight !== null ? nearestBookedSeatRight : 'null'}`);
                    console.log(`Row ${row}, Group: availableSeatsAtStart=${availableSeatsAtStart}, availableSeatsAtEnd=${availableSeatsAtEnd}`);


                    // QUY TẮC: Công thức tổng quát cho nhóm có X ghế available
                    // - Nếu một trong hai điểm bắt đầu có ghế đã đặt, thì có thể đặt ngay sau ghế đó (bỏ qua kiểm tra)
                    // - Nếu đặt số ghế >= X/2 và không có ghế đã đặt ở hai đầu: Bắt buộc phải đặt từ đầu hàng
                    // - Nếu đặt số ghế < X/2 và không đặt từ đầu hàng: Phải để lại >= 2 ghế ở đầu trái HOẶC đầu phải

                    const halfOfAvailable = Math.floor(totalAvailableInGroup / 2);

                    // Kiểm tra nếu đặt từ đầu hàng (chọn ít nhất 1 trong 2 ghế ngoài cùng) - OK
                    if (hasFirstSeat || hasLastSeat) {
                        console.log(`Row ${row}, Group: Validation OK - Đặt từ đầu hàng (hasFirstSeat=${hasFirstSeat}, hasLastSeat=${hasLastSeat})`);
                        continue; // Bỏ qua validation cho nhóm này
                    }

                    // Kiểm tra riêng cho trường hợp đặt 1 vé
                    if (selectedSeatCountInGroup === 1) {
                        // Quy tắc 1: Không được chọn ghế ngay sát ghế ngoài cùng (ghế thứ 2 từ đầu hoặc từ cuối)
                        if (selectedMinCol === minColInGroup + 1 || selectedMinCol === maxColInGroup - 1) {
                            console.log(`Row ${row}: Validation FAILED - Không được chọn ghế ngay sát ghế ngoài cùng (ghế ${selectedMinCol}, minCol=${minColInGroup}, maxCol=${maxColInGroup})`);
                            return `Không được chọn ghế ngay sát ghế ngoài cùng! Vui lòng chọn ghế ngoài cùng hoặc ghế khác.`;
                        }

                        // Quy tắc 2: Nếu giữa 2 ghế đã đặt có >= 3 ghế trống, không được đặt ghế ở giữa (cách cả 2 ghế đã đặt ít nhất 1 ghế)
                        // Tìm ghế đã đặt gần nhất bên trái
                        let nearestBookedLeft = null;
                        for (let checkCol = selectedMinCol - 1; checkCol >= minColInGroup; checkCol--) {
                            if (groupCols.indexOf(checkCol) === -1) continue;
                            const checkSeat = row + checkCol;
                            const seatLabel = document.querySelector(`.seat-label[data-seat="${checkSeat}"]`);
                            if (seatLabel && (seatLabel.classList.contains('booked') || seatLabel.classList.contains('reserved'))) {
                                nearestBookedLeft = checkCol;
                                break;
                            }
                        }

                        // Tìm ghế đã đặt gần nhất bên phải
                        let nearestBookedRight = null;
                        for (let checkCol = selectedMinCol + 1; checkCol <= maxColInGroup; checkCol++) {
                            if (groupCols.indexOf(checkCol) === -1) continue;
                            const checkSeat = row + checkCol;
                            const seatLabel = document.querySelector(`.seat-label[data-seat="${checkSeat}"]`);
                            if (seatLabel && (seatLabel.classList.contains('booked') || seatLabel.classList.contains('reserved'))) {
                                nearestBookedRight = checkCol;
                                break;
                            }
                        }

                        // Nếu có cả 2 ghế đã đặt ở 2 bên
                        if (nearestBookedLeft !== null && nearestBookedRight !== null) {
                            // Tính khoảng cách giữa 2 ghế đã đặt (số ghế trống)
                            const gapBetweenBooked = nearestBookedRight - nearestBookedLeft - 1;

                            // Nếu khoảng cách >= 3 ghế trống
                            if (gapBetweenBooked >= 3) {
                                // Kiểm tra xem ghế được chọn có cách cả 2 ghế đã đặt ít nhất 1 ghế không
                                const distanceFromLeft = selectedMinCol - nearestBookedLeft;
                                const distanceFromRight = nearestBookedRight - selectedMinCol;

                                // Nếu ghế được chọn cách cả 2 ghế đã đặt ít nhất 1 ghế (không phải ghế ngay sát)
                                if (distanceFromLeft > 1 && distanceFromRight > 1) {
                                    console.log(`Row ${row}: Validation FAILED - Đặt 1 vé (ghế ${selectedMinCol}) giữa 2 ghế đã đặt (ghế ${nearestBookedLeft} và ${nearestBookedRight}) có ${gapBetweenBooked} ghế trống, cách cả 2 ghế đã đặt ít nhất 1 ghế`);
                                    return `Không được đặt ghế ở giữa khi giữa 2 ghế đã đặt có 3 ghế trống trở lên! Vui lòng chọn ghế ngay sát một trong hai ghế đã đặt hoặc chọn ghế khác.`;
                                }
                            }
                        }
                    }

                    // Không đặt từ đầu hàng, kiểm tra các trường hợp khác (áp dụng cho cả 1 ghế)
                    // Nếu có ghế đã đặt ở một trong hai đầu, chỉ cho phép đặt NGAY SAU ghế đó (không có ghế ở giữa)
                    const isAdjacentToBookedLeft = (nearestBookedSeatLeft !== null && selectedMinCol === nearestBookedSeatLeft + 1);
                    const isAdjacentToBookedRight = (nearestBookedSeatRight !== null && selectedMaxCol === nearestBookedSeatRight - 1);

                    if (isAdjacentToBookedLeft || isAdjacentToBookedRight) {
                        console.log(`Row ${row}, Group: Validation OK - Đặt ngay sau ghế đã đặt (trái: ${isAdjacentToBookedLeft ? 'ghế ' + nearestBookedSeatLeft : 'no'}, phải: ${isAdjacentToBookedRight ? 'ghế ' + nearestBookedSeatRight : 'no'})`);
                        continue; // Bỏ qua validation cho nhóm này
                    }

                    // Kiểm tra khi đặt 2 ghế: Không được đặt nếu có ghế đã đặt cách 2 ô (bên trái hoặc phải)
                    // Trừ khi bên cạnh ghế được chọn đã có ghế đặt rồi (đã xử lý ở trên)
                    if (selectedSeatCountInGroup === 2) {
                        // Kiểm tra ghế đã đặt cách 2 ô về bên trái (từ ghế được chọn đầu tiên)
                        const seatTwoAwayLeft = selectedMinCol - 2;
                        if (seatTwoAwayLeft >= minColInGroup && groupCols.indexOf(seatTwoAwayLeft) !== -1) {
                            const checkSeatLeft = row + seatTwoAwayLeft;
                            const seatLabelLeft = document.querySelector(`.seat-label[data-seat="${checkSeatLeft}"]`);
                            if (seatLabelLeft && (seatLabelLeft.classList.contains('booked') || seatLabelLeft.classList.contains('reserved'))) {
                                // Kiểm tra xem bên cạnh ghế được chọn có ghế đã đặt không
                                const seatAdjacentLeft = selectedMinCol - 1;
                                if (seatAdjacentLeft >= minColInGroup && groupCols.indexOf(seatAdjacentLeft) !== -1) {
                                    const checkSeatAdjacentLeft = row + seatAdjacentLeft;
                                    const seatLabelAdjacentLeft = document.querySelector(`.seat-label[data-seat="${checkSeatAdjacentLeft}"]`);
                                    // Nếu bên cạnh không có ghế đã đặt, thì không được đặt
                                    if (!seatLabelAdjacentLeft || (!seatLabelAdjacentLeft.classList.contains('booked') && !seatLabelAdjacentLeft.classList.contains('reserved'))) {
                                        console.log(`Row ${row}, Group: Validation FAILED - Đặt 2 ghế nhưng có ghế đã đặt cách 2 ô về bên trái (ghế ${checkSeatLeft}) và bên cạnh không có ghế đã đặt`);
                                        return `Không được đặt ghế khi có ghế đã đặt cách 2 ô! Vui lòng chọn ghế khác.`;
                                    }
                                }
                            }
                        }

                        // Kiểm tra ghế đã đặt cách 2 ô về bên phải (từ ghế được chọn cuối cùng)
                        const seatTwoAwayRight = selectedMaxCol + 2;
                        if (seatTwoAwayRight <= maxColInGroup && groupCols.indexOf(seatTwoAwayRight) !== -1) {
                            const checkSeatRight = row + seatTwoAwayRight;
                            const seatLabelRight = document.querySelector(`.seat-label[data-seat="${checkSeatRight}"]`);
                            if (seatLabelRight && (seatLabelRight.classList.contains('booked') || seatLabelRight.classList.contains('reserved'))) {
                                // Kiểm tra xem bên cạnh ghế được chọn có ghế đã đặt không
                                const seatAdjacentRight = selectedMaxCol + 1;
                                if (seatAdjacentRight <= maxColInGroup && groupCols.indexOf(seatAdjacentRight) !== -1) {
                                    const checkSeatAdjacentRight = row + seatAdjacentRight;
                                    const seatLabelAdjacentRight = document.querySelector(`.seat-label[data-seat="${checkSeatAdjacentRight}"]`);
                                    // Nếu bên cạnh không có ghế đã đặt, thì không được đặt
                                    if (!seatLabelAdjacentRight || (!seatLabelAdjacentRight.classList.contains('booked') && !seatLabelAdjacentRight.classList.contains('reserved'))) {
                                        console.log(`Row ${row}, Group: Validation FAILED - Đặt 2 ghế nhưng có ghế đã đặt cách 2 ô về bên phải (ghế ${checkSeatRight}) và bên cạnh không có ghế đã đặt`);
                                        return `Không được đặt ghế khi có ghế đã đặt cách 2 ô! Vui lòng chọn ghế khác.`;
                                    }
                                }
                            }
                        }
                    }

                    // Không đặt từ đầu hàng và không đặt ngay sau ghế đã đặt, áp dụng quy tắc bình thường
                    if (selectedSeatCountInGroup >= halfOfAvailable) {
                        // Đặt >= X/2 ghế: Bắt buộc phải đặt từ đầu hàng
                        console.log(`Row ${row}, Group: Validation FAILED - Nhóm có ${totalAvailableInGroup} ghế available, đặt ${selectedSeatCountInGroup} vé (>= ${halfOfAvailable}) nhưng không đặt từ đầu hàng`);
                        return `Khi đặt từ ${halfOfAvailable} vé trở lên trong nhóm có ${totalAvailableInGroup} ghế trống, bắt buộc phải đặt từ đầu hàng (chọn ít nhất 1 trong 2 ghế ngoài cùng)!`;
                    } else {
                        // Đặt < X/2 ghế (bao gồm cả 1 ghế): Phải để lại >= 2 ghế ở cả hai đầu (nếu không đặt ngay sau ghế đã đặt)
                        if (availableSeatsAtStart < 2 || availableSeatsAtEnd < 2) {
                            console.log(`Row ${row}, Group: Validation FAILED - Nhóm có ${totalAvailableInGroup} ghế available, đặt ${selectedSeatCountInGroup} vé (< ${halfOfAvailable}) nhưng không đặt từ đầu hàng và không để lại ít nhất 2 ghế ở cả hai đầu (đầu trái: ${availableSeatsAtStart}, đầu phải: ${availableSeatsAtEnd})`);
                            return `Khi đặt ${selectedSeatCountInGroup} vé trong nhóm có ${totalAvailableInGroup} ghế trống mà không đặt từ đầu hàng, phải để lại ít nhất 2 ghế kể từ ghế ngoài cùng ở cả hai đầu hàng!`;
                        }
                    }
                }
            }

            return null;
        };

        // Đảm bảo function có thể truy cập từ global scope
        // (đã được gán vào window.validateSeatSelection ở trên)

        // Attach events cho checkbox hiện có (cho keyboard support)
        checkboxes.forEach(function(checkbox) {
            const label = checkbox.closest('.seat-label');
            if (label && !label.classList.contains('booked')) {
                label.setAttribute('tabindex', '0');
                label.setAttribute('role', 'checkbox');
                label.setAttribute('aria-checked', checkbox.checked);

                label.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        checkbox.checked = !checkbox.checked;
                        checkbox.dispatchEvent(new Event('change', {
                            bubbles: true
                        }));
                    }
                });
            }
        });

        // Ẩn form đồ ăn uống khi trang load lần đầu
        const foodSection = document.querySelector('.food-items-luxury-section');
        if (foodSection) {
            foodSection.style.display = 'none';
        }

        // Gọi updateSelection lần đầu để cập nhật trạng thái ban đầu
        updateSelection();

        function updateSelection() {
            // Lấy lại tất cả checkbox (có thể có checkbox mới được thêm vào)
            const allCheckboxes = document.querySelectorAll('.seat-checkbox');
            var checkedBoxes = Array.from(allCheckboxes).filter(function(cb) {
                return cb.checked;
            });
            var selected = checkedBoxes.map(function(cb) {
                return cb.value;
            });

            // Loại bỏ duplicate (cho ghế đôi có thể có 2 checkbox cùng value)
            var uniqueSelected = [];
            for (var i = 0; i < selected.length; i++) {
                if (uniqueSelected.indexOf(selected[i]) === -1) {
                    uniqueSelected.push(selected[i]);
                }
            }

            const emailContainer = document.getElementById('email-container');
            const emailInput = document.getElementById('customer_email');

            // Update visual
            allCheckboxes.forEach(function(cb) {
                const label = cb.closest('.seat-label');
                if (label) {
                    if (cb.checked) {
                        label.classList.add('selected');
                        label.classList.remove('available');
                        label.setAttribute('aria-checked', 'true');
                        // Đảm bảo giữ class vip-seat nếu có
                        if (label.classList.contains('vip-seat')) {
                            // vip-seat class đã có, không cần làm gì
                        }
                    } else {
                        // Chỉ xóa selected nếu cả 2 ghế trong cặp đều không được chọn (cho ghế đôi)
                        if (label.classList.contains('couple-seat')) {
                            const coupleSeatId = cb.getAttribute('data-couple-seat');
                            const coupleCheckbox = document.querySelector('input[value="' + coupleSeatId + '"].couple-seat-checkbox');
                            if (coupleCheckbox && !coupleCheckbox.checked) {
                                label.classList.remove('selected');
                                if (!label.classList.contains('booked') && !label.classList.contains('reserved')) {
                                    label.classList.add('available');
                                }
                                label.setAttribute('aria-checked', 'false');
                            }
                        } else {
                            label.classList.remove('selected');
                            if (!label.classList.contains('booked') && !label.classList.contains('reserved')) {
                                label.classList.add('available');
                                // Đảm bảo giữ class vip-seat nếu có
                                if (label.classList.contains('vip-seat')) {
                                    // vip-seat class đã có, không cần làm gì
                                }
                            }
                            label.setAttribute('aria-checked', 'false');
                        }
                    }
                }
            });

            // Calculate total price
            let seatTotal = 0;
            uniqueSelected.forEach(function(seat) {
                seatTotal += getSeatPrice(seat);
            });

            // Calculate food items total (both old and luxury inputs)
            let foodTotal = 0;
            // Old inputs
            document.querySelectorAll('.food-quantity-input').forEach(function(input) {
                const quantity = parseInt(input.value) || 0;
                const price = parseFloat(input.getAttribute('data-price')) || 0;
                foodTotal += quantity * price;
            });
            // Luxury inputs
            document.querySelectorAll('.food-quantity-input-luxury').forEach(function(input) {
                const quantity = parseInt(input.value) || 0;
                const price = parseFloat(input.getAttribute('data-price')) || 0;
                foodTotal += quantity * price;
            });

            const grandTotal = seatTotal + foodTotal;

            // Validate ghế để quyết định có cho phép chọn đồ uống không
            let seatsValid = false;
            let validationError = null;
            const errorMessageEl = document.getElementById('seat-validation-error');
            const errorTextEl = document.getElementById('seat-validation-error-text');

            if (uniqueSelected.length > 0) {
                if (typeof validateSeatSelection === 'function') {
                    validationError = validateSeatSelection(uniqueSelected);
                    seatsValid = (validationError === null);
                } else {
                    // Nếu không có function validate, coi như hợp lệ
                    seatsValid = true;
                }
            }

            // Ẩn thông báo lỗi khi chọn ghế (chỉ hiển thị khi click nút "Đặt vé")
            if (errorMessageEl) {
                errorMessageEl.style.display = 'none';
            }

            // Ẩn form đồ ăn uống mặc định (chỉ hiển thị khi click nút và ghế hợp lệ)
            const foodSection = document.querySelector('.food-items-luxury-section');
            if (foodSection) {
                foodSection.style.display = 'none';
            }

            if (uniqueSelected.length > 0) {
                totalAmountSpan.textContent = grandTotal.toLocaleString('vi-VN') + '₫';
                totalAmountSpan.setAttribute('aria-label', 'Tổng tiền ' + grandTotal.toLocaleString('vi-VN') + ' đồng');
                totalSeatsSpan.textContent = uniqueSelected.length + ' ghế' + (foodTotal > 0 ? ' + đồ ăn' : '');
                // Luôn enable nút submit, không disable dựa trên validation
                submitBtn.disabled = false;
                submitBtn.setAttribute('aria-label', 'Xác nhận đặt ' + uniqueSelected.length + ' vé');
                // Luôn set onclick để validate khi click
                submitBtn.onclick = function() {
                    openFoodModal();
                };

                // Hiển thị trường email
                if (emailContainer) {
                    emailContainer.style.display = 'block';
                }

                // Không start timer mới khi chọn ghế, chỉ dùng timer từ khi chọn showtime
            } else {
                totalAmountSpan.textContent = '0₫';
                totalAmountSpan.setAttribute('aria-label', 'Chưa chọn ghế nào');
                totalSeatsSpan.textContent = '0 ghế';
                submitBtn.disabled = true;
                submitBtn.onclick = null;

                // Ẩn trường email và reset về email mặc định của user
                if (emailContainer) {
                    emailContainer.style.display = 'none';
                }
                if (emailInput) {
                    // Reset về email mặc định của user (nếu có)
                    const userEmail = emailInput.getAttribute('data-user-email');
                    if (userEmail) {
                        emailInput.value = userEmail;
                    } else {
                        emailInput.value = '';
                    }
                }
            }

            // Return selected seats for use in override function
            return uniqueSelected;
        }

        function startShowtimeTimer() {
            const timerElement = document.getElementById('reservation-timer');
            const countdownElement = document.getElementById('timer-countdown');

            console.log('=== Starting showtime timer ===');
            console.log('Timer element:', timerElement);
            console.log('Countdown element:', countdownElement);

            if (!timerElement || !countdownElement) {
                console.error('Timer elements not found!');
                console.log('Looking for #reservation-timer and #timer-countdown');
                return;
            }

            // Lấy showtime_id và theater_id hiện tại từ URL
            const urlParams = new URLSearchParams(window.location.search);
            const showtimeIdFromUrl = urlParams.get('showtime_id');
            const theaterIdFromUrl = urlParams.get('theater');
            const currentShowtimeId = showtimeIdFromUrl || '';
            const currentTheaterId = theaterIdFromUrl || '';

            // Kiểm tra xem có thời gian bắt đầu đã lưu trong sessionStorage không
            const savedStartTime = sessionStorage.getItem('showtimeStartTime');
            const savedShowtimeId = sessionStorage.getItem('selectedShowtimeId');

            // Chỉ so sánh showtime_id để tránh reset khi reload
            if (savedStartTime && currentShowtimeId === savedShowtimeId) {
                // Sử dụng timer đã lưu, KHÔNG reset khi reload
                showtimeStartTime = parseInt(savedStartTime);
                console.log('Using existing timer for showtime:', currentShowtimeId);
            } else {
                // Tạo timer mới khi chọn showtime mới
                showtimeStartTime = Date.now();
                sessionStorage.setItem('showtimeStartTime', showtimeStartTime.toString());
                sessionStorage.setItem('selectedShowtimeId', currentShowtimeId);
                console.log('Starting new timer for showtime:', currentShowtimeId);
            }

            // Hiển thị timer element
            timerElement.style.display = 'block';
            console.log('Timer element displayed');

            // Clear timer cũ nếu có
            if (showtimeTimer) {
                clearInterval(showtimeTimer);
            }

            // Cập nhật ngay lập tức
            const elapsed = Date.now() - showtimeStartTime;
            const remaining = SHOWTIME_DURATION - elapsed;
            const minutes = Math.floor(remaining / 60000);
            const seconds = Math.floor((remaining % 60000) / 1000);
            countdownElement.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
            console.log('Initial countdown:', minutes + ':' + (seconds < 10 ? '0' : '') + seconds);

            showtimeTimer = setInterval(function() {
                const elapsed = Date.now() - showtimeStartTime;
                const remaining = SHOWTIME_DURATION - elapsed;

                if (remaining <= 0) {
                    clearInterval(showtimeTimer);
                    showtimeTimer = null;
                    alert('Thời gian đặt vé đã hết! Vui lòng chọn lại suất chiếu.');
                    // Xóa sessionStorage
                    sessionStorage.removeItem('selectedShowtimeId');
                    sessionStorage.removeItem('showtimeStartTime');
                    window.location.reload();
                    return;
                }

                const minutes = Math.floor(remaining / 60000);
                const seconds = Math.floor((remaining % 60000) / 1000);
                countdownElement.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
            }, 1000);

            console.log('Timer started successfully');
        }

        // Thêm event listener cho các time button để bắt đầu timer khi click
        document.querySelectorAll('.time-btn').forEach(function(timeBtn) {
            timeBtn.addEventListener('click', function(e) {
                // Lưu scroll position trước khi chuyển trang
                sessionStorage.setItem('bookingScrollPos', window.pageYOffset || document.documentElement.scrollTop);

                // Lấy showtime_id từ URL
                const url = new URL(this.href);
                const showtimeIdParam = url.searchParams.get('showtime_id');

                if (showtimeIdParam) {
                    // Lấy theater_id từ URL
                    const theaterIdParam = url.searchParams.get('theater') || '';

                    // Kiểm tra xem có phải showtime/theater mới không
                    const savedShowtimeId = sessionStorage.getItem('selectedShowtimeId');
                    const savedTheaterId = sessionStorage.getItem('selectedTheaterId');
                    const currentKey = showtimeIdParam + '_' + theaterIdParam;
                    const savedKey = (savedShowtimeId || '') + '_' + (savedTheaterId || '');

                    if (savedShowtimeId && currentKey !== savedKey) {
                        // Showtime hoặc theater đã thay đổi, reset timer
                        console.log('Showtime/theater changed from', savedKey, 'to', currentKey, '- Resetting timer');
                        sessionStorage.removeItem('showtimeStartTime');
                    }

                    // Lưu showtime_id, theater_id và thời gian bắt đầu mới vào sessionStorage
                    sessionStorage.setItem('selectedShowtimeId', showtimeIdParam);
                    if (theaterIdParam) {
                        sessionStorage.setItem('selectedTheaterId', theaterIdParam);
                    }
                    const startTime = Date.now();
                    sessionStorage.setItem('showtimeStartTime', startTime.toString());
                    console.log('Timer will start for showtime:', showtimeIdParam, 'theater:', theaterIdParam);

                    // Hiển thị timer ngay lập tức (không cần đợi reload)
                    const timerElement = document.getElementById('reservation-timer');
                    const countdownElement = document.getElementById('timer-countdown');

                    if (timerElement && countdownElement) {
                        // Hiển thị timer element ngay
                        timerElement.style.display = 'block';

                        // Khởi động timer ngay lập tức với thời gian mới
                        showtimeStartTime = startTime;

                        // Clear timer cũ nếu có
                        if (showtimeTimer) {
                            clearInterval(showtimeTimer);
                            showtimeTimer = null;
                        }

                        // Cập nhật ngay lập tức
                        const elapsed = Date.now() - showtimeStartTime;
                        const remaining = SHOWTIME_DURATION - elapsed;
                        const minutes = Math.floor(remaining / 60000);
                        const seconds = Math.floor((remaining % 60000) / 1000);
                        countdownElement.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;

                        // Bắt đầu đếm ngược
                        showtimeTimer = setInterval(function() {
                            const elapsed = Date.now() - showtimeStartTime;
                            const remaining = SHOWTIME_DURATION - elapsed;

                            if (remaining <= 0) {
                                clearInterval(showtimeTimer);
                                showtimeTimer = null;
                                alert('Thời gian đặt vé đã hết! Vui lòng chọn lại suất chiếu.');
                                sessionStorage.removeItem('selectedShowtimeId');
                                sessionStorage.removeItem('showtimeStartTime');
                                window.location.reload();
                                return;
                            }

                            const minutes = Math.floor(remaining / 60000);
                            const seconds = Math.floor((remaining % 60000) / 1000);
                            countdownElement.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
                        }, 1000);

                        console.log('Timer started immediately for showtime:', showtimeIdParam);
                    } else {
                        console.log('Timer elements not found, will start after page reload');
                    }
                }
            });
        });

        // Real-time seat reservation system
        <?php if ($selected_showtime_id): ?>
            const showtimeId = <?php echo $selected_showtime_id; ?>;
            let selectedSeats = [];
            let pollingInterval = null;
            let reservationTimeout = null;

            // Lưu showtime_id hiện tại để so sánh
            let currentShowtimeId = null;

            // Start timer when page loads with showtime selected
            console.log('=== Showtime ID from PHP:', showtimeId, '===');
            if (showtimeId) {
                // Kiểm tra xem có phải showtime mới không
                const savedShowtimeId = sessionStorage.getItem('selectedShowtimeId');
                if (savedShowtimeId && savedShowtimeId != showtimeId) {
                    // Showtime đã thay đổi, reset timer
                    console.log('Showtime changed from', savedShowtimeId, 'to', showtimeId);
                    sessionStorage.removeItem('showtimeStartTime');
                    sessionStorage.setItem('showtimeStartTime', Date.now().toString());
                }

                currentShowtimeId = showtimeId;
                sessionStorage.setItem('selectedShowtimeId', showtimeId.toString());

                // Đợi DOM load xong rồi khởi động timer
                setTimeout(function() {
                    console.log('Attempting to start timer for showtime:', showtimeId);
                    const timerEl = document.getElementById('reservation-timer');
                    const countdownEl = document.getElementById('timer-countdown');
                    console.log('Timer element exists:', !!timerEl);
                    console.log('Countdown element exists:', !!countdownEl);

                    if (timerEl && countdownEl) {
                        startShowtimeTimer();
                    } else {
                        console.error('Timer elements not found! Retrying...');
                        // Thử lại sau 500ms
                        setTimeout(function() {
                            startShowtimeTimer();
                        }, 500);
                    }
                }, 300);
            }

            // Reserve seats when selected
            function reserveSeats(seats) {
                if (seats.length === 0) return;

                fetch('?route=booking/reserve-seats-api', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            showtime_id: showtimeId,
                            seats: seats
                        })
                    })
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        if (data.success) {
                            // Extend reservation every 9 minutes (before 10 minutes expire)
                            reservationTimeout = setInterval(function() {
                                extendReservations(seats);
                            }, 9 * 60 * 1000);
                        }
                    })
                    .catch(function(error) {
                        console.error('Error reserving seats:', error);
                    });
            }

            // Release seats when deselected
            function releaseSeats(seats) {
                if (seats.length === 0) return;

                if (reservationTimeout) {
                    clearInterval(reservationTimeout);
                    reservationTimeout = null;
                }

                fetch('?route=booking/release-seats-api', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            showtime_id: showtimeId,
                            seats: seats
                        })
                    })
                    .catch(function(error) {
                        console.error('Error releasing seats:', error);
                    });
            }

            // Extend reservations
            function extendReservations(seats) {
                if (seats.length === 0) return;

                fetch('?route=booking/extend-reservation-api', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({
                            showtime_id: showtimeId,
                            seats: seats
                        })
                    })
                    .catch(function(error) {
                        console.error('Error extending reservations:', error);
                    });
            }

            // Check seat status real-time
            function checkSeatStatus() {
                fetch('?route=booking/get-seat-status-api&showtime_id=' + showtimeId)
                    .then(function(response) {
                        return response.json();
                    })
                    .then(function(data) {
                        if (data.success) {
                            updateSeatStatus(data.booked_seats, data.reserved_seats);
                        }
                    })
                    .catch(function(error) {
                        console.error('Error checking seat status:', error);
                    });
            }

            // Update seat visual status
            function updateSeatStatus(bookedSeats, reservedSeats) {
                document.querySelectorAll('.seat-label').forEach(label => {
                    const seat = label.getAttribute('data-seat');
                    if (!seat) return;

                    // Skip if seat is currently selected by this user
                    const checkbox = label.querySelector('.seat-checkbox');
                    if (checkbox && checkbox.checked) return;

                    // Remove all status classes
                    label.classList.remove('booked', 'reserved', 'available');

                    if (bookedSeats.includes(seat)) {
                        label.classList.add('booked');
                        // Remove checkbox if booked
                        if (checkbox) checkbox.remove();
                    } else if (reservedSeats[seat]) {
                        label.classList.add('reserved');
                        // Remove checkbox if reserved
                        if (checkbox) checkbox.remove();
                    } else {
                        label.classList.add('available');
                        // Re-add checkbox if available
                        if (!checkbox) {
                            const seatNum = label.querySelector('.seat-number').textContent;
                            const row = seat.charAt(0);
                            const col = seat.substring(1);
                            const newCheckbox = document.createElement('input');
                            newCheckbox.type = 'checkbox';
                            newCheckbox.name = 'seats[]';
                            newCheckbox.value = seat;
                            newCheckbox.className = 'seat-checkbox';
                            if (label.classList.contains('couple-seat')) {
                                newCheckbox.classList.add('couple-seat-checkbox');
                            }
                            label.insertBefore(newCheckbox, label.firstChild);
                        }
                    }
                });
            }

            // Start polling for seat status updates (every 2 seconds)
            if (showtimeId) {
                checkSeatStatus(); // Check immediately
                pollingInterval = setInterval(checkSeatStatus, 2000);

                // Clean up on page unload
                window.addEventListener('beforeunload', function() {
                    if (selectedSeats.length > 0) {
                        releaseSeats(selectedSeats);
                    }
                    if (pollingInterval) {
                        clearInterval(pollingInterval);
                    }
                });

                // Override updateSelection to handle reservations
                const originalUpdateSelection = updateSelection;
                updateSelection = function() {
                    // Gọi function gốc để cập nhật UI và lấy danh sách ghế đã chọn
                    const newSelected = originalUpdateSelection();

                    // Release seats that are no longer selected
                    var toRelease = selectedSeats.filter(function(seat) {
                        return newSelected.indexOf(seat) === -1;
                    });
                    if (toRelease.length > 0) {
                        releaseSeats(toRelease);
                    }

                    // Reserve newly selected seats
                    var toReserve = newSelected.filter(function(seat) {
                        return selectedSeats.indexOf(seat) === -1;
                    });
                    if (toReserve.length > 0) {
                        reserveSeats(toReserve);
                    }

                    selectedSeats = newSelected;
                };
            }
        <?php endif; ?>

        // Function validate và submit form
        function validateBookingForm(e) {
            // Lấy các ghế đã chọn (loại bỏ duplicate)
            const selectedCheckboxes = Array.from(document.querySelectorAll('.seat-checkbox:checked'));
            var seatValues = selectedCheckboxes.map(function(cb) {
                return cb.value;
            });
            var selectedSeatValues = [];
            for (var i = 0; i < seatValues.length; i++) {
                if (selectedSeatValues.indexOf(seatValues[i]) === -1) {
                    selectedSeatValues.push(seatValues[i]);
                }
            }

            // Debug log
            console.log('Form submit - Selected seats:', selectedSeatValues);
            console.log('Form submit - Checkboxes found:', selectedCheckboxes.length);
            var allFormCheckboxes = document.querySelectorAll('#booking-form input[name="seats[]"]');
            console.log('Form submit - All checkboxes in form:', allFormCheckboxes ? allFormCheckboxes.length : 0);

            // Validate: phải có ít nhất 1 ghế được chọn
            if (selectedSeatValues.length === 0) {
                e.preventDefault();
                alert('Vui lòng chọn ít nhất một ghế!');
                console.error('Validation failed: No seats selected');
                return false;
            }

            // Validate: Giới hạn 8 vé
            if (selectedSeatValues.length > 8) {
                e.preventDefault();
                alert('Bạn chỉ có thể đặt tối đa 8 vé một lần!');
                return false;
            }

            // Validate seat selection rules
            console.log('=== VALIDATION START (JavaScript) ===');
            console.log('Selected seats:', selectedSeatValues);
            const validationError = validateSeatSelection(selectedSeatValues);
            console.log('Validation result:', validationError || 'PASSED');
            console.log('=== VALIDATION END (JavaScript) ===');
            if (validationError) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                alert(validationError);
                console.error('Validation FAILED - Form submission prevented');
                // Re-enable submit button
                const submitBtn = document.getElementById('submit-btn');
                if (submitBtn) {
                    submitBtn.disabled = false;
                }
                return false;
            }

            // Validate: phải có email
            const emailInput = document.getElementById('customer_email');
            if (emailInput && !emailInput.value.trim()) {
                e.preventDefault();
                alert('Vui lòng nhập email để nhận vé!');
                emailInput.focus();
                console.error('Validation failed: No email');
                return false;
            }

            // Đảm bảo tất cả checkbox được checked và có name="seats[]"
            selectedCheckboxes.forEach(function(checkbox) {
                if (!checkbox.checked) {
                    checkbox.checked = true;
                }
                if (!checkbox.name || checkbox.name !== 'seats[]') {
                    checkbox.name = 'seats[]';
                }
                // Đảm bảo checkbox không bị disabled
                checkbox.disabled = false;
            });

            // Verify lại trước khi submit - lấy từ form
            const form = document.getElementById('booking-form');
            const formSeats = Array.from(form.querySelectorAll('input[name="seats[]"]:checked'));
            var seatValues = formSeats.map(function(cb) {
                return cb.value;
            });
            var finalSeats = [];
            for (var i = 0; i < seatValues.length; i++) {
                if (finalSeats.indexOf(seatValues[i]) === -1) {
                    finalSeats.push(seatValues[i]);
                }
            }
            console.log('Final seats to submit (from form):', finalSeats);
            var formDataObj = new FormData(form);
            console.log('Form data:', formDataObj.getAll('seats[]'));

            if (finalSeats.length === 0) {
                e.preventDefault();
                alert('Lỗi: Không thể xác định ghế đã chọn. Vui lòng thử lại!');
                console.error('Final validation failed: No seats found in form');
                return false;
            }

            // Disable submit button để tránh double submit
            const submitBtn = document.getElementById('submit-btn');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xử lý...';
            }

            // Update total info để hiển thị "Đang xử lý"
            const totalSeatsSpan = document.getElementById('total-seats');
            if (totalSeatsSpan) {
                totalSeatsSpan.textContent = finalSeats.length + ' ghế - Đang xử lý...';
            }

            // Release reservations của ghế đã chọn (vì đã được đặt rồi)
            <?php if ($selected_showtime_id): ?>
                if (typeof releaseSeats === 'function') {
                    releaseSeats(selectedSeatValues);
                }
                if (typeof reservationTimeout !== 'undefined' && reservationTimeout) {
                    clearInterval(reservationTimeout);
                    reservationTimeout = null;
                }
                // Stop polling vì đã đặt vé rồi
                if (typeof pollingInterval !== 'undefined' && pollingInterval) {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                }
            <?php endif; ?>

            // KHÔNG remove checkbox hoặc thay đổi UI ở đây - để form submit với dữ liệu đúng
            // Form sẽ tự động submit và redirect, sau đó server sẽ xử lý

            console.log('Form submitting with seats:', finalSeats);
            console.log('Form action:', form.action);
            return true; // Cho phép form submit
        }
    });

    // Support Form Toggle
    // Toggle Food Items Section (Luxury)
    function toggleFoodItemsLuxury() {
        const grid = document.getElementById('foodItemsGridLuxury');
        const icon = document.getElementById('foodToggleIconLuxury');
        if (grid && icon) {
            if (grid.style.display === 'none' || grid.style.display === '') {
                grid.style.display = 'grid';
                icon.style.transform = 'rotate(180deg)';
            } else {
                grid.style.display = 'none';
                icon.style.transform = 'rotate(0deg)';
            }
        }
    }

    // Toggle Food Items Section (Old - for compatibility)
    function toggleFoodItems() {
        const grid = document.getElementById('foodItemsGrid');
        const icon = document.getElementById('foodToggleIcon');

        if (grid && icon) {
            if (grid.style.display === 'none' || !grid.style.display) {
                grid.style.display = 'grid';
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            } else {
                grid.style.display = 'none';
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            }
        }
    }

    // Attach event listener for food toggle
    document.addEventListener('DOMContentLoaded', function() {
        // Luxury food toggle
        const foodToggleBtnLuxury = document.getElementById('foodToggleBtnLuxury');
        if (foodToggleBtnLuxury) {
            foodToggleBtnLuxury.addEventListener('click', toggleFoodItemsLuxury);
        }

        // Old food toggle (for compatibility)
        const foodToggleBtn = document.getElementById('foodToggleBtn');
        if (foodToggleBtn) {
            foodToggleBtn.addEventListener('click', toggleFoodItems);
        }

        // Food quantity buttons (Luxury) - increase/decrease
        // Modal functions đã được định nghĩa ở global scope ở trên

        // Modal quantity buttons
        document.querySelectorAll('.food-qty-btn-modal').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const action = this.getAttribute('data-action');
                const itemId = this.getAttribute('data-item-id');
                const input = document.getElementById('food_qty_modal_' + itemId);

                if (input) {
                    let currentValue = parseInt(input.value) || 0;
                    if (action === 'increase' && currentValue < 10) {
                        input.value = currentValue + 1;
                    } else if (action === 'decrease' && currentValue > 0) {
                        input.value = currentValue - 1;
                    }
                    updateModalFoodTotal();
                }
            });
        });

        // Modal input change
        document.querySelectorAll('.food-quantity-input-modal').forEach(function(input) {
            input.addEventListener('change', function() {
                let value = parseInt(this.value) || 0;
                if (value < 0) value = 0;
                if (value > 10) value = 10;
                this.value = value;
                updateModalFoodTotal();
            });
        });

        // Close modal when clicking outside
        const foodModal = document.getElementById('foodModal');
        if (foodModal) {
            foodModal.addEventListener('click', function(e) {
                if (e.target === foodModal) {
                    closeFoodModal();
                }
            });
        }

        document.querySelectorAll('.food-qty-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const action = this.getAttribute('data-action');
                const itemId = this.getAttribute('data-item-id');
                const input = document.querySelector(`.food-quantity-input-luxury[data-item-id="${itemId}"]`);

                if (input) {
                    let currentValue = parseInt(input.value) || 0;
                    if (action === 'increase' && currentValue < 10) {
                        input.value = currentValue + 1;
                    } else if (action === 'decrease' && currentValue > 0) {
                        input.value = currentValue - 1;
                    }

                    // Trigger change event to update total
                    input.dispatchEvent(new Event('change', {
                        bubbles: true
                    }));
                }
            });
        });
    });

    function toggleSupportForm() {
        const container = document.getElementById('supportFormContainer');
        const btn = document.getElementById('supportToggleBtn');

        if (container.style.display === 'none') {
            container.style.display = 'block';
            btn.style.display = 'none';
        } else {
            container.style.display = 'none';
            btn.style.display = 'block';
        }
    }

    // Location Detection
    function detectUserLocation() {
        const locationInfo = document.getElementById('location-info');
        const locationText = document.getElementById('location-text');
        const locationBtn = document.getElementById('location-detect-btn');
        const locationBtnText = document.getElementById('location-btn-text');

        if (!navigator.geolocation) {
            locationText.innerHTML = '<span class="text-warning">Trình duyệt của bạn không hỗ trợ xác định vị trí</span>';
            locationInfo.style.display = 'block';
            return;
        }

        // Hiển thị trạng thái đang tải
        locationBtn.disabled = true;
        locationBtnText.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xác định...';
        locationInfo.style.display = 'block';
        locationText.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Đang xác định vị trí của bạn...';

        navigator.geolocation.getCurrentPosition(
            function(position) {
                const latitude = position.coords.latitude;
                const longitude = position.coords.longitude;

                // Lưu vị trí vào localStorage
                localStorage.setItem('userLocation', JSON.stringify({
                    lat: latitude,
                    lng: longitude,
                    timestamp: Date.now()
                }));

                // Hiển thị tọa độ
                locationText.innerHTML = `
                <span class="text-success">
                    <i class="fas fa-check-circle me-2"></i>
                    Đã xác định vị trí: ${latitude.toFixed(6)}, ${longitude.toFixed(6)}
                </span>
            `;

                // Thử lấy địa chỉ từ reverse geocoding (nếu có thể)
                getAddressFromCoordinates(latitude, longitude);

                locationBtn.disabled = false;
                locationBtnText.innerHTML = '<i class="fas fa-redo me-2"></i>Cập nhật vị trí';

                // Sắp xếp rạp theo khoảng cách (nếu có thể)
                sortTheatersByDistance(latitude, longitude);
            },
            function(error) {
                let errorMessage = 'Không thể xác định vị trí. ';
                switch (error.code) {
                    case error.PERMISSION_DENIED:
                        errorMessage += 'Bạn đã từ chối quyền truy cập vị trí.';
                        break;
                    case error.POSITION_UNAVAILABLE:
                        errorMessage += 'Thông tin vị trí không khả dụng.';
                        break;
                    case error.TIMEOUT:
                        errorMessage += 'Yêu cầu xác định vị trí đã hết thời gian chờ.';
                        break;
                    default:
                        errorMessage += 'Đã xảy ra lỗi không xác định.';
                        break;
                }
                locationText.innerHTML = '<span class="text-warning"><i class="fas fa-exclamation-triangle me-2"></i>' + errorMessage + '</span>';
                locationBtn.disabled = false;
                locationBtnText.innerHTML = '<i class="fas fa-crosshairs me-2"></i>Xác định vị trí';
            }, {
                enableHighAccuracy: true,
                timeout: 10000,
                maximumAge: 0
            }
        );
    }

    // Reverse Geocoding - Lấy địa chỉ từ tọa độ
    function getAddressFromCoordinates(lat, lng) {
        // Sử dụng Nominatim API (OpenStreetMap) để reverse geocoding
        fetch('https://nominatim.openstreetmap.org/reverse?format=json&lat=' + lat + '&lon=' + lng + '&zoom=18&addressdetails=1')
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data && data.address) {
                    const address = data.address;
                    var addressString = '';

                    if (address.road) addressString += address.road + ', ';
                    if (address.suburb || address.village) addressString += (address.suburb || address.village) + ', ';
                    if (address.city || address.town || address.county) addressString += (address.city || address.town || address.county) + ', ';
                    if (address.state) addressString += address.state;

                    if (addressString) {
                        const locationText = document.getElementById('location-text');
                        var cleanAddress = addressString.trim().replace(/,\s*$/, '');
                        locationText.innerHTML = '<span class="text-success"><i class="fas fa-check-circle me-2"></i>Vị trí: ' + cleanAddress + '</span>';
                    }
                }
            })
            .catch(function(error) {
                console.log('Reverse geocoding failed:', error);
            });
    }

    // Sắp xếp rạp theo khoảng cách (nếu có tọa độ rạp)
    function sortTheatersByDistance(userLat, userLng) {
        const theaters = document.querySelectorAll('.theater-btn');
        const theatersArray = Array.from(theaters);

        // Tính khoảng cách và sắp xếp
        theatersArray.forEach(function(theater) {
            const locationSpan = theater.querySelector('.theater-location');
            // Có thể thêm logic tính khoảng cách nếu có tọa độ rạp trong database
            // Hiện tại chỉ hiển thị thông tin
        });
    }

    // AJAX functions để tránh reload trang
    function selectTheater(theaterId, movieId) {
        // Cập nhật active state cho các theater buttons
        document.querySelectorAll('.theater-btn, .more-theater-item').forEach(function(btn) {
            btn.classList.remove('active');
            if (btn.getAttribute('data-theater-id') == theaterId) {
                btn.classList.add('active');
                // Cập nhật style
                btn.style.borderColor = '#e50914';
                btn.style.color = '#e50914';
                btn.style.background = '#fff5f5';
                btn.style.fontWeight = 'bold';
            } else {
                btn.style.borderColor = '#ddd';
                btn.style.color = '#333';
                btn.style.background = 'white';
                btn.style.fontWeight = 'normal';
            }
        });

        // Cập nhật URL
        const url = new URL(window.location);
        url.searchParams.set('theater', theaterId);
        url.searchParams.set('movie', movieId);
        url.searchParams.delete('date');
        url.searchParams.delete('showtime_id');
        window.history.pushState({}, '', url);

        // Tìm hoặc tạo phần chọn ngày
        let dateSection = document.getElementById('date-selection-section') ||
            document.querySelector('.dates-scroll')?.closest('.booking-step');

        if (!dateSection) {
            // Tạo phần chọn ngày nếu chưa có
            const theaterSection = document.querySelector('.theaters-list')?.closest('.booking-step');
            if (theaterSection) {
                dateSection = document.createElement('div');
                dateSection.className = 'booking-step mb-4';
                dateSection.id = 'date-selection-section';

                // Tạo danh sách ngày (7 ngày tiếp theo)
                const dates = [];
                for (let i = 0; i < 7; i++) {
                    const date = new Date();
                    date.setDate(date.getDate() + i);
                    const dayNames = ['Chủ nhật', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7'];
                    const dayName = dayNames[date.getDay()];
                    const dateStr = date.toISOString().split('T')[0];
                    const dateLabel = String(date.getDate()).padStart(2, '0') + '/' + String(date.getMonth() + 1).padStart(2, '0');
                    dates.push({
                        value: dateStr,
                        label: dateLabel,
                        day_name: dayName
                    });
                }

                let datesHtml = '<label class="booking-label"><i class="fas fa-calendar-alt me-2"></i>Chọn ngày</label>';
                datesHtml += '<div class="dates-scroll" role="group" aria-label="Chọn ngày chiếu">';
                dates.forEach(function(dateItem) {
                    datesHtml += `
                    <a href="?route=booking/index&movie=${movieId}&theater=${theaterId}&date=${dateItem.value}"
                       class="date-btn"
                       data-date="${dateItem.value}"
                       data-movie-id="${movieId}"
                       data-theater-id="${theaterId}"
                       onclick="event.preventDefault(); selectDate('${dateItem.value}', ${movieId}, ${theaterId}); return false;"
                       aria-pressed="false"
                       aria-label="Chọn ngày ${dateItem.label}"
                       style="cursor: pointer;">
                        <span class="date-day">${dateItem.day_name}</span>
                        <span class="date-number">${dateItem.label}</span>
                    </a>
                `;
                });
                datesHtml += '</div>';
                dateSection.innerHTML = datesHtml;
                theaterSection.insertAdjacentElement('afterend', dateSection);
            }
        } else {
            // Cập nhật data attributes cho các date buttons
            dateSection.querySelectorAll('.date-btn').forEach(function(btn) {
                btn.setAttribute('data-theater-id', theaterId);
                btn.setAttribute('data-movie-id', movieId);
                // Cập nhật href
                const date = btn.getAttribute('data-date');
                if (date) {
                    btn.href = `?route=booking/index&movie=${movieId}&theater=${theaterId}&date=${date}`;
                    btn.setAttribute('onclick', `event.preventDefault(); selectDate('${date}', ${movieId}, ${theaterId}); return false;`);
                }
            });
        }

        // Hiển thị phần chọn ngày
        if (dateSection) {
            dateSection.style.display = 'block';
            // Đảm bảo nó không bị ẩn
            dateSection.style.visibility = 'visible';
            dateSection.style.opacity = '1';
        }

        // Ẩn phần chọn giờ và ghế
        const timeSection = document.querySelector('.times-grid')?.closest('.booking-step');
        if (timeSection) {
            timeSection.style.display = 'none';
        }

        // Scroll đến phần chọn ngày
        if (dateSection) {
            setTimeout(function() {
                dateSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            }, 100);
        }
    }

    function selectDate(date, movieId, theaterId) {
        // Cập nhật active state cho các date buttons
        document.querySelectorAll('.date-btn').forEach(function(btn) {
            btn.classList.remove('active');
            if (btn.getAttribute('data-date') == date) {
                btn.classList.add('active');
            }
        });

        // Cập nhật URL
        const url = new URL(window.location);
        url.searchParams.set('date', date);
        url.searchParams.set('movie', movieId);
        url.searchParams.set('theater', theaterId);
        url.searchParams.delete('showtime_id');
        window.history.pushState({}, '', url);

        // Load showtimes bằng AJAX
        loadShowtimes(movieId, theaterId, date);
    }

    function loadShowtimes(movieId, theaterId, date) {
        let timesGrid = document.querySelector('.times-grid');
        if (!timesGrid) {
            // Tạo times-grid nếu chưa có
            const dateSection = document.querySelector('.dates-scroll')?.closest('.booking-step');
            if (dateSection) {
                const newSection = document.createElement('div');
                newSection.className = 'booking-step mb-4';
                newSection.innerHTML = `
                <label class="booking-label">
                    <i class="fas fa-clock me-2"></i>Chọn giờ chiếu
                </label>
                <div class="times-grid" role="group" aria-label="Chọn giờ chiếu phim"></div>
            `;
                dateSection.insertAdjacentElement('afterend', newSection);
                timesGrid = newSection.querySelector('.times-grid');
            }
        }

        if (!timesGrid) return;

        // Hiển thị loading
        timesGrid.innerHTML = '<div style="text-align: center; padding: 20px; color: #999;"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>';
        timesGrid.closest('.booking-step').style.display = 'block';

        // Gọi API
        fetch(`?route=booking/getShowtimesApi&movie_id=${movieId}&theater_id=${theaterId}&date=${date}`)
            .then(response => response.json())
            .then(data => {
                if (data.success && data.showtimes && data.showtimes.length > 0) {
                    // Render showtimes
                    let html = '';
                    data.showtimes.forEach(function(showtime) {
                        html += `
                        <a href="?route=booking/index&movie=${movieId}&theater=${theaterId}&date=${date}&showtime_id=${showtime.id}"
                           class="time-btn"
                           data-showtime-id="${showtime.id}"
                           data-movie-id="${movieId}"
                           data-theater-id="${theaterId}"
                           data-date="${date}"
                           onclick="event.preventDefault(); selectShowtime(${showtime.id}, ${movieId}, ${theaterId}, '${date}'); return false;"
                           aria-pressed="false"
                           aria-label="Chọn suất chiếu lúc ${showtime.time}"
                           style="cursor: pointer;">
                            ${showtime.time}
                            <span class="time-price">${showtime.price}₫</span>
                        </a>
                    `;
                    });
                    timesGrid.innerHTML = html;
                } else {
                    timesGrid.innerHTML = `
                    <div class="no-showtimes">
                        <i class="fas fa-clock"></i>
                        <p>Không có suất chiếu nào trong ngày này</p>
                    </div>
                `;
                }

                // Scroll đến phần chọn giờ
                timesGrid.closest('.booking-step').scrollIntoView({
                    behavior: 'smooth',
                    block: 'nearest'
                });
            })
            .catch(error => {
                console.error('Error loading showtimes:', error);
                timesGrid.innerHTML = `
                <div class="no-showtimes">
                    <i class="fas fa-exclamation-triangle"></i>
                    <p>Có lỗi xảy ra khi tải suất chiếu. Vui lòng thử lại.</p>
                </div>
            `;
            });
    }

    function selectShowtime(showtimeId, movieId, theaterId, date) {
        // Cập nhật active state cho các time buttons
        document.querySelectorAll('.time-btn').forEach(function(btn) {
            btn.classList.remove('active');
            if (btn.getAttribute('data-showtime-id') == showtimeId) {
                btn.classList.add('active');
            }
        });

        // Cập nhật URL và reload để load phần chọn ghế (vì phần này phức tạp với seat map)
        const url = new URL(window.location);
        url.searchParams.set('showtime_id', showtimeId);
        url.searchParams.set('movie', movieId);
        url.searchParams.set('theater', theaterId);
        url.searchParams.set('date', date);
        window.location.href = url.toString();
    }
</script>
