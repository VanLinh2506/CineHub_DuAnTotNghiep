<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ isset($title) ? $title . ' - ' : '' }}CineHub Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { min-height: 100%; overflow-x: hidden; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f6fa; }
        
        /* Sidebar */
        .admin-sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #4a3567 0%, #2d1f3d 100%);
            padding: 0;
            position: fixed;
            width: 240px;
            left: 0;
            top: 0;
            z-index: 1000;
            box-shadow: 2px 0 15px rgba(0,0,0,0.1);
            overflow-y: auto;
        }
        .admin-sidebar::-webkit-scrollbar { width: 5px; }
        .admin-sidebar::-webkit-scrollbar-track { background: rgba(255,255,255,0.05); }
        .admin-sidebar::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.2); border-radius: 3px; }
        
        /* Sidebar Brand */
        .sidebar-brand { 
            padding: 20px 15px; 
            color: #fff; 
            font-size: 1.3rem; 
            font-weight: 600; 
            border-bottom: 1px solid rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .sidebar-brand i { font-size: 1.5rem; }
        
        /* Sidebar Menu */
        .sidebar-menu { 
            list-style: none; 
            padding: 10px 0; 
            margin: 0; 
        }
        .sidebar-menu li { margin: 0; }
        .sidebar-menu a { 
            display: flex; 
            align-items: center; 
            padding: 12px 20px; 
            color: rgba(255,255,255,0.8); 
            text-decoration: none; 
            transition: all 0.3s ease; 
            position: relative;
            font-size: 0.95rem;
        }
        .sidebar-menu a::before { 
            content: ''; 
            position: absolute; 
            left: 0; 
            top: 0; 
            height: 100%; 
            width: 3px; 
            background: #fff; 
            transform: scaleY(0); 
            transition: transform 0.3s ease; 
        }
        .sidebar-menu a:hover { 
            background: rgba(255,255,255,0.1); 
            color: #fff;
            padding-left: 25px;
        }
        .sidebar-menu a.active { 
            background: rgba(255,255,255,0.15); 
            color: #fff; 
            font-weight: 500;
        }
        .sidebar-menu a.active::before { transform: scaleY(1); }
        .sidebar-menu a i { 
            width: 22px; 
            margin-right: 12px; 
            font-size: 1.1rem;
            text-align: center;
        }
        
        /* Main Content */
        .admin-main { 
            margin-left: 300px; 
            width: calc(100% - 300px);
            max-width: calc(100vw - 300px);
            padding: 25px; 
            background: #f5f6fa; 
            min-height: 100vh; 
            overflow-x: hidden;
        }
        
        /* Stats Cards */
        .stat-card { 
            background: #fff; 
            border-radius: 12px; 
            padding: 25px; 
            box-shadow: 0 2px 8px rgba(0,0,0,0.08); 
            margin-bottom: 20px; 
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid #e9ecef;
            max-width: 100%;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 20px rgba(0,0,0,0.12);
        }
        .stat-card .d-flex { align-items: center; }
        .stat-card .stat-icon { 
            width: 65px; 
            height: 65px; 
            border-radius: 12px; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            font-size: 1.8rem; 
            color: #fff;
            margin-left: 15px;
        }
        .stat-card .stat-value { 
            font-size: 2.2rem; 
            font-weight: 700; 
            margin: 0;
            line-height: 1;
        }
        .stat-card .stat-label { 
            color: #8c8c8c; 
            font-size: 0.9rem; 
            text-transform: uppercase;
            font-weight: 500;
            letter-spacing: 0.5px;
            margin-bottom: 8px;
        }
        
        /* Color Variants */
        .bg-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important; }
        .bg-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%) !important; }
        .bg-info { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important; }
        .bg-warning { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%) !important; }
        .bg-danger { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important; }
        
        .text-primary { color: #667eea !important; }
        .text-success { color: #11998e !important; }
        .text-info { color: #4facfe !important; }
        .text-warning { color: #fa709a !important; }
        .text-danger { color: #f5576c !important; }
        
        /* Tables */
        .table { 
            background: #fff; 
            border-radius: 8px; 
            overflow: hidden;
        }
        .table thead th { 
            background: #f8f9fa; 
            border: none; 
            padding: 15px; 
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            color: #495057;
        }
        .table tbody td { 
            padding: 15px; 
            vertical-align: middle;
            border-bottom: 1px solid #f0f0f0;
        }
        .table tbody tr:last-child td { border-bottom: none; }
        .table tbody tr:hover { background: #f8f9fa; }
        
        /* Buttons */
        .btn { 
            padding: 8px 20px; 
            border-radius: 8px; 
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; }
        .btn-success { background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%); border: none; }
        .btn-danger { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); border: none; }
        .btn-sm { padding: 5px 12px; font-size: 0.875rem; }
        
        /* Forms */
        .form-control, .form-select { 
            border-radius: 8px; 
            border: 1px solid #e0e0e0;
            padding: 10px 15px;
        }
        .form-control:focus, .form-select:focus { 
            border-color: #667eea; 
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25); 
        }
        .form-label { font-weight: 500; margin-bottom: 8px; }

        .admin-main .page-header {
            gap: 15px;
            flex-wrap: wrap;
        }
        .admin-main .movie-form-container,
        .admin-main .admin-form-card {
            overflow: hidden;
        }
        .admin-main .movie-form-container .row,
        .admin-main .admin-form-card .row {
            min-width: 0;
        }
        .admin-main .movie-form-container [class*="col-"],
        .admin-main .admin-form-card [class*="col-"] {
            min-width: 0;
        }
        .admin-main .form-control,
        .admin-main .form-select,
        .admin-main textarea {
            max-width: 100%;
        }
        .admin-main select[multiple],
        .admin-main select[size] {
            overflow: auto;
        }
        .admin-main .category-picker {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            max-height: 190px;
            overflow: auto;
            padding: 12px;
            border: 1px solid #e0e0e0;
            border-radius: 12px;
            background: #fff;
        }
        .admin-main .category-picker-input {
            display: none;
        }
        .admin-main .category-picker-chip {
            display: inline-flex;
            align-items: center;
            min-height: 38px;
            margin: 0;
            padding: 8px 13px;
            border: 1px solid #d7dce5;
            border-radius: 999px;
            background: #f8fafc;
            color: #2d3748;
            font-weight: 600;
            cursor: pointer;
            user-select: none;
            transition: all 0.18s ease;
        }
        .admin-main .category-picker-chip:hover {
            border-color: #667eea;
            background: #f0f4ff;
        }
        .admin-main .category-picker-input:checked + .category-picker-chip {
            border-color: #e50914;
            background: #fff1f2;
            color: #b00008;
            box-shadow: 0 8px 18px rgba(229, 9, 20, 0.12);
        }
        .admin-main .upload-box {
            border: 2px dashed #cbd5e0;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s ease;
            background: linear-gradient(145deg, #f7fafc 0%, #edf2f7 100%);
            min-height: 150px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            max-width: 100%;
        }
        .admin-main .upload-box:hover,
        .admin-main .upload-box.dragover {
            border-color: #667eea;
            background: linear-gradient(145deg, #f0f4ff 0%, #e8ecff 100%);
        }
        .admin-main .upload-box.has-file {
            border-style: solid;
            border-color: #48bb78;
            background: linear-gradient(145deg, #f0fff4 0%, #e6ffed 100%);
        }
        .admin-main .upload-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 0;
        }
        .admin-main .upload-placeholder p,
        .admin-main .upload-placeholder small,
        .admin-main .video-name,
        .admin-main .trailer-name {
            max-width: 100%;
            overflow-wrap: anywhere;
        }
        .admin-main .upload-preview {
            max-width: 100%;
            max-height: 120px;
            border-radius: 8px;
            object-fit: contain;
        }
        .admin-main .video-upload {
            min-height: 120px;
        }
        .admin-main #seriesSection {
            max-width: 100%;
            overflow-x: hidden;
        }
        .admin-main .episode-item {
            margin-left: 0;
            margin-right: 0;
        }
        .admin-main .table-responsive {
            overflow-x: auto;
        }
        
        /* Alerts */
        .alert { 
            border-radius: 10px; 
            border: none; 
            padding: 15px 20px;
            margin-bottom: 20px;
        }
        .alert-success { background: #d4edda; color: #155724; }
        .alert-danger { background: #f8d7da; color: #721c24; }
        .alert-dismissible .btn-close { padding: 18px 20px; }
        
        /* Responsive */
        @media screen and (max-width: 768px) {
            .admin-sidebar { 
                position: fixed; 
                left: -240px; 
                transition: left 0.3s ease; 
                z-index: 9999; 
            }
            .admin-sidebar.active { left: 0; }
            .admin-main { 
                margin-left: 0; 
                width: 100%;
                max-width: 100vw;
                padding: 15px; 
            }
            .stat-card { padding: 15px; }
            .stat-card .stat-value { font-size: 1.5rem; }
            .stat-card .stat-icon { width: 50px; height: 50px; font-size: 1.3rem; }
            .mobile-menu-toggle { 
                display: block !important; 
                position: fixed; 
                top: 15px; 
                left: 15px; 
                z-index: 10000; 
                background: #4a3567; 
                border: none; 
                color: #fff; 
                padding: 10px 15px; 
                border-radius: 8px; 
                box-shadow: 0 2px 10px rgba(0,0,0,0.2);
            }
            .sidebar-overlay { 
                display: none; 
                position: fixed; 
                top: 0; 
                left: 0; 
                right: 0; 
                bottom: 0; 
                background: rgba(0,0,0,0.5); 
                z-index: 9998; 
            }
            .sidebar-overlay.active { display: block; }
        }
        @media screen and (min-width: 769px) { 
            .mobile-menu-toggle { display: none !important; } 
        }
    </style>
    @stack('styles')
</head>
<body>
    <button class="mobile-menu-toggle" onclick="toggleAdminSidebar()">
        <i class="fas fa-bars"></i>
    </button>

    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeAdminSidebar()"></div>

    <div class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand">
            <i class="fas fa-film"></i> CineHub Admin
        </div>
        <ul class="sidebar-menu">
            <li><a href="{{ route('admin.index') }}" class="{{ request()->routeIs('admin.index') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a></li>
            
            @if(Auth::user()->isAdmin())
            <li><a href="{{ route('admin.users.index') }}" class="{{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                <i class="fas fa-users"></i> Quản lý người dùng
            </a></li>
            @endif
            
            <li><a href="{{ route('admin.movies.index') }}" class="{{ request()->routeIs('admin.movies*') ? 'active' : '' }}">
                <i class="fas fa-film"></i> Quản lý phim
            </a></li>
            
            @if(Auth::user()->isAdmin())
            <li><a href="{{ route('admin.categories.index') }}" class="{{ request()->routeIs('admin.categories*') ? 'active' : '' }}">
                <i class="fas fa-tags"></i> Quản lý thể loại
            </a></li>
            @endif
            
            <li><a href="{{ route('admin.theaters.index') }}" class="{{ request()->routeIs('admin.theaters*') ? 'active' : '' }}">
                <i class="fas fa-building"></i> Quản lý rạp
            </a></li>
            
            <li><a href="{{ route('admin.tickets.index') }}" class="{{ request()->routeIs('admin.tickets*') ? 'active' : '' }}">
                <i class="fas fa-ticket-alt"></i> Hỗ trợ khách hàng
            </a></li>
            
            <li><a href="{{ route('admin.foodItems.index') }}" class="{{ request()->routeIs('admin.foodItems*') ? 'active' : '' }}">
                <i class="fas fa-utensils"></i> Đồ ăn & Combo
            </a></li>
            
            <li><a href="{{ route('admin.analytics') }}" class="{{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                <i class="fas fa-chart-line"></i> Analytics & Báo cáo
            </a></li>
            
            <li><a href="{{ route('admin.logs') }}" class="{{ request()->routeIs('admin.logs') ? 'active' : '' }}">
                <i class="fas fa-history"></i> Lịch sử hoạt động
            </a></li>
            
            <li><a href="{{ route('home') }}">
                <i class="fas fa-home"></i> Về trang chủ
            </a></li>
            
            <li><a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </a></li>
        </ul>
        
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>

    <div class="admin-main">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    function toggleAdminSidebar() {
        document.getElementById('adminSidebar').classList.toggle('active');
        document.getElementById('sidebarOverlay').classList.toggle('active');
    }
    function closeAdminSidebar() {
        document.getElementById('adminSidebar').classList.remove('active');
        document.getElementById('sidebarOverlay').classList.remove('active');
    }
    </script>
    @stack('scripts')
</body>
</html>
