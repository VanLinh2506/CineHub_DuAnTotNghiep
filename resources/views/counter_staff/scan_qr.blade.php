@extends('layouts.app')

@php
    $title = 'Quét QR vé';
@endphp

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="page-title" style="color: #fff;">
            <i class="fas fa-qrcode"></i> {{ $title }}
        </h1>
    </div>

    <div class="row">
        <div class="col-md-6 offset-md-3">
            <!-- QR Code Scanner -->
            <div class="stat-card mb-4">
                <h5 class="mb-3 text-center">
                    <i class="fas fa-camera"></i> Quét mã QR từ vé
                </h5>
                
                <div id="camera-container" style="width: 100%; height: 300px; background: #000; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-bottom: 15px;">
                    <div class="text-center" style="color: #fff;">
                        <p><i class="fas fa-video fa-3x mb-2"></i></p>
                        <p>Không có quyền truy cập camera</p>
                        <p class="small">Hoặc nhập mã vé thủ công dưới đây</p>
                    </div>
                </div>

                <div class="form-group">
                    <label>Nhập mã vé:</label>
                    <input type="text" id="ticketCode" class="form-control form-control-lg" 
                           placeholder="Nhập hoặc quét mã vé..." 
                           onkeypress="if(event.key==='Enter') verifyTicket()">
                </div>

                <button type="button" class="btn btn-primary btn-lg w-100" onclick="verifyTicket()">
                    <i class="fas fa-check-circle"></i> Xác nhận vé
                </button>
            </div>

            <!-- Verification Result -->
            <div id="result-section" style="display: none;">
                <div id="result-card" class="stat-card">
                    <!-- Results will be displayed here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Scans -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="stat-card">
                <h5 class="mb-3">
                    <i class="fas fa-history"></i> Những vé vừa quét
                </h5>
                <div class="table-responsive">
                    <table class="table table-hover table-sm">
                        <thead>
                            <tr>
                                <th>Mã vé</th>
                                <th>Khách hàng</th>
                                <th>Phim</th>
                                <th>Phòng/Ghế</th>
                                <th>Thời gian quét</th>
                                <th>Kết quả</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (empty($recentScans))
                                <tr>
                                    <td colspan="6" class="text-center text-muted">Chưa quét vé nào</td>
                                </tr>
                            @else
                                @foreach ($recentScans as $scan)
                                    <tr>
                                        <td><code>{{ $scan['ticket_code'] }}</code></td>
                                        <td>{{ $scan['user_name'] }}</td>
                                        <td>{{ $scan['movie_title'] }}</td>
                                        <td>{{ $scan['screen_name'] }} - {{ $scan['seat'] }}</td>
                                        <td>{{ \Carbon\Carbon::parse($scan['scanned_at'])->diffForHumans() }}</td>
                                        <td>
                                            <span class="badge bg-{{ $scan['status'] === 'valid' ? 'success' : 'danger' }}">
                                                {{ $scan['status'] === 'valid' ? 'Hợp lệ' : 'Không hợp lệ' }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.js"></script>
<script>
    function verifyTicket() {
        const code = document.getElementById('ticketCode').value.trim();
        if (!code) {
            alert('Vui lòng nhập mã vé');
            return;
        }

        // Call verification API
        fetch('{{ url("?route=counter_staff/verifyTicket") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: 'ticket_code=' + encodeURIComponent(code)
        })
        .then(response => response.json())
        .then(data => {
            displayResult(data);
            if (data.success) {
                document.getElementById('ticketCode').value = '';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Lỗi khi xác nhận vé');
        });
    }

    function displayResult(data) {
        const resultSection = document.getElementById('result-section');
        const resultCard = document.getElementById('result-card');
        
        if (data.success) {
            resultCard.innerHTML = `
                <div class="alert alert-success">
                    <h5><i class="fas fa-check-circle"></i> Vé hợp lệ</h5>
                    <p><strong>Khách:</strong> ${data.user_name}</p>
                    <p><strong>Phim:</strong> ${data.movie_title}</p>
                    <p><strong>Phòng/Ghế:</strong> ${data.screen_name} - ${data.seat}</p>
                    <p><strong>Ngày/Giờ:</strong> ${data.show_date} ${data.show_time}</p>
                </div>
            `;
        } else {
            resultCard.innerHTML = `
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-circle"></i> Vé không hợp lệ</h5>
                    <p>${data.message}</p>
                </div>
            `;
        }
        
        resultSection.style.display = 'block';
        setTimeout(() => {
            resultSection.style.display = 'none';
        }, 5000);
    }

    // Initialize camera (placeholder)
    // In real implementation, would use getUserMedia() to access device camera
</script>
@endsection
