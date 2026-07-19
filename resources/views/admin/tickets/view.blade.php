@extends('admin.layout')

@section('content')
@php
    $booking = $ticket->bookingPending;
    $customer = $ticket->user ?? $booking?->user;
    $showtime = $ticket->showtime;
    $ticketCode = $booking?->booking_code ?: 'VE-' . str_pad((string) $ticket->id, 6, '0', STR_PAD_LEFT);
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Chi tiết vé</h2>
        <div class="text-muted">{{ $ticketCode }}</div>
    </div>
    <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-secondary">
        <i class="fas fa-arrow-left me-1"></i> Quay lại danh sách
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="stat-card mb-4">
            <h6 class="mb-3"><i class="fas fa-ticket-alt me-2"></i>Thông tin vé</h6>
            <div class="row g-3">
                <div class="col-md-6"><span class="text-muted d-block">Mã vé</span><strong>{{ $ticketCode }}</strong></div>
                <div class="col-md-3"><span class="text-muted d-block">Ghế</span><span class="badge bg-info fs-6">{{ $ticket->seat }}</span></div>
                <div class="col-md-3"><span class="text-muted d-block">Loại ghế</span><strong>{{ ucfirst($ticket->seat_type ?? 'normal') }}</strong></div>
                <div class="col-md-6"><span class="text-muted d-block">Giá vé</span><strong class="text-success">{{ number_format((float) $ticket->price) }}₫</strong></div>
                <div class="col-md-3">
                    <span class="text-muted d-block">Trạng thái</span>
                    <span class="badge bg-{{ $ticket->status === 'Đã đặt' ? 'success' : 'danger' }}">{{ $ticket->status }}</span>
                </div>
                <div class="col-md-3"><span class="text-muted d-block">Ngày đặt</span><strong>{{ optional($ticket->created_at)->format('d/m/Y H:i') }}</strong></div>
            </div>
        </div>

        <div class="stat-card">
            <h6 class="mb-3"><i class="fas fa-film me-2"></i>Thông tin suất chiếu</h6>
            <div class="row g-3">
                <div class="col-md-6"><span class="text-muted d-block">Phim</span><strong>{{ $showtime?->movie?->title ?? 'Không xác định' }}</strong></div>
                <div class="col-md-6"><span class="text-muted d-block">Rạp</span><strong>{{ $showtime?->theater?->name ?? 'Không xác định' }}</strong></div>
                <div class="col-md-4"><span class="text-muted d-block">Phòng chiếu</span><strong>{{ $showtime?->screen?->screen_name ?? 'Không xác định' }}</strong></div>
                <div class="col-md-4"><span class="text-muted d-block">Ngày chiếu</span><strong>{{ $showtime?->show_date?->format('d/m/Y') ?? 'Không xác định' }}</strong></div>
                <div class="col-md-4"><span class="text-muted d-block">Giờ chiếu</span><strong>{{ $showtime?->show_time ? \Carbon\Carbon::parse($showtime->show_time)->format('H:i') : 'Không xác định' }}</strong></div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="stat-card mb-4">
            <h6 class="mb-3"><i class="fas fa-user me-2"></i>Khách hàng</h6>
            <div class="mb-3"><span class="text-muted d-block">Họ tên</span><strong>{{ $customer?->name ?? $booking?->customer_name ?? 'Không xác định' }}</strong></div>
            <div class="mb-3"><span class="text-muted d-block">Email</span><span>{{ $customer?->email ?? $booking?->customer_email ?? 'Không xác định' }}</span></div>
            <div><span class="text-muted d-block">Số điện thoại</span><span>{{ $customer?->phone ?? $booking?->customer_phone ?? 'Không xác định' }}</span></div>
        </div>

        <div class="stat-card">
            <h6 class="mb-3"><i class="fas fa-qrcode me-2"></i>Mã xác nhận</h6>
            @if($ticket->qr_code)
                <code class="d-block p-3 bg-light rounded text-break">{{ $ticket->qr_code }}</code>
            @else
                <div class="alert alert-warning mb-0"><i class="fas fa-exclamation-triangle me-1"></i>Vé chưa có mã QR.</div>
            @endif
        </div>
    </div>
</div>
@endsection
