@php
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
@endphp

<!-- Desktop Header -->
<header class="header-new header-desktop">
    <div class="header-container">
        <div class="header-left">
            <div class="logo-new">
                <a href="{{ route('home') }}">
                    <i class="fas fa-film"></i>
                    <span>CineHub</span>
                </a>
            </div>
            
            <div class="search-bar">
                <form method="GET" action="{{ route('search') }}" class="search-form-inline" data-movie-search>
                    <input type="text" name="search" id="search-input-header" class="search-input" value="{{ request('search') }}" placeholder="Tìm kiếm phim..." autocomplete="off" aria-expanded="false" aria-controls="headerSearchDropdown">
                    <button type="submit" class="search-btn">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
                <div class="header-search-dropdown" id="headerSearchDropdown" hidden>
                    <div class="search-history-block" id="headerSearchHistory"></div>
                    <div class="search-suggestion-block">
                        <div class="search-dropdown-title"><span>Phim gợi ý</span><i class="fas fa-sparkles"></i></div>
                        <div id="headerMovieSuggestions" class="header-movie-suggestions"></div>
                    </div>
                </div>
            </div>
        </div>
        
        <nav class="nav-new">
            <a href="{{ route('movies.phimle') }}" class="nav-link-new">
                Phim lẻ
            </a>
            <a href="{{ route('movies.phimbo') }}" class="nav-link-new">
                Phim bộ
            </a>
            <div class="nav-dropdown">
                <span class="nav-link-new dropdown-trigger">
                    Thể loại <i class="fas fa-chevron-down"></i>
                </span>
                <div class="dropdown-menu">
                    @foreach ($menuCategories as $cat)
                        <a href="{{ route('movies.category', $cat->id) }}" class="dropdown-item">
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
                        <a href="{{ route('movies.index', ['country' => $country]) }}" class="dropdown-item">
                            {{ $country }}
                        </a>
                    @endforeach
                </div>
            </div>
            <a href="{{ route('movies.library.index') }}" class="nav-link-new">
                Kho phim của tôi
            </a>
            <a href="{{ route('movies.theater') }}" class="nav-link-new" id="booking-link">
                Vé xem phim
            </a>
        </nav>
        
        <div class="header-right">
            @if ($user)
                @if ($isAdmin)
                    <a href="{{ route('admin.index') }}" class="sign-in-btn" style="background-color: #FFFFFF37;">
                        <i class="fas fa-cog"></i>
                        <span>Admin Panel</span>
                    </a>
                @elseif ($isModerator)
                    <a href="{{ route('moderator.index') }}" class="sign-in-btn">
                        <i class="fas fa-building"></i>
                        <span>Quản lý rạp</span>
                    </a>
                @elseif ($isCounterStaff)
                    <a href="{{ route('counter.index') }}" class="sign-in-btn">
                        <i class="fas fa-user-tie"></i>
                        <span>Quản lý quầy</span>
                    </a>
                @endif
                <a href="{{ route('profile.index') }}" class="sign-in-btn">
                    <i class="fas fa-user"></i>
                    <span>{{ $user->name }}</span>
                </a>
            @else
                <a href="{{ route('login') }}" class="sign-in-btn" data-auth-trigger="login" onclick="event.preventDefault(); openAuthModal('login'); return false;">
                    <i class="fas fa-user"></i>
                    <span>Login</span>
                    <i class="fas fa-arrow-right"></i>
                </a>
            @endif
        </div>
    </div>
</header>

<style>
    .header-desktop .header-left { position:relative; z-index:3; transition: flex-basis .28s ease; }
    .header-desktop .header-left.search-expanded { flex-basis: 520px; }
    .header-desktop .nav-new { position:relative; z-index:1; transition:filter .25s ease, opacity .25s ease, transform .25s ease; }
    .header-desktop .search-bar { flex: 0 0 170px; width: 170px; max-width: 170px; transition: width .28s ease, max-width .28s ease, flex-basis .28s ease, transform .25s ease; z-index: 4; }
    .header-desktop .search-bar.search-active { flex-basis: 390px; width: 390px; max-width: 390px; z-index:20; transform:translateY(-3px); }
    .header-desktop .search-bar.search-active .search-input { background: rgba(21,22,27,.96); border-color: rgba(255,255,255,.35); box-shadow: 0 12px 35px rgba(0,0,0,.3); }
    .header-desktop.search-mode .nav-new { filter:blur(4px); opacity:.24; transform:scale(.985); pointer-events:none; user-select:none; }
    .header-search-dropdown { position:absolute; top:calc(100% + 10px); left:0; width:100%; padding:12px; border:1px solid rgba(255,255,255,.12); border-radius:17px; color:#fff; background:rgba(17,18,23,.97); box-shadow:0 22px 60px rgba(0,0,0,.55); backdrop-filter:blur(18px); }
    .header-search-dropdown[hidden] { display:none; }
    .search-dropdown-title { display:flex; align-items:center; justify-content:space-between; gap:10px; margin-bottom:8px; color:#aaa; font-size:.72rem; font-weight:800; letter-spacing:.08em; text-transform:uppercase; }
    .search-dropdown-title i { color:#ff4d5a; }
    .search-history-block:empty { display:none; }
    .search-history-block:not(:empty) { padding-bottom:10px; margin-bottom:10px; border-bottom:1px solid rgba(255,255,255,.08); }
    .search-history-head { display:flex; align-items:center; justify-content:space-between; margin-bottom:8px; }
    .search-history-head span { color:#aaa; font-size:.72rem; font-weight:800; letter-spacing:.08em; text-transform:uppercase; }
    .search-history-clear { padding:0; border:0; color:#ff6873; background:none; font-size:.72rem; cursor:pointer; }
    .search-history-list { display:flex; flex-wrap:wrap; gap:6px; }
    .search-history-item { max-width:100%; padding:6px 9px; border:1px solid rgba(255,255,255,.1); border-radius:999px; color:#ddd; background:rgba(255,255,255,.06); font-size:.78rem; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; cursor:pointer; }
    .search-history-item:hover { color:#fff; border-color:rgba(229,9,20,.55); background:rgba(229,9,20,.16); }
    .header-movie-suggestions { display:grid; gap:6px; }
    .header-movie-suggestion { display:grid; grid-template-columns:42px minmax(0,1fr) auto; gap:10px; align-items:center; padding:7px; border-radius:11px; color:#fff; text-decoration:none; transition:background .18s ease, transform .18s ease; }
    .header-movie-suggestion:hover { color:#fff; background:rgba(255,255,255,.08); transform:translateX(2px); }
    .header-movie-poster { width:42px; height:58px; object-fit:cover; border-radius:8px; background:#292a30; }
    .header-movie-poster-empty { width:42px; height:58px; display:grid; place-items:center; border-radius:8px; color:#777; background:#292a30; }
    .header-movie-info { min-width:0; }
    .header-movie-title { display:block; overflow:hidden; color:#fff; font-size:.84rem; font-weight:750; text-overflow:ellipsis; white-space:nowrap; }
    .header-movie-meta { display:flex; gap:7px; margin-top:4px; color:#999; font-size:.7rem; }
    .header-movie-meta i { color:#ffc43d; }
    .header-movie-level { padding:4px 7px; border-radius:999px; color:#ff7b85; background:rgba(229,9,20,.13); font-size:.67rem; font-weight:800; }
    .header-search-state { padding:12px 7px; color:#8f9096; text-align:center; font-size:.8rem; }
    @media (max-width:1200px) {
        .header-desktop .header-left.search-expanded { flex-basis:430px; }
        .header-desktop .search-bar.search-active { flex-basis:320px; width:320px; max-width:320px; }
    }
</style>

<!-- Mobile Header (Top Bar) -->
<header class="header-mobile">
    <div class="mobile-header-container">
        <div class="logo-new">
            <a href="{{ route('home') }}">
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
    <a href="{{ route('movies.index') }}" class="mobile-nav-item">
        <i class="fas fa-search"></i>
        <span>Tìm kiếm</span>
    </a>
    <a href="{{ route('home') }}" class="mobile-nav-item mobile-nav-home">
        <i class="fas fa-home"></i>
        <span>Trang chủ</span>
    </a>
    @if ($user)
        <a href="{{ route('profile.index') }}" class="mobile-nav-item">
            <i class="fas fa-user"></i>
            <span>Tài khoản</span>
        </a>
    @else
        <a href="{{ route('login') }}" class="mobile-nav-item" data-auth-trigger="login" onclick="event.preventDefault(); openAuthModal('login'); return false;">
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
                    <img src="{{ $user->avatar_url }}" alt="Avatar">
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
        <form method="GET" action="{{ route('search') }}" data-movie-search>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Tìm kiếm phim..." class="mobile-search-input">
            <button type="submit" class="mobile-search-btn">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>
    
    <div class="mobile-menu-content">
        <div class="mobile-menu-section">
            <a href="{{ route('movies.phimle') }}" class="mobile-menu-link" onclick="closeMobileMenu()">
                <i class="fas fa-film"></i>
                <span>Phim lẻ</span>
            </a>
            <a href="{{ route('movies.phimbo') }}" class="mobile-menu-link" onclick="closeMobileMenu()">
                <i class="fas fa-tv"></i>
                <span>Phim bộ</span>
            </a>
            <a href="{{ route('movies.library.index') }}" class="mobile-menu-link" onclick="closeMobileMenu()">
                <i class="fas fa-layer-group"></i>
                <span>Kho phim của tôi</span>
            </a>
            <a href="{{ route('movies.theater') }}" class="mobile-menu-link" onclick="closeMobileMenu()">
                <i class="fas fa-ticket-alt"></i>
                <span>Vé xem phim</span>
            </a>
        </div>
        
        <div class="mobile-menu-section">
            <div class="mobile-menu-section-title">Thể loại</div>
            <div class="mobile-menu-tags">
                @foreach ($menuCategories->take(8) as $cat)
                    <a href="{{ route('movies.category', $cat->id) }}" class="mobile-menu-tag" onclick="closeMobileMenu()">
                        {{ $cat->name }}
                    </a>
                @endforeach
            </div>
        </div>
        
        <div class="mobile-menu-section">
            <div class="mobile-menu-section-title">Quốc gia</div>
            <div class="mobile-menu-tags">
                @foreach (array_slice($countries, 0, 6) as $country)
                    <a href="{{ route('movies.index', ['country' => $country]) }}" class="mobile-menu-tag" onclick="closeMobileMenu()">
                        {{ $country }}
                    </a>
                @endforeach
            </div>
        </div>
        
        @if ($user)
            <div class="mobile-menu-section">
                <div class="mobile-menu-section-title">Tài khoản</div>
                <a href="{{ route('profile.index') }}" class="mobile-menu-link" onclick="closeMobileMenu()">
                    <i class="fas fa-user-circle"></i>
                    <span>Hồ sơ của tôi</span>
                </a>
                <a href="{{ route('booking.history') }}" class="mobile-menu-link" onclick="closeMobileMenu()">
                    <i class="fas fa-ticket-alt"></i>
                    <span>Vé của tôi</span>
                </a>
                @if ($isAdmin)
                    <a href="{{ route('admin.index') }}" class="mobile-menu-link" onclick="closeMobileMenu()">
                        <i class="fas fa-cog"></i>
                        <span>Admin Panel</span>
                    </a>
                @elseif ($isModerator)
                    <a href="{{ route('moderator.index') }}" class="mobile-menu-link" onclick="closeMobileMenu()">
                        <i class="fas fa-building"></i>
                        <span>Quản lý rạp</span>
                    </a>
                @elseif ($isCounterStaff)
                    <a href="{{ route('counter.index') }}" class="mobile-menu-link" onclick="closeMobileMenu()">
                        <i class="fas fa-user-tie"></i>
                        <span>Quản lý quầy</span>
                    </a>
                @endif
                <a href="#" class="mobile-menu-link mobile-menu-logout" 
                   onclick="event.preventDefault(); document.getElementById('logout-form-mobile').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Đăng xuất</span>
                </a>
                <form id="logout-form-mobile" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        @else
            <div class="mobile-menu-section">
                <a href="{{ route('login') }}" class="mobile-menu-link mobile-menu-login" data-auth-trigger="login" data-close-mobile-menu="true" onclick="event.preventDefault(); closeMobileMenu(); openAuthModal('login'); return false;">
                    <i class="fas fa-sign-in-alt"></i>
                    <span>Đăng nhập</span>
                </a>
                <a href="{{ route('register') }}" class="mobile-menu-link" data-auth-trigger="register" data-close-mobile-menu="true" onclick="event.preventDefault(); closeMobileMenu(); openAuthModal('register'); return false;">
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
        try {
            $unreadCount = $user->notifications()->where('is_read', false)->count();
        } catch (\Exception $e) {
            $unreadCount = 0;
        }
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
                <a href="{{ route('notifications.index') }}" class="view-all-link">Xem tất cả</a>
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

@if (session('moderation_warning'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            showAlertModal('warning', @json(session('moderation_warning')));
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
            
            <form id="loginForm" method="POST" action="{{ route('login') }}" onsubmit="handleLogin(event)">
                @csrf
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

            <a href="{{ route('auth.google') }}" class="btn-glass btn-google-glass">
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
            
            <form id="registerForm" method="POST" action="{{ route('register') }}" onsubmit="handleRegister(event)">
                @csrf
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

                {{-- Checkbox đồng ý điều khoản --}}
                <div id="modalTosCheckGroup" style="display:flex;align-items:flex-start;gap:10px;background:rgba(255,255,255,0.04);border:1.5px solid rgba(255,255,255,0.1);border-radius:12px;padding:11px 14px;margin-bottom:4px;">
                    <input type="checkbox" id="modal_agree_tos" name="agree_tos" value="1"
                           style="width:17px;height:17px;flex-shrink:0;margin-top:2px;cursor:pointer;accent-color:#e50914;">
                    <label for="modal_agree_tos" style="color:rgba(255,255,255,0.75);font-size:13px;line-height:1.6;cursor:pointer;">
                        Tôi đã đọc và đồng ý với
                        <a href="#" id="modalOpenTos" style="color:#ff6b75;font-weight:600;text-decoration:none;">Điều khoản dịch vụ</a>
                        của CineHub
                    </label>
                </div>
                <div id="modalTosError" style="display:none;color:#ff6b6b;font-size:12px;padding-left:4px;margin-bottom:8px;">
                    <i class="fas fa-exclamation-circle"></i> Vui lòng đồng ý với Điều khoản dịch vụ
                </div>

                <button type="submit" class="btn-glass btn-primary-glass">Đăng ký</button>
            </form>

            <div class="divider-glass"><span>hoặc</span></div>

            <a href="{{ route('auth.google') }}" class="btn-glass btn-google-glass">
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

// Link điều khoản trong form đăng ký modal
document.addEventListener('DOMContentLoaded', function() {
    const modalOpenTos = document.getElementById('modalOpenTos');
    const tosCheckbox  = document.getElementById('modal_agree_tos');
    const tosError     = document.getElementById('modalTosError');
    const tosGroup     = document.getElementById('modalTosCheckGroup');

    if (modalOpenTos) {
        modalOpenTos.addEventListener('click', function(e) {
            e.preventDefault();
            window.open('{{ route('terms') }}', '_blank');
        });
    }

    if (tosCheckbox) {
        tosCheckbox.addEventListener('change', function() {
            if (this.checked && tosError) {
                tosError.style.display = 'none';
                if (tosGroup) tosGroup.style.borderColor = 'rgba(229,9,20,0.4)';
            }
        });
    }
});

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
    
    fetch(form.action || '{{ route('login') }}', {
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

        if (response.status === 419) {
            const csrfError = new Error('Phiên đăng nhập đã được làm mới. Vui lòng đăng nhập lại.');
            csrfError.isCsrfMismatch = true;
            throw csrfError;
        }

        if (!response.ok) {
            const firstError = data.errors ? Object.values(data.errors)[0]?.[0] : null;
            throw new Error(firstError || data.error || data.message || 'Không thể đăng nhập. Vui lòng kiểm tra lại thông tin.');
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
        if (error.isCsrfMismatch) {
            window.location.reload();
            return;
        }

        errorDiv.textContent = error.message || 'Có lỗi xảy ra. Vui lòng thử lại!';
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

    // Validate checkbox điều khoản
    const tosCheckbox = document.getElementById('modal_agree_tos');
    const tosError = document.getElementById('modalTosError');
    const tosGroup = document.getElementById('modalTosCheckGroup');
    if (tosCheckbox && !tosCheckbox.checked) {
        tosError.style.display = 'block';
        tosGroup.style.borderColor = 'rgba(229,9,20,0.6)';
        return;
    }
    if (tosError) tosError.style.display = 'none';
    if (tosGroup) tosGroup.style.borderColor = 'rgba(255,255,255,0.1)';

    // Disable button
    submitBtn.disabled = true;
    submitBtn.textContent = 'Đang đăng ký...';
    errorDiv.style.display = 'none';
    
    fetch(form.action || '{{ route('register') }}', {
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
    const searchHistoryKey = 'cinehub_search_history';
    const readSearchHistory = function() {
        try {
            const history = JSON.parse(localStorage.getItem(searchHistoryKey) || '[]');
            return Array.isArray(history) ? history.filter(item => typeof item === 'string').slice(0, 6) : [];
        } catch (error) {
            return [];
        }
    };
    const saveSearchKeyword = function(keyword) {
        const normalized = String(keyword || '').trim();
        if (!normalized) return;

        const history = readSearchHistory().filter(item => item.toLocaleLowerCase('vi') !== normalized.toLocaleLowerCase('vi'));
        history.unshift(normalized);
        try {
            localStorage.setItem(searchHistoryKey, JSON.stringify(history.slice(0, 6)));
        } catch (error) {
            // Search still works when browser storage is unavailable.
        }
    };

    // Keep header search independent from other page scripts. This also avoids
    // stale query parameters and always opens the canonical search URL.
    document.querySelectorAll('form[data-movie-search]').forEach(function(form) {
        form.addEventListener('submit', function(event) {
            const input = form.querySelector('input[name="search"]');
            const keyword = input ? input.value.trim() : '';

            event.preventDefault();

            if (!keyword) {
                if (input) input.focus();
                return;
            }

            saveSearchKeyword(keyword);
            window.location.assign(@json(route('search')) + '?search=' + encodeURIComponent(keyword));
        });
    });

    const searchBar = document.querySelector('.header-desktop .search-bar');
    const searchInput = document.getElementById('search-input-header');
    const searchDropdown = document.getElementById('headerSearchDropdown');
    const historyContainer = document.getElementById('headerSearchHistory');
    const movieContainer = document.getElementById('headerMovieSuggestions');

    if (searchBar && searchInput && searchDropdown && historyContainer && movieContainer) {
        const headerLeft = searchBar.closest('.header-left');
        let searchTimer = null;
        let activeRequest = null;

        const setSearchOpen = function(open) {
            searchBar.classList.toggle('search-active', open);
            headerLeft?.classList.toggle('search-expanded', open);
            searchBar.closest('.header-desktop')?.classList.toggle('search-mode', open);
            searchDropdown.hidden = !open;
            searchInput.setAttribute('aria-expanded', open ? 'true' : 'false');
        };

        const goToSearch = function(keyword) {
            const value = String(keyword || '').trim();
            if (!value) return;
            saveSearchKeyword(value);
            window.location.assign(@json(route('search')) + '?search=' + encodeURIComponent(value));
        };

        const renderHistory = function(keyword = '') {
            const normalized = keyword.toLocaleLowerCase('vi');
            const history = readSearchHistory()
                .filter(item => !normalized || item.toLocaleLowerCase('vi').includes(normalized))
                .slice(0, 5);

            historyContainer.replaceChildren();
            if (!history.length) return;

            const head = document.createElement('div');
            head.className = 'search-history-head';
            const title = document.createElement('span');
            title.textContent = 'Tìm kiếm gần đây';
            const clear = document.createElement('button');
            clear.type = 'button';
            clear.className = 'search-history-clear';
            clear.textContent = 'Xóa';
            clear.addEventListener('click', function() {
                try { localStorage.removeItem(searchHistoryKey); } catch (error) {}
                renderHistory(searchInput.value.trim());
                loadMovieSuggestions(searchInput.value.trim());
            });
            head.append(title, clear);

            const list = document.createElement('div');
            list.className = 'search-history-list';
            history.forEach(function(item) {
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'search-history-item';
                button.textContent = item;
                button.title = item;
                button.addEventListener('click', () => goToSearch(item));
                list.appendChild(button);
            });
            historyContainer.append(head, list);
        };

        const renderMovies = function(movies) {
            movieContainer.replaceChildren();
            if (!movies.length) {
                const empty = document.createElement('div');
                empty.className = 'header-search-state';
                empty.textContent = 'Chưa tìm thấy phim phù hợp';
                movieContainer.appendChild(empty);
                return;
            }

            movies.forEach(function(movie) {
                const link = document.createElement('a');
                link.className = 'header-movie-suggestion';
                link.href = movie.url;
                link.addEventListener('click', () => saveSearchKeyword(searchInput.value.trim() || movie.title));

                if (movie.thumbnail) {
                    const image = document.createElement('img');
                    image.className = 'header-movie-poster';
                    image.src = movie.thumbnail;
                    image.alt = movie.title;
                    image.loading = 'lazy';
                    link.appendChild(image);
                } else {
                    const imageFallback = document.createElement('div');
                    imageFallback.className = 'header-movie-poster-empty';
                    imageFallback.innerHTML = '<i class="fas fa-film"></i>';
                    link.appendChild(imageFallback);
                }

                const info = document.createElement('div');
                info.className = 'header-movie-info';
                const title = document.createElement('span');
                title.className = 'header-movie-title';
                title.textContent = movie.title;
                const meta = document.createElement('span');
                meta.className = 'header-movie-meta';
                const rating = document.createElement('span');
                rating.innerHTML = '<i class="fas fa-star"></i> ' + movie.rating;
                meta.appendChild(rating);
                if (movie.year) {
                    const year = document.createElement('span');
                    year.textContent = movie.year;
                    meta.appendChild(year);
                }
                info.append(title, meta);

                const level = document.createElement('span');
                level.className = 'header-movie-level';
                level.textContent = movie.level;
                link.append(info, level);
                movieContainer.appendChild(link);
            });
        };

        const loadMovieSuggestions = function(keyword = '') {
            if (activeRequest) activeRequest.abort();
            activeRequest = new AbortController();

            movieContainer.innerHTML = '<div class="header-search-state"><i class="fas fa-spinner fa-spin"></i> Đang tìm phim...</div>';
            const params = new URLSearchParams();
            if (keyword) params.set('q', keyword);
            readSearchHistory().forEach(item => params.append('history[]', item));

            fetch(@json(route('search.suggestions', [], false)) + '?' + params.toString(), {
                headers: { 'Accept': 'application/json' },
                signal: activeRequest.signal
            })
                .then(response => {
                    if (!response.ok) throw new Error('Suggestion request failed');
                    return response.json();
                })
                .then(data => renderMovies(Array.isArray(data.movies) ? data.movies : []))
                .catch(error => {
                    if (error.name === 'AbortError') return;
                    movieContainer.innerHTML = '<div class="header-search-state">Không thể tải gợi ý lúc này</div>';
                });
        };

        const refreshSearchDropdown = function() {
            const keyword = searchInput.value.trim();
            renderHistory(keyword);
            clearTimeout(searchTimer);
            searchTimer = setTimeout(() => loadMovieSuggestions(keyword), 220);
        };

        searchInput.addEventListener('focus', function() {
            setSearchOpen(true);
            refreshSearchDropdown();
        });
        searchInput.addEventListener('input', function() {
            setSearchOpen(true);
            refreshSearchDropdown();
        });
        searchInput.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                setSearchOpen(false);
                searchInput.blur();
            }
        });
        document.addEventListener('click', function(event) {
            if (!searchBar.contains(event.target)) setSearchOpen(false);
        });

        const initialSearch = @json(trim((string) request('search', '')));
        if (initialSearch) saveSearchKeyword(initialSearch);
    }

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
    @if(session('openLoginModal'))
        setTimeout(function() {
            openAuthModal('login');
        }, 300);
    @endif

    @if(session('openRegisterModal'))
        setTimeout(function() {
            openAuthModal('register');
        }, 300);
    @endif
});

// Browsers can restore the login modal from back/forward cache with an old
// CSRF token after visiting the OTP page. Always request a fresh form/session.
window.addEventListener('pageshow', function(event) {
    if (event.persisted) {
        window.location.reload();
    }
});
</script>
