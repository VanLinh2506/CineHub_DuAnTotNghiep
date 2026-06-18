<?php
    $user = auth()->user();
    $isAdmin = false;
    $isModerator = false;
    
    if ($user) {
        $isAdmin = $user->isAdmin();
        $isModerator = $user->isModerator();
    }
    
    // Fetch menu categories and countries
    $menuCategories = \App\Models\Category::all();
    $countries = \App\Models\Movie::distinct()->whereNotNull('country')
        ->where('country', '!=', '')
        ->orderBy('country')
        ->pluck('country')
        ->toArray();
?>

<!-- Desktop Header -->
<header class="header-new header-desktop">
    <div class="header-container">
        <div class="header-left">
            <div class="logo-new">
                <a href="<?php echo e(route('home')); ?>">
                    <i class="fas fa-film"></i>
                    <span>CineHub</span>
                </a>
            </div>
            
            <div class="search-bar">
                <form method="GET" action="<?php echo e(route('home')); ?>" class="search-form-inline">
                    <input type="hidden" name="route" value="movie/index">
                    <input type="text" name="search" id="search-input-header" class="search-input" placeholder="Tìm kiếm phim...">
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
        
        <nav class="nav-new">
            <a href="<?php echo e(route('movies.phimle')); ?>" class="nav-link-new">
                Phim lẻ
            </a>
            <a href="<?php echo e(route('movies.phimbo')); ?>" class="nav-link-new">
                Phim bộ
            </a>
            <div class="nav-dropdown">
                <span class="nav-link-new dropdown-trigger">
                    Thể loại <i class="fas fa-chevron-down"></i>
                </span>
                <div class="dropdown-menu">
                    <?php $__currentLoopData = $menuCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('movies.category', $cat->id)); ?>" class="dropdown-item">
                            <?php echo e($cat->name); ?>

                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <div class="nav-dropdown">
                <span class="nav-link-new dropdown-trigger">
                    Quốc gia <i class="fas fa-chevron-down"></i>
                </span>
                <div class="dropdown-menu">
                    <?php $__currentLoopData = $countries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('movies.index', ['country' => $country])); ?>" class="dropdown-item">
                            <?php echo e($country); ?>

                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <a href="<?php echo e(route('movies.index')); ?>" class="nav-link-new">
                Top phim
            </a>
            <a href="<?php echo e(route('movies.theater')); ?>" class="nav-link-new" id="booking-link">
                Vé xem phim
            </a>
        </nav>
        
        <div class="header-right">
            <?php if($user): ?>
                <?php if($isAdmin): ?>
                    <a href="<?php echo e(route('admin.index')); ?>" class="sign-in-btn" style="background-color: #FFFFFF37;">
                        <i class="fas fa-cog"></i>
                        <span>Admin Panel</span>
                    </a>
                <?php elseif($isModerator): ?>
                    <a href="<?php echo e(route('moderator.index')); ?>" class="sign-in-btn">
                        <i class="fas fa-building"></i>
                        <span>Quản lý rạp</span>
                    </a>
                <?php endif; ?>
                <a href="<?php echo e(route('profile.index')); ?>" class="sign-in-btn">
                    <i class="fas fa-user"></i>
                    <span><?php echo e($user->name); ?></span>
                </a>
            <?php else: ?>
                <a href="<?php echo e(route('login')); ?>" class="sign-in-btn" data-auth-trigger="login" onclick="event.preventDefault(); openAuthModal('login'); return false;">
                    <i class="fas fa-user"></i>
                    <span>Login</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>

<!-- Mobile Header (Top Bar) -->
<header class="header-mobile">
    <div class="mobile-header-container">
        <div class="logo-new">
            <a href="<?php echo e(route('home')); ?>">
                <i class="fas fa-film"></i>
                <span>CineHub</span>
            </a>
        </div>
    </div>
</header>

<!-- Mobile Bottom Navigation -->
<style>
@media screen and (max-width: 768px) {
    .header-desktop { display: none !important; }
    .header-mobile { display: block !important; }
    .mobile-bottom-nav { display: flex !important; }
}
</style>
<nav class="mobile-bottom-nav">
    <button class="mobile-nav-item" onclick="toggleMobileMenu()">
        <i class="fas fa-bars"></i>
        <span>Menu</span>
    </button>
    <a href="<?php echo e(route('movies.index')); ?>" class="mobile-nav-item">
        <i class="fas fa-search"></i>
        <span>Tìm kiếm</span>
    </a>
    <a href="<?php echo e(route('home')); ?>" class="mobile-nav-item mobile-nav-home">
        <i class="fas fa-home"></i>
        <span>Trang chủ</span>
    </a>
    <?php if($user): ?>
        <a href="<?php echo e(route('profile.index')); ?>" class="mobile-nav-item">
            <i class="fas fa-user"></i>
            <span>Tài khoản</span>
        </a>
    <?php else: ?>
        <a href="<?php echo e(route('login')); ?>" class="mobile-nav-item" data-auth-trigger="login" onclick="event.preventDefault(); openAuthModal('login'); return false;">
            <i class="fas fa-user"></i>
            <span>Đăng nhập</span>
        </a>
    <?php endif; ?>
</nav>

<!-- Mobile Slide Menu -->
<div class="mobile-menu-overlay" id="mobileMenuOverlay" onclick="closeMobileMenu()"></div>
<div class="mobile-slide-menu" id="mobileSlideMenu">
    <div class="mobile-menu-header">
        <div class="mobile-menu-logo">
            <i class="fas fa-film"></i>
            <span>CineHub</span>
        </div>
        <button class="mobile-menu-close" onclick="closeMobileMenu()">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <?php if($user): ?>
        <div class="mobile-menu-user">
            <div class="mobile-user-avatar">
                <?php if(!empty($user->avatar)): ?>
                    <img src="<?php echo e($user->avatar); ?>" alt="Avatar">
                <?php else: ?>
                    <i class="fas fa-user"></i>
                <?php endif; ?>
            </div>
            <div class="mobile-user-info">
                <span class="mobile-user-name"><?php echo e($user->name); ?></span>
                <span class="mobile-user-email"><?php echo e($user->email); ?></span>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="mobile-menu-search">
        <form method="GET" action="<?php echo e(route('movies.index')); ?>">
            <input type="hidden" name="route" value="movie/index">
            <input type="text" name="search" placeholder="Tìm kiếm phim..." class="mobile-search-input">
            <button type="submit" class="mobile-search-btn">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
    
    <div class="mobile-menu-content">
        <div class="mobile-menu-section">
            <a href="<?php echo e(route('movies.phimle')); ?>" class="mobile-menu-link" onclick="closeMobileMenu()">
                <i class="fas fa-film"></i>
                <span>Phim lẻ</span>
            </a>
            <a href="<?php echo e(route('movies.phimbo')); ?>" class="mobile-menu-link" onclick="closeMobileMenu()">
                <i class="fas fa-tv"></i>
                <span>Phim bộ</span>
            </a>
            <a href="<?php echo e(route('movies.index')); ?>" class="mobile-menu-link" onclick="closeMobileMenu()">
                <i class="fas fa-star"></i>
                <span>Top phim</span>
            </a>
            <a href="<?php echo e(route('movies.theater')); ?>" class="mobile-menu-link" onclick="closeMobileMenu()">
                <i class="fas fa-ticket-alt"></i>
                <span>Vé xem phim</span>
            </a>
        </div>
        
        <div class="mobile-menu-section">
            <div class="mobile-menu-section-title">Thể loại</div>
            <div class="mobile-menu-tags">
                <?php $__currentLoopData = $menuCategories->take(8); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('movies.category', $cat->id)); ?>" class="mobile-menu-tag" onclick="closeMobileMenu()">
                        <?php echo e($cat->name); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        
        <div class="mobile-menu-section">
            <div class="mobile-menu-section-title">Quốc gia</div>
            <div class="mobile-menu-tags">
                <?php $__currentLoopData = array_slice($countries, 0, 6); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $country): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <a href="<?php echo e(route('movies.index', ['country' => $country])); ?>" class="mobile-menu-tag" onclick="closeMobileMenu()">
                        <?php echo e($country); ?>

                    </a>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
        
        <?php if($user): ?>
            <div class="mobile-menu-section">
                <div class="mobile-menu-section-title">Tài khoản</div>
                <a href="<?php echo e(route('profile.index')); ?>" class="mobile-menu-link" onclick="closeMobileMenu()">
                    <i class="fas fa-user-circle"></i>
                    <span>Hồ sơ của tôi</span>
                </a>
                <a href="<?php echo e(route('booking.history')); ?>" class="mobile-menu-link" onclick="closeMobileMenu()">
                    <i class="fas fa-ticket-alt"></i>
                    <span>Vé của tôi</span>
                </a>
                <?php if($isAdmin): ?>
                    <a href="<?php echo e(route('admin.index')); ?>" class="mobile-menu-link" onclick="closeMobileMenu()">
                        <i class="fas fa-cog"></i>
                        <span>Admin Panel</span>
                    </a>
                <?php elseif($isModerator): ?>
                    <a href="<?php echo e(route('moderator.index')); ?>" class="mobile-menu-link" onclick="closeMobileMenu()">
                        <i class="fas fa-building"></i>
                        <span>Quản lý rạp</span>
                    </a>
                <?php endif; ?>
                <a href="#" class="mobile-menu-link mobile-menu-logout" 
                   onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Đăng xuất</span>
                </a>
                <form id="logout-form-mobile" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                    <?php echo csrf_field(); ?>
                </form>
            </div>
        <?php else: ?>
            <div class="mobile-menu-section">
                <a href="<?php echo e(route('login')); ?>" class="mobile-menu-link mobile-menu-login" data-auth-trigger="login" data-close-mobile-menu="true" onclick="event.preventDefault(); closeMobileMenu(); openAuthModal('login'); return false;">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Đăng nhập</span>
                </a>
                <a href="<?php echo e(route('register')); ?>" class="mobile-menu-link" data-auth-trigger="register" data-close-mobile-menu="true" onclick="event.preventDefault(); closeMobileMenu(); openAuthModal('register'); return false;">
                    <i class="fas fa-user-plus"></i>
                    <span>Đăng ký</span>
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobileSlideMenu');
        const overlay = document.getElementById('mobileMenuOverlay');
        menu.classList.toggle('active');
        overlay.classList.toggle('active');
        document.body.style.overflow = menu.classList.contains('active') ? 'hidden' : '';
    }
    
    function closeMobileMenu() {
        const menu = document.getElementById('mobileSlideMenu');
        const overlay = document.getElementById('mobileMenuOverlay');
        menu.classList.remove('active');
        overlay.classList.remove('active');
        document.body.style.overflow = '';
    }
</script>

<?php if($user): ?>
    <?php
        try {
            $unreadCount = $user->notifications()->where('is_read', false)->count();
        } catch (\Exception $e) {
            $unreadCount = 0;
        }
    ?>
    <div class="notification-wrapper-fixed" id="notificationWrapper">
        <a href="javascript:void(0);" class="notification-btn-fixed" id="notificationBtnFixed" onclick="toggleNotificationDropdown()">
            <i class="fas fa-bell"></i>
            <?php if($unreadCount > 0): ?>
                <span class="notification-badge"><?php echo e($unreadCount > 99 ? '99+' : $unreadCount); ?></span>
            <?php endif; ?>
        </a>
        <div class="notification-dropdown" id="notificationDropdown" style="display: none;">
            <div class="notification-dropdown-header">
                <h6>Thông báo</h6>
                <a href="<?php echo e(route('notifications.index')); ?>" class="view-all-link">Xem tất cả</a>
            </div>
            <div class="notification-dropdown-body" id="notificationList">
                <div class="notification-loading">
                    <i class="fas fa-spinner fa-spin"></i> Đang tải...
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if(session('success')): ?>
    <div class="alert-modal alert-success-modal" id="alertModal">
        <div class="alert-modal-content">
            <i class="fas fa-check-circle"></i>
            <span><?php echo e(session('success')); ?></span>
            <button class="alert-close" onclick="closeAlertModal()">&times;</button>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showAlertModal('success', <?php echo json_encode(session('success'), 15, 512) ?>);
        });
    </script>
<?php endif; ?>

<?php if(session('error')): ?>
    <div class="alert-modal alert-error-modal" id="alertModal">
        <div class="alert-modal-content">
            <i class="fas fa-exclamation-circle"></i>
            <span><?php echo e(session('error')); ?></span>
            <button class="alert-close" onclick="closeAlertModal()">&times;</button>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showAlertModal('error', <?php echo json_encode(session('error'), 15, 512) ?>);
        });
    </script>
<?php endif; ?>

<!-- Auth Modal (Login/Register) -->
<div id="authModal" class="modal-overlay" style="display: none;">
    <div class="modal-content-login">
        <span class="modal-close" onclick="closeAuthModal()">&times;</span>
        
        <!-- Tab Navigation -->
        <div class="auth-tabs">
            <button class="auth-tab active" onclick="switchAuthTab('login')">Đăng nhập</button>
            <button class="auth-tab" onclick="switchAuthTab('register')">Đăng ký</button>
        </div>
        
        <!-- Login Form -->
        <div id="loginTab" class="auth-tab-content active">
            <h2 class="modal-title">Đăng nhập</h2>
            <div id="loginError" class="alert alert-error" style="display: none;"></div>
            <form id="loginForm" method="POST" action="<?php echo e(route('login')); ?>" onsubmit="handleLogin(event)">
                <?php echo csrf_field(); ?>
                <div class="form-group-new">
                    <input type="email" name="email" required placeholder="Email" class="input-field">
                </div>
                <div class="form-group-new">
                    <input type="password" name="password" required placeholder="Mật khẩu" class="input-field">
                </div>
                <div class="form-options">
                    <label class="checkbox-label">
                        <input type="checkbox" name="remember_me">
                        <span>Ghi nhớ đăng nhập</span>
                    </label>
                    <a href="javascript:void(0);" onclick="closeAuthModal(); openForgotPasswordModal();" class="forgot-password">Quên mật khẩu?</a>
                </div>
                <button type="submit" class="btn-login">Đăng nhập</button>
            </form>

            <!-- Divider -->
            <div class="auth-divider">
                <span>hoặc</span>
            </div>

            <!-- Google Login -->
            <a href="<?php echo e(route('auth.google')); ?>" class="btn-google-auth">
                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19.8 10.2273C19.8 9.51819 19.7364 8.83637 19.6182 8.18182H10.2V12.05H15.5818C15.3273 13.3 14.5727 14.3591 13.4455 15.0682V17.5773H16.7364C18.6091 15.8364 19.8 13.2727 19.8 10.2273Z" fill="#4285F4"/>
                    <path d="M10.2 20C12.9 20 15.1727 19.1045 16.7364 17.5773L13.4455 15.0682C12.4909 15.6682 11.2636 16.0227 10.2 16.0227C7.59091 16.0227 5.37273 14.2636 4.52727 11.9H1.11364V14.4909C2.66818 17.5909 6.19091 20 10.2 20Z" fill="#34A853"/>
                    <path d="M4.52727 11.9C4.30909 11.3 4.18182 10.6591 4.18182 10C4.18182 9.34091 4.30909 8.7 4.52727 8.1V5.50909H1.11364C0.418182 6.89091 0 8.4 0 10C0 11.6 0.418182 13.1091 1.11364 14.4909L4.52727 11.9Z" fill="#FBBC05"/>
                    <path d="M10.2 3.97727C11.3636 3.97727 12.4 4.37727 13.2091 5.15455L16.1364 2.22727C15.1636 1.32727 12.9 0 10.2 0C6.19091 0 2.66818 2.40909 1.11364 5.50909L4.52727 8.1C5.37273 5.73636 7.59091 3.97727 10.2 3.97727Z" fill="#EA4335"/>
                </svg>
                Đăng nhập bằng Google
            </a>

            <div class="modal-footer">
                <p>Chưa có tài khoản? <a href="javascript:void(0);" onclick="switchAuthTab('register');">Đăng ký ngay</a></p>
            </div>
        </div>
        
        <!-- Register Form -->
        <div id="registerTab" class="auth-tab-content" style="display: none;">
            <h2 class="modal-title">Đăng ký</h2>
            <div id="registerError" class="alert alert-error" style="display: none;"></div>
            <form id="registerForm" method="POST" action="<?php echo e(route('register')); ?>" onsubmit="handleRegister(event)">
                <?php echo csrf_field(); ?>
                <div class="form-group-new">
                    <input type="text" name="name" required placeholder="Họ và tên" class="input-field">
                </div>
                <div class="form-group-new">
                    <input type="email" name="email" required placeholder="Email" class="input-field">
                </div>
                <div class="form-group-new">
                    <input type="password" name="password" required placeholder="Mật khẩu" class="input-field">
                </div>
                <div class="form-group-new">
                    <input type="password" name="confirm_password" required placeholder="Xác nhận mật khẩu" class="input-field">
                </div>
                <button type="submit" class="btn-register-form">Đăng ký</button>
            </form>

            <!-- Divider -->
            <div class="auth-divider">
                <span>hoặc</span>
            </div>

            <!-- Google Register -->
            <a href="<?php echo e(route('auth.google')); ?>" class="btn-google-auth">
                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19.8 10.2273C19.8 9.51819 19.7364 8.83637 19.6182 8.18182H10.2V12.05H15.5818C15.3273 13.3 14.5727 14.3591 13.4455 15.0682V17.5773H16.7364C18.6091 15.8364 19.8 13.2727 19.8 10.2273Z" fill="#4285F4"/>
                    <path d="M10.2 20C12.9 20 15.1727 19.1045 16.7364 17.5773L13.4455 15.0682C12.4909 15.6682 11.2636 16.0227 10.2 16.0227C7.59091 16.0227 5.37273 14.2636 4.52727 11.9H1.11364V14.4909C2.66818 17.5909 6.19091 20 10.2 20Z" fill="#34A853"/>
                    <path d="M4.52727 11.9C4.30909 11.3 4.18182 10.6591 4.18182 10C4.18182 9.34091 4.30909 8.7 4.52727 8.1V5.50909H1.11364C0.418182 6.89091 0 8.4 0 10C0 11.6 0.418182 13.1091 1.11364 14.4909L4.52727 11.9Z" fill="#FBBC05"/>
                    <path d="M10.2 3.97727C11.3636 3.97727 12.4 4.37727 13.2091 5.15455L16.1364 2.22727C15.1636 1.32727 12.9 0 10.2 0C6.19091 0 2.66818 2.40909 1.11364 5.50909L4.52727 8.1C5.37273 5.73636 7.59091 3.97727 10.2 3.97727Z" fill="#EA4335"/>
                </svg>
                Đăng ký bằng Google
            </a>

            <div class="modal-footer">
                <p>Đã có tài khoản? <a href="javascript:void(0);" onclick="switchAuthTab('login');">Đăng nhập ngay</a></p>
            </div>
        </div>
    </div>
</div>

<style>
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 10000;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease;
    }
    
    .modal-overlay.show {
        opacity: 1;
        visibility: visible;
    }
    
    .modal-content-login {
        background: #1a1a1a;
        padding: 40px;
        border-radius: 10px;
        width: 90%;
        max-width: 450px;
        position: relative;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        transform: scale(0.7) translateY(-50px);
        opacity: 0;
        transition: transform 0.3s ease, opacity 0.3s ease;
    }
    
    .modal-overlay.show .modal-content-login {
        transform: scale(1) translateY(0);
        opacity: 1;
    }
    
    .modal-close {
        position: absolute;
        top: 15px;
        right: 20px;
        font-size: 28px;
        font-weight: bold;
        color: #fff;
        cursor: pointer;
        transition: color 0.3s;
    }
    
    .modal-close:hover {
        color: #e50914;
    }
    
    .modal-title {
        color: #fff;
        margin-bottom: 30px;
        text-align: center;
        font-size: 28px;
    }
    
    .modal-footer {
        text-align: center;
        margin-top: 20px;
        color: #999;
    }
    
    .modal-footer a {
        color: #e50914;
        text-decoration: none;
    }
    
    .alert {
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 20px;
    }
    
    .alert-error {
        background: #dc3545;
        color: white;
    }
    
    .form-group-new {
        margin-bottom: 15px;
    }
    
    .input-field {
        width: 100%;
        padding: 12px;
        background: #2a2a2a;
        border: 1px solid #444;
        border-radius: 6px;
        color: #fff;
        font-size: 16px;
    }
    
    .input-field:focus {
        outline: none;
        border-color: #e50914;
    }
    
    .form-options {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        font-size: 14px;
    }
    
    .checkbox-label {
        color: #999;
        display: flex;
        align-items: center;
        cursor: pointer;
    }
    
    .checkbox-label input {
        margin-right: 8px;
    }
    
    .forgot-password {
        color: #e50914;
        text-decoration: none;
    }
    
    .btn-login,
    .btn-register-form {
        width: 100%;
        padding: 12px;
        background: #e50914;
        border: none;
        border-radius: 6px;
        color: white;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.3s;
        margin-bottom: 0;
    }

    .btn-login:hover,
    .btn-register-form:hover {
        background: #c00812;
    }

    .auth-divider {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 18px 0;
        color: #555;
        font-size: 13px;
    }

    .auth-divider::before,
    .auth-divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #333;
    }

    .auth-divider span {
        padding: 0 12px;
    }

    .btn-google-auth {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        width: 100%;
        padding: 11px;
        background: #fff;
        border: none;
        border-radius: 6px;
        color: #333;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.2s, box-shadow 0.2s;
        box-shadow: 0 1px 4px rgba(0,0,0,0.2);
        margin-bottom: 4px;
    }

    .btn-google-auth:hover {
        background: #f1f1f1;
        box-shadow: 0 2px 8px rgba(0,0,0,0.25);
        color: #111;
        text-decoration: none;
    }
        transition: background 0.3s;
    }
    
    .btn-login:hover,
    .btn-register-form:hover {
        background: #d00913;
    }
    
    .btn-login:disabled,
    .btn-register-form:disabled {
        background: #666;
        cursor: not-allowed;
    }
    
    .auth-tabs {
        display: flex;
        margin-bottom: 30px;
        border-bottom: 2px solid #333;
    }
    
    .auth-tab {
        flex: 1;
        padding: 12px;
        background: transparent;
        border: none;
        color: #999;
        font-size: 16px;
        cursor: pointer;
        transition: color 0.3s;
    }
    
    .auth-tab.active {
        color: #e50914;
        border-bottom: 2px solid #e50914;
        margin-bottom: -2px;
    }
    
    .auth-tab-content {
        display: none;
    }
    
    .auth-tab-content.active {
        display: block;
    }
</style>

<style>
    #authModal.modal-overlay {
        background:
            radial-gradient(circle at 18% 18%, rgba(229, 9, 20, 0.24), transparent 28%),
            radial-gradient(circle at 82% 12%, rgba(255, 255, 255, 0.14), transparent 24%),
            rgba(5, 5, 8, 0.74);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        padding: 18px;
    }

    #authModal .modal-content-login {
        max-width: 460px;
        padding: 34px;
        border-radius: 100px;
        overflow: hidden;
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.16), rgba(18, 18, 24, 0.72));
        border: 1px solid rgba(255, 255, 255, 0.22);
        box-shadow: 0 24px 70px rgba(0, 0, 0, 0.55), inset 0 1px 0 rgba(255, 255, 255, 0.22);
        backdrop-filter: blur(26px) saturate(145%);
        -webkit-backdrop-filter: blur(26px) saturate(145%);
        transform: scale(0.94) translateY(22px);
        transition: transform 0.32s ease, opacity 0.32s ease;
    }

    #authModal .modal-content-login::before {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background:
            linear-gradient(120deg, rgba(255,255,255,0.2), transparent 35%, rgba(229,9,20,0.12)),
            radial-gradient(circle at 50% 0%, rgba(255,255,255,0.2), transparent 34%);
    }

    #authModal .modal-content-login > * {
        position: relative;
        z-index: 1;
    }

    #authModal.modal-overlay.show .modal-content-login {
        transform: scale(1) translateY(0);
    }

    #authModal .modal-close {
        width: 34px;
        height: 34px;
        border-radius: 100px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        color: rgba(255, 255, 255, 0.86);
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.14);
        transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
    }

    #authModal .modal-close:hover {
        color: #fff;
        background: rgba(229, 9, 20, 0.72);
        transform: rotate(90deg);
    }

    #authModal .modal-title {
        margin-bottom: 24px;
        font-weight: 800;
    }

    #authModal .auth-tabs {
        gap: 8px;
        margin-bottom: 28px;
        padding: 5px;
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 100px;
        background: rgba(0, 0, 0, 0.18);
    }

    #authModal .auth-tab {
        border-radius: 100px;
        color: rgba(255, 255, 255, 0.66);
        transition: color 0.2s ease, background 0.2s ease;
    }

    #authModal .auth-tab.active {
        color: #fff;
        background: rgba(229, 9, 20, 0.78);
        border-bottom: 0;
        margin-bottom: 0;
        box-shadow: 0 10px 22px rgba(229, 9, 20, 0.22);
    }

    #authModal .input-field {
        padding: 13px 14px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: 100px;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    #authModal .input-field:focus {
        border-color: rgba(255, 85, 98, 0.9);
        background: rgba(255, 255, 255, 0.14);
        box-shadow: 0 0 0 4px rgba(229, 9, 20, 0.16), inset 0 1px 0 rgba(255, 255, 255, 0.12);
    }

    #authModal .checkbox-label,
    #authModal .modal-footer {
        color: rgba(255, 255, 255, 0.72);
    }

    #authModal .forgot-password,
    #authModal .modal-footer a {
        color: #ff6b75;
        font-weight: 700;
    }

    #authModal .btn-login,
    #authModal .btn-register-form {
        padding: 13px;
        border-radius: 100px;
        background: linear-gradient(135deg, #ff3340, #b7030c);
        border: 1px solid rgba(255, 255, 255, 0.18);
        font-weight: 800;
        box-shadow: 0 12px 26px rgba(229, 9, 20, 0.3);
        transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
    }

    #authModal .btn-login:hover,
    #authModal .btn-register-form:hover {
        filter: brightness(1.08);
        transform: translateY(-1px);
        box-shadow: 0 16px 34px rgba(229, 9, 20, 0.38);
    }

    #authModal .btn-login:disabled,
    #authModal .btn-register-form:disabled {
        background: rgba(120, 120, 130, 0.5);
        box-shadow: none;
        transform: none;
    }

    #authModal .btn-google-auth {
        padding: 12px;
        border-radius: 100px;
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(255, 255, 255, 0.45);
        box-shadow: 0 10px 24px rgba(0,0,0,0.18);
        transition: background 0.2s, box-shadow 0.2s, transform 0.2s ease;
    }

    #authModal .btn-google-auth:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 28px rgba(0,0,0,0.24);
    }

    #authModal .alert-error {
        background: rgba(220, 53, 69, 0.24);
        border: 1px solid rgba(255, 111, 124, 0.35);
        border-radius: 100px;
    }

    #authModal .auth-tab-content.active {
        animation: authFadeIn 0.22s ease;
    }

    @keyframes authFadeIn {
        from {
            opacity: 0;
            transform: translateY(8px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @media (max-width: 520px) {
        #authModal .modal-content-login {
            padding: 28px 20px;
            border-radius: 44px;
        }

        #authModal .form-options {
            align-items: flex-start;
            flex-direction: column;
            gap: 10px;
        }
    }
</style>


<script>
// Auth Modal Functions
function openAuthModal(tab) {
    const modal = document.getElementById('authModal');
    if (modal) {
        modal.style.display = 'flex';
        modal.classList.add('show');
        document.body.style.overflow = 'hidden';
        if (tab) {
            switchAuthTab(tab);
        }
    }
}

function closeAuthModal() {
    const modal = document.getElementById('authModal');
    if (modal) {
        modal.classList.remove('show');
        setTimeout(() => {
            modal.style.display = 'none';
            document.body.style.overflow = '';
        }, 300);
    }
}

function switchAuthTab(tab) {
    const loginTab = document.getElementById('loginTab');
    const registerTab = document.getElementById('registerTab');
    const tabs = document.querySelectorAll('.auth-tab');
    
    if (tab === 'login') {
        loginTab.style.display = 'block';
        registerTab.style.display = 'none';
        loginTab.classList.add('active');
        registerTab.classList.remove('active');
        tabs[0].classList.add('active');
        tabs[1].classList.remove('active');
    } else if (tab === 'register') {
        loginTab.style.display = 'none';
        registerTab.style.display = 'block';
        loginTab.classList.remove('active');
        registerTab.classList.add('active');
        tabs[0].classList.remove('active');
        tabs[1].classList.add('active');
    }
}

// Handle Login Form Submit
function handleLogin(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const errorDiv = document.getElementById('loginError');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Disable button
    submitBtn.disabled = true;
    submitBtn.textContent = 'Đang đăng nhập...';
    errorDiv.style.display = 'none';
    
    fetch(form.action || '<?php echo e(route('login')); ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        const contentType = response.headers.get('content-type') || '';
        const data = contentType.includes('application/json') ? await response.json() : {};

        if (!response.ok) {
            const firstError = data.errors ? Object.values(data.errors)[0]?.[0] : null;
            throw new Error(firstError || data.message || 'Khong the dang nhap. Vui long kiem tra lai thong tin.');
        }

        return data;
    })
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            errorDiv.textContent = data.error || 'Đăng nhập thất bại!';
            errorDiv.style.display = 'block';
            submitBtn.disabled = false;
            submitBtn.textContent = 'Đăng nhập';
        }
    })
    .catch(error => {
        errorDiv.textContent = 'Có lỗi xảy ra. Vui lòng thử lại!';
        errorDiv.style.display = 'block';
        submitBtn.disabled = false;
        submitBtn.textContent = 'Đăng nhập';
    });
}

// Handle Register Form Submit
function handleRegister(event) {
    event.preventDefault();
    const form = event.target;
    const formData = new FormData(form);
    const errorDiv = document.getElementById('registerError');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Disable button
    submitBtn.disabled = true;
    submitBtn.textContent = 'Đang đăng ký...';
    errorDiv.style.display = 'none';
    
    fetch(form.action || '<?php echo e(route('register')); ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(async response => {
        const contentType = response.headers.get('content-type') || '';
        const data = contentType.includes('application/json') ? await response.json() : {};

        if (!response.ok) {
            const firstError = data.errors ? Object.values(data.errors)[0]?.[0] : null;
            throw new Error(firstError || data.message || 'Khong the dang ky. Vui long kiem tra lai thong tin.');
        }

        return data;
    })
    .then(data => {
        if (data.success) {
            window.location.href = data.redirect;
        } else {
            errorDiv.textContent = data.error || 'Đăng ký thất bại!';
            errorDiv.style.display = 'block';
            submitBtn.disabled = false;
            submitBtn.textContent = 'Đăng ký';
        }
    })
    .catch(error => {
        errorDiv.textContent = 'Có lỗi xảy ra. Vui lòng thử lại!';
        errorDiv.style.display = 'block';
        submitBtn.disabled = false;
        submitBtn.textContent = 'Đăng ký';
    });
}

// Close modal when clicking outside
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('authModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeAuthModal();
            }
        });
    }

    document.querySelectorAll('[data-auth-trigger]').forEach(function(link) {
        link.addEventListener('click', function(e) {
            const tab = this.getAttribute('data-auth-trigger');

            if (this.getAttribute('data-close-mobile-menu') === 'true') {
                closeMobileMenu();
            }

            if (typeof openAuthModal === 'function') {
                e.preventDefault();
                openAuthModal(tab);
                return false;
            }
        });
    });

    // Auto open modal from session
    <?php if(session('openLoginModal')): ?>
        setTimeout(function() {
            openAuthModal('login');
        }, 300);
    <?php endif; ?>

    <?php if(session('openRegisterModal')): ?>
        setTimeout(function() {
            openAuthModal('register');
        }, 300);
    <?php endif; ?>
});
</script>
<?php /**PATH D:\laragon\www\CineHub_DuAnTotNghiep\resources\views/components/header.blade.php ENDPATH**/ ?>