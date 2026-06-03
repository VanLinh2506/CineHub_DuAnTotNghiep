@extends('admin.moderator.layout')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Cấu hình layout ghế - {{ $screen['screen_name'] }}</h5>
    <a href="?route=moderator/screens" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Quay lại</a>
</div>

@php
    $numGroups = 1;
    $seatsPerGroupRow = count($layout['cols'] ?? []);
    $numRows = count($layout['rows'] ?? []);
    $numVipRows = count($layout['vip_rows'] ?? []);
    if (!empty($layout['seat_groups'])) {
        $numGroups = count($layout['seat_groups']);
        $seatsPerGroupRow = count($layout['seat_groups'][0]['cols'] ?? []);
    }
@endphp

<form method="POST" action="?route=moderator/screenLayoutUpdate" id="screenEditForm">
    <input type="hidden" name="screen_id" value="{{ $screen['id'] }}">
    <div class="row">
        <div class="col-md-6">
            <div class="stat-card mb-4">
                <h6 class="mb-3">Thông tin phòng</h6>
                <div class="mb-3">
                    <label class="form-label">Tên phòng <span class="text-danger">*</span></label>
                    <input type="text" name="screen_name" class="form-control" value="{{ $screen['screen_name'] }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Loại phòng <span class="text-danger">*</span></label>
                    <select name="screen_type" class="form-select" required>
                        <option value="2D" {{ ($screen['screen_type'] ?? '2D') === '2D' ? 'selected' : '' }}>2D</option>
                        <option value="3D" {{ ($screen['screen_type'] ?? '') === '3D' ? 'selected' : '' }}>3D</option>
                        <option value="IMAX" {{ ($screen['screen_type'] ?? '') === 'IMAX' ? 'selected' : '' }}>IMAX</option>
                        <option value="4DX" {{ ($screen['screen_type'] ?? '') === '4DX' ? 'selected' : '' }}>4DX</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Số ghế hiện tại:</label>
                    <input type="text" class="form-control" value="{{ $screen['total_seats'] }}" disabled>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card mb-4">
                <h6 class="mb-3">Thông tin giá vé</h6>
                <div class="alert alert-info mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Lưu ý:</strong> Giá vé được tính tự động dựa trên:
                    <ul class="mb-0 mt-2">
                        <li>Giá cơ bản từ <strong>lịch chiếu</strong></li>
                        <li>Phụ phí theo <strong>loại phòng</strong> (2D: 0đ, 3D: +30,000đ, IMAX: +50,000đ, 4DX: +70,000đ)</li>
                        <li>Phụ phí theo <strong>loại ghế</strong> (VIP: +30%, Đôi: +50%)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="stat-card mb-4">
        <h6 class="mb-3">Cấu hình nhóm ghế</h6>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Số nhóm ghế <span class="text-danger">*</span></label>
                <input type="number" name="num_groups" id="num_groups" class="form-control" min="1" value="{{ $numGroups }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Số ghế trên 1 hàng của 1 nhóm <span class="text-danger">*</span></label>
                <input type="number" name="seats_per_group_row" id="seats_per_group_row" class="form-control" min="1" value="{{ $seatsPerGroupRow }}" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Số hàng ghế <span class="text-danger">*</span></label>
                <input type="number" name="num_rows" id="num_rows" class="form-control" min="1" value="{{ $numRows }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Số hàng ghế VIP <span class="text-danger">*</span></label>
                <input type="number" name="num_vip_rows" id="num_vip_rows" class="form-control" min="0" value="{{ $numVipRows }}" required>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Có hàng ghế đôi?</label>
                <select name="has_couple_row" id="has_couple_row" class="form-select">
                    <option value="1" {{ !empty($layout['couple_rows']) ? 'selected' : '' }}>Có (hàng cuối)</option>
                    <option value="0" {{ empty($layout['couple_rows']) ? 'selected' : '' }}>Không</option>
                </select>
            </div>
        </div>
    </div>

    <div class="stat-card mb-4">
        <h6 class="mb-3">Xem trước sơ đồ ghế</h6>
        <div id="seatPreview" style="background: #1a1a2e; padding: 20px; border-radius: 10px; overflow-x: auto;">
            <div style="text-align: center; color: #fff; margin-bottom: 20px; padding: 10px; background: #333; border-radius: 5px;">MÀN HÌNH</div>
            <div id="seatMap" style="display: flex; flex-direction: column; align-items: center; gap: 5px;"></div>
            <div style="margin-top: 20px; display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
                <span style="color:#fff;display:flex;align-items:center;gap:5px;"><span style="display:inline-block;width:20px;height:20px;background:#6c757d;border-radius:3px;"></span> Thường</span>
                <span style="color:#fff;display:flex;align-items:center;gap:5px;"><span style="display:inline-block;width:20px;height:20px;background:#ffc107;border-radius:3px;"></span> VIP</span>
                <span style="color:#fff;display:flex;align-items:center;gap:5px;"><span style="display:inline-block;width:40px;height:20px;background:#9c27b0;border-radius:3px;"></span> Đôi</span>
            </div>
            <div id="seatSummary" style="margin-top: 15px; text-align: center; color: #28a745; font-weight: bold;"></div>
        </div>
    </div>

    <div class="d-flex justify-content-end gap-2">
        <a href="?route=moderator/screens" class="btn btn-secondary">Hủy</a>
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Lưu cấu hình</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const rowLetters = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T'];

    function renderSeatPreview() {
        const numGroups = parseInt(document.getElementById('num_groups').value) || 1;
        const seatsPerGroupRow = parseInt(document.getElementById('seats_per_group_row').value) || 12;
        const numRows = parseInt(document.getElementById('num_rows').value) || 12;
        const numVipRows = parseInt(document.getElementById('num_vip_rows').value) || 0;
        const hasCoupleRow = document.getElementById('has_couple_row').value === '1';
        const seatMap = document.getElementById('seatMap');
        const seatSummary = document.getElementById('seatSummary');
        seatMap.innerHTML = '';
        if (!numGroups || !seatsPerGroupRow || !numRows) { seatMap.innerHTML = '<p style="color:#fff;">Nhập thông tin để xem sơ đồ ghế</p>'; seatSummary.textContent = ''; return; }
        const rows = rowLetters.slice(0, numRows);
        const middleStartIndex = Math.floor((numRows - numVipRows) / 2);
        const vipRows = rows.slice(middleStartIndex, middleStartIndex + numVipRows);
        const coupleRow = hasCoupleRow ? rows[rows.length - 1] : null;
        rows.forEach(rowLetter => {
            const rowDiv = document.createElement('div');
            rowDiv.style.cssText = 'display:flex;align-items:center;gap:3px;';
            const rowLabel = document.createElement('span');
            rowLabel.style.cssText = 'width:25px;color:#fff;font-weight:bold;font-size:12px;text-align:center;';
            rowLabel.textContent = rowLetter;
            rowDiv.appendChild(rowLabel);
            const isVip = vipRows.includes(rowLetter), isCouple = rowLetter === coupleRow;
            let seatNumber = 1;
            for (let g = 0; g < numGroups; g++) {
                if (isCouple) {
                    for (let s = 0; s < seatsPerGroupRow; s += 2) {
                        const seat = document.createElement('div');
                        seat.style.cssText = 'width:50px;height:24px;background:#9c27b0;border-radius:3px;display:flex;align-items:center;justify-content:center;color:#fff;font-size:9px;font-weight:bold;margin:1px;';
                        seat.textContent = rowLetter + seatNumber + '-' + (seatNumber + 1);
                        rowDiv.appendChild(seat); seatNumber += 2;
                    }
                } else {
                    for (let s = 0; s < seatsPerGroupRow; s++) {
                        const seat = document.createElement('div');
                        seat.style.cssText = `width:24px;height:24px;background:${isVip?'#ffc107':'#6c757d'};border-radius:3px;display:flex;align-items:center;justify-content:center;color:${isVip?'#000':'#fff'};font-size:9px;font-weight:bold;margin:1px;`;
                        seat.textContent = seatNumber;
                        rowDiv.appendChild(seat); seatNumber++;
                    }
                }
                if (g < numGroups - 1) { const gap = document.createElement('span'); gap.style.cssText = `width:${2*26}px;`; rowDiv.appendChild(gap); }
            }
            seatMap.appendChild(rowDiv);
        });
        let totalSeats = numRows * seatsPerGroupRow * numGroups;
        let vipSeats = numVipRows * seatsPerGroupRow * numGroups;
        let coupleSeats = hasCoupleRow ? seatsPerGroupRow * numGroups : 0;
        let normalSeats = totalSeats - vipSeats - coupleSeats;
        if (hasCoupleRow && vipRows.includes(coupleRow)) vipSeats -= seatsPerGroupRow * numGroups;
        seatSummary.innerHTML = `Tổng: <strong>${totalSeats}</strong> ghế | Thường: <strong>${normalSeats}</strong> | VIP: <strong>${vipSeats}</strong> | Đôi: <strong>${coupleSeats/2}</strong> cặp`;
    }

    renderSeatPreview();
    ['num_groups','seats_per_group_row','num_rows','num_vip_rows'].forEach(id => {
        document.getElementById(id)?.addEventListener('input', renderSeatPreview);
    });
    document.getElementById('has_couple_row')?.addEventListener('change', renderSeatPreview);
});
</script>
@endpush
