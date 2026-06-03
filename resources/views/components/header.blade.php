@php
    $user = auth()->user();
    $isAdmin = false;
    $isModerator = false;
    
    if ($user) {
        $isAdmin = $user->hasRole('admin') || $user->hasRole('Super Admin') || $user->hasRole('Admin');
        $isModerator = $user->hasRole('moderator') || !empty($user->theater_id);
    }
    
    // Fetch menu categories and countries
    $menuCategories = \App\Models\Category::all();
    $countries = \App\Models\Movie::distinct()->whereNotNull('country')
        ->where('country', '!=', '')
        ->orderBy('country')
        ->pluck('country')
        ->toArray();
@endphp

<!-- Desktop Header -->
<header class="header-new header-desktop">
    <div class="header-container">
        <div class="header-left">
            <div class="logo-new">
                <a href="{{ url('/') }}">
                    <i class="fas fa-film"></i>
                    <span>CineHub</span>
                </a>
            </div>
            
            <div class="search-bar">
                <form method="GET" action="{{ url('/') }}" class="search-form-inline">
                    <input type="hidden" name="route" value="movie/index">
                    <input type="text" name="search" id="search-input-header" class="search-input" placeholder="Tìm kiếm phim...">
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </div>
        
        <nav class="nav-new">
            <a href="{{ url('/?route=movie/index&type=phimle') }}" class="nav-link-new">
                Phim lẻ
            </a>
            <a href="{{ url('/?route=movie/index&type=phimbo') }}" class="nav-link-new">
                Phim bộ
            </a>
            <div class="nav-dropdown">
                <span class="nav-link-new dropdown-trigger">
                    Thể loại <i class="fas fa-chevron-down"></i>
                </span>
                <div class="dropdown-menu">
                    @foreach ($menuCategories as $cat)
                        <a href="{{ url('/?route=movie/index&category=' . $cat->id) }}" class="dropdown-item">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="nav-dropdown">
                <span class="nav-link-new dropdown-trigger">
                    Quốc gia <i class="fas fa-chevron-down"></i>
                </span>
                <div class="dropdown-menu">
                    @foreach ($countries as $country)
                        <a href="{{ url('/?route=movie/index&country=' . urlencode($country)) }}" class="dropdown-item">
                            {{ $country }}
                        </a>
                    @endforeach
                </div>
            </div>
            <a href="{{ url('/?route=movie/index') }}" class="nav-link-new">
                Top phim
            </a>
            <a href="{{ url('/?route=booking/index') }}" class="nav-link-new" id="booking-link">
                Vé xem phim
            </a>
        </nav>
        
        <div class="header-right">
            @if ($user)
                @if ($isAdmin)
                    <a href="{{ url('/?route=admin/index') }}" class="sign-in-btn" style="background-color: #FFFFFF37;">
                        <i class="fas fa-cog"></i>
                        <span>Admin Panel</span>
                    </a>
                @elseif ($isModerator)
                    <a href="{{ url('/?route=moderator/index') }}" class="sign-in-btn">
                        <i class="fas fa-building"></i>
                        <span>Quản lý rạp</span>
                    </a>
                @endif
                <a href="{{ url('/?route=profile/index') }}" class="sign-in-btn">
                    <i class="fas fa-user"></i>
                    <span>{{ $user->name }}</span>
                </a>
            @else
                <a href="#" class="sign-in-btn" onclick="event.preventDefault(); openAuthModal('login');">
                    <i class="fas fa-user"></i>
                    <span>Login</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            @endif
        </div>
    </div>
</header>

<!-- Mobile Header (Top Bar) -->
<header class="header-mobile">
    <div class="mobile-header-container">
        <div class="logo-new">
            <a href="{{ url('/') }}">
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
    <a href="{{ url('/?route=movie/index') }}" class="mobile-nav-item">
        <i class="fas fa-search"></i>
        <span>Tìm kiếm</span>
    </a>
    <a href="{{ url('/') }}" class="mobile-nav-item mobile-nav-home">
        <i class="fas fa-home"></i>
        <span>Trang chủ</span>
    </a>
    @if ($user)
        <a href="{{ url('/?route=profile/index') }}" class="mobile-nav-item">
            <i class="fas fa-user"></i>
            <span>Tài khoản</span>
        </a>
    @else
        <a href="#" class="mobile-nav-item" onclick="event.preventDefault(); openAuthModal('login');">
            <i class="fas fa-user"></i>
            <span>Đăng nhập</span>
        </a>
    @endif
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
    
    @if ($user)
        <div class="mobile-menu-user">
            <div class="mobile-user-avatar">
                @if (!empty($user->avatar))
                    <img src="{{ $user->avatar }}" alt="Avatar">
                @else
                    <i class="fas fa-user"></i>
                @endif
            </div>
            <div class="mobile-user-info">
                <span class="mobile-user-name">{{ $user->name }}</span>
                <span class="mobile-user-email">{{ $user->email }}</span>
            </div>
        </div>
    @endif
    
    <div class="mobile-menu-search">
        <form method="GET" action="{{ url('/?route=movie/index') }}">
            <input type="hidden" name="route" value="movie/index">
            <input type="text" name="search" placeholder="Tìm kiếm phim..." class="mobile-search-input">
            <button type="submit" class="mobile-search-btn">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
    
    <div class="mobile-menu-content">
        <div class="mobile-menu-section">
            <a href="{{ url('/?route=movie/index&type=phimle') }}" class="mobile-menu-link" onclick="closeMobileMenu()">
                <i class="fas fa-film"></i>
                <span>Phim lẻ</span>
            </a>
            <a href="{{ url('/?route=movie/index&type=phimbo') }}" class="mobile-menu-link" onclick="closeMobileMenu()">
                <i class="fas fa-tv"></i>
                <span>Phim bộ</span>
            </a>
            <a href="{{ url('/?route=movie/index') }}" class="mobile-menu-link" onclick="closeMobileMenu()">
                <i class="fas fa-star"></i>
                <span>Top phim</span>
            </a>
            <a href="{{ url('/?route=booking/index') }}" class="mobile-menu-link" onclick="closeMobileMenu()">
                <i class="fas fa-ticket-alt"></i>
                <span>Vé xem phim</span>
            </a>
        </div>
        
        <div class="mobile-menu-section">
            <div class="mobile-menu-section-title">Thể loại</div>
            <div class="mobile-menu-tags">
                @foreach ($menuCategories->take(8) as $cat)
                    <a href="{{ url('/?route=movie/index&category=' . $cat->id) }}" class="mobile-menu-tag" onclick="closeMobileMenu()">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </div>
        
        <div class="mobile-menu-section">
            <div class="mobile-menu-section-title">Quốc gia</div>
            <div class="mobile-menu-tags">
                @foreach (array_slice($countries, 0, 6) as $country)
                    <a href="{{ url('/?route=movie/index&country=' . urlencode($country)) }}" class="mobile-menu-tag" onclick="closeMobileMenu()">
                        {{ $country }}
                    </a>
                @endforeach
            </div>
        </div>
        
        @if ($user)
            <div class="mobile-menu-section">
                <div class="mobile-menu-section-title">Tài khoản</div>
                <a href="{{ url('/?route=profile/index') }}" class="mobile-menu-link" onclick="closeMobileMenu()">
                    <i class="fas fa-user-circle"></i>
                    <span>Hồ sơ của tôi</span>
                </a>
                <a href="{{ url('/?route=booking/myTickets') }}" class="mobile-menu-link" onclick="closeMobileMenu()">
                    <i class="fas fa-ticket-alt"></i>
                    <span>Vé của tôi</span>
                </a>
                @if ($isAdmin)
                    <a href="{{ url('/?route=admin/index') }}" class="mobile-menu-link" onclick="closeMobileMenu()">
                        <i class="fas fa-cog"></i>
                        <span>Admin Panel</span>
                    </a>
                @elseif ($isModerator)
                    <a href="{{ url('/?route=moderator/index') }}" class="mobile-menu-link" onclick="closeMobileMenu()">
                        <i class="fas fa-building"></i>
                        <span>Quản lý rạp</span>
                    </a>
                @endif
                <a href="{{ url('/?route=auth/logout') }}" class="mobile-menu-link mobile-menu-logout" onclick="closeMobileMenu()">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Đăng xuất</span>
                </a>
            </div>
        @else
            <div class="mobile-menu-section">
                <a href="#" class="mobile-menu-link mobile-menu-login" onclick="event.preventDefault(); closeMobileMenu(); openAuthModal('login');">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Đăng nhập</span>
                </a>
                <a href="#" class="mobile-menu-link" onclick="event.preventDefault(); closeMobileMenu(); openAuthModal('register');">
                    <i class="fas fa-user-plus"></i>
                    <span>Đăng ký</span>
                </a>
            </div>
        @endif
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

@if ($user)
    @php
        $unreadCount = $user->notifications()->where('is_read', false)->count();
    @endphp
    <div class="notification-wrapper-fixed" id="notificationWrapper">
        <a href="javascript:void(0);" class="notification-btn-fixed" id="notificationBtnFixed" onclick="toggleNotificationDropdown()">
            <i class="fas fa-bell"></i>
            @if ($unreadCount > 0)
                <span class="notification-badge">{{ $unreadCount > 99 ? '99+' : $unreadCount }}</span>
            @endif
        </a>
        <div class="notification-dropdown" id="notificationDropdown" style="display: none;">
            <div class="notification-dropdown-header">
                <h6>Thông báo</h6>
                <a href="{{ url('/?route=notifications/index') }}" class="view-all-link">Xem tất cả</a>
            </div>
            <div class="notification-dropdown-body" id="notificationList">
                <div class="notification-loading">
                    <i class="fas fa-spinner fa-spin"></i> Đang tải...
                </div>
            </div>
        </div>
    </div>
@endif

@if (session('success'))
    <div class="alert-modal alert-success-modal" id="alertModal">
        <div class="alert-modal-content">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
            <button class="alert-close" onclick="closeAlertModal()">&times;</button>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showAlertModal('success', @json(session('success')));
        });
    </script>
@endif

@if (session('error'))
    <div class="alert-modal alert-error-modal" id="alertModal">
        <div class="alert-modal-content">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
            <button class="alert-close" onclick="closeAlertModal()">&times;</button>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showAlertModal('error', @json(session('error')));
        });
    </script>
@endif

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
            <form id="loginForm" method="POST" action="{{ url('/?route=auth/login') }}">
                @csrf
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
                    <a href="#" class="forgot-password">Quên mật khẩu?</a>
                </div>
                <button type="submit" class="btn-login">Đăng nhập</button>
            </form>
            <div class="modal-footer">
                <p>Chưa có tài khoản? <a href="#" onclick="event.preventDefault(); switchAuthTab('register');">Đăng ký ngay</a></p>
            </div>
        </div>
        
        <!-- Register Form -->
        <div id="registerTab" class="auth-tab-content" style="display: none;">
            <h2 class="modal-title">Đăng ký</h2>
            <div id="registerError" class="alert alert-error" style="display: none;"></div>
            <form id="registerForm" method="POST" action="{{ url('/?route=auth/register') }}">
                @csrf
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
                <p>Đã có tài khoản? <a href="#" onclick="event.preventDefault(); switchAuthTab('login');">Đăng nhập ngay</a></p>
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
</style>
