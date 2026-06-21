<?php $__env->startSection('content'); ?>
<div class="stat-card">
    <h5 class="mb-4"><i class="fas fa-qrcode"></i> Quét QR Code vé</h5>

    <div class="row">
        <div class="col-md-6">
            <div class="mb-4">
                <label class="form-label">Quét QR Code từ điện thoại khách hàng</label>
                <div id="reader" style="width: 100%; max-width: 500px; margin: 0 auto;"></div>
                <div class="mt-3 text-center">
                    <button class="btn btn-primary" id="startScanBtn">
                        <i class="fas fa-camera"></i> Bắt đầu quét
                    </button>
                    <button class="btn btn-secondary" id="stopScanBtn" style="display: none;">
                        <i class="fas fa-stop"></i> Dừng quét
                    </button>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label">Hoặc nhập thủ công</label>
                <div class="input-group">
                    <input type="text" class="form-control" id="manualCode" placeholder="Nhập booking code hoặc booking ID">
                    <button class="btn btn-primary" id="manualVerifyBtn">
                        <i class="fas fa-search"></i> Xác nhận
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div id="ticketInfo" style="display: none;">
                <h6 class="mb-3">Thông tin vé</h6>
                <div id="ticketDetails"></div>
                <div class="mt-3">
                    <button class="btn btn-success" id="confirmPickupBtn">
                        <i class="fas fa-check"></i> Xác nhận đã lấy vé
                    </button>
                </div>
            </div>
            <div id="scanResult" class="mt-3"></div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
let html5QrCode = null;
let isScanning = false;

document.getElementById('startScanBtn').addEventListener('click', function() {
    if (!html5QrCode) html5QrCode = new Html5Qrcode("reader");
    html5QrCode.start({ facingMode: "environment" }, { fps: 10, qrbox: { width: 250, height: 250 } }, onScanSuccess, onScanError)
        .then(() => {
            isScanning = true;
            document.getElementById('startScanBtn').style.display = 'none';
            document.getElementById('stopScanBtn').style.display = 'inline-block';
        }).catch(err => { alert('Không thể khởi động camera. Vui lòng kiểm tra quyền truy cập camera.'); });
});

document.getElementById('stopScanBtn').addEventListener('click', function() {
    if (html5QrCode && isScanning) {
        html5QrCode.stop().then(() => {
            isScanning = false;
            document.getElementById('startScanBtn').style.display = 'inline-block';
            document.getElementById('stopScanBtn').style.display = 'none';
        });
    }
});

function onScanSuccess(decodedText) {
    if (html5QrCode && isScanning) {
        html5QrCode.stop().then(() => {
            isScanning = false;
            document.getElementById('startScanBtn').style.display = 'inline-block';
            document.getElementById('stopScanBtn').style.display = 'none';
        });
    }
    verifyTicket(decodedText);
}
function onScanError(errorMessage) {}

document.getElementById('manualVerifyBtn').addEventListener('click', function() {
    const code = document.getElementById('manualCode').value.trim();
    if (code) { verifyTicket(code); } else { alert('Vui lòng nhập booking code hoặc booking ID'); }
});
document.getElementById('manualCode').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') document.getElementById('manualVerifyBtn').click();
});

let currentBookingId = null;

function verifyTicket(code) {
    let booking_id = null, booking_code = null;
    if (code.includes('booking_id=')) {
        const urlParams = new URLSearchParams(code.split('?')[1]);
        booking_id = urlParams.get('booking_id');
        booking_code = urlParams.get('booking_code');
    } else if (code.match(/^\d+$/)) {
        booking_id = code;
    } else {
        booking_code = code;
    }
    document.getElementById('scanResult').innerHTML = '<div class="alert alert-info"><i class="fas fa-spinner fa-spin"></i> Đang xử lý...</div>';
    document.getElementById('ticketInfo').style.display = 'none';
    fetch('?route=counterStaff/verifyTicket', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ booking_id: booking_id || '', booking_code: booking_code || '' })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            currentBookingId = data.booking.id;
            displayTicketInfo(data);
            document.getElementById('scanResult').innerHTML = '<div class="alert alert-success"><i class="fas fa-check-circle"></i> ' + data.message + '</div>';
        } else {
            document.getElementById('scanResult').innerHTML = '<div class="alert alert-danger"><i class="fas fa-times-circle"></i> ' + data.message + '</div>';
            document.getElementById('ticketInfo').style.display = 'none';
        }
    })
    .catch(() => {
        document.getElementById('scanResult').innerHTML = '<div class="alert alert-danger"><i class="fas fa-times-circle"></i> Có lỗi xảy ra khi xử lý</div>';
    });
}

function displayTicketInfo(data) {
    const booking = data.booking, tickets = data.tickets;
    let html = `<div class="card mb-3"><div class="card-header bg-primary text-white"><strong>Booking Code: ${booking.booking_code}</strong></div><div class="card-body">
        <p><strong>Khách hàng:</strong> ${tickets[0].user_name}</p>
        <p><strong>Phim:</strong> ${tickets[0].movie_title}</p>
        <p><strong>Ngày chiếu:</strong> ${tickets[0].show_date} ${tickets[0].show_time}</p>
        <p><strong>Phòng:</strong> ${tickets[0].screen_name}</p>
        <hr><h6>Danh sách vé:</h6><ul class="list-group">`;
    tickets.forEach(ticket => {
        const pickedUp = ticket.is_picked_up ? '<span class="badge bg-success">Đã lấy</span>' : '<span class="badge bg-warning">Chưa lấy</span>';
        html += `<li class="list-group-item d-flex justify-content-between align-items-center">Ghế ${ticket.seat} - ${ticket.seat_type} ${pickedUp}</li>`;
    });
    html += `</ul><p class="mt-3"><strong>Tổng số vé:</strong> ${tickets.length}</p><p><strong>Đã xác nhận:</strong> ${data.updated_count} vé</p></div></div>`;
    document.getElementById('ticketDetails').innerHTML = html;
    document.getElementById('ticketInfo').style.display = 'block';
}

document.getElementById('confirmPickupBtn').addEventListener('click', function() {
    if (!currentBookingId) { alert('Không có thông tin booking'); return; }
    if (confirm('Xác nhận tất cả vé trong booking này đã được lấy?')) {
        const code = document.getElementById('manualCode').value.trim() || currentBookingId;
        verifyTicket(code);
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin.counter_staff.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\laragon\www\CineHub_DuAnTotNghiep\resources\views\admin\counter_staff\scan_qr.blade.php ENDPATH**/ ?>