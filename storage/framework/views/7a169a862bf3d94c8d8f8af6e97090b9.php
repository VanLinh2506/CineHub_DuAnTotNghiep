<?php
    $title = 'Bán vé trực tiếp';
?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h2><i class="fas fa-ticket-alt"></i> <?php echo e($title); ?></h2>
    
    <!-- Chọn suất chiếu -->
    <div class="showtime-selection">
        <div class="form-group mb-3">
            <label>Chọn ngày:</label>
            <input type="date" id="selectDate" value="<?php echo e($date ?? date('Y-m-d')); ?>" 
                   min="<?php echo e(date('Y-m-d')); ?>" class="form-control">
        </div>
        
        <div class="showtimes-grid" id="showtimesGrid">
            <?php if(empty($showtimes)): ?>
                <p class="no-data">Không có suất chiếu nào trong ngày này</p>
            <?php else: ?>
                <?php $__currentLoopData = $showtimes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $st): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="showtime-card <?php echo e(($selected_showtime && $selected_showtime['id'] == $st['id']) ? 'selected' : ''); ?>" 
                         data-showtime-id="<?php echo e($st['id']); ?>"
                         onclick="selectShowtime(<?php echo e($st['id']); ?>)">
                        <div class="movie-info">
                            <img src="<?php echo e($st['thumbnail']); ?>" alt="<?php echo e($st['movie_title']); ?>">
                            <div class="details">
                                <h4><?php echo e($st['movie_title']); ?></h4>
                                <p><i class="fas fa-clock"></i> <?php echo e(\Carbon\Carbon::parse($st['show_time'])->format('H:i')); ?></p>
                                <p><i class="fas fa-tv"></i> <?php echo e($st['screen_name']); ?></p>
                                <p><i class="fas fa-chair"></i> Còn <?php echo e($st['available_seats']); ?> ghế</p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Sơ đồ ghế -->
    <?php if($selected_showtime): ?>
        <div class="seat-selection-section mt-4">
            <h3>Chọn ghế - <?php echo e($selected_showtime['movie_title']); ?> 
                (<?php echo e(\Carbon\Carbon::parse($selected_showtime['show_time'])->format('H:i')); ?>)
            </h3>
            
            <div class="screen-display mb-3">
                <div class="screen-label">MÀN HÌNH</div>
            </div>

            <!-- Seat Grid -->
            <div id="seatGrid" class="seat-grid mb-4">
                <?php if(!empty($seats)): ?>
                    <?php $__currentLoopData = $seats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $seat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="seat <?php echo e($seat['status'] ?? 'available'); ?>" 
                             data-seat="<?php echo e($seat['name']); ?>"
                             onclick="toggleSeat(this, '<?php echo e($seat['name']); ?>')">
                            <?php echo e($seat['name']); ?>

                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
            </div>

            <!-- Selected Seats & Price -->
            <div class="cart-section">
                <h4>Ghế được chọn:</h4>
                <div id="selectedSeats" class="mb-3">
                    <p class="text-muted">Chọn ghế để thêm vào giỏ</p>
                </div>
                
                <div class="price-summary mb-3">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Tổng vé:</label>
                            <input type="number" id="ticketCount" class="form-control" value="0" readonly>
                        </div>
                        <div class="col-md-6">
                            <label>Tổng tiền:</label>
                            <input type="text" id="totalPrice" class="form-control" value="0₫" readonly>
                        </div>
                    </div>
                </div>

                <button type="button" class="btn btn-primary btn-lg w-100" onclick="completeBooking()">
                    <i class="fas fa-credit-card"></i> Thanh toán
                </button>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
    .showtime-card {
        border: 2px solid #ddd;
        border-radius: 8px;
        padding: 15px;
        margin: 10px 0;
        cursor: pointer;
        transition: all 0.3s;
    }

    .showtime-card.selected,
    .showtime-card:hover {
        border-color: #e50914;
        background-color: rgba(229, 9, 20, 0.1);
    }

    .movie-info {
        display: flex;
        gap: 15px;
    }

    .movie-info img {
        width: 80px;
        height: 120px;
        object-fit: cover;
        border-radius: 4px;
    }

    .details {
        flex: 1;
    }

    .screen-display {
        text-align: center;
        padding: 15px;
        background: #f0f0f0;
        border-radius: 8px;
    }

    .screen-label {
        background: #333;
        color: white;
        padding: 8px 15px;
        display: inline-block;
        border-radius: 4px;
    }

    .seat-grid {
        display: grid;
        grid-template-columns: repeat(12, 1fr);
        gap: 8px;
    }

    .seat {
        width: 100%;
        aspect-ratio: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid #ddd;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        background: white;
        transition: all 0.3s;
    }

    .seat.available:hover {
        background: #e50914;
        color: white;
        border-color: #e50914;
    }

    .seat.sold {
        background: #ccc;
        cursor: not-allowed;
    }

    .seat.selected {
        background: #e50914;
        color: white;
        border-color: #e50914;
    }
</style>

<script>
    function selectShowtime(showtimeId) {
        window.location.href = '<?php echo e(url("?route=counter_staff/sellTicket&showtime_id=")); ?>' + showtimeId;
    }

    function toggleSeat(element, seatName) {
        if (element.classList.contains('sold')) return;
        element.classList.toggle('selected');
        updateSelectedSeats();
    }

    function updateSelectedSeats() {
        const selected = document.querySelectorAll('.seat.selected');
        const seatNames = Array.from(selected).map(s => s.dataset.seat).join(', ');
        document.getElementById('selectedSeats').innerHTML = seatNames ? `<strong>${seatNames}</strong>` : '<p class="text-muted">Chọn ghế để thêm vào giỏ</p>';
        document.getElementById('ticketCount').value = selected.length;
        // Update price based on selected seats
        const totalPrice = selected.length * 100000; // Default price
        document.getElementById('totalPrice').value = totalPrice.toLocaleString('vi-VN') + '₫';
    }

    function completeBooking() {
        const selected = document.querySelectorAll('.seat.selected');
        if (selected.length === 0) {
            alert('Vui lòng chọn ít nhất một ghế');
            return;
        }
        // Submit booking
        alert('Hoàn thành đặt vé cho ' + selected.length + ' ghế');
    }

    document.getElementById('selectDate')?.addEventListener('change', function(e) {
        window.location.href = '<?php echo e(url("?route=counter_staff/sellTicket&date=")); ?>' + e.target.value;
    });
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\CineHub_DuAnTotNghiep\resources\views\counter_staff\sell_ticket.blade.php ENDPATH**/ ?>