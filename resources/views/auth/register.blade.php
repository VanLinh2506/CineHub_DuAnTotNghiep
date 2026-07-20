<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - CineHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0a0a0a;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            position: relative;
            overflow: hidden;
        }

        /* Animated background gradient */
        body::before {
            content: '';
            position: fixed;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: 
                radial-gradient(circle at 20% 50%, rgba(229, 9, 20, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(138, 43, 226, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(0, 123, 255, 0.15) 0%, transparent 50%),
                linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #16213e 100%);
            animation: gradientShift 20s ease infinite;
            z-index: 0;
        }

        @keyframes gradientShift {
            0%, 100% {
                transform: translate(0, 0) rotate(0deg);
            }
            50% {
                transform: translate(5%, 5%) rotate(5deg);
            }
        }

        .register-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 400px;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(25px) saturate(180%);
            -webkit-backdrop-filter: blur(25px) saturate(180%);
            border-radius: 20px;
            padding: 40px 35px 35px;
            box-shadow:
                0 8px 32px 0 rgba(0, 0, 0, 0.5),
                inset 0 1px 0 0 rgba(255, 255, 255, 0.1),
                0 0 60px rgba(138, 43, 226, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.08);
            animation: floatIn 0.8s ease-out;
        }

        @keyframes floatIn {
            from {
                opacity: 0;
                transform: translateY(50px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        /* Glowing orbs behind the form */
        .register-container::before,
        .register-container::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.6;
            animation: pulse 4s ease-in-out infinite;
            z-index: -1;
        }

        .register-container::before {
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(138, 43, 226, 0.4) 0%, transparent 70%);
            top: -50px;
            right: -50px;
            animation-delay: 0s;
        }

        .register-container::after {
            width: 180px;
            height: 180px;
            background: radial-gradient(circle, rgba(0, 123, 255, 0.3) 0%, transparent 70%);
            bottom: -40px;
            left: -40px;
            animation-delay: 2s;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 0.6;
            }
            50% {
                transform: scale(1.2);
                opacity: 0.8;
            }
        }

        .logo-section {
            text-align: center;
            margin-bottom: 25px;
        }

        .logo-icon {
            font-size: 40px;
            background: linear-gradient(135deg, #8a2be2 0%, #ff6b6b 50%, #e50914 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 10px;
            display: inline-block;
            animation: logoFloat 3s ease-in-out infinite;
            filter: drop-shadow(0 0 20px rgba(138, 43, 226, 0.5));
        }

        @keyframes logoFloat {
            0%, 100% {
                transform: translateY(0) rotate(0deg);
            }
            25% {
                transform: translateY(-8px) rotate(-5deg);
            }
            75% {
                transform: translateY(-8px) rotate(5deg);
            }
        }

        .logo-text {
            font-size: 28px;
            font-weight: 800;
            background: linear-gradient(135deg, #fff 0%, #e0e0e0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 2px;
            text-transform: uppercase;
            filter: drop-shadow(0 2px 10px rgba(255, 255, 255, 0.2));
        }

        .welcome-text {
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 20px;
            font-size: 13px;
            font-weight: 300;
            letter-spacing: 0.5px;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 50px;
            margin-bottom: 18px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideInBounce 0.4s ease-out;
            backdrop-filter: blur(10px);
            font-size: 13px;
        }

        @keyframes slideInBounce {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }
            60% {
                transform: translateY(5px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-error {
            background: rgba(229, 9, 20, 0.15);
            border: 1px solid rgba(229, 9, 20, 0.3);
            color: #ff6b6b;
        }

        .alert-success {
            background: rgba(40, 200, 120, 0.15);
            border: 1px solid rgba(40, 200, 120, 0.3);
            color: #51cf66;
        }

        .form-group {
            margin-bottom: 15px;
            position: relative;
        }

        .form-label {
            display: block;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 6px;
            font-size: 11px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.3);
            font-size: 14px;
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            padding: 12px 20px 12px 45px;
            background: rgba(255, 255, 255, 0.04);
            border: 1.5px solid rgba(255, 255, 255, 0.08);
            border-radius: 10px;
            color: #fff;
            font-size: 14px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
            backdrop-filter: blur(10px);
            height: auto;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(138, 43, 226, 0.6);
            box-shadow: 
                0 0 0 4px rgba(138, 43, 226, 0.1),
                0 8px 20px rgba(138, 43, 226, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .form-control:focus + .input-icon {
            color: #8a2be2;
            transform: translateY(-50%) scale(1.1);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.25);
        }

        .password-strength {
            margin-top: 6px;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.5);
            padding-left: 12px;
        }

        .btn-register {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #8a2be2 0%, #6a1bb2 100%);
            border: none;
            border-radius: 10px;
            color: white;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow:
                0 4px 20px rgba(138, 43, 226, 0.4),
                0 0 40px rgba(138, 43, 226, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            margin-top: 12px;
            position: relative;
            overflow: hidden;
        }

        .btn-register::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-register:hover {
            background: linear-gradient(135deg, #a855f7 0%, #8a2be2 100%);
            transform: translateY(-3px);
            box-shadow: 
                0 8px 30px rgba(138, 43, 226, 0.5),
                0 0 60px rgba(138, 43, 226, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }

        .btn-register:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-register:active {
            transform: translateY(-1px);
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 20px 0;
            color: rgba(255, 255, 255, 0.4);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        .divider span {
            padding: 0 16px;
        }

        .login-link {
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            font-size: 13px;
            margin-top: 8px;
        }

        .login-link a {
            color: #a855f7;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            position: relative;
        }

        .login-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #8a2be2;
            transition: width 0.3s;
        }

        .login-link a:hover {
            color: #8a2be2;
        }

        .login-link a:hover::after {
            width: 100%;
        }

        .btn-google {
            width: 100%;
            padding: 12px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            color: #333;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            text-decoration: none;
            box-shadow:
                0 4px 20px rgba(0, 0, 0, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.5);
            margin-bottom: 15px;
        }

        .btn-google:hover {
            background: rgba(255, 255, 255, 1);
            transform: translateY(-3px);
            box-shadow: 
                0 8px 30px rgba(0, 0, 0, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.7);
        }

        .btn-google:active {
            transform: translateY(-1px);
        }

        .btn-google svg {
            width: 20px;
            height: 20px;
        }

        .back-home {
            position: fixed;
            top: 30px;
            left: 30px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 12px 24px;
            background: rgba(255, 255, 255, 0.04);
            border-radius: 50px;
            backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            z-index: 10;
        }

        .back-home:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.08);
            transform: translateX(-5px);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
        }

        .terms {
            text-align: center;
            font-size: 11px;
            color: rgba(255, 255, 255, 0.5);
            margin-top: 15px;
            line-height: 1.6;
        }

        .terms a {
            color: #a855f7;
            text-decoration: none;
            transition: color 0.3s;
        }

        .terms a:hover {
            color: #8a2be2;
            text-decoration: underline;
        }

        /* Checkbox điều khoản */
        .tos-check-group {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            background: rgba(255,255,255,0.03);
            border: 1.5px solid rgba(255,255,255,0.08);
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 4px;
            transition: border-color 0.3s;
        }
        .tos-check-group:has(input:checked) {
            border-color: rgba(138, 43, 226, 0.5);
            background: rgba(138, 43, 226, 0.06);
        }
        .tos-check-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            margin-top: 2px;
            cursor: pointer;
            accent-color: #8a2be2;
        }
        .tos-check-group label {
            color: rgba(255,255,255,0.75);
            font-size: 13px;
            line-height: 1.6;
            cursor: pointer;
        }
        .tos-check-group label a {
            color: #a855f7;
            text-decoration: none;
            font-weight: 600;
        }
        .tos-check-group label a:hover {
            color: #e50914;
            text-decoration: underline;
        }
        .tos-error {
            color: #ff6b6b;
            font-size: 12px;
            padding-left: 4px;
            display: none;
        }
        .tos-error.show { display: block; }

        /* Modal điều khoản */
        .tos-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.8);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .tos-modal-overlay.open {
            display: flex;
        }
        .tos-modal {
            background: #1a1a2e;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 16px;
            width: 100%;
            max-width: 640px;
            max-height: 80vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 60px rgba(0,0,0,0.6);
            animation: modalIn 0.3s ease-out;
        }
        @keyframes modalIn {
            from { opacity:0; transform: scale(0.9) translateY(20px); }
            to   { opacity:1; transform: scale(1) translateY(0); }
        }
        .tos-modal-header {
            padding: 20px 24px 16px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .tos-modal-header h3 {
            color: #fff;
            font-size: 1.1rem;
            font-weight: 700;
        }
        .tos-modal-close {
            background: none;
            border: none;
            color: #aaa;
            font-size: 1.3rem;
            cursor: pointer;
            transition: color 0.2s;
        }
        .tos-modal-close:hover { color: #fff; }
        .tos-modal-body {
            padding: 20px 24px;
            overflow-y: auto;
            flex: 1;
            color: #ccc;
            font-size: 0.88rem;
            line-height: 1.7;
        }
        .tos-modal-body h4 {
            color: #fff;
            font-size: 0.95rem;
            margin: 1.2rem 0 0.5rem;
            padding-bottom: 4px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .tos-modal-body h4:first-child { margin-top: 0; }
        .tos-modal-body ul { padding-left: 1.3rem; margin: 0.4rem 0; }
        .tos-modal-body li { margin-bottom: 4px; }
        .tos-modal-footer {
            padding: 16px 24px;
            border-top: 1px solid rgba(255,255,255,0.08);
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }
        .btn-tos-decline {
            padding: 10px 20px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.12);
            border-radius: 8px;
            color: #aaa;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-tos-decline:hover { background: rgba(255,255,255,0.1); color: #fff; }
        .btn-tos-accept {
            padding: 10px 24px;
            background: linear-gradient(135deg, #8a2be2, #6a1bb2);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.2s;
        }
        .btn-tos-accept:hover {
            background: linear-gradient(135deg, #a855f7, #8a2be2);
            transform: translateY(-1px);
        }
        .tos-read-note {
            font-size: 11px;
            color: #aaa;
            margin-right: auto;
            align-self: center;
        }

        @media (max-width: 576px) {
            .register-container {
                padding: 30px 25px 25px;
                border-radius: 15px;
            }

            .logo-text {
                font-size: 24px;
                letter-spacing: 1px;
            }

            .logo-icon {
                font-size: 36px;
            }

            .back-home {
                top: 15px;
                left: 15px;
                font-size: 13px;
                padding: 8px 15px;
            }

            .form-control {
                padding: 10px 15px 10px 40px;
                font-size: 13px;
            }

            .btn-register,
            .btn-google {
                padding: 11px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>
    <a href="{{ route('home') }}" class="back-home">
        <i class="fas fa-arrow-left"></i>
        <span>Về trang chủ</span>
    </a>

    <div class="register-container">
        <div class="logo-section">
            <div class="logo-icon">
                <i class="fas fa-film"></i>
            </div>
            <div class="logo-text">CineHub</div>
        </div>

        <div class="welcome-text">
            Tạo tài khoản để trải nghiệm dịch vụ tốt nhất
        </div>

        @if (session('error'))
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        @if (session('success'))
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Họ và tên</label>
                <div class="input-wrapper">
                    <input type="text" 
                           name="name" 
                           class="form-control" 
                           placeholder="Nhập họ và tên đầy đủ"
                           value="{{ old('name') }}"
                           required 
                           autofocus>
                    <i class="fas fa-user input-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Email</label>
                <div class="input-wrapper">
                    <input type="email" 
                           name="email" 
                           class="form-control" 
                           placeholder="Nhập địa chỉ email"
                           value="{{ old('email') }}"
                           required>
                    <i class="fas fa-envelope input-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Mật khẩu</label>
                <div class="input-wrapper">
                    <input type="password" 
                           name="password" 
                           class="form-control" 
                           placeholder="Tạo mật khẩu (tối thiểu 6 ký tự)"
                           required>
                    <i class="fas fa-lock input-icon"></i>
                </div>
                <div class="password-strength">
                    <i class="fas fa-info-circle"></i> Mật khẩu nên có ít nhất 6 ký tự
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Xác nhận mật khẩu</label>
                <div class="input-wrapper">
                    <input type="password" 
                           name="confirm_password" 
                           class="form-control" 
                           placeholder="Nhập lại mật khẩu"
                           required>
                    <i class="fas fa-lock input-icon"></i>
                </div>
            </div>

            {{-- Checkbox đồng ý điều khoản --}}
            <div class="tos-check-group" id="tosCheckGroup">
                <input type="checkbox" id="agree_tos" name="agree_tos" value="1"
                       {{ old('agree_tos') ? 'checked' : '' }}>
                <label for="agree_tos">
                    Tôi đã đọc và đồng ý với
                    <a href="#" id="openTosModal">Điều khoản dịch vụ</a>
                    của CineHub. Bắt buộc phải đồng ý để tạo tài khoản.
                </label>
            </div>
            <div class="tos-error {{ $errors->has('agree_tos') ? 'show' : '' }}" id="tosError">
                <i class="fas fa-exclamation-circle"></i> Vui lòng đọc và đồng ý với Điều khoản dịch vụ trước khi đăng ký.
            </div>

            <button type="submit" class="btn-register" id="registerBtn">
                Đăng ký
            </button>
        </form>

        <div class="divider">
            <span>hoặc</span>
        </div>

        <!-- Google Login Button -->
        <a href="{{ route('auth.google') }}" class="btn-google">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19.8 10.2273C19.8 9.51819 19.7364 8.83637 19.6182 8.18182H10.2V12.05H15.5818C15.3273 13.3 14.5727 14.3591 13.4455 15.0682V17.5773H16.7364C18.6091 15.8364 19.8 13.2727 19.8 10.2273Z" fill="#4285F4"/>
                <path d="M10.2 20C12.9 20 15.1727 19.1045 16.7364 17.5773L13.4455 15.0682C12.4909 15.6682 11.2636 16.0227 10.2 16.0227C7.59091 16.0227 5.37273 14.2636 4.52727 11.9H1.11364V14.4909C2.66818 17.5909 6.19091 20 10.2 20Z" fill="#34A853"/>
                <path d="M4.52727 11.9C4.30909 11.3 4.18182 10.6591 4.18182 10C4.18182 9.34091 4.30909 8.7 4.52727 8.1V5.50909H1.11364C0.418182 6.89091 0 8.4 0 10C0 11.6 0.418182 13.1091 1.11364 14.4909L4.52727 11.9Z" fill="#FBBC05"/>
                <path d="M10.2 3.97727C11.3636 3.97727 12.4 4.37727 13.2091 5.15455L16.1364 2.22727C15.1636 1.32727 12.9 0 10.2 0C6.19091 0 2.66818 2.40909 1.11364 5.50909L4.52727 8.1C5.37273 5.73636 7.59091 3.97727 10.2 3.97727Z" fill="#EA4335"/>
            </svg>
            <span>Đăng ký bằng Google</span>
        </a>

        <div class="login-link">
            Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập ngay</a>
        </div>
    </div>

    {{-- Modal Điều khoản dịch vụ --}}
    <div class="tos-modal-overlay" id="tosModalOverlay">
        <div class="tos-modal">
            <div class="tos-modal-header">
                <h3><i class="fas fa-file-contract" style="color:#e50914;margin-right:8px;"></i> Điều khoản dịch vụ CineHub</h3>
                <button class="tos-modal-close" id="closeTosModal"><i class="fas fa-times"></i></button>
            </div>
            <div class="tos-modal-body" id="tosScrollBody">
                <h4>1. Giới thiệu</h4>
                <p>CineHub là nền tảng xem phim trực tuyến và đặt vé rạp tại Việt Nam. Bằng việc đăng ký, bạn xác nhận đồng ý ràng buộc với toàn bộ điều khoản dưới đây.</p>

                <h4>2. Tài khoản người dùng</h4>
                <ul>
                    <li>Cung cấp thông tin đăng ký trung thực, chính xác.</li>
                    <li>Tự chịu trách nhiệm bảo mật mật khẩu tài khoản.</li>
                    <li>Mỗi cá nhân chỉ được tạo một tài khoản duy nhất.</li>
                    <li>CineHub có quyền khóa tài khoản vi phạm điều khoản.</li>
                </ul>

                <h4>3. Phân loại nội dung theo độ tuổi</h4>
                <ul>
                    <li><strong>P:</strong> Mọi lứa tuổi. <strong>T13:</strong> Từ 13 tuổi trở lên.</li>
                    <li><strong>T16:</strong> Từ 16 tuổi trở lên. <strong>T18:</strong> Từ 18 tuổi trở lên.</li>
                    <li>Bạn tự chịu trách nhiệm cung cấp ngày sinh chính xác. CineHub không chịu trách nhiệm nếu bạn khai sai độ tuổi.</li>
                </ul>

                <h4>4. Gói đăng ký (Subscription)</h4>
                <ul>
                    <li>Phí đăng ký được thanh toán trước và không hoàn lại sau khi kích hoạt.</li>
                    <li>CineHub thông báo thay đổi giá trước tối thiểu 30 ngày qua email.</li>
                    <li>Vi phạm điều khoản có thể dẫn đến chấm dứt gói đăng ký không hoàn tiền.</li>
                </ul>

                <h4>5. Đặt vé rạp & Hoàn tiền</h4>
                <ul>
                    <li>Được hủy và hoàn tiền nếu hủy trước ít nhất 2 giờ so với giờ chiếu và vé chưa sử dụng.</li>
                    <li>Vé đã quét mã QR (đã vào rạp) không thể hủy.</li>
                    <li>Thời gian hoàn tiền tối đa 7 ngày làm việc.</li>
                </ul>

                <h4>6. Bình luận & Đánh giá</h4>
                <ul>
                    <li>Nghiêm cấm nội dung thù địch, spam, vi phạm bản quyền, khiêu dâm.</li>
                    <li>Vi phạm lần đầu: cảnh cáo. Lần 2: khóa bình luận 7 ngày. Lần 3: khóa tài khoản vĩnh viễn.</li>
                    <li>Bạn tự chịu trách nhiệm pháp lý về nội dung đăng tải.</li>
                </ul>

                <h4>7. Thanh toán</h4>
                <p>CineHub chấp nhận thanh toán qua VNPay và điểm CineHub (Points). Mọi tranh chấp cần phản ánh trong 30 ngày.</p>

                <h4>8. Quyền sở hữu trí tuệ</h4>
                <p>Nghiêm cấm sao chép, phân phối hoặc khai thác nội dung trên CineHub khi chưa có sự cho phép bằng văn bản.</p>

                <h4>9. Giới hạn trách nhiệm</h4>
                <p>CineHub không chịu trách nhiệm về thiệt hại gián tiếp, mất dữ liệu hoặc nội dung do người dùng tạo ra.</p>

                <h4>10. Luật áp dụng</h4>
                <p>Điều khoản được điều chỉnh theo pháp luật Việt Nam. Tranh chấp được giải quyết tại Tòa án nhân dân có thẩm quyền tại Việt Nam.</p>

                <p style="margin-top:1.2rem;padding-top:1rem;border-top:1px solid rgba(255,255,255,0.1);color:#888;font-size:0.82rem;">
                    <i class="fas fa-info-circle"></i> Để xem đầy đủ,
                    <a href="{{ route('terms') }}" target="_blank" style="color:#a855f7;">mở trang Điều khoản dịch vụ</a>.
                    Phiên bản 1.0 · Hiệu lực từ 01/01/2025
                </p>
            </div>
            <div class="tos-modal-footer">
                <span class="tos-read-note" id="tosReadNote">
                    <i class="fas fa-arrow-down" style="color:#ffc107;"></i> Vui lòng cuộn xuống để đọc hết
                </span>
                <button class="btn-tos-decline" id="tosDeclineBtn">Không đồng ý</button>
                <button class="btn-tos-accept" id="tosAcceptBtn">Đồng ý & Xác nhận</button>
            </div>
        </div>
    </div>

    <script>
    (function() {
        const overlay    = document.getElementById('tosModalOverlay');
        const openLink   = document.getElementById('openTosModal');
        const closeBtn   = document.getElementById('closeTosModal');
        const declineBtn = document.getElementById('tosDeclineBtn');
        const acceptBtn  = document.getElementById('tosAcceptBtn');
        const checkbox   = document.getElementById('agree_tos');
        const tosError   = document.getElementById('tosError');
        const scrollBody = document.getElementById('tosScrollBody');
        const readNote   = document.getElementById('tosReadNote');
        const form       = document.querySelector('form[action="{{ route('register') }}"]');

        // Mở modal khi click link điều khoản
        openLink.addEventListener('click', function(e) {
            e.preventDefault();
            overlay.classList.add('open');
            checkScrolled();
        });

        // Đóng modal
        closeBtn.addEventListener('click', closeModal);
        overlay.addEventListener('click', function(e) {
            if (e.target === overlay) closeModal();
        });

        function closeModal() {
            overlay.classList.remove('open');
        }

        // Theo dõi cuộn để biết đã đọc chưa
        let hasScrolled = false;
        function checkScrolled() {
            const el = scrollBody;
            if (el.scrollHeight - el.scrollTop <= el.clientHeight + 50) {
                hasScrolled = true;
                readNote.innerHTML = '<i class="fas fa-check" style="color:#51cf66;"></i> Đã đọc hết';
            }
        }
        scrollBody.addEventListener('scroll', checkScrolled);

        // Từ chối
        declineBtn.addEventListener('click', function() {
            checkbox.checked = false;
            closeModal();
        });

        // Đồng ý
        acceptBtn.addEventListener('click', function() {
            checkbox.checked = true;
            tosError.classList.remove('show');
            closeModal();
        });

        // Khi checkbox thay đổi thủ công
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                tosError.classList.remove('show');
            }
        });

        // Validate trước khi submit
        form.addEventListener('submit', function(e) {
            if (!checkbox.checked) {
                e.preventDefault();
                tosError.classList.add('show');
                checkbox.closest('.tos-check-group').style.borderColor = 'rgba(229,9,20,0.6)';
                checkbox.closest('.tos-check-group').scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });

        // Reset border khi tick
        checkbox.addEventListener('change', function() {
            if (this.checked) {
                this.closest('.tos-check-group').style.borderColor = 'rgba(138,43,226,0.5)';
            }
        });
    })();
    </script>
</body>
</html>
