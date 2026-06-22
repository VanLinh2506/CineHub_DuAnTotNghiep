@extends('layouts.app')

@php
    $title = 'Đặt vé thành công';
    $showtime = $booking->showtime;
    $movie = $showtime->movie ?? null;
    $theater = $showtime->theater ?? null;
@endphp

@section('content')
<div class="container confirmation-container">
    <div class="confirmation-card">
        <div class="confirmation-header">
            <i class="fas fa-check-circle"></i>
            <h1>Đặt vé thành công!</h1>
            <p>Mã đặt vé: <strong>{{ $booking->qr_code }}</strong></p>
        </div>

        <div class="confirmation-info">
            <div class="info-row"><span>Phim</span><strong>{{ $movie->title ?? 'N/A' }}</strong></div>
            <div class="info-row"><span>Rạp</span><strong>{{ $theater->name ?? 'N/A' }}</strong></div>
            <div class="info-row"><span>Ngày chiếu</span><strong>{{ $showtime->show_date ? date('d/m/Y', strtotime($showtime->show_date)) : 'N/A' }}</strong></div>
            <div class="info-row"><span>Giờ chiếu</span><strong>{{ $showtime->show_time ?? 'N/A' }}</strong></div>
            <div class="info-row"><span>Ghế</span><strong>{{ implode(', ', $booking->seats ?? []) }}</strong></div>
            <div class="info-row"><span>Tổng tiền</span><strong class="price">{{ number_format($booking->total_amount, 0, ',', '.') }} ₫</strong></div>
        </div>

        <div class="qr-section">
            <h2><i class="fas fa-qrcode"></i> Mã QR check vé</h2>
            <p>Đưa mã QR cho nhân viên rạp để xác nhận vé của bạn.</p>

            <div class="qr-grid">
                @foreach ($booking->tickets as $ticket)
                    <div class="qr-item">
                        <img src="{{ qr_code_data_uri($ticket->qr_code ?: ('TICKET-' . $ticket->id), 180) }}" alt="QR ghế {{ $ticket->seat }}">
                        <span>Ghế {{ $ticket->seat }}</span>
                    </div>
                @endforeach
            </div>

            @if ($booking->tickets->isEmpty())
                <div class="qr-item">
                    <img src="{{ qr_code_data_uri($booking->qr_code ?: ('BOOKING-' . $booking->id), 200) }}" alt="QR booking">
                    <span>Mã đặt vé</span>
                </div>
            @endif
        </div>

        <div class="confirmation-actions">
            <a href="{{ route('booking.history') }}" class="btn btn-primary">
                <i class="fas fa-ticket-alt"></i> Xem vé của tôi
            </a>
            <a href="{{ route('movies.theater') }}" class="btn btn-secondary">
                <i class="fas fa-film"></i> Tiếp tục đặt vé
            </a>
        </div>
    </div>
</div>

<style>
    .confirmation-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }

    .confirmation-card {
        background: #1a1a1a;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5);
    }

    .confirmation-header {
        background: linear-gradient(135deg, #28a745 0%, #1e7e34 100%);
        padding: 2rem;
        text-align: center;
        color: #fff;
    }

    .confirmation-header i {
        font-size: 3rem;
        margin-bottom: 0.75rem;
    }

    .confirmation-header h1 {
        margin: 0 0 0.5rem;
        font-size: 1.75rem;
    }

    .confirmation-info {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .info-row {
        display: flex;
        justify-content: space-between;
        padding: 0.6rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        color: #ccc;
    }

    .info-row strong {
        color: #fff;
    }

    .info-row .price {
        color: #ffc107;
    }

    .qr-section {
        padding: 1.5rem 2rem;
    }

    .qr-section h2 {
        color: #fff;
        font-size: 1.2rem;
        margin-bottom: 0.5rem;
    }

    .qr-section p {
        color: #aaa;
        margin-bottom: 1.25rem;
    }

    .qr-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
        gap: 1rem;
    }

    .qr-item {
        background: rgba(255, 255, 255, 0.04);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
    }

    .qr-item img {
        width: 140px;
        height: 140px;
        background: #fff;
        border-radius: 8px;
        padding: 6px;
    }

    .qr-item span {
        display: block;
        margin-top: 0.75rem;
        color: #fff;
        font-weight: 600;
    }

    .confirmation-actions {
        padding: 1.5rem 2rem 2rem;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        justify-content: center;
    }

    .btn {
        padding: 0.75rem 1.25rem;
        border-radius: 8px;
        text-decoration: none;
        font-weight: 600;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: #e50914;
        color: #fff;
    }

    .btn-secondary {
        background: #2a2a2a;
        color: #fff;
    }
</style>
@endsection
