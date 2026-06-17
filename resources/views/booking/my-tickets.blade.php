@extends('layouts.app')

@php
    $title = 'Vé Của Tôi';
@endphp

@section('content')
<div class="container my-tickets-container">
    <h1 class="page-title">Vé Của Tôi</h1>
    
    @if (empty($tickets))
        <div class="empty-state">
            <i class="fas fa-ticket-alt empty-icon"></i>
            <h2>Bạn chưa có vé nào</h2>
            <p>Hãy mua vé để xem phim yêu thích của bạn</p>
            <a href="{{ route('movies.theater') }}" class="btn btn-primary">Đặt vé ngay</a>
        </div>
    @else
        <div class="tickets-grid">
            @foreach ($tickets as $ticket)
                <div class="ticket-card">
                    <div class="ticket-header">
                        <h3 class="ticket-movie-title">{{ $ticket->showtime->movie->title ?? 'N/A' }}</h3>
                        <span class="ticket-status {{ strtolower($ticket->status) }}">
                            {{ $ticket->status }}
                        </span>
                    </div>
                    
                    <div class="ticket-body">
                        <div class="ticket-info-row">
                            <span class="label">Rạp:</span>
                            <span class="value">{{ $ticket->showtime->screen->theater->name ?? 'N/A' }}</span>
                        </div>
                        <div class="ticket-info-row">
                            <span class="label">Màn hình:</span>
                            <span class="value">{{ $ticket->showtime->screen->name ?? 'N/A' }}</span>
                        </div>
                        <div class="ticket-info-row">
                            <span class="label">Ngày chiếu:</span>
                            <span class="value">{{ $ticket->showtime->show_date ? date('d/m/Y', strtotime($ticket->showtime->show_date)) : 'N/A' }}</span>
                        </div>
                        <div class="ticket-info-row">
                            <span class="label">Giờ chiếu:</span>
                            <span class="value">{{ $ticket->showtime->show_time ?? 'N/A' }}</span>
                        </div>
                        <div class="ticket-info-row">
                            <span class="label">Ghế:</span>
                            <span class="value seat-info">{{ $ticket->seat_number ?? 'N/A' }}</span>
                        </div>
                        <div class="ticket-info-row">
                            <span class="label">Giá vé:</span>
                            <span class="value price">{{ number_format($ticket->price, 0, ',', '.') }} ₫</span>
                        </div>
                    </div>
                    
                    <div class="ticket-footer">
                        @if ($ticket->status === 'active')
                            <a href="{{ url('/?route=ticket/qrcode&id=' . $ticket->id) }}" class="btn btn-sm btn-primary">
                                <i class="fas fa-qrcode"></i> Mã QR
                            </a>
                        @endif
                        @if ($ticket->status === 'pending')
                            <a href="{{ url('/?route=booking/verify&id=' . $ticket->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-check"></i> Xác nhận
                            </a>
                        @endif
                        <a href="{{ url('/?route=ticket/download&id=' . $ticket->id) }}" class="btn btn-sm btn-secondary">
                            <i class="fas fa-download"></i> Tải xuống
                        </a>
                    </div>
                </div>
            @endforeach
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
        display: flex;
        gap: 0.5rem;
        justify-content: flex-end;
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
