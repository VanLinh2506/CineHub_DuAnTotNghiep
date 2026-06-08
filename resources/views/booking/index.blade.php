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
                    <article class="booking-movie-info" itemscope itemtype="https://schema.org/Movie">
                        <!-- Movie Poster -->
                        <div class="movie-poster-large mb-4">
                            @if ($movie->thumbnail)
                                <img id="img-moviee" 
                                     src="{{ $movie->thumbnail }}" 
                                     alt="{{ $movie->title }}" 
                                     class="img-fluid rounded"
                                     itemprop="image">
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
                    <h2 class="booking-form-title">Chọn Lịch Chiếu & Ghế</h2>
                    
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
                    
                    <form id="bookingForm" method="POST" action="{{ url('/?route=booking/store') }}" class="booking-form">
                        @csrf
                        
                        <!-- Theater Selection -->
                        <div class="form-group">
                            <label class="form-label">Rạp Chiếu</label>
                            <select name="theater_id" id="theaterSelect" required class="form-control" onchange="updateShowtimes()">
                                <option value="">-- Chọn rạp --</option>
                                @if (isset($theaters))
                                    @foreach ($theaters as $theater)
                                        <option value="{{ $theater->id }}">{{ $theater->name }}</option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        
                        <!-- Showtime Selection -->
                        <div class="form-group">
                            <label class="form-label">Lịch Chiếu</label>
                            <select name="showtime_id" id="showtimeSelect" required class="form-control">
                                <option value="">-- Chọn lịch chiếu --</option>
                            </select>
                        </div>
                        
                        <!-- Screen Selection -->
                        <div class="form-group">
                            <label class="form-label">Màn Hình</label>
                            <select name="screen_id" id="screenSelect" required class="form-control">
                                <option value="">-- Chọn màn hình --</option>
                            </select>
                        </div>
                        
                        <!-- Seat Selection -->
                        <div class="form-group">
                            <label class="form-label">Chọn Ghế</label>
                            <div id="seatMap" class="seat-map">
                                <p class="text-center text-muted">Vui lòng chọn lịch chiếu trước</p>
                            </div>
                            <input type="hidden" name="seats" id="selectedSeats" value="">
                        </div>
                        
                        <!-- Selected Seats Display -->
                        <div id="selectedSeatsDisplay" class="selected-seats-display" style="display: none;">
                            <strong>Ghế đã chọn:</strong>
                            <span id="seatsText"></span>
                        </div>
                        
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
    function updateShowtimes() {
        const theaterId = document.getElementById('theaterSelect').value;
        if (!theaterId) {
            document.getElementById('showtimeSelect').innerHTML = '<option value="">-- Chọn lịch chiếu --</option>';
            return;
        }
        
        // Fetch showtimes for selected theater
        fetch(`{{ url('/?route=booking/getShowtimes') }}&theater_id=${theaterId}`)
            .then(response => response.json())
            .then(data => {
                let html = '<option value="">-- Chọn lịch chiếu --</option>';
                if (data.showtimes) {
                    data.showtimes.forEach(showtime => {
                        html += `<option value="${showtime.id}">${showtime.show_date} - ${showtime.show_time}</option>`;
                    });
                }
                document.getElementById('showtimeSelect').innerHTML = html;
            })
            .catch(error => console.error('Error:', error));
    }
    
    // Update total price when seats are selected
    function updateSeatSelection() {
        const selectedSeats = document.querySelectorAll('.seat.selected');
        const quantity = selectedSeats.length;
        const unitPrice = 100000; // Example price
        const totalPrice = quantity * unitPrice;
        
        document.getElementById('quantity').textContent = quantity;
        document.getElementById('unitPrice').textContent = (unitPrice / 1000).toFixed(0) + 'k ₫';
        document.getElementById('totalPrice').textContent = (totalPrice / 1000).toFixed(0) + 'k ₫';
        
        if (quantity > 0) {
            document.getElementById('selectedSeatsDisplay').style.display = 'block';
            document.getElementById('seatsText').textContent = Array.from(selectedSeats)
                .map(seat => seat.dataset.seat)
                .join(', ');
            document.getElementById('bookBtn').disabled = false;
        } else {
            document.getElementById('selectedSeatsDisplay').style.display = 'none';
            document.getElementById('bookBtn').disabled = true;
        }
        
        const seats = Array.from(selectedSeats).map(seat => seat.dataset.seat).join(',');
        document.getElementById('selectedSeats').value = seats;
    }
    
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize seat map or other components as needed
    });
</script>
@endsection
