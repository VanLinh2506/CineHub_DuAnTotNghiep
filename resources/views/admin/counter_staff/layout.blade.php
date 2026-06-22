<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($title) ? $title . ' - ' : '' }}Nhân viên đứng quầy - CineHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .staff-sidebar { min-height: 100vh; background: linear-gradient(180deg, #4facfe 0%, #00f2fe 100%); padding: 0; position: fixed; width: 250px; left: 0; top: 0; z-index: 1000; box-shadow: 2px 0 10px rgba(0,0,0,0.1); }
        .staff-main { margin-left: 250px; padding: 20px; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); min-height: 100vh; }
        .staff-header { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: #fff; padding: 15px 30px; box-shadow: 0 4px 15px rgba(79,172,254,0.3); margin-bottom: 20px; border-radius: 12px; }
        .staff-header h4, .staff-header small { color: #fff !important; }
        .sidebar-brand { padding: 20px; color: #fff; font-size: 1.5rem; font-weight: bold; border-bottom: 1px solid rgba(255,255,255,0.2); background: rgba(255,255,255,0.1); }
        .sidebar-menu { list-style: none; padding: 0; margin: 0; }
        .sidebar-menu li { border-bottom: 1px solid rgba(255,255,255,0.1); }
        .sidebar-menu a { display: flex; align-items: center; padding: 15px 20px; color: rgba(255,255,255,0.9); text-decoration: none; transition: all 0.3s ease; position: relative; }
        .sidebar-menu a::before { content: ''; position: absolute; left: 0; top: 0; height: 100%; width: 4px; background: #fff; transform: scaleY(0); transition: transform 0.3s ease; }
        .sidebar-menu a:hover, .sidebar-menu a.active { background: rgba(255,255,255,0.2); color: #fff; padding-left: 25px; box-shadow: inset 0 0 10px rgba(255,255,255,0.1); }
        .sidebar-menu a.active::before, .sidebar-menu a:hover::before { transform: scaleY(1); }
        .sidebar-menu a i { width: 25px; margin-right: 10px; }
        .stat-card { background: #fff; border-radius: 10px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; color: #333 !important; }
        .stat-card * { color: #333; }
        .staff-main { color: #333; }
        .staff-main > h4, .staff-main > h5, .staff-main > h6 { color: #333 !important; }
        .staff-main form {
            width: 100%;
            max-width: 100%;
        }
        .staff-main .row {
            min-width: 0;
        }
        .staff-main .d-inline-flex {
            flex-wrap: wrap;
        }
        .form-control, .form-select {
            width: 100%;
            max-width: 100%;
            border-radius: 8px;
        }
        textarea.form-control {
            resize: vertical;
        }
        @media screen and (max-width: 768px) {
            .staff-sidebar { position: fixed; left: -250px; transition: left 0.3s ease; z-index: 9999; }
            .staff-sidebar.active { left: 0; }
            .staff-main { margin-left: 0; padding: 15px; padding-top: 70px; }
            .staff-main .d-flex {
                flex-wrap: wrap;
            }
            .staff-main .btn-group {
                flex-wrap: wrap;
            }
            .staff-main .btn-group .btn,
            .staff-main .d-flex > .btn,
            .staff-main .d-flex > .form-control,
            .staff-main .d-flex > .form-select {
                width: 100%;
                max-width: 100%;
            }
            .mobile-menu-toggle { display: block !important; position: fixed; top: 15px; left: 15px; z-index: 10000; background: #1a1a2e; border: none; color: #fff; padding: 10px 15px; border-radius: 8px; font-size: 1.2rem; }
            .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9998; }
            .sidebar-overlay.active { display: block; }
        }
        @media screen and (min-width: 769px) { .mobile-menu-toggle { display: none !important; } }
    </style>
    @stack('styles')
</head>
<body>
    <button class="mobile-menu-toggle" onclick="toggleStaffSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeStaffSidebar()"></div>

    <div class="staff-sidebar" id="staffSidebar">
        <div class="sidebar-brand">
            <i class="fas fa-user-tie"></i> Nhân viên quầy
        </div>
        <ul class="sidebar-menu">
            <li><a href="{{ route('counter.sellTicket') }}" class="{{ request()->routeIs('counter.sellTicket') ? 'active' : '' }}">
                <i class="fas fa-cash-register"></i> Bán vé trực tiếp
            </a></li>
            <li><a href="{{ route('counter.salesHistory') }}" class="{{ request()->routeIs('counter.salesHistory') ? 'active' : '' }}">
                <i class="fas fa-history"></i> Lịch sử bán vé
            </a></li>
            <li><a href="{{ route('counter.scanQR') }}" class="{{ request()->routeIs('counter.scanQR') ? 'active' : '' }}">
                <i class="fas fa-qrcode"></i> Quét QR Code
            </a></li>
            <li><a href="{{ route('counter.scannedTickets') }}" class="{{ request()->routeIs('counter.scannedTickets') ? 'active' : '' }}">
                <i class="fas fa-ticket-alt"></i> Vé đã quét
            </a></li>
            <li><a href="{{ route('counter.showtimes') }}" class="{{ request()->routeIs('counter.showtimes') ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i> Lịch chiếu phim
            </a></li>
            <li><a href="{{ route('home') }}"><i class="fas fa-home"></i> Về trang chủ</a></li>
            <li><a href="{{ route('profile.index') }}"><i class="fas fa-user"></i> Hồ sơ</a></li>
            <li><a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </a></li>
        </ul>
        
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>

    <div class="staff-main">
        <div class="staff-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">{{ $title ?? 'Nhân viên đứng quầy' }}</h4>
                    @if(isset($theater))
                        <small class="text-muted">{{ $theater['name'] }} - {{ $theater['location'] ?? '' }}</small>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span><i class="fas fa-user-circle"></i> {{ $user['name'] ?? 'Nhân viên' }}</span>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function toggleStaffSidebar() {
        document.getElementById('staffSidebar').classList.toggle('active');
        document.getElementById('sidebarOverlay').classList.toggle('active');
    }
    function closeStaffSidebar() {
        document.getElementById('staffSidebar').classList.remove('active');
        document.getElementById('sidebarOverlay').classList.remove('active');
    }
    document.querySelectorAll('.sidebar-menu a').forEach(link => {
        link.addEventListener('click', closeStaffSidebar);
    });
    </script>
    @stack('scripts')
</body>
</html>
