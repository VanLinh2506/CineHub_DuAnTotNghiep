<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Cấu hình layout ghế - <?php echo htmlspecialchars($screen['screen_name']); ?></h5>
    <a href="?route=moderator/screens" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<?php
// Parse layout để lấy thông tin nhóm ghế
$numGroups = 1;
$seatsPerGroupRow = count($layout['cols'] ?? []);
$numRows = count($layout['rows'] ?? []);
$numVipRows = count($layout['vip_rows'] ?? []);

// Nếu có seat_groups, tính toán lại
if (!empty($layout['seat_groups'])) {
    $numGroups = count($layout['seat_groups']);
    $seatsPerGroupRow = count($layout['seat_groups'][0]['cols'] ?? []);
}
?>

<form method="POST" action="?route=moderator/screenLayoutUpdate" id="screenEditForm">
    <input type="hidden" name="screen_id" value="<?php echo $screen['id']; ?>">
    
    <div class="row">
        <div class="col-md-6">
            <div class="stat-card mb-4">
                <h6 class="mb-3">Thông tin phòng</h6>
                <div class="mb-3">
                    <label for="screen_name" class="form-label">Tên phòng <span class="text-danger">*</span></label>
                    <input type="text" name="screen_name" id="screen_name" class="form-control" value="<?php echo htmlspecialchars($screen['screen_name']); ?>" required placeholder="Ví dụ: Phòng 1, Phòng VIP">
                </div>
                <div class="mb-3">
                    <label for="screen_type" class="form-label">Loại phòng <span class="text-danger">*</span></label>
                    <select name="screen_type" id="screen_type" class="form-select" required>
                        <option value="2D" <?php echo ($screen['screen_type'] ?? '2D') === '2D' ? 'selected' : ''; ?>>2D</option>
                        <option value="3D" <?php echo ($screen['screen_type'] ?? '') === '3D' ? 'selected' : ''; ?>>3D</option>
                        <option value="IMAX" <?php echo ($screen['screen_type'] ?? '') === 'IMAX' ? 'selected' : ''; ?>>IMAX</option>
                        <option value="4DX" <?php echo ($screen['screen_type'] ?? '') === '4DX' ? 'selected' : ''; ?>>4DX</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Số ghế hiện tại:</label>
                    <input type="text" class="form-control" value="<?php echo $screen['total_seats']; ?>" disabled>
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
                <label for="num_groups" class="form-label">Số nhóm ghế <span class="text-danger">*</span></label>
                <input type="number" name="num_groups" id="num_groups" class="form-control" min="1" value="<?php echo $numGroups; ?>" required>
                <small class="text-muted">Số nhóm ghế trong phòng (thường là 1 hoặc 2 nhóm)</small>
            </div>
            <div class="col-md-6 mb-3">
                <label for="seats_per_group_row" class="form-label">Số ghế trên 1 hàng của 1 nhóm <span class="text-danger">*</span></label>
                <input type="number" name="seats_per_group_row" id="seats_per_group_row" class="form-control" min="1" value="<?php echo $seatsPerGroupRow; ?>" required>
                <small class="text-muted">Số ghế trong mỗi hàng của một nhóm</small>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="num_rows" class="form-label">Số hàng ghế <span class="text-danger">*</span></label>
                <input type="number" name="num_rows" id="num_rows" class="form-control" min="1" value="<?php echo $numRows; ?>" required>
                <small class="text-muted">Tổng số hàng ghế trong phòng</small>
            </div>
            <div class="col-md-6 mb-3">
                <label for="num_vip_rows" class="form-label">Số hàng ghế VIP <span class="text-danger">*</span></label>
                <input type="number" name="num_vip_rows" id="num_vip_rows" class="form-control" min="0" value="<?php echo $numVipRows; ?>" required>
                <small class="text-muted">Số hàng ghế VIP (từ hàng giữa)</small>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="has_couple_row" class="form-label">Có hàng ghế đôi?</label>
                <select name="has_couple_row" id="has_couple_row" class="form-select">
                    <option value="1" <?php echo !empty($layout['couple_rows']) ? 'selected' : ''; ?>>Có (hàng cuối)</option>
                    <option value="0" <?php echo empty($layout['couple_rows']) ? 'selected' : ''; ?>>Không</option>
                </select>
                <small class="text-muted">Hàng ghế đôi sẽ là hàng cuối cùng</small>
            </div>
        </div>
    </div>
    
    <!-- Xem trước sơ đồ ghế -->
    <div class="stat-card mb-4">
        <h6 class="mb-3">Xem trước sơ đồ ghế</h6>
        <div id="seatPreview" style="background: #1a1a2e; padding: 20px; border-radius: 10px; overflow-x: auto;">
            <div style="text-align: center; color: #fff; margin-bottom: 20px; padding: 10px; background: #333; border-radius: 5px;">MÀN HÌNH</div>
            <div id="seatMap" style="display: flex; flex-direction: column; align-items: center; gap: 5px;"></div>
            <div style="margin-top: 20px; display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">
                <span style="color: #fff; display: flex; align-items: center; gap: 5px;">
                    <span style="display: inline-block; width: 20px; height: 20px; background: #6c757d; border-radius: 3px;"></span> Thường
                </span>
                <span style="color: #fff; display: flex; align-items: center; gap: 5px;">
                    <span style="display: inline-block; width: 20px; height: 20px; background: #ffc107; border-radius: 3px;"></span> VIP
                </span>
                <span style="color: #fff; display: flex; align-items: center; gap: 5px;">
                    <span style="display: inline-block; width: 40px; height: 20px; background: #9c27b0; border-radius: 3px;"></span> Đôi
                </span>
            </div>
            <div id="seatSummary" style="margin-top: 15px; text-align: center; color: #28a745; font-weight: bold;"></div>
        </div>
    </div>
    
    <div class="d-flex justify-content-end gap-2">
        <a href="?route=moderator/screens" class="btn btn-secondary">Hủy</a>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save"></i> Lưu cấu hình
        </button>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const rowLetters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T'];
    
    function renderSeatPreview() {
        const numGroups = parseInt(document.getElementById('num_groups').value) || 1;
        const seatsPerGroupRow = parseInt(document.getElementById('seats_per_group_row').value) || 12;
        const numRows = parseInt(document.getElementById('num_rows').value) || 12;
        const numVipRows = parseInt(document.getElementById('num_vip_rows').value) || 0;
        const hasCoupleRow = document.getElementById('has_couple_row').value === '1';
        
        const seatMap = document.getElementById('seatMap');
        const seatSummary = document.getElementById('seatSummary');
        seatMap.innerHTML = '';
        
        if (!numGroups || !seatsPerGroupRow || !numRows) {
            seatMap.innerHTML = '<p style="color: #fff;">Nhập thông tin để xem sơ đồ ghế</p>';
            seatSummary.textContent = '';
            return;
        }
        
        const rows = rowLetters.slice(0, numRows);
        
        // Tính hàng VIP (ở giữa)
        const middleStartIndex = Math.floor((numRows - numVipRows) / 2);
        const vipRows = rows.slice(middleStartIndex, middleStartIndex + numVipRows);
        const coupleRow = hasCoupleRow ? rows[rows.length - 1] : null;
        
        // Khoảng cách giữa các nhóm
        const gapBetweenGroups = 2;
        
        rows.forEach(function(rowLetter) {
            const rowDiv = document.createElement('div');
            rowDiv.style.cssText = 'display: flex; align-items: center; gap: 3px;';
            
            // Row label
            const rowLabel = document.createElement('span');
            rowLabel.style.cssText = 'width: 25px; color: #fff; font-weight: bold; font-size: 12px; text-align: center;';
            rowLabel.textContent = rowLetter;
            rowDiv.appendChild(rowLabel);
            
            const isVip = vipRows.includes(rowLetter);
            const isCouple = rowLetter === coupleRow;
            
            // Render các nhóm ghế
            let seatNumber = 1;
            for (let g = 0; g < numGroups; g++) {
                if (isCouple) {
                    // Ghế đôi - mỗi 2 ghế gộp thành 1
                    for (let s = 0; s < seatsPerGroupRow; s += 2) {
                        const seat = document.createElement('div');
                        const seatNum1 = seatNumber;
                        const seatNum2 = seatNumber + 1;
                        seat.style.cssText = 'width: 50px; height: 24px; background: #9c27b0; border-radius: 3px; display: flex; align-items: center; justify-content: center; color: #fff; font-size: 9px; font-weight: bold; margin: 1px;';
                        seat.textContent = rowLetter + seatNum1 + '-' + seatNum2;
                        rowDiv.appendChild(seat);
                        seatNumber += 2;
                    }
                } else {
                    // Ghế thường hoặc VIP
                    for (let s = 0; s < seatsPerGroupRow; s++) {
                        const seat = document.createElement('div');
                        seat.style.cssText = 'width: 24px; height: 24px; background: ' + (isVip ? '#ffc107' : '#6c757d') + '; border-radius: 3px; display: flex; align-items: center; justify-content: center; color: ' + (isVip ? '#000' : '#fff') + '; font-size: 9px; font-weight: bold; margin: 1px;';
                        seat.textContent = seatNumber;
                        rowDiv.appendChild(seat);
                        seatNumber++;
                    }
                }
                
                // Khoảng cách giữa các nhóm (trừ nhóm cuối)
                if (g < numGroups - 1) {
                    const gap = document.createElement('span');
                    gap.style.cssText = 'width: ' + (gapBetweenGroups * 26) + 'px;';
                    rowDiv.appendChild(gap);
                }
            }
            
            seatMap.appendChild(rowDiv);
        });
        
        // Tính tổng số ghế
        let totalSeats = numRows * seatsPerGroupRow * numGroups;
        let vipSeats = numVipRows * seatsPerGroupRow * numGroups;
        let coupleSeats = hasCoupleRow ? seatsPerGroupRow * numGroups : 0;
        let normalSeats = totalSeats - vipSeats - coupleSeats;
        
        // Nếu hàng VIP trùng với hàng couple, điều chỉnh
        if (hasCoupleRow && vipRows.includes(coupleRow)) {
            vipSeats -= seatsPerGroupRow * numGroups;
        }
        
        seatSummary.innerHTML = 'Tổng: <strong>' + totalSeats + '</strong> ghế | ' +
            'Thường: <strong>' + normalSeats + '</strong> | ' +
            'VIP: <strong>' + vipSeats + '</strong> | ' +
            'Đôi: <strong>' + (coupleSeats / 2) + '</strong> cặp';
    }
    
    // Initial render
    renderSeatPreview();
    
    // Update preview when inputs change
    document.getElementById('num_groups').addEventListener('input', renderSeatPreview);
    document.getElementById('seats_per_group_row').addEventListener('input', renderSeatPreview);
    document.getElementById('num_rows').addEventListener('input', renderSeatPreview);
    document.getElementById('num_vip_rows').addEventListener('input', renderSeatPreview);
    document.getElementById('has_couple_row').addEventListener('change', renderSeatPreview);
});
</script>
