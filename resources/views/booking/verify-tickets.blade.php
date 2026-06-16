@extends('layouts.app')

@php
    $title = 'Xác Nhận Vé';
@endphp

@section('content')
<div class="container verify-tickets-container">
    <div class="verification-card">
        <div class="verification-header">
            <h1 class="verification-title">
                <i class="fas fa-check-circle"></i>
                Xác Nhận Vé
            </h1>
        </div>
        
        <div class="verification-body">
            @if (isset($ticket))
                <div class="ticket-verification">
                    <div class="verification-info">
                        <div class="info-item">
                            <span class="info-label">Phim:</span>
                            <span class="info-value">{{ $ticket->showtime->movie->title ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Rạp:</span>
                            <span class="info-value">{{ $ticket->showtime->screen->theater->name ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Ngày chiếu:</span>
                            <span class="info-value">{{ $ticket->showtime->show_date ? date('d/m/Y H:i', strtotime($ticket->showtime->show_date . ' ' . $ticket->showtime->show_time)) : 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <span class="info-label">Ghế:</span>
                            <span class="info-value">{{ $ticket->seat_number ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item highlight">
                            <span class="info-label">Tổng tiền:</span>
                            <span class="info-value">{{ number_format($ticket->price, 0, ',', '.') }} ₫</span>
                        </div>
                    </div>
                    
                    @if ($ticket->status === 'pending')
                        <div class="verification-qr">
                            <p>Mã QR sẽ được hiển thị sau khi thanh toán</p>
                        </div>
                        
                        <form method="POST" action="{{ url('/?route=booking/confirmPayment') }}" class="verification-form">
                            @csrf
                            <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">
                            
                            <div class="form-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="confirm" required>
                                    <span>Tôi xác nhận thanh toán cho vé này</span>
                                </label>
                            </div>
                            
                            <button type="submit" class="btn-confirm">
                                <i class="fas fa-check"></i>
                                Xác Nhận Thanh Toán
                            </button>
                        </form>
                    @else
                        <div class="verification-success">
                            <i class="fas fa-check-circle success-icon"></i>
                            <h2>Thanh toán thành công!</h2>
                            <p>Vé của bạn đã được xác nhận. Vui lòng kiểm tra email để nhận mã QR.</p>
                        </div>
                        
                        <div class="verification-actions">
                            <a href="{{ url('/?route=ticket/qrcode&id=' . $ticket->id) }}" class="btn-action primary">
                                <i class="fas fa-qrcode"></i>
                                Xem Mã QR
                            </a>
                            <a href="{{ route('booking.history') }}" class="btn-action secondary">
                                <i class="fas fa-list"></i>
                                Vé Của Tôi
                            </a>
                        </div>
                    @endif
                </div>
            @else
                <div class="error-state">
                    <i class="fas fa-exclamation-triangle"></i>
                    <h2>Không tìm thấy vé</h2>
                    <p>Vé bạn yêu cầu không tồn tại</p>
                    <a href="{{ route('booking.history') }}" class="btn-link">
                        Quay lại vé của tôi
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .verify-tickets-container {
        max-width: 600px;
        margin: 2rem auto;
        padding: 1rem;
    }
    
    .verification-card {
        background: #1a1a1a;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
    }
    
    .verification-header {
        background: linear-gradient(135deg, #e50914 0%, #8b0009 100%);
        padding: 2rem;
        text-align: center;
    }
    
    .verification-title {
        color: #fff;
        margin: 0;
        font-size: 1.8rem;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
    }
    
    .verification-body {
        padding: 2rem;
    }
    
    .ticket-verification {
        display: flex;
        flex-direction: column;
        gap: 2rem;
    }
    
    .verification-info {
        display: flex;
        flex-direction: column;
        gap: 1rem;
        background: rgba(255, 255, 255, 0.02);
        padding: 1.5rem;
        border-radius: 8px;
        border-left: 4px solid #e50914;
    }
    
    .info-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }
    
    .info-item:last-child {
        border-bottom: none;
    }
    
    .info-item.highlight {
        background: rgba(229, 9, 20, 0.1);
        padding: 0.75rem;
        border-radius: 4px;
        margin-top: 0.5rem;
    }
    
    .info-label {
        color: #999;
        font-size: 0.9rem;
    }
    
    .info-value {
        color: #fff;
        font-weight: 500;
        text-align: right;
    }
    
    .info-item.highlight .info-value {
        color: #e50914;
        font-size: 1.2rem;
    }
    
    .verification-qr {
        text-align: center;
        padding: 1rem;
        background: rgba(255, 255, 255, 0.02);
        border-radius: 8px;
    }
    
    .verification-qr p {
        color: #999;
        margin: 0;
    }
    
    .verification-success {
        text-align: center;
    }
    
    .success-icon {
        font-size: 3rem;
        color: #e50914;
        margin-bottom: 1rem;
    }
    
    .verification-success h2 {
        color: #fff;
        margin: 0 0 0.5rem 0;
    }
    
    .verification-success p {
        color: #999;
        margin: 0;
    }
    
    .verification-form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .form-group {
        margin-bottom: 1rem;
    }
    
    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        color: #ccc;
        cursor: pointer;
    }
    
    .checkbox-label input[type="checkbox"] {
        cursor: pointer;
    }
    
    .btn-confirm {
        padding: 0.75rem 1.5rem;
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
    
    .btn-confirm:hover {
        background: #ff1f1f;
    }
    
    .verification-actions {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-top: 1rem;
    }
    
    .btn-action {
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 6px;
        font-size: 0.95rem;
        cursor: pointer;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: all 0.3s;
    }
    
    .btn-action.primary {
        background: #e50914;
        color: white;
    }
    
    .btn-action.primary:hover {
        background: #ff1f1f;
    }
    
    .btn-action.secondary {
        background: #2a2a2a;
        color: #999;
    }
    
    .btn-action.secondary:hover {
        background: #3a3a3a;
        color: #fff;
    }
    
    .error-state {
        text-align: center;
        padding: 2rem;
    }
    
    .error-state i {
        font-size: 3rem;
        color: #e50914;
        display: block;
        margin-bottom: 1rem;
    }
    
    .error-state h2 {
        color: #fff;
        margin-bottom: 0.5rem;
    }
    
    .error-state p {
        color: #999;
        margin-bottom: 1.5rem;
    }
    
    .btn-link {
        color: #e50914;
        text-decoration: none;
        font-weight: 500;
    }
    
    .btn-link:hover {
        text-decoration: underline;
    }
</style>
@endsection
