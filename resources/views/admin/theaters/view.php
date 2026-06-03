<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Thông tin rạp chiếu</h5>
    <a href="?route=admin/theaters" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="stat-card">
            <h6 class="mb-4" style="color: #000;">Thông tin cơ bản</h6>
            
            <?php if (!empty($theater['image'])): ?>
                <div class="mb-4" style="width: 100%; height: 400px; overflow: hidden; border-radius: 8px;">
                    <img src="<?php echo htmlspecialchars($theater['image']); ?>" 
                         alt="<?php echo htmlspecialchars($theater['name']); ?>" 
                         style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            <?php endif; ?>
            
            <table class="table table-borderless">
                <tr>
                    <td style="width: 200px; color: #000; font-weight: bold;">Tên rạp:</td>
                    <td style="color: #000;"><?php echo htmlspecialchars($theater['name']); ?></td>
                </tr>
                <tr>
                    <td style="color: #000; font-weight: bold;">Địa điểm:</td>
                    <td style="color: #000;">
                        <i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($theater['location'] ?? 'N/A'); ?>
                    </td>
                </tr>
                <?php if ($theater['address']): ?>
                <tr>
                    <td style="color: #000; font-weight: bold;">Địa chỉ:</td>
                    <td style="color: #000;"><?php echo htmlspecialchars($theater['address']); ?></td>
                </tr>
                <?php endif; ?>
                <?php if ($theater['phone']): ?>
                <tr>
                    <td style="color: #000; font-weight: bold;">Số điện thoại:</td>
                    <td style="color: #000;">
                        <i class="fas fa-phone"></i> <?php echo htmlspecialchars($theater['phone']); ?>
                    </td>
                </tr>
                <?php endif; ?>
                <?php if (isset($theater['latitude']) && isset($theater['longitude'])): ?>
                <tr>
                    <td style="color: #000; font-weight: bold;">Tọa độ:</td>
                    <td style="color: #000;">
                        <i class="fas fa-map-pin"></i> 
                        Latitude: <?php echo htmlspecialchars($theater['latitude']); ?>, 
                        Longitude: <?php echo htmlspecialchars($theater['longitude']); ?>
                        <br>
                        <a href="https://www.google.com/maps?q=<?php echo urlencode($theater['latitude'] . ',' . $theater['longitude']); ?>" 
                           target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                            <i class="fas fa-external-link-alt"></i> Xem trên Google Maps
                        </a>
                    </td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td style="color: #000; font-weight: bold;">Số phòng chiếu:</td>
                    <td style="color: #000;"><?php echo $theater['total_screens'] ?? 0; ?> phòng</td>
                </tr>
                <tr>
                    <td style="color: #000; font-weight: bold;">Trạng thái:</td>
                    <td>
                        <span class="badge bg-<?php echo $theater['is_active'] ? 'success' : 'secondary'; ?>">
                            <?php echo $theater['is_active'] ? 'Hoạt động' : 'Ngừng hoạt động'; ?>
                        </span>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="stat-card">
            <h6 class="mb-4" style="color: #000;">Admin của rạp</h6>
            <?php if ($moderator): ?>
                <div class="mb-3">
                    <p style="color: #000; margin-bottom: 5px;"><strong>Tên:</strong></p>
                    <p style="color: #000;"><?php echo htmlspecialchars($moderator['name']); ?></p>
                </div>
                <div class="mb-3">
                    <p style="color: #000; margin-bottom: 5px;"><strong>Email:</strong></p>
                    <p style="color: #000;"><?php echo htmlspecialchars($moderator['email']); ?></p>
                </div>
                <div class="mb-3">
                    <p style="color: #000; margin-bottom: 5px;"><strong>Ngày tạo:</strong></p>
                    <p style="color: #000;"><?php echo date('d/m/Y H:i', strtotime($moderator['created_at'])); ?></p>
                </div>
            <?php else: ?>
                <p style="color: #666;">Chưa có admin được gán cho rạp này</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Danh sách phòng chiếu -->
<div class="row mt-4">
    <div class="col-12">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 style="color: #000; margin: 0;">Danh sách phòng chiếu</h6>
                <span class="badge bg-secondary">
                    <i class="fas fa-info-circle me-1"></i> Chỉ xem - Liên hệ Admin rạp để chỉnh sửa
                </span>
            </div>
            
            <?php if (!empty($screens)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th style="color: #333;">Tên phòng</th>
                            <th style="color: #333;">Loại</th>
                            <th style="color: #333;">Số ghế</th>
                            <th style="color: #333;">Ghế VIP</th>
                            <th style="color: #333;">Ghế đôi</th>
                            <th style="color: #333;">Trạng thái</th>
                            <th style="color: #333;">Xem sơ đồ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($screens as $screen): 
                            $layout = $screen['seat_layout_config'] ? json_decode($screen['seat_layout_config'], true) : null;
                            $vipRows = $layout['vip_rows'] ?? [];
                            $coupleRows = $layout['couple_rows'] ?? [];
                        ?>
                        <tr>
                            <td style="color: #333;"><?php echo htmlspecialchars($screen['screen_name']); ?></td>
                            <td><span class="badge bg-info"><?php echo htmlspecialchars($screen['screen_type'] ?? '2D'); ?></span></td>
                            <td style="color: #333;"><?php echo $screen['total_seats'] ?? 0; ?> ghế</td>
                            <td style="color: #333;">
                                <?php echo !empty($vipRows) ? implode(', ', $vipRows) : 'Chưa cấu hình'; ?>
                            </td>
                            <td style="color: #333;">
                                <?php echo !empty($coupleRows) ? implode(', ', $coupleRows) : 'Chưa cấu hình'; ?>
                            </td>
                            <td>
                                <span class="badge bg-<?php echo $screen['is_active'] ? 'success' : 'secondary'; ?>">
                                    <?php echo $screen['is_active'] ? 'Hoạt động' : 'Tắt'; ?>
                                </span>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-info" 
                                        onclick="viewScreenLayout(<?php echo $screen['id']; ?>)"
                                        title="Xem sơ đồ ghế">
                                    <i class="fas fa-eye"></i> Xem
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <p style="color: #666; text-align: center;">Chưa có phòng chiếu nào.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Xem sơ đồ ghế (chỉ xem, không chỉnh sửa) -->
<div class="modal fade" id="viewScreenModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sơ đồ ghế - <span id="view_screen_name"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <p style="color: #333;"><strong>Loại phòng:</strong> <span id="view_screen_type" class="badge bg-info"></span></p>
                    </div>
                    <div class="col-md-4">
                        <p style="color: #333;"><strong>Tổng số ghế:</strong> <span id="view_total_seats"></span></p>
                    </div>
                    <div class="col-md-4">
                        <p style="color: #333;"><strong>Trạng thái:</strong> <span id="view_status"></span></p>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <p style="color: #333;"><strong>Hàng ghế VIP:</strong> <span id="view_vip_rows"></span></p>
                    </div>
                    <div class="col-md-6">
                        <p style="color: #333;"><strong>Hàng ghế đôi:</strong> <span id="view_couple_rows"></span></p>
                    </div>
                </div>
                
                <!-- Preview sơ đồ ghế -->
                <h6 style="color: #333;">Sơ đồ ghế</h6>
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
                </div>
                
                <div class="alert alert-info mt-3 mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    <small>Để chỉnh sửa sơ đồ ghế, vui lòng liên hệ Admin của rạp này.</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
// Dữ liệu screens từ PHP
const screensData = <?php echo json_encode($screens ?? []); ?>;

// Xem sơ đồ ghế (chỉ xem, không chỉnh sửa)
function viewScreenLayout(screenId) {
    const screen = screensData.find(s => s.id == screenId);
    if (!screen) return;
    
    const layout = screen.seat_layout_config ? JSON.parse(screen.seat_layout_config) : {};
    
    // Hiển thị thông tin
    document.getElementById('view_screen_name').textContent = screen.screen_name;
    document.getElementById('view_screen_type').textContent = screen.screen_type || '2D';
    document.getElementById('view_total_seats').textContent = screen.total_seats + ' ghế';
    document.getElementById('view_status').innerHTML = screen.is_active == 1 
        ? '<span class="badge bg-success">Hoạt động</span>' 
        : '<span class="badge bg-secondary">Tắt</span>';
    
    const vipRows = layout.vip_rows || [];
    const coupleRows = layout.couple_rows || [];
    
    document.getElementById('view_vip_rows').textContent = vipRows.length > 0 ? vipRows.join(', ') : 'Không có';
    document.getElementById('view_couple_rows').textContent = coupleRows.length > 0 ? coupleRows.join(', ') : 'Không có';
    
    // Render sơ đồ ghế
    const rows = layout.rows || [];
    const seatGroups = layout.seat_groups || null;
    const numGroups = seatGroups ? seatGroups.length : 1;
    const seatsPerGroupRow = seatGroups ? seatGroups[0].cols.length : (layout.cols ? layout.cols.length : 12);
    
    renderSeatPreviewView(rows, vipRows, coupleRows, numGroups, seatsPerGroupRow);
    
    // Show modal
    new bootstrap.Modal(document.getElementById('viewScreenModal')).show();
}

// Render seat preview (chỉ xem)
function renderSeatPreviewView(rows, vipRows, coupleRows, numGroups, seatsPerGroupRow) {
    const seatMap = document.getElementById('seatMap');
    seatMap.innerHTML = '';
    
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
        const isCouple = coupleRows.includes(rowLetter);
        
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
}
</script>

