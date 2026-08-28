@extends('admin.counter_staff.layout')

@section('content')
<div class="sell-ticket-container">
    <h2><i class="fas fa-ticket-alt"></i> Bán vé trực tiếp</h2>

    <!-- Chọn suất chiếu -->
    <div class="showtime-selection">
        <div class="form-group">
            <label>Chọn ngày:</label>
            <input type="date" id="selectDate" value="{{ $date }}" min="{{ date('Y-m-d') }}" class="form-control">
        </div>
        <div class="showtimes-grid" id="showtimesGrid">
            @if($showtimes->isEmpty())
                <p class="no-data">Không có suất chiếu nào trong ngày này</p>
            @else
                @foreach($showtimes as $st)
                    <div class="showtime-card {{ ($selectedShowtime && $selectedShowtime->id == $st->id) ? 'selected' : '' }}"
                         data-showtime-id="{{ $st->id }}"
                         onclick="selectShowtime({{ $st->id }})">
                        <div class="movie-info">
                            @if($st->movie->poster)
                                <img src="{{ asset('storage/' . $st->movie->poster) }}" alt="{{ $st->movie->title }}">
                            @endif
                            <div class="details">
                                <h4>{{ $st->movie->title }}</h4>
                                <p><i class="fas fa-clock"></i> {{ date('H:i', strtotime($st->show_time)) }}</p>
                                <p><i class="fas fa-tv"></i> {{ $st->screen->screen_name ?? 'N/A' }}</p>
                                <p><i class="fas fa-chair"></i> Còn {{ $st->available_seats }} ghế</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    @if($selectedShowtime)
    <div class="seat-selection-section">
        <h3>Chọn ghế - {{ $selectedShowtime->movie->title }} ({{ date('H:i', strtotime($selectedShowtime->show_time)) }})</h3>
        <div class="screen-display">
            <div class="screen-label">MÀN HÌNH</div>
        </div>
        <div class="seat-map" id="seatMap">
            @php
                $rows = $seatLayout['rows'] ?? ['A','B','C','D','E','F','G','H','I','J'];
                $cols = $seatLayout['cols'] ?? range(1, (int) ($seatLayout['seats_per_row'] ?? 12));
                $vipRows = $seatLayout['vip_rows'] ?? ['D','E','F'];
                $coupleRows = $seatLayout['couple_rows'] ?? ['J'];
            @endphp
            @foreach($rows as $row)
                @php
                    $isVip = in_array($row, $vipRows);
                    $isCouple = in_array($row, $coupleRows);
                    // Hàng J có 6 ghế đôi thay vì 12 ghế thường
                    $rowGroups = [];
                    if ($isCouple) {
                        $coupleCount = max(1, (int) floor(count($cols) / 2));
                        $half = max(1, (int) ceil($coupleCount / 2));
                        $rowGroups = [range(1, $half), range($half + 1, $coupleCount)];
                    } elseif (!empty($seatLayout['seat_groups'])) {
                        foreach ($seatLayout['seat_groups'] as $group) {
                            if (in_array($row, $group['rows'] ?? []) && !empty($group['cols'])) {
                                $rowGroups[] = $group['cols'];
                            }
                        }
                    }
                    if (empty($rowGroups)) {
                        $half = max(1, (int) ceil(count($cols) / 2));
                        $rowGroups = [array_slice($cols, 0, $half), array_slice($cols, $half)];
                    }
                @endphp
                <div class="seat-row">
                    <span class="row-label">{{ $row }}</span>
                    <div class="seats">
                        @foreach($rowGroups as $groupIndex => $groupCols)
                            @if($groupIndex > 0)
                                <div class="seat-space"></div>
                            @endif
                            @foreach($groupCols as $i)
                            @php
                                $seatId = $row . $i;
                                $isBooked = in_array($seatId, $bookedSeats);
                                $seatClass = $isBooked ? 'booked' : ($isVip ? 'vip' : ($isCouple ? 'couple' : 'normal'));
                            @endphp
                            
                            <div class="seat {{ $seatClass }}"
                                 data-seat="{{ $seatId }}"
                                 data-type="{{ $isCouple ? 'couple' : ($isVip ? 'vip' : 'normal') }}"
                                 @if(!$isBooked) onclick="toggleSeat(this)" @endif>{{ $i }}</div>
                            @endforeach
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        <div class="seat-legend">
            <div class="legend-item"><span class="seat normal"></span> Thường</div>
            <div class="legend-item"><span class="seat vip"></span> VIP</div>
            <div class="legend-item"><span class="seat couple"></span> Đôi</div>
            <div class="legend-item"><span class="seat booked"></span> Đã đặt</div>
            <div class="legend-item"><span class="seat reserved"></span> Đang giữ online</div>
            <div class="legend-item"><span class="seat selected"></span> Đang chọn</div>
        </div>
        <div class="customer-info">
            <h4>Thông tin khách hàng</h4>
            <div class="form-row">
                <div class="form-group">
                    <label>Tên khách hàng:</label>
                    <input type="text" id="customerName" class="form-control" placeholder="Nhập tên khách hàng">
                </div>
                <div class="form-group">
                    <label>Số điện thoại:</label>
                    <input type="tel" id="customerPhone" class="form-control" placeholder="Nhập số điện thoại">
                </div>
            </div>
        </div>
        @if(isset($foodItems) && $foodItems->isNotEmpty())
        <div class="food-selection">
            <h4>Combo / đồ ăn uống</h4>
            <div class="food-grid">
                @foreach($foodItems as $food)
                    <div class="food-card" data-food-id="{{ $food->id }}" data-price="{{ (float) $food->price }}">
                        <div>
                            <strong>{{ $food->name }}</strong>
                            <small>{{ $food->type === 'combo' ? 'Combo' : ($food->type === 'snack' ? 'Snack' : 'Đồ uống') }}</small>
                        </div>
                        <div class="food-controls">
                            <span>{{ number_format($food->price) }} đ</span>
                            <button type="button" onclick="changeFoodQty({{ $food->id }}, -1)">-</button>
                            <input type="number" id="foodQty{{ $food->id }}" value="0" min="0" max="10" readonly>
                            <button type="button" onclick="changeFoodQty({{ $food->id }}, 1)">+</button>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif
        <div class="booking-summary">
            <div class="summary-row"><span>Ghế đã chọn:</span><span id="selectedSeats">Chưa chọn</span></div>
            <div class="summary-row"><span>Số lượng:</span><span id="seatCount">0</span></div>
            <div class="summary-row"><span>Combo / đồ ăn uống:</span><span id="foodTotal">0 đ</span></div>
            <div class="summary-row total"><span>Tổng tiền:</span><span id="totalPrice">0 đ</span></div>
            <button type="button" class="btn btn-primary btn-lg" id="btnProcessSale" onclick="processSale()" disabled>
                <i class="fas fa-cash-register"></i> Xác nhận bán vé
            </button>
        </div>
    </div>
    @endif
</div>
@endsection

@push('styles')
<style>
.sell-ticket-container { padding: 20px; }
.showtime-selection { margin-bottom: 30px; }
.showtime-selection > .form-group { margin-bottom: 18px; }
.showtime-selection > .form-group label { color: #4b5563; font-size: 14px; font-weight: 700; margin: 0 0 8px; }
.showtime-selection #selectDate { height: 52px; padding: 0 18px; border: 1px solid #cbd5e1; background: rgba(255,255,255,.82); color: #172033; box-shadow: 0 8px 24px rgba(15,23,42,.06); }
.showtimes-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 14px; margin-top: 0; }
.showtime-card { min-height: 148px; background: #202225; border-radius: 8px; padding: 18px; cursor: pointer; transition: transform .2s ease, border-color .2s ease, background .2s ease, box-shadow .2s ease; border: 1px solid #34373c; box-shadow: 0 8px 20px rgba(15,23,42,.10); }
.showtime-card:hover { transform: translateY(-2px); border-color: #717782; background: #272a2e; box-shadow: 0 12px 26px rgba(15,23,42,.16); }
.showtime-card.selected { border-color: #ef233c; background: #292326; box-shadow: inset 4px 0 0 #ef233c, 0 12px 28px rgba(239,35,60,.14); }
.showtime-card .movie-info { display: flex; gap: 15px; }
.showtime-card img { width: 80px; height: 120px; object-fit: cover; border-radius: 5px; }
.showtime-card .details { min-width: 0; }
.showtime-card .details h4 { margin: 0 0 14px; color: #f8fafc !important; font-size: 16px; line-height: 1.35; font-weight: 700 !important; }
.showtime-card .details p { display: flex; align-items: center; gap: 7px; margin: 7px 0; color: #c2c7d0; font-size: 14px; }
.showtime-card .details p i { width: 16px; color: #9da4af; text-align: center; }
.showtime-card.selected .details p i { color: #ff6478; }
.seat-selection-section { background: #1a1a1a; border-radius: 15px; padding: 30px; margin-top: 20px; }
.screen-display { text-align: center; margin-bottom: 30px; }
.screen-label { background: linear-gradient(180deg, #fff 0%, #ccc 100%); color: #333; padding: 10px 50px; border-radius: 5px; display: inline-block; font-weight: bold; }
.seat-map { max-width: 600px; margin: 0 auto; }
.seat-row { display: flex; align-items: center; margin-bottom: 8px; }
.row-label { width: 30px; text-align: center; font-weight: bold; color: #fff; }
.seats { display: flex; gap: 5px; justify-content: center; flex: 1; }
.seat-space { width: 20px; } /* Khoảng trống lối đi */
.seat { width: 35px; height: 35px; border-radius: 5px; display: flex; align-items: center; justify-content: center; cursor: pointer; font-size: 12px; font-weight: bold; transition: all 0.2s; }
.seat.normal { background: #4a4a4a; color: #fff; }
.seat.vip { background: #ffd700; color: #333; }
.seat.couple { background: #ff69b4; color: #fff; width: 75px; }
.seat.booked { background: #666; color: #999; cursor: not-allowed; }
.seat.reserved { background: #7c3aed; color: #fff; cursor: not-allowed; }
.seat.selected { background: #e50914 !important; color: #fff !important; transform: scale(1.1); }
.seat:not(.booked):not(.reserved):hover { transform: scale(1.1); }
.seat-legend { display: flex; justify-content: center; gap: 20px; margin: 30px 0; flex-wrap: wrap; }
.legend-item { display: flex; align-items: center; gap: 8px; color: #fff; }
.legend-item .seat { width: 25px; height: 25px; cursor: default; }
.customer-info { background: #2a2a2a; border-radius: 10px; padding: 20px; margin: 20px 0; }
.customer-info h4 { margin: 0 0 15px 0; color: #fff; }
.food-selection { background: #2a2a2a; border-radius: 10px; padding: 20px; margin: 20px 0; }
.food-selection h4 { margin: 0 0 15px 0; color: #fff; }
.food-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 12px; }
.food-card { display: flex; justify-content: space-between; gap: 12px; align-items: center; padding: 12px; border: 1px solid #444; border-radius: 8px; background: #333; color: #fff; }
.food-card strong { display: block; }
.food-card small { color: #aaa; }
.food-controls { display: flex; align-items: center; gap: 8px; white-space: nowrap; }
.food-controls button { width: 28px; height: 28px; border: 0; border-radius: 5px; background: #e50914; color: #fff; font-weight: bold; cursor: pointer; }
.food-controls input { width: 36px; height: 28px; text-align: center; border: 1px solid #555; border-radius: 5px; background: #222; color: #fff; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
.form-group label { display: block; margin-bottom: 5px; color: #aaa; }
.form-control { width: 100%; padding: 10px 15px; border: 1px solid #444; border-radius: 5px; background: #333; color: #fff; }
.booking-summary { background: #2a2a2a; border-radius: 10px; padding: 20px; }
.summary-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #444; color: #fff; }
.summary-row.total { font-size: 20px; font-weight: bold; color: #e50914; border-bottom: none; }
.btn-lg { width: 100%; padding: 15px; font-size: 18px; margin-top: 20px; }
.btn-primary { background: #e50914; border: none; color: #fff; border-radius: 5px; cursor: pointer; }
.btn-primary:disabled { background: #666; cursor: not-allowed; }
.no-data { color: #aaa; text-align: center; padding: 20px; }
@media (max-width: 768px) {
    .sell-ticket-container { padding: 8px; }
    .showtimes-grid { grid-template-columns: 1fr; }
    .showtime-card { min-height: auto; }
}
</style>
@endpush

@push('scripts')
<script>
let selectedSeats = [];
let selectedFood = {};
let showtimeId = {{ $selectedShowtime ? $selectedShowtime->id : 'null' }};
let seatStatusUrl = showtimeId
    ? @json(route('counter.api.seatStatus', ['showtime' => '__SHOWTIME__'])).replace('__SHOWTIME__', showtimeId)
    : null;
let seatStatusTimer = null;
let seatRealtimeChannel = null;
let prices = {
    normal: {{ $selectedShowtime ? $selectedShowtime->price : 0 }},
    vip: {{ $selectedShowtime ? ($selectedShowtime->price * 1.5) : 0 }},
    couple: {{ $selectedShowtime ? ($selectedShowtime->price * 2.5) : 0 }}
};
let foodPrices = {
@if(isset($foodItems))
@foreach($foodItems as $food)
    {{ $food->id }}: {{ (float) $food->price }},
@endforeach
@endif
};

function selectShowtime(id) {
    window.location.href = '{{ route("counter.sell") }}?showtime_id=' + id + '&date=' + document.getElementById('selectDate').value;
}

document.getElementById('selectDate')?.addEventListener('change', function() {
    window.location.href = '{{ route("counter.sell") }}?date=' + this.value;
});

function toggleSeat(element) {
    if (element.classList.contains('booked') || element.classList.contains('reserved')) return;
    const seat = element.dataset.seat;
    const type = element.dataset.type;
    
    // Check if already selected
    if (element.classList.contains('selected')) {
        element.classList.remove('selected');
        selectedSeats = selectedSeats.filter(s => s.seat !== seat);
    } else {
        // Check max 8 seats limit
        if (selectedSeats.length >= 8) {
            alert('Chỉ được đặt tối đa 8 ghế!');
            return;
        }
        
        element.classList.add('selected');
        selectedSeats.push({ seat, type });
    }
    
    updateSummary();
}

function uniqueSeats(seats) {
    return [...new Set((seats || []).map(String).filter(Boolean))];
}

function applySeatStatus(bookedSeats, reservedSeats) {
    const booked = uniqueSeats(bookedSeats);
    const reserved = uniqueSeats(reservedSeats).filter(seat => !booked.includes(seat));
    const unavailable = new Set([...booked, ...reserved]);
    const removedSeats = [];

    document.querySelectorAll('#seatMap .seat[data-seat]').forEach(element => {
        const seat = String(element.dataset.seat);
        const isBooked = booked.includes(seat);
        const isReserved = reserved.includes(seat);

        if (unavailable.has(seat) && element.classList.contains('selected')) {
            removedSeats.push(seat);
            selectedSeats = selectedSeats.filter(item => item.seat !== seat);
        }

        element.classList.toggle('booked', isBooked);
        element.classList.toggle('reserved', isReserved);
        element.classList.toggle('selected', !unavailable.has(seat) && selectedSeats.some(item => item.seat === seat));
        element.onclick = unavailable.has(seat) ? null : () => toggleSeat(element);
    });

    updateSummary();
    if (removedSeats.length) {
        alert('Ghế ' + removedSeats.join(', ') + ' vừa được khách online giữ hoặc đặt. Vui lòng chọn ghế khác.');
    }
}

function refreshSeatStatus() {
    if (!seatStatusUrl) return;
    fetch(seatStatusUrl, { headers: { 'Accept': 'application/json' }, cache: 'no-store' })
        .then(response => {
            if (!response.ok) throw new Error('HTTP ' + response.status);
            return response.json();
        })
        .then(data => applySeatStatus(data.bookedSeats, data.reservedSeats))
        .catch(error => console.warn('Không thể đồng bộ ghế tại quầy:', error));
}

function startSeatRealtime() {
    if (!showtimeId) return;
    refreshSeatStatus();
    seatStatusTimer = window.setInterval(refreshSeatStatus, 3000);

    if (!window.Echo || typeof window.Echo.private !== 'function') return;
    seatRealtimeChannel = 'booking.showtime.' + showtimeId;
    const handleSeatChange = event => {
        if (event && String(event.showtimeId) === String(showtimeId)) {
            applySeatStatus(event.bookedSeats, event.reservedSeats);
        }
    };
    window.Echo.private(seatRealtimeChannel)
        .listen('.seat:selected', handleSeatChange)
        .listen('.seat:released', handleSeatChange)
        .listen('.seat:paid', handleSeatChange)
        .listen('.seat:expired', handleSeatChange);
}

window.addEventListener('load', startSeatRealtime);
window.addEventListener('beforeunload', () => {
    if (seatStatusTimer) window.clearInterval(seatStatusTimer);
    if (seatRealtimeChannel && window.Echo) window.Echo.leave(seatRealtimeChannel);
});

function changeFoodQty(foodId, delta) {
    const input = document.getElementById('foodQty' + foodId);
    if (!input) return;

    const nextValue = Math.max(0, Math.min(10, parseInt(input.value || '0', 10) + delta));
    input.value = nextValue;

    if (nextValue > 0) {
        selectedFood[foodId] = nextValue;
    } else {
        delete selectedFood[foodId];
    }

    updateSummary();
}

function updateSummary() {
    const selectedSeatsText = selectedSeats.length > 0 
        ? selectedSeats.map(s => s.seat).join(', ') 
        : 'Chưa chọn';
    
    document.getElementById('selectedSeats').textContent = selectedSeatsText;
    document.getElementById('seatCount').textContent = selectedSeats.length;
    
    let total = 0;
    selectedSeats.forEach(s => { 
        total += prices[s.type]; 
    });

    let foodTotal = 0;
    Object.entries(selectedFood).forEach(([foodId, quantity]) => {
        foodTotal += (foodPrices[foodId] || 0) * quantity;
    });
    total += foodTotal;
    
    document.getElementById('foodTotal').textContent = foodTotal.toLocaleString('vi-VN') + ' đ';
    document.getElementById('totalPrice').textContent = total.toLocaleString('vi-VN') + ' đ';
    document.getElementById('btnProcessSale').disabled = selectedSeats.length === 0;
}

function processSale() {
    if (selectedSeats.length === 0) {
        alert('Vui lòng chọn ít nhất 1 ghế!');
        return;
    }
    
    if (selectedSeats.length > 8) {
        alert('Chỉ được đặt tối đa 8 ghế!');
        return;
    }
    
    const customerName = document.getElementById('customerName').value || 'Khách lẻ';
    const customerPhone = document.getElementById('customerPhone').value || '';
    
    if (!confirm('Xác nhận bán ' + selectedSeats.length + ' vé cho ' + customerName + '?')) {
        return;
    }
    
    // Disable button to prevent double click
    const btn = document.getElementById('btnProcessSale');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang xử lý...';
    
    fetch('{{ route("counter.processSale") }}', {
        method: 'POST',
        headers: { 
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ 
            showtime_id: showtimeId, 
            seats: selectedSeats.map(s => s.seat), 
            customer_name: customerName, 
            customer_phone: customerPhone,
            food_items: selectedFood
        })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Bán vé thành công!\n\nGhế: ' + selectedSeats.map(s => s.seat).join(', ') + '\nTổng tiền: ' + document.getElementById('totalPrice').textContent);
            // Reload to reset form
            window.location.reload();
        } else {
            alert('Lỗi: ' + data.message);
            refreshSeatStatus();
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-cash-register"></i> Xác nhận bán vé';
        }
    })
    .catch(error => {
        alert('Có lỗi xảy ra: ' + error);
        console.error('Error:', error);
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-cash-register"></i> Xác nhận bán vé';
    });
}
</script>
@endpush
