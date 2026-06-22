<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5>Quản lý Lịch chiếu - <?php echo e($theater['name']); ?></h5>
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
    <form method="GET" action="<?php echo e(route('moderator.showtimes.index')); ?>" class="row g-2">
        <div class="col-md-4">
            <input type="date" name="date" class="form-control" value="<?php echo e($date ?? ''); ?>" onchange="this.form.submit()">
        </div>
        <div class="col-md-2">
            <label class="form-label small">Hoặc xem tất cả lịch chiếu</label>
            <a href="<?php echo e(route('moderator.showtimes.index')); ?>?all=1" class="btn btn-outline-info w-100 btn-sm">
                <i class="fas fa-list"></i> Tất cả
            </a>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-secondary w-100"><i class="fas fa-search"></i> Lọc</button>
        </div>
        <div class="col-md-2">
            <a href="<?php echo e(route('moderator.showtimes.index')); ?>" class="btn btn-outline-secondary w-100"><i class="fas fa-redo"></i> Xóa lọc</a>
        </div>
    </form>
</div>

<!-- Showtimes Table -->
<div class="stat-card">
    <div class="table-responsive">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th><th>Phim</th><th>Phòng</th><th>Ngày chiếu</th>
                    <th>Giờ chiếu</th><th>Giá vé</th><th>Trạng thái</th><th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if(empty($showtimes)): ?>
                    <tr><td colspan="8" class="text-center text-muted">Chưa có lịch chiếu nào</td></tr>
                <?php else: ?>
                    <?php $__currentLoopData = $showtimes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $showtime): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($showtime['id']); ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if(isset($showtime['thumbnail']) && $showtime['thumbnail']): ?>
                                    <img src="<?php echo e($showtime['thumbnail']); ?>" alt="" class="rounded me-2" style="width: 50px; height: 75px; object-fit: cover;">
                                <?php endif; ?>
                                <span><?php echo e($showtime['movie_title']); ?></span>
                            </div>
                        </td>
                        <td><?php echo e($showtime['screen_name'] ?? 'Phòng ' . $showtime['screen_id']); ?></td>
                        <td><?php echo e(date('d/m/Y', strtotime($showtime['show_date']))); ?></td>
                        <td><?php echo e(date('H:i', strtotime($showtime['show_time']))); ?></td>
                        <td><?php echo e(number_format($showtime['price'])); ?>₫</td>
                        <td>
                            <?php
                                $showDate = strtotime($showtime['show_date'] . ' ' . $showtime['show_time']);
                                $now = time();
                            ?>
                            <?php if($showDate < $now): ?>
                                <span class="badge bg-secondary">Đã chiếu</span>
                            <?php elseif($showDate - $now < 3600): ?>
                                <span class="badge bg-warning">Sắp chiếu</span>
                            <?php else: ?>
                                <span class="badge bg-success">Sắp tới</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-outline-primary me-1"
                                    data-bs-toggle="modal" data-bs-target="#editShowtimeModal"
                                    data-id="<?php echo e($showtime['id']); ?>"
                                    data-movie-id="<?php echo e($showtime['movie_id']); ?>"
                                    data-screen-id="<?php echo e($showtime['screen_id']); ?>"
                                    data-show-date="<?php echo e($showtime['show_date']); ?>"
                                    data-show-time="<?php echo e(date('H:i', strtotime($showtime['show_time']))); ?>"
                                    data-price="<?php echo e($showtime['price']); ?>">
                                <i class="fas fa-edit"></i> Sửa
                            </button>
                            <a href="?route=moderator/showtimesDelete&id=<?php echo e($showtime['id']); ?>"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Bạn chắc chắn muốn xóa lịch chiếu này?')">
                                <i class="fas fa-trash"></i> Xóa
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                <h5 class="modal-title">Thêm lịch chiếu mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?php echo e(route('moderator.showtimes.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="movie_id" class="form-label">Phim <span class="text-danger">*</span></label>
                        <select name="movie_id" id="movie_id" class="form-select" required>
                            <option value="">Chọn phim</option>
                            <?php $__currentLoopData = $movies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($movie['id']); ?>" data-duration="<?php echo e($movie['duration'] ?? 120); ?>">
                                    <?php echo e($movie['title']); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <div id="movieDurationDisplay" class="mt-2" style="display: none;">
                            <small class="text-muted"><i class="fas fa-clock"></i> Thời lượng: <span id="movieDurationValue"></span> phút</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="screen_id" class="form-label">Phòng <span class="text-danger">*</span></label>
                        <select name="screen_id" id="screen_id" class="form-select" required>
                            <option value="">Chọn phòng</option>
                            <?php $__currentLoopData = $screens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $screen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($screen['id']); ?>">
                                    <?php echo e($screen['screen_name']); ?> (<?php echo e($screen['total_seats']); ?> ghế, <?php echo e($screen['screen_type']); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="show_date" class="form-label">Ngày chiếu <span class="text-danger">*</span></label>
                        <input type="date" name="show_date" id="show_date" class="form-control" required min="<?php echo e(date('Y-m-d')); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="show_time" class="form-label">Giờ chiếu <span class="text-danger">*</span></label>
                        <input type="time" name="show_time" id="show_time" class="form-control" required>
                        <div id="availableTimeSlots" class="mt-2" style="display: none;">
                            <small class="text-muted d-block mb-2">Các khung giờ còn trống trong ngày:</small>
                            <div id="timeSlotsContainer" class="d-flex flex-wrap gap-2"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="price" class="form-label">Giá vé (VNĐ) <span class="text-danger">*</span></label>
                        <input type="number" name="price" id="price" class="form-control" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-primary">Thêm lịch chiếu</button>
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
            <form method="POST" action="<?php echo e(route('moderator.showtimes.update', ['id' => 0])); ?>" id="editShowtimeForm">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>
                <input type="hidden" name="id" id="edit_showtime_id">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_movie_id" class="form-label">Phim <span class="text-danger">*</span></label>
                        <select name="movie_id" id="edit_movie_id" class="form-select" required>
                            <option value="">Chọn phim</option>
                            <?php $__currentLoopData = $movies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $movie): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($movie['id']); ?>"><?php echo e($movie['title']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit_screen_id" class="form-label">Phòng <span class="text-danger">*</span></label>
                        <select name="screen_id" id="edit_screen_id" class="form-select" required>
                            <option value="">Chọn phòng</option>
                            <?php $__currentLoopData = $screens; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $screen): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($screen['id']); ?>">
                                    <?php echo e($screen['screen_name']); ?> (<?php echo e($screen['total_seats']); ?> ghế, <?php echo e($screen['screen_type']); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                <h5 class="modal-title">Thêm phòng mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="<?php echo e(route('moderator.screens.store')); ?>" id="addScreenFormShowtimes">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="screen_name_showtimes" class="form-label">Tên phòng <span class="text-danger">*</span></label>
                            <input type="text" name="screen_name" id="screen_name_showtimes" class="form-control" required placeholder="Ví dụ: Phòng 1, Phòng VIP">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="screen_type_showtimes" class="form-label">Loại phòng <span class="text-danger">*</span></label>
                            <select name="screen_type" id="screen_type_showtimes" class="form-select" required>
                                <option value="2D">2D</option>
                                <option value="3D">3D</option>
                            </select>
                        </div>
                    </div>
                    <hr>
                    <h6 class="mb-3">Cấu hình sơ đồ ghế</h6>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số nhóm ghế <span class="text-danger">*</span></label>
                            <input type="number" name="num_groups" id="num_groups_showtimes" class="form-control" min="1" value="1" required onchange="updateSeatPreviewShowtimes()">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số ghế trên 1 hàng của 1 nhóm <span class="text-danger">*</span></label>
                            <input type="number" name="seats_per_group_row" id="seats_per_group_row_showtimes" class="form-control" min="1" value="12" required onchange="updateSeatPreviewShowtimes()">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số hàng ghế <span class="text-danger">*</span></label>
                            <input type="number" name="num_rows" id="num_rows_showtimes" class="form-control" min="1" value="12" required onchange="updateSeatPreviewShowtimes()">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Số hàng ghế VIP <span class="text-danger">*</span></label>
                            <input type="number" name="num_vip_rows" id="num_vip_rows_showtimes" class="form-control" min="0" value="3" required onchange="updateSeatPreviewShowtimes()">
                        </div>
                    </div>
                    <hr>
                    <h6 class="mb-3">Xem trước sơ đồ ghế</h6>
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
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
#timeSlotsContainer .btn-outline-primary { background-color: #007bff !important; color: #fff !important; border: 2px solid #007bff !important; padding: 0.5rem 1rem !important; font-weight: 600 !important; border-radius: 8px !important; min-width: 80px !important; }
#timeSlotsContainer .btn-primary { background-color: #28a745 !important; color: #fff !important; border: 2px solid #28a745 !important; font-weight: 700 !important; }
#timeSlotsContainer .btn.disabled-slot { opacity: 0.3 !important; cursor: not-allowed !important; pointer-events: none !important; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit showtime modal
    const editShowtimeModal = document.getElementById('editShowtimeModal');
    if (editShowtimeModal) {
        editShowtimeModal.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            editShowtimeModal.querySelector('#edit_showtime_id').value = button.getAttribute('data-id');
            editShowtimeModal.querySelector('#edit_movie_id').value = button.getAttribute('data-movie-id');
            editShowtimeModal.querySelector('#edit_screen_id').value = button.getAttribute('data-screen-id');
            editShowtimeModal.querySelector('#edit_show_date').value = button.getAttribute('data-show-date');
            editShowtimeModal.querySelector('#edit_show_time').value = button.getAttribute('data-show-time');
            editShowtimeModal.querySelector('#edit_price').value = button.getAttribute('data-price');
        });
    }

    // Add showtime modal — available time slots
    const addShowtimeModal = document.getElementById('addShowtimeModal');
    if (addShowtimeModal) {
        const screenSelect = addShowtimeModal.querySelector('#screen_id');
        const dateInput = addShowtimeModal.querySelector('#show_date');
        const timeInput = addShowtimeModal.querySelector('#show_time');
        const availableSlotsDiv = addShowtimeModal.querySelector('#availableTimeSlots');
        const timeSlotsContainer = addShowtimeModal.querySelector('#timeSlotsContainer');
        let movieDuration = 120;
        let timeSlotButtons = [];

        function calculateEndTime(startTime, duration) {
            const [hours, minutes] = startTime.split(':').map(Number);
            const endMinutes = hours * 60 + minutes + duration + 30;
            return `${String(Math.floor(endMinutes / 60)).padStart(2,'0')}:${String(endMinutes % 60).padStart(2,'0')}`;
        }

        function isTimeInRange(time, startTime, endTime) {
            const toMins = t => t.split(':').map(Number).reduce((h, m) => h * 60 + m);
            return toMins(time) > toMins(startTime) && toMins(time) < toMins(endTime);
        }

        function updateTimeSlotsVisibility(selectedTime) {
            if (!selectedTime) {
                timeSlotButtons.forEach(btn => { btn.style.display = ''; btn.disabled = false; btn.classList.remove('disabled-slot'); });
                return;
            }
            const parts = selectedTime.split(':');
            const formattedTime = `${parts[0].padStart(2,'0')}:${parts[1].padStart(2,'0')}`;
            const endTime = calculateEndTime(formattedTime, movieDuration);
            timeSlotButtons.forEach(btn => {
                const slotTime = btn.textContent.trim();
                if (slotTime === formattedTime) { btn.style.display = ''; btn.disabled = false; btn.classList.remove('disabled-slot'); return; }
                if (isTimeInRange(slotTime, formattedTime, endTime)) {
                    btn.style.display = 'none'; btn.disabled = true; btn.classList.add('disabled-slot');
                } else { btn.style.display = ''; btn.disabled = false; btn.classList.remove('disabled-slot'); }
            });
        }

        function loadAvailableTimeSlots() {
            const screenId = screenSelect.value;
            const showDate = dateInput.value;
            const movieId = document.querySelector('#movie_id')?.value;
            
            if (!screenId || !showDate || !movieId) { 
                availableSlotsDiv.style.display = 'none'; 
                return; 
            }
            
            timeSlotsContainer.innerHTML = '<small class="text-muted">Đang tải...</small>';
            availableSlotsDiv.style.display = 'block';
            
            fetch(`<?php echo e(route('moderator.api.availableTimeSlots')); ?>?screen_id=${screenId}&date=${showDate}&movie_id=${movieId}`)
                .then(r => r.json())
                .then(data => {
                    if (data.slots && data.slots.length > 0) {
                        timeSlotsContainer.innerHTML = '';
                        timeSlotButtons = [];
                        
                        // Tạo container cho các slots
                        const slotsWrapper = document.createElement('div');
                        slotsWrapper.className = 'time-slots-wrapper';
                        
                        data.slots.forEach((slot, index) => {
                            const button = document.createElement('button');
                            button.type = 'button';
                            button.className = 'btn btn-sm btn-outline-primary me-2 mb-2 time-slot-btn';
                            button.textContent = slot.label;
                            
                            // Ẩn các button sau 8 cái đầu tiên
                            if (index >= 8) {
                                button.classList.add('d-none', 'extra-slot');
                            }
                            
                            button.onclick = function() {
                                timeInput.value = slot.time;
                                timeSlotsContainer.querySelectorAll('button').forEach(btn => { 
                                    btn.classList.remove('btn-primary'); 
                                    btn.classList.add('btn-outline-primary'); 
                                });
                                button.classList.remove('btn-outline-primary'); 
                                button.classList.add('btn-primary');
                                updateTimeSlotsVisibility(slot.time);
                            };
                            slotsWrapper.appendChild(button);
                            timeSlotButtons.push(button);
                        });
                        
                        timeSlotsContainer.appendChild(slotsWrapper);
                        
                        // Thêm nút "Xem thêm" nếu có nhiều hơn 8 slots
                        if (data.slots.length > 8) {
                            const showMoreBtn = document.createElement('button');
                            showMoreBtn.type = 'button';
                            showMoreBtn.className = 'btn btn-sm btn-link text-decoration-none';
                            showMoreBtn.innerHTML = '<i class="fas fa-angle-down me-1"></i>Xem thêm (' + (data.slots.length - 8) + ' khung giờ)';
                            showMoreBtn.id = 'showMoreSlotsBtn';
                            
                            showMoreBtn.onclick = function() {
                                const extraSlots = timeSlotsContainer.querySelectorAll('.extra-slot');
                                const isExpanded = this.getAttribute('data-expanded') === 'true';
                                
                                if (isExpanded) {
                                    extraSlots.forEach(slot => slot.classList.add('d-none'));
                                    this.innerHTML = '<i class="fas fa-angle-down me-1"></i>Xem thêm (' + extraSlots.length + ' khung giờ)';
                                    this.setAttribute('data-expanded', 'false');
                                } else {
                                    extraSlots.forEach(slot => slot.classList.remove('d-none'));
                                    this.innerHTML = '<i class="fas fa-angle-up me-1"></i>Thu gọn';
                                    this.setAttribute('data-expanded', 'true');
                                }
                            };
                            
                            timeSlotsContainer.appendChild(showMoreBtn);
                        }
                        
                        if (timeInput.value) updateTimeSlotsVisibility(timeInput.value);
                    } else {
                        timeSlotsContainer.innerHTML = '<small class="text-warning">Không còn khung giờ trống trong ngày này</small>';
                    }
                })
                .catch(error => { 
                    console.error('Error loading time slots:', error);
                    timeSlotsContainer.innerHTML = '<small class="text-danger">Có lỗi xảy ra khi tải khung giờ</small>'; 
                });
        }

        const movieSelect = addShowtimeModal.querySelector('#movie_id');
        const movieDurationDisplay = addShowtimeModal.querySelector('#movieDurationDisplay');
        const movieDurationValue = addShowtimeModal.querySelector('#movieDurationValue');
        if (movieSelect) {
            movieSelect.addEventListener('change', function() {
                movieDuration = parseInt(this.options[this.selectedIndex].getAttribute('data-duration')) || 120;
                if (this.value && movieDurationDisplay) { 
                    movieDurationValue.textContent = movieDuration; 
                    movieDurationDisplay.style.display = 'block'; 
                }
                else if (movieDurationDisplay) movieDurationDisplay.style.display = 'none';
                if (timeInput.value) updateTimeSlotsVisibility(timeInput.value);
                // Load available time slots when movie changes
                loadAvailableTimeSlots();
            });
        }

        screenSelect.addEventListener('change', loadAvailableTimeSlots);
        dateInput.addEventListener('change', loadAvailableTimeSlots);
        if (timeInput) {
            timeInput.addEventListener('change', function() { if (this.value) updateTimeSlotsVisibility(this.value); else updateTimeSlotsVisibility(null); });
        }
        addShowtimeModal.addEventListener('hidden.bs.modal', function() {
            availableSlotsDiv.style.display = 'none'; timeSlotsContainer.innerHTML = ''; timeSlotButtons = []; if (timeInput) timeInput.value = '';
        });
    }

    // Seat preview for add screen modal in showtimes page
    const modal = document.getElementById('addScreenModal');
    if (modal) modal.addEventListener('shown.bs.modal', function() { updateSeatPreviewShowtimes(); });
});

function updateSeatPreviewShowtimes() {
    const numGroups = parseInt(document.getElementById('num_groups_showtimes')?.value || 1);
    const seatsPerGroupRow = parseInt(document.getElementById('seats_per_group_row_showtimes')?.value || 12);
    const numRows = parseInt(document.getElementById('num_rows_showtimes')?.value || 12);
    const numVipRows = parseInt(document.getElementById('num_vip_rows_showtimes')?.value || 3);
    const preview = document.getElementById('seatPreviewShowtimes');
    if (!preview) return;
    if (!numGroups || !seatsPerGroupRow || !numRows) { preview.innerHTML = '<p class="text-muted text-center">Nhập thông tin để xem sơ đồ ghế</p>'; return; }
    const rowLetters = ['A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T'];
    const rows = rowLetters.slice(0, numRows);
    const middleStartIndex = Math.floor((numRows - numVipRows) / 2);
    const vipRows = rows.slice(middleStartIndex, middleStartIndex + numVipRows);
    const coupleRow = rows[rows.length - 1];
    let html = '<div style="text-align:center;margin-bottom:15px;"><div style="background:#333;color:white;padding:10px;border-radius:5px;margin-bottom:20px;font-weight:bold;">MÀN HÌNH</div></div>';
    html += '<div style="display:flex;flex-direction:column;gap:3px;align-items:center;">';
    rows.forEach(row => {
        const isVip = vipRows.includes(row), isCouple = row === coupleRow;
        html += `<div style="display:flex;align-items:center;gap:5px;margin-bottom:2px;"><span style="width:30px;text-align:center;font-weight:bold;color:${isVip?'#ffc107':isCouple?'#e91e63':'#333'};font-size:12px;">${row}</span>`;
        let currentCol = 1;
        for (let g = 0; g < numGroups; g++) {
            for (let s = 0; s < seatsPerGroupRow; s++) {
                const bgColor = isVip ? '#ffc107' : isCouple ? '#e91e63' : '#6c757d';
                html += `<span style="display:inline-block;width:25px;height:25px;background:${bgColor};color:white;border-radius:4px;text-align:center;line-height:25px;font-size:10px;margin:1px;">${currentCol}</span>`;
                currentCol++;
            }
            if (g < numGroups - 1) html += '<span style="display:inline-block;width:50px;height:25px;"></span>';
        }
        html += '</div>';
    });
    html += '</div>';
    const totalSeats = numRows * seatsPerGroupRow * numGroups;
    html += `<div style="margin-top:10px;text-align:center;font-weight:bold;color:#28a745;">Tổng số ghế: ${totalSeats} ghế</div>`;
    preview.innerHTML = html;
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.moderator.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\CineHub_DuAnTotNghiep\resources\views/admin/moderator/showtimes.blade.php ENDPATH**/ ?>