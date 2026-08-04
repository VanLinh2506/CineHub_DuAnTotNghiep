<div class="section-header">
    <h2>Vé của tôi</h2>
    <a href="{{ route('movies.theater') }}" class="btn-primary profile-ticket-book">
        <i class="fas fa-plus"></i> Đặt vé mới
    </a>
</div>

@if ($bookings->isEmpty())
    <div class="profile-ticket-empty">
        <i class="fas fa-ticket-alt"></i>
        <h3>Bạn chưa có vé nào</h3>
        <p>Hãy chọn một suất chiếu để bắt đầu trải nghiệm tại CineHub.</p>
        <a href="{{ route('movies.theater') }}" class="btn-primary">Đặt vé ngay</a>
    </div>
@else
    <div class="profile-ticket-list">
        @foreach ($bookings as $booking)
            @php
                $pending = $booking->status === 'pending';
                $showtime = $booking->showtime;
                $ticketSeats = $pending ? ($booking->seats ?? []) : $booking->tickets->pluck('seat')->all();
                $showAt = null;
                if ($showtime?->show_date && $showtime?->show_time) {
                    try {
                        $showAt = \Carbon\Carbon::parse(
                            $showtime->show_date->format('Y-m-d') . ' ' . $showtime->show_time
                        );
                    } catch (\Throwable $e) {
                        $showAt = null;
                    }
                }
                $expired = $showAt?->isPast() ?? false;
            @endphp

            <article class="profile-ticket-card {{ $expired ? 'is-expired' : '' }}">
                <div class="profile-ticket-main">
                    <div class="profile-ticket-title">
                        <div>
                            <small>{{ $booking->qr_code ?: 'BOOKING #' . $booking->id }}</small>
                            <h3>{{ $showtime?->movie?->title ?? 'Phim không còn hiển thị' }}</h3>
                        </div>
                        <span class="profile-ticket-status {{ $pending ? 'pending' : ($expired ? 'expired' : 'active') }}">
                            {{ $pending ? 'Chờ thanh toán' : ($expired ? 'Đã hết hạn' : 'Đã đặt') }}
                        </span>
                    </div>

                    <div class="profile-ticket-details">
                        <span><i class="fas fa-building"></i> {{ $showtime?->theater?->name ?? 'N/A' }}</span>
                        <span><i class="fas fa-door-open"></i> {{ $showtime?->screen?->screen_name ?? 'N/A' }}</span>
                        <span><i class="fas fa-calendar"></i> {{ $showAt?->format('d/m/Y') ?? 'N/A' }}</span>
                        <span><i class="fas fa-clock"></i> {{ $showAt?->format('H:i') ?? 'N/A' }}</span>
                        <span><i class="fas fa-couch"></i> Ghế {{ implode(', ', $ticketSeats) ?: 'N/A' }}</span>
                        <span><i class="fas fa-money-bill-wave"></i> {{ number_format((float) $booking->total_amount, 0, ',', '.') }} ₫</span>
                    </div>

                    @if ($pending)
                        <a href="{{ route('booking.payment', $booking->id) }}" class="btn-primary profile-ticket-action">
                            <i class="fas fa-credit-card"></i> Tiếp tục thanh toán
                        </a>
                    @endif
                </div>

                @if (!$pending && !$expired)
                    <div class="profile-ticket-qr">
                        <img src="{{ qr_code_data_uri($booking->qr_code ?: ('BOOKING-' . $booking->id), 180) }}"
                             alt="Mã QR booking {{ $booking->id }}">
                        <small>Đưa mã này cho nhân viên soát vé</small>
                    </div>
                @endif
            </article>
        @endforeach
    </div>

    <div class="profile-ticket-pagination">{{ $bookings->links() }}</div>
@endif

@once
<style>
    .profile-ticket-book, .profile-ticket-action { width: auto; text-decoration: none; }
    .profile-ticket-list { display: grid; gap: 1rem; }
    .profile-ticket-card { display: flex; justify-content: space-between; gap: 1.5rem; padding: 1.25rem; border: 1px solid rgba(255,255,255,.1); border-radius: 16px; background: rgba(255,255,255,.04); }
    .profile-ticket-card.is-expired { opacity: .72; }
    .profile-ticket-main { flex: 1; min-width: 0; }
    .profile-ticket-title { display: flex; justify-content: space-between; gap: 1rem; }
    .profile-ticket-title small { color: #9ca3af; }
    .profile-ticket-title h3 { margin: .25rem 0 1rem; }
    .profile-ticket-status { align-self: flex-start; padding: .35rem .7rem; border-radius: 999px; font-size: .8rem; white-space: nowrap; }
    .profile-ticket-status.active { color: #86efac; background: rgba(34,197,94,.14); }
    .profile-ticket-status.pending { color: #fde68a; background: rgba(245,158,11,.14); }
    .profile-ticket-status.expired { color: #d1d5db; background: rgba(107,114,128,.2); }
    .profile-ticket-details { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: .75rem 1.25rem; color: #d1d5db; }
    .profile-ticket-details i { width: 20px; color: #f59e0b; }
    .profile-ticket-action { display: inline-flex; margin-top: 1rem; }
    .profile-ticket-qr { width: 155px; flex: 0 0 155px; text-align: center; }
    .profile-ticket-qr img { width: 145px; max-width: 100%; padding: 7px; border-radius: 10px; background: white; }
    .profile-ticket-qr small { display: block; margin-top: .4rem; color: #9ca3af; }
    .profile-ticket-empty { padding: 3rem 1rem; text-align: center; border: 1px dashed rgba(255,255,255,.15); border-radius: 16px; }
    .profile-ticket-empty > i { font-size: 3rem; color: #f59e0b; }
    .profile-ticket-empty .btn-primary { display: inline-flex; width: auto; text-decoration: none; }
    .profile-ticket-pagination { margin-top: 1.5rem; }
    @media (max-width: 767px) {
        .profile-ticket-card { display: block; }
        .profile-ticket-details { grid-template-columns: 1fr; }
        .profile-ticket-qr { width: 100%; margin-top: 1rem; }
    }
</style>
@endonce
