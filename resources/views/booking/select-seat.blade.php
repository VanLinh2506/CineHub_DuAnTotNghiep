@extends('layouts.app')

@php
    $title = 'Chọn ghế';
@endphp

@section('content')
<div class="container">
    <h2 class="mb-4">{{ $title }}</h2>

    <div class="row">
        <div class="col-md-8">
            <!-- Screen -->
            <div class="text-center mb-4">
                <div style="background: #333; color: white; padding: 15px 30px; display: inline-block; border-radius: 20px; font-weight: bold;">
                    MÀN HÌNH
                </div>
            </div>

            <!-- Seat Grid -->
            <div id="seatGrid" class="seat-grid mb-4">
                @if (!empty($seats))
                    @foreach ($seats as $seat)
                        <button type="button" class="seat seat-{{ $seat['status'] }}" 
                                data-seat-id="{{ $seat['id'] }}"
                                data-seat="{{ $seat['name'] }}"
                                data-price="{{ $seat['price'] }}"
                                onclick="toggleSeat(this, '{{ $seat['name'] }}', {{ $seat['price'] }})"
                                @if($seat['status'] !== 'available') disabled @endif>
                            {{ $seat['name'] }}
                        </button>
                    @endforeach
                @endif
            </div>

            <!-- Legend -->
            <div class="d-flex justify-content-center gap-4 mb-4">
                <div><span class="badge bg-light border" style="color: #333;">●</span> Còn trống</div>
                <div><span class="badge bg-success">●</span> Đang chọn</div>
                <div><span class="badge bg-secondary">●</span> Đã bán</div>
            </div>
        </div>

        <!-- Booking Summary -->
        <div class="col-md-4">
            <div class="stat-card">
                <h5 class="mb-3">Thông tin đặt vé</h5>
                
                <div class="mb-3">
                    <p><strong>Phim:</strong> {{ $movie['title'] }}</p>
                    <p><strong>Ngày:</strong> {{ \Carbon\Carbon::parse($showtime['show_date'])->format('d/m/Y') }}</p>
                    <p><strong>Giờ:</strong> {{ \Carbon\Carbon::parse($showtime['show_time'])->format('H:i') }}</p>
                    <p><strong>Phòng:</strong> {{ $screen['name'] }}</p>
                </div>

                <hr>

                <h6>Ghế được chọn:</h6>
                <div id="selectedSeatsList" class="mb-3">
                    <p class="text-muted small">Chọn ghế để tiếp tục</p>
                </div>

                <hr>

                <div class="d-flex justify-content-between mb-2">
                    <span>Tổng vé:</span>
                    <span id="totalTickets">0</span>
                </div>
                <div class="d-flex justify-content-between mb-3">
                    <span>Giá/vé:</span>
                    <span id="pricePerTicket">{{ number_format($showtime['price']) }}₫</span>
                </div>

                <hr>

                <div class="d-flex justify-content-between mb-3">
                    <strong>Tổng tiền:</strong>
                    <strong id="totalPrice" style="color: #e50914; font-size: 1.2rem;">0₫</strong>
                </div>

                <button type="button" class="btn btn-primary btn-lg w-100" onclick="proceedToPayment()">
                    <i class="fas fa-credit-card"></i> Tiếp tục thanh toán
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .seat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(40px, 1fr));
        gap: 8px;
        max-width: 600px;
        margin-left: auto;
        margin-right: auto;
    }

    .seat {
        aspect-ratio: 1;
        border: 2px solid #ddd;
        border-radius: 4px;
        background: white;
        cursor: pointer;
        font-size: 12px;
        padding: 0;
        transition: all 0.3s;
    }

    .seat:hover:not(:disabled) {
        border-color: #e50914;
        background: #ffe0e0;
    }

    .seat.seat-available:not(:disabled) {
        background: white;
    }

    .seat.seat-selected {
        background: #e50914;
        color: white;
        border-color: #e50914;
    }

    .seat.seat-sold,
    .seat:disabled {
        background: #ccc;
        border-color: #999;
        cursor: not-allowed;
        opacity: 0.6;
    }
</style>

<script>
    const pricePerTicket = {{ $showtime['price'] ?? 0 }};
    let selectedSeats = {};

    function toggleSeat(button, seatName, price) {
        if (button.disabled) return;
        
        button.classList.toggle('seat-selected');
        
        if (button.classList.contains('seat-selected')) {
            selectedSeats[seatName] = price;
        } else {
            delete selectedSeats[seatName];
        }
        
        updateSummary();
    }

    function updateSummary() {
        const seats = Object.keys(selectedSeats);
        const total = seats.length;
        const totalPrice = total * pricePerTicket;

        document.getElementById('selectedSeatsList').innerHTML = seats.length > 0 
            ? `<strong>${seats.join(', ')}</strong>`
            : '<p class="text-muted small">Chọn ghế để tiếp tục</p>';
        
        document.getElementById('totalTickets').textContent = total;
        document.getElementById('totalPrice').textContent = totalPrice.toLocaleString('vi-VN') + '₫';
    }

    function proceedToPayment() {
        const seats = Object.keys(selectedSeats);
        if (seats.length === 0) {
            alert('Vui lòng chọn ít nhất một ghế');
            return;
        }

        // Redirect to payment page with selected seats
        const seatIds = Array.from(document.querySelectorAll('.seat.seat-selected')).map(s => s.dataset.seatId).join(',');
        window.location.href = '{{ url("?route=booking/verify-tickets") }}&seats=' + seatIds + '&showtime_id={{ $showtime['id'] }}';
    }

    // Initialize summary
    updateSummary();
</script>
@endsection
