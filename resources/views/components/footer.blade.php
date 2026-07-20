<footer class="footer">
    <div class="container">
        <div class="footer-content">
            <div class="footer-section">
                <div class="footer-logo">
                    <i class="fas fa-film"></i>
                    <span>CineHub</span>
                </div>
                <p class="footer-description">
                    Nền tảng xem phim trực tuyến hàng đầu Việt Nam. 
                    Xem phim chất lượng cao, không giới hạn với nhiều thể loại đa dạng.
                </p>
                <div class="footer-social">
                    <a href="#" class="social-link" title="Facebook">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="#" class="social-link" title="YouTube">
                        <i class="fab fa-youtube"></i>
                    </a>
                    <a href="#" class="social-link" title="Instagram">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="#" class="social-link" title="Twitter">
                        <i class="fab fa-twitter"></i>
                    </a>
                </div>
            </div>
            
            <div class="footer-section">
                <h3 class="footer-title">Danh mục</h3>
                <ul class="footer-links">
                    <li><a href="{{ route('movies.index') }}">Phim mới</a></li>
                    <li><a href="{{ route('movies.index') }}">Phim hot</a></li>
                    <li><a href="{{ route('movies.phimle') }}">Phim lẻ</a></li>
                    <li><a href="{{ route('movies.phimbo') }}">Phim bộ</a></li>
                    <li><a href="{{ route('movies.index') }}">Phim hoạt hình</a></li>
                </ul>
            </div>
            
            <div class="footer-section">
                <h3 class="footer-title">Thể loại</h3>
                <ul class="footer-links">
                    @php
                        $categories = \App\Models\Category::limit(5)->get();
                    @endphp
                    @foreach($categories as $index => $category)
                        <li><a href="{{ route('movies.category', $category->id) }}">{{ $category->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            
            <div class="footer-section">
                <h3 class="footer-title">Hỗ trợ</h3>
                <ul class="footer-links">
                    <li><a href="#">Câu hỏi thường gặp</a></li>
                    <li><a href="{{ route('terms') }}">Điều khoản dịch vụ</a></li>
                    <li><a href="#">Chính sách bảo mật</a></li>
                    <li><a href="#">Liên hệ</a></li>
                    <li><a href="{{ route('movies.theater') }}">Đặt vé xem phim</a></li>
                </ul>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>&copy; {{ date('Y') }} CineHub. Tất cả quyền được bảo lưu.</p>
            <div class="footer-payment">
                <span>Chấp nhận thanh toán:</span>
                <i class="fab fa-cc-visa" title="Visa"></i>
                <i class="fab fa-cc-mastercard" title="Mastercard"></i>
                <i class="fas fa-mobile-alt" title="Momo"></i>
            </div>
        </div>
    </div>
</footer>

<script>
    // Notification Dropdown
    let notificationDropdownOpen = false;
    
    function toggleNotificationDropdown() {
        const dropdown = document.getElementById('notificationDropdown');
        if (!dropdown) return;
        
        notificationDropdownOpen = !notificationDropdownOpen;
        
        if (notificationDropdownOpen) {
            dropdown.style.display = 'block';
            loadNotifications();
        } else {
            dropdown.style.display = 'none';
        }
    }
    
    function loadNotifications() {
        const list = document.getElementById('notificationList');
        if (!list) return;
        
        list.innerHTML = '<div class="notification-loading"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>';
        
        fetch('{{ route('notifications.list') }}?limit=10', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    return response.text().then(text => {
                        throw new Error('Response is not JSON: ' + text.substring(0, 200));
                    });
                }
                return response.json();
            })
            .then(data => {
                console.log('Notifications data received:', data);
                if (data && data.notifications && Array.isArray(data.notifications) && data.notifications.length > 0) {
                    let html = '';
                    data.notifications.forEach(notif => {
                        const iconClass = getIconClass(notif.type);
                        const iconName = getIconName(notif.type);
                        const unreadClass = notif.is_read == 0 ? 'unread' : '';
                        const link = notif.link || '{{ route('notifications.index') }}';
                        
                        html += `
                            <div class="notification-item-dropdown ${unreadClass}" onclick="window.location.href='${link}'; markAsRead(${notif.id});">
                                <div class="notification-icon-dropdown ${iconClass}">
                                    <i class="fas ${iconName}"></i>
                                </div>
                                <div class="notification-content-dropdown">
                                    <div class="notification-title-dropdown">${escapeHtml(notif.title)}</div>
                                    <div class="notification-message-dropdown">${escapeHtml(notif.message)}</div>
                                    <div class="notification-time-dropdown">${notif.time_ago || 'Vừa xong'}</div>
                                </div>
                            </div>
                        `;
                    });
                    list.innerHTML = html;
                } else {
                    console.log('No notifications found or empty array');
                    if (data && data.error) {
                        list.innerHTML = '<div class="notification-empty"><i class="fas fa-exclamation-circle"></i>Lỗi: ' + escapeHtml(data.error) + '</div>';
                    } else {
                        list.innerHTML = '<div class="notification-empty"><i class="fas fa-bell-slash"></i>Không có thông báo</div>';
                    }
                }
            })
            .catch(error => {
                console.error('Error loading notifications:', error);
                console.error('Error details:', error.message, error.stack);
                list.innerHTML = '<div class="notification-empty"><i class="fas fa-exclamation-circle"></i>Không thể tải thông báo: ' + escapeHtml(error.message) + '</div>';
            });
    }
    
    function getIconClass(type) {
        const map = {
            'success': 'success',
            'warning': 'warning',
            'error': 'error',
            'booking': 'booking',
            'info': 'info'
        };
        return map[type] || 'info';
    }
    
    function getIconName(type) {
        const map = {
            'success': 'fa-check-circle',
            'warning': 'fa-exclamation-triangle',
            'error': 'fa-times-circle',
            'booking': 'fa-ticket-alt',
            'info': 'fa-info-circle'
        };
        return map[type] || 'fa-info-circle';
    }
    
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    function markAsRead(notificationId) {
        fetch('{{ url('/notifications') }}/' + notificationId + '/read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        }).catch(error => console.error('Error marking notification as read:', error));
    }
    
    // Auth Modal Functions
    function openAuthModal(tab) {
        const modal = document.getElementById('authModal');
        if (modal) {
            modal.style.display = 'flex';
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
            switchAuthTab(tab);
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
        const isLogin = tab === 'login';
        
        if (loginTab) {
            loginTab.style.display = isLogin ? 'block' : 'none';
            loginTab.classList.toggle('active', isLogin);
        }

        if (registerTab) {
            registerTab.style.display = isLogin ? 'none' : 'block';
            registerTab.classList.toggle('active', !isLogin);
        }
        
        tabs.forEach(t => t.classList.remove('active'));
        if (tabs.length >= 2) {
            tabs[isLogin ? 0 : 1].classList.add('active');
        }
    }
    
    function closeAlertModal() {
        const modal = document.getElementById('alertModal');
        if (modal) {
            modal.style.display = 'none';
        }
    }
    
    function showAlertModal(type, message) {
        const modal = document.getElementById('alertModal');
        if (modal) {
            modal.style.display = 'flex';
            setTimeout(() => {
                modal.style.display = 'none';
            }, 3000);
        }
    }
    
    // Close modal when clicking outside
    document.addEventListener('click', function(event) {
        const modal = document.getElementById('authModal');
        if (modal && event.target === modal) {
            closeAuthModal();
        }
        
        const notification = document.getElementById('notificationWrapper');
        if (notification && !notification.contains(event.target)) {
            notificationDropdownOpen = false;
            const dropdown = document.getElementById('notificationDropdown');
            if (dropdown) dropdown.style.display = 'none';
        }
    });
</script>
