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

            <button type="submit" class="btn-register">
                Đăng ký
            </button>

            <div class="terms">
                Bằng việc đăng ký, bạn đồng ý với 
                <a href="#">Điều khoản dịch vụ</a> và 
                <a href="#">Chính sách bảo mật</a> của chúng tôi
            </div>
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
</body>
</html>
