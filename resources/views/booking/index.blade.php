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
        screenSurcharge: @json($screenSurcharge ?? 0),
        csrfToken: @json(csrf_token()),
        ticketPurchaseCountdownSeconds: 600,
        routes: {
            bookingLocation: "{{ route('booking.location', [], false) }}",
            bookingMovieContext: "{{ route('api.booking.movieContext', [], false) }}",
            bookingShowtimes: "{{ route('api.booking.showtimes', [], false) }}",
            bookingSeatMap: "{{ route('api.booking.seatMap', [], false) }}",
            bookingFoodItems: "{{ route('api.booking.foodItems', [], false) }}",
            bookingReserveSeats: "{{ route('booking.reservations.reserve', [], false) }}",
            bookingReleaseSeats: "{{ route('booking.reservations.release', [], false) }}",
            bookingExtendSeats: "{{ route('booking.reservations.extend', [], false) }}",
        },
        flashError: @json(session('error')),
        validationError: @json($errors->any() ? $errors->first() : null),
    };

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-cinema-banner]').forEach(function (banner) {
            var slides = Array.from(banner.querySelectorAll('[data-banner-slide]'));
            var dots = Array.from(banner.querySelectorAll('[data-banner-dot]'));
            if (slides.length < 2) return;
            var current = 0;
            var timer;
            var show = function (index) {
                current = (index + slides.length) % slides.length;
                slides.forEach(function (slide, slideIndex) {
                    slide.classList.toggle('is-active', slideIndex === current);
                });
                dots.forEach(function (dot, dotIndex) {
                    dot.classList.toggle('is-active', dotIndex === current);
                });
            };
            var start = function () {
                window.clearInterval(timer);
                timer = window.setInterval(function () { show(current + 1); }, 3500);
            };
            dots.forEach(function (dot, index) {
                dot.addEventListener('click', function () { show(index); start(); });
            });
            start();
        });

        document.addEventListener('click', function (event) {
            var movieLink = event.target.closest('[data-booking-movie-id]');
            if (!movieLink) return;

            event.preventDefault();
            var movieId = movieLink.getAttribute('data-booking-movie-id');
            var contextRoute = window.bookingPageConfig?.routes?.bookingMovieContext;
            if (!movieId || !contextRoute || movieLink.dataset.loading === 'true') return;

            movieLink.dataset.loading = 'true';
            movieLink.setAttribute('aria-busy', 'true');
            fetch(contextRoute + '?movie_id=' + encodeURIComponent(movieId), {
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(function (response) {
                    if (!response.ok) throw new Error('Movie context request failed');
                    return response.json();
                })
                .then(function (data) {
                    window.setBookingMovieId?.(movieId);
                    window.bookingPageConfig.currentMovieId = String(movieId);
                    window.bookingDynamicMovieMode = true;
                    window.selectedTheaterId = null;
                    window.selectedDate = null;
                    window.selectedShowtimeId = null;

                    var workflow = document.getElementById('bookingWorkflow');
                    var picker = document.getElementById('bookingMoviePicker');
                    var title = document.querySelector('.booking-form-title');
                    if (picker) picker.style.display = 'none';
                    if (workflow) workflow.style.display = 'block';
                    if (title) title.textContent = 'Chọn lịch chiếu & ghế · ' + (data.movie?.title || 'Phim');

                    var theaterInput = document.getElementById('theaterIdInput');
                    var theaterGroup = theaterInput?.closest('.form-group');
                    var container = document.getElementById('theatersContainer');
                    if (!container && theaterGroup) {
                        container = document.createElement('div');
                        container.id = 'theatersContainer';
                        container.className = 'theaters-grid';
                        theaterGroup.appendChild(container);
                    }
                    if (container) {
                        container.innerHTML = '';
                        container.style.display = 'grid';
                        container.style.gridTemplateColumns = 'repeat(auto-fill,minmax(280px,1fr))';
                        container.style.gap = '15px';
                        (data.theaters || []).forEach(function (theater) {
                            var card = document.createElement('div');
                            card.className = 'theater-card';
                            card.dataset.theaterId = theater.id;
                            card.dataset.lat = theater.latitude || '';
                            card.dataset.lng = theater.longitude || '';
                            card.dataset.location = theater.location || '';
                            card.setAttribute('role', 'button');
                            card.tabIndex = 0;
                            card.innerHTML = '<div class="theater-card-inner"><span class="theater-icon"><i class="fas fa-film"></i></span><span class="theater-copy"><h5></h5><p class="theater-location"><i class="fas fa-map-marker-alt"></i> <span></span></p><small></small></span><span class="theater-check"><i class="fas fa-check"></i></span></div>';
                            card.querySelector('h5').textContent = theater.name || '';
                            card.querySelector('.theater-location span').textContent = theater.location || '';
                            card.querySelector('small').textContent = theater.address || '';
                            container.appendChild(card);
                        });
                    }

                    ['dateSelectionSection','showtimeSelectionSection','seatSelectionSection','emailSection'].forEach(function (id) {
                        var section = document.getElementById(id);
                        if (section) section.style.display = 'none';
                    });
                    var showtimeInput = document.getElementById('showtimeIdInput');
                    if (showtimeInput) showtimeInput.value = '';
                    if (theaterInput) theaterInput.value = '';
                    history.pushState({ movie: movieId }, '', movieLink.href || ('?movie=' + movieId));
                    workflow?.scrollIntoView({ behavior: 'smooth', block: 'start' });
                })
                .catch(function () {
                    window.showToast?.('Không thể tải lịch chiếu của phim lúc này.', 'error');
                })
                .finally(function () {
                    movieLink.dataset.loading = 'false';
                    movieLink.removeAttribute('aria-busy');
                });
        });
    });
</script>
@vite(['resources/js/booking.js'])
<script src="{{ asset('js/booking-price-sync.js') }}?v={{ filemtime(public_path('js/booking-price-sync.js')) }}"></script>
<script>
    (function() {
        var lastMobileSelectionAt = 0;

        function formatDate(date) {
            var year = date.getFullYear();
            var month = String(date.getMonth() + 1).padStart(2, '0');
            var day = String(date.getDate()).padStart(2, '0');
            return year + '-' + month + '-' + day;
        }

        function refreshFoodItemsForTheater(theaterId) {
            var container = document.getElementById('foodItemsContainer');
            var route = window.bookingPageConfig?.routes?.bookingFoodItems;
            if (!container || !route || !theaterId) {
                return;
            }

            container.innerHTML = '<div style="grid-column:1/-1;padding:22px;text-align:center;color:#aaa;">Đang tải combo...</div>';

            fetch(route + '?theater_id=' + encodeURIComponent(theaterId), {
                headers: { Accept: 'application/json' }
            })
                .then(function(response) {
                    if (!response.ok) throw new Error('Food items request failed');
                    return response.json();
                })
                .then(function(data) {
                    var items = Array.isArray(data.foodItems) ? data.foodItems : [];
                    container.innerHTML = '';

                    if (!items.length) {
                        var selectedCard = document.querySelector('.theater-card[data-theater-id="' + theaterId + '"]');
                        var theaterName = selectedCard?.querySelector('h5')?.textContent.trim() || 'đã chọn';
                        container.innerHTML = '<div style="grid-column:1/-1;padding:22px;text-align:center;color:#aaa;"><i class="fas fa-store-slash" style="font-size:28px;margin-bottom:10px;"></i><p style="margin:0;">Rạp <strong style="color:#fff;"></strong> hiện chưa có combo.</p><small>Combo được quản lý riêng theo từng rạp.</small></div>';
                        container.querySelector('strong').textContent = theaterName;
                        return;
                    }

                    items.forEach(function(item) {
                        var card = document.createElement('div');
                        card.className = 'food-item-card-compact';
                        card.dataset.foodId = item.id;
                        card.dataset.foodPrice = item.price;
                        card.innerHTML = '<div class="food-item-media"></div><h6></h6><p class="food-item-price"></p><div class="quantity-control"><button type="button" class="btn-quantity-compact btn-quantity-minus" aria-label="Giảm số lượng">−</button><input type="number" min="0" max="10" inputmode="numeric"><button type="button" class="btn-quantity-compact btn-quantity-plus" aria-label="Tăng số lượng">+</button></div>';

                        var media = card.querySelector('.food-item-media');
                        if (item.image_url) {
                            var image = document.createElement('img');
                            image.src = item.image_url;
                            image.alt = item.name;
                            media.appendChild(image);
                        } else {
                            media.innerHTML = '<i class="fas fa-utensils"></i>';
                        }

                        card.querySelector('h6').textContent = item.name;
                        card.querySelector('.food-item-price').textContent = Number(item.price).toLocaleString('vi-VN') + ' ₫';
                        var input = card.querySelector('input');
                        input.name = 'food_items[' + item.id + ']';
                        input.id = 'food_' + item.id;
                        input.value = '0';
                        card.querySelector('.btn-quantity-minus').addEventListener('click', function() {
                            window.updateFoodQuantity?.(item.id, -1);
                        });
                        card.querySelector('.btn-quantity-plus').addEventListener('click', function() {
                            window.updateFoodQuantity?.(item.id, 1);
                        });
                        container.appendChild(card);
                    });
                })
                .catch(function() {
                    container.innerHTML = '<div style="grid-column:1/-1;padding:22px;text-align:center;color:#ffb4b4;">Không thể tải combo. Vui lòng thử lại.</div>';
                });
        }

        window.refreshFoodItemsForTheater = refreshFoodItemsForTheater;

        function closeFoodComboModal() {
            var foodSection = document.getElementById('foodSection');
            if (foodSection) foodSection.style.display = 'none';
            document.body.classList.remove('food-modal-open');
        }

        function openFoodComboModal() {
            var foodSection = document.getElementById('foodSection');
            if (!foodSection) return;

            var theaterInput = document.getElementById('theaterIdInput');
            var theaterId = theaterInput?.value || window.selectedTheaterId || window.bookingPageConfig?.selectedTheaterId;
            if (theaterId) refreshFoodItemsForTheater(theaterId);

            var header = foodSection.querySelector('.food-iframe-header');
            if (header && !header.querySelector('.food-modal-close-btn')) {
                var closeButton = document.createElement('button');
                closeButton.type = 'button';
                closeButton.className = 'food-modal-close-btn';
                closeButton.setAttribute('aria-label', 'Đóng cửa sổ combo');
                closeButton.innerHTML = '<i class="fas fa-times"></i>';
                closeButton.addEventListener('click', closeFoodComboModal);
                header.appendChild(closeButton);
            }

            foodSection.style.display = 'flex';
            document.body.classList.add('food-modal-open');
        }

        window.openFoodModal = openFoodComboModal;
        window.closeFoodModal = closeFoodComboModal;

        document.addEventListener('click', function(event) {
            var launcher = event.target.closest('.food-modal-launcher-btn');
            if (!launcher) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();

            var theaterInput = document.getElementById('theaterIdInput');
            var theaterId = theaterInput?.value || window.selectedTheaterId || window.bookingPageConfig?.selectedTheaterId;
            if (theaterId) {
                refreshFoodItemsForTheater(theaterId);
            }
            openFoodComboModal();
        }, false);

        document.addEventListener('click', function(event) {
            var foodSection = document.getElementById('foodSection');
            if (foodSection && event.target === foodSection) closeFoodComboModal();
        });

        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && document.body.classList.contains('food-modal-open')) {
                closeFoodComboModal();
            }
        });

        function fallbackSelectTheater(theaterId) {
            document.querySelectorAll('.theater-card').forEach(function(card) {
                card.classList.toggle('selected', card.getAttribute('data-theater-id') === String(theaterId));
            });

            var theaterInput = document.getElementById('theaterIdInput');
            if (theaterInput) {
                theaterInput.value = theaterId;
            }

            window.selectedTheaterId = theaterId;
            refreshFoodItemsForTheater(theaterId);
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
            if (!window.bookingDynamicMovieMode && typeof window.selectDate === 'function') {
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
                        return '<div class="showtime-btn" data-showtime-id="' + showtime.id + '" data-price="' + showtime.price + '" data-screen-type="' + (showtime.screen_type || '2D') + '">' +
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
            if (Date.now() - lastMobileSelectionAt < 600) {
                return;
            }

            if (event.bookingTheaterHandled) {
                return;
            }

            var theaterCard = event.target.closest('.theater-card');
            if (theaterCard) {
                event.preventDefault();
                var theaterId = theaterCard.getAttribute('data-theater-id');
                if (!window.bookingDynamicMovieMode && typeof window.selectTheaterDirect === 'function') {
                    window.selectTheaterDirect(theaterId);
                    refreshFoodItemsForTheater(theaterId);
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

        document.addEventListener('pointerup', function(event) {
            if (event.pointerType !== 'touch' && event.pointerType !== 'pen') {
                return;
            }

            var theaterCard = event.target.closest('.theater-card');
            if (theaterCard) {
                event.preventDefault();
                lastMobileSelectionAt = Date.now();
                var theaterId = theaterCard.getAttribute('data-theater-id');
                if (!window.bookingDynamicMovieMode && typeof window.selectTheaterDirect === 'function') {
                    window.selectTheaterDirect(theaterId);
                    refreshFoodItemsForTheater(theaterId);
                } else {
                    fallbackSelectTheater(theaterId);
                }
                return;
            }

            var dateTab = event.target.closest('.date-tab');
            if (dateTab) {
                event.preventDefault();
                lastMobileSelectionAt = Date.now();
                fallbackSelectDate(dateTab.getAttribute('data-date'));
                return;
            }

            var showtimeBtn = event.target.closest('.showtime-btn');
            if (showtimeBtn && typeof window.selectShowtime === 'function') {
                event.preventDefault();
                lastMobileSelectionAt = Date.now();
                window.selectShowtime(showtimeBtn.getAttribute('data-showtime-id'));
            }
        }, { passive: false });

        document.addEventListener('touchend', function(event) {
            if (Date.now() - lastMobileSelectionAt < 350) {
                return;
            }

            var theaterCard = event.target.closest('.theater-card');
            if (theaterCard) {
                event.preventDefault();
                lastMobileSelectionAt = Date.now();
                var theaterId = theaterCard.getAttribute('data-theater-id');
                if (!window.bookingDynamicMovieMode && typeof window.selectTheaterDirect === 'function') {
                    window.selectTheaterDirect(theaterId);
                    refreshFoodItemsForTheater(theaterId);
                } else {
                    fallbackSelectTheater(theaterId);
                }
                return;
            }

            var dateTab = event.target.closest('.date-tab');
            if (dateTab) {
                event.preventDefault();
                lastMobileSelectionAt = Date.now();
                fallbackSelectDate(dateTab.getAttribute('data-date'));
                return;
            }

            var showtimeBtn = event.target.closest('.showtime-btn');
            if (showtimeBtn && typeof window.selectShowtime === 'function') {
                event.preventDefault();
                lastMobileSelectionAt = Date.now();
                window.selectShowtime(showtimeBtn.getAttribute('data-showtime-id'));
            }
        }, { passive: false });
    })();
</script>
@endpush

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
        @if(!empty($bannerMovies) && $bannerMovies->isNotEmpty())
        <section class="cinema-banner" data-cinema-banner aria-label="Phim đang chiếu nổi bật">
            @foreach($bannerMovies as $bannerMovie)
            <article class="cinema-banner-slide {{ $loop->first ? 'is-active' : '' }}" data-banner-slide>
                <div class="cinema-banner-backdrop" style="background-image:url('{{ $bannerMovie->banner ?: $bannerMovie->thumbnail }}')"></div>
                <div class="cinema-banner-shade"></div>
                <div class="cinema-banner-content">
                    <span class="cinema-banner-kicker"><i class="fas fa-clapperboard"></i> Đang chiếu tại CineHub</span>
                    <h1>{{ $bannerMovie->title }}</h1>
                    <div class="cinema-banner-meta">
                        @if($bannerMovie->rating)<span><i class="fas fa-star"></i> {{ number_format($bannerMovie->rating, 1) }}/10</span>@endif
                        @if($bannerMovie->duration)<span><i class="far fa-clock"></i> {{ $bannerMovie->duration }} phút</span>@endif
                        @if($bannerMovie->age_rating)<span>{{ $bannerMovie->age_rating }}</span>@endif
                    </div>
                    <p>{{ \Illuminate\Support\Str::limit(strip_tags($bannerMovie->description ?: 'Đặt vé và chọn suất chiếu phù hợp ngay hôm nay.'), 150) }}</p>
                    <a href="{{ route('booking.index', ['movie' => $bannerMovie->id]) }}" class="cinema-banner-action" data-booking-movie-id="{{ $bannerMovie->id }}">
                        <i class="fas fa-ticket-alt"></i> Chọn lịch chiếu
                    </a>
                </div>
            </article>
            @endforeach
            <div class="cinema-banner-dots" role="tablist" aria-label="Chọn phim trên banner">
                @foreach($bannerMovies as $bannerMovie)
                <button type="button" class="{{ $loop->first ? 'is-active' : '' }}" data-banner-dot="{{ $loop->index }}" aria-label="Hiện {{ $bannerMovie->title }}"></button>
                @endforeach
            </div>
        </section>
        @endif

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

                        @php
                            $bookingCategories = $movie->categories ?? collect();
                            if ($bookingCategories->isEmpty() && $movie->category) {
                                $bookingCategories = collect([$movie->category]);
                            }
                        @endphp

                        @if ($bookingCategories->isNotEmpty())
                        <div class="detail-item">
                            <span class="detail-label">Thể loại:</span>
                            <span class="detail-value">{{ $bookingCategories->pluck('name')->join(', ') }}</span>
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
                    <div class="booking-step mb-4" id="bookingMoviePicker">
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
                            <a href="{{ route('booking.index', ['movie' => $m->id]) }}"
                                class="movie-card-booking"
                                data-booking-movie-id="{{ $m->id }}"
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
                    @endif

                    <div id="bookingWorkflow" @if(!isset($movie)) style="display:none" @endif>

                    <form id="bookingForm" method="POST" action="{{ route('booking.processBooking') }}" class="booking-form" novalidate onsubmit="return validateFormBeforeSubmit()">
                        @csrf

                        <!-- Hidden inputs for form submission -->
                        <input type="hidden" name="showtime_id" id="showtimeIdInput" value="{{ old('showtime_id', $selectedShowtimeId ?? '') }}">
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
                            <button type="button" onclick="alert('Button works! Theater cards: ' + document.querySelectorAll('.theater-card').length)" style="display: none; margin-bottom: 10px; padding: 8px 16px; background: #e50914; color: white; border: none; border-radius: 4px;">
                                🔍 Test Click (Debug)
                            </button>

                            @if (isset($theaters) && count($theaters) > 0)
                            <div id="theatersContainer" class="theaters-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px;">
                                @foreach ($theaters as $theater)
                                <div class="theater-card"
                                    data-theater-id="{{ $theater->id }}"
                                    data-lat="{{ $theater->latitude ?? '' }}"
                                    data-lng="{{ $theater->longitude ?? '' }}"
                                    data-location="{{ $theater->location ?? '' }}"
                                    role="button"
                                    tabindex="0"
                                    onkeydown="if (event.key === 'Enter' || event.key === ' ') { event.preventDefault(); this.click(); }"
                                    style="border: 2px solid #ddd; border-radius: 12px; padding: 15px; cursor: pointer; transition: all 0.3s; background: white; position: relative; z-index: 1; touch-action: manipulation; -webkit-tap-highlight-color: transparent;">

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
                                autocomplete="email"
                                required
                                value="{{ old('customer_email', Auth::check() ? Auth::user()->email : '') }}">
                            <div class="email-ticket-help">
                                <i class="fas fa-circle-info" aria-hidden="true"></i>
                                <span>Vé điện tử sẽ được gửi đến email này.</span>
                            </div>
                        </div>

                        <!-- Selected Seats Display -->
                        <div id="selectedSeatsDisplay" class="selected-seats-display" style="display: none;">
                            <strong>Ghế đã chọn:</strong>
                            <span id="seatsText"></span>
                        </div>

                        <!-- Confirm Seats Button -->
                        <div class="confirm-seats-section" style="margin: 15px 0;">
                            <div id="reselectSeatsRemainingNotice"
                                class="reselect-seats-remaining-notice"
                                role="status"
                                aria-live="polite"
                                hidden></div>
                            <button type="button" id="confirmSeatsBtn" onclick="confirmSeats()" disabled class="btn-confirm-seats" style="width: 100%; padding: 12px; background: #ffc107; color: #000; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; transition: all 0.3s;">
                                <i class="fas fa-check-circle"></i> Xác nhận chọn ghế
                            </button>
                            <button type="button" id="reselectSeatsBtn" onclick="reselectSeats()" style="display: none; width: 100%; padding: 12px; background: #6c757d; color: #fff; border: none; border-radius: 6px; font-weight: bold; cursor: pointer; transition: all 0.3s;">
                                <i class="fas fa-redo"></i> Chọn lại ghế
                            </button>
                            {{-- <div id="reservationTimerBoxOld" class="reservation-timer-box" style="display: none;">
                                <span>Thời gian giữ ghế còn lại:</span>
                                <strong id="reservationTimerTextOld">10:00</strong>
                            </div> --}}
                        </div>

                        <style>
                            #emailSection .form-label {
                                margin-bottom: 9px;
                                color: #fff;
                                font-size: 15px;
                                font-weight: 700;
                            }

                            #emailSection #customerEmail {
                                height: 46px;
                                border: 1px solid #555;
                                background: #202124;
                                color: #fff;
                                font-size: 14px;
                            }

                            #emailSection #customerEmail::placeholder {
                                color: #8f949e;
                                opacity: 1;
                            }

                            #emailSection #customerEmail:focus {
                                border-color: #ffc107;
                                box-shadow: 0 0 0 3px rgba(255, 193, 7, 0.14);
                            }

                            .email-ticket-help {
                                display: flex;
                                align-items: flex-start;
                                gap: 8px;
                                margin-top: 9px;
                                padding: 9px 11px;
                                border: 1px solid rgba(56, 189, 248, 0.24);
                                border-radius: 8px;
                                background: rgba(56, 189, 248, 0.08);
                                color: #c9eefe;
                                font-size: 12px;
                                line-height: 1.45;
                            }

                            .email-ticket-help i {
                                flex: 0 0 auto;
                                margin-top: 2px;
                                color: #38bdf8;
                                font-size: 13px;
                            }

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

                            .reselect-seats-remaining-notice {
                                margin-bottom: 8px;
                                padding: 9px 12px;
                                border: 1px solid rgba(255, 193, 7, 0.45);
                                border-radius: 6px;
                                background: rgba(255, 193, 7, 0.1);
                                color: #ffd45c;
                                font-size: 13px;
                                line-height: 1.4;
                                text-align: center;
                            }

                            .reselect-seats-remaining-notice.is-exhausted {
                                border-color: rgba(229, 9, 20, 0.5);
                                background: rgba(229, 9, 20, 0.12);
                                color: #ff9ca3;
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

                        <div id="foodModalLauncher" class="form-group" style="display: none;">
                            <label class="form-label" style="margin-bottom: 10px;">
                                <i class="fas fa-utensils me-2"></i>Combo Đồ Ăn & Nước (Tùy chọn)
                            </label>
                            <button type="button" class="food-modal-launcher-btn" onclick="openFoodModal()">
                                <span><i class="fas fa-shopping-basket"></i> Chọn combo đồ ăn & nước</span>
                                <small id="foodLauncherSummary">Chưa chọn combo nào</small>
                                <i class="fas fa-chevron-right"></i>
                            </button>
                        </div>

                        <!-- Food Items Section - iframe-style scrollable panel -->
                        <div id="foodSection" class="form-group" style="display: none;">
                            <label class="form-label" style="margin-bottom: 10px;">
                                <i class="fas fa-utensils me-2"></i>Combo Đồ Ăn & Nước (Tùy chọn)
                            </label>
                            <div class="food-iframe-shell">
                                <div class="food-iframe-header">
                                    <span><i class="fas fa-shopping-basket"></i> Chọn đồ ăn & nước</span>
                                    <small>Chọn số lượng cho từng món</small>
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
                                            <p class="food-item-price">{{ number_format($food->price, 0, ',', '.') }} ₫</p>
                                            <div class="quantity-control" style="display: flex; align-items: center; justify-content: center; gap: 6px;">
                                                <button type="button" class="btn-quantity-compact btn-quantity-minus" aria-label="Giảm số lượng {{ $food->name }}" onclick="updateFoodQuantity({{ $food->id }}, -1)">−</button>
                                                <input type="number" name="food_items[{{ $food->id }}]" id="food_{{ $food->id }}" value="0" min="0" max="10" inputmode="numeric" aria-label="Số lượng {{ $food->name }}">
                                                <button type="button" class="btn-quantity-compact btn-quantity-plus" aria-label="Tăng số lượng {{ $food->name }}" onclick="updateFoodQuantity({{ $food->id }}, 1)">+</button>
                                            </div>
                                        </div>
                                        @endforeach
                                        @else
                                        <div style="text-align:center;grid-column:1/-1;padding:22px;color:#aaa;">
                                            <i class="fas fa-store-slash" style="font-size:28px;margin-bottom:10px;"></i>
                                            <p style="margin:0;">Rạp <strong style="color:#fff;">{{ $theaterInfo->name ?? optional($theaters->firstWhere('id', (int) $selectedTheater))->name ?? 'đã chọn' }}</strong> hiện chưa có combo.</p>
                                            <small>Combo được quản lý riêng theo từng rạp.</small>
                                        </div>
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
                                position: relative !important;
                                inset: auto;
                                z-index: 30;
                                width: 100%;
                                margin: 12px 0 0 !important;
                                padding: 0;
                                background: transparent;
                                backdrop-filter: none;
                            }

                            body.food-modal-open #foodSection {
                                display: block !important;
                            }

                            #foodSection > .form-label {
                                display: none;
                            }

                            #foodSection .food-iframe-shell {
                                width: 100%;
                                max-height: min(560px, calc(100vh - 40px));
                                display: flex;
                                flex-direction: column;
                                border-radius: 12px;
                                background: rgba(22, 22, 22, 0.98);
                                box-shadow: 0 18px 54px rgba(0, 0, 0, 0.52);
                            }

                            #foodSection .food-iframe-header {
                                min-height: 58px;
                                padding: 9px 50px 9px 14px;
                                position: relative;
                                background: linear-gradient(135deg, #b5121b, #73080e);
                                border-bottom-color: rgba(255, 255, 255, 0.18);
                            }

                            #foodSection .food-iframe-header span {
                                color: #fff;
                                font-size: 16px;
                                font-weight: 800;
                            }

                            #foodSection .food-iframe-header small {
                                color: #ffe9e9;
                                font-size: 12px;
                            }

                            #foodSection .food-order-frame {
                                max-height: none;
                                flex: 1;
                                padding: 12px;
                            }

                            #foodSection .food-items-grid {
                                grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)) !important;
                                gap: 14px !important;
                            }

                            #foodSection .food-item-card-compact {
                                padding: 14px !important;
                                border: 1px solid #4b4b4b !important;
                                border-radius: 12px !important;
                                background: #292929 !important;
                                box-shadow: 0 4px 14px rgba(0, 0, 0, 0.22);
                            }

                            #foodSection .food-item-card-compact.food-item-selected {
                                border-color: #ffc107 !important;
                                background: #352f1c !important;
                                box-shadow: 0 0 0 2px rgba(255, 193, 7, 0.18);
                            }

                            #foodSection .food-item-card-compact img,
                            #foodSection .food-item-card-compact > div:first-child {
                                width: 42px !important;
                                height: 42px !important;
                                margin-bottom: 7px !important;
                            }

                            #foodSection .food-item-card-compact h6 {
                                min-height: 42px !important;
                                margin-bottom: 7px !important;
                                color: #fff !important;
                                font-size: 14px !important;
                                line-height: 1.4;
                            }

                            #foodSection .food-item-card-compact .food-item-price,
                            #foodSection .food-item-card-compact p {
                                margin: 0 0 12px !important;
                                color: #ffd54f !important;
                                font-size: 15px !important;
                                font-weight: 800 !important;
                            }

                            #foodSection .quantity-control {
                                gap: 10px !important;
                            }

                            #foodSection .btn-quantity-compact {
                                width: 38px !important;
                                height: 38px !important;
                                display: inline-flex !important;
                                align-items: center;
                                justify-content: center;
                                padding: 0 !important;
                                border: 1px solid #686868 !important;
                                border-radius: 9px !important;
                                background: #414141 !important;
                                color: #fff !important;
                                font-size: 22px !important;
                                font-weight: 800 !important;
                                line-height: 1 !important;
                                cursor: pointer;
                                box-shadow: none !important;
                            }

                            #foodSection .btn-quantity-plus {
                                border-color: #e50914 !important;
                                background: #e50914 !important;
                            }

                            #foodSection .btn-quantity-compact:hover {
                                filter: brightness(1.2);
                                transform: translateY(-1px);
                            }

                            #foodSection .btn-quantity-compact:focus-visible {
                                outline: 3px solid rgba(255, 193, 7, 0.55);
                                outline-offset: 2px;
                            }

                            #foodSection input[name^="food_items["] {
                                width: 52px !important;
                                height: 38px !important;
                                padding: 0 !important;
                                border: 2px solid #777 !important;
                                border-radius: 9px !important;
                                background: #111 !important;
                                color: #fff !important;
                                font-size: 17px !important;
                                font-weight: 800 !important;
                                text-align: center !important;
                                opacity: 1 !important;
                                -webkit-text-fill-color: #fff;
                                appearance: textfield;
                            }

                            #foodSection input[name^="food_items["]::-webkit-inner-spin-button,
                            #foodSection input[name^="food_items["]::-webkit-outer-spin-button {
                                margin: 0;
                                appearance: none;
                                -webkit-appearance: none;
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
                                font-size: 16px;
                            }

                            .food-modal-close-btn:hover {
                                border-color: #fff;
                                background: #fff;
                                color: #111;
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
                                color: #fff;
                                min-width: 96px;
                            }

                            .food-modal-done-btn:hover {
                                background: #ff1723;
                            }

                            body.food-modal-open {
                                overflow: auto;
                            }

                            @media (max-width: 980px) {
                            }

                            @media (max-width: 640px) {
                                #foodSection {
                                    position: fixed !important;
                                    inset: 0;
                                    z-index: 10050;
                                    margin: 0 !important;
                                    padding: 10px;
                                    background: rgba(0,0,0,.72);
                                    backdrop-filter: blur(8px);
                                }

                                body.food-modal-open #foodSection {
                                    display: flex !important;
                                    align-items: stretch;
                                    flex-direction: column;
                                }

                                body.food-modal-open { overflow:hidden; }

                                #foodSection .food-items-grid {
                                    grid-template-columns: repeat(2, minmax(0, 1fr)) !important;
                                }

                                #foodSection .food-iframe-shell {
                                    max-height: calc(100vh - 20px);
                                }

                                #foodSection .food-item-card-compact {
                                    padding: 10px !important;
                                }

                                #foodSection .btn-quantity-compact {
                                    width: 34px !important;
                                    height: 34px !important;
                                }

                                #foodSection input[name^="food_items["] {
                                    width: 42px !important;
                                    height: 34px !important;
                                }
                            }
                        </style>

                        <!-- Payment Method Selection -->
                        <div id="paymentSection" class="form-group" data-payment-confirmed="false" style="display: none;">
                            <div class="payment-options-dialog">
                            <label class="form-label" style="margin-bottom: 10px;">
                                <i class="fas fa-credit-card me-2"></i>Phương thức thanh toán
                            </label>
                            @if(empty($vnpayConfigured))
                            <div class="alert alert-warning" style="font-size: 13px; margin-bottom: 10px;">
                                <i class="fas fa-exclamation-triangle"></i>
                                VNPay chưa cấu hình (.env). Bạn có thể thanh toán bằng <strong>Ví CineHub</strong>.
                            </div>
                            @endif
                            <div class="payment-methods" style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
                                <label class="payment-method-card" style="border: 2px solid #444; border-radius: 10px; padding: 12px; cursor: pointer; transition: all 0.3s; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 8px; {{ empty($vnpayConfigured) ? 'opacity:0.55;' : '' }}">
                                    <input type="radio" name="payment_method" value="vnpay" {{ empty($vnpayConfigured) ? 'disabled' : '' }} style="position: absolute; opacity: 0;">
                                    <i class="fas fa-credit-card" style="color: #1e88e5; font-size: 24px;"></i>
                                    <div>
                                        <div style="color: #fff; font-weight: bold; font-size: 13px;">VNPay</div>
                                        <small style="color: #999; font-size: 11px;">Thẻ/QR</small>
                                    </div>
                                </label>
                                <label class="payment-method-card" style="border: 2px solid #444; border-radius: 10px; padding: 12px; cursor: pointer; transition: all 0.3s; text-align: center; display: flex; flex-direction: column; align-items: center; gap: 8px;">
                                    <input type="radio" name="payment_method" value="wallet" style="position: absolute; opacity: 0;">
                                    <i class="fas fa-wallet" style="color: #28a745; font-size: 24px;"></i>
                                    <div>
                                        <div style="color: #fff; font-weight: bold; font-size: 13px;">Ví CineHub</div>
                                        <small style="color: #999; font-size: 11px;" id="walletBalance">{{ Auth::check() ? number_format(Auth::user()->points ?? 0) : 0 }}đ</small>
                                    </div>
                                </label>
                            </div>
                            </div>
                        </div>

                        <style>
                            .payment-method-card:has(input:checked) {
                                border-color: #ffc107 !important;
                                background: rgba(255, 193, 7, 0.1) !important;
                            }

                            #paymentSection.payment-options-open {
                                position: fixed;
                                inset: 0;
                                z-index: 10060;
                                align-items: center;
                                justify-content: center;
                                padding: 20px;
                                background: rgba(0, 0, 0, 0.78);
                                backdrop-filter: blur(9px);
                            }

                            #paymentSection .payment-options-dialog {
                                width: min(520px, 100%);
                                padding: 24px;
                                border: 1px solid rgba(255, 255, 255, 0.18);
                                border-radius: 18px;
                                background: linear-gradient(145deg, #29242a, #171317);
                                box-shadow: 0 24px 70px rgba(0, 0, 0, 0.55);
                            }

                            .btn-quantity:hover {
                                background: #4a4a4a !important;
                            }
                        </style>

                        <script>
                            document.addEventListener('change', function (event) {
                                if (!event.target || event.target.name !== 'payment_method') return;

                                var paymentSection = document.getElementById('paymentSection');
                                if (!paymentSection) return;

                                paymentSection.setAttribute('data-payment-confirmed', 'true');
                                window.setTimeout(function () {
                                    paymentSection.classList.remove('payment-options-open');
                                    paymentSection.style.display = 'none';
                                }, 180);
                            });
                        </script>

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
                    </div>
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

    /* Unified dark booking controls. Several cards previously mixed white
       inline backgrounds with dark-page text, producing poor contrast. */
    .booking-form-container .form-group {
        padding-top: 0.25rem;
    }

    .booking-form-container .form-label {
        color: #d8dbe2;
        font-size: 1rem;
        font-weight: 650;
        letter-spacing: 0.01em;
    }

    .booking-form-container .form-label > i {
        width: 1.2rem;
        color: #ef4444;
        text-align: center;
    }

    .booking-form-container .theaters-grid {
        grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)) !important;
    }

    .booking-form-container .theater-card {
        min-height: 132px;
        padding: 18px !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 14px !important;
        background: #222327 !important;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.035);
        pointer-events: auto !important;
        user-select: none;
        -webkit-user-select: none;
    }

    .booking-form-container .theater-card:active {
        transform: scale(0.985);
    }

    .booking-form-container .theater-card:hover {
        border-color: rgba(239, 68, 68, 0.75) !important;
        background: #27282d !important;
        box-shadow: 0 10px 24px rgba(0, 0, 0, 0.24);
    }

    .booking-form-container .theater-card.selected {
        border-color: #22c55e !important;
        background: linear-gradient(145deg, rgba(34, 197, 94, 0.13), rgba(34, 197, 94, 0.045)) !important;
        box-shadow: 0 0 0 1px rgba(34, 197, 94, 0.12), 0 10px 26px rgba(0, 0, 0, 0.2);
    }

    .booking-form-container .theater-card h5 {
        padding-right: 28px;
        color: #f8fafc !important;
        font-size: 1rem !important;
    }

    .booking-form-container .theater-card p {
        color: #a9adb7 !important;
        line-height: 1.45;
    }

    .booking-form-container .theater-card .theater-distance {
        color: #4ade80 !important;
        font-weight: 600;
    }

    .booking-form-container .theater-check {
        top: 12px !important;
        right: 12px !important;
        width: 26px !important;
        height: 26px !important;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
    }

    .booking-form-container .dates-tabs {
        gap: 8px !important;
        padding: 2px 2px 12px !important;
        scrollbar-width: thin;
        scrollbar-color: #50525a transparent;
    }

    .booking-form-container .dates-tabs .date-tab {
        min-width: 104px;
        padding: 13px 12px;
        border: 1px solid rgba(255, 255, 255, 0.18);
        border-radius: 12px;
        background: #242529;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.04);
    }

    .booking-form-container .dates-tabs .date-tab:hover {
        border-color: rgba(239, 68, 68, 0.75);
        background: #2b2c31;
        transform: translateY(-2px);
    }

    .booking-form-container .dates-tabs .date-tab.selected {
        border-color: #ef4444;
        background: linear-gradient(145deg, rgba(239, 68, 68, 0.24), rgba(239, 68, 68, 0.09));
        box-shadow: 0 0 0 1px rgba(239, 68, 68, 0.12);
    }

    .booking-form-container .dates-tabs .date-tab .day-name {
        color: #9ca3af;
        letter-spacing: 0.035em;
    }

    .booking-form-container .dates-tabs .date-tab .date-text {
        color: #f8fafc;
        font-size: 1.05rem;
    }

    .booking-form-container .btn-confirm-seats {
        min-height: 52px;
        border-radius: 10px !important;
        background: #f5b800 !important;
        box-shadow: 0 8px 20px rgba(245, 184, 0, 0.14);
    }

    .booking-form-container .btn-confirm-seats:disabled {
        color: #7f8490 !important;
        background: #292a2f !important;
        border: 1px solid rgba(255, 255, 255, 0.08) !important;
        box-shadow: none;
        opacity: 1;
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
        background: linear-gradient(145deg, #222327, #1e1f23);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 1.15rem 1.25rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
    }

    .price-row {
        display: flex;
        justify-content: space-between;
        gap: 1rem;
        color: #c9cbd2;
        margin-bottom: 0.65rem;
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

    .booking-page-section { padding-top:6.25rem; background:radial-gradient(circle at 12% 0%,rgba(255,255,255,.08),transparent 28%),linear-gradient(180deg,#111214,#08090b); }
    .booking-page-section > .container-fluid { max-width:1880px; margin:auto; }
    .cinema-banner { position:relative; height:clamp(310px,35vw,560px); margin:0 0 28px; overflow:hidden; border:1px solid rgba(255,255,255,.13); border-radius:32px; background:#17181b; box-shadow:inset 0 1px 0 rgba(255,255,255,.08),0 28px 70px rgba(0,0,0,.4); }
    .cinema-banner-slide { position:absolute; inset:0; opacity:0; visibility:hidden; transform:scale(1.035); transition:opacity .85s ease,transform 5s ease,visibility .85s; }
    .cinema-banner-slide.is-active { opacity:1; visibility:visible; transform:scale(1); }
    .cinema-banner-backdrop { position:absolute; inset:0; background-size:cover; background-position:center 28%; }
    .cinema-banner-shade { position:absolute; inset:0; background:linear-gradient(90deg,rgba(5,6,8,.96) 0%,rgba(8,9,11,.76) 38%,rgba(8,9,11,.14) 72%),linear-gradient(0deg,rgba(5,6,8,.72),transparent 58%); }
    .cinema-banner-content { position:absolute; z-index:2; left:clamp(28px,5vw,86px); top:50%; width:min(600px,62%); color:#fff; transform:translateY(-50%); }
    .cinema-banner-kicker { display:inline-flex; align-items:center; gap:8px; padding:9px 14px; border:1px solid rgba(255,255,255,.22); border-radius:999px; background:rgba(255,255,255,.1); backdrop-filter:blur(14px); font-size:12px; font-weight:800; letter-spacing:.08em; text-transform:uppercase; }
    .cinema-banner-content h1 { margin:18px 0 10px; font-size:clamp(30px,4vw,64px); line-height:1.04; text-shadow:0 8px 26px rgba(0,0,0,.45); }
    .cinema-banner-meta { display:flex; flex-wrap:wrap; gap:9px; }
    .cinema-banner-meta span { padding:7px 11px; border:1px solid rgba(255,255,255,.18); border-radius:999px; background:rgba(15,16,19,.38); backdrop-filter:blur(10px); font-size:12px; }
    .cinema-banner-meta .fa-star { color:#ffd45b; }
    .cinema-banner-content p { margin:15px 0 20px; color:rgba(255,255,255,.76); line-height:1.55; }
    .cinema-banner-action { display:inline-flex; align-items:center; gap:9px; padding:13px 20px; border:1px solid rgba(255,255,255,.42); border-radius:999px; color:#fff; background:linear-gradient(145deg,rgba(255,255,255,.24),rgba(255,255,255,.08)); box-shadow:inset 0 1px 0 rgba(255,255,255,.28),0 12px 28px rgba(0,0,0,.28); backdrop-filter:blur(16px); text-decoration:none; font-weight:800; }
    .cinema-banner-action:hover { color:#fff; background:rgba(255,255,255,.24); transform:translateY(-2px); }
    .cinema-banner-dots { position:absolute; z-index:4; right:28px; bottom:24px; display:flex; gap:7px; }
    .cinema-banner-dots button { width:10px; height:10px; padding:0; border:1px solid rgba(255,255,255,.6); border-radius:999px; background:rgba(255,255,255,.22); transition:width .25s,background .25s; }
    .cinema-banner-dots button.is-active { width:34px; background:#fff; }

    .booking-form-container,.booking-movie-info { border:1px solid rgba(255,255,255,.12); border-radius:30px; background:linear-gradient(145deg,rgba(35,36,40,.94),rgba(17,18,21,.96)); box-shadow:inset 0 1px 0 rgba(255,255,255,.07),0 24px 55px rgba(0,0,0,.3); backdrop-filter:blur(20px); }
    .booking-form-container { padding:32px !important; }
    /* A backdrop-filter ancestor turns fixed descendants into locally positioned
       elements. Keep the form unfiltered so the combo modal is fixed to viewport. */
    .booking-form-container { backdrop-filter:none; -webkit-backdrop-filter:none; }
    .booking-movie-info { padding:24px; }
    .movie-card-booking { border:1px solid rgba(255,255,255,.13) !important; border-radius:26px !important; color:#fff !important; background:linear-gradient(145deg,#25262a,#17181b) !important; box-shadow:0 14px 30px rgba(0,0,0,.2); }
    .movie-card-booking:hover { border-color:rgba(255,255,255,.5) !important; box-shadow:0 20px 38px rgba(0,0,0,.34) !important; }
    .movie-card-booking h4 { color:#fff !important; }
    .movie-card-booking span { color:#bbb !important; }
    .movie-card-booking img { height:240px !important; }
    .booking-page-section .alert { border-radius:999px; }
    .theater-card { border:1px solid rgba(255,255,255,.14) !important; border-radius:999px !important; color:#fff; background:linear-gradient(145deg,rgba(255,255,255,.12),rgba(255,255,255,.05)) !important; }
    .theater-card h5 { color:#fff !important; }
    .theater-card p { color:#aaa !important; }
    .theater-card-inner { display:flex; align-items:center; gap:13px; padding:15px 20px; pointer-events:none; }
    .theater-card-inner .theater-icon { flex:0 0 44px; width:44px; height:44px; display:grid; place-items:center; border:1px solid rgba(255,255,255,.22); border-radius:50%; background:rgba(255,255,255,.1); }
    .theater-card-inner .theater-copy { min-width:0; flex:1; }
    .theater-card-inner h5 { margin:0 0 4px; font-size:15px; }
    .theater-card-inner p,.theater-card-inner small { display:block; margin:0; color:#aaa; font-size:12px; }
    .theater-card-inner .theater-check { width:26px; height:26px; display:none; place-items:center; border-radius:50%; color:#111; background:#fff; }
    .theater-card.selected .theater-card-inner .theater-check { display:grid; }
    .dates-tabs .date-tab,.showtimes-grid .showtime-btn,.booking-page-section .form-control,.btn-confirm-seats { border-radius:999px !important; }
    .movie-poster-large,.movie-poster-large img { border-radius:24px !important; }

    @media (max-width: 768px) {
        .booking-page-section { padding-top:5.25rem; }
        .cinema-banner { height:430px; border-radius:24px; }
        .cinema-banner-content { left:22px; width:calc(100% - 44px); }
        .cinema-banner-content p { display:none; }
        .cinema-banner-shade { background:linear-gradient(0deg,rgba(5,6,8,.95),rgba(5,6,8,.18) 78%); }
        .cinema-banner-dots { right:18px; bottom:16px; }
        .booking-movie-info {
            position: static !important;
            z-index: auto;
        }

        .booking-page-section .col-lg-7 {
            position: relative;
            z-index: 20;
        }

        .booking-form-container {
            padding: 1rem;
            position: static;
            max-height: none;
            overflow: visible;
            pointer-events: auto !important;
        }

        .booking-movie-details {
            grid-template-columns: 1fr;
        }
    }
</style>

@endsection
