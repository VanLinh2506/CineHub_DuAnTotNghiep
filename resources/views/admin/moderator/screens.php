<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Quản lý phòng chiếu - <?php echo htmlspecialchars($theater['name']); ?></h5>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addScreenModal">
        <i class="fas fa-plus"></i> Thêm phòng mới
    </button>
</div>

<!-- Filter -->
<div class="stat-card mb-3">
    <form method="GET" class="row g-2">
        <input type="hidden" name="route" value="moderator/screens">
        <div class="col-md-4">
            <label for="movie_filter" class="form-label">Lọc theo phim</label>
            <select name="movie_id" id="movie_filter" class="form-select" onchange="this.form.submit()">
                <option value="">Tất cả phòng</option>
                <?php foreach ($movies as $movie): ?>
                    <option value="<?php echo $movie['id']; ?>" <?php echo (isset($_GET['movie_id']) && $_GET['movie_id'] == $movie['id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($movie['title']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
            <a href="?route=moderator/screens" class="btn btn-outline-secondary w-100">
                <i class="fas fa-redo"></i> Xóa lọc
            </a>
        </div>
    </form>
</div>

<div class="stat-card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Tên phòng</th>
                    <th>Loại phòng</th>
                    <th>Số ghế</th>
                    <th>Phim đang chiếu</th>
                    <th>Trạng thái</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($screens)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted">Chưa có phòng chiếu nào</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($screens as $screen): ?>
                        <tr>
                            <td><?php echo $screen['id']; ?></td>
                            <td><?php echo htmlspecialchars($screen['screen_name']); ?></td>
                            <td>
                                <span class="badge bg-info"><?php echo htmlspecialchars($screen['screen_type'] ?? '2D'); ?></span>
                            </td>
                            <td><?php echo $screen['total_seats']; ?> ghế</td>
                            <td>
                                <?php if (!empty($screen['current_movies']) && is_array($screen['current_movies']) && count($screen['current_movies']) > 0): ?>
                                    <div class="d-flex flex-column gap-1">
                                        <?php foreach ($screen['current_movies'] as $movie): ?>
                                            <span class="badge bg-primary">
                                                <?php echo htmlspecialchars($movie['title'] ?? ''); ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted">Chưa có phim</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($screen['is_active']): ?>
                                    <span class="badge bg-success">Hoạt động</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Tạm dừng</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="?route=moderator/screenEdit&id=<?php echo $screen['id']; ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-cog"></i> Layout
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#manageMoviesModal"
                                            data-screen-id="<?php echo $screen['id']; ?>"
                                            data-screen-name="<?php echo htmlspecialchars($screen['screen_name']); ?>"
                                            onclick="loadScreenMovies(<?php echo $screen['id']; ?>, '<?php echo htmlspecialchars($screen['screen_name']); ?>')">
                                        <i class="fas fa-film"></i> Quản lý phim
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Screen Modal -->
<div class="modal fade" id="addScreenModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm phòng mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="?route=moderator/screensStore" id="addScreenForm">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="screen_name" class="form-label">Tên phòng <span class="text-danger">*</span></label>
                            <input type="text" name="screen_name" id="screen_name" class="form-control" required placeholder="Ví dụ: Phòng 1, Phòng VIP">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="screen_type" class="form-label">Loại phòng <span class="text-danger">*</span></label>
                            <select name="screen_type" id="screen_type" class="form-select" required>
                                <option value="2D">2D</option>
                                <option value="3D">3D</option>
                            </select>
                        </div>
                    </div>
                    
                    <hr>
                    <h6 class="mb-3">Cấu hình sơ đồ ghế</h6>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="num_groups" class="form-label">Số nhóm ghế <span class="text-danger">*</span></label>
                            <input type="number" name="num_groups" id="num_groups" class="form-control" min="1" value="1" required onchange="updateSeatPreview()">
                            <small class="text-muted">Số nhóm ghế trong phòng (thường là 1 hoặc 2 nhóm)</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="seats_per_group_row" class="form-label">Số ghế trên 1 hàng của 1 nhóm <span class="text-danger">*</span></label>
                            <input type="number" name="seats_per_group_row" id="seats_per_group_row" class="form-control" min="1" value="12" required onchange="updateSeatPreview()">
                            <small class="text-muted">Số ghế trong mỗi hàng của một nhóm</small>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="num_rows" class="form-label">Số hàng ghế <span class="text-danger">*</span></label>
                            <input type="number" name="num_rows" id="num_rows" class="form-control" min="1" value="12" required onchange="updateSeatPreview()">
                            <small class="text-muted">Tổng số hàng ghế trong phòng</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="num_vip_rows" class="form-label">Số hàng ghế VIP <span class="text-danger">*</span></label>
                            <input type="number" name="num_vip_rows" id="num_vip_rows" class="form-control" min="0" value="3" required onchange="updateSeatPreview()">
                            <small class="text-muted">Số hàng ghế VIP (từ hàng giữa)</small>
                        </div>
                    </div>
                    
                    <div class="alert alert-info mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <small><strong>Lưu ý:</strong> Giá vé được tính tự động từ lịch chiếu + phụ phí loại phòng (3D: +30k, IMAX: +50k, 4DX: +70k) + phụ phí loại ghế (VIP: +30%, Đôi: +50%)</small>
                    </div>
                    
                    <hr>
                    <h6 class="mb-3">Xem trước sơ đồ ghế</h6>
                    <div id="seatPreview" class="border rounded p-3" style="background: #f8f9fa; min-height: 200px; max-height: 400px; overflow: auto;">
                        <p class="text-muted text-center">Nhập thông tin để xem sơ đồ ghế</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm phòng</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Manage Movies Modal -->
<div class="modal fade" id="manageMoviesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quản lý phim - <span id="modalScreenName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="currentScreenId" value="">
                
                <!-- Add Movie Form -->
                <div class="card mb-3">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-plus"></i> Thêm phim vào phòng</h6>
                    </div>
                    <div class="card-body">
                        <form id="addMovieToScreenForm" method="POST" action="?route=moderator/screenAddMovie">
                            <input type="hidden" name="screen_id" id="addMovieScreenId">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="add_movie_id" class="form-label">Chọn phim <span class="text-danger">*</span></label>
                                    <select name="movie_id" id="add_movie_id" class="form-select" required>
                                        <option value="">-- Chọn phim --</option>
                                        <?php foreach ($movies as $movie): ?>
                                            <option value="<?php echo $movie['id']; ?>">
                                                <?php echo htmlspecialchars($movie['title']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="add_price" class="form-label">Giá vé (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="number" name="price" id="add_price" class="form-control" min="0" value="120000" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="add_from_date" class="form-label">Từ ngày <span class="text-danger">*</span></label>
                                    <input type="date" name="from_date" id="add_from_date" class="form-control" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="add_to_date" class="form-label">Đến ngày <span class="text-danger">*</span></label>
                                    <input type="date" name="to_date" id="add_to_date" class="form-control" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Khung giờ chiếu <span class="text-danger">*</span></label>
                                <div class="border rounded p-3">
                                    <div class="row" id="addTimeSlotsContainer">
                                        <!-- Time slots will be added here -->
                                    </div>
                                    <button type="button" class="btn btn-sm btn-outline-primary mt-2" onclick="addTimeSlotToForm()">
                                        <i class="fas fa-plus"></i> Thêm khung giờ
                                    </button>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Thêm lịch chiếu
                            </button>
                        </form>
                    </div>
                </div>
                
                <!-- Current Movies List -->
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fas fa-list"></i> Phim đang chiếu trong phòng</h6>
                    </div>
                    <div class="card-body">
                        <div id="currentMoviesList">
                            <p class="text-muted text-center">Đang tải...</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

<script>
let timeSlotCount = 0;
const defaultTimeSlots = ['10:00', '14:00', '18:00', '20:30'];

function loadScreenMovies(screenId, screenName) {
    document.getElementById('currentScreenId').value = screenId;
    document.getElementById('addMovieScreenId').value = screenId;
    document.getElementById('modalScreenName').textContent = screenName;
    
    // Reset form
    document.getElementById('addMovieToScreenForm').reset();
    document.getElementById('addMovieScreenId').value = screenId;
    timeSlotCount = 0;
    document.getElementById('addTimeSlotsContainer').innerHTML = '';
    
    // Add default time slots
    defaultTimeSlots.forEach(function(time) {
        addTimeSlotToForm(time);
    });
    
    // Set default dates
    const today = new Date().toISOString().split('T')[0];
    document.getElementById('add_from_date').value = today;
    document.getElementById('add_from_date').min = today;
    document.getElementById('add_to_date').min = today;
    
    // Load current movies
    fetch('?route=moderator/screenMovies&screen_id=' + screenId)
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('currentMoviesList');
            if (data.success && data.movies && data.movies.length > 0) {
                let html = '<div class="table-responsive"><table class="table table-sm">';
                html += '<thead><tr><th>Phim</th><th>Từ ngày</th><th>Đến ngày</th><th>Số suất</th><th>Thao tác</th></tr></thead><tbody>';
                
                data.movies.forEach(function(movie) {
                    html += '<tr>';
                    html += '<td><strong>' + escapeHtml(movie.title) + '</strong></td>';
                    html += '<td>' + formatDate(movie.from_date) + '</td>';
                    html += '<td>' + formatDate(movie.to_date) + '</td>';
                    html += '<td>' + movie.showtime_count + ' suất</td>';
                    html += '<td>';
                    html += '<button type="button" class="btn btn-sm btn-outline-danger" onclick="removeMovieFromScreen(' + screenId + ', ' + movie.id + ', \'' + escapeHtml(movie.title) + '\')">';
                    html += '<i class="fas fa-trash"></i> Xóa lịch chiếu';
                    html += '</button>';
                    html += '</td>';
                    html += '</tr>';
                });
                
                html += '</tbody></table></div>';
                container.innerHTML = html;
            } else {
                container.innerHTML = '<p class="text-muted text-center">Chưa có phim nào trong phòng này</p>';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('currentMoviesList').innerHTML = '<p class="text-danger text-center">Lỗi khi tải dữ liệu</p>';
        });
}

function addTimeSlotToForm(time = '') {
    timeSlotCount++;
    const container = document.getElementById('addTimeSlotsContainer');
    const col = document.createElement('div');
    col.className = 'col-md-3 mb-2';
    col.id = 'timeslot-' + timeSlotCount;
    col.innerHTML = `
        <div class="input-group">
            <input type="time" class="form-control" name="showtimes_time[]" value="${time}" required>
            <button type="button" class="btn btn-outline-danger" onclick="removeTimeSlotFromForm(${timeSlotCount})">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    container.appendChild(col);
}

function removeTimeSlotFromForm(id) {
    const slot = document.getElementById('timeslot-' + id);
    if (slot) {
        slot.remove();
    }
}

function removeMovieFromScreen(screenId, movieId, movieTitle) {
    if (!confirm('Bạn có chắc chắn muốn xóa tất cả lịch chiếu của phim "' + movieTitle + '" khỏi phòng này?')) {
        return;
    }
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '?route=moderator/screenRemoveMovie';
    
    const screenInput = document.createElement('input');
    screenInput.type = 'hidden';
    screenInput.name = 'screen_id';
    screenInput.value = screenId;
    
    const movieInput = document.createElement('input');
    movieInput.type = 'hidden';
    movieInput.name = 'movie_id';
    movieInput.value = movieId;
    
    form.appendChild(screenInput);
    form.appendChild(movieInput);
    document.body.appendChild(form);
    form.submit();
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, m => map[m]);
}

function formatDate(dateString) {
    if (!dateString) return '';
    const date = new Date(dateString);
    return date.toLocaleDateString('vi-VN');
}

// Set min date for to_date when from_date changes
document.addEventListener('DOMContentLoaded', function() {
    const fromDateInput = document.getElementById('add_from_date');
    const toDateInput = document.getElementById('add_to_date');
    
    if (fromDateInput && toDateInput) {
        fromDateInput.addEventListener('change', function() {
            if (this.value) {
                toDateInput.min = this.value;
            }
        });
    }
    
    // Gọi updateSeatPreview khi modal mở
    const modal = document.getElementById('addScreenModal');
    if (modal) {
        modal.addEventListener('shown.bs.modal', function() {
            updateSeatPreview();
        });
    }
});

// Function để cập nhật preview sơ đồ ghế
function updateSeatPreview() {
    const numGroups = parseInt(document.getElementById('num_groups')?.value || 1);
    const seatsPerGroupRow = parseInt(document.getElementById('seats_per_group_row')?.value || 12);
    const numRows = parseInt(document.getElementById('num_rows')?.value || 12);
    const numVipRows = parseInt(document.getElementById('num_vip_rows')?.value || 3);
    
    const preview = document.getElementById('seatPreview');
    if (!preview) return;
    
    if (!numGroups || !seatsPerGroupRow || !numRows) {
        preview.innerHTML = '<p class="text-muted text-center">Nhập thông tin để xem sơ đồ ghế</p>';
        return;
    }
    
    // Tính toán layout
    const rowLetters = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T'];
    const rows = rowLetters.slice(0, numRows);
    
    // Tính hàng VIP (ở giữa)
    const middleStartIndex = Math.floor((numRows - numVipRows) / 2);
    const vipRows = rows.slice(middleStartIndex, middleStartIndex + numVipRows);
    const coupleRow = rows[rows.length - 1]; // Hàng cuối là ghế đôi
    
    // Khoảng cách giữa các nhóm
    const gapBetweenGroups = 2;
    
    // Tạo HTML preview
    let html = '<div style="text-align: center; margin-bottom: 15px;">';
    html += '<div style="background: #333; color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px; font-weight: bold;">MÀN HÌNH</div>';
    html += '</div>';
    
    html += '<div style="display: flex; flex-direction: column; gap: 3px; align-items: center;">';
    
    rows.forEach((row, rowIndex) => {
        const isVip = vipRows.includes(row);
        const isCouple = row === coupleRow;
        
        html += '<div style="display: flex; align-items: center; gap: 3px; margin-bottom: 2px;">';
        html += `<span style="width: 25px; text-align: center; font-weight: bold; color: ${isVip ? '#ffc107' : isCouple ? '#e91e63' : '#333'}; font-size: 12px;">${row}</span>`;
        
        // Render các nhóm ghế với số ghế liên tục
        let seatNumber = 1;
        for (let g = 0; g < numGroups; g++) {
            if (isCouple) {
                // Ghế đôi - mỗi 2 ghế gộp thành 1
                for (let s = 0; s < seatsPerGroupRow; s += 2) {
                    const seatNum1 = seatNumber;
                    const seatNum2 = seatNumber + 1;
                    html += `<span style="display: inline-block; width: 50px; height: 25px; background: #e91e63; color: white; border-radius: 4px; text-align: center; line-height: 25px; font-size: 9px; font-weight: bold; margin: 1px;">${row}${seatNum1}-${seatNum2}</span>`;
                    seatNumber += 2;
                }
            } else {
                // Ghế thường hoặc VIP
                for (let s = 0; s < seatsPerGroupRow; s++) {
                    const bgColor = isVip ? '#ffc107' : '#6c757d';
                    const textColor = isVip ? '#000' : 'white';
                    html += `<span style="display: inline-block; width: 25px; height: 25px; background: ${bgColor}; color: ${textColor}; border-radius: 4px; text-align: center; line-height: 25px; font-size: 10px; font-weight: bold; margin: 1px;">${seatNumber}</span>`;
                    seatNumber++;
                }
            }
            
            // Khoảng cách giữa các nhóm (trừ nhóm cuối)
            if (g < numGroups - 1) {
                html += `<span style="display: inline-block; width: ${gapBetweenGroups * 26}px;"></span>`;
            }
        }
        
        html += '</div>';
    });
    
    html += '</div>';
    
    // Tính số ghế theo loại
    const totalSeats = numRows * seatsPerGroupRow * numGroups;
    const vipSeats = numVipRows * seatsPerGroupRow * numGroups;
    const coupleSeats = seatsPerGroupRow * numGroups;
    const normalSeats = totalSeats - vipSeats - coupleSeats;
    
    // Thêm chú thích
    html += '<div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd; display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">';
    html += '<div><span style="display: inline-block; width: 20px; height: 20px; background: #6c757d; border-radius: 4px; margin-right: 5px;"></span> Ghế thường</div>';
    html += `<div><span style="display: inline-block; width: 20px; height: 20px; background: #ffc107; border-radius: 4px; margin-right: 5px;"></span> Ghế VIP (${vipRows.join(', ')})</div>`;
    html += `<div><span style="display: inline-block; width: 40px; height: 20px; background: #e91e63; border-radius: 4px; margin-right: 5px;"></span> Ghế đôi (${coupleRow})</div>`;
    html += '</div>';
    
    // Thông tin tổng
    html += `<div style="margin-top: 10px; text-align: center; font-weight: bold; color: #28a745;">`;
    html += `Tổng: <strong>${totalSeats}</strong> ghế | `;
    html += `Thường: <strong>${normalSeats}</strong> | `;
    html += `VIP: <strong>${vipSeats}</strong> | `;
    html += `Đôi: <strong>${coupleSeats / 2}</strong> cặp`;
    html += `</div>`;
    
    preview.innerHTML = html;
}
</script>
