@extends('layouts.app')

@php
    $title = 'Đặt Vé Xem Phim';
    $meta_description = isset($movie) ? 'Đặt vé xem phim ' . $movie->title . ' tại CineHub. Chọn rạp, ngày, giờ và ghế ngồi phù hợp cho bạn.' : 'Đặt vé xem phim tại CineHub.';
    $meta_keywords = 'đặt vé xem phim, vé xem phim online, mua vé xem phim, CineHub';
    $meta_og_title = $title . ' - CineHub';
    $meta_og_description = $meta_description;
@endphp

@section('content')
<section class="booking-page-section">
    <div class="container-fluid px-4">
        <div class="row g-4">
            <!-- Left Column: Movie Info -->
            <div class="col-lg-5">
                @if (isset($movie))
                    <article class="booking-movie-info" itemscope itemtype="https://schema.org/Movie" style="position: sticky; top: 20px;">
                        <!-- Movie Poster -->
                        <div class="movie-poster-large mb-4">
                            @if ($movie->thumbnail)
                                <img id="img-moviee" 
                                     src="{{ $movie->thumbnail }}" 
                                     alt="{{ $movie->title }}" 
                                     class="img-fluid rounded"
                                     itemprop="image"
                                     style="max-height: 500px; width: 100%; object-fit: cover;">
                            @else
                                <div class="poster-placeholder">
                                    <i class="fas fa-film fa-5x"></i>
                                </div>
                            @endif
                        </div>
                        <div class="background_film_blur">
                            <img src="{{ $movie->thumbnail ?? '' }}" alt="">
                        </div>
                        
                        <!-- Movie Title -->
                        <h1 class="booking-movie-title" itemprop="name">{{ $movie->title }}</h1>
                        
                        <!-- Movie Details -->
                        <div class="booking-movie-details">
                            @if ($movie->rating)
                                <div class="detail-item">
                                    <span class="detail-label">Đánh giá:</span>
                                    <span class="detail-value">
                                        <i class="fas fa-star"></i>
                                        {{ number_format($movie->rating, 1) }}/10
                                    </span>
                                </div>
                            @endif
                            
                            @if ($movie->duration)
                                <div class="detail-item">
                                    <span class="detail-label">Thời lượng:</span>
                                    <span class="detail-value">{{ floor($movie->duration / 60) }}h {{ $movie->duration % 60 }}m</span>
                                </div>
                            @endif
                            
                            @if ($movie->category)
                                <div class="detail-item">
                                    <span class="detail-label">Thể loại:</span>
                                    <span class="detail-value">{{ $movie->category->name }}</span>
                                </div>
                            @endif
                            
                            @if ($movie->country)
                                <div class="detail-item">
                                    <span class="detail-label">Quốc gia:</span>
                                    <span class="detail-value">{{ $movie->country }}</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Movie Description -->
                        @if ($movie->description)
                            <div class="booking-movie-description">
                                <h3>Mô tả</h3>
                                <p itemprop="description">{{ $movie->description }}</p>
                            </div>
                        @endif
                    </article>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-film"></i>
                        Vui lòng chọn một bộ phim để đặt vé
                    </div>
                @endif
            </div>
            
            <!-- Right Column: Booking Form -->
            <div class="col-lg-7">
                <div class="booking-form-container">
                    <h2 class="booking-form-title">
                        @if(!isset($movie))
                            Đặt vé xem phim
                        @else
                            Chọn Lịch Chiếu & Ghế
                        @endif
                    </h2>
                    
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    
                    @if(!isset($movie) && isset($allMovies))
                        <!-- Movies List - Display when no movie selected -->
                        <div class="booking-step mb-4">
                            <label class="booking-label">
                                <i class="fas fa-film me-2"></i>Danh sách phim đang chiếu
                            </label>
                            @if(count($allMovies) == 0)
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Hiện tại chưa có phim nào đang chiếu rạp. Vui lòng quay lại sau!
                                </div>
                            @else
                                <div class="movies-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; margin-top: 15px;">
                                    @foreach($allMovies as $m)
                                        <a href="?route=booking/index&movie={{ $m->id }}"
                                            class="movie-card-booking"
                                            style="display: block; text-decoration: none; border: 2px solid #ddd; border-radius: 8px; overflow: hidden; transition: all 0.3s; background: white; cursor: pointer;"
                                            onmouseover="this.style.borderColor='#e50914'; this.style.transform='translateY(-5px)'; this.style.boxShadow='0 5px 15px rgba(0,0,0,0.2)';"
                                            onmouseout="this.style.borderColor='#ddd'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                                            @if($m->thumbnail)
                                                <img src="{{ $m->thumbnail }}"
                                                    alt="{{ $m->title }}"
                                                    style="width: 100%; height: 200px; object-fit: cover;">
                                            @else
                                                <div style="width: 100%; height: 200px; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-film" style="font-size: 48px; color: #999;"></i>
                                                </div>
                                            @endif
                                            <div style="padding: 10px;">
                                                <h4 style="margin: 0; font-size: 14px; color: #333; font-weight: bold; text-align: center; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                                    {{ $m->title }}
                                                </h4>
                                                @if($m->rating)
                                                    <div style="text-align: center; margin-top: 5px;">
                                                        <i class="fas fa-star text-warning" style="font-size: 12px;"></i>
                                                        <span style="font-size: 12px; color: #666;">{{ number_format($m->rating, 1) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @else
                    
                    <form id="bookingForm" method="POST" action="{{ route('booking.processBooking') }}" class="booking-form" onsubmit="return checkAuth()">
                        @csrf
                        
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
                            
                            <input type="hidden" name="theater_id" id="theaterIdInput" required>
                            
                            @if (isset($theaters) && count($theaters) > 0)
                                <div id="theatersContainer" class="theaters-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px;">
                                    @foreach ($theaters as $theater)
                                        <div class="theater-card" 
                                             data-theater-id="{{ $theater->id }}"
                                             data-lat="{{ $theater->latitude ?? '' }}" 
                                             data-lng="{{ $theater->longitude ?? '' }}"
                                             data-location="{{ $theater->location ?? '' }}"
                                             onclick="selectTheater({{ $theater->id }})"
                                             style="border: 2px solid #ddd; border-radius: 12px; padding: 15px; cursor: pointer; transition: all 0.3s; background: white; position: relative;">
                                            
                                            <div class="d-flex align-items-start">
                                                <div class="theater-icon" style="width: 40px; height: 40px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 10px; display: flex; align-items: center; justify-content: center; margin-right: 12px;">
                                                    <i class="fas fa-film" style="color: white; font-size: 18px;"></i>
                                                </div>
                                                
                                                <div style="flex: 1;">
                                                    <h5 style="margin: 0 0 5px 0; font-size: 16px; font-weight: bold; color: #333;">
                                                        {{ $theater->name }}
                                                    </h5>
                                                    
                                                    @if($theater->location)
                                                        <p style="margin: 0; font-size: 13px; color: #666;">
                                                            <i class="fas fa-map-marker-alt" style="color: #e50914;"></i>
                                                            {{ $theater->location }}
                                                        </p>
                                                    @endif
                                                    
                                                    @if($theater->address)
                                                        <p style="margin: 5px 0 0 0; font-size: 12px; color: #999;">
                                                            {{ $theater->address }}
                                                        </p>
                                                    @endif
                                                    
                                                    <div class="theater-distance" data-theater-id="{{ $theater->id }}" style="margin-top: 8px; font-size: 12px; color: #28a745; display: none;">
                                                        <i class="fas fa-route"></i>
                                                        <span class="distance-text"></span>
                                                    </div>
                                                </div>
                                                
                                                <div class="theater-check" style="display: none; position: absolute; top: 10px; right: 10px; width: 24px; height: 24px; background: #28a745; border-radius: 50%; color: white; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-check" style="font-size: 12px;"></i>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Hiện tại chưa có rạp nào chiếu phim này.
                                </div>
                            @endif
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
                        
                        <!-- Date Selection (appears after theater selection) -->
                        <div id="dateSelectionSection" class="form-group" style="display: none;">
                            <label class="form-label">
                                <i class="fas fa-calendar-alt me-2"></i>Chọn ngày xem
                            </label>
                            <div id="datesContainer" class="dates-tabs" style="display: flex; gap: 10px; overflow-x: auto; padding-bottom: 10px;">
                                <!-- Dates will be loaded via JavaScript -->
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
                        </div>
                        
                        <input type="hidden" name="showtime_id" id="showtimeIdInput" required>
                        
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
                                    <div class="seat-box" style="width: 24px; height: 24px; background: #4a4a4a; border: 1px solid #666; border-radius: 4px;"></div>
                                    <span style="font-size: 12px; color: #ccc;">Trống</span>
                                </div>
                                <div class="legend-item" style="display: flex; align-items: center; gap: 5px;">
                                    <div class="seat-box" style="width: 24px; height: 24px; background: #28a745; border: 1px solid #28a745; border-radius: 4px;"></div>
                                    <span style="font-size: 12px; color: #ccc;">Đang chọn</span>
                                </div>
                                <div class="legend-item" style="display: flex; align-items: center; gap: 5px;">
                                    <div class="seat-box" style="width: 24px; height: 24px; background: #dc3545; border: 1px solid #dc3545; border-radius: 4px;"></div>
                                    <span style="font-size: 12px; color: #ccc;">Đã đặt</span>
                                </div>
                                <div class="legend-item" style="display: flex; align-items: center; gap: 5px;">
                                    <div class="seat-box" style="width: 24px; height: 24px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: 1px solid #764ba2; border-radius: 4px;"></div>
                                    <span style="font-size: 12px; color: #ccc;">VIP</span>
                                </div>
                                <div class="legend-item" style="display: flex; align-items: center; gap: 5px;">
                                    <div class="seat-box" style="width: 40px; height: 24px; background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: 1px solid #f5576c; border-radius: 4px;"></div>
                                    <span style="font-size: 12px; color: #ccc;">Đôi</span>
                                </div>
                            </div>
                            
                            <input type="hidden" name="seats[]" id="selectedSeatsInput">
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
                                   value="{{ Auth::check() ? Auth::user()->email : '' }}"
                                   required>
                            <small class="text-muted" style="font-size: 11px; display: block; margin-top: 5px;">
                                <i class="fas fa-info-circle"></i> Vé điện tử sẽ được gửi đến email này
                            </small>
                        </div>
                        
                        <!-- Selected Seats Display -->
                        <div id="selectedSeatsDisplay" class="selected-seats-display" style="display: none;">
                            <strong>Ghế đã chọn:</strong>
                            <span id="seatsText"></span>
                        </div>
                        
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
                        
                        <!-- Food Items Section -->
                        <div id="foodSection" class="form-group" style="display: none;">
                            <label class="form-label">
                                <i class="fas fa-utensils me-2"></i>Combo Đồ Ăn & Nước (Tùy chọn)
                            </label>
                            <div id="foodItemsContainer" class="food-items-grid" style="display: grid; grid-template-columns: 1fr; gap: 10px;">
                                @if(isset($foodItems) && count($foodItems) > 0)
                                    @foreach($foodItems as $food)
                                        <div class="food-item-card" style="border: 1px solid #444; border-radius: 8px; padding: 12px; background: #2a2a2a; display: flex; align-items: center; gap: 12px;">
                                            @if($food->image)
                                                <img src="{{ asset('storage/' . $food->image) }}" alt="{{ $food->name }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 6px;">
                                            @else
                                                <div style="width: 60px; height: 60px; background: #444; border-radius: 6px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="fas fa-utensils" style="color: #666;"></i>
                                                </div>
                                            @endif
                                            <div style="flex: 1;">
                                                <h6 style="margin: 0; color: #fff; font-size: 14px;">{{ $food->name }}</h6>
                                                <p style="margin: 5px 0; color: #ffc107; font-weight: bold;">{{ number_format($food->price) }}đ</p>
                                            </div>
                                            <div class="quantity-control" style="display: flex; align-items: center; gap: 8px;">
                                                <button type="button" class="btn-quantity" onclick="updateFoodQuantity({{ $food->id }}, -1)" style="width: 30px; height: 30px; border: 1px solid #666; background: #3a3a3a; color: #fff; border-radius: 4px; cursor: pointer;">-</button>
                                                <input type="number" name="food_items[{{ $food->id }}]" id="food_{{ $food->id }}" value="0" min="0" max="10" readonly style="width: 40px; text-align: center; background: #1a1a1a; border: 1px solid #666; color: #fff; border-radius: 4px;">
                                                <button type="button" class="btn-quantity" onclick="updateFoodQuantity({{ $food->id }}, 1)" style="width: 30px; height: 30px; border: 1px solid #666; background: #3a3a3a; color: #fff; border-radius: 4px; cursor: pointer;">+</button>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <p class="text-muted" style="text-align: center;">Không có combo đồ ăn nào</p>
                                @endif
                            </div>
                        </div>
                        
                        <!-- Payment Method Selection -->
                        <div id="paymentSection" class="form-group" style="display: none;">
                            <label class="form-label">
                                <i class="fas fa-credit-card me-2"></i>Phương thức thanh toán
                            </label>
                            <div class="payment-methods" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                <label class="payment-method-card" style="border: 2px solid #444; border-radius: 8px; padding: 15px; cursor: pointer; transition: all 0.3s;">
                                    <input type="radio" name="payment_method" value="vnpay" checked style="margin-right: 8px;">
                                    <div style="display: inline-block;">
                                        <div style="color: #fff; font-weight: bold; margin-bottom: 5px;">
                                            <i class="fas fa-credit-card" style="color: #1e88e5;"></i> VNPay
                                        </div>
                                        <small style="color: #999;">Thanh toán qua VNPay</small>
                                    </div>
                                </label>
                                <label class="payment-method-card" style="border: 2px solid #444; border-radius: 8px; padding: 15px; cursor: pointer; transition: all 0.3s;">
                                    <input type="radio" name="payment_method" value="wallet" style="margin-right: 8px;">
                                    <div style="display: inline-block;">
                                        <div style="color: #fff; font-weight: bold; margin-bottom: 5px;">
                                            <i class="fas fa-wallet" style="color: #28a745;"></i> Ví CineHub
                                        </div>
                                        <small style="color: #999;" id="walletBalance">Số dư: {{ Auth::check() ? number_format(Auth::user()->points ?? 0) : 0 }}đ</small>
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
                                <span>Số lượng:</span>
                                <span id="quantity">0</span>
                            </div>
                            <div class="price-row total">
                                <span>Tổng cộng:</span>
                                <span id="totalPrice">0 ₫</span>
                            </div>
                        </div>
                        
                        <!-- Terms -->
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="accept_terms" required>
                                <span>Tôi đồng ý với điều khoản và chính sách</span>
                            </label>
                        </div>
                        
                        <!-- Submit Button -->
                        <button type="submit" class="btn-book" id="bookBtn" disabled>
                            <i class="fas fa-credit-card"></i>
                            Tiếp tục thanh toán
                        </button>
                    </form>
                    @endif
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
        overflow-y: auto;
    }
    
    .seat-row {
        display: flex;
        justify-content: center;
        align-items: center;
        margin: 8px 0;
        gap: 8px;
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
    
    .seat:hover:not(.seat-booked):not(.seat-disabled) {
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
    
    .seat.seat-vip {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-color: #764ba2;
    }
    
    .seat.seat-couple {
        width: 56px;
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        border-color: #f5576c;
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
        }
        
        .booking-movie-details {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
// Global variables
let userLat = null;
let userLng = null;
let currentMovieId = {{ isset($movie) ? $movie->id : 'null' }};
let selectedTheaterId = null;
let selectedDate = null;
let selectedShowtimeId = null;

// Request user location when page loads
document.addEventListener('DOMContentLoaded', function() {
    @if(isset($movie))
        // Auto request location when movie is selected
        requestUserLocation();
    @endif
});

function selectTheater(theaterId) {
    // Remove previous selection
    document.querySelectorAll('.theater-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Select clicked theater
    const selectedCard = document.querySelector(`.theater-card[data-theater-id="${theaterId}"]`);
    if (selectedCard) {
        selectedCard.classList.add('selected');
    }
    
    // Set hidden input and variable
    document.getElementById('theaterIdInput').value = theaterId;
    selectedTheaterId = theaterId;
    
    // Reset date and showtime
    selectedDate = null;
    selectedShowtimeId = null;
    document.getElementById('showtimeIdInput').value = '';
    
    // Hide seat map
    document.getElementById('seatMap').innerHTML = '<p class="text-center text-muted">Vui lòng chọn lịch chiếu</p>';
    document.getElementById('showtimeSelectionSection').style.display = 'none';
    
    // Load dates
    loadDates();
}

function loadDates() {
    const datesSection = document.getElementById('dateSelectionSection');
    const datesContainer = document.getElementById('datesContainer');
    
    // Show dates section
    datesSection.style.display = 'block';
    
    // Generate 7 days from today
    const dates = [];
    const dayNames = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
    
    for (let i = 0; i < 7; i++) {
        const date = new Date();
        date.setDate(date.getDate() + i);
        
        const dateStr = date.toISOString().split('T')[0];
        const dayOfWeek = date.getDay();
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        
        dates.push({
            value: dateStr,
            dayName: dayNames[dayOfWeek],
            dateText: `${day}/${month}`,
            isToday: i === 0
        });
    }
    
    // Render date tabs
    let html = '';
    dates.forEach(date => {
        html += `
            <div class="date-tab" onclick="selectDate('${date.value}')" data-date="${date.value}">
                <div class="day-name">${date.dayName}${date.isToday ? ' (Hôm nay)' : ''}</div>
                <div class="date-text">${date.dateText}</div>
            </div>
        `;
    });
    
    datesContainer.innerHTML = html;
}

function selectDate(dateValue) {
    // Remove previous selection
    document.querySelectorAll('.date-tab').forEach(tab => {
        tab.classList.remove('selected');
    });
    
    // Select clicked date
    const selectedTab = document.querySelector(`.date-tab[data-date="${dateValue}"]`);
    if (selectedTab) {
        selectedTab.classList.add('selected');
    }
    
    selectedDate = dateValue;
    selectedShowtimeId = null;
    document.getElementById('showtimeIdInput').value = '';
    
    // Hide seat map
    document.getElementById('seatMap').innerHTML = '<p class="text-center text-muted">Vui lòng chọn khung giờ chiếu</p>';
    
    // Load showtimes
    loadShowtimes();
}

function loadShowtimes() {
    if (!selectedTheaterId || !selectedDate || !currentMovieId) {
        return;
    }
    
    const showtimesSection = document.getElementById('showtimeSelectionSection');
    const showtimesContainer = document.getElementById('showtimesContainer');
    
    // Show loading
    showtimesSection.style.display = 'block';
    showtimesContainer.innerHTML = '<p class="text-center text-muted">Đang tải...</p>';
    
    // Fetch showtimes
    const url = `{{ url('/?route=booking/api/showtimes') }}&movie_id=${currentMovieId}&theater_id=${selectedTheaterId}&date=${selectedDate}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            if (data.showtimes && data.showtimes.length > 0) {
                let html = '';
                data.showtimes.forEach(showtime => {
                    html += `
                        <div class="showtime-btn" onclick="selectShowtime(${showtime.id})" data-showtime-id="${showtime.id}">
                            <div>${showtime.show_time}</div>
                            <div class="screen-info">${showtime.screen_name} - ${showtime.screen_type}</div>
                        </div>
                    `;
                });
                showtimesContainer.innerHTML = html;
            } else {
                showtimesContainer.innerHTML = '<p class="text-center text-warning">Không có suất chiếu nào cho ngày này</p>';
            }
        })
        .catch(error => {
            console.error('Error loading showtimes:', error);
            showtimesContainer.innerHTML = '<p class="text-center text-danger">Lỗi khi tải lịch chiếu</p>';
        });
}

function selectShowtime(showtimeId) {
    // Remove previous selection
    document.querySelectorAll('.showtime-btn').forEach(btn => {
        btn.classList.remove('selected');
    });
    
    // Select clicked showtime
    const selectedBtn = document.querySelector(`.showtime-btn[data-showtime-id="${showtimeId}"]`);
    if (selectedBtn) {
        selectedBtn.classList.add('selected');
    }
    
    selectedShowtimeId = showtimeId;
    document.getElementById('showtimeIdInput').value = showtimeId;
    
    // Load seat map
    loadSeatMap(showtimeId);
}

function checkAuth() {
    @if(!Auth::check())
        alert('Vui lòng đăng nhập để tiếp tục đặt vé');
        window.location.href = '{{ route("login") }}?redirect=' + encodeURIComponent(window.location.href);
        return false;
    @endif
    return true;
}

function loadSeatMap(showtimeId) {
    const seatMapContainer = document.getElementById('seatMap');
    const seatSelectionSection = document.getElementById('seatSelectionSection');
    
    // Show seat selection section
    seatSelectionSection.style.display = 'block';
    seatMapContainer.innerHTML = '<p class="text-center text-muted">Đang tải sơ đồ ghế...</p>';
    
    // Fetch seat map data from API
    const url = `{{ route('api.booking.seatMap') }}?showtime_id=${showtimeId}`;
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            // Display screen name
            if (data.screen && data.screen.name) {
                const screenDisplay = document.getElementById('screenNameDisplay');
                if (screenDisplay) {
                    screenDisplay.textContent = `(${data.screen.name} - ${data.screen.type || '2D'})`;
                }
            }
            
            if (data.layout) {
                renderSeatMap(data.layout, data.bookedSeats || [], data.prices || {});
            } else {
                // Generate default seat layout
                generateDefaultSeatLayout(data.bookedSeats || []);
            }
        })
        .catch(error => {
            console.error('Error loading seat map:', error);
            generateDefaultSeatLayout([]);
        });
}

function generateDefaultSeatLayout(bookedSeats = []) {
    // Default 10 rows x 12 seats layout
    const rows = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J'];
    const seatsPerRow = 12;
    const seatMapContainer = document.getElementById('seatMap');
    
    let html = '';
    
    rows.forEach((row, rowIndex) => {
        html += `<div class="seat-row">`;
        html += `<div class="seat-row-label">${row}</div>`;
        
        // For row J (couple seats), show only 6 couple seats (each takes 2 spaces)
        if (row === 'J') {
            for (let col = 1; col <= 6; col++) {
                const seatNumber = `${row}${col}`;
                let seatClass = 'seat seat-couple';
                
                // Check if booked
                if (bookedSeats.includes(seatNumber)) {
                    seatClass += ' seat-booked';
                }
                
                // Aisle space after 3rd couple seat
                if (col === 4) {
                    html += `<div class="seat-space"></div>`;
                }
                
                const onclick = bookedSeats.includes(seatNumber) ? '' : `onclick="toggleSeat('${seatNumber}')"`;
                
                html += `<div class="${seatClass}" data-seat="${seatNumber}" ${onclick}>
                            ${col}
                         </div>`;
            }
        } else {
            // Normal rows
            for (let col = 1; col <= seatsPerRow; col++) {
                const seatNumber = `${row}${col}`;
                let seatClass = 'seat';
                
                // Check if booked
                if (bookedSeats.includes(seatNumber)) {
                    seatClass += ' seat-booked';
                }
                
                // VIP rows (middle rows D, E, F)
                if (['D', 'E', 'F'].includes(row)) {
                    seatClass += ' seat-vip';
                }
                
                // Aisle spaces (middle gap after seat 6)
                if (col === 7) {
                    html += `<div class="seat-space"></div>`;
                }
                
                const onclick = bookedSeats.includes(seatNumber) ? '' : `onclick="toggleSeat('${seatNumber}')"`;
                
                html += `<div class="${seatClass}" data-seat="${seatNumber}" ${onclick}>
                            ${col}
                         </div>`;
            }
        }
        
        html += `</div>`;
    });
    
    seatMapContainer.innerHTML = html;
}

let selectedSeats = [];

function toggleSeat(seatNumber) {
    const seatElement = document.querySelector(`.seat[data-seat="${seatNumber}"]`);
    
    if (!seatElement || seatElement.classList.contains('seat-booked')) {
        return; // Can't select booked seats
    }
    
    if (seatElement.classList.contains('seat-selected')) {
        // Deselect
        seatElement.classList.remove('seat-selected');
        selectedSeats = selectedSeats.filter(s => s !== seatNumber);
    } else {
        // Check max 8 seats
        if (selectedSeats.length >= 8) {
            alert('Chỉ được đặt tối đa 8 ghế');
            return;
        }
        
        // Select
        seatElement.classList.add('seat-selected');
        selectedSeats.push(seatNumber);
    }
    
    updateBookingSummary();
}

function updateBookingSummary() {
    const quantity = selectedSeats.length;
    
    // Update selected seats input
    document.getElementById('selectedSeatsInput').value = JSON.stringify(selectedSeats);
    
    // Show/hide sections based on seat selection
    const emailSection = document.getElementById('emailSection');
    const priceInfoBox = document.getElementById('priceInfoBox');
    const foodSection = document.getElementById('foodSection');
    const paymentSection = document.getElementById('paymentSection');
    
    if (quantity > 0) {
        if (emailSection) emailSection.style.display = 'block';
        if (priceInfoBox) priceInfoBox.style.display = 'block';
        if (foodSection) foodSection.style.display = 'block';
        if (paymentSection) paymentSection.style.display = 'block';
    } else {
        if (emailSection) emailSection.style.display = 'none';
        if (priceInfoBox) priceInfoBox.style.display = 'none';
        if (foodSection) foodSection.style.display = 'none';
        if (paymentSection) paymentSection.style.display = 'none';
    }
    
    // Update display
    if (quantity > 0) {
        document.getElementById('selectedSeatsDisplay').style.display = 'block';
        document.getElementById('seatsText').textContent = selectedSeats.join(', ');
        
        // Calculate prices (default prices, should be from server)
        const basePrice = {{ isset($basePrice) ? $basePrice : 90000 }};
        const normalPrice = basePrice;
        const vipPrice = Math.round(basePrice * 1.3);
        const couplePrice = Math.round(basePrice * 1.5);
        
        // Update price display
        document.getElementById('normalPriceDisplay').textContent = new Intl.NumberFormat('vi-VN').format(normalPrice) + 'đ';
        document.getElementById('vipPriceDisplay').textContent = new Intl.NumberFormat('vi-VN').format(vipPrice) + 'đ';
        document.getElementById('couplePriceDisplay').textContent = new Intl.NumberFormat('vi-VN').format(couplePrice) + 'đ';
        
        let totalPrice = 0;
        let seatBreakdown = { normal: 0, vip: 0, couple: 0 };
        
        selectedSeats.forEach(seat => {
            const row = seat.charAt(0);
            // VIP rows: D, E, F
            if (['D', 'E', 'F'].includes(row)) {
                totalPrice += vipPrice;
                seatBreakdown.vip++;
            }
            // Couple row: J only
            else if (row === 'J') {
                totalPrice += couplePrice;
                seatBreakdown.couple++;
            }
            // Normal
            else {
                totalPrice += normalPrice;
                seatBreakdown.normal++;
            }
        });
        
        // Add food items to total
        const foodInputs = document.querySelectorAll('input[name^="food_items"]');
        let foodTotal = 0;
        foodInputs.forEach(input => {
            const qty = parseInt(input.value) || 0;
            if (qty > 0) {
                const foodCard = input.closest('.food-item-card');
                const priceText = foodCard.querySelector('p').textContent;
                const price = parseInt(priceText.replace(/[^0-9]/g, ''));
                foodTotal += price * qty;
            }
        });
        
        totalPrice += foodTotal;
        
        // Build quantity text
        let quantityText = quantity + ' ghế';
        if (seatBreakdown.normal > 0) quantityText += ` (${seatBreakdown.normal} thường`;
        if (seatBreakdown.vip > 0) quantityText += `${seatBreakdown.normal > 0 ? ', ' : ' ('}${seatBreakdown.vip} VIP`;
        if (seatBreakdown.couple > 0) quantityText += `${(seatBreakdown.normal > 0 || seatBreakdown.vip > 0) ? ', ' : ' ('}${seatBreakdown.couple} đôi`;
        if (seatBreakdown.normal > 0 || seatBreakdown.vip > 0 || seatBreakdown.couple > 0) quantityText += ')';
        
        document.getElementById('quantity').textContent = quantityText;
        document.getElementById('unitPrice').textContent = new Intl.NumberFormat('vi-VN').format(basePrice) + ' ₫';
        document.getElementById('totalPrice').textContent = new Intl.NumberFormat('vi-VN').format(totalPrice) + ' ₫';
        document.getElementById('bookBtn').disabled = false;
    } else {
        document.getElementById('selectedSeatsDisplay').style.display = 'none';
        document.getElementById('bookBtn').disabled = true;
        
        document.getElementById('quantity').textContent = '0';
        document.getElementById('unitPrice').textContent = '0 ₫';
        document.getElementById('totalPrice').textContent = '0 ₫';
    }
}

function updateFoodQuantity(foodId, change) {
    const input = document.getElementById('food_' + foodId);
    let currentValue = parseInt(input.value) || 0;
    let newValue = currentValue + change;
    
    if (newValue < 0) newValue = 0;
    if (newValue > 10) newValue = 10;
    
    input.value = newValue;
    updateBookingSummary();
}

function validateSeatSelection() {
    if (selectedSeats.length === 0) {
        alert('Vui lòng chọn ít nhất 1 ghế!');
        return false;
    }
    
    if (selectedSeats.length > 8) {
        alert('Chỉ được đặt tối đa 8 ghế!');
        return false;
    }
    
    // Check for gaps (seats must be adjacent in same row)
    const seatsByRow = {};
    selectedSeats.forEach(seat => {
        const row = seat.charAt(0);
        const col = parseInt(seat.substring(1));
        if (!seatsByRow[row]) seatsByRow[row] = [];
        seatsByRow[row].push(col);
    });
    
    for (const row in seatsByRow) {
        const cols = seatsByRow[row].sort((a, b) => a - b);
        for (let i = 0; i < cols.length - 1; i++) {
            if (cols[i + 1] - cols[i] > 1) {
                alert(`Ghế trong hàng ${row} phải liền kề nhau. Không được bỏ trống ghế giữa!`);
                return false;
            }
        }
    }
    
    return true;
}

function renderSeatMap(layout, bookedSeats = [], prices = {}) {
    const seatMapContainer = document.getElementById('seatMap');
    let html = '';
    
    if (!layout || !Array.isArray(layout)) {
        generateDefaultSeatLayout();
        return;
    }
    
    layout.forEach(row => {
        html += `<div class="seat-row">`;
        html += `<div class="seat-row-label">${row.row}</div>`;
        
        row.seats.forEach(seat => {
            let seatClass = 'seat';
            
            if (seat.type === 'vip') seatClass += ' seat-vip';
            if (seat.type === 'couple') seatClass += ' seat-couple';
            if (seat.type === 'disabled' || !seat.available) seatClass += ' seat-disabled';
            if (bookedSeats.includes(seat.number)) seatClass += ' seat-booked';
            
            const onclick = (seat.type !== 'disabled' && seat.available && !bookedSeats.includes(seat.number))
                ? `onclick="toggleSeat('${seat.number}')"`
                : '';
            
            html += `<div class="${seatClass}" data-seat="${seat.number}" ${onclick}>
                        ${seat.label || ''}
                     </div>`;
        });
        
        html += `</div>`;
    });
    
    seatMapContainer.innerHTML = html;
}

function requestUserLocation() {
    const badge = document.getElementById('userLocationBadge');
    const text = document.getElementById('userLocationText');
    
    if (badge) badge.style.display = 'inline-block';
    if (text) text.textContent = 'Đang lấy vị trí...';
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(
            function(position) {
                userLat = position.coords.latitude;
                userLng = position.coords.longitude;
                
                if (text) {
                    text.textContent = `Đã xác định vị trí`;
                    text.style.color = '#28a745';
                }
                
                // Sort and update theaters by distance
                sortTheatersByDistance();
            },
            function(error) {
                console.error('Geolocation error:', error);
                if (text) {
                    text.textContent = 'Không thể lấy vị trí. Click để thử lại';
                    text.style.color = '#dc3545';
                }
            }
        );
    } else {
        if (text) {
            text.textContent = 'Trình duyệt không hỗ trợ định vị';
            text.style.color = '#dc3545';
        }
    }
}

function calculateDistance(lat1, lon1, lat2, lon2) {
    const R = 6371; // Earth radius in km
    const dLat = (lat2 - lat1) * Math.PI / 180;
    const dLon = (lon2 - lon1) * Math.PI / 180;
    const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
              Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) *
              Math.sin(dLon/2) * Math.sin(dLon/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));
    return R * c;
}

function sortTheatersByDistance() {
    if (!userLat || !userLng) return;
    
    const container = document.getElementById('theatersContainer');
    if (!container) return;
    
    const cards = Array.from(container.querySelectorAll('.theater-card'));
    
    // Calculate distance for each theater
    const theatersWithDistance = cards.map(card => {
        const lat = parseFloat(card.dataset.lat);
        const lng = parseFloat(card.dataset.lng);
        
        let distance = null;
        if (lat && lng && !isNaN(lat) && !isNaN(lng)) {
            distance = calculateDistance(userLat, userLng, lat, lng);
        }
        
        return { card, distance };
    });
    
    // Sort by distance (nulls last)
    theatersWithDistance.sort((a, b) => {
        if (a.distance === null) return 1;
        if (b.distance === null) return -1;
        return a.distance - b.distance;
    });
    
    // Reorder cards in DOM
    theatersWithDistance.forEach(({ card, distance }) => {
        container.appendChild(card);
        
        // Show distance
        if (distance !== null) {
            const distanceDiv = card.querySelector('.theater-distance');
            const distanceText = distanceDiv.querySelector('.distance-text');
            
            if (distanceDiv && distanceText) {
                distanceText.textContent = `Cách ${distance.toFixed(1)} km`;
                distanceDiv.style.display = 'block';
            }
        }
    });
}

// Update total price when seats are selected
function updateSeatSelection() {
    updateBookingSummary();
}
</script>
    
    // Calculate distance and add to each card
    cards.forEach(card => {
        const lat = parseFloat(card.dataset.lat);
        const lng = parseFloat(card.dataset.lng);
        
        if (lat && lng) {
            const dist = calculateDistance(userLat, userLng, lat, lng);
            card.dataset.distance = dist;
            
            // Show distance
            const distanceEl = card.querySelector('.theater-distance');
            const distanceText = card.querySelector('.distance-text');
            if (distanceEl && distanceText) {
                distanceEl.style.display = 'block';
                distanceText.textContent = `Cách bạn ${dist.toFixed(1)} km`;
            }
        } else {
            card.dataset.distance = 9999; // No coordinates = far away
        }
    });
    
    // Sort cards by distance
    cards.sort((a, b) => {
        const distA = parseFloat(a.dataset.distance) || 9999;
        const distB = parseFloat(b.dataset.distance) || 9999;
        return distA - distB;
    });
    
    // Re-append sorted cards
    cards.forEach(card => container.appendChild(card));
}
</script>

@endsection
