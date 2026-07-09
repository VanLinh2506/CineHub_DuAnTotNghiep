@extends('layouts.app')

@push('scripts')
<script>
    window.bookingPageConfig = {
        currentMovieId: @json(isset($movie) ? data_get($movie, 'id') : null),
        currentUserId: @json(Auth::id()),
        selectedTheaterId: @json($selectedTheater ?? null),
        selectedDate: @json(isset($selectedDate) && $selectedDate instanceof \Carbon\CarbonInterface ? $selectedDate->toDateString() : ($selectedDate ?? null)),
        selectedShowtimeId: @json($selectedShowtimeId ?? null),
        myReservedSeats: @json($myReservedSeats ?? []),
        basePrice: @json($basePrice ?? 90000),
        csrfToken: @json(csrf_token()),
        ticketPurchaseCountdownSeconds: 600,
        routes: {
            bookingLocation: "{{ route('booking.location') }}",
            bookingShowtimes: "{{ route('api.booking.showtimes') }}",
            bookingSeatMap: "{{ route('api.booking.seatMap') }}",
            bookingReserveSeats: "{{ route('booking.reservations.reserve') }}",
            bookingReleaseSeats: "{{ route('booking.reservations.release') }}",
            bookingExtendSeats: "{{ route('booking.reservations.extend') }}",
        },
        flashError: @json(session('error')),
        validationError: @json($errors->any() ? $errors->first() : null),
    };
</script>
@vite(['resources/js/booking.js'])
<script>
    (function() {
        function formatDate(date) {
            var year = date.getFullYear();
            var month = String(date.getMonth() + 1).padStart(2, '0');
            var day = String(date.getDate()).padStart(2, '0');
            return year + '-' + month + '-' + day;
        }

        function fallbackSelectTheater(theaterId) {
            document.querySelectorAll('.theater-card').forEach(function(card) {
                card.classList.toggle('selected', card.getAttribute('data-theater-id') === String(theaterId));
            });

            var theaterInput = document.getElementById('theaterIdInput');
            if (theaterInput) {
                theaterInput.value = theaterId;
            }

            window.selectedTheaterId = theaterId;
            window.selectedDate = null;
            window.selectedShowtimeId = null;

            var showtimeInput = document.getElementById('showtimeIdInput');
            if (showtimeInput) {
                showtimeInput.value = '';
            }

            var dateSection = document.getElementById('dateSelectionSection');
            var datesContainer = document.getElementById('datesContainer');
            var showtimeSection = document.getElementById('showtimeSelectionSection');
            var seatSection = document.getElementById('seatSelectionSection');

            if (showtimeSection) {
                showtimeSection.style.display = 'none';
            }
            if (seatSection) {
                seatSection.style.display = 'none';
            }
            if (dateSection) {
                dateSection.style.display = 'block';
            }

            if (!datesContainer) {
                return;
            }

            var dayNames = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
            var html = '';
            for (var i = 0; i < 7; i++) {
                var date = new Date();
                date.setDate(date.getDate() + i);
                var dateValue = formatDate(date);
                html += '<div class="date-tab" data-date="' + dateValue + '">';
                html += '<div class="day-name">' + dayNames[date.getDay()] + (i === 0 ? ' (Hom nay)' : '') + '</div>';
                html += '<div class="date-text">' + String(date.getDate()).padStart(2, '0') + '/' + String(date.getMonth() + 1).padStart(2, '0') + '</div>';
                html += '</div>';
            }
            datesContainer.innerHTML = html;
        }

        function fallbackSelectDate(dateValue) {
            if (typeof window.selectDate === 'function') {
                window.selectDate(dateValue);
                return;
            }

            document.querySelectorAll('.date-tab').forEach(function(tab) {
                tab.classList.toggle('selected', tab.getAttribute('data-date') === dateValue);
            });

            window.selectedDate = dateValue;
            var config = window.bookingPageConfig || {};
            var routes = config.routes || {};
            var showtimesSection = document.getElementById('showtimeSelectionSection');
            var showtimesContainer = document.getElementById('showtimesContainer');

            if (!showtimesSection || !showtimesContainer || !routes.bookingShowtimes || !config.currentMovieId || !window.selectedTheaterId) {
                return;
            }

            showtimesSection.style.display = 'block';
            showtimesContainer.innerHTML = '<p class="text-center text-muted">Dang tai...</p>';

            var url = routes.bookingShowtimes + '?movie_id=' + encodeURIComponent(config.currentMovieId) +
                '&theater_id=' + encodeURIComponent(window.selectedTheaterId) +
                '&date=' + encodeURIComponent(dateValue);

            fetch(url)
                .then(function(response) {
                    return response.json();
                })
                .then(function(data) {
                    if (!data.showtimes || !data.showtimes.length) {
                        showtimesContainer.innerHTML = '<p class="text-center text-warning">Khong co suat chieu nao cho ngay nay</p>';
                        return;
                    }

                    showtimesContainer.innerHTML = data.showtimes.map(function(showtime) {
                        return '<div class="showtime-btn" data-showtime-id="' + showtime.id + '">' +
                            '<div>' + showtime.show_time + '</div>' +
                            '<div class="screen-info">' + (showtime.screen_name || 'N/A') + ' - ' + (showtime.screen_type || '2D') + '</div>' +
                            '</div>';
                    }).join('');
                })
                .catch(function() {
                    showtimesContainer.innerHTML = '<p class="text-center text-danger">Loi khi tai lich chieu</p>';
                });
        }

        document.addEventListener('click', function(event) {
            if (event.bookingTheaterHandled) {
                return;
            }

            var theaterCard = event.target.closest('.theater-card');
            if (theaterCard) {
                event.preventDefault();
                var theaterId = theaterCard.getAttribute('data-theater-id');
                if (typeof window.selectTheaterDirect === 'function') {
                    window.selectTheaterDirect(theaterId);
                } else {
                    fallbackSelectTheater(theaterId);
                }
                return;
            }

            var dateTab = event.target.closest('.date-tab');
            if (dateTab) {
                event.preventDefault();
                fallbackSelectDate(dateTab.getAttribute('data-date'));
                return;
            }

            var showtimeBtn = event.target.closest('.showtime-btn');
            if (showtimeBtn && typeof window.selectShowtime !== 'function') {
                document.querySelectorAll('.showtime-btn').forEach(function(btn) {
                    btn.classList.remove('selected');
                });
                showtimeBtn.classList.add('selected');
                window.selectedShowtimeId = showtimeBtn.getAttribute('data-showtime-id');
                var showtimeInput = document.getElementById('showtimeIdInput');
                if (showtimeInput) {
                    showtimeInput.value = window.selectedShowtimeId;
                }
            }
        });
    })();
</script>
@endpush

@php
$title = 'Äáº·t VÃ© Xem Phim';
$meta_description = isset($movie) ? 'Äáº·t vÃ© xem phim ' . $movie->title . ' táº¡i CineHub. Chá»n ráº¡p, ngÃ y, giá» vÃ  gháº¿ ngá»“i phÃ¹ há»£p cho báº¡n.' : 'Äáº·t vÃ© xem phim táº¡i CineHub.';
$meta_keywords = 'Ä‘áº·t vÃ© xem phim, vÃ© xem phim online, mua vÃ© xem phim, CineHub';
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
                            <span class="detail-label">ÄÃ¡nh giÃ¡:</span>
                            <span class="detail-value">
                                <i class="fas fa-star"></i>
                                {{ number_format($movie->rating, 1) }}/10
                            </span>
                        </div>
                        @endif

                        @if ($movie->duration)
                        <div class="detail-item">
                            <span class="detail-label">Thá»i lÆ°á»£ng:</span>
                            <span class="detail-value">{{ floor($movie->duration / 60) }}h {{ $movie->duration % 60 }}m</span>
                        </div>
                        @endif

                        @php
                            $bookingCategories = $movie->categories ?? collect();
                            if ($bookingCategories->isEmpty() && $movie->category) {
                                $bookingCategories = collect([$movie->category]);
                            }
                        @endphp

                        @if ($bookingCategories->isNotEmpty())
                        <div class="detail-item">
                            <span class="detail-label">Thá»ƒ loáº¡i:</span>
                            <span class="detail-value">{{ $bookingCategories->pluck('name')->join(', ') }}</span>
                        </div>
                        @endif

                        @if ($movie->country)
                        <div class="detail-item">
                            <span class="detail-label">Quá»‘c gia:</span>
                            <span class="detail-value">{{ $movie->country }}</span>
                        </div>
                        @endif
                    </div>

                    <!-- Movie Description -->
                    @if ($movie->description)
                    <div class="booking-movie-description">
                        <h3>MÃ´ táº£</h3>
                        <p itemprop="description">{{ $movie->description }}</p>
                    </div>
                    @endif
                </article>
                @else
                <div class="alert alert-warning">
                    <i class="fas fa-film"></i>
                    Vui lÃ²ng chá»n má»™t bá»™ phim Ä‘á»ƒ Ä‘áº·t vÃ©
                </div>
                @endif
            </div>

            <!-- Right Column: Booking Form -->
            <div class="col-lg-7">
                <div class="booking-form-container">
                    <h2 class="booking-form-title">
                        @if(!isset($movie))
                        Äáº·t vÃ© xem phim
                        @else
                        Chá»n Lá»‹ch Chiáº¿u & Gháº¿
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
                            <i class="fas fa-film me-2"></i>Danh sÃ¡ch phim Ä‘ang chiáº¿u
                        </label>
                        @if(count($allMovies) == 0)
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Hiá»‡n táº¡i chÆ°a cÃ³ phim nÃ o Ä‘ang chiáº¿u ráº¡p. Vui lÃ²ng quay láº¡i sau!
                        </div>
                        @else
                        <div class="movies-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 15px; margin-top: 15px;">
                            @foreach($allMovies as $m)
                            <a href="{{ route('booking.index', ['movie' => $m->id]) }}"
                                class="movie-card-booking"
                                style="display: block; text-decoration: none; border: 2px solid #ddd; border-radius: 24px; overflow: hidden; transition: all 0.3s; background: white; cursor: pointer;"
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

                    <form id="bookingForm" method="POST" action="{{ route('booking.processBooking') }}" class="booking-form" novalidate onsubmit="return validateFormBeforeSubmit()">
                        @csrf

                        <!-- Hidden inputs for form submission -->
                        <input type="hidden" name="showtime_id" id="showtimeIdInput" value="{{ old('showtime_id', $selectedShowtimeId ?? '') }}">
                        <div id="seatsInputContainer" style="display: none;"></div>

                        <!-- Theater Selection as Cards -->
                        <div class="form-group">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label mb-0">
                                    <i class="fas fa-building me-2"></i>Chá»n ráº¡p cho phim nÃ y
                                </label>
                                <div id="userLocationBadge" style="display: none; font-size: 12px; padding: 6px 12px; background: rgba(40, 167, 69, 0.1); border-radius: 20px; color: #28a745;">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span id="userLocationText">Äang láº¥y vá»‹ trÃ­...</span>
                                    <button type="button" class="btn btn-sm btn-link p-0 ms-2" onclick="requestUserLocation()" title="Láº¥y láº¡i vá»‹ trÃ­" style="font-size: 12px;">
                                        <i class="fas fa-sync-alt"></i>
                                    </button>
                                </div>
                            </div>

                            <input type="hidden" name="theater_id" id="theaterIdInput">

                            <!-- Test button for debugging -->
                            <button type="button" onclick="alert('Button works! Theater cards: ' + document.querySelectorAll('.theater-card').length)" style="display: none; margin-bottom: 10px; padding: 8px 16px; background: #e50914; color: white; border: none; border-radius: 4px;">
                                ðŸ” Test Click (Debug)
                            </button>

                            @if (isset($theaters) && count($theaters) > 0)
                            <div id="theatersContainer" class="theaters-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px;">
                                @foreach ($theaters as $theater)
                                <div class="theater-card"
                                    data-theater-id="{{ $theater->id }}"
                                    data-lat="{{ $theater->latitude ?? '' }}"
                                    data-lng="{{ $theater->longitude ?? '' }}"
                                    data-location="{{ $theater->location ?? '' }}"
                                    style="border: 2px solid #ddd; border-radius: 12px; padding: 15px; cursor: pointer; transition: all 0.3s; background: white; position: relative; z-index: 1;">

                                    <div class="d-flex align-items-start" style="pointer-events: none;">
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

                                        <div class="theater-check" style="display: none; position: absolute; top: 10px; right: 10px; width: 24px; height: 24px; background: #28a745; border-radius: 50%; color: white;">
                                            <i class="fas fa-check" style="font-size: 12px;"></i>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @else
                            <div class="alert alert-warning">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Hiá»‡n táº¡i chÆ°a cÃ³ ráº¡p nÃ o chiáº¿u phim nÃ y.
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

                        <!-- Date Selection (hiá»ƒn thá»‹ sau khi chá»n ráº¡p) -->
                        <div id="dateSelectionSection" class="form-group" style="display: none;">
                            <label class="form-label">
                                <i class="fas fa-calendar-alt me-2"></i>Chá»n ngÃ y xem
                            </label>
                            <div id="datesContainer" class="dates-tabs" style="display: flex; gap: 10px; overflow-x: auto; padding-bottom: 10px;">
                                <!-- Dates will be loaded via JavaScript when theater is selected -->
                            </div>
                        </div>

                        <!-- Showtime Selection (appears after date selection) -->
                        <div id="showtimeSelectionSection" class="form-group" style="display: none;">
                            <label class="form-label">
                                <i class="fas fa-clock me-2"></i>Chá»n khung giá» chiáº¿u
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
                                <i class="fas fa-couch me-2"></i>Chá»n Gháº¿
                                <span id="screenNameDisplay" style="margin-left: 10px; color: #ffc107; font-weight: bold;"></span>
                            </label>

                            <!-- Screen indicator -->
                            <div class="screen-indicator" style="margin: 20px 0; text-align: center;">
                                <div style="width: 80%; height: 4px; background: linear-gradient(to bottom, #fff, #666); margin: 0 auto; border-radius: 50%; box-shadow: 0 3px 10px rgba(255,255,255,0.4);"></div>
                                <p style="color: #999; margin-top: 10px; font-size: 12px;">MÃ n hÃ¬nh</p>
                            </div>

                            <!-- Seat map container -->
                            <div id="seatMap" class="seat-map-container" style="padding: 20px; background: #2a2a2a; border-radius: 8px; max-width: 600px; margin: 0 auto;">
                                <p class="text-center text-muted">Vui lÃ²ng chá»n khung giá» chiáº¿u</p>
                            </div>

                            <!-- Seat legend -->
                            <div class="seat-legend" style="display: flex; justify-content: center; gap: 20px; margin-top: 15px; flex-wrap: wrap;">
                                <div class="legend-item" style="display: flex; align-items: center; gap: 5px;">
                                    <div class="seat seat-legend-box"></div>
                                    <span style="font-size: 12px; color: #ccc;">Trá»‘ng</span>
                                </div>
                                <div class="legend-item" style="display: flex; align-items: center; gap: 5px;">
                                    <div class="seat seat-legend-box seat-selected"></div>
                                    <span style="font-size: 12px; color: #ccc;">Äang chá»n</span>
                                </div>
                                <div class="legend-item" style="display: flex; align-items: center; gap: 5px;">
                                    <div class="seat seat-legend-box seat-booked"></div>
                                    <span style="font-size: 12px; color: #ccc;">ÄÃ£ Ä‘áº·t</span>
                                </div>
                                <div class="legend-item" style="display: flex; align-items: center; gap: 5px;">
                                    <div class="seat seat-legend-box seat-reserved"></div>
                                    <span style="font-size: 12px; color: #ccc;">Äang giá»¯ chá»—</span>
                                </div>
                                <div class="legend-item" style="display: flex; align-items: center; gap: 5px;">
                                    <div class="seat seat-legend-box seat-vip"></div>
                                    <span style="font-size: 12px; color: #ccc;">VIP</span>
                                </div>
                                <div class="legend-item" style="display: flex; align-items: center; gap: 5px;">
                                    <div class="seat seat-legend-box seat-couple"></div>
                                    <span style="font-size: 12px; color: #ccc;">ÄÃ´i</span>
                                </div>
                            </div>
                        </div>

                        <!-- Email for ticket -->
                        <div id="emailSection" class="form-group" style="display: none;">
                            <label class="form-label">
                                <i class="fas fa-envelope me-2"></i>Email nháº­n vÃ©
                            </label>
                            <input type="email"
                                name="customer_email"
                                id="customerEmail"
                                class="form-control"
                                placeholder="email@example.com"
                                value="{{ old('customer_email', Auth::check() ? Auth::user()->email : '') }}">
                            <small class="text-muted" style="font-size: 11px; display: block; margin-top: 5px;">
                                <i class="fas fa-info-circle"></i> VÃ© Ä‘iá»‡n tá»­ sáº½ Ä‘Æ°á»£c gá»­i Ä‘áº¿n email nÃ y
                            </small>
                        </div>

                        <!-- Selected Seats Display -->
                        <div id="selectedSeatsDisplay" class="selected-seats-display" style="display: none;">
                            <strong>Gháº¿ Ä‘Ã£ chá»n:</strong>
                            <span id="seatsText"></span>
                        </div>

                        <!-- Confirm Seats Button -->
                        <div class="confirm-seats-section" style="margin: 15px 0;">
                            <button type="button" id="confirmSeatsBtn" onclick="confirmSeats()" disabled class="btn-confirm-seats" style="width: 100%; padding: 12px; background: #ffc107; color: #000; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; transition: all 0.3s;">
                                <i class="fas fa-check-circle"></i> XÃ¡c nháº­n chá»n gháº¿
                            </button>
                            <button type="button" id="reselectSeatsBtn" onclick="reselectSeats()" style="display: none; width: 100%; padding: 12px; background: #6c757d; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; transition: all 0.3s;">
                                <i class="fas fa-redo"></i> Chá»n láº¡i gháº¿
                            </button>
                            {{-- <div id="reservationTimerBoxOld" class="reservation-timer-box" style="display: none;">
                                <span>Thá»i gian giá»¯ gháº¿ cÃ²n láº¡i:</span>
                                <strong id="reservationTimerTextOld">10:00</strong>
                            </div> --}}
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
                                <i class="fas fa-info-circle"></i> ThÃ´ng tin giÃ¡ vÃ©
                            </h6>
                            <div style="font-size: 13px; color: #ccc;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                    <span><i class="fas fa-couch" style="color: #999;"></i> Gháº¿ thÆ°á»ng:</span>
                                    <span id="normalPriceDisplay">150.000Ä‘</span>
                                </div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                    <span><i class="fas fa-crown" style="color: #764ba2;"></i> Gháº¿ VIP (+30%):</span>
                                    <span id="vipPriceDisplay">186.000Ä‘</span>
                                </div>
                                <div style="display: flex; justify-content: space-between;">
                                    <span><i class="fas fa-heart" style="color: #f5576c;"></i> Gháº¿ Ä‘Ã´i (+50%/gháº¿):</span>
                                    <span id="couplePriceDisplay">210.000Ä‘</span>
                                </div>
                            </div>
                        </div>

                        <div id="foodModalLauncher" class="form-group" style="display: none;">
                            <button type="button" class="food-modal-launcher-btn" onclick="openFoodModal()">
                                <span><i class="fas fa-shopping-basket"></i> Chon combo do an & nuoc</span>
                                <small id="foodLauncherSummary">Chua chon combo nao</small>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>

                        <!-- Food Items Section - iframe-style scrollable panel -->
                        <div id="foodSection" class="form-group" style="display: none;">
                            <label class="form-label" style="margin-bottom: 10px;">
                                <i class="fas fa-utensils me-2"></i>Combo Äá»“ Ä‚n & NÆ°á»›c (TÃ¹y chá»n)
                            </label>
                            <div class="food-iframe-shell">
                                <div class="food-iframe-header">
                                    <span><i class="fas fa-shopping-basket"></i> Chá»n combo</span>
                                    <small>Cuá»™n xuá»‘ng Ä‘á»ƒ xem thÃªm</small>
                                </div>
                                <div class="food-order-frame">
                                    <div id="foodItemsContainer" class="food-items-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 12px;">
                                        @php
                                        $hasFoodItems = isset($foodItems) && count($foodItems) > 0;
                                        @endphp

                                        @if($hasFoodItems)
                                        @foreach($foodItems as $food)
                                        <div class="food-item-card-compact" data-food-id="{{ $food->id }}" data-food-price="{{ $food->price }}" style="border: 2px solid #444; border-radius: 10px; padding: 10px; background: #2a2a2a; text-align: center; transition: all 0.3s; cursor: pointer;" onmouseover="this.style.borderColor='#ffc107'" onmouseout="this.style.borderColor='#444'">
                                            @if($food->image)
                                            <img src="{{ storage_url($food->image) }}" alt="{{ $food->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 8px; margin: 0 auto 8px;">
                                            @else
                                            <div style="width: 50px; height: 50px; background: #444; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px;">
                                                <i class="fas fa-utensils" style="color: #666; font-size: 20px;"></i>
                                            </div>
                                            @endif
                                            <h6 style="margin: 0 0 5px 0; color: #fff; font-size: 12px; font-weight: 600; min-height: 32px; display: flex; align-items: center; justify-content: center;">{{ $food->name }}</h6>
                                            <p style="margin: 0 0 8px 0; color: #ffc107; font-weight: bold; font-size: 13px;">{{ number_format($food->price) }}Ä‘</p>
                                            <div class="quantity-control" style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                                <button type="button" class="btn-quantity-compact" onclick="updateFoodQuantity({{ $food->id }}, -1)" style="width: 26px; height: 26px; border: 1px solid #666; background: #3a3a3a; color: #fff; border-radius: 4px; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">âˆ’</button>
                                                <input type="number" name="food_items[{{ $food->id }}]" id="food_{{ $food->id }}" value="0" min="0" max="10" readonly style="width: 40px; height: 26px; text-align: center; background: #1a1a1a; border: 1px solid #666; color: #fff; border-radius: 4px; font-size: 14px; font-weight: bold; padding: 0;">
                                                <button type="button" class="btn-quantity-compact" onclick="updateFoodQuantity({{ $food->id }}, 1)" style="width: 26px; height: 26px; border: 1px solid #666; background: #3a3a3a; color: #fff; border-radius: 4px; cursor: pointer; font-size: 14px; display: flex; align-items: center; justify-content: center; transition: all 0.2s;">+</button>
                                            </div>
                                        </div>
                                        @endforeach
                                        @else
                                        <p class="text-muted" style="text-align: center; grid-column: 1 / -1;">KhÃ´ng cÃ³ combo Ä‘á»“ Äƒn nÃ o</p>
                                        @endif
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

                            .food-modal-launcher-btn {
                                width: 100%;
                                display: grid;
                                grid-template-columns: 1fr auto auto;
                                gap: 10px;
                                align-items: center;
                                padding: 11px 13px;
                                border: 1px solid rgba(255, 193, 7, 0.35);
                                border-radius: 8px;
                                background: rgba(255, 193, 7, 0.08);
                                color: #fff;
                                text-align: left;
                                cursor: pointer;
                                transition: all 0.2s ease;
                            }

                            .food-modal-launcher-btn:hover {
                                border-color: #ffc107;
                                background: rgba(255, 193, 7, 0.14);
                                transform: translateY(-1px);
                            }

                            .food-modal-launcher-btn span {
                                display: flex;
                                align-items: center;
                                gap: 8px;
                                font-weight: 700;
                                font-size: 13px;
                            }

                            .food-modal-launcher-btn small {
                                color: #ffc107;
                                white-space: nowrap;
                                font-size: 12px;
                            }

                            #foodSection {
                                position: fixed !important;
                                inset: 0;
                                z-index: 10050;
                                margin: 0 !important;
                                padding: 20px;
                                background: rgba(0, 0, 0, 0.72);
                                backdrop-filter: blur(8px);
                                align-items: center;
                                justify-content: center;
                            }

                            #foodSection[style*="display: block"] {
                                display: flex !important;
                            }

                            #foodSection > .form-label {
                                position: absolute;
                                top: calc(50% - 280px);
                                left: calc(50% - 340px);
                                margin: 0 !important;
                                color: #fff;
                                font-size: 17px;
                                font-weight: 700;
                                z-index: 2;
                            }

                            #foodSection .food-iframe-shell {
                                width: min(680px, 100%);
                                max-height: min(560px, calc(100vh - 40px));
                                display: flex;
                                flex-direction: column;
                                border-radius: 12px;
                                background: rgba(22, 22, 22, 0.98);
                                box-shadow: 0 18px 54px rgba(0, 0, 0, 0.52);
                            }

                            #foodSection .food-iframe-header {
                                min-height: 48px;
                                padding: 9px 50px 9px 14px;
                                position: relative;
                            }

                            #foodSection .food-order-frame {
                                max-height: none;
                                flex: 1;
                                padding: 12px;
                            }

                            #foodSection .food-items-grid {
                                grid-template-columns: repeat(auto-fit, minmax(138px, 1fr)) !important;
                                gap: 10px !important;
                            }

                            #foodSection .food-item-card-compact {
                                padding: 10px !important;
                                border-radius: 8px !important;
                            }

                            #foodSection .food-item-card-compact img,
                            #foodSection .food-item-card-compact > div:first-child {
                                width: 42px !important;
                                height: 42px !important;
                                margin-bottom: 7px !important;
                            }

                            #foodSection .food-item-card-compact h6 {
                                min-height: 30px !important;
                                margin-bottom: 5px !important;
                                font-size: 12px !important;
                                line-height: 1.25;
                            }

                            #foodSection .food-item-card-compact p {
                                margin-bottom: 7px !important;
                                font-size: 12px !important;
                            }

                            #foodSection .quantity-control {
                                gap: 5px !important;
                            }

                            #foodSection .btn-quantity-compact {
                                width: 25px !important;
                                height: 25px !important;
                                font-size: 13px !important;
                            }

                            #foodSection input[name^="food_items["] {
                                width: 36px !important;
                                height: 25px !important;
                                font-size: 13px !important;
                            }

                            .food-modal-close-btn,
                            .food-modal-done-btn {
                                border: 1px solid rgba(255, 255, 255, 0.16);
                                background: #242424;
                                color: #fff;
                                cursor: pointer;
                            }

                            .food-modal-close-btn {
                                position: absolute;
                                right: 10px;
                                top: 50%;
                                transform: translateY(-50%);
                                width: 31px;
                                height: 31px;
                                border-radius: 50%;
                            }

                            .food-modal-actions {
                                display: flex;
                                justify-content: space-between;
                                align-items: center;
                                gap: 12px;
                                padding: 10px 14px;
                                border-top: 1px solid rgba(255, 255, 255, 0.08);
                                background: #171717;
                            }

                            .food-modal-actions span {
                                display: block;
                                color: #aaa;
                                font-size: 12px;
                            }

                            .food-modal-actions strong {
                                color: #ffc107;
                                font-size: 14px;
                            }

                            .food-modal-done-btn {
                                border: none;
                                border-radius: 8px;
                                padding: 8px 16px;
                                background: #e50914;
                                font-weight: 700;
                            }

                            body.food-modal-open {
                                overflow: hidden;
                            }

                            @media (max-width: 980px) {
                                #foodSection > .form-label {
                                    left: 24px;
                                    top: 24px;
                                }
                            }

                            @media (max-width: 640px) {
                                #foodSection {
                                    padding: 10px;
                                }

                                #foodSection > .form-label {
                                    position: static;
                                    width: 100%;
                                    margin-bottom: 10px !important;
                                }

                                #foodSection[style*="display: block"] {
                                    align-items: stretch;
                                    flex-direction: column;
                                }

                                #foodSection .food-items-grid {
                                    grid-template-columns: repeat(auto-fit, minmax(125px, 1fr)) !important;
                                }

                                #foodSection .food-iframe-shell {
                                    max-height: calc(100vh - 70px);
                                }
                            }
                        </style>

                        <!-- Payment Method Selection -->
                        <div id="paymentSection" class="form-group" style="display: none;">
                            <label class="form-label" style="margin-bottom: 10px;">
                                <i class="fas fa-credit-card me-2"></i>PhÆ°Æ¡ng thá»©c thanh toÃ¡n
                            </label>
                            @if(empty($vnpayConfigured))
                            <div class="alert alert-warning" style="font-size: 13px; margin-bottom: 10px;">
                                <i class="fas fa-exclamation-triangle"></i>
                                VNPay chÆ°a cáº¥u hÃ¬nh (.env). Báº¡n cÃ³ thá»ƒ thanh toÃ¡n báº±ng <strong>VÃ­ CineHub</strong>.
                            </div>
                            @endif
                            <div class="payment-methods" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                <label class="payment-method-card" style="border: 2px solid #444; border-radius: 10px; padding: 12px; cursor: pointer; transition: all 0.3s; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 8px; {{ empty($vnpayConfigured) ? 'opacity:0.55;' : '' }}">
                                    <input type="radio" name="payment_method" value="vnpay" {{ !empty($vnpayConfigured) ? 'checked' : 'disabled' }} style="position: absolute; opacity: 0;">
                                    <i class="fas fa-credit-card" style="color: #1e88e5; font-size: 24px;"></i>
                                    <div>
                                        <div style="color: #fff; font-weight: bold; font-size: 13px;">VNPay</div>
                                        <small style="color: #999; font-size: 11px;">Tháº»/QR</small>
                                    </div>
                                </label>
                                <label class="payment-method-card" style="border: 2px solid #444; border-radius: 10px; padding: 12px; cursor: pointer; transition: all 0.3s; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                    <input type="radio" name="payment_method" value="wallet" {{ empty($vnpayConfigured) ? 'checked' : '' }} style="position: absolute; opacity: 0;">
                                    <i class="fas fa-wallet" style="color: #28a745; font-size: 24px;"></i>
                                    <div>
                                        <div style="color: #fff; font-weight: bold; font-size: 13px;">VÃ­ CineHub</div>
                                        <small style="color: #999; font-size: 11px;" id="walletBalance">{{ Auth::check() ? number_format(Auth::user()->points ?? 0) : 0 }}Ä‘</small>
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
                                <span>GiÃ¡ vÃ© (1 vÃ©):</span>
                                <span id="unitPrice">0 â‚«</span>
                            </div>
                            <div class="price-row">
                                <span>Sá»‘ lÆ°á»£ng gháº¿:</span>
                                <span id="quantity">0</span>
                            </div>
                            <div class="price-row">
                                <span>Tiá»n vÃ©:</span>
                                <span id="seatsTotal">0 â‚«</span>
                            </div>
                            <div id="foodSummaryRows" style="border-top: 1px dashed rgba(255,255,255,0.2); padding-top: 8px; margin-top: 8px; display: none;">
                                <!-- Food items will be added here dynamically -->
                            </div>
                            <div class="price-row total" style="border-top: 2px solid rgba(229, 9, 20, 0.5); margin-top: 8px; padding-top: 8px; font-size: 18px;">
                                <span style="font-weight: bold;">Tá»•ng thanh toÃ¡n:</span>
                                <span id="totalPrice" style="font-weight: bold; color: #ffc107;">0 â‚«</span>
                            </div>
                        </div>

                        <!-- Terms -->
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="accept_terms" value="1">
                                <span>TÃ´i Ä‘á»“ng Ã½ vá»›i Ä‘iá»u khoáº£n vÃ  chÃ­nh sÃ¡ch</span>
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn-book" id="bookBtn" disabled>
                            <i class="fas fa-credit-card"></i>
                            Tiáº¿p tá»¥c thanh toÃ¡n
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

@endsection
