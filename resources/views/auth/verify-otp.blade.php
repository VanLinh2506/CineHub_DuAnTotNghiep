<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Xác thực mã OTP - CineHub</title>
    <!-- Bootstrap 5 CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Outfit', sans-serif;
            background: radial-gradient(circle at 10% 20%, rgb(18, 18, 30) 0%, rgb(30, 30, 50) 90%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            overflow-x: hidden;
            position: relative;
        }

        /* Trang trí background */
        body::before, body::after {
            content: "";
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: rgba(224, 49, 49, 0.15);
            filter: blur(80px);
            z-index: -1;
        }
        body::before {
            top: 10%;
            left: 10%;
        }
        body::after {
            bottom: 10%;
            right: 10%;
            background: rgba(42, 82, 152, 0.2);
        }

        .otp-container {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(16px) saturate(180%);
            -webkit-backdrop-filter: blur(16px) saturate(180%);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 40px 30px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
            max-width: 450px;
            width: 100%;
            text-align: center;
            transition: all 0.3s ease;
        }

        .otp-container:hover {
            box-shadow: 0 8px 32px 0 rgba(224, 49, 49, 0.2);
            border-color: rgba(224, 49, 49, 0.3);
        }

        .logo {
            font-size: 32px;
            font-weight: 800;
            background: linear-gradient(45deg, #ff416c, #ff4b2b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 20px;
            display: inline-block;
            text-decoration: none;
        }

        .otp-icon {
            font-size: 50px;
            color: #ff4b2b;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.1);
                opacity: 0.8;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        h2 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
        }

        p.description {
            color: #b0b3b8;
            font-size: 14px;
            margin-bottom: 30px;
        }

        .form-control-otp {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.1);
            color: #ffffff;
            font-size: 24px;
            font-weight: 700;
            text-align: center;
            letter-spacing: 6px;
            border-radius: 12px;
            padding: 12px;
            transition: all 0.3s ease;
        }

        .form-control-otp:focus {
            background: rgba(255, 255, 255, 0.12);
            border-color: #ff4b2b;
            box-shadow: 0 0 10px rgba(255, 75, 43, 0.5);
            color: #ffffff;
        }

        .btn-verify {
            background: linear-gradient(45deg, #ff416c, #ff4b2b);
            border: none;
            border-radius: 12px;
            padding: 12px;
            font-size: 16px;
            font-weight: 600;
            color: #ffffff;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(255, 75, 43, 0.3);
        }

        .btn-verify:hover {
            background: linear-gradient(45deg, #ff4b2b, #ff416c);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(255, 75, 43, 0.5);
        }

        .btn-verify:active {
            transform: translateY(0);
        }

        .timer-container {
            font-size: 14px;
            color: #b0b3b8;
            margin-top: 20px;
        }

        #timer {
            font-weight: 600;
            color: #ff4b2b;
        }

        .resend-link {
            color: #ff4b2b;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .resend-link:hover {
            color: #ff416c;
            text-decoration: underline;
        }

        .alert {
            background: rgba(220, 53, 69, 0.15);
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: #ea868f;
            border-radius: 12px;
            font-size: 14px;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.15);
            border: 1px solid rgba(40, 167, 69, 0.3);
            color: #75b798;
        }

        .back-home {
            margin-top: 30px;
            font-size: 14px;
        }

        .back-home a {
            color: #b0b3b8;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .back-home a:hover {
            color: #ffffff;
        }
    </style>
</head>
<body>

    @php
        $expiresAt = session('register_otp_expires_at');
        $remainingSeconds = $expiresAt ? max(0, $expiresAt->diffInSeconds(now())) : 300;
        $registerEmail = session('register_data.email') ?? 'email của bạn';
    @endphp

    <div class="otp-container">
        <a href="/" class="logo">CineHub</a>
        <div>
            <i class="fa-solid fa-envelope-shield otp-icon"></i>
        </div>
        <h2>Xác thực mã OTP</h2>
        <p class="description">Chúng tôi đã gửi mã xác thực gồm 6 chữ số tới email <strong class="text-white">{{ $registerEmail }}</strong>. Vui lòng nhập mã để hoàn tất đăng ký.</p>

        @if ($errors->any())
            <div class="alert alert-danger mb-4">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                {{ $errors->first() }}
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success mb-4">
                <i class="fa-solid fa-circle-check me-2"></i>
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('auth.verify-otp') }}" method="POST">
            @csrf
            <div class="mb-4">
                <input type="text" 
                       name="otp" 
                       id="otp-input"
                       class="form-control form-control-otp" 
                       placeholder="••••••" 
                       maxlength="6" 
                       required 
                       autocomplete="one-time-code"
                       pattern="[0-9]{6}"
                       inputmode="numeric"
                       oninput="this.value = this.value.replace(/[^0-9]/g, '')">
            </div>
            
            <button type="submit" class="btn btn-verify w-100 py-3">
                <i class="fa-solid fa-shield-halved me-2"></i>Xác nhận tài khoản
            </button>
        </form>

        <div class="timer-container">
            <span id="countdown-text">Mã OTP sẽ hết hạn sau: <span id="timer">05:00</span></span>
            <span id="resend-text" style="display: none;">
                Không nhận được mã? 
                <a href="javascript:void(0);" id="btn-resend" class="resend-link" onclick="resendOtp()">Gửi lại mã</a>
            </span>
        </div>

        <div class="back-home">
            <a href="/"><i class="fa-solid fa-arrow-left me-2"></i>Quay lại trang chủ</a>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Thời gian đếm ngược (giây) lấy từ Laravel session
        let remainingSeconds = {{ $remainingSeconds }};
        const timerElement = document.getElementById('timer');
        const countdownText = document.getElementById('countdown-text');
        const resendText = document.getElementById('resend-text');
        let timerInterval = null;

        function startTimer() {
            if (timerInterval) clearInterval(timerInterval);
            
            timerInterval = setInterval(() => {
                if (remainingSeconds <= 0) {
                    clearInterval(timerInterval);
                    countdownText.style.display = 'none';
                    resendText.style.display = 'inline';
                    return;
                }
                
                remainingSeconds--;
                
                let minutes = Math.floor(remainingSeconds / 60);
                let seconds = remainingSeconds % 60;
                
                minutes = minutes < 10 ? '0' + minutes : minutes;
                seconds = seconds < 10 ? '0' + seconds : seconds;
                
                timerElement.textContent = `${minutes}:${seconds}`;
            }, 1000);
        }

        // Tự động focus vào ô nhập OTP
        document.getElementById('otp-input').focus();

        // Chạy timer
        if (remainingSeconds > 0) {
            let minutes = Math.floor(remainingSeconds / 60);
            let seconds = remainingSeconds % 60;
            minutes = minutes < 10 ? '0' + minutes : minutes;
            seconds = seconds < 10 ? '0' + seconds : seconds;
            timerElement.textContent = `${minutes}:${seconds}`;
            startTimer();
        } else {
            countdownText.style.display = 'none';
            resendText.style.display = 'inline';
        }

        // Hàm xử lý gửi lại OTP bằng AJAX
        function resendOtp() {
            const btnResend = document.getElementById('btn-resend');
            const originalText = btnResend.textContent;
            
            btnResend.textContent = 'Đang gửi...';
            btnResend.style.pointerEvents = 'none';
            btnResend.style.opacity = '0.6';

            fetch("{{ route('auth.resend-otp') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    "X-Requested-With": "XMLHttpRequest",
                    "Accept": "application/json"
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Hiển thị thông báo thành công
                    alert(data.message || 'Mã OTP mới đã được gửi!');
                    // Reset lại timer về 5 phút (300 giây)
                    remainingSeconds = 300;
                    countdownText.style.display = 'inline';
                    resendText.style.display = 'none';
                    startTimer();
                } else {
                    alert(data.message || 'Không thể gửi lại OTP. Vui lòng thử lại!');
                }
            })
            .catch(error => {
                console.error("Error resending OTP:", error);
                alert("Có lỗi kết nối xảy ra. Vui lòng thử lại!");
            })
            .finally(() => {
                btnResend.textContent = originalText;
                btnResend.style.pointerEvents = 'auto';
                btnResend.style.opacity = '1';
            });
        }
    </script>
</body>
</html>
