@extends('admin.counter_staff.layout')

@section('content')
<!-- Date Filter -->
<div class="row mb-4">
    <div class="col-12">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0" style="color: #333; font-weight: 600;">
                    <i class="fas fa-calendar-alt text-primary me-2"></i>Lịch chiếu phim
                </h5>
                <form method="GET" class="d-flex gap-2">
                    <input type="hidden" name="route" value="counterStaff/showtimes">
                    <input type="date" name="date" value="{{ $date }}" class="form-control" style="width: 200px;" onchange="this.form.submit()">
                </form>
            </div>
        </div>
    </div>
</div>

@if(empty($showtimes))
    <div class="stat-card">
        <div class="text-center py-5">
            <i class="fas fa-calendar-times text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-3 mb-0">Không có suất chiếu nào vào ngày {{ date('d/m/Y', strtotime($date)) }}</p>
        </div>
    </div>
@else
    <div class="row">
        @foreach($showtimes as $showtime)
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="stat-card showtime-card animated-card" style="transition: all 0.3s ease;">
                    <div class="d-flex align-items-start mb-3">
                        @if($showtime['thumbnail'])
                            <img src="{{ $showtime['thumbnail'] }}" alt="{{ $showtime['movie_title'] }}"
                                 class="me-3" style="width: 80px; height: 120px; object-fit: cover; border-radius: 8px;">
                        @else
                            <div class="me-3 d-flex align-items-center justify-content-center bg-light"
                                 style="width: 80px; height: 120px; border-radius: 8px;">
                                <i class="fas fa-film text-muted" style="font-size: 2rem;"></i>
                            </div>
                        @endif
                        <div class="flex-grow-1">
                            <h6 class="mb-1 fw-bold" style="color: #333;">{{ $showtime['movie_title'] }}</h6>
                            <small class="text-muted d-block mb-2">
                                <i class="fas fa-clock me-1"></i>{{ $showtime['duration'] ?? 'N/A' }} phút
                            </small>
                            <span class="badge bg-primary mb-2">{{ $showtime['screen_type'] ?? '2D' }}</span>
                        </div>
                    </div>
                    <div class="border-top pt-3">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block">Phòng chiếu</small>
                                <strong style="color: #333;"><i class="fas fa-door-open text-primary me-1"></i>{{ $showtime['screen_name'] }}</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">Giờ chiếu</small>
                                <strong style="color: #333;"><i class="fas fa-clock text-success me-1"></i>{{ date('H:i', strtotime($showtime['show_time'])) }}</strong>
                            </div>
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <small class="text-muted d-block">Giá vé</small>
                                <strong class="text-success" style="font-size: 1.1rem;">{{ number_format($showtime['price']) }}₫</strong>
                            </div>
                            <div class="col-6">
                                @php
                                    $available = $showtime['available_seats'] ?? 0;
                                    $total = $showtime['total_seats'] ?? 0;
                                    $percentage = $total > 0 ? ($available / $total) * 100 : 0;
                                @endphp
                                <small class="text-muted d-block">Ghế còn lại</small>
                                <strong style="color: #333;">
                                    <span class="{{ $percentage < 20 ? 'text-danger' : ($percentage < 50 ? 'text-warning' : 'text-success') }}">
                                        {{ $available }}/{{ $total }}
                                    </span>
                                </strong>
                            </div>
                        </div>
                        <div class="mb-2">
                            <div class="progress" style="height: 8px; border-radius: 4px;">
                                <div class="progress-bar {{ $percentage < 20 ? 'bg-danger' : ($percentage < 50 ? 'bg-warning' : 'bg-success') }}"
                                     role="progressbar" style="width: {{ $percentage }}%"
                                     aria-valuenow="{{ $percentage }}" aria-valuemin="0" aria-valuemax="100"></div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <small class="text-muted"><i class="fas fa-users me-1"></i>Đã đặt: {{ $showtime['booked_seats'] }} vé</small>
                            <span class="badge {{ $percentage < 20 ? 'bg-danger' : ($percentage < 50 ? 'bg-warning' : 'bg-success') }}">
                                {{ number_format($percentage, 0) }}% còn trống
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection

@push('styles')
<style>
.showtime-card { transition: all 0.3s ease; cursor: pointer; }
.showtime-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important; }
.animated-card { animation: fadeInUp 0.5s ease-out forwards; opacity: 0; }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.animated-card').forEach((card, index) => {
        setTimeout(() => { card.style.opacity = '1'; }, index * 100);
    });
});
</script>
@endpush
