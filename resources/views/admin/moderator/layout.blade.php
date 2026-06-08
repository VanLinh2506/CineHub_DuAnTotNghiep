<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($title) ? $title . ' - ' : '' }}Quản lý rạp - CineHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .moderator-sidebar { min-height: 100vh; background: linear-gradient(180deg, #2d1b3d 0%, #1a0d2e 100%); padding: 0; position: fixed; width: 250px; left: 0; top: 0; z-index: 1000; box-shadow: 2px 0 10px rgba(0,0,0,0.3); overflow-y: auto; overflow-x: hidden; }
        .moderator-sidebar::-webkit-scrollbar { width: 6px; }
        .moderator-sidebar::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
        .moderator-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 3px; }
        .moderator-sidebar::-webkit-scrollbar-thumb:hover { background: rgba(255,255,255,0.3); }
        .moderator-main { margin-left: 250px; padding: 20px; background: linear-gradient(135deg, #2d1b3d 0%, #1a0d2e 100%); min-height: 100vh; }
        .moderator-header { background: linear-gradient(135deg, #3d2a4d 0%, #2d1b3d 100%); color: #fff; padding: 15px 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.3); margin-bottom: 20px; border-radius: 12px; }
        .moderator-header h4, .moderator-header small { color: #fff !important; }
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
        .stat-card h1,.stat-card h2,.stat-card h3,.stat-card h4,.stat-card h5,.stat-card h6 { color: #333 !important; }
        .stat-card .badge { color: #fff !important; }
        .stat-card .btn { color: #fff !important; }
        .stat-card .btn-outline-primary { color: #0d6efd !important; }
        .stat-card .btn-outline-secondary { color: #6c757d !important; }
        .stat-card .btn-outline-info { color: #0dcaf0 !important; }
        .stat-card .btn-outline-success { color: #198754 !important; }
        .stat-card .btn-outline-warning { color: #ffc107 !important; }
        .stat-card .btn-outline-danger { color: #dc3545 !important; }
        .stat-card a:not(.btn) { color: #0d6efd !important; }
        .stat-card .text-primary { color: #0d6efd !important; }
        .stat-card .text-secondary { color: #6c757d !important; }
        .stat-card .text-success { color: #198754 !important; }
        .stat-card .text-danger { color: #dc3545 !important; }
        .stat-card .text-warning { color: #ffc107 !important; }
        .stat-card .text-info { color: #0dcaf0 !important; }
        .stat-card .text-muted { color: #6c757d !important; }
        .stat-card .text-dark { color: #212529 !important; }
        .stat-card .table { color: #333 !important; }
        .stat-card .table th { color: #333 !important; background-color: #f8f9fa !important; }
        .stat-card .table td { color: #333 !important; }
        .stat-card .form-control, .stat-card .form-select { color: #333 !important; background-color: #fff !important; }
        .stat-card .form-label { color: #333 !important; }
        .stat-card .input-group-text { color: #333 !important; background-color: #e9ecef !important; }
        .stat-card .list-group-item { color: #333 !important; background-color: #fff !important; }
        .moderator-main { color: #333; }
        .moderator-main > .d-flex h5, .moderator-main > .d-flex h4, .moderator-main > .mb-4 > h5, .moderator-main > .mb-3 > h5, .moderator-main > .row > .col-12 > h5, .moderator-main > h5, .moderator-main > h4 { color: #fff !important; }
        .moderator-main > form .form-control, .moderator-main > .mb-3 .form-control { color: #333 !important; background-color: #fff !important; }
        .modal-content { color: #333 !important; background-color: #fff !important; }
        .modal-content * { color: #333; }
        .modal-content h1,.modal-content h2,.modal-content h3,.modal-content h4,.modal-content h5,.modal-content h6 { color: #333 !important; }
        .modal-content p,.modal-content span,.modal-content div,.modal-content label,.modal-content strong,.modal-content small { color: #333 !important; }
        .modal-content .form-control, .modal-content .form-select { color: #333 !important; background-color: #fff !important; }
        .modal-content .form-label { color: #333 !important; }
        .modal-content .text-muted { color: #6c757d !important; }
        .modal-content .text-danger { color: #dc3545 !important; }
        .modal-content .btn { color: #fff !important; }
        .modal-content .btn-secondary { background-color: #6c757d !important; }
        @media screen and (max-width: 768px) {
            .moderator-sidebar { position: fixed; left: -250px; transition: left 0.3s ease; z-index: 9999; }
            .moderator-sidebar.active { left: 0; }
            .moderator-main { margin-left: 0; padding: 15px; padding-top: 70px; }
            .moderator-header { padding: 10px 15px; }
            .moderator-header h4 { font-size: 1rem; }
            .stat-card { padding: 15px; }
            .mobile-menu-toggle { display: block !important; position: fixed; top: 15px; left: 15px; z-index: 10000; background: #1a1a2e; border: none; color: #fff; padding: 10px 15px; border-radius: 8px; font-size: 1.2rem; }
            .sidebar-overlay { display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.5); z-index: 9998; }
            .sidebar-overlay.active { display: block; }
        }
        @media screen and (min-width: 769px) { .mobile-menu-toggle { display: none !important; } }
    </style>
    @stack('styles')
</head>
<body>
    <button class="mobile-menu-toggle" onclick="toggleModeratorSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeModeratorSidebar()"></div>

    <div class="moderator-sidebar" id="moderatorSidebar">
        <div class="sidebar-brand">
            <i class="fas fa-building"></i> Quản lý rạp
        </div>
        <ul class="sidebar-menu">
            @if(!isset($isCounterStaff) || !$isCounterStaff)
            <li><a href="{{ route('moderator.index') }}" class="{{ ($current_page ?? '') === 'dashboard' ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a></li>
            <li><a href="{{ route('moderator.theater') }}" class="{{ ($current_page ?? '') === 'theater' ? 'active' : '' }}">
                <i class="fas fa-building"></i> Thông tin rạp
            </a></li>
            @endif
            <li><a href="{{ route('moderator.screens.index') }}" class="{{ ($current_page ?? '') === 'screens' ? 'active' : '' }}">
                <i class="fas fa-door-open"></i> Quản lý phòng
            </a></li>
            <li><a href="{{ route('moderator.showtimes.index') }}" class="{{ ($current_page ?? '') === 'showtimes' ? 'active' : '' }}">
                <i class="fas fa-calendar-alt"></i> Lịch chiếu
            </a></li>
            <li><a href="{{ route('moderator.tickets') }}" class="{{ ($current_page ?? '') === 'tickets' ? 'active' : '' }}">
                <i class="fas fa-ticket-alt"></i> Quản lý vé
            </a></li>
            <li><a href="{{ route('moderator.foodItems') }}" class="{{ ($current_page ?? '') === 'food_items' ? 'active' : '' }}">
                <i class="fas fa-utensils"></i> Combo & Đồ ăn
            </a></li>
            @if(!isset($isCounterStaff) || !$isCounterStaff)
            <li><a href="{{ route('moderator.statistics') }}" class="{{ ($current_page ?? '') === 'statistics' ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> Thống kê
            </a></li>
            @if(isset($isTheaterAdmin) && $isTheaterAdmin || isset($isModerator) && $isModerator)
            <li><a href="{{ route('moderator.counterStaff') }}" class="{{ ($current_page ?? '') === 'counter_staff' ? 'active' : '' }}">
                <i class="fas fa-users"></i> Quản lý nhân viên
            </a></li>
            @endif
            <li><a href="{{ route('moderator.permissionRequests') }}" class="{{ ($current_page ?? '') === 'permission_requests' ? 'active' : '' }}">
                <i class="fas fa-user-shield"></i> Yêu cầu thay đổi quyền
                @if(isset($pendingCount) && $pendingCount > 0)
                    <span class="badge bg-danger ms-2">{{ $pendingCount }}</span>
                @endif
            </a></li>
            @endif
            <li><a href="/"><i class="fas fa-home"></i> Về trang chủ</a></li>
            <li><a href="{{ route('profile.index') }}"><i class="fas fa-user"></i> Hồ sơ</a></li>
            <li>
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form-moderator').submit();">
                    <i class="fas fa-sign-out-alt"></i> Đăng xuất
                </a>
            </li>
        </ul>
        
        <form id="logout-form-moderator" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>

    <div class="moderator-main">
        <div class="moderator-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0">{{ $title ?? 'Quản lý rạp' }}</h4>
                    @if(isset($theater))
                        <small class="text-muted">{{ $theater['name'] }} - {{ $theater['location'] ?? '' }}</small>
                    @endif
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span><i class="fas fa-user-circle"></i> {{ $user['name'] ?? 'Moderator' }}</span>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.js"></script>
    <script>
    function toggleModeratorSidebar() {
        document.getElementById('moderatorSidebar').classList.toggle('active');
        document.getElementById('sidebarOverlay').classList.toggle('active');
    }
    function closeModeratorSidebar() {
        document.getElementById('moderatorSidebar').classList.remove('active');
        document.getElementById('sidebarOverlay').classList.remove('active');
    }
    document.querySelectorAll('.sidebar-menu a').forEach(link => {
        link.addEventListener('click', closeModeratorSidebar);
    });
    </script>
    @stack('scripts')
</body>
</html>
