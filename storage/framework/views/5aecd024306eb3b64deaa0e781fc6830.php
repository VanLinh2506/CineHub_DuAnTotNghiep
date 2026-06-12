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
            <form id="loginForm" onsubmit="handleLogin(event)">
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
            <div class="modal-footer">
                <p>Chưa có tài khoản? <a href="javascript:void(0);" onclick="switchAuthTab('register');">Đăng ký ngay</a></p>
            </div>
        </div>
        
        <!-- Register Form -->
        <div id="registerTab" class="auth-tab-content" style="display: none;">
            <h2 class="modal-title">Đăng ký</h2>
            <div id="registerError" class="alert alert-error" style="display: none;"></div>
            <form id="registerForm" onsubmit="handleRegister(event)">
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
    
    fetch('<?php echo e(route('login')); ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
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
    
    fetch('<?php echo e(route('register')); ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
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
                openAuthModal(tab || 'login');
            }
        });
    });
    
    // Auto open modal if redirected from login/register route
    <?php if(session('openAuthModal')): ?>
        openAuthModal('<?php echo e(session('openAuthModal')); ?>');
    <?php endif; ?>
});
</script>
<?php /**PATH D:\laragon\www\CineHub_DuAnTotNghiep\resources\views/components/header.blade.php ENDPATH**/ ?>