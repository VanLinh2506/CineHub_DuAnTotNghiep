@extends('layouts.app')

@php
    $title = 'Vé Của Tôi';
@endphp

@section('content')
<div class="container my-tickets-container">
    <h1 class="page-title">Vé Của Tôi</h1>
    
    @if ($bookings->isEmpty())
        <div class="empty-state">
            <i class="fas fa-ticket-alt empty-icon"></i>
            <h2>Bạn chưa có vé nào</h2>
            <p>Hãy mua vé để xem phim yêu thích của bạn</p>
            <a href="{{ route('movies.theater') }}" class="btn btn-primary">Đặt vé ngay</a>
        </div>
    @else
        <div class="tickets-grid">
            @foreach ($bookings as $booking)
                @php
                    $showtime = $booking->showtime;
                    $movie = $showtime->movie ?? null;
                    $theater = $showtime->theater ?? null;
                    $screen = $showtime->screen ?? null;
                    $tickets = $booking->tickets;
                    $seats = $tickets->pluck('seat')->toArray();
                    $totalPrice = $tickets->sum('price');
                    
                    $showDateTime = null;
                    $isExpired = false;
                    $qrShowing = false;
                    
                    if ($showtime && $showtime->show_date && $showtime->show_time) {
                        $dateStr = trim($showtime->show_date);
                        $timeStr = trim($showtime->show_time);
                        
                        try {
                            if (strlen($dateStr) >= 10) {
                                $dateStr = substr($dateStr, 0, 10);
                            }
                            
                            $datetimeStr = $dateStr . ' ' . $timeStr;
                            $showDateTime = \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $datetimeStr);
                            $isExpired = $showDateTime->isPast();
                            $qrShowing = true;
                        } catch (\Exception $e) {
                            $qrShowing = false;
                        }
                    }
                @endphp
                
                <div class="ticket-card">
                    <div class="ticket-header">
                        <h3 class="ticket-movie-title">{{ $movie->title ?? 'N/A' }}</h3>
                        <span class="ticket-status completed">Đã đặt</span>
                    </div>
                    
                    <div class="ticket-body">
                        <div class="ticket-info-row">
                            <span class="label">Mã booking:</span>
                            <span class="value code">{{ $booking->qr_code }}</span>
                        </div>
                        <div class="ticket-info-row">
                            <span class="label">Rạp:</span>
                            <span class="value">{{ $theater->name ?? 'N/A' }}</span>
                        </div>
                        <div class="ticket-info-row">
                            <span class="label">Màn hình:</span>
                            <span class="value">{{ $screen->screen_name ?? 'N/A' }}</span>
                        </div>
                        <div class="ticket-info-row">
                            <span class="label">Ngày chiếu:</span>
                            <span class="value">{{ $showtime->show_date ? date('d/m/Y', strtotime($showtime->show_date)) : 'N/A' }}</span>
                        </div>
                        <div class="ticket-info-row">
                            <span class="label">Giờ chiếu:</span>
                            <span class="value">{{ $showtime->show_time ?? 'N/A' }}</span>
                        </div>
                        <div class="ticket-info-row">
                            <span class="label">Ghế:</span>
                            <span class="value seat-info">{{ implode(', ', $seats) }}</span>
                        </div>
                        <div class="ticket-info-row">
                            <span class="label">Tổng tiền:</span>
                            <span class="value price">{{ number_format($totalPrice, 0, ',', '.') }} ₫</span>
                        </div>
                    </div>
                    
                    @if ($qrShowing && !$isExpired)
                        <div class="ticket-qr">
                            <img src="{{ qr_code_data_uri($booking->qr_code ?: ('BOOKING-' . $booking->id), 200) }}" alt="QR Booking">
                            <div class="qr-info">
                                <strong>Mã QR check vé</strong>
                                <span>Đưa mã này cho nhân viên để check tất cả {{ count($seats) }} ghế</span>
                            </div>
                        </div>
                    @elseif ($qrShowing && $isExpired)
                        <div class="ticket-qr expired">
                            <span class="expired-message">Vé đã hết hạn</span>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
        
        <div class="pagination-wrapper">
            {{ $bookings->links() }}
        </div>
    @endif
</div>

<style>
    .my-tickets-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .page-title {
        color: #fff;
        font-size: 2rem;
        margin-bottom: 2rem;
        text-align: center;
    }
    
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: #1a1a1a;
        border-radius: 12px;
    }
    
    .empty-icon {
        font-size: 4rem;
        color: #e50914;
        margin-bottom: 1rem;
        display: block;
    }
    
    .empty-state h2 {
        color: #fff;
        margin-bottom: 0.5rem;
    }
    
    .empty-state p {
        color: #999;
        margin-bottom: 1.5rem;
    }
    
    .tickets-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 2rem;
    }
    
    .ticket-card {
        background: #1a1a1a;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        transition: transform 0.3s, box-shadow 0.3s;
        display: flex;
        flex-direction: column;
    }
    
    .ticket-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.7);
    }
    
    .ticket-header {
        background: linear-gradient(135deg, #e50914 0%, #8b0009 100%);
        padding: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 1rem;
    }
    
    .ticket-movie-title {
        color: #fff;
        margin: 0;
        font-size: 1.1rem;
        flex: 1;
    }
    
    .ticket-status {
        padding: 0.25rem 0.75rem;
        background: rgba(255, 255, 255, 0.2);
        color: #fff;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: bold;
        white-space: nowrap;
    }
    
    .ticket-status.active {
        background: rgba(40, 167, 69, 0.3);
        color: #85ff9f;
    }
    
    .ticket-status.used {
        background: rgba(108, 117, 125, 0.3);
        color: #adb5bd;
    }
    
    .ticket-status.pending {
        background: rgba(255, 193, 7, 0.3);
        color: #ffd649;
    }
    
    .ticket-body {
        padding: 1.5rem;
        flex: 1;
    }
    
    .ticket-info-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .ticket-info-row:last-child {
        border-bottom: none;
    }
    
    .ticket-info-row .label {
        color: #999;
        font-size: 0.9rem;
    }
    
    .ticket-info-row .value {
        color: #fff;
        font-weight: 500;
    }
    
    .seat-info {
        background: rgba(229, 9, 20, 0.1);
        padding: 0.25rem 0.75rem;
        border-radius: 4px;
        border-left: 3px solid #e50914;
    }
    
    .price {
        color: #e50914;
        font-weight: bold;
    }
    
    .ticket-footer {
        padding: 1rem;
        background: rgba(255, 255, 255, 0.02);
        border-top: 1px solid rgba(255, 255, 255, 0.05);
        display: none;
        gap: 0.5rem;
        justify-content: flex-end;
    }

    .ticket-qr {
        padding: 1.5rem;
        background: rgba(255, 255, 255, 0.03);
        border-top: 1px solid rgba(255, 255, 255, 0.06);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 1rem;
        text-align: center;
    }

    .ticket-qr img {
        width: 200px;
        height: 200px;
        background: #fff;
        border-radius: 12px;
        padding: 10px;
    }

    .ticket-qr .qr-info {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .ticket-qr .qr-info strong {
        color: #fff;
        font-size: 1rem;
    }

    .ticket-qr .qr-info span {
        color: #ffc107;
        font-size: 0.9rem;
        line-height: 1.4;
    }

    .ticket-qr.expired {
        justify-content: center;
        background: rgba(108, 117, 125, 0.15);
    }

    .ticket-qr.expired .expired-message {
        color: #adb5bd;
        font-size: 0.95rem;
        font-weight: 500;
    }
    
    .code {
        font-family: 'Courier New', monospace;
        background: rgba(255, 255, 255, 0.08);
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.85rem;
    }
    
    .pagination-wrapper {
        margin-top: 2rem;
        display: flex;
        justify-content: center;
    }
    
    .btn {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 4px;
        font-size: 0.85rem;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        transition: all 0.3s;
    }
    
    .btn-sm {
        padding: 0.4rem 0.75rem;
        font-size: 0.8rem;
    }
    
    .btn-primary {
        background: #e50914;
        color: white;
    }
    
    .btn-primary:hover {
        background: #ff1f1f;
    }
    
    .btn-secondary {
        background: #2a2a2a;
        color: #999;
    }
    
    .btn-secondary:hover {
        background: #3a3a3a;
        color: #fff;
    }
    
    .btn-warning {
        background: #ffc107;
        color: #000;
    }
    
    .btn-warning:hover {
        background: #ffcd39;
    }
    
    @media (max-width: 768px) {
        .tickets-grid {
            grid-template-columns: 1fr;
        }
        
        .ticket-header {
            flex-direction: column;
        }
    }
</style>
@endsection
