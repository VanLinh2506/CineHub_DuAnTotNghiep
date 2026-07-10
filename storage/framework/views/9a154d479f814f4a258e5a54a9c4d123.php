<?php
    $user = auth()->user();
    $isAdmin = false;
    $isModerator = false;
    $isCounterStaff = false;
    
    if ($user) {
        $isAdmin = $user->isAdmin();
        $isModerator = $user->isModerator();
        $isCounterStaff = $user->isCounterStaff();
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
            <a href="<?php echo e(route('movies.library.index')); ?>" class="nav-link-new">
                Kho phim của tôi
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
                <?php elseif($isCounterStaff): ?>
                    <a href="<?php echo e(route('counter.index')); ?>" class="sign-in-btn">
                        <i class="fas fa-user-tie"></i>
                        <span>Quản lý quầy</span>
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
                    <img src="<?php echo e($user->avatar_url); ?>" alt="Avatar">
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
            <a href="<?php echo e(route('movies.library.index')); ?>" class="mobile-menu-link" onclick="closeMobileMenu()">
                <i class="fas fa-layer-group"></i>
                <span>Kho phim của tôi</span>
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
                <?php elseif($isCounterStaff): ?>
                    <a href="<?php echo e(route('counter.index')); ?>" class="mobile-menu-link" onclick="closeMobileMenu()">
                        <i class="fas fa-user-tie"></i>
                        <span>Quản lý quầy</span>
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

<!-- Auth Modal (Login/Register) - Glassmorphism Design v2.1 -->
<div id="authModal" class="modal-overlay" style="display: none;">
    <div class="modal-content-glass">
        <span class="modal-close-glass" onclick="closeAuthModal()">×</span>
        
        <!-- Tab Navigation -->
        <div class="auth-tabs-glass">
            <button id="loginTabBtn" class="auth-tab-glass active" onclick="switchAuthTab('login')">Đăng nhập</button>
            <button id="registerTabBtn" class="auth-tab-glass" onclick="switchAuthTab('register')">Đăng ký</button>
        </div>
        
        <!-- Login Form -->
        <div id="loginTab" class="auth-tab-content active">
            <h2 class="modal-title-glass">Đăng nhập</h2>
            <div id="loginError" class="alert alert-error" style="display: none;"></div>
            
            <form id="loginForm" method="POST" action="<?php echo e(route('login')); ?>" onsubmit="handleLogin(event)">
                <?php echo csrf_field(); ?>
                <div class="form-group-glass">
                    <input type="email" name="email" required placeholder="Email" class="input-glass">
                </div>
                <div class="form-group-glass">
                    <input type="password" name="password" required placeholder="Mật khẩu" class="input-glass">
                </div>
                <div class="form-options-glass">
                    <label class="checkbox-glass">
                        <input type="checkbox" name="remember_me">
                        <span>Ghi nhớ đăng nhập</span>
                    </label>
                    <a href="javascript:void(0);" onclick="closeAuthModal(); alert('Chức năng đang phát triển');" class="forgot-link-glass">Quên mật khẩu?</a>
                </div>
                <button type="submit" class="btn-glass btn-primary-glass">Đăng nhập</button>
            </form>

            <div class="divider-glass"><span>hoặc</span></div>

            <a href="<?php echo e(route('auth.google')); ?>" class="btn-glass btn-google-glass">
                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19.8 10.2273C19.8 9.51819 19.7364 8.83637 19.6182 8.18182H10.2V12.05H15.5818C15.3273 13.3 14.5727 14.3591 13.4455 15.0682V17.5773H16.7364C18.6091 15.8364 19.8 13.2727 19.8 10.2273Z" fill="#4285F4"/>
                    <path d="M10.2 20C12.9 20 15.1727 19.1045 16.7364 17.5773L13.4455 15.0682C12.4909 15.6682 11.2636 16.0227 10.2 16.0227C7.59091 16.0227 5.37273 14.2636 4.52727 11.9H1.11364V14.4909C2.66818 17.5909 6.19091 20 10.2 20Z" fill="#34A853"/>
                    <path d="M4.52727 11.9C4.30909 11.3 4.18182 10.6591 4.18182 10C4.18182 9.34091 4.30909 8.7 4.52727 8.1V5.50909H1.11364C0.418182 6.89091 0 8.4 0 10C0 11.6 0.418182 13.1091 1.11364 14.4909L4.52727 11.9Z" fill="#FBBC05"/>
                    <path d="M10.2 3.97727C11.3636 3.97727 12.4 4.37727 13.2091 5.15455L16.1364 2.22727C15.1636 1.32727 12.9 0 10.2 0C6.19091 0 2.66818 2.40909 1.11364 5.50909L4.52727 8.1C5.37273 5.73636 7.59091 3.97727 10.2 3.97727Z" fill="#EA4335"/>
                </svg>
                Đăng nhập bằng Google
            </a>

            <div class="modal-footer-glass">
                <p>Chưa có tài khoản? <a href="javascript:void(0);" onclick="switchAuthTab('register');">Đăng ký ngay</a></p>
            </div>
        </div>
        
        <!-- Register Form -->
        <div id="registerTab" class="auth-tab-content" style="display: none;">
            <h2 class="modal-title-glass">Đăng ký</h2>
            <div id="registerError" class="alert alert-error" style="display: none;"></div>
            
            <form id="registerForm" method="POST" action="<?php echo e(route('register')); ?>" onsubmit="handleRegister(event)">
                <?php echo csrf_field(); ?>
                <div class="form-group-glass">
                    <input type="text" name="name" required placeholder="Họ và tên" class="input-glass">
                </div>
                <div class="form-group-glass">
                    <input type="email" name="email" required placeholder="Email" class="input-glass">
                </div>
                <div class="form-group-glass">
                    <input type="password" name="password" required placeholder="Mật khẩu" class="input-glass">
                </div>
                <div class="form-group-glass">
                    <input type="password" name="confirm_password" required placeholder="Xác nhận mật khẩu" class="input-glass">
                </div>
                <button type="submit" class="btn-glass btn-primary-glass">Đăng ký</button>
            </form>

            <div class="divider-glass"><span>hoặc</span></div>

            <a href="<?php echo e(route('auth.google')); ?>" class="btn-glass btn-google-glass">
                <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M19.8 10.2273C19.8 9.51819 19.7364 8.83637 19.6182 8.18182H10.2V12.05H15.5818C15.3273 13.3 14.5727 14.3591 13.4455 15.0682V17.5773H16.7364C18.6091 15.8364 19.8 13.2727 19.8 10.2273Z" fill="#4285F4"/>
                    <path d="M10.2 20C12.9 20 15.1727 19.1045 16.7364 17.5773L13.4455 15.0682C12.4909 15.6682 11.2636 16.0227 10.2 16.0227C7.59091 16.0227 5.37273 14.2636 4.52727 11.9H1.11364V14.4909C2.66818 17.5909 6.19091 20 10.2 20Z" fill="#34A853"/>
                    <path d="M4.52727 11.9C4.30909 11.3 4.18182 10.6591 4.18182 10C4.18182 9.34091 4.30909 8.7 4.52727 8.1V5.50909H1.11364C0.418182 6.89091 0 8.4 0 10C0 11.6 0.418182 13.1091 1.11364 14.4909L4.52727 11.9Z" fill="#FBBC05"/>
                    <path d="M10.2 3.97727C11.3636 3.97727 12.4 4.37727 13.2091 5.15455L16.1364 2.22727C15.1636 1.32727 12.9 0 10.2 0C6.19091 0 2.66818 2.40909 1.11364 5.50909L4.52727 8.1C5.37273 5.73636 7.59091 3.97727 10.2 3.97727Z" fill="#EA4335"/>
                </svg>
                Đăng ký bằng Google
            </a>

            <div class="modal-footer-glass">
                <p>Đã có tài khoản? <a href="javascript:void(0);" onclick="switchAuthTab('login');">Đăng nhập ngay</a></p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Base Modal Overlay - used by both old and new styles */
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

    /* Alert Styles */
    .alert {
        padding: 12px;
        border-radius: 6px;
        margin-bottom: 20px;
    }
    
    .alert-error {
        background: rgba(220, 53, 69, 0.24);
        border: 1px solid rgba(255, 111, 124, 0.35);
        border-radius: 100px;
        color: white;
    }
</style>

<style>
    /* Glassmorphism Auth Modal Styles */
    #authModal.modal-overlay {
        background:
            radial-gradient(circle at 18% 18%, rgba(229, 9, 20, 0.24), transparent 28%),
            radial-gradient(circle at 82% 12%, rgba(255, 255, 255, 0.14), transparent 24%),
            rgba(5, 5, 8, 0.74);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        padding: 18px;
    }

    .modal-content-glass {
        max-width: 420px;
        width: 90%;
        padding: 32px 28px;
        border-radius: 32px;
        overflow: hidden;
        background: linear-gradient(145deg, rgba(255, 255, 255, 0.16), rgba(18, 18, 24, 0.72));
        border: 1px solid rgba(255, 255, 255, 0.22);
        box-shadow: 0 24px 70px rgba(0, 0, 0, 0.55), inset 0 1px 0 rgba(255, 255, 255, 0.22);
        backdrop-filter: blur(26px) saturate(145%);
        -webkit-backdrop-filter: blur(26px) saturate(145%);
        transform: scale(0.94) translateY(22px);
        opacity: 0;
        transition: transform 0.32s ease, opacity 0.32s ease;
        position: relative;
    }

    .modal-content-glass::before {
        content: '';
        position: absolute;
        inset: 0;
        pointer-events: none;
        background:
            linear-gradient(120deg, rgba(255,255,255,0.2), transparent 35%, rgba(229,9,20,0.12)),
            radial-gradient(circle at 50% 0%, rgba(255,255,255,0.2), transparent 34%);
    }

    .modal-content-glass > * {
        position: relative;
        z-index: 1;
    }

    #authModal.modal-overlay.show .modal-content-glass {
        transform: scale(1) translateY(0);
        opacity: 1;
    }

    .modal-close-glass {
        position: absolute;
        top: 12px;
        right: 16px;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: rgba(255, 255, 255, 0.86);
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.14);
        cursor: pointer;
        transition: background 0.2s ease, color 0.2s ease, transform 0.2s ease;
    }

    .modal-close-glass:hover {
        color: #fff;
        background: rgba(229, 9, 20, 0.72);
        transform: rotate(90deg);
    }

    .modal-title-glass {
        color: #fff;
        margin-bottom: 20px;
        text-align: center;
        font-size: 26px;
        font-weight: 800;
    }

    .auth-tabs-glass {
        display: flex;
        gap: 6px;
        margin-bottom: 24px;
        padding: 4px;
        border: 1px solid rgba(255, 255, 255, 0.14);
        border-radius: 50px;
        background: rgba(0, 0, 0, 0.18);
    }

    .auth-tab-glass {
        flex: 1;
        padding: 10px;
        border-radius: 50px;
        background: transparent;
        border: none;
        color: rgba(255, 255, 255, 0.66);
        font-size: 15px;
        cursor: pointer;
        transition: color 0.2s ease, background 0.2s ease;
    }

    .auth-tab-glass.active {
        color: #fff !important;
        background: rgba(229, 9, 20, 0.78) !important;
        box-shadow: 0 10px 22px rgba(229, 9, 20, 0.22) !important;
    }

    .form-group-glass {
        margin-bottom: 12px;
    }

    .input-glass {
        width: 100%;
        padding: 12px 16px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.16);
        border-radius: 50px;
        color: #fff;
        font-size: 15px;
        box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.08);
        transition: border-color 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
    }

    .input-glass::placeholder {
        color: rgba(255, 255, 255, 0.5);
    }

    .input-glass:focus {
        outline: none;
        border-color: rgba(255, 85, 98, 0.9);
        background: rgba(255, 255, 255, 0.14);
        box-shadow: 0 0 0 4px rgba(229, 9, 20, 0.16), inset 0 1px 0 rgba(255, 255, 255, 0.12);
    }

    .form-options-glass {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        font-size: 13px;
    }

    .checkbox-glass {
        color: rgba(255, 255, 255, 0.72);
        display: flex;
        align-items: center;
        cursor: pointer;
    }

    .checkbox-glass input {
        margin-right: 8px;
        cursor: pointer;
    }

    .forgot-link-glass {
        color: #ff6b75;
        text-decoration: none;
        font-weight: 700;
        transition: color 0.2s ease;
    }

    .forgot-link-glass:hover {
        color: #ff8a92;
    }

    .btn-glass {
        width: 100%;
        padding: 12px;
        border-radius: 50px;
        font-size: 15px;
        font-weight: 700;
        cursor: pointer;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        transition: transform 0.2s ease, box-shadow 0.2s ease, filter 0.2s ease;
    }

    .btn-primary-glass {
        background: linear-gradient(135deg, #ff3340, #b7030c);
        border: 1px solid rgba(255, 255, 255, 0.18);
        color: white;
        box-shadow: 0 12px 26px rgba(229, 9, 20, 0.3);
    }

    .btn-primary-glass:hover {
        filter: brightness(1.08);
        transform: translateY(-1px);
        box-shadow: 0 16px 34px rgba(229, 9, 20, 0.38);
    }

    .btn-primary-glass:disabled {
        background: rgba(120, 120, 130, 0.5);
        box-shadow: none;
        transform: none;
        cursor: not-allowed;
    }

    .btn-google-glass {
        background: rgba(255, 255, 255, 0.92);
        border: 1px solid rgba(255, 255, 255, 0.45);
        color: #333;
        box-shadow: 0 10px 24px rgba(0,0,0,0.18);
    }

    .btn-google-glass:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 28px rgba(0,0,0,0.24);
        color: #111;
    }

    .divider-glass {
        display: flex;
        align-items: center;
        text-align: center;
        margin: 16px 0;
        color: rgba(255, 255, 255, 0.4);
        font-size: 12px;
    }

    .divider-glass::before,
    .divider-glass::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2);
    }

    .divider-glass span {
        padding: 0 12px;
    }

    .modal-footer-glass {
        text-align: center;
        margin-top: 16px;
        color: rgba(255, 255, 255, 0.72);
        font-size: 13px;
    }

    .modal-footer-glass a {
        color: #ff6b75;
        text-decoration: none;
        font-weight: 700;
        transition: color 0.2s ease;
    }

    .modal-footer-glass a:hover {
        color: #ff8a92;
    }

    .auth-tab-content {
        display: none;
    }

    .auth-tab-content.active {
        display: block;
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
        .modal-content-glass {
            padding: 24px 18px;
            border-radius: 28px;
        }

        .form-options-glass {
            align-items: flex-start;
            flex-direction: column;
            gap: 8px;
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
    console.log('switchAuthTab called with:', tab);
    
    const loginTab = document.getElementById('loginTab');
    const registerTab = document.getElementById('registerTab');
    const loginTabBtn = document.getElementById('loginTabBtn');
    const registerTabBtn = document.getElementById('registerTabBtn');
    
    console.log('Elements found:', {
        loginTab: !!loginTab,
        registerTab: !!registerTab,
        loginTabBtn: !!loginTabBtn,
        registerTabBtn: !!registerTabBtn
    });
    
    if (tab === 'login') {
        // Show/hide content
        loginTab.style.display = 'block';
        registerTab.style.display = 'none';
        loginTab.classList.add('active');
        registerTab.classList.remove('active');
        
        // Update tab buttons
        loginTabBtn.classList.add('active');
        registerTabBtn.classList.remove('active');
        console.log('Login tab activated');
    } else if (tab === 'register') {
        // Show/hide content
        loginTab.style.display = 'none';
        registerTab.style.display = 'block';
        loginTab.classList.remove('active');
        registerTab.classList.add('active');
        
        // Update tab buttons
        loginTabBtn.classList.remove('active');
        registerTabBtn.classList.add('active');
        console.log('Register tab activated');
        console.log('Register button classes:', registerTabBtn.classList);
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
            throw new Error(firstError || data.message || data.error || 'Không thể đăng ký. Vui lòng kiểm tra lại thông tin.');
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
        errorDiv.textContent = error.message || 'Có lỗi xảy ra. Vui lòng thử lại!';
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
<?php /**PATH C:\xampp\htdocs\CineHub_DuAnTotNghiep\resources\views/components/header.blade.php ENDPATH**/ ?>