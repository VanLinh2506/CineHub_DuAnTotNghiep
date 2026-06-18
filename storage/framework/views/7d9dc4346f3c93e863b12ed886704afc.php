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

        .login-container {
            position: relative;
            z-index: 1;
            width: 100%;
            max-width: 480px;
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(25px) saturate(180%);
            -webkit-backdrop-filter: blur(25px) saturate(180%);
            border-radius: 100% 100% 40px 40px;
            padding: 60px 45px 45px;
            box-shadow: 
                0 8px 32px 0 rgba(0, 0, 0, 0.5),
                inset 0 1px 0 0 rgba(255, 255, 255, 0.1),
                0 0 60px rgba(229, 9, 20, 0.15);
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
        .login-container::before,
        .login-container::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.6;
            animation: pulse 4s ease-in-out infinite;
            z-index: -1;
        }

        .login-container::before {
            width: 200px;
            height: 200px;
            background: radial-gradient(circle, rgba(229, 9, 20, 0.4) 0%, transparent 70%);
            top: -50px;
            right: -50px;
            animation-delay: 0s;
        }

        .login-container::after {
            width: 180px;
            height: 180px;
            background: radial-gradient(circle, rgba(138, 43, 226, 0.3) 0%, transparent 70%);
            bottom: -40px;
            left: -40px;
            animation-delay: 2s;
        }

        .logo-section {
            text-align: center;
            margin-bottom: 35px;
        }

        .logo-icon {
            font-size: 55px;
            background: linear-gradient(135deg, #e50914 0%, #ff6b6b 50%, #ff1f2f 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 15px;
            display: inline-block;
            animation: logoFloat 3s ease-in-out infinite;
            filter: drop-shadow(0 0 20px rgba(229, 9, 20, 0.5));
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
            font-size: 36px;
            font-weight: 800;
            background: linear-gradient(135deg, #fff 0%, #e0e0e0 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 3px;
            text-transform: uppercase;
            filter: drop-shadow(0 2px 10px rgba(255, 255, 255, 0.2));
        }

        .welcome-text {
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 30px;
            font-size: 15px;
            font-weight: 300;
            letter-spacing: 0.5px;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 50px;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideInBounce 0.4s ease-out;
            backdrop-filter: blur(10px);
            font-size: 14px;
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
            margin-bottom: 22px;
            position: relative;
        }

        .form-label {
            display: block;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 10px;
            font-size: 13px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-icon {
            position: absolute;
            left: 22px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.3);
            font-size: 16px;
            transition: all 0.3s ease;
            pointer-events: none;
        }

        .form-control {
            width: 100%;
            padding: 18px 24px 18px 56px;
            background: rgba(255, 255, 255, 0.04);
            border: 1.5px solid rgba(255, 255, 255, 0.08);
            border-radius: 50px;
            color: #fff;
            font-size: 15px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            outline: none;
            backdrop-filter: blur(10px);
        }

        .form-control:focus {
            background: rgba(255, 255, 255, 0.06);
            border-color: rgba(229, 9, 20, 0.6);
            box-shadow: 
                0 0 0 4px rgba(229, 9, 20, 0.1),
                0 8px 20px rgba(229, 9, 20, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .form-control:focus + .input-icon {
            color: #e50914;
            transform: translateY(-50%) scale(1.1);
        }

        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.25);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 28px;
            flex-wrap: wrap;
            gap: 12px;
        }

        .checkbox-wrapper {
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
        }

        .checkbox-wrapper input[type="checkbox"] {
            width: 20px;
            height: 20px;
            cursor: pointer;
            accent-color: #e50914;
            border-radius: 6px;
        }

        .checkbox-wrapper label {
            color: rgba(255, 255, 255, 0.75);
            font-size: 13px;
            cursor: pointer;
            transition: color 0.3s;
        }

        .checkbox-wrapper:hover label {
            color: rgba(255, 255, 255, 0.95);
        }

        .forgot-link {
            color: #ff6b6b;
            text-decoration: none;
            font-size: 13px;
            transition: all 0.3s;
            position: relative;
        }

        .forgot-link::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 1px;
            background: #ff6b6b;
            transition: width 0.3s;
        }

        .forgot-link:hover {
            color: #e50914;
        }

        .forgot-link:hover::after {
            width: 100%;
        }

        .btn-login {
            width: 100%;
            padding: 18px;
            background: linear-gradient(135deg, #e50914 0%, #b20710 100%);
            border: none;
            border-radius: 50px;
            color: white;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            text-transform: uppercase;
            letter-spacing: 2px;
            box-shadow: 
                0 4px 20px rgba(229, 9, 20, 0.4),
                0 0 40px rgba(229, 9, 20, 0.2),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            position: relative;
            overflow: hidden;
        }

        .btn-login::before {
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

        .btn-login:hover {
            background: linear-gradient(135deg, #ff1f2f 0%, #e50914 100%);
            transform: translateY(-3px);
            box-shadow: 
                0 8px 30px rgba(229, 9, 20, 0.5),
                0 0 60px rgba(229, 9, 20, 0.3),
                inset 0 1px 0 rgba(255, 255, 255, 0.3);
        }

        .btn-login:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-login:active {
            transform: translateY(-1px);
        }

        .divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 28px 0;
            color: rgba(255, 255, 255, 0.4);
            font-size: 13px;
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
            padding: 0 18px;
        }

        .register-link {
            text-align: center;
            color: rgba(255, 255, 255, 0.7);
            font-size: 14px;
            margin-top: 5px;
        }

        .register-link a {
            color: #ff6b6b;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
            position: relative;
        }

        .register-link a::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            width: 0;
            height: 2px;
            background: #e50914;
            transition: width 0.3s;
        }

        .register-link a:hover {
            color: #e50914;
        }

        .register-link a:hover::after {
            width: 100%;
        }

        .btn-google {
            width: 100%;
            padding: 17px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            color: #333;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            text-decoration: none;
            box-shadow: 
                0 4px 20px rgba(0, 0, 0, 0.15),
                inset 0 1px 0 rgba(255, 255, 255, 0.5);
            margin-bottom: 22px;
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
            width: 22px;
            height: 22px;
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

        @media (max-width: 576px) {
            .login-container {
                padding: 50px 35px 40px;
                border-radius: 100% 100% 35px 35px;
            }

            .logo-text {
                font-size: 30px;
                letter-spacing: 2px;
            }

            .logo-icon {
                font-size: 48px;
            }

            .back-home {
                top: 20px;
                left: 20px;
                font-size: 14px;
                padding: 10px 18px;
            }

            .form-options {
                flex-direction: column;
                align-items: flex-start;
            }

            .form-control {
                padding: 16px 22px 16px 52px;
            }

            .btn-login,
            .btn-google {
                padding: 16px;
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <a href="<?php echo e(route('home')); ?>" class="back-home">
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

        <?php if(session('error')): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo e(session('error')); ?></span>
            </div>
        <?php endif; ?>

        <?php if(session('success')): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span><?php echo e(session('success')); ?></span>
            </div>
        <?php endif; ?>

        <?php if($errors->any()): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <div>
                    <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div><?php echo e($error); ?></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?php echo e(route('login')); ?>">
            <?php echo csrf_field(); ?>
            
            <div class="form-group">
                <label class="form-label">Email</label>
                <div class="input-wrapper">
                    <input type="email" 
                           name="email" 
                           class="form-control" 
                           placeholder="Nhập email của bạn"
                           value="<?php echo e(old('email')); ?>"
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
                <a href="<?php echo e(route('password.request')); ?>" class="forgot-link">Quên mật khẩu?</a>
            </div>

            <button type="submit" class="btn-login">
                Đăng nhập
            </button>
        </form>

        <div class="divider">
            <span>hoặc</span>
        </div>

        <!-- Google Login Button -->
        <a href="<?php echo e(route('auth.google')); ?>" class="btn-google">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M19.8 10.2273C19.8 9.51819 19.7364 8.83637 19.6182 8.18182H10.2V12.05H15.5818C15.3273 13.3 14.5727 14.3591 13.4455 15.0682V17.5773H16.7364C18.6091 15.8364 19.8 13.2727 19.8 10.2273Z" fill="#4285F4"/>
                <path d="M10.2 20C12.9 20 15.1727 19.1045 16.7364 17.5773L13.4455 15.0682C12.4909 15.6682 11.2636 16.0227 10.2 16.0227C7.59091 16.0227 5.37273 14.2636 4.52727 11.9H1.11364V14.4909C2.66818 17.5909 6.19091 20 10.2 20Z" fill="#34A853"/>
                <path d="M4.52727 11.9C4.30909 11.3 4.18182 10.6591 4.18182 10C4.18182 9.34091 4.30909 8.7 4.52727 8.1V5.50909H1.11364C0.418182 6.89091 0 8.4 0 10C0 11.6 0.418182 13.1091 1.11364 14.4909L4.52727 11.9Z" fill="#FBBC05"/>
                <path d="M10.2 3.97727C11.3636 3.97727 12.4 4.37727 13.2091 5.15455L16.1364 2.22727C15.1636 1.32727 12.9 0 10.2 0C6.19091 0 2.66818 2.40909 1.11364 5.50909L4.52727 8.1C5.37273 5.73636 7.59091 3.97727 10.2 3.97727Z" fill="#EA4335"/>
            </svg>
            <span>Đăng nhập bằng Google</span>
        </a>

        <div class="register-link">
            Chưa có tài khoản? <a href="<?php echo e(route('register')); ?>">Đăng ký ngay</a>
        </div>
    </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\CineHub_DuAnTotNghiep\resources\views/auth/login.blade.php ENDPATH**/ ?>