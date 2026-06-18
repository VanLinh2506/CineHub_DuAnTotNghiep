<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>In vé - CineHub</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Arial', sans-serif; background: #f5f5f5; padding: 20px; }
        .print-container { max-width: 400px; margin: 0 auto; }
        .ticket { background: #fff; border-radius: 15px; overflow: hidden; margin-bottom: 20px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); page-break-inside: avoid; }
        .ticket-header { background: linear-gradient(135deg, #e50914 0%, #b20710 100%); color: #fff; padding: 20px; text-align: center; }
        .ticket-header h1 { font-size: 24px; margin-bottom: 5px; }
        .ticket-header p { font-size: 12px; opacity: 0.9; }
        .ticket-body { padding: 20px; }
        .movie-title { font-size: 20px; font-weight: bold; color: #333; margin-bottom: 15px; text-align: center; }
        .ticket-info { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px; }
        .info-item { text-align: center; }
        .info-item .label { font-size: 11px; color: #888; text-transform: uppercase; margin-bottom: 5px; }
        .info-item .value { font-size: 16px; font-weight: bold; color: #333; }
        .seat-info { background: #f8f8f8; border-radius: 10px; padding: 15px; text-align: center; margin-bottom: 20px; }
        .seat-info .seat-number { font-size: 36px; font-weight: bold; color: #e50914; }
        .seat-info .seat-type { font-size: 12px; color: #666; }
        .qr-code { text-align: center; padding: 15px; border-top: 2px dashed #ddd; }
        .qr-code img { width: 150px; height: 150px; }
        .qr-code p { font-size: 10px; color: #888; margin-top: 10px; }
        .ticket-footer { background: #f8f8f8; padding: 15px; text-align: center; font-size: 11px; color: #666; }
        .price-tag { background: #e50914; color: #fff; padding: 10px 20px; border-radius: 20px; display: inline-block; font-size: 18px; font-weight: bold; margin-bottom: 15px; }
        .customer-info { font-size: 12px; color: #666; text-align: center; margin-bottom: 15px; }
        .print-actions { text-align: center; margin-bottom: 20px; }
        .btn-print { background: #e50914; color: #fff; border: none; padding: 15px 40px; font-size: 16px; border-radius: 5px; cursor: pointer; margin: 5px; }
        .btn-close-custom { background: #666; color: #fff; border: none; padding: 15px 40px; font-size: 16px; border-radius: 5px; cursor: pointer; margin: 5px; }
        @media print {
            body { background: #fff; padding: 0; }
            .print-actions { display: none; }
            .ticket { box-shadow: none; border: 1px solid #ddd; }
        }
    </style>
</head>
<body>
    <div class="print-container">
        <div class="print-actions">
            <button class="btn-print" onclick="window.print()">
                <i class="fas fa-print"></i> In vé
            </button>
            <button class="btn-close-custom" onclick="window.close()">Đóng</button>
        </div>

        <?php $__currentLoopData = $tickets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ticket): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="ticket">
            <div class="ticket-header">
                <h1>🎬 CineHub</h1>
                <p>Vé xem phim điện tử</p>
            </div>
            <div class="ticket-body">
                <div class="movie-title"><?php echo e($ticket['movie_title']); ?></div>
                <div class="customer-info">
                    <strong>Khách hàng:</strong> <?php echo e($booking['customer_name'] ?? 'Khách lẻ'); ?>

                    <?php if(!empty($booking['customer_phone'])): ?>
                    <br>SĐT: <?php echo e($booking['customer_phone']); ?>

                    <?php endif; ?>
                </div>
                <div class="ticket-info">
                    <div class="info-item">
                        <div class="label">Rạp</div>
                        <div class="value"><?php echo e($theater['name']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="label">Phòng chiếu</div>
                        <div class="value"><?php echo e($ticket['screen_name']); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="label">Ngày chiếu</div>
                        <div class="value"><?php echo e(date('d/m/Y', strtotime($ticket['show_date']))); ?></div>
                    </div>
                    <div class="info-item">
                        <div class="label">Giờ chiếu</div>
                        <div class="value"><?php echo e(date('H:i', strtotime($ticket['show_time']))); ?></div>
                    </div>
                </div>
                <div class="seat-info">
                    <div class="seat-number"><?php echo e($ticket['seat']); ?></div>
                    <div class="seat-type">
                        <?php $seatTypes = ['normal' => 'Ghế thường', 'vip' => 'Ghế VIP', 'couple' => 'Ghế đôi']; ?>
                        <?php echo e($seatTypes[$ticket['seat_type']] ?? 'Ghế thường'); ?>

                    </div>
                </div>
                <div style="text-align: center;">
                    <div class="price-tag"><?php echo e(number_format($ticket['price'])); ?> đ</div>
                </div>
                <div class="qr-code">
                    <img src="<?php echo e(route('counterStaff.ticketQR', ['ticket_id' => $ticket['id']])); ?>" alt="QR Code">
                    <p>Mã vé: <?php echo e($ticket['qr_code']); ?></p>
                </div>
            </div>
            <div class="ticket-footer">
                <p>Vui lòng đến trước giờ chiếu 15 phút</p>
                <p>Vé đã mua không hoàn trả</p>
                <p>Ngày bán: <?php echo e(date('d/m/Y H:i')); ?></p>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</body>
</html>
<?php /**PATH D:\laragon\www\CineHub_DuAnTotNghiep\resources\views\admin\counter_staff\print_tickets.blade.php ENDPATH**/ ?>