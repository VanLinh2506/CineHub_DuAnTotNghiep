<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - CineHub</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0f0c29 0%, #302b63 50%, #24243e 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 50px 40px;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
            border: 1px solid rgba(255, 255, 255, 0.18);
            animation: fadeInUp 0.6s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .logo-section {
            text-align: center;
            margin-bottom: 40px;
        }

        .logo-icon {
            font-size: 50px;
            color: #e50914;
            margin-bottom: 15px;
            display: inline-block;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.1);
            }
        }

        .logo-text {
            font-size: 32px;
            font-weight: 700;
            color: #fff;
            letter-spacing: 2px;
        }

        .welcome-text {
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 30px;
            font-size: 16px;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 10px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .alert-error {
            background: rgba(220, 53, 69, 0.2);
            border: 1px solid rgba(220, 53, 69, 0.5);
            color: #ff6b6b;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.2);
            border: 1px solid rgba(40, 167, 69, 0.5);
            color: #51cf66;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-label {
            display: block;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 10px;
            font-size: 14px;
            font-weight: 500;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.4);
            font-size: 16px;
            transition: color 0.3s;
        }

        .form-control {
            width: 100%;
            padding: 15px 20px 15px 50px;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: #fff;
            font-size: 15px;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: #e50914;
            box-shadow: 0 0 0 4px rgba(229, 9, 20, 0.1);
        }

        .form-control:focus + .input-icon {
            color: #e50914;
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 10px;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 8px;
            cursor: pointer;
        }

        .checkbox-wrapper input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: #e50914;
        }

        .checkbox-wrapper label {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            cursor: pointer;
        }

        .forgot-link {
            color: #e50914;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s;
        }

        .forgot-link:hover {
            color: #ff1f2f;
            text-decoration: underline;
        }

        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #e50914 0%, #b20710 100%);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(229, 9, 20, 0.3);
        }

        .btn-login:hover {
            background: linear-gradient(135deg, #ff1f2f 0%, #e50914 100%);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(229, 9, 20, 0.4);
        }

        .btn-login:active {
            transform: translateY(0);
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 30px 0;
            color: rgba(255, 255, 255, 0.5);
            font-size: 14px;
        }

        .divider::before,
        .divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .divider span {
            padding: 0 15px;
        }

        .register-link {
            text-align: center;
            color: rgba(255, 255, 255, 0.8);
            font-size: 15px;
        }

        .register-link a {
            color: #e50914;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s;
        }

        .register-link a:hover {
            color: #ff1f2f;
            text-decoration: underline;
        }

        .btn-google {
            width: 100%;
            padding: 16px;
            background: #fff;
            border: none;
            border-radius: 12px;
            color: #444;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            text-decoration: none;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
        }

        .btn-google:hover {
            background: #f8f8f8;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        }

        .btn-google:active {
            transform: translateY(0);
        }

        .btn-google svg {
            width: 20px;
            height: 20px;
        }

        .back-home {
            position: absolute;
            top: 30px;
            left: 30px;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 15px;
            transition: all 0.3s;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 25px;
            backdrop-filter: blur(10px);
        }

        .back-home:hover {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
            transform: translateX(-5px);
        }

        @media (max-width: 576px) {
            .login-container {
                padding: 40px 30px;
            }

            .logo-text {
                font-size: 28px;
            }

            .back-home {
                top: 20px;
                left: 20px;
                font-size: 14px;
                padding: 8px 15px;
            }

            .form-options {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <a href="{{ route('home') }}" class="back-home">
        <i class="fas fa-arrow-left"></i>
        <span>Về trang chủ</span>
    </a>

    <div class="login-container">
        <div class="logo-section">
            <div class="logo-icon">
                <i class="fas fa-film"></i>
            </div>
            <div class="logo-text">CineHub</div>
        </div>

        <div class="welcome-text">
            Chào mừng bạn trở lại! Đăng nhập để tiếp tục
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

        <form method="POST" action="{{ route('login') }}">
            @csrf
            
            <div class="form-group">
                <label class="form-label">Email</label>
                <div class="input-wrapper">
                    <input type="email" 
                           name="email" 
                           class="form-control" 
                           placeholder="Nhập email của bạn"
                           value="{{ old('email') }}"
                           required 
                           autofocus>
                    <i class="fas fa-envelope input-icon"></i>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label">Mật khẩu</label>
                <div class="input-wrapper">
                    <input type="password" 
                           name="password" 
                           class="form-control" 
                           placeholder="Nhập mật khẩu"
                           required>
                    <i class="fas fa-lock input-icon"></i>
                </div>
            </div>

            <div class="form-options">
                <div class="checkbox-wrapper">
                    <input type="checkbox" name="remember_me" id="remember_me">
                    <label for="remember_me">Ghi nhớ đăng nhập</label>
                </div>
                <a href="{{ route('password.request') }}" class="forgot-link">Quên mật khẩu?</a>
            </div>

            <button type="submit" class="btn-login">
                Đăng nhập
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
            <span>Đăng nhập bằng Google</span>
        </a>

        <div class="register-link">
            Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a>
        </div>
    </div>
</body>
</html>
