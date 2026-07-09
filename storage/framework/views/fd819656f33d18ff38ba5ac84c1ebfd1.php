<?php $__env->startPush('scripts'); ?>
<script>
    window.bookingPageConfig = {
        currentMovieId: <?php echo json_encode(isset($movie) ? data_get($movie, 'id') : null, 512) ?>,
        currentUserId: <?php echo json_encode(Auth::id(), 15, 512) ?>,
        basePrice: <?php echo json_encode($basePrice ?? 90000, 15, 512) ?>,
        csrfToken: <?php echo json_encode(csrf_token(), 15, 512) ?>,
        ticketPurchaseCountdownSeconds: 600,
        routes: {
            bookingLocation: "<?php echo e(route('booking.location')); ?>",
            bookingShowtimes: "<?php echo e(route('api.booking.showtimes')); ?>",
            bookingSeatMap: "<?php echo e(route('api.booking.seatMap')); ?>",
            bookingReserveSeats: "<?php echo e(route('booking.reservations.reserve')); ?>",
            bookingReleaseSeats: "<?php echo e(route('booking.reservations.release')); ?>",
            bookingExtendSeats: "<?php echo e(route('booking.reservations.extend')); ?>",
        },
        flashError: <?php echo json_encode(session('error'), 15, 512) ?>,
        validationError: <?php echo json_encode($errors->any() ? $errors->first() : null, 15, 512) ?>,
    };
</script>
<?php echo app('Illuminate\Foundation\Vite')(['resources/js/booking.js']); ?>
<?php $__env->stopPush(); ?>

<?php
$title = 'Đặt Vé Xem Phim';
$meta_description = isset($movie) ? 'Đặt vé xem phim ' . $movie->title . ' tại CineHub. Chọn rạp, ngày, giờ và ghế ngồi phù hợp cho bạn.' : 'Đặt vé xem phim tại CineHub.';
$meta_keywords = 'đặt vé xem phim, vé xem phim online, mua vé xem phim, CineHub';
$meta_og_title = $title . ' - CineHub';
$meta_og_description = $meta_description;
?>

<?php $__env->startSection('content'); ?>
<section class="booking-page-section">
    <div class="container-fluid px-4">
        <div class="row g-4">
            <!-- Left Column: Movie Info -->
            <div class="col-lg-5">
                <?php if(isset($movie)): ?>
                <article class="booking-movie-info" itemscope itemtype="https://schema.org/Movie" style="position: sticky; top: 20px;">
                    <!-- Movie Poster -->
                    <div class="movie-poster-large mb-4">
                        <?php if($movie->thumbnail): ?>
                        <img id="img-moviee"
                            src="<?php echo e($movie->thumbnail); ?>"
                            alt="<?php echo e($movie->title); ?>"
                            class="img-fluid rounded"
                            itemprop="image"
                            style="max-height: 500px; width: 100%; object-fit: cover;">
                        <?php else: ?>
                        <div class="poster-placeholder">
                            <i class="fas fa-film fa-5x"></i>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="background_film_blur">
                        <img src="<?php echo e($movie->thumbnail ?? ''); ?>" alt="">
                    </div>

                    <!-- Movie Title -->
                    <h1 class="booking-movie-title" itemprop="name"><?php echo e($movie->title); ?></h1>

                    <!-- Movie Details -->
                    <div class="booking-movie-details">
                        <?php if($movie->rating): ?>
                        <div class="detail-item">
                            <span class="detail-label">Đánh giá:</span>
                            <span class="detail-value">
                                <i class="fas fa-star"></i>
                                <?php echo e(number_format($movie->rating, 1)); ?>/10
                            </span>
                        </div>
                        <?php endif; ?>

                        <?php if($movie->duration): ?>
                        <div class="detail-item">
                            <span class="detail-label">Thời lượng:</span>
                            <span class="detail-value"><?php echo e(floor($movie->duration / 60)); ?>h <?php echo e($movie->duration % 60); ?>m</span>
                        </div>
                        <?php endif; ?>

                        <?php if($movie->category): ?>
                        <div class="detail-item">
                            <span class="detail-label">Thể loại:</span>
                            <span class="detail-value"><?php echo e($movie->category->name); ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if($movie->country): ?>
                        <div class="detail-item">
                            <span class="detail-label">Quốc gia:</span>
                            <span class="detail-value"><?php echo e($movie->country); ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- Movie Description -->
                    <?php if($movie->description): ?>
                    <div class="booking-movie-description">
                        <h3>Mô tả</h3>
                        <p itemprop="description"><?php echo e($movie->description); ?></p>
                    </div>
                    <?php endif; ?>
                </article>
                <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-film"></i>
                    Vui lòng chọn một bộ phim để đặt vé
                </div>
                <?php endif; ?>
            </div>

            <!-- Right Column: Booking Form -->
            <div class="col-lg-7">
                <div class="booking-form-container">
                    <h2 class="booking-form-title">
                        <?php if(!isset($movie)): ?>
                        Đặt vé xem phim
                        <?php else: ?>
                        Chọn Lịch Chiếu & Ghế
                        <?php endif; ?>
                    </h2>

                    <?php if($errors->any()): ?>
                    <div class="alert alert-danger">
                        <ul>
                            <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                    <?php if(session('success')): ?>
                    <div class="alert alert-success">
                        <?php echo e(session('success')); ?>

                    </div>
                    <?php endif; ?>

                    <?php if(!isset($movie) && isset($allMovies)): ?>
                    <!-- Movies List - Display when no movie selected -->
                    <div class="booking-step mb-4">
                        <label class="booking-label">
                            <i class="fas fa-film me-2"></i>Danh sách phim đang chiếu
                        </label>
                        <?php if(count($allMovies) == 0): ?>
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Hiện tại chưa có phim nào đang chiếu rạp. Vui lòng quay lại sau!
                        </div>
                        <?php else: ?>
                        <div class="movies-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; margin-top: 15px;">
                            <?php $__currentLoopData = $allMovies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('booking.index', ['movie' => $m->id])); ?>"
                                class="movie-card-booking"
                                style="display: block; text-decoration: none; border: 2px solid #ddd; border-radius: 24px; overflow: hidden; transition: all 0.3s; background: white; cursor: pointer;"
                                onmouseover="this.style.borderColor='#e50914'; this.style.transform='translateY(-5px)'; this.style.boxShadow='0 5px 15px rgba(0,0,0,0.2)';"
                                onmouseout="this.style.borderColor='#ddd'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                <?php if($m->thumbnail): ?>
                                <img src="<?php echo e($m->thumbnail); ?>"
                                    alt="<?php echo e($m->title); ?>"
                                    style="width: 100%; height: 200px; object-fit: cover;">
                                <?php else: ?>
                                <div style="width: 100%; height: 200px; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-film" style="font-size: 48px; color: #999;"></i>
                                </div>
                                <?php endif; ?>
                                <div style="padding: 10px;">
                                    <h4 style="margin: 0; font-size: 14px; color: #333; font-weight: bold; text-align: center; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        <?php echo e($m->title); ?>

                                    </h4>
                                    <?php if($m->rating): ?>
                                    <div style="text-align: center; margin-top: 5px;">
                                        <i class="fas fa-star text-warning" style="font-size: 12px;"></i>
                                        <span style="font-size: 12px; color: #666;"><?php echo e(number_format($m->rating, 1)); ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                        <?php endif; ?>
                    </div>
                    <?php else: ?>

                    <form id="bookingForm" method="POST" action="<?php echo e(route('booking.processBooking')); ?>" class="booking-form" novalidate onsubmit="return validateFormBeforeSubmit()">
                        <?php echo csrf_field(); ?>

                        <!-- Hidden inputs for form submission -->
                        <input type="hidden" name="showtime_id" id="showtimeIdInput" value="<?php echo e(old('showtime_id', $selectedShowtimeId ?? '')); ?>">
                        <div id="seatsInputContainer" style="display: none;"></div>

                        <!-- Theater Selection as Cards -->
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label mb-0">
                                    <i class="fas fa-building me-2"></i>Chọn rạp cho phim này
                                </label>
                                <div id="userLocationBadge" style="display: none; font-size: 12px; padding: 6px 12px; background: rgba(40, 167, 69, 0.1); border-radius: 20px; color: #28a745;">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span id="userLocationText">Đang lấy vị trí...</span>
                                    <button type="button" class="btn btn-sm btn-link p-0 ms-2" onclick="requestUserLocation()" title="Lấy lại vị trí" style="font-size: 12px;">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>

                            <input type="hidden" name="theater_id" id="theaterIdInput">

                            <!-- Test button for debugging -->
                            <button type="button" onclick="alert('Button works! Theater cards: ' + document.querySelectorAll('.theater-card').length)" style="margin-bottom: 10px; padding: 8px 16px; background: #e50914; color: white; border: none; border-radius: 4px;">
                                🔍 Test Click (Debug)
                            </button>

                            <?php if(isset($theaters) && count($theaters) > 0): ?>
                            <div id="theatersContainer" class="theaters-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px;">
                                <?php $__currentLoopData = $theaters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $theater): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="theater-card"
                                    data-theater-id="<?php echo e($theater->id); ?>"
                                    data-lat="<?php echo e($theater->latitude ?? ''); ?>"
                                    data-lng="<?php echo e($theater->longitude ?? ''); ?>"
                                    data-location="<?php echo e($theater->location ?? ''); ?>"
                                    onclick="window.selectTheaterDirect(<?php echo e($theater->id); ?>)"
                                    style="border: 2px solid #ddd; border-radius: 12px; padding: 15px; cursor: pointer; transition: all 0.3s; background: white; position: relative; z-index: 1;">

                                    <div class="d-flex align-items-start" style="pointer-events: none;">
                                        <div class="theater-icon" style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                                            <i class="fas fa-film" style="color: white; font-size: 18px;"></i>
                                        </div>

                                        <div style="flex: 1;">
                                            <h5 style="margin: 0 0 5px 0; font-size: 16px; font-weight: bold; color: #333;">
                                                <?php echo e($theater->name); ?>

                                            </h5>

                                            <?php if($theater->location): ?>
                                            <p style="margin: 0; font-size: 13px; color: #666;">
                                                <i class="fas fa-map-marker-alt" style="color: #e50914;"></i>
                                                <?php echo e($theater->location); ?>

                                            </p>
                                            <?php endif; ?>

                                            <?php if($theater->address): ?>
                                            <p style="margin: 5px 0 0 0; font-size: 12px; color: #999;">
                                                <?php echo e($theater->address); ?>

                                            </p>
                                            <?php endif; ?>

                                            <div class="theater-distance" data-theater-id="<?php echo e($theater->id); ?>" style="margin-top: 8px; font-size: 12px; color: #28a745; display: none;">
                                                <i class="fas fa-route"></i>
                                                <span class="distance-text"></span>
                                            </div>
                                        </div>

                                        <div class="theater-check" style="display: none; position: absolute; top: 10px; right: 10px; width: 24px; height: 24px; background: #28a745; border-radius: 50%; color: white;">
                                            <i class="fas fa-check" style="font-size: 12px;"></i>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                            <?php else: ?>
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Hiện tại chưa có rạp nào chiếu phim này.
                            </div>
                            <?php endif; ?>
                        </div>

                        <style>
                            .theater-card:hover {
                                border-color: #e50914 !important;
                                box-shadow: 0 4px 12px rgba(229, 9, 20, 0.2);
                                transform: translateY(-2px);
                            }

                            .theater-card.selected {
                                border-color: #28a745 !important;
                                background: rgba(40, 167, 69, 0.05) !important;
                            }

                            .theater-card.selected .theater-check {
                                display: flex !important;
                            }

                            .dates-tabs .date-tab {
                                min-width: 90px;
                                padding: 12px 16px;
                                border: 2px solid #ddd;
                                border-radius: 8px;
                                background: #2a2a2a;
                                color: #fff;
                                text-align: center;
                                cursor: pointer;
                                transition: all 0.3s;
                                flex-shrink: 0;
                            }

                            .dates-tabs .date-tab:hover {
                                border-color: #e50914;
                                transform: translateY(-2px);
                            }

                            .dates-tabs .date-tab.selected {
                                border-color: #28a745;
                                background: rgba(40, 167, 69, 0.2);
                            }

                            .dates-tabs .date-tab .day-name {
                                font-size: 11px;
                                color: #999;
                                text-transform: uppercase;
                            }

                            .dates-tabs .date-tab .date-text {
                                font-size: 16px;
                                font-weight: bold;
                                margin-top: 4px;
                            }

                            .showtimes-grid .showtime-btn {
                                padding: 12px 16px;
                                border: 2px solid #ddd;
                                border-radius: 8px;
                                background: #2a2a2a;
                                color: #fff;
                                text-align: center;
                                cursor: pointer;
                                transition: all 0.3s;
                                font-size: 14px;
                                font-weight: 600;
                            }

                            .showtimes-grid .showtime-btn:hover {
                                border-color: #e50914;
                                transform: scale(1.05);
                            }

                            .showtimes-grid .showtime-btn.selected {
                                border-color: #28a745;
                                background: rgba(40, 167, 69, 0.2);
                            }

                            .showtimes-grid .showtime-btn .screen-info {
                                font-size: 11px;
                                color: #999;
                                margin-top: 4px;
                            }
                        </style>

                        <!-- Date Selection (hiển thị sau khi chọn rạp) -->
                        <div id="dateSelectionSection" class="form-group" style="display: none;">
                            <label class="form-label">
                                <i class="fas fa-calendar-alt me-2"></i>Chọn ngày xem
                            </label>
                            <div id="datesContainer" class="dates-tabs" style="display: flex; gap: 10px; overflow-x: auto; padding-bottom: 10px;">
                                <!-- Dates will be loaded via JavaScript when theater is selected -->
                            </div>
                        </div>

                        <!-- Showtime Selection (appears after date selection) -->
                        <div id="showtimeSelectionSection" class="form-group" style="display: none;">
                            <label class="form-label">
                                <i class="fas fa-clock me-2"></i>Chọn khung giờ chiếu
                            </label>
                            <div id="showtimesContainer" class="showtimes-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px;">
                                <!-- Showtimes will be loaded via JavaScript -->
                            </div>
                            <div id="reservationTimerBox" class="reservation-timer-box" style="display: none;">
                                <span>Thoi gian mua ve con lai:</span>
                                <span id="reservationTimerText" class="reservation-timer-text">10:00</span>
                            </div>
                        </div>

                        <!-- Seat Selection -->
                        <div id="seatSelectionSection" class="form-group" style="display: none;">
                            <label class="form-label">
                                <i class="fas fa-couch me-2"></i>Chọn Ghế
                                <span id="screenNameDisplay" style="margin-left: 10px; color: #ffc107; font-weight: bold;"></span>
                            </label>

                            <!-- Screen indicator -->
                            <div class="screen-indicator" style="margin: 20px 0; text-align: center;">
                                <div style="width: 80%; height: 4px; background: linear-gradient(to bottom, #fff, #666); margin: 0 auto; border-radius: 50%; box-shadow: 0 3px 10px rgba(255,255,255,0.4);"></div>
                                <p style="color: #999; margin-top: 10px; font-size: 12px;">Màn hình</p>
                            </div>

                            <!-- Seat map container -->
                            <div id="seatMap" class="seat-map-container" style="padding: 20px; background: #2a2a2a; border-radius: 8px; max-width: 600px; margin: 0 auto;">
                                <p class="text-center text-muted">Vui lòng chọn khung giờ chiếu</p>
                            </div>

                            <!-- Seat legend -->
                            <div class="seat-legend" style="display: flex; justify-content: center; gap: 20px; margin-top: 15px; flex-wrap: wrap;">
                                <div class="legend-item" style="display: flex; align-items: center; gap: 5px;">
                                    <div class="seat seat-legend-box"></div>
                                    <span style="font-size: 12px; color: #ccc;">Trống</span>
                                </div>
                                <div class="legend-item" style="display: flex; align-items: center; gap: 5px;">
                                    <div class="seat seat-legend-box seat-selected"></div>
                                    <span style="font-size: 12px; color: #ccc;">Đang chọn</span>
                                </div>
                                <div class="legend-item" style="display: flex; align-items: center; gap: 5px;">
                                    <div class="seat seat-legend-box seat-booked"></div>
                                    <span style="font-size: 12px; color: #ccc;">Đã đặt</span>
                                </div>
                                <div class="legend-item" style="display: flex; align-items: center; gap: 5px;">
                                    <div class="seat seat-legend-box seat-reserved"></div>
                                    <span style="font-size: 12px; color: #ccc;">Đang giữ chỗ</span>
                                </div>
                                <div class="legend-item" style="display: flex; align-items: center; gap: 5px;">
                                    <div class="seat seat-legend-box seat-vip"></div>
                                    <span style="font-size: 12px; color: #ccc;">VIP</span>
                                </div>
                                <div class="legend-item" style="display: flex; align-items: center; gap: 5px;">
                                    <div class="seat seat-legend-box seat-couple"></div>
                                    <span style="font-size: 12px; color: #ccc;">Đôi</span>
                                </div>
                            </div>
                        </div>

                        <!-- Email for ticket -->
                        <div id="emailSection" class="form-group" style="display: none;">
                            <label class="form-label">
                                <i class="fas fa-envelope me-2"></i>Email nhận vé
                            </label>
                            <input type="email"
                                name="customer_email"
                                id="customerEmail"
                                class="form-control"
                                placeholder="email@example.com"
                                value="<?php echo e(old('customer_email', Auth::check() ? Auth::user()->email : '')); ?>">
                            <small class="text-muted" style="font-size: 11px; display: block; margin-top: 5px;">
                                <i class="fas fa-info-circle"></i> Vé điện tử sẽ được gửi đến email này
                            </small>
                        </div>

                        <!-- Selected Seats Display -->
                        <div id="selectedSeatsDisplay" class="selected-seats-display" style="display: none;">
                            <strong>Ghế đã chọn:</strong>
                            <span id="seatsText"></span>
                        </div>

                        <!-- Confirm Seats Button -->
                        <div class="confirm-seats-section" style="margin: 15px 0;">
                            <button type="button" id="confirmSeatsBtn" onclick="confirmSeats()" disabled class="btn-confirm-seats" style="width: 100%; padding: 12px; background: #ffc107; color: #000; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; transition: all 0.3s;">
                                <i class="fas fa-check-circle"></i> Xác nhận chọn ghế
                            </button>
                            <button type="button" id="reselectSeatsBtn" onclick="reselectSeats()" style="display: none; width: 100%; padding: 12px; background: #6c757d; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; transition: all 0.3s;">
                                <i class="fas fa-redo"></i> Chọn lại ghế
                            </button>
                            
                        </div>

                        <style>
                            .btn-confirm-seats:hover:not(:disabled) {
                                background: #ffca2c;
                                transform: translateY(-2px);
                                box-shadow: 0 4px 8px rgba(255, 193, 7, 0.3);
                            }

                            .btn-confirm-seats:disabled {
                                opacity: 0.5;
                                cursor: not-allowed;
                            }

                            #reselectSeatsBtn:hover {
                                background: #5a6268;
                            }
                        </style>

                        <!-- Price Info Box -->
                        <div id="priceInfoBox" class="price-info-box" style="display: none; background: rgba(255, 193, 7, 0.1); border: 1px solid rgba(255, 193, 7, 0.3); padding: 15px; border-radius: 8px; margin-bottom: 15px;">
                            <h6 style="color: #ffc107; margin-bottom: 10px;">
                                <i class="fas fa-info-circle"></i> Thông tin giá vé
                            </h6>
                            <div style="font-size: 13px; color: #ccc;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                    <span><i class="fas fa-couch" style="color: #999;"></i> Ghế thường:</span>
                                    <span id="normalPriceDisplay">150.000đ</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                    <span><i class="fas fa-crown" style="color: #764ba2;"></i> Ghế VIP (+30%):</span>
                                    <span id="vipPriceDisplay">186.000đ</span>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span><i class="fas fa-heart" style="color: #f5576c;"></i> Ghế đôi (+50%/ghế):</span>
                                    <span id="couplePriceDisplay">210.000đ</span>
                                </div>
                            </div>
                        </div>

                        <!-- Food Items Section - iframe-style scrollable panel -->
                        <div id="foodSection" class="form-group" style="display: none;">
                            <label class="form-label" style="margin-bottom: 10px;">
                                <i class="fas fa-utensils me-2"></i>Combo Đồ Ăn & Nước (Tùy chọn)
                            </label>
                            <div class="food-iframe-shell">
                                <div class="food-iframe-header">
                                    <span><i class="fas fa-shopping-basket"></i> Chọn combo</span>
                                    <small>Cuộn xuống để xem thêm</small>
                                </div>
                                <div class="food-order-frame">
                                    <div id="foodItemsContainer" class="food-items-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px;">
                                        <?php
                                        $hasFoodItems = isset($foodItems) && count($foodItems) > 0;
                                        ?>

                                        <?php if($hasFoodItems): ?>
                                        <?php $__currentLoopData = $foodItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $food): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="food-item-card-compact" data-food-id="<?php echo e($food->id); ?>" data-food-price="<?php echo e($food->price); ?>" style="border: 2px solid #444; border-radius: 10px; padding: 10px; background: #2a2a2a; text-align: center; transition: all 0.3s; cursor: pointer;" onmouseover="this.style.borderColor='#ffc107'" onmouseout="this.style.borderColor='#444'">
                                            <?php if($food->image): ?>
                                            <img src="<?php echo e(storage_url($food->image)); ?>" alt="<?php echo e($food->name); ?>" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; margin: 0 auto 8px;">
                                            <?php else: ?>
                                            <div style="width: 50px; height: 50px; background: #444; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px;">
                                                <i class="fas fa-utensils" style="color: #666; font-size: 20px;"></i>
                                            </div>
                                            <?php endif; ?>
                                            <h6 style="margin: 0 0 5px 0; color: #fff; font-size: 12px; font-weight: 600; min-height: 32px; display: flex; align-items: center; justify-content: center;"><?php echo e($food->name); ?></h6>
                                            <p style="margin: 0 0 8px 0; color: #ffc107; font-weight: bold; font-size: 13px;"><?php echo e(number_format($food->price)); ?>đ</p>
                                            <div class="quantity-control" style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                                <button type="button" class="btn-quantity-compact" onclick="updateFoodQuantity(<?php echo e($food->id); ?>, -1)" style="width: 26px; height: 26px; border: 1px solid #666; background: #3a3a3a; color: #fff; border-radius: 4px; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">−</button>
                                                <input type="number" name="food_items[<?php echo e($food->id); ?>]" id="food_<?php echo e($food->id); ?>" value="0" min="0" max="10" readonly style="width: 40px; height: 26px; text-align: center; background: #1a1a1a; border: 1px solid #666; color: #fff; border-radius: 4px; font-size: 14px; font-weight: bold; padding: 0;">
                                                <button type="button" class="btn-quantity-compact" onclick="updateFoodQuantity(<?php echo e($food->id); ?>, 1)" style="width: 26px; height: 26px; border: 1px solid #666; background: #3a3a3a; color: #fff; border-radius: 4px; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">+</button>
                                            </div>
                                        </div>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        <?php else: ?>
                                        <p class="text-muted" style="text-align: center; grid-column: 1 / -1;">Không có combo đồ ăn nào</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <style>
                            .food-iframe-shell {
                                border: 2px solid rgba(255, 255, 255, 0.18);
                                border-radius: 14px;
                                overflow: hidden;
                                background: #151515;
                                box-shadow: 0 8px 24px rgba(0, 0, 0, 0.35);
                            }

                            .food-iframe-header {
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                                padding: 10px 14px;
                                background: linear-gradient(90deg, #2a2a2a, #1f1f1f);
                                border-bottom: 1px solid rgba(255, 255, 255, 0.08);
                                color: #fff;
                                font-size: 13px;
                                font-weight: 600;
                            }

                            .food-iframe-header small {
                                color: #aaa;
                                font-weight: normal;
                                font-size: 11px;
                            }

                            .food-order-frame {
                                max-height: 360px;
                                overflow-y: auto;
                                overflow-x: hidden;
                                padding: 14px;
                                background: #1f1f1f;
                                box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.04);
                            }

                            .food-order-frame::-webkit-scrollbar {
                                width: 10px;
                            }

                            .food-order-frame::-webkit-scrollbar-track {
                                background: #2a2a2a;
                                border-radius: 999px;
                            }

                            .food-order-frame::-webkit-scrollbar-thumb {
                                background: #b5121b;
                                border-radius: 999px;
                            }

                            .btn-quantity-compact:hover {
                                background: #4a4a4a !important;
                                transform: scale(1.05);
                            }

                            .food-item-card-compact:hover {
                                transform: translateY(-2px);
                                box-shadow: 0 4px 12px rgba(255, 193, 7, 0.2);
                            }
                        </style>

                        <!-- Payment Method Selection -->
                        <div id="paymentSection" class="form-group" style="display: none;">
                            <label class="form-label" style="margin-bottom: 10px;">
                                <i class="fas fa-credit-card me-2"></i>Phương thức thanh toán
                            </label>
                            <?php if(empty($vnpayConfigured)): ?>
                            <div class="alert alert-warning" style="font-size: 13px; margin-bottom: 10px;">
                                <i class="fas fa-exclamation-triangle"></i>
                                VNPay chưa cấu hình (.env). Bạn có thể thanh toán bằng <strong>Ví CineHub</strong>.
                            </div>
                            <?php endif; ?>
                            <div class="payment-methods" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                <label class="payment-method-card" style="border: 2px solid #444; border-radius: 10px; padding: 12px; cursor: pointer; transition: all 0.3s; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 8px; <?php echo e(empty($vnpayConfigured) ? 'opacity:0.55;' : ''); ?>">
                                    <input type="radio" name="payment_method" value="vnpay" <?php echo e(!empty($vnpayConfigured) ? 'checked' : 'disabled'); ?> style="position: absolute; opacity: 0;">
                                    <i class="fas fa-credit-card" style="color: #1e88e5; font-size: 24px;"></i>
                                    <div>
                                        <div style="color: #fff; font-weight: bold; font-size: 13px;">VNPay</div>
                                        <small style="color: #999; font-size: 11px;">Thẻ/QR</small>
                                    </div>
                                </label>
                                <label class="payment-method-card" style="border: 2px solid #444; border-radius: 10px; padding: 12px; cursor: pointer; transition: all 0.3s; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                    <input type="radio" name="payment_method" value="wallet" <?php echo e(empty($vnpayConfigured) ? 'checked' : ''); ?> style="position: absolute; opacity: 0;">
                                    <i class="fas fa-wallet" style="color: #28a745; font-size: 24px;"></i>
                                    <div>
                                        <div style="color: #fff; font-weight: bold; font-size: 13px;">Ví CineHub</div>
                                        <small style="color: #999; font-size: 11px;" id="walletBalance"><?php echo e(Auth::check() ? number_format(Auth::user()->points ?? 0) : 0); ?>đ</small>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <style>
                            .payment-method-card:has(input:checked) {
                                border-color: #ffc107 !important;
                                background: rgba(255, 193, 7, 0.1) !important;
                            }

                            .btn-quantity:hover {
                                background: #4a4a4a !important;
                            }
                        </style>

                        <!-- Price Summary -->
                        <div class="price-summary">
                            <div class="price-row">
                                <span>Giá vé (1 vé):</span>
                                <span id="unitPrice">0 ₫</span>
                            </div>
                            <div class="price-row">
                                <span>Số lượng ghế:</span>
                                <span id="quantity">0</span>
                            </div>
                            <div class="price-row">
                                <span>Tiền vé:</span>
                                <span id="seatsTotal">0 ₫</span>
                            </div>
                            <div id="foodSummaryRows" style="border-top: 1px dashed rgba(255,255,255,0.2); padding-top: 8px; margin-top: 8px; display: none;">
                                <!-- Food items will be added here dynamically -->
                            </div>
                            <div class="price-row total" style="border-top: 2px solid rgba(229, 9, 20, 0.5); margin-top: 8px; padding-top: 8px; font-size: 18px;">
                                <span style="font-weight: bold;">Tổng thanh toán:</span>
                                <span id="totalPrice" style="font-weight: bold; color: #ffc107;">0 ₫</span>
                            </div>
                        </div>

                        <!-- Terms -->
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="accept_terms" value="1">
                                <span>Tôi đồng ý với điều khoản và chính sách</span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn-book" id="bookBtn" disabled>
                            <i class="fas fa-credit-card"></i>
                            Tiếp tục thanh toán
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .booking-page-section {
        padding: 2rem 0;
        min-height: 100vh;
    }

    .booking-movie-info {
        position: relative;
        z-index: 2;
    }

    .booking-form-container {
        position: relative;
        z-index: 10;
        width: 100%;
        max-width: 100%;
        box-sizing: border-box;
        overflow-x: hidden;
        overflow-wrap: break-word;
    }

    /* Custom scrollbar for booking form */
    .booking-form-container::-webkit-scrollbar {
        width: 8px;
    }

    .booking-form-container::-webkit-scrollbar-track {
        background: rgba(255, 255, 255, 0.05);
        border-radius: 4px;
    }

    .booking-form-container::-webkit-scrollbar-thumb {
        background: rgba(229, 9, 20, 0.6);
        border-radius: 4px;
    }

    .booking-form-container::-webkit-scrollbar-thumb:hover {
        background: rgba(229, 9, 20, 0.8);
    }

    .background_film_blur {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        background-size: cover;
        background-position: center;
        filter: blur(15px);
        opacity: 0.2;
        z-index: -1;
    }

    .movie-poster-large {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
    }

    .movie-poster-large img {
        width: 100%;
        height: auto;
        display: block;
    }

    .poster-placeholder {
        background: #2a2a2a;
        padding: 3rem;
        text-align: center;
        color: #666;
    }

    .booking-movie-title {
        font-size: 2rem;
        color: #fff;
        margin: 1rem 0;
    }

    .booking-movie-details {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin: 1.5rem 0;
        padding: 1rem;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 8px;
    }

    .detail-item {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .detail-label {
        font-size: 0.8rem;
        color: #999;
    }

    .detail-value {
        color: #fff;
        font-weight: 500;
    }

    .booking-movie-description {
        margin-top: 1.5rem;
    }

    .booking-movie-description h3 {
        color: #fff;
        font-size: 1.1rem;
        margin: 0 0 0.5rem 0;
    }

    .booking-movie-description p {
        color: #ccc;
        line-height: 1.6;
        margin: 0;
    }

    .booking-form-container {
        background: #1a1a1a;
        border-radius: 12px;
        padding: 2rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
    }

    @media (min-width: 992px) {
        .booking-form-container {
            position: sticky;
            top: 20px;
            max-height: calc(100dvh - 40px);
            overflow-y: auto;
            overscroll-behavior: contain;
        }
    }

    .booking-form-title {
        color: #fff;
        margin-bottom: 1.5rem;
        font-size: 1.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        color: #fff;
        font-weight: 500;
        margin-bottom: 0.5rem;
    }

    .form-control {
        width: 100%;
        padding: 0.75rem 1rem;
        background: #2a2a2a;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 6px;
        color: #fff;
        font-size: 0.95rem;
    }

    .form-control:focus {
        outline: none;
        border-color: #e50914;
        box-shadow: 0 0 10px rgba(229, 9, 20, 0.3);
    }

    .seat-map {
        background: #2a2a2a;
        padding: 1rem;
        border-radius: 6px;
        min-height: 150px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .seat-map-container {
        max-height: 500px;
        max-width: 100%;
        overflow-y: auto;
        overflow-x: auto;
    }

    .seat-row {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 8px 0;
        gap: 8px;
        min-width: max-content;
    }

    .seat-row-label {
        width: 30px;
        text-align: center;
        color: #999;
        font-weight: bold;
        font-size: 14px;
    }

    .seat {
        width: 32px;
        height: 32px;
        background: #4a4a4a;
        border: 2px solid #666;
        border-radius: 6px 6px 2px 2px;
        cursor: pointer;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        font-weight: bold;
        color: #fff;
        position: relative;
    }

    .seat:hover:not(.seat-booked):not(.seat-reserved):not(.seat-disabled) {
        transform: scale(1.05);
        box-shadow: 0 0 10px rgba(255, 193, 7, 0.5);
    }

    .seat.seat-selected {
        background: #28a745 !important;
        border: 3px solid #ffc107 !important;
        color: white !important;
        box-shadow: none !important;
        transform: scale(1.05);
    }

    .seat.seat-selected.seat-vip {
        background: #28a745 !important;
        border: 3px solid #ffc107 !important;
    }

    .seat.seat-selected.seat-couple {
        background: #28a745 !important;
        border: 3px solid #ffc107 !important;
    }

    .seat.seat-booked {
        background: #dc3545;
        border-color: #dc3545;
        cursor: not-allowed;
        color: white;
    }

    .seat.seat-reserved {
        background: #ffc107;
        border-color: #ffc107;
        cursor: not-allowed;
        color: #000;
        opacity: 1;
    }

    .seat.seat-my-reserved {
        background: #28a745;
        border-color: #28a745;
        color: #fff;
    }

    .reservation-timer-box {
        margin-top: 10px;
        padding: 10px 12px;
        border-radius: 6px;
        background: rgba(255, 193, 7, 0.12);
        border: 1px solid #ffc107;
        color: #ffc107;
        font-size: 14px;
        text-align: center;
    }

    .reservation-timer-box strong,
    .reservation-timer-box .reservation-timer-text {
        margin-left: 6px;
        color: #fff;
        font-size: 16px;
        font-weight: 700;
    }

    .seat.seat-vip {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #764ba2;
    }

    .seat.seat-couple {
        width: 56px;
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border-color: #f5576c;
    }

    .seat.seat-my-reserved,
    .seat.seat-my-reserved.seat-vip,
    .seat.seat-my-reserved.seat-couple {
        background: #28a745 !important;
        border-color: #28a745 !important;
        color: #fff !important;
        opacity: 1 !important;
    }

    .seat-legend .seat-legend-box {
        width: 24px;
        height: 24px;
        min-width: 24px;
        cursor: default;
        pointer-events: none;
        transform: none !important;
        box-shadow: none !important;
        font-size: 0;
    }

    .seat-legend .seat-legend-box.seat-selected {
        border-width: 3px;
    }

    .seat-legend .seat-legend-box.seat-couple {
        width: 40px;
        min-width: 40px;
    }

    .seat.seat-disabled {
        background: transparent;
        border: none;
        cursor: default;
    }

    .seat-space {
        width: 32px;
        height: 32px;
    }

    .selected-seats-display {
        background: rgba(229, 9, 20, 0.1);
        border: 1px solid rgba(229, 9, 20, 0.3);
        padding: 1rem;
        border-radius: 6px;
        color: #fff;
        margin-bottom: 1rem;
    }

    .price-summary {
        background: rgba(229, 9, 20, 0.1);
        border: 1px solid rgba(229, 9, 20, 0.3);
        padding: 1rem;
        border-radius: 6px;
        margin-bottom: 1.5rem;
    }

    .price-row {
        display: flex;
        justify-content: space-between;
        color: #ccc;
        margin-bottom: 0.5rem;
    }

    .price-row.total {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 0.5rem;
        color: #fff;
        font-weight: bold;
        font-size: 1.1rem;
    }

    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #ccc;
        cursor: pointer;
    }

    .checkbox-label input[type="checkbox"] {
        cursor: pointer;
    }

    .btn-book {
        width: 100%;
        padding: 1rem;
        background: #e50914;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 1rem;
        font-weight: bold;
        cursor: pointer;
        transition: background 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    .btn-book:hover:not(:disabled) {
        background: #ff1f1f;
    }

    .btn-book:disabled {
        background: #666;
        cursor: not-allowed;
        opacity: 0.6;
    }

    .alert {
        padding: 1rem;
        border-radius: 6px;
        margin-bottom: 1rem;
    }

    .alert-danger {
        background: rgba(220, 53, 69, 0.1);
        border: 1px solid rgba(220, 53, 69, 0.3);
        color: #ff7b8f;
    }

    .alert-success {
        background: rgba(40, 167, 69, 0.1);
        border: 1px solid rgba(40, 167, 69, 0.3);
        color: #85ff9f;
    }

    .alert-warning {
        background: rgba(255, 193, 7, 0.1);
        border: 1px solid rgba(255, 193, 7, 0.3);
        color: #ffd649;
    }

    @media (max-width: 768px) {
        .booking-form-container {
            padding: 1rem;
            position: static;
            max-height: none;
            overflow: visible;
        }

        .booking-movie-details {
            grid-template-columns: 1fr;
        }
    }
</style>

<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\CineHub_DuAnTotNghiep\resources\views/booking/index.blade.php ENDPATH**/ ?>