<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        {{ isset($title) ? $title . ($title != 'CineHub' ? ' - ' : '') : '' }}CineHub
    </title>
    <link rel="icon" href="{{ storage_url('data/img/avt_webb.png') }}" type="image/png">

    @isset($meta_description)
        <meta name="description" content="{{ $meta_description }}">
    @endisset

    @isset($meta_keywords)
        <meta name="keywords" content="{{ $meta_keywords }}">
    @endisset

    @isset($meta_og_title)
        <meta property="og:title" content="{{ $meta_og_title }}">
    @endisset

    @isset($meta_og_description)
        <meta property="og:description" content="{{ $meta_og_description }}">
    @endisset

    @isset($meta_og_image)
        <meta property="og:image" content="{{ $meta_og_image }}">
    @endisset

    <meta property="og:type" content="website">
    <meta property="og:site_name" content="CineHub">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('style.css') }}?v={{ time() }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    @php
        $isAuthPage = false;

        if (isset($current_page) && $current_page === 'auth') {
            $isAuthPage = true;
        } elseif (
            request()->has('route') &&
            (
                str_contains(request('route'), 'auth/login') ||
                str_contains(request('route'), 'auth/register')
            )
        ) {
            $isAuthPage = true;
        }
    @endphp

    @if(!$isAuthPage)

        @php
            $menuCategories = $menuCategories ?? [];
            $countries = $countries ?? [];

            $isAdmin = $isAdmin ?? false;
            $isModerator = $isModerator ?? false;
            $isCounterStaff = $isCounterStaff ?? false;
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
                        <form method="GET" action="{{ route('movies.index') }}" class="search-form-inline">

                            <label class="labeo" for="search-input-header"></label>

                            <input type="text" name="search" id="search-input-header" class="search-input"
                                placeholder="Tìm kiếm phim...">

                            <button type="submit" class="search-btn">
                                <i class="fas fa-search"></i>
                            </button>

                        </form>
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
                            Thể loại
                            <i class="fas fa-chevron-down"></i>
                        </span>

                        <div class="dropdown-menu">
                            @foreach($menuCategories as $cat)
                                <a href="{{ route('movies.category', $cat['id']) }}" class="dropdown-item">
                                    {{ $cat['name'] }}
                                </a>
                            @endforeach
                        </div>

                    </div>

                    <div class="nav-dropdown">

                        <span class="nav-link-new dropdown-trigger">
                            Quốc gia
                            <i class="fas fa-chevron-down"></i>
                        </span>

                        <div class="dropdown-menu">
                            @foreach($countries as $country)
                                <a href="{{ route('movies.index', ['country' => $country['country']]) }}"
                                    class="dropdown-item">
                                    {{ $country['country'] }}
                                </a>
                            @endforeach
                        </div>

                    </div>

                    <a href="{{ route('movies.index') }}" class="nav-link-new">
                        Top phim
                    </a>

                    <a href="{{ route('movies.theater') }}" id="booking-link" class="nav-link-new">
                        Vé xem phim
                    </a>

                </nav>

                <div class="header-right">

                    @if(!empty($user))

                        @if($isAdmin)
                            <a href="{{ route('admin.index') }}" class="sign-in-btn" style="background-color:#FFFFFF37;">
                                <i class="fas fa-cog"></i>
                                <span>Admin Panel</span>
                            </a>

                        @elseif($isModerator)

                            <a href="{{ route('moderator.index') }}" class="sign-in-btn">
                                <i class="fas fa-building"></i>
                                <span>Quản lý rạp</span>
                            </a>

                        @elseif($isCounterStaff)

                            <a href="{{ route('counter.index') }}" class="sign-in-btn">
                                <i class="fas fa-user-tie"></i>
                                <span>Quản lý quầy</span>
                            </a>

                        @endif

                        <a href="{{ route('profile.index') }}" class="sign-in-btn">

                            <i class="fas fa-user"></i>
                            <span>{{ $user['name'] }}</span>

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

        <!-- Mobile Header -->
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
        <!-- Mobile Responsive -->
        <style>
            @media screen and (max-width: 768px) {
                .header-desktop {
                    display: none !important;
                }

                .header-mobile {
                    display: block !important;
                }

                .mobile-bottom-nav {
                    display: flex !important;
                }
            }
        </style>

        <!-- Mobile Bottom Navigation -->
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

            @if(!empty($user))
                <a href="{{ route('profile.index') }}" class="mobile-nav-item">
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

        <!-- Mobile Menu -->
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

            @if(!empty($user))

                <div class="mobile-menu-user">

                    <div class="mobile-user-avatar">

                        @if(!empty($user['avatar_url']))
                            <img src="{{ $user['avatar_url'] }}" alt="Avatar">
                        @else
                            <i class="fas fa-user"></i>
                        @endif

                    </div>

                    <div class="mobile-user-info">
                        <span class="mobile-user-name">
                            {{ $user['name'] }}
                        </span>

                        <span class="mobile-user-email">
                            {{ $user['email'] }}
                        </span>
                    </div>

                </div>

            @endif

            <!-- Search -->
            <div class="mobile-menu-search">

                <form method="GET" action="{{ route('movies.index') }}">

                    <input type="text" name="search" class="mobile-search-input" placeholder="Tìm kiếm phim...">

                    <button type="submit" class="mobile-search-btn">
                        <i class="fas fa-search"></i>
                    </button>

                </form>

            </div>

            <!-- Content -->
            <div class="mobile-menu-content">

                <div class="mobile-menu-section">

                    <a href="{{ route('movies.phimle') }}" class="mobile-menu-link"
                        onclick="closeMobileMenu()">

                        <i class="fas fa-film"></i>
                        <span>Phim lẻ</span>

                    </a>

                    <a href="{{ route('movies.phimbo') }}" class="mobile-menu-link"
                        onclick="closeMobileMenu()">

                        <i class="fas fa-tv"></i>
                        <span>Phim bộ</span>

                    </a>

                    <a href="{{ route('movies.index') }}" class="mobile-menu-link" onclick="closeMobileMenu()">

                        <i class="fas fa-star"></i>
                        <span>Top phim</span>

                    </a>

                    <a href="{{ route('movies.theater') }}" class="mobile-menu-link" onclick="closeMobileMenu()">

                        <i class="fas fa-ticket-alt"></i>
                        <span>Vé xem phim</span>

                    </a>

                </div>

                <!-- Categories -->
                <div class="mobile-menu-section">

                    <div class="mobile-menu-section-title">
                        Thể loại
                    </div>

                    <div class="mobile-menu-tags">

                        @foreach(array_slice($menuCategories, 0, 8) as $cat)

                            <a href="{{ route('movies.category', $cat['id']) }}" class="mobile-menu-tag"
                                onclick="closeMobileMenu()">

                                {{ $cat['name'] }}

                            </a>

                        @endforeach

                    </div>

                </div>

                <!-- Countries -->
                <div class="mobile-menu-section">

                    <div class="mobile-menu-section-title">
                        Quốc gia
                    </div>

                    <div class="mobile-menu-tags">

                        @foreach(array_slice($countries, 0, 6) as $country)

                            <a href="{{ route('movies.index', ['country' => $country['country']]) }}"
                                class="mobile-menu-tag" onclick="closeMobileMenu()">

                                {{ $country['country'] }}

                            </a>

                        @endforeach

                    </div>

                </div>

                @if(!empty($user))

                    <div class="mobile-menu-section">

                        <div class="mobile-menu-section-title">
                            Tài khoản
                        </div>

                        <a href="{{ route('profile.index') }}" class="mobile-menu-link" onclick="closeMobileMenu()">

                            <i class="fas fa-user-circle"></i>
                            <span>Hồ sơ của tôi</span>

                        </a>

                        <a href="{{ route('booking.history') }}" class="mobile-menu-link" onclick="closeMobileMenu()">

                            <i class="fas fa-ticket-alt"></i>
                            <span>Vé của tôi</span>

                        </a>

                        @if($isAdmin)

                            <a href="{{ route('admin.index') }}" class="mobile-menu-link" onclick="closeMobileMenu()">

                                <i class="fas fa-cog"></i>
                                <span>Admin Panel</span>

                            </a>

                        @elseif($isModerator)

                            <a href="{{ route('moderator.index') }}" class="mobile-menu-link" onclick="closeMobileMenu()">

                                <i class="fas fa-building"></i>
                                <span>Quản lý rạp</span>

                            </a>

                        @elseif($isCounterStaff)

                            <a href="{{ route('counter.index') }}" class="mobile-menu-link" onclick="closeMobileMenu()">

                                <i class="fas fa-user-tie"></i>
                                <span>Quản lý quầy</span>

                            </a>

                        @endif

                        <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                            @csrf
                            <button type="submit" class="mobile-menu-link mobile-menu-logout" onclick="closeMobileMenu()" style="width: 100%; background: none; border: none; text-align: left;">
                                <i class="fas fa-sign-out-alt"></i>
                                <span>Đăng xuất</span>
                            </button>
                        </form>

                    </div>

                @else

                    <div class="mobile-menu-section">

                        <a href="#" class="mobile-menu-link mobile-menu-login"
                            onclick="event.preventDefault(); closeMobileMenu(); openAuthModal('login');">

                            <i class="fas fa-sign-in-alt"></i>
                            <span>Đăng nhập</span>

                        </a>

                        <a href="#" class="mobile-menu-link"
                            onclick="event.preventDefault(); closeMobileMenu(); openAuthModal('register');">

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

                document.body.style.overflow =
                    menu.classList.contains('active')
                        ? 'hidden'
                        : '';
            }

            function closeMobileMenu() {
                const menu = document.getElementById('mobileSlideMenu');
                const overlay = document.getElementById('mobileMenuOverlay');

                menu.classList.remove('active');
                overlay.classList.remove('active');

                document.body.style.overflow = '';
            }
        </script>

    @endif

    @if(!empty($user))

        <div class="notification-wrapper-fixed" id="notificationWrapper">

            <a href="javascript:void(0)" class="notification-btn-fixed" id="notificationBtnFixed"
                onclick="toggleNotificationDropdown()">

                <i class="fas fa-bell"></i>

                @if(($unreadCount ?? 0) > 0)
                    <span class="notification-badge">
                        {{ ($unreadCount > 99) ? '99+' : $unreadCount }}
                    </span>
                @endif

            </a>

            <div class="notification-dropdown" id="notificationDropdown" style="display:none;">

                <div class="notification-dropdown-header">

                    <h6>Thông báo</h6>

                    <a href="{{ isset($isModerator) && $isModerator
            ? route('moderator.permissionRequests')
            : route('notifications.index') }}" class="view-all-link">

                        Xem tất cả

                    </a>

                </div>

                <div class="notification-dropdown-body" id="notificationList">

                    <div class="notification-loading">
                        <i class="fas fa-spinner fa-spin"></i>
                        Đang tải...
                    </div>

                </div>

            </div>

        </div>

    @endif
    @if(session('success'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                showAlertModal('success', @json(session('success')));
            });
        </script>
    @endif

    @if(session('error'))
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                showAlertModal('error', @json(session('error')));
            });
        </script>
    @endif

    <!-- Auth Modal -->
    <div id="authModal" class="modal-overlay" style="display:none;">
        <div class="modal-content-login">

            <span class="modal-close" onclick="closeAuthModal()">&times;</span>

            <div class="auth-tabs">
                <button class="auth-tab active" onclick="switchAuthTab('login')">
                    Đăng nhập
                </button>

                <button class="auth-tab" onclick="switchAuthTab('register')">
                    Đăng ký
                </button>
            </div>

            <!-- Login -->
            <div id="loginTab" class="auth-tab-content active">

                <h2 class="modal-title">
                    Đăng nhập
                </h2>

                <div id="loginError" class="alert alert-danger" style="display:none;">
                </div>

                <form id="loginForm" method="POST" action="{{ route('login') }}">

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

                        <a href="#" class="forgot-password">
                            Quên mật khẩu?
                        </a>

                    </div>

                    <button type="submit" class="btn-login">
                        Đăng nhập
                    </button>

                </form>

                <!-- Divider -->
                <div class="auth-divider">
                    <span>hoặc</span>
                </div>

                <!-- Google Login -->
                <a href="{{ route('auth.google') }}" class="btn-google-auth">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19.8 10.2273C19.8 9.51819 19.7364 8.83637 19.6182 8.18182H10.2V12.05H15.5818C15.3273 13.3 14.5727 14.3591 13.4455 15.0682V17.5773H16.7364C18.6091 15.8364 19.8 13.2727 19.8 10.2273Z" fill="#4285F4"/>
                        <path d="M10.2 20C12.9 20 15.1727 19.1045 16.7364 17.5773L13.4455 15.0682C12.4909 15.6682 11.2636 16.0227 10.2 16.0227C7.59091 16.0227 5.37273 14.2636 4.52727 11.9H1.11364V14.4909C2.66818 17.5909 6.19091 20 10.2 20Z" fill="#34A853"/>
                        <path d="M4.52727 11.9C4.30909 11.3 4.18182 10.6591 4.18182 10C4.18182 9.34091 4.30909 8.7 4.52727 8.1V5.50909H1.11364C0.418182 6.89091 0 8.4 0 10C0 11.6 0.418182 13.1091 1.11364 14.4909L4.52727 11.9Z" fill="#FBBC05"/>
                        <path d="M10.2 3.97727C11.3636 3.97727 12.4 4.37727 13.2091 5.15455L16.1364 2.22727C15.1636 1.32727 12.9 0 10.2 0C6.19091 0 2.66818 2.40909 1.11364 5.50909L4.52727 8.1C5.37273 5.73636 7.59091 3.97727 10.2 3.97727Z" fill="#EA4335"/>
                    </svg>
                    Đăng nhập bằng Google
                </a>

                <div class="modal-footer">
                    <p>
                        Chưa có tài khoản?
                        <a href="#" onclick="event.preventDefault(); switchAuthTab('register');">
                            Đăng ký ngay
                        </a>
                    </p>
                </div>

            </div>

            <!-- Register -->
            <div id="registerTab" class="auth-tab-content" style="display:none;">

                <h2 class="modal-title">
                    Đăng ký
                </h2>

                <div id="registerError" class="alert alert-danger" style="display:none;">
                </div>

                <form id="registerForm" method="POST" action="{{ route('register') }}">

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
                        <input type="password" name="confirm_password" required placeholder="Xác nhận mật khẩu"
                            class="input-field">
                    </div>

                    <button type="submit" class="btn-register-form">
                        Đăng ký
                    </button>

                </form>

                <!-- Divider -->
                <div class="auth-divider">
                    <span>hoặc</span>
                </div>

                <!-- Google Register -->
                <a href="{{ route('auth.google') }}" class="btn-google-auth">
                    <svg width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19.8 10.2273C19.8 9.51819 19.7364 8.83637 19.6182 8.18182H10.2V12.05H15.5818C15.3273 13.3 14.5727 14.3591 13.4455 15.0682V17.5773H16.7364C18.6091 15.8364 19.8 13.2727 19.8 10.2273Z" fill="#4285F4"/>
                        <path d="M10.2 20C12.9 20 15.1727 19.1045 16.7364 17.5773L13.4455 15.0682C12.4909 15.6682 11.2636 16.0227 10.2 16.0227C7.59091 16.0227 5.37273 14.2636 4.52727 11.9H1.11364V14.4909C2.66818 17.5909 6.19091 20 10.2 20Z" fill="#34A853"/>
                        <path d="M4.52727 11.9C4.30909 11.3 4.18182 10.6591 4.18182 10C4.18182 9.34091 4.30909 8.7 4.52727 8.1V5.50909H1.11364C0.418182 6.89091 0 8.4 0 10C0 11.6 0.418182 13.1091 1.11364 14.4909L4.52727 11.9Z" fill="#FBBC05"/>
                        <path d="M10.2 3.97727C11.3636 3.97727 12.4 4.37727 13.2091 5.15455L16.1364 2.22727C15.1636 1.32727 12.9 0 10.2 0C6.19091 0 2.66818 2.40909 1.11364 5.50909L4.52727 8.1C5.37273 5.73636 7.59091 3.97727 10.2 3.97727Z" fill="#EA4335"/>
                    </svg>
                    Đăng ký bằng Google
                </a>

                <div class="modal-footer">
                    <p>
                        Đã có tài khoản?
                        <a href="#" onclick="event.preventDefault(); switchAuthTab('login');">
                            Đăng nhập ngay
                        </a>
                    </p>
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

        .modal-footer a:hover {
            text-decoration: underline;
        }

        .auth-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
        }

        .auth-tab {
            flex: 1;
            padding: 12px;
            background: transparent;
            border: none;
            color: #999;
            cursor: pointer;
            font-size: 16px;
            font-weight: 500;
            transition: all 0.3s;
            border-bottom: 2px solid transparent;
            margin-bottom: -2px;
        }

        .auth-tab:hover {
            color: #fff;
        }

        .auth-tab.active {
            color: #e50914;
            border-bottom-color: #e50914;
        }

        .auth-tab-content {
            display: none;
            opacity: 0;
            transform: translateX(-20px);
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .auth-tab-content.active {
            display: block;
            opacity: 1;
            transform: translateX(0);
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
    </style>

    <script>

        function openAuthModal(tab = 'login') {
            const modal = document.getElementById('authModal');

            modal.style.display = 'flex';

            document.body.style.overflow = 'hidden';

            setTimeout(() => {
                modal.classList.add('show');
            }, 10);

            switchAuthTab(tab);
        }

        function closeAuthModal() {
            const modal = document.getElementById('authModal');

            modal.classList.remove('show');

            setTimeout(() => {

                modal.style.display = 'none';

                document.body.style.overflow = '';

                document.getElementById('loginForm')?.reset();
                document.getElementById('registerForm')?.reset();

                document.getElementById('loginError').style.display = 'none';
                document.getElementById('registerError').style.display = 'none';

            }, 300);
        }

        function switchAuthTab(tab) {
            document.querySelectorAll('.auth-tab-content').forEach(el => {
                el.classList.remove('active');
                el.style.display = 'none';
            });

            document.querySelectorAll('.auth-tab').forEach(el => {
                el.classList.remove('active');
            });

            if (tab === 'login') {
                document.getElementById('loginTab').style.display = 'block';
                document.getElementById('loginTab').classList.add('active');

                document.querySelectorAll('.auth-tab')[0]
                    .classList.add('active');
            }
            else {
                document.getElementById('registerTab').style.display = 'block';
                document.getElementById('registerTab').classList.add('active');

                document.querySelectorAll('.auth-tab')[1]
                    .classList.add('active');
            }
        }

        document.getElementById('authModal')
            ?.addEventListener('click', function (e) {

                if (e.target === this) {
                    closeAuthModal();
                }

            });

    </script>

    <script>

        document.getElementById('loginForm')
            ?.addEventListener('submit', function (e) {

                e.preventDefault();

                const formData = new FormData(this);

                const errorDiv = document.getElementById('loginError');

                errorDiv.style.display = 'none';

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(res => res.json())
                    .then(data => {

                        if (data.success) {
                            location.reload();
                        }
                        else {
                            errorDiv.textContent =
                                data.error || 'Đăng nhập thất bại';

                            errorDiv.style.display = 'block';
                        }

                    })
                    .catch(() => {
                        this.submit();
                    });

            });

        document.getElementById('registerForm')
            ?.addEventListener('submit', function (e) {

                e.preventDefault();

                const formData = new FormData(this);

                const errorDiv =
                    document.getElementById('registerError');

                const password =
                    formData.get('password');

                const confirm =
                    formData.get('confirm_password');

                if (password !== confirm) {
                    errorDiv.textContent =
                        'Mật khẩu xác nhận không khớp';

                    errorDiv.style.display = 'block';

                    return;
                }

                fetch(this.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(res => res.json())
                    .then(data => {

                        if (data.success) {
                            location.reload();
                        }
                        else {
                            errorDiv.textContent =
                                data.error || 'Đăng ký thất bại';

                            errorDiv.style.display = 'block';
                        }

                    })
                    .catch(() => {
                        this.submit();
                    });

            });

    </script>

    <script>

        function showAlertModal(type, message) {
            const modal =
                document.getElementById('alertModal');

            if (modal) {
                modal.classList.add('show');

                setTimeout(() => {
                    closeAlertModal();
                }, 5000);
            }
            else {
                alert(message);
            }
        }

        function closeAlertModal() {
            const modal =
                document.getElementById('alertModal');

            if (modal) {
                modal.classList.remove('show');

                setTimeout(() => {
                    modal.remove();
                }, 300);
            }
        }

        document.addEventListener('keydown', function (e) {

            if (e.key === 'Escape') {
                closeAlertModal();
            }

        });

    </script>

    <script>

        var isUserLoggedIn = @json(!empty($user));

        document.addEventListener('DOMContentLoaded', function () {

            const bookingLink =
                document.getElementById('booking-link');

            if (bookingLink) {
                bookingLink.addEventListener('click', function (e) {

                    if (!isUserLoggedIn) {
                        e.preventDefault();

                        openAuthModal('login');

                        setTimeout(() => {

                            const loginError =
                                document.getElementById('loginError');

                            if (loginError) {
                                loginError.textContent =
                                    'Vui lòng đăng nhập để đặt vé xem phim!';

                                loginError.style.display = 'block';
                            }

                        }, 100);
                    }

                });
            }

        });

    </script>
