<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . ' - ' : ''; ?>Admin Panel - CineHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php
    // Sử dụng UrlHelper để lấy base URL
    if (!class_exists('UrlHelper')) {
        require_once __DIR__ . '/../../core/UrlHelper.php';
    }
    $baseUrl = UrlHelper::getBaseUrl();
    ?>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($baseUrl); ?>/style.css?v=<?php echo time(); ?>">
    <style>
        .admin-sidebar {
            min-height: 100vh;
            background: linear-gradient(180deg, #2d1b3d 0%, #1a0d2e 100%);
            padding: 0;
            position: fixed;
            width: 250px;
            left: 0;
            top: 0;
            z-index: 1000;
            box-shadow: 2px 0 10px rgba(0,0,0,0.3);
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        .admin-sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .admin-sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }
        
        .admin-sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }
        
        .admin-sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .admin-main {
            margin-left: 250px;
            padding: 20px;
            background: linear-gradient(135deg, #2d1b3d 0%, #1a0d2e 100%);
            min-height: 100vh;
        }
        
        .admin-header {
            background: linear-gradient(135deg, #3d2a4d 0%, #2d1b3d 100%);
            color: #fff;
            padding: 15px 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
            border-radius: 12px;
        }
        
        .admin-header h4,
        .admin-header small {
            color: #fff !important;
        }
        
        .sidebar-brand {
            padding: 20px;
            color: #fff;
            font-size: 1.5rem;
            font-weight: bold;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            background: rgba(255,255,255,0.1);
        }
        
        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-menu li {
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        
        .sidebar-menu a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: rgba(255,255,255,0.9);
            text-decoration: none;
            transition: all 0.3s ease;
            position: relative;
        }
        
        .sidebar-menu a::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: #fff;
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }
        
        .sidebar-menu a:hover,
        .sidebar-menu a.active {
            background: rgba(255,255,255,0.2);
            color: #fff;
            padding-left: 25px;
            box-shadow: inset 0 0 10px rgba(255,255,255,0.1);
        }
        
        .sidebar-menu a.active::before,
        .sidebar-menu a:hover::before {
            transform: scaleY(1);
        }
        
        .sidebar-menu a i {
            width: 25px;
            margin-right: 10px;
        }
        
        .stat-card {
            background: #fff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            color: #333 !important;
        }
        
        .stat-card * {
            color: #333;
        }
        
        .stat-card h1, .stat-card h2, .stat-card h3,
        .stat-card h4, .stat-card h5, .stat-card h6 {
            color: #333 !important;
        }
        
        .stat-card p, .stat-card span, .stat-card div,
        .stat-card label, .stat-card strong, .stat-card small {
            color: #333 !important;
        }
        
        .stat-card .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #fff !important;
        }
        
        .stat-card .stat-icon i {
            color: #fff !important;
        }
        
        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: bold;
            margin: 10px 0;
            color: #333 !important;
        }
        
        .stat-card .stat-label {
            color: #666 !important;
            font-size: 0.9rem;
        }
        
        /* Badge trong stat-card giữ màu trắng */
        .stat-card .badge {
            color: #fff !important;
        }
        
        /* Button text trong stat-card */
        .stat-card .btn {
            color: #fff !important;
        }
        
        .stat-card .btn-outline-primary,
        .stat-card .btn-outline-secondary,
        .stat-card .btn-outline-info,
        .stat-card .btn-outline-success,
        .stat-card .btn-outline-warning,
        .stat-card .btn-outline-danger {
            color: inherit !important;
        }
        
        .stat-card .btn-outline-primary { color: #0d6efd !important; }
        .stat-card .btn-outline-secondary { color: #6c757d !important; }
        .stat-card .btn-outline-info { color: #0dcaf0 !important; }
        .stat-card .btn-outline-success { color: #198754 !important; }
        .stat-card .btn-outline-warning { color: #ffc107 !important; }
        .stat-card .btn-outline-danger { color: #dc3545 !important; }
        
        /* Link trong stat-card */
        .stat-card a:not(.btn) {
            color: #0d6efd !important;
        }
        
        /* Bootstrap text colors trong stat-card */
        .stat-card .text-primary { color: #0d6efd !important; }
        .stat-card .text-secondary { color: #6c757d !important; }
        .stat-card .text-success { color: #198754 !important; }
        .stat-card .text-danger { color: #dc3545 !important; }
        .stat-card .text-warning { color: #ffc107 !important; }
        .stat-card .text-info { color: #0dcaf0 !important; }
        .stat-card .text-muted { color: #6c757d !important; }
        .stat-card .text-dark { color: #212529 !important; }
        
        /* Table trong stat-card */
        .stat-card .table { color: #333 !important; }
        .stat-card .table th { color: #333 !important; background-color: #f8f9fa !important; }
        .stat-card .table td { color: #333 !important; }
        .stat-card .table td a { color: #0d6efd !important; }
        
        /* Form elements trong stat-card */
        .stat-card .form-control,
        .stat-card .form-select {
            color: #333 !important;
            background-color: #fff !important;
        }
        
        .stat-card .form-control::placeholder {
            color: #6c757d !important;
        }
        
        .stat-card .form-label {
            color: #333 !important;
        }
        
        /* Input group trong stat-card */
        .stat-card .input-group-text {
            color: #333 !important;
            background-color: #e9ecef !important;
        }
        
        /* List items trong stat-card */
        .stat-card .list-group-item {
            color: #333 !important;
            background-color: #fff !important;
        }
        
        .stat-card ul li,
        .stat-card ol li {
            color: #333 !important;
        }
        
        /* Phần nền sáng - chữ tối */
        .admin-main {
            color: #333;
        }
        
        /* Các phần tử trực tiếp trong admin-main (không nằm trong card trắng) */
        .admin-main > h4,
        .admin-main > h5,
        .admin-main > h6,
        .admin-main > p {
            color: #333;
        }
        
        /* Card trắng - chữ tối */
        .stat-card,
        .admin-header {
            color: #333;
        }
        
        .stat-card h4,
        .stat-card h5,
        .stat-card h6,
        .admin-header h4,
        .admin-header h5,
        .admin-header h6 {
            color: #333 !important;
        }
        
        /* Table trong card trắng - chữ tối */
        .stat-card .table,
        .admin-header .table {
            color: #333;
        }
        
        .stat-card .table th {
            color: #333;
            background-color: #f8f9fa;
        }
        
        .stat-card .table td {
            color: #333;
        }
        
        /* Form trong card trắng - chữ tối */
        .stat-card .form-control,
        .admin-header .form-control {
            color: #333;
            background-color: #fff;
        }
        
        .stat-card .form-control::placeholder,
        .admin-header .form-control::placeholder {
            color: #999;
        }
        
        /* Text muted trong card trắng */
        .stat-card .text-muted,
        .admin-header .text-muted {
            color: #666 !important;
            
        }
        
        /* List trong card trắng */
        .stat-card .list-unstyled li,
        .admin-header .list-unstyled li {
            color: #333;
        }
        
        .stat-card .list-unstyled li span,
        .admin-header .list-unstyled li span {
            color: #333;
        }
        
        /* Bold text trong card trắng */
        .stat-card .fw-bold,
        .admin-header .fw-bold {
            color: #333;
        }
        
        /* Small text trong card trắng */
        .stat-card small,
        .admin-header small {
            color: #666;
        }
        
        /* Paragraph trong card trắng */
        .stat-card p,
        .admin-header p {
            color: #333;
        }
        
        /* Buttons - giữ nguyên */
        .admin-main .btn {
            color: #fff;
        }
        
        /* Outline buttons trong card trắng */
        .stat-card .btn-outline-primary,
        .stat-card .btn-outline-secondary,
        .stat-card .btn-outline-info,
        .stat-card .btn-outline-success,
        .stat-card .btn-outline-danger,
        .admin-header .btn-outline-primary,
        .admin-header .btn-outline-secondary,
        .admin-header .btn-outline-info,
        .admin-header .btn-outline-success,
        .admin-header .btn-outline-danger {
            color: #333;
        }
        
        /* Admin header - nền trắng nên chữ tối */
        .admin-header {
            color: #333;
        }
        
        .admin-header h4 {
            color: #333;
        }
        
        .admin-header span {
            color: #333;
        }
        
        /* Các phần tử ngoài card (trên nền tối) - chữ sáng */
        .admin-main > .d-flex h5,
        .admin-main > .d-flex h6,
        .admin-main > .row h5,
        .admin-main > .row h6,
        .admin-main > form h5,
        .admin-main > form h6,
        .admin-main > .mb-4 h5,
        .admin-main > .mb-3 h5 {
            color: black !important;
        }
        
        /* Form elements ngoài card (trên nền tối) */
        .admin-main > form .form-control,
        .admin-main > form .form-select {
            color: #333;
            background-color: #fff;
        }
        
        .admin-main > form .form-control::placeholder {
            color: #999;
        }
        
        /* Text ngoài card */
        .admin-main > .text-muted {
            color: rgba(255, 255, 255, 0.6) !important;
        }
        
        .admin-main > p {
            color: #fff;
        }
        
        .admin-main > small {
            color: rgba(255, 255, 255, 0.7);
        }
        
        /* Stat info trong tickets - trong card trắng nên chữ tối */
        .stat-card .stat-info {
            color: #333;
        }
        
        .stat-card .stat-info .stat-label {
            color: #666;
        }
        
        .stat-card .stat-info .stat-value {
            color: #333;
        }
        
        /* Badge - giữ màu trắng cho text */
        .admin-main .badge {
            color: #fff !important;
        }
        
        /* Select dropdown - trong card trắng */
        .stat-card .form-select,
        .admin-header .form-select {
            color: #333;
            background-color: #fff;
        }
        
        /* Links - trong card trắng */
        .stat-card a,
        .admin-header a {
            color: #0d6efd;
        }
        
        .stat-card a:hover,
        .admin-header a:hover {
            color: #0a58ca;
        }
        
        /* Links ngoài card (trên nền tối) - màu sáng */
        .admin-main > a {
            color: #4da6ff;
        }
        
        .admin-main > a:hover {
            color: #66b3ff;
        }
        
        /* Chart text - trong card trắng */
        .stat-card canvas {
            color: #333;
        }
        
        /* Modal - nền trắng nên chữ tối */
        .admin-main .modal-content {
            color: #333;
            background-color: #fff;
        }
        
        .admin-main .modal-header,
        .admin-main .modal-body,
        .admin-main .modal-footer {
            color: #333;
        }
        
        /* Alert messages - giữ màu mặc định của Bootstrap */
        .admin-main .alert {
            color: inherit;
        }
        
        .admin-main .alert-success {
            background-color: #d1e7dd;
            border-color: #badbcc;
            color: #0f5132;
        }
        
        .admin-main .alert-danger {
            background-color: #f8d7da;
            border-color: #f5c2c7;
            color: #842029;
        }
        
        /* Pagination - trong card trắng */
        .stat-card .pagination .page-link {
            color: #0d6efd;
        }
        
        .stat-card .pagination .page-item.active .page-link {
            background-color: #0d6efd;
            border-color: #0d6efd;
            color: #fff;
        }
        
        /* Fix tiêu đề trùng màu - ngoài stat-card (trên nền tối) */
        .admin-main > .d-flex h5,
        .admin-main > .d-flex h4,
        .admin-main > .mb-4 > h5,
        .admin-main > .mb-3 > h5,
        .admin-main > .row > .col-12 > h5,
        .admin-main > h5,
        .admin-main > h4 {
            color: #fff !important;
        }
        
        /* Form ngoài stat-card */
        .admin-main > form .form-control,
        .admin-main > .mb-3 .form-control {
            color: #333 !important;
            background-color: #fff !important;
        }
        
        /* Modal - nền trắng nên chữ tối */
        .modal-content {
            color: #333 !important;
            background-color: #fff !important;
        }
        
        .modal-content * {
            color: #333;
        }
        
        .modal-content h1, .modal-content h2, .modal-content h3,
        .modal-content h4, .modal-content h5, .modal-content h6 {
            color: #333 !important;
        }
        
        .modal-content p, .modal-content span, .modal-content div,
        .modal-content label, .modal-content strong, .modal-content small {
            color: #333 !important;
        }
        
        .modal-content .form-control,
        .modal-content .form-select {
            color: #333 !important;
            background-color: #fff !important;
        }
        
        .modal-content .form-label {
            color: #333 !important;
        }
        
        .modal-content .text-muted {
            color: #6c757d !important;
        }
        
        .modal-content .text-danger {
            color: #dc3545 !important;
        }
        
        .modal-content .btn {
            color: #fff !important;
        }
        
        .modal-content .btn-secondary {
            background-color: #6c757d !important;
        }
        
        /* Responsive cho mobile */
        @media screen and (max-width: 768px) {
            .admin-sidebar {
                position: fixed;
                left: -250px;
                transition: left 0.3s ease;
                z-index: 9999;
            }
            
            .admin-sidebar.active {
                left: 0;
            }
            
            .admin-main {
                margin-left: 0;
                padding: 15px;
                padding-top: 70px;
            }
            
            .admin-header {
                padding: 10px 15px;
            }
            
            .admin-header h4 {
                font-size: 1rem;
            }
            
            .stat-card {
                padding: 15px;
            }
            
            /* Mobile menu toggle button */
            .mobile-menu-toggle {
                display: block !important;
                position: fixed;
                top: 15px;
                left: 15px;
                z-index: 10000;
                background: #1a1a2e;
                border: none;
                color: #fff;
                padding: 10px 15px;
                border-radius: 8px;
                font-size: 1.2rem;
            }
            
            /* Overlay khi menu mở */
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
            
            .sidebar-overlay.active {
                display: block;
            }
        }
        
        @media screen and (min-width: 769px) {
            .mobile-menu-toggle {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <!-- Mobile menu toggle -->
    <button class="mobile-menu-toggle" onclick="toggleAdminSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeAdminSidebar()"></div>
    
    <div class="admin-sidebar" id="adminSidebar">
        <div class="sidebar-brand">
            <i class="fas fa-film"></i> CineHub Admin
        </div>
        <ul class="sidebar-menu">
            <li><a href="?route=admin/index" class="<?php echo ($current_page ?? '') === 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a></li>
            <?php 
            // Chỉ hiển thị menu "Quản lý người dùng" cho admin (không phải moderator)
            $isModerator = false;
            if (isset($user['role']) && $user['role'] === 'moderator') {
                $isModerator = true;
            } else {
                try {
                    $isModerator = AdminMiddleware::isModerator($user['id'] ?? 0);
                } catch (Exception $e) {
                    // Bỏ qua nếu có lỗi
                }
            }
            if (!$isModerator): ?>
            <li><a href="?route=admin/users" class="<?php echo ($current_page ?? '') === 'users' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Quản lý người dùng
            </a></li>
            <?php endif; ?>
            <li><a href="?route=admin/movies" class="<?php echo ($current_page ?? '') === 'movies' ? 'active' : ''; ?>">
                <i class="fas fa-film"></i> Quản lý phim
            </a></li>
            <li><a href="?route=admin/categories" class="<?php echo ($current_page ?? '') === 'categories' ? 'active' : ''; ?>">
                <i class="fas fa-tags"></i> Quản lý thể loại
            </a></li>
            <li><a href="?route=admin/theaters" class="<?php echo ($current_page ?? '') === 'theaters' ? 'active' : ''; ?>">
                <i class="fas fa-building"></i> Quản lý rạp
            </a></li>
            <?php 
            // "Quản lý vé" và "Combo & Đồ ăn" chỉ dành cho moderator (admin của các rạp)
            // Admin chính không cần 2 phần này vì đã có moderator quản lý
            // Chỉ hiển thị nếu user là moderator (trường hợp moderator truy cập admin panel)
            if (isset($user['role']) && $user['role'] === 'moderator'): ?>
            <li><a href="?route=admin/tickets" class="<?php echo ($current_page ?? '') === 'tickets' ? 'active' : ''; ?>">
                <i class="fas fa-ticket-alt"></i> Quản lý vé
            </a></li>
            <li><a href="?route=admin/foodItems" class="<?php echo ($current_page ?? '') === 'food_items' ? 'active' : ''; ?>">
                <i class="fas fa-utensils"></i> Combo & Đồ ăn
            </a></li>
            <?php endif; ?>
            <li><a href="?route=admin/analytics" class="<?php echo ($current_page ?? '') === 'analytics' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i> Analytics & Báo cáo
            </a></li>
            <li><a href="?route=admin/support" class="<?php echo ($current_page ?? '') === 'support' ? 'active' : ''; ?>">
                <i class="fas fa-headset"></i> Hỗ trợ khách hàng
            </a></li>
            <li><a href="?route=admin/logs" class="<?php echo ($current_page ?? '') === 'logs' ? 'active' : ''; ?>">
                <i class="fas fa-history"></i> Lịch sử hoạt động
            </a></li>
            <li><a href="/">
                <i class="fas fa-home"></i> Về trang chủ
            </a></li>
            <li><a href="?route=auth/logout">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </a></li>
        </ul>
    </div>
    
    <div class="admin-main">
        <div class="admin-header">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0"><?php echo $title ?? 'Admin Panel'; ?></h4>
                <div class="d-flex align-items-center gap-3">
                    <span><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($user['name'] ?? 'Admin'); ?></span>
                </div>
            </div>
        </div>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="fas fa-check-circle"></i> <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="fas fa-exclamation-circle"></i> <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php echo $content; ?>
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
    
    // Đóng menu khi click vào link
    document.querySelectorAll('.sidebar-menu a').forEach(link => {
        link.addEventListener('click', closeAdminSidebar);
    });
    </script>
</body>
</html>

