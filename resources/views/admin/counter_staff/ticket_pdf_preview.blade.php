@extends('admin.counter_staff.layout')

@section('content')
<div class="stat-card">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h4 class="mb-1"><i class="fas fa-ticket-alt text-danger"></i> Xem trước vé PDF</h4>
            <div class="text-muted">Mã đặt vé: {{ $booking->qr_code ?: ('#' . $booking->id) }}</div>
        </div>
        <a href="{{ route('counter.scan') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Quay lại quét QR
        </a>
    </div>

    <div class="row g-4">
        @forelse($booking->tickets as $ticket)
            <div class="col-12 col-xl-6">
                <article class="border rounded-3 p-4 h-100 bg-white shadow-sm">
                    <div class="d-flex justify-content-between align-items-start gap-3 mb-3">
                        <div>
                            <span class="badge bg-danger mb-2">Ghế {{ $ticket->seat }}</span>
                            <h5 class="mb-1">{{ $booking->showtime->movie->title ?? 'N/A' }}</h5>
                            <small class="text-muted">{{ $ticket->qr_code ?: ('TICKET-' . $ticket->id) }}</small>
                        </div>
                        <img
                            src="{{ qr_code_data_uri($ticket->qr_code ?: ('TICKET-' . $ticket->id), 120) }}"
                            width="96"
                            height="96"
                            alt="QR vé ghế {{ $ticket->seat }}"
                        >
                    </div>

                    <dl class="row small mb-4">
                        <dt class="col-4">Rạp</dt>
                        <dd class="col-8">{{ $booking->showtime->screen->theater->name ?? 'N/A' }}</dd>
                        <dt class="col-4">Phòng</dt>
                        <dd class="col-8">{{ $booking->showtime->screen->screen_name ?? 'N/A' }}</dd>
                        <dt class="col-4">Thời gian</dt>
                        <dd class="col-8">{{ $booking->showtime->show_time }} - {{ optional($booking->showtime->show_date)->format('d/m/Y') }}</dd>
                        <dt class="col-4">Giá vé</dt>
                        <dd class="col-8 fw-bold">{{ number_format((float) $ticket->price, 0, ',', '.') }} VNĐ</dd>
                    </dl>

                    <div class="d-flex flex-wrap gap-2">
                        <a href="{{ route('counter.ticketPdf.file', $ticket) }}" target="_blank" class="btn btn-outline-danger">
                            <i class="fas fa-eye"></i> Xem PDF
                        </a>
                        <a href="{{ route('counter.ticketPdf.file', ['ticket' => $ticket, 'download' => 1]) }}" class="btn btn-danger">
                            <i class="fas fa-download"></i> Tải PDF
                        </a>
                    </div>
                </article>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning mb-0">Booking này chưa có vé để xuất PDF.</div>
            </div>
        @endforelse
    </div>
</div>
@endsection
