    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5>Quản lý Lịch chiếu - <?php echo htmlspecialchars($theater['name']); ?></h5>
        <div>
            <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#addScreenModal">
                <i class="fas fa-plus"></i> Thêm phòng
            </button>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addShowtimeModal">
                <i class="fas fa-plus"></i> Thêm lịch chiếu
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="stat-card mb-3">
        <form method="GET" class="row g-2">
            <input type="hidden" name="route" value="moderator/showtimes">
            <div class="col-md-4">
                <input type="date" name="date" class="form-control" value="<?php echo htmlspecialchars($date ?? date('Y-m-d')); ?>" onchange="this.form.submit()">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Hoặc xem tất cả (7 ngày tới)</label>
                <a href="?route=moderator/showtimes&date=" class="btn btn-outline-info w-100 btn-sm">
                    <i class="fas fa-list"></i> Tất cả
                </a>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-secondary w-100">
                    <i class="fas fa-search"></i> Lọc
                </button>
            </div>
            <div class="col-md-2">
                <a href="?route=moderator/showtimes" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-redo"></i> Xóa lọc
                </a>
            </div>
        </form>
    </div>

    <!-- Showtimes Table -->
    <div class="stat-card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Phim</th>
                        <th>Phòng</th>
                        <th>Ngày chiếu</th>
                        <th>Giờ chiếu</th>
                        <th>Giá vé</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($showtimes)): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted">Chưa có lịch chiếu nào</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($showtimes as $showtime): ?>
                            <tr>
                                <td><?php echo $showtime['id']; ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (isset($showtime['thumbnail']) && $showtime['thumbnail']): ?>
                                            <img src="<?php echo htmlspecialchars($showtime['thumbnail']); ?>" alt="" class="rounded me-2" style="width: 50px; height: 75px; object-fit: cover;">
                                        <?php endif; ?>
                                        <span><?php echo htmlspecialchars($showtime['movie_title']); ?></span>
                                    </div>
                                </td>
                                <td><?php echo htmlspecialchars($showtime['screen_name'] ?? 'Phòng ' . $showtime['screen_id']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($showtime['show_date'])); ?></td>
                                <td><?php echo date('H:i', strtotime($showtime['show_time'])); ?></td>
                                <td><?php echo number_format($showtime['price']); ?>₫</td>
                                <td>
                                    <?php
                                    $showDate = strtotime($showtime['show_date'] . ' ' . $showtime['show_time']);
                                    $now = time();
                                    if ($showDate < $now) {
                                        echo '<span class="badge bg-secondary">Đã chiếu</span>';
                                    } elseif ($showDate - $now < 3600) {
                                        echo '<span class="badge bg-warning">Sắp chiếu</span>';
                                    } else {
                                        echo '<span class="badge bg-success">Sắp tới</span>';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editShowtimeModal"
                                            data-id="<?php echo $showtime['id']; ?>"
                                            data-movie-id="<?php echo $showtime['movie_id']; ?>"
                                            data-screen-id="<?php echo $showtime['screen_id']; ?>"
                                            data-show-date="<?php echo $showtime['show_date']; ?>"
                                            data-show-time="<?php echo date('H:i', strtotime($showtime['show_time'])); ?>"
                                            data-price="<?php echo $showtime['price']; ?>">
                                        <i class="fas fa-edit"></i> Sửa
                                    </button>
                                    <a href="?route=moderator/showtimesDelete&id=<?php echo $showtime['id']; ?>" 
                                    class="btn btn-sm btn-outline-danger" 
                                    onclick="return confirm('Bạn chắc chắn muốn xóa lịch chiếu này?')">
                                        <i class="fas fa-trash"></i> Xóa
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Add Showtime Modal -->
    <div class="modal fade" id="addShowtimeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="color: black;">Thêm lịch chiếu mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="?route=moderator/showtimesStore">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="movie_id" class="form-label" style="color: black;">Phim <span class="text-danger">*</span></label>
                            <select name="movie_id" id="movie_id" class="form-select" required>
                                <option value="">Chọn phim</option>
                                <?php foreach ($movies as $movie): ?>
                                    <option value="<?php echo $movie['id']; ?>" data-duration="<?php echo $movie['duration'] ?? 120; ?>">
                                        <?php echo htmlspecialchars($movie['title']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div id="movieDurationDisplay" class="mt-2" style="display: none;">
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> Thời lượng: <span id="movieDurationValue"></span> phút
                                </small>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="screen_id" class="form-label" style="color: black;">Phòng <span class="text-danger">*</span></label>
                            <select name="screen_id" id="screen_id" class="form-select" required>
                                <option value="">Chọn phòng</option>
                                <?php foreach ($screens as $screen): ?>
                                    <option value="<?php echo $screen['id']; ?>">
                                        <?php echo htmlspecialchars($screen['screen_name']); ?> 
                                        (<?php echo $screen['total_seats']; ?> ghế, <?php echo htmlspecialchars($screen['screen_type']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="show_date" class="form-label" style="color: black;">Ngày chiếu <span class="text-danger">*</span></label>
                            <input type="date" name="show_date" id="show_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="show_time" class="form-label" style="color: black;">Giờ chiếu <span class="text-danger">*</span></label>
                            <input type="time" name="show_time" id="show_time" class="form-control" required>
                            <div id="availableTimeSlots" class="mt-2" style="display: none;">
                                <small class="text-muted d-block mb-2">Các khung giờ còn trống trong ngày:</small>
                                <div id="timeSlotsContainer" class="d-flex flex-wrap gap-2"></div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="price" class="form-label" style="color: black;">Giá vé (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="price" id="price" class="form-control" min="0" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary" >Thêm lịch chiếu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Showtime Modal -->
    <div class="modal fade" id="editShowtimeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa lịch chiếu</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="?route=moderator/showtimesUpdate">
                    <input type="hidden" name="id" id="edit_showtime_id">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="edit_movie_id" class="form-label">Phim <span class="text-danger">*</span></label>
                            <select name="movie_id" id="edit_movie_id" class="form-select" required>
                                <option value="">Chọn phim</option>
                                <?php foreach ($movies as $movie): ?>
                                    <option value="<?php echo $movie['id']; ?>"><?php echo htmlspecialchars($movie['title']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_screen_id" class="form-label">Phòng <span class="text-danger">*</span></label>
                            <select name="screen_id" id="edit_screen_id" class="form-select" required>
                                <option value="">Chọn phòng</option>
                                <?php foreach ($screens as $screen): ?>
                                    <option value="<?php echo $screen['id']; ?>">
                                        <?php echo htmlspecialchars($screen['screen_name']); ?> 
                                        (<?php echo $screen['total_seats']; ?> ghế, <?php echo htmlspecialchars($screen['screen_type']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit_show_date" class="form-label">Ngày chiếu <span class="text-danger">*</span></label>
                            <input type="date" name="show_date" id="edit_show_date" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_show_time" class="form-label">Giờ chiếu <span class="text-danger">*</span></label>
                            <input type="time" name="show_time" id="edit_show_time" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_price" class="form-label">Giá vé (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="price" id="edit_price" class="form-control" min="0" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Screen Modal -->
    <div class="modal fade" id="addScreenModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" style="color: black;">Thêm phòng mới</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" action="?route=moderator/screensStore" id="addScreenFormShowtimes">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="screen_name_showtimes" class="form-label" style="color: black;">Tên phòng <span class="text-danger">*</span></label>
                                <input type="text" name="screen_name" id="screen_name_showtimes" class="form-control" required placeholder="Ví dụ: Phòng 1, Phòng VIP">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="screen_type_showtimes" class="form-label" style="color: black;">Loại phòng <span class="text-danger">*</span></label>
                                <select name="screen_type" id="screen_type_showtimes" class="form-select" required>
                                    <option value="2D">2D</option>
                                    <option value="3D">3D</option>
                                </select>
                            </div>
                        </div>
                        
                        <hr>
                        <h6 class="mb-3" style="color: black;">Cấu hình sơ đồ ghế</h6>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="num_groups_showtimes" class="form-label" style="color: black;">Số nhóm ghế <span class="text-danger">*</span></label>
                                <input type="number" name="num_groups" id="num_groups_showtimes" class="form-control" min="1" value="1" required onchange="updateSeatPreviewShowtimes()">
                                <small class="text-muted">Số nhóm ghế trong phòng (thường là 1 hoặc 2 nhóm)</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="seats_per_group_row_showtimes" class="form-label" style="color: black;">Số ghế trên 1 hàng của 1 nhóm <span class="text-danger">*</span></label>
                                <input type="number" name="seats_per_group_row" id="seats_per_group_row_showtimes" class="form-control" min="1" value="12" required onchange="updateSeatPreviewShowtimes()">
                                <small class="text-muted">Số ghế trong mỗi hàng của một nhóm</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="num_rows_showtimes" class="form-label" style="color: black;">Số hàng ghế <span class="text-danger">*</span></label>
                                <input type="number" name="num_rows" id="num_rows_showtimes" class="form-control" min="1" value="12" required onchange="updateSeatPreviewShowtimes()">
                                <small class="text-muted">Tổng số hàng ghế trong phòng</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="num_vip_rows_showtimes" class="form-label" style="color: black;">Số hàng ghế VIP <span class="text-danger">*</span></label>
                                <input type="number" name="num_vip_rows" id="num_vip_rows_showtimes" class="form-control" min="0" value="3" required onchange="updateSeatPreviewShowtimes()">
                                <small class="text-muted">Số hàng ghế VIP (từ hàng giữa)</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="normal_price_showtimes" class="form-label" style="color: black;">Giá ghế thường (VNĐ)</label>
                                <input type="number" name="normal_price" id="normal_price_showtimes" class="form-control" min="0" value="120000" step="1000">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="vip_price_showtimes" class="form-label" style="color: black;">Giá ghế VIP (VNĐ)</label>
                                <input type="number" name="vip_price" id="vip_price_showtimes" class="form-control" min="0" value="180000" step="1000">
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="couple_price_showtimes" class="form-label" style="color: black;">Giá ghế đôi (VNĐ)</label>
                                <input type="number" name="couple_price" id="couple_price_showtimes" class="form-control" min="0" value="240000" step="1000">
                            </div>
                        </div>
                        
                        <hr>
                        <h6 class="mb-3" style="color: black;">Xem trước sơ đồ ghế</h6>
                        <div id="seatPreviewShowtimes" class="border rounded p-3" style="background: #f8f9fa; min-height: 200px; max-height: 400px; overflow: auto;">
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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Xử lý modal sửa lịch chiếu
        const editShowtimeModal = document.getElementById('editShowtimeModal');
        if (editShowtimeModal) {
            editShowtimeModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const id = button.getAttribute('data-id');
                const movieId = button.getAttribute('data-movie-id');
                const screenId = button.getAttribute('data-screen-id');
                const showDate = button.getAttribute('data-show-date');
                const showTime = button.getAttribute('data-show-time');
                const price = button.getAttribute('data-price');
                
                editShowtimeModal.querySelector('#edit_showtime_id').value = id;
                editShowtimeModal.querySelector('#edit_movie_id').value = movieId;
                editShowtimeModal.querySelector('#edit_screen_id').value = screenId;
                editShowtimeModal.querySelector('#edit_show_date').value = showDate;
                editShowtimeModal.querySelector('#edit_show_time').value = showTime;
                editShowtimeModal.querySelector('#edit_price').value = price;
            });
        }
        
        // Xử lý hiển thị khung giờ còn trống khi chọn ngày và phòng
        const addShowtimeModal = document.getElementById('addShowtimeModal');
        if (addShowtimeModal) {
            const screenSelect = addShowtimeModal.querySelector('#screen_id');
            const dateInput = addShowtimeModal.querySelector('#show_date');
            const timeInput = addShowtimeModal.querySelector('#show_time');
            const availableSlotsDiv = addShowtimeModal.querySelector('#availableTimeSlots');
            const timeSlotsContainer = addShowtimeModal.querySelector('#timeSlotsContainer');
            
            // Lưu thời lượng phim và các nút khung giờ
            let movieDuration = 120; // Mặc định 120 phút
            let timeSlotButtons = [];
            
            // Hàm tính thời gian kết thúc (giờ bắt đầu + thời lượng + buffer 30 phút)
            function calculateEndTime(startTime, duration) {
                const [hours, minutes] = startTime.split(':').map(Number);
                const startMinutes = hours * 60 + minutes;
                const endMinutes = startMinutes + duration + 30; // +30 phút buffer
                const endHours = Math.floor(endMinutes / 60);
                const endMins = endMinutes % 60;
                return `${String(endHours).padStart(2, '0')}:${String(endMins).padStart(2, '0')}`;
            }
            
            // Hàm kiểm tra xem một khung giờ có nằm trong khoảng thời gian không
            function isTimeInRange(time, startTime, endTime) {
                const timeMinutes = time.split(':').map(Number).reduce((h, m) => h * 60 + m);
                const startMinutes = startTime.split(':').map(Number).reduce((h, m) => h * 60 + m);
                const endMinutes = endTime.split(':').map(Number).reduce((h, m) => h * 60 + m);
                // Không bao gồm startTime, nhưng bao gồm tất cả các giờ sau đó cho đến endTime
                return timeMinutes > startMinutes && timeMinutes < endMinutes;
            }
            
            // Hàm cập nhật trạng thái các nút khung giờ dựa trên giờ chiếu đã chọn
            function updateTimeSlotsVisibility(selectedTime) {
                if (!selectedTime || !movieDuration || timeSlotButtons.length === 0) {
                    // Nếu chưa chọn giờ, chưa có thời lượng phim, hoặc chưa có nút nào, hiển thị tất cả
                    timeSlotButtons.forEach(btn => {
                        btn.style.display = '';
                        btn.disabled = false;
                        btn.classList.remove('disabled-slot');
                    });
                    return;
                }
                
                // Chuyển đổi selectedTime sang format HH:MM nếu cần
                let formattedTime = selectedTime;
                if (selectedTime.includes(':')) {
                    const parts = selectedTime.split(':');
                    formattedTime = `${parts[0].padStart(2, '0')}:${parts[1].padStart(2, '0')}`;
                }
                
                // Tính thời gian kết thúc
                const endTime = calculateEndTime(formattedTime, movieDuration);
                
                console.log('Updating time slots visibility:', {
                    selectedTime: formattedTime,
                    movieDuration: movieDuration,
                    endTime: endTime,
                    totalButtons: timeSlotButtons.length
                });
                
                // Cập nhật trạng thái các nút
                timeSlotButtons.forEach(btn => {
                    const slotTime = btn.textContent.trim();
                    // Không ẩn chính giờ được chọn
                    if (slotTime === formattedTime) {
                        btn.style.display = '';
                        btn.disabled = false;
                        btn.classList.remove('disabled-slot');
                        return;
                    }
                    
                    // Kiểm tra xem khung giờ này có nằm trong khoảng thời gian bị trùng không
                    if (isTimeInRange(slotTime, formattedTime, endTime)) {
                        // Ẩn các khung giờ bị trùng
                        btn.style.display = 'none';
                        btn.disabled = true;
                        btn.classList.add('disabled-slot');
                        console.log('Hiding slot:', slotTime, 'because it conflicts with', formattedTime, '-', endTime);
                    } else {
                        btn.style.display = '';
                        btn.disabled = false;
                        btn.classList.remove('disabled-slot');
                    }
                });
            }
            
            function loadAvailableTimeSlots() {
                const screenId = screenSelect.value;
                const showDate = dateInput.value;
                
                if (!screenId || !showDate) {
                    availableSlotsDiv.style.display = 'none';
                    return;
                }
                
                // Hiển thị loading
                timeSlotsContainer.innerHTML = '<small class="text-muted">Đang tải...</small>';
                availableSlotsDiv.style.display = 'block';
                
                // Gọi API để lấy các khung giờ còn trống
                fetch(`?route=moderator/getAvailableTimeSlots&screen_id=${screenId}&show_date=${showDate}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.available_slots.length > 0) {
                            timeSlotsContainer.innerHTML = '';
                            timeSlotButtons = []; // Reset mảng
                            
                            data.available_slots.forEach(slot => {
                                const button = document.createElement('button');
                                button.type = 'button';
                                button.className = 'btn btn-sm btn-outline-primary';
                                button.textContent = slot;
                                button.onclick = function() {
                                    timeInput.value = slot;
                                    // Highlight button được chọn
                                    timeSlotsContainer.querySelectorAll('button').forEach(btn => {
                                        btn.classList.remove('btn-primary');
                                        btn.classList.add('btn-outline-primary');
                                    });
                                    button.classList.remove('btn-outline-primary');
                                    button.classList.add('btn-primary');
                                    
                                    // Cập nhật trạng thái các khung giờ khác
                                    updateTimeSlotsVisibility(slot);
                                };
                                timeSlotsContainer.appendChild(button);
                                timeSlotButtons.push(button);
                            });
                            
                            // Nếu đã có giờ chiếu được chọn, cập nhật lại
                            if (timeInput.value) {
                                updateTimeSlotsVisibility(timeInput.value);
                            }
                        } else {
                            timeSlotsContainer.innerHTML = '<small class="text-warning">Không còn khung giờ trống trong ngày này</small>';
                        }
                    })
                    .catch(error => {
                        console.error('Error loading available time slots:', error);
                        timeSlotsContainer.innerHTML = '<small class="text-danger">Có lỗi xảy ra khi tải khung giờ</small>';
                    });
            }
            
            // Lắng nghe sự kiện thay đổi phim để lấy thời lượng
            const movieSelect = addShowtimeModal.querySelector('#movie_id');
            const movieDurationDisplay = addShowtimeModal.querySelector('#movieDurationDisplay');
            const movieDurationValue = addShowtimeModal.querySelector('#movieDurationValue');
            
            if (movieSelect) {
                movieSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    movieDuration = parseInt(selectedOption.getAttribute('data-duration')) || 120;
                    
                    // Hiển thị thời lượng phim
                    if (this.value && movieDurationDisplay && movieDurationValue) {
                        movieDurationValue.textContent = movieDuration;
                        movieDurationDisplay.style.display = 'block';
                    } else if (movieDurationDisplay) {
                        movieDurationDisplay.style.display = 'none';
                    }
                    
                    // Nếu đã có giờ chiếu được chọn, cập nhật lại
                    if (timeInput.value) {
                        updateTimeSlotsVisibility(timeInput.value);
                    }
                });
            }
            
            // Lắng nghe sự kiện thay đổi phòng và ngày
            screenSelect.addEventListener('change', loadAvailableTimeSlots);
            dateInput.addEventListener('change', loadAvailableTimeSlots);
            
            // Lắng nghe sự kiện thay đổi giờ chiếu (khi nhập trực tiếp vào input)
            if (timeInput) {
                timeInput.addEventListener('change', function() {
                    const selectedTime = this.value;
                    if (selectedTime) {
                        // Chuyển đổi format từ HH:MM sang HH:MM (có thể cần format)
                        let formattedTime = selectedTime;
                        if (selectedTime.match(/^\d{1,2}:\d{2}$/)) {
                            const parts = selectedTime.split(':');
                            formattedTime = `${parts[0].padStart(2, '0')}:${parts[1]}`;
                        }
                        
                        // Cập nhật trạng thái các khung giờ
                        updateTimeSlotsVisibility(formattedTime);
                        
                        // Highlight button tương ứng nếu có
                        timeSlotButtons.forEach(btn => {
                            const slotTime = btn.textContent.trim();
                            if (slotTime === formattedTime) {
                                btn.classList.remove('btn-outline-primary');
                                btn.classList.add('btn-primary');
                            } else {
                                btn.classList.remove('btn-primary');
                                btn.classList.add('btn-outline-primary');
                            }
                        });
                    } else {
                        // Nếu xóa giờ, hiển thị lại tất cả
                        updateTimeSlotsVisibility(null);
                    }
                });
                
                timeInput.addEventListener('input', function() {
                    // Kiểm tra real-time khi đang nhập
                    const selectedTime = this.value;
                    if (selectedTime && selectedTime.match(/^\d{1,2}:\d{2}$/)) {
                        const parts = selectedTime.split(':');
                        const formattedTime = `${parts[0].padStart(2, '0')}:${parts[1]}`;
                        updateTimeSlotsVisibility(formattedTime);
                    }
                });
            }
            
            // Reset khi đóng modal
            addShowtimeModal.addEventListener('hidden.bs.modal', function() {
                availableSlotsDiv.style.display = 'none';
                timeSlotsContainer.innerHTML = '';
                timeSlotButtons = [];
                if (timeInput) timeInput.value = '';
            });
        }
    });
    
    // Function để cập nhật preview sơ đồ ghế cho form trong showtimes
    function updateSeatPreviewShowtimes() {
        const numGroups = parseInt(document.getElementById('num_groups_showtimes')?.value || 1);
        const seatsPerGroupRow = parseInt(document.getElementById('seats_per_group_row_showtimes')?.value || 12);
        const numRows = parseInt(document.getElementById('num_rows_showtimes')?.value || 12);
        const numVipRows = parseInt(document.getElementById('num_vip_rows_showtimes')?.value || 3);
        
        const preview = document.getElementById('seatPreviewShowtimes');
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
        
        // Tính số cột tổng (nếu có nhiều nhóm, có khoảng cách giữa các nhóm)
        const gapBetweenGroups = 2; // Khoảng cách giữa các nhóm (2 cột)
        const totalCols = numGroups * seatsPerGroupRow + (numGroups - 1) * gapBetweenGroups;
        const cols = Array.from({length: totalCols}, (_, i) => i + 1);
        
        // Tạo HTML preview
        let html = '<div style="text-align: center; margin-bottom: 15px;">';
        html += '<div style="background: #333; color: white; padding: 10px; border-radius: 5px; margin-bottom: 20px; font-weight: bold;">MÀN HÌNH</div>';
        html += '</div>';
        
        html += '<div style="display: flex; flex-direction: column; gap: 3px; align-items: center;">';
        
        rows.forEach((row, rowIndex) => {
            const isVip = vipRows.includes(row);
            const isCouple = row === coupleRow;
            
            html += '<div style="display: flex; align-items: center; gap: 5px; margin-bottom: 2px;">';
            html += `<span style="width: 30px; text-align: center; font-weight: bold; color: ${isVip ? '#ffc107' : isCouple ? '#e91e63' : '#333'}; font-size: 12px;">${row}</span>`;
            
            // Render các nhóm ghế
            let currentCol = 1;
            for (let g = 0; g < numGroups; g++) {
                // Render ghế trong nhóm này
                for (let s = 0; s < seatsPerGroupRow; s++) {
                    const seatClass = isVip ? 'vip' : isCouple ? 'couple' : 'normal';
                    const bgColor = isVip ? '#ffc107' : isCouple ? '#e91e63' : '#6c757d';
                    html += `<span style="display: inline-block; width: 25px; height: 25px; background: ${bgColor}; color: white; border-radius: 4px; text-align: center; line-height: 25px; font-size: 10px; margin: 1px;">${currentCol}</span>`;
                    currentCol++;
                }
                
                // Khoảng cách giữa các nhóm (trừ nhóm cuối)
                if (g < numGroups - 1) {
                    for (let gap = 0; gap < gapBetweenGroups; gap++) {
                        html += '<span style="display: inline-block; width: 25px; height: 25px;"></span>';
                    }
                }
            }
            
            html += '</div>';
        });
        
        html += '</div>';
        
        // Thêm chú thích
        html += '<div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #ddd; display: flex; justify-content: center; gap: 20px; flex-wrap: wrap;">';
        html += '<div><span style="display: inline-block; width: 20px; height: 20px; background: #6c757d; border-radius: 4px; margin-right: 5px;"></span> Ghế thường</div>';
        html += `<div><span style="display: inline-block; width: 20px; height: 20px; background: #ffc107; border-radius: 4px; margin-right: 5px;"></span> Ghế VIP (${vipRows.join(', ')})</div>`;
        html += `<div><span style="display: inline-block; width: 20px; height: 20px; background: #e91e63; border-radius: 4px; margin-right: 5px;"></span> Ghế đôi (${coupleRow})</div>`;
        html += '</div>';
        
        // Thông tin tổng
        const totalSeats = numRows * seatsPerGroupRow * numGroups;
        html += `<div style="margin-top: 10px; text-align: center; font-weight: bold; color: #28a745;">Tổng số ghế: ${totalSeats} ghế</div>`;
        
        preview.innerHTML = html;
    }
    
    // Gọi updateSeatPreviewShowtimes khi modal mở
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('addScreenModal');
        if (modal) {
            modal.addEventListener('shown.bs.modal', function() {
                updateSeatPreviewShowtimes();
            });
        }
    });
    </script>
    
    <style>
    /* CSS cho các nút khung giờ còn trống */
    #timeSlotsContainer .btn-outline-primary {
        background-color: #007bff !important;
        color: #ffffff !important;
        border: 2px solid #007bff !important;
        padding: 0.5rem 1rem !important;
        font-weight: 600 !important;
        border-radius: 8px !important;
        transition: all 0.3s ease !important;
        min-width: 80px !important;
    }
    
    #timeSlotsContainer .btn-outline-primary:hover {
        background-color: #0056b3 !important;
        border-color: #0056b3 !important;
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3) !important;
    }
    
    #timeSlotsContainer .btn-primary {
        background-color: #28a745 !important;
        color: #ffffff !important;
        border: 2px solid #28a745 !important;
        padding: 0.5rem 1rem !important;
        font-weight: 700 !important;
        border-radius: 8px !important;
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4) !important;
        transform: scale(1.05) !important;
    }
    
    #timeSlotsContainer .btn-primary:hover {
        background-color: #218838 !important;
        border-color: #218838 !important;
    }
    
    /* CSS cho các nút bị vô hiệu hóa (bị trùng với phim đang chọn) */
    #timeSlotsContainer .btn.disabled-slot {
        opacity: 0.3 !important;
        cursor: not-allowed !important;
        pointer-events: none !important;
    }
    </style>
