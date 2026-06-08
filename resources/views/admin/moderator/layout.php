<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title . ' - ' : ''; ?>Quản lý rạp - CineHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <?php
    // Sử dụng UrlHelper để lấy base URL
    if (!class_exists('UrlHelper')) {
        require_once __DIR__ . '/../../../core/UrlHelper.php';
    }
    $baseUrl = UrlHelper::getBaseUrl();
    ?>
    <link rel="stylesheet" href="<?php echo htmlspecialchars($baseUrl); ?>/style.css?v=<?php echo time(); ?>">
    <style>
        .moderator-sidebar {
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
        
        .moderator-sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .moderator-sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }
        
        .moderator-sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
        }
        
        .moderator-sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        .moderator-main {
            margin-left: 250px;
            padding: 20px;
            background: linear-gradient(135deg, #2d1b3d 0%, #1a0d2e 100%);
            min-height: 100vh;
        }
        
        .moderator-header {
            background: linear-gradient(135deg, #3d2a4d 0%, #2d1b3d 100%);
            color: #fff;
            padding: 15px 30px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
            margin-bottom: 20px;
            border-radius: 12px;
        }
        
        .moderator-header h4,
        .moderator-header small {
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
        
        /* Đảm bảo text trong card trắng có màu tối */
        .stat-card,
        .moderator-header {
            color: #333;
        }
        
        .stat-card h4,
        .stat-card h5,
        .stat-card h6,
        .moderator-header h4,
        .moderator-header h5,
        .moderator-header h6 {
            color: #333 !important;
        }
        
        .stat-card .table {
            color: #333;
        }
        
        .stat-card .table th {
            color: #333;
            background-color: #f8f9fa;
        }
        
        .stat-card .table td {
            color: #333;
        }
        
        .stat-card .form-control {
            color: #333;
            background-color: #fff;
        }
        
        .stat-card .text-muted {
            color: #666 !important;
        }
        
        .moderator-main {
            color: #fff;
        }
        
        .moderator-main {
            color: #333;
        }
        
        .moderator-main > h4,
        .moderator-main > h5,
        .moderator-main > h6 {
            color: #333 !important;
        }
        
        /* Button styles trên nền tối - đảm bảo màu nổi bật */
        .moderator-main .btn-primary {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
            color: #fff !important;
        }
        
        .moderator-main .btn-primary:hover {
            background-color: #0b5ed7 !important;
            border-color: #0a58ca !important;
            color: #fff !important;
        }
        
        .moderator-main .btn-secondary {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: #fff !important;
        }
        
        .moderator-main .btn-secondary:hover {
            background-color: #5c636a !important;
            border-color: #565e64 !important;
            color: #fff !important;
        }
        
        .moderator-main .btn-outline-primary {
            border-color: #0d6efd !important;
            color: #0d6efd !important;
            background-color: transparent !important;
        }
        
        .moderator-main .btn-outline-primary:hover {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
            color: #fff !important;
        }
        
        .moderator-main .btn-outline-secondary {
            border-color: #6c757d !important;
            color: #6c757d !important;
            background-color: transparent !important;
        }
        
        .moderator-main .btn-outline-secondary:hover {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: #fff !important;
        }
        
        .moderator-main .btn-outline-danger {
            border-color: #dc3545 !important;
            color: #dc3545 !important;
            background-color: transparent !important;
        }
        
        .moderator-main .btn-outline-danger:hover {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: #fff !important;
        }
        
        .moderator-main .btn-outline-info {
            border-color: #0dcaf0 !important;
            color: #0dcaf0 !important;
            background-color: transparent !important;
        }
        
        .moderator-main .btn-outline-info:hover {
            background-color: #0dcaf0 !important;
            border-color: #0dcaf0 !important;
            color: #fff !important;
        }
        
        .moderator-main .btn-outline-success {
            border-color: #198754 !important;
            color: #198754 !important;
            background-color: transparent !important;
        }
        
        .moderator-main .btn-outline-success:hover {
            background-color: #198754 !important;
            border-color: #198754 !important;
            color: #fff !important;
        }
        
        /* Button trong stat-card (nền trắng) - đảm bảo màu rõ ràng */
        .stat-card .btn-outline-primary {
            border-color: #0d6efd !important;
            color: #0d6efd !important;
        }
        
        .stat-card .btn-outline-primary:hover {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
            color: #fff !important;
        }
        
        .stat-card .btn-outline-secondary {
            border-color: #6c757d !important;
            color: #6c757d !important;
        }
        
        .stat-card .btn-outline-secondary:hover {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: #fff !important;
        }
        
        .stat-card .btn-outline-danger {
            border-color: #dc3545 !important;
            color: #dc3545 !important;
        }
        
        .stat-card .btn-outline-danger:hover {
            background-color: #dc3545 !important;
            border-color: #dc3545 !important;
            color: #fff !important;
        }
        
        .stat-card .btn-primary {
            background-color: #0d6efd !important;
            border-color: #0d6efd !important;
            color: #fff !important;
        }
        
        .stat-card .btn-primary:hover {
            background-color: #0b5ed7 !important;
            border-color: #0a58ca !important;
            color: #fff !important;
        }
        
        .stat-card .btn-secondary {
            background-color: #6c757d !important;
            border-color: #6c757d !important;
            color: #fff !important;
        }
        
        .stat-card .btn-secondary:hover {
            background-color: #5c636a !important;
            border-color: #565e64 !important;
            color: #fff !important;
        }
        
        /* Fix tiêu đề trùng màu - ngoài stat-card (trên nền tối) */
        .moderator-main > .d-flex h5,
        .moderator-main > .d-flex h4,
        .moderator-main > .mb-4 > h5,
        .moderator-main > .mb-3 > h5,
        .moderator-main > .row > .col-12 > h5,
        .moderator-main > h5,
        .moderator-main > h4 {
            color: #fff !important;
        }
        
        /* Form ngoài stat-card */
        .moderator-main > form .form-control,
        .moderator-main > .mb-3 .form-control {
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
            .moderator-sidebar {
                position: fixed;
                left: -250px;
                transition: left 0.3s ease;
                z-index: 9999;
            }
            
            .moderator-sidebar.active {
                left: 0;
            }
            
            .moderator-main {
                margin-left: 0;
                padding: 15px;
                padding-top: 70px;
            }
            
            .moderator-header {
                padding: 10px 15px;
            }
            
            .moderator-header h4 {
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
    <button class="mobile-menu-toggle" onclick="toggleModeratorSidebar()">
        <i class="fas fa-bars"></i>
    </button>
    
    <!-- Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeModeratorSidebar()"></div>
    
    <div class="moderator-sidebar" id="moderatorSidebar">
        <div class="sidebar-brand">
            <i class="fas fa-building"></i> Quản lý rạp
        </div>
        <ul class="sidebar-menu">
            <?php
            // Kiểm tra xem user có phải Counter Staff không
            $isCounterStaff = false;
            try {
                $isCounterStaff = AdminMiddleware::isCounterStaff($user['id'] ?? 0);
            } catch (Exception $e) {}
            
            // Counter Staff không thấy Dashboard, Thông tin rạp, và Yêu cầu thay đổi quyền
            if (!$isCounterStaff):
            ?>
            <li><a href="?route=moderator/index" class="<?php echo ($current_page ?? '') === 'dashboard' ? 'active' : ''; ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a></li>
            <li><a href="?route=moderator/theater" class="<?php echo ($current_page ?? '') === 'theater' ? 'active' : ''; ?>">
                <i class="fas fa-building"></i> Thông tin rạp
            </a></li>
            <?php endif; ?>
            <li><a href="?route=moderator/screens" class="<?php echo ($current_page ?? '') === 'screens' ? 'active' : ''; ?>">
                <i class="fas fa-door-open"></i> Quản lý phòng
            </a></li>
            <li><a href="?route=moderator/showtimes" class="<?php echo ($current_page ?? '') === 'showtimes' ? 'active' : ''; ?>">
                <i class="fas fa-calendar-alt"></i> Lịch chiếu
            </a></li>
            <li><a href="?route=moderator/tickets" class="<?php echo ($current_page ?? '') === 'tickets' ? 'active' : ''; ?>">
                <i class="fas fa-ticket-alt"></i> Quản lý vé
            </a></li>
            <li><a href="?route=moderator/foodItems" class="<?php echo ($current_page ?? '') === 'food_items' ? 'active' : ''; ?>">
                <i class="fas fa-utensils"></i> Combo & Đồ ăn
            </a></li>
            <?php if (!$isCounterStaff): ?>
            <li><a href="?route=moderator/statistics" class="<?php echo ($current_page ?? '') === 'statistics' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i> Thống kê
            </a></li>
            <?php
            // Admin Rạp hoặc Moderator có thể quản lý nhân viên
            $isTheaterAdmin = false;
            $isModerator = false;
            try {
                $isTheaterAdmin = AdminMiddleware::isTheaterAdmin($user['id'] ?? 0);
                $isModerator = AdminMiddleware::isModerator($user['id'] ?? 0);
            } catch (Exception $e) {}
            if ($isTheaterAdmin || $isModerator):
            ?>
            <li><a href="?route=moderator/counterStaff" class="<?php echo ($current_page ?? '') === 'counter_staff' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i> Quản lý nhân viên
            </a></li>
            <?php endif; ?>
            <li><a href="?route=moderator/permissionRequests" class="<?php echo ($current_page ?? '') === 'permission_requests' ? 'active' : ''; ?>">
                <i class="fas fa-user-shield"></i> Yêu cầu thay đổi quyền
                <?php
                // Đếm số yêu cầu chưa xử lý
                try {
                    $db = Database::getInstance();
                    $pendingCount = $db->fetch("
                        SELECT COUNT(*) as count 
                        FROM moderator_permission_requests 
                        WHERE theater_id = ? AND status = 'pending'
                    ", [$theaterId])['count'] ?? 0;
                    if ($pendingCount > 0):
                ?>
                    <span class="badge bg-danger ms-2"><?php echo $pendingCount; ?></span>
                <?php endif; } catch (Exception $e) {} ?>
            </a></li>
            <?php endif; ?>
            <li><a href="/">
                <i class="fas fa-home"></i> Về trang chủ
            </a></li>
            <li><a href="?route=profile/index">
                <i class="fas fa-user"></i> Hồ sơ
            </a></li>
            <li><a href="?route=auth/logout">
                <i class="fas fa-sign-out-alt"></i> Đăng xuất
            </a></li>
        </ul>
    </div>
    
    <div class="moderator-main">
        <div class="moderator-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-0"><?php echo $title ?? 'Quản lý rạp'; ?></h4>
                    <?php if (isset($theater)): ?>
                        <small class="text-muted"><?php echo htmlspecialchars($theater['name']); ?> - <?php echo htmlspecialchars($theater['location'] ?? ''); ?></small>
                    <?php endif; ?>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <span><i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($user['name'] ?? 'Moderator'); ?></span>
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
    
    // Đóng menu khi click vào link
    document.querySelectorAll('.sidebar-menu a').forEach(link => {
        link.addEventListener('click', closeModeratorSidebar);
    });
    </script>
</body>
</html>


