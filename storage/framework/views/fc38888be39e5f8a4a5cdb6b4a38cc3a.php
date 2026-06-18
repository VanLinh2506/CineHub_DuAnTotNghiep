<?php
    $title = 'Chọn ghế';
?>

<?php $__env->startSection('content'); ?>
<div class="container">
    <h2 class="mb-4"><?php echo e($title); ?></h2>

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
                <?php if(!empty($seats)): ?>
                    <?php $__currentLoopData = $seats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $seat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <button type="button" class="seat seat-<?php echo e($seat['status']); ?>" 
                                data-seat-id="<?php echo e($seat['id']); ?>"
                                data-seat="<?php echo e($seat['name']); ?>"
                                data-price="<?php echo e($seat['price']); ?>"
                                onclick="toggleSeat(this, '<?php echo e($seat['name']); ?>', <?php echo e($seat['price']); ?>)"
                                <?php if($seat['status'] !== 'available'): ?> disabled <?php endif; ?>>
                            <?php echo e($seat['name']); ?>

                        </button>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <?php endif; ?>
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
                    <p><strong>Phim:</strong> <?php echo e($movie['title']); ?></p>
                    <p><strong>Ngày:</strong> <?php echo e(\Carbon\Carbon::parse($showtime['show_date'])->format('d/m/Y')); ?></p>
                    <p><strong>Giờ:</strong> <?php echo e(\Carbon\Carbon::parse($showtime['show_time'])->format('H:i')); ?></p>
                    <p><strong>Phòng:</strong> <?php echo e($screen['name']); ?></p>
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
                    <span id="pricePerTicket"><?php echo e(number_format($showtime['price'])); ?>₫</span>
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
    const pricePerTicket = <?php echo e($showtime['price'] ?? 0); ?>;
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
        window.location.href = '<?php echo e(url("?route=booking/verify-tickets")); ?>&seats=' + seatIds + '&showtime_id=<?php echo e($showtime['id']); ?>';
    }

    // Initialize summary
    updateSummary();
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\CineHub_DuAnTotNghiep\resources\views\booking\select-seat.blade.php ENDPATH**/ ?>