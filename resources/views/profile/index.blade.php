@extends('layouts.app')

@php
    $user = auth()->user();
    $title = 'Hồ Sơ';
    $isAdmin = $user ? $user->isAdmin() : false;
    $isModerator = $user ? $user->isModerator() : false;
@endphp

@section('content')
<div class="profile-luxury-container">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="profile-luxury-sidebar">
                <!-- Avatar Section -->
                <div class="profile-avatar-wrapper" onclick="document.getElementById('avatarInput').click()">
                    <div class="profile-avatar-container">
                        @if ($user && $user->avatar)
                            <img src="{{ $user->avatar }}" alt="Avatar">
                        @else
                            <div class="avatar-placeholder-luxury">
                                <i class="fas fa-user"></i>
                            </div>
                        @endif
                    </div>
                    <div class="avatar-overlay">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>
                
                <!-- User Info -->
                <div class="profile-user-info">
                    <h2 class="profile-user-name">{{ $user ? $user->name : 'Người dùng' }}</h2>
                    <p class="profile-user-email">{{ $user ? $user->email : 'email@example.com' }}</p>
                </div>
                
                <!-- Navigation Menu -->
                <nav class="profile-nav-menu">
                    <a href="#personal-info" class="profile-nav-item active" onclick="switchProfileTab('personal-info', event)">
                        <i class="fas fa-user-circle"></i>
                        <span>Thông tin cá nhân</span>
                    </a>
                    <a href="#wallet" class="profile-nav-item" onclick="switchProfileTab('wallet', event)">
                        <i class="fas fa-wallet"></i>
                        <span>Ví điểm</span>
                    </a>
                    <a href="#security" class="profile-nav-item" onclick="switchProfileTab('security', event)">
                        <i class="fas fa-lock"></i>
                        <span>Bảo mật</span>
                    </a>
                    <a href="#preferences" class="profile-nav-item" onclick="switchProfileTab('preferences', event)">
                        <i class="fas fa-cog"></i>
                        <span>Tùy chỉnh</span>
                    </a>
                    <a href="#subscription" class="profile-nav-item" onclick="switchProfileTab('subscription', event)">
                        <i class="fas fa-crown"></i>
                        <span>Gói dịch vụ</span>
                    </a>
                </nav>
                
                <!-- Logout Button -->
                <form method="POST" action="{{ route('logout') }}" style="margin-top: 2rem;">
                    @csrf
                    <button type="submit" class="profile-logout-btn">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Đăng xuất</span>
                    </button>
                </form>
            </div>
        </div>
        
        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Personal Info Section -->
            <div id="personal-info" class="profile-section active">
                <div class="section-header">
                    <h2>Thông tin cá nhân</h2>
                    <button class="btn-edit" onclick="toggleEditMode('personal')">
                        <i class="fas fa-edit"></i>
                    </button>
                </div>
                
                <form id="personalForm" method="POST" action="{{ route('profile.update') }}" class="profile-form">
                    @csrf
                    @method('PUT')
                    <div class="form-row">
                        <div class="form-group">
                            <label>Họ và tên</label>
                            <input type="text" name="name" value="{{ $user ? $user->name : '' }}" placeholder="Nhập họ và tên" class="form-control" disabled>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="{{ $user ? $user->email : '' }}" placeholder="Nhập email" class="form-control" disabled>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <input type="tel" name="phone" value="{{ $user ? $user->phone ?? '' : '' }}" placeholder="Nhập số điện thoại" class="form-control" disabled>
                        </div>
                        <div class="form-group">
                            <label>Ngày sinh</label>
                            <input type="date" name="birth_date" value="{{ $user && $user->birth_date ? date('Y-m-d', strtotime($user->birth_date)) : '' }}" class="form-control" disabled>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label>Địa chỉ</label>
                            <input type="text" name="address" value="{{ $user ? $user->address ?? '' : '' }}" placeholder="Nhập địa chỉ" class="form-control" disabled>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn-primary" style="display: none;">Lưu thay đổi</button>
                        <button type="button" class="btn-secondary" onclick="cancelEdit('personal')" style="display: none;">Hủy</button>
                    </div>
                </form>
            </div>
            
            <!-- Wallet Section -->
            <div id="wallet" class="profile-section" style="display: none;">
                <div class="section-header">
                    <h2>Ví điểm</h2>
                </div>
                
                <div class="wallet-balance">
                    <div class="balance-card">
                        <div class="balance-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div class="balance-info">
                            <p class="balance-label">Số dư hiện tại</p>
                            <h3 class="balance-amount">{{ number_format($user ? $user->points : 0) }} điểm</h3>
                        </div>
                    </div>
                </div>
                
                <div class="deposit-form">
                    <h4>Nạp điểm qua VNPay</h4>
                    <form method="POST" action="{{ route('profile.depositVnpay') }}">
                        @csrf
                        <div class="form-group">
                            <label>Số điểm muốn nạp (1 điểm = 1 VNĐ)</label>
                            <select name="points" class="form-control">
                                <option value="10000">10,000 điểm (10,000 VNĐ)</option>
                                <option value="20000">20,000 điểm (20,000 VNĐ)</option>
                                <option value="50000">50,000 điểm (50,000 VNĐ)</option>
                                <option value="100000">100,000 điểm (100,000 VNĐ)</option>
                                <option value="200000">200,000 điểm (200,000 VNĐ)</option>
                                <option value="500000">500,000 điểm (500,000 VNĐ)</option>
                            </select>
                        </div>
                        <button type="submit" class="btn-primary">
                            <i class="fas fa-credit-card"></i> Nạp điểm qua VNPay
                        </button>
                    </form>
                </div>
            </div>
            
            <!-- Security Section -->
            <div id="security" class="profile-section" style="display: none;">
                <div class="section-header">
                    <h2>Bảo mật</h2>
                </div>
                
                <form method="POST" action="{{ route('profile.updatePassword') }}" class="profile-form">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label>Mật khẩu hiện tại</label>
                        <input type="password" name="current_password" placeholder="Nhập mật khẩu hiện tại" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Mật khẩu mới</label>
                        <input type="password" name="new_password" placeholder="Nhập mật khẩu mới" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Xác nhận mật khẩu</label>
                        <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu" class="form-control" required>
                    </div>
                    
                    <button type="submit" class="btn-primary">Cập nhật mật khẩu</button>
                </form>
            </div>
            
            <!-- Preferences Section -->
            <div id="preferences" class="profile-section" style="display: none;">
                <div class="section-header">
                    <h2>Tùy chỉnh</h2>
                </div>
                
                <form method="POST" action="{{ url('/?route=profile/updatePreferences') }}" class="profile-form">
                    @csrf
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="newsletter" value="1" @if($user && $user->preferences && $user->preferences->newsletter) checked @endif>
                            <span>Đăng ký nhận tin tức</span>
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="notifications" value="1" @if($user && $user->preferences && $user->preferences->notifications) checked @endif>
                            <span>Nhận thông báo</span>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn-primary">Lưu tùy chỉnh</button>
                </form>
            </div>
            
            <!-- Subscription Section -->
            <div id="subscription" class="profile-section" style="display: none;">
                <div class="section-header">
                    <h2>Gói dịch vụ</h2>
                </div>
                
                <div class="subscription-info">
                    @if ($user && $user->subscription)
                        <p><strong>Gói hiện tại:</strong> {{ $user->subscription->name }}</p>
                        <p><strong>Ngày hết hạn:</strong> {{ $user->subscription->expires_at ? date('d/m/Y', strtotime($user->subscription->expires_at)) : 'Không xác định' }}</p>
                        <a href="{{ url('/?route=subscription/upgrade') }}" class="btn-primary">Nâng cấp</a>
                    @else
                        <p>Bạn chưa có gói dịch vụ nào. Hãy chọn một gói để tận hưởng các lợi ích.</p>
                        <a href="{{ url('/?route=subscription/plans') }}" class="btn-primary">Xem các gói</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Avatar Input -->
<input type="file" id="avatarInput" accept="image/*" style="display: none;" onchange="uploadAvatar(this.files[0])">

<style>
    .profile-luxury-container {
        max-width: 1400px;
        margin: 0 auto;
        padding: 2rem 1rem;
    }
    
    .profile-luxury-sidebar {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        border-radius: 24px;
        padding: 3rem 2rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        position: sticky;
        top: 2rem;
        height: fit-content;
        max-height: calc(100vh - 4rem);
        overflow-y: auto;
    }
    
    .profile-avatar-wrapper {
        position: relative;
        width: 160px;
        height: 160px;
        margin: 0 auto 2rem;
        cursor: pointer;
    }
    
    .profile-avatar-container {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid rgba(255, 255, 255, 0.1);
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
    }
    
    .profile-avatar-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .avatar-placeholder-luxury {
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #2d2d2d 0%, #1a1a1a 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(255, 255, 255, 0.3);
        font-size: 4rem;
    }
    
    .avatar-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s;
        color: #fff;
        font-size: 1.5rem;
    }
    
    .profile-avatar-wrapper:hover .avatar-overlay {
        opacity: 1;
    }
    
    .profile-user-info {
        text-align: center;
        margin-bottom: 2rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 2rem;
    }
    
    .profile-user-name {
        font-size: 1.3rem;
        color: #fff;
        margin: 0 0 0.5rem 0;
    }
    
    .profile-user-email {
        font-size: 0.9rem;
        color: #999;
        margin: 0;
    }
    
    .profile-nav-menu {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .profile-nav-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem 1rem;
        color: #999;
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.3s;
    }
    
    .profile-nav-item:hover,
    .profile-nav-item.active {
        background: rgba(229, 9, 20, 0.2);
        color: #e50914;
    }
    
    .profile-logout-btn {
        width: 100%;
        padding: 0.75rem 1rem;
        background: #e50914;
        color: white;
        border: none;
        border-radius: 8px;
        font-size: 0.95rem;
        cursor: pointer;
        transition: background 0.3s;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }
    
    .profile-logout-btn:hover {
        background: #ff1f1f;
    }
    
    .profile-section {
        background: #1a1a1a;
        border-radius: 12px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
    }
    
    .profile-section.active {
        display: block;
    }
    
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 2rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 1rem;
    }
    
    .section-header h2 {
        margin: 0;
        color: #fff;
        font-size: 1.3rem;
    }
    
    .btn-edit {
        background: #e50914;
        color: white;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.3s;
    }
    
    .btn-edit:hover {
        background: #ff1f1f;
    }
    
    .profile-form {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 1rem;
    }
    
    .form-row.full-width {
        grid-template-columns: 1fr;
    }
    
    .form-group {
        display: flex;
        flex-direction: column;
    }
    
    .form-group.full-width {
        grid-column: 1 / -1;
    }
    
    .form-group label {
        color: #fff;
        font-size: 0.95rem;
        margin-bottom: 0.5rem;
        font-weight: 500;
    }
    
    .form-control {
        padding: 0.75rem 1rem;
        background: #2d2d2d;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 6px;
        color: #fff;
        font-size: 0.95rem;
        transition: all 0.3s;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #e50914;
        box-shadow: 0 0 10px rgba(229, 9, 20, 0.3);
    }
    
    .form-control:disabled {
        background: #1f1f1f;
        color: #999;
        cursor: not-allowed;
    }
    
    .checkbox-label {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        cursor: pointer;
        color: #fff;
    }
    
    .checkbox-label input[type="checkbox"] {
        cursor: pointer;
    }
    
    .btn-primary {
        padding: 0.75rem 1.5rem;
        background: #e50914;
        color: white;
        border: none;
        border-radius: 6px;
        font-size: 0.95rem;
        cursor: pointer;
        transition: background 0.3s;
        align-self: flex-start;
    }
    
    .btn-primary:hover {
        background: #ff1f1f;
    }
    
    .btn-secondary {
        padding: 0.75rem 1.5rem;
        background: transparent;
        color: #999;
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 6px;
        font-size: 0.95rem;
        cursor: pointer;
        transition: all 0.3s;
        align-self: flex-start;
    }
    
    .btn-secondary:hover {
        background: rgba(255, 255, 255, 0.1);
        color: #fff;
    }
    
    .subscription-info {
        background: rgba(229, 9, 20, 0.1);
        border: 1px solid rgba(229, 9, 20, 0.3);
        border-radius: 8px;
        padding: 2rem;
        text-align: center;
    }
    
    .subscription-info p {
        color: #fff;
        margin: 0.5rem 0;
    }
    
    .wallet-balance {
        margin-bottom: 2rem;
    }
    
    .balance-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        padding: 2rem;
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }
    
    .balance-icon {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: white;
    }
    
    .balance-info {
        flex: 1;
    }
    
    .balance-label {
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.9rem;
        margin: 0 0 0.5rem 0;
    }
    
    .balance-amount {
        color: white;
        font-size: 2rem;
        margin: 0;
        font-weight: bold;
    }
    
    .deposit-form {
        background: rgba(229, 9, 20, 0.1);
        border: 1px solid rgba(229, 9, 20, 0.3);
        border-radius: 12px;
        padding: 2rem;
    }
    
    .deposit-form h4 {
        color: #fff;
        margin: 0 0 1.5rem 0;
        font-size: 1.1rem;
    }
    
    @media (max-width: 768px) {
        .profile-luxury-container {
            padding: 1rem;
        }
        
        .row {
            flex-direction: column-reverse;
        }
        
        .col-lg-3,
        .col-lg-9 {
            max-width: 100%;
        }
        
        .profile-luxury-sidebar {
            position: static;
            margin-bottom: 2rem;
        }
        
        .profile-section {
            padding: 1rem;
        }
    }
</style>

<script>
    function switchProfileTab(tabId, event) {
        if (event) event.preventDefault();
        
        // Hide all sections
        const sections = document.querySelectorAll('.profile-section');
        sections.forEach(section => section.style.display = 'none');
        
        // Remove active class from all nav items
        const navItems = document.querySelectorAll('.profile-nav-item');
        navItems.forEach(item => item.classList.remove('active'));
        
        // Show selected section
        const selectedSection = document.getElementById(tabId);
        if (selectedSection) {
            selectedSection.style.display = 'block';
        }
        
        // Add active class to clicked nav item
        event.target.closest('.profile-nav-item').classList.add('active');
    }
    
    function toggleEditMode(section) {
        const form = document.getElementById(section + 'Form');
        const inputs = form.querySelectorAll('input:not([type="hidden"]), textarea, select');
        const isDisabled = inputs[0].disabled;
        
        inputs.forEach(input => {
            input.disabled = !isDisabled;
        });
        
        // Toggle button visibility
        const buttons = form.querySelectorAll('.form-actions button');
        buttons.forEach(btn => {
            btn.style.display = isDisabled ? 'inline-block' : 'none';
        });
    }
    
    function cancelEdit(section) {
        toggleEditMode(section);
        // Reload page or reset form
        location.reload();
    }
    
    function uploadAvatar(file) {
        if (!file) return;
        
        const formData = new FormData();
        formData.append('avatar', file);
        formData.append('_token', '{{ csrf_token() }}');
        
        fetch('{{ route("profile.uploadAvatar") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Lỗi: ' + (data.message || 'Không thể tải ảnh lên'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Lỗi khi tải ảnh lên');
        });
    }
</script>
@endsection
