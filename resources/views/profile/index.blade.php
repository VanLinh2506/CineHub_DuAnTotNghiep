@extends('layouts.app')

@php
    $user = auth()->user();
    $title = 'Hồ Sơ';
    $isAdmin = $user ? $user->isAdmin() : false;
    $isModerator = $user ? $user->isModerator() : false;
    $nextNameChangeAt = $user?->name_changed_at?->copy()->addDays(15);
    $canChangeName = !$nextNameChangeAt || $nextNameChangeAt->isPast();
@endphp

@section('content')
<div class="profile-luxury-container">
    <div class="row g-4 align-items-start">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="profile-luxury-sidebar">
                <!-- Avatar Section -->
                <div class="profile-avatar-wrapper" onclick="document.getElementById('avatarInput').click()">
                    <div class="profile-avatar-container">
                        @if ($user && $user->avatar)
                            <img src="{{ $user->avatar_url }}" alt="Avatar">
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
                    <a href="#interests" class="profile-nav-item profile-nav-interest" onclick="switchProfileTab('interests', event)">
                        <i class="fas fa-bell"></i>
                        <span>Phim tôi quan tâm</span>
                        <b class="interest-nav-count">{{ ($interestedMovies ?? collect())->count() }}</b>
                    </a>
                    <a href="#tickets" class="profile-nav-item" onclick="switchProfileTab('tickets', event)">
                        <i class="fas fa-ticket-alt"></i>
                        <span>Vé của tôi</span>
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
            <!-- Alert Messages -->
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Có lỗi xảy ra:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <!-- Personal Info Section -->
            <div id="personal-info" class="profile-section active">
                <div class="section-header">
                    <h2>Thông tin cá nhân</h2>
                    <div class="profile-header-actions">
                        <a href="#interests" class="interest-shortcut" onclick="switchProfileTab('interests', event)">
                            <i class="fas fa-bell"></i> Phim quan tâm
                            <b>{{ ($interestedMovies ?? collect())->count() }}</b>
                        </a>
                        <button class="btn-edit" onclick="toggleEditMode('personal')"><i class="fas fa-edit"></i></button>
                    </div>
                </div>
                
                <form id="personalForm" method="POST" action="{{ route('profile.update') }}" class="profile-form">
                    @csrf
                    @method('PUT')
                    <div class="form-row">
                        <div class="form-group">
                            <label>Họ và tên</label>
                            <input type="text" name="name" value="{{ $user ? $user->name : '' }}" placeholder="Nhập họ và tên" class="form-control" disabled data-profile-locked="{{ $canChangeName ? 'false' : 'true' }}">
                            @unless($canChangeName)<input type="hidden" name="name" value="{{ $user->name }}">@endunless
                            @if($canChangeName)
                                <small class="profile-field-note">Bạn có thể đổi tên ngay. Sau khi lưu phải chờ 15 ngày cho lần tiếp theo.</small>
                            @else
                                <small class="profile-field-note locked"><i class="fas fa-clock"></i> Có thể đổi lại sau {{ $nextNameChangeAt->format('H:i d/m/Y') }}.</small>
                            @endif
                            @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="{{ $user ? $user->email : '' }}" placeholder="Nhập email" class="form-control" disabled>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Số điện thoại</label>
                            <div class="address-input-group">
                                <input type="tel" id="profilePhone" name="phone" value="{{ $user ? $user->phone ?? '' : '' }}" placeholder="Nhập số điện thoại" class="form-control" maxlength="20" autocomplete="tel" disabled>
                                <button type="button" class="location-button" onclick="enablePersonalField('profilePhone')">
                                    <i class="fas fa-pen"></i><span>Thay đổi</span>
                                </button>
                            </div>
                            <small class="profile-field-note">Số điện thoại có thể thay đổi bất kỳ lúc nào.</small>
                        </div>
                        <div class="form-group">
                            <label>Ngày sinh</label>
                            <input type="date" name="birthdate" value="{{ $user && $user->birthdate ? date('Y-m-d', strtotime($user->birthdate)) : '' }}" class="form-control" max="{{ now()->toDateString() }}" @disabled($user && $user->birthdate) data-profile-locked="{{ $user && $user->birthdate ? 'true' : 'false' }}">
                            @if($user && $user->birthdate)
                                <small class="text-muted"><i class="fas fa-lock"></i> Ngày sinh đã được xác nhận và không thể thay đổi.</small>
                            @else
                                <small class="text-warning">Vui lòng kiểm tra kỹ. Ngày sinh chỉ được xác nhận một lần.</small>
                                <button type="submit" class="confirm-age-button">
                                    <i class="fas fa-check-circle"></i> Xác nhận ngày sinh
                                </button>
                            @endif
                            @error('birthdate')<small class="text-danger">{{ $message }}</small>@enderror
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group full-width">
                            <label>Địa chỉ</label>
                            <div class="address-input-group">
                                <input type="text" id="profileAddress" name="address" value="{{ $user ? $user->address ?? '' : '' }}" placeholder="Nhập địa chỉ hoặc lấy vị trí hiện tại" class="form-control" autocomplete="street-address" disabled>
                                <button type="button" class="location-button" id="locationButton" onclick="fillCurrentAddress()">
                                    <i class="fas fa-location-crosshairs"></i><span>Lấy tự động</span>
                                </button>
                            </div>
                            <small class="profile-field-note" id="locationStatus">Trình duyệt sẽ xin quyền truy cập vị trí khi bạn sử dụng tính năng này.</small>
                        </div>
                    </div>
                    
                    <div class="form-actions {{ !$user->birthdate ? 'is-visible' : '' }}">
                        <button type="submit" class="btn-primary" style="display: {{ !$user->birthdate ? 'inline-flex' : 'none' }}; align-items:center; gap:.5rem;">
                            <i class="fas fa-save"></i> {{ !$user->birthdate ? 'Xác nhận thông tin' : 'Lưu thay đổi' }}
                        </button>
                        <button type="button" class="btn-secondary" onclick="cancelEdit('personal')" style="display: none;">Hủy</button>
                    </div>
                </form>
            </div>
            
            <!-- My Tickets Section -->
            <div id="tickets" class="profile-section" style="display: none;">
                @include('profile.partials.tickets')
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
                
                <form method="POST" action="{{ route('profile.updatePreferences') }}" class="profile-form">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="newsletter" value="1" @checked($user && $user->newsletter)>
                            <span>Đăng ký nhận tin tức</span>
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="notifications" value="1" @checked(!$user || $user->notifications_enabled)>
                            <span>Nhận thông báo</span>
                        </label>
                    </div>
                    
                    <button type="submit" class="btn-primary">Lưu tùy chỉnh</button>
                </form>
            </div>
            
            <!-- Interested upcoming movies -->
            <div id="interests" class="profile-section" style="display: none;">
                <div class="section-header">
                    <div>
                        <h2>Phim sắp chiếu tôi quan tâm</h2>
                        <small class="profile-field-note">Danh sách phim bạn muốn nhận thông tin khi bắt đầu phát hành.</small>
                    </div>
                </div>

                <div class="interest-movie-grid">
                    @forelse($interestedMovies as $movie)
                        <article class="interest-movie-card">
                            <a href="{{ route('movies.introduce', $movie->id) }}" class="interest-poster">
                                @if($movie->thumbnail)
                                    <img src="{{ $movie->thumbnail }}" alt="{{ $movie->title }}" loading="lazy">
                                @else
                                    <span><i class="fas fa-film"></i></span>
                                @endif
                            </a>
                            <div class="interest-movie-info">
                                <h3><a href="{{ route('movies.introduce', $movie->id) }}">{{ $movie->title }}</a></h3>
                                <p><i class="far fa-calendar-alt"></i> {{ $movie->publish_date?->format('H:i d/m/Y') ?? 'Đang cập nhật' }}</p>
                                <form method="POST" action="{{ route('movies.interest.remove', $movie->id) }}" onsubmit="return confirm('Bỏ quan tâm phim này?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="remove-interest-button"><i class="fas fa-bell-slash"></i> Bỏ quan tâm</button>
                                </form>
                            </div>
                        </article>
                    @empty
                        <div class="empty-interest-state">
                            <i class="far fa-bell"></i>
                            <h3>Chưa có phim quan tâm</h3>
                            <p>Đánh dấu phim sắp chiếu để chúng xuất hiện tại đây.</p>
                            <a href="{{ route('movies.upcoming') }}">Khám phá phim sắp chiếu</a>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- Subscription Section -->
            <div id="subscription" class="profile-section" style="display: none;">
                <div class="section-header">
                    <h2>Gói dịch vụ</h2>
                </div>
                
                <div class="subscription-info">
                    @if ($user && $user->subscription)
                        <p><strong>Gói hiện tại:</strong> {{ $user->subscription->name }}</p>
                        @if($user->subscription->accessRank() === 0)
                            <p><strong>Thời hạn:</strong> Vĩnh viễn</p>
                        @elseif($user->subscription_expires_at)
                            <p><strong>Ngày bắt đầu:</strong> {{ $user->subscription_started_at?->format('H:i d/m/Y') ?? 'Chưa xác định' }}</p>
                            <p><strong>Hết hạn:</strong> {{ $user->subscription_expires_at->format('H:i d/m/Y') }}</p>
                            <p><strong>Thời gian còn lại:</strong>
                                <span class="subscription-countdown"
                                      data-expires-at="{{ $user->subscription_expires_at->toIso8601String() }}"
                                      data-server-now="{{ now()->toIso8601String() }}">Đang tính...</span>
                            </p>
                        @endif
                    @else
                        <p>Bạn chưa có gói dịch vụ nào. Hãy chọn một gói để tận hưởng các lợi ích.</p>
                    @endif
                    <p><strong>Số điểm hiện có:</strong> <span class="points-balance">{{ number_format($user->points ?? 0, 0, ',', '.') }} điểm</span></p>
                </div>

                <div class="subscription-plans">
                    @forelse($allSubscriptions as $package)
                        @php
                            $currentPrice = (float) optional($user->subscription)->price;
                            $hasCurrentSubscription = $user->subscription !== null;
                            $isCurrent = $user->subscription_id === $package->id;
                            $isRenewable = $isCurrent && $package->accessRank() > 0;
                            $isPaidUpgrade = optional($user->subscription)->accessRank() > 0
                                && $package->accessRank() > optional($user->subscription)->accessRank();
                            $isDowngrade = $hasCurrentSubscription && $package->price <= $currentPrice && !$isCurrent;
                            $userPoints = $user->points ?? 0;
                            $upgradeCost = $isRenewable ? (float) $package->price : max((float) $package->price - $currentPrice, 0);
                            $remainingDays = $user->subscription_expires_at?->isFuture()
                                ? (int) ceil(now()->diffInDays($user->subscription_expires_at, false)) : 0;
                            $upgradeBillingUnits = $user->subscriptionUpgradeBillingUnits();
                            $keepTimeCost = (int) ceil($upgradeCost * $upgradeBillingUnits);
                            $resetCost = (int) $package->price;
                            $notEnoughPoints = $userPoints < $upgradeCost;
                        @endphp
                        <div class="subscription-plan plan-{{ strtolower($package->access_level ?? 'free') }} {{ $isCurrent ? 'is-current-plan' : '' }}">
                            <h3>{{ $package->name }}</h3>
                            @if(!$isDowngrade && (!$isCurrent || $isRenewable))
                                <p class="plan-upgrade-cost-clean">{{ $isRenewable ? 'Phí gia hạn 1 tháng' : ($isPaidUpgrade ? 'Phí nâng cấp từ' : 'Giá mua') }}: {{ number_format($isPaidUpgrade ? $keepTimeCost : $upgradeCost, 0, ',', '.') }} điểm</p>
                            @endif
                            <p class="plan-price">{{ number_format((float) $package->price, 0, ',', '.') }} xu</p>
                            <p>{{ ($package->duration_months ?? 1) >= 12 ? 'Thanh toán theo năm' : 'Thanh toán theo tháng' }}</p>
                            @if($package->description)
                                <p>{{ $package->description }}</p>
                            @endif
                            @if($package->benefits)
                                <p class="plan-benefits">{{ $package->benefits }}</p>
                            @endif
                            @if($isRenewable)
                                <form method="POST" action="{{ route('profile.upgradeSubscription') }}" onsubmit="return confirmUpgrade(event, {{ $upgradeCost }}, {{ $userPoints }}, '{{ $package->name }}', true)">
                                    @csrf
                                    <input type="hidden" name="subscription_id" value="{{ $package->id }}">
                                    <button type="submit" class="btn-primary" @if($notEnoughPoints) disabled title="Không đủ điểm" @endif>
                                        @if($notEnoughPoints)
                                            <i class="fas fa-lock"></i> Không đủ điểm gia hạn
                                        @else
                                            Gia hạn thêm 1 tháng
                                        @endif
                                    </button>
                                </form>
                            @elseif($isCurrent)
                                <button class="btn-secondary" type="button" disabled>Gói hiện tại</button>
                            @elseif($isDowngrade)
                                <button class="btn-secondary" type="button" disabled>Gói thấp hơn</button>
                            @else
                                <form class="upgrade-choice-form" method="POST" action="{{ route('profile.upgradeSubscription') }}"
                                      onsubmit="return confirmUpgradeChoice(event, {{ $userPoints }}, '{{ $package->name }}')">
                                    @csrf
                                    <input type="hidden" name="subscription_id" value="{{ $package->id }}">
                                    @if($isPaidUpgrade)
                                        <button type="submit" name="purchase_mode" value="keep_time" class="btn-primary"
                                                data-cost="{{ $keepTimeCost }}" @disabled($userPoints < $keepTimeCost)>
                                            Giữ hạn cũ · {{ number_format($keepTimeCost, 0, ',', '.') }} điểm
                                        </button>
                                        <small>Còn {{ $remainingDays }} ngày: tính {{ rtrim(rtrim(number_format($upgradeBillingUnits, 1, ',', '.'), '0'), ',') }} tháng phí chênh lệch.</small>
                                        <button type="submit" name="purchase_mode" value="reset" class="btn-secondary"
                                                data-cost="{{ $resetCost }}" @disabled($userPoints < $resetCost)>
                                            Mua mới 1 tháng · {{ number_format($resetCost, 0, ',', '.') }} điểm
                                        </button>
                                    @else
                                        <button type="submit" name="purchase_mode" value="reset" class="btn-primary"
                                                data-cost="{{ $resetCost }}" @disabled($userPoints < $resetCost)>
                                            Mua gói · {{ number_format($resetCost, 0, ',', '.') }} điểm
                                        </button>
                                    @endif
                                </form>
                            @endif
                        </div>
                    @empty
                        <p style="color: #999;">Chưa có gói dịch vụ nào.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden Avatar Input -->
<input type="file" id="avatarInput" accept="image/*" style="display: none;" onchange="uploadAvatar(this.files[0])">

<div class="profile-confirm-modal" id="profileConfirmModal" aria-hidden="true">
    <div class="profile-confirm-backdrop" data-close-confirm></div>
    <div class="profile-confirm-dialog" role="dialog" aria-modal="true" aria-labelledby="profileConfirmTitle">
        <div class="profile-confirm-icon"><i class="fas fa-crown"></i></div>
        <h3 id="profileConfirmTitle">Xác nhận thanh toán</h3>
        <p id="profileConfirmMessage"></p>
        <div class="profile-confirm-summary">
            <div><span>Chi phí</span><strong id="profileConfirmCost"></strong></div>
            <div><span>Số dư sau thanh toán</span><strong id="profileConfirmBalance"></strong></div>
        </div>
        <div class="profile-confirm-actions">
            <button type="button" class="btn-secondary" data-close-confirm>Hủy</button>
            <button type="button" class="btn-primary" id="profileConfirmSubmit">Xác nhận</button>
        </div>
    </div>
</div>

<style>
    /* Alert Messages */
    .alert {
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        border-radius: 12px;
        border: none;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        position: relative;
        animation: slideIn 0.3s ease-out;
    }
    
    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .alert-success {
        background: rgba(40, 167, 69, 0.15);
        border-left: 4px solid #28a745;
        color: #85ff9f;
    }
    
    .alert-danger {
        background: rgba(220, 53, 69, 0.15);
        border-left: 4px solid #dc3545;
        color: #ff6b6b;
    }
    
    .alert i {
        font-size: 1.25rem;
        margin-top: 0.125rem;
    }
    
    .alert ul {
        list-style: none;
        padding-left: 0;
    }
    
    .alert ul li:before {
        content: "• ";
        margin-right: 0.5rem;
    }
    
    .alert .btn-close {
        background: transparent;
        border: none;
        color: inherit;
        opacity: 0.5;
        cursor: pointer;
        padding: 0;
        width: 24px;
        height: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-left: auto;
        font-size: 1.25rem;
        transition: opacity 0.2s;
    }
    
    .alert .btn-close:hover {
        opacity: 1;
    }
    
    .alert .btn-close:before {
        content: "×";
        font-size: 1.5rem;
        line-height: 1;
    }

    .profile-luxury-container {
        max-width: 1540px;
        margin: 0 auto;
        padding: 6.5rem 1.5rem 3rem;
    }
    
    .profile-luxury-sidebar {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 18px;
        padding: 1.5rem;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        position: sticky;
        top: 5.5rem;
        height: fit-content;
        max-height: calc(100vh - 7rem);
        overflow-y: auto;
    }
    
    .profile-avatar-wrapper {
        position: relative;
        width: 112px;
        height: 112px;
        margin: 0 auto 1.25rem;
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
        margin-bottom: 1.25rem;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding-bottom: 1.25rem;
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
    .interest-nav-count {
        margin-left: auto;
        min-width: 22px;
        padding: 2px 6px;
        border-radius: 999px;
        color: #fff;
        background: #e50914;
        text-align: center;
        font-size: .72rem;
    }
    .profile-nav-interest {
        border: 1px solid rgba(229,9,20,.28);
        background: rgba(229,9,20,.08);
    }
    .profile-header-actions { display:flex; align-items:center; gap:.65rem; }
    .interest-shortcut {
        display:inline-flex; align-items:center; gap:.45rem; border:1px solid rgba(229,9,20,.4);
        border-radius:9px; padding:.5rem .75rem; color:#ff9ca2; background:rgba(229,9,20,.1);
        text-decoration:none; font-size:.82rem; font-weight:650;
    }
    .interest-shortcut b { display:grid; place-items:center; min-width:20px; height:20px; border-radius:999px; color:#fff; background:#e50914; font-size:.7rem; }

    .interest-movie-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 1rem;
    }
    .interest-movie-card {
        display: grid;
        grid-template-columns: 96px minmax(0, 1fr);
        gap: 1rem;
        padding: .9rem;
        border: 1px solid rgba(255,255,255,.09);
        border-radius: 14px;
        background: #202228;
    }
    .interest-poster { display: block; height: 138px; overflow: hidden; border-radius: 10px; background:#292b31; }
    .interest-poster img { width:100%; height:100%; object-fit:cover; }
    .interest-poster span { display:grid; place-items:center; height:100%; color:#777; font-size:1.8rem; }
    .interest-movie-info { min-width:0; display:flex; flex-direction:column; align-items:flex-start; }
    .interest-movie-info h3 { margin:0 0 .55rem; font-size:1rem; line-height:1.35; }
    .interest-movie-info h3 a { color:#fff; text-decoration:none; }
    .interest-movie-info p { margin:0 0 auto; color:#f5b942; font-size:.82rem; }
    .remove-interest-button {
        border:1px solid rgba(229,9,20,.5); border-radius:8px; padding:.45rem .7rem;
        color:#ff8d94; background:rgba(229,9,20,.1); cursor:pointer; font-size:.78rem;
    }
    .empty-interest-state { grid-column:1/-1; padding:3rem 1rem; text-align:center; color:#9ca3af; }
    .empty-interest-state > i { font-size:2.5rem; color:#e50914; margin-bottom:1rem; }
    .empty-interest-state h3 { color:#fff; font-size:1.1rem; }
    .empty-interest-state a { display:inline-block; margin-top:.75rem; color:#fff; background:#e50914; padding:.65rem 1rem; border-radius:9px; text-decoration:none; }
    
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
        background: linear-gradient(145deg, #17191d, #121316);
        border: 1px solid rgba(255,255,255,.08);
        border-radius: 18px;
        padding: 1.75rem;
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
        max-width: none;
        width: 100%;
    }
    
    .form-row {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 1.25rem;
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
        min-height: 48px;
        padding: 0.8rem 1.15rem;
        background: linear-gradient(135deg, rgba(255,255,255,.12), rgba(255,255,255,.045));
        border: 1px solid rgba(255, 255, 255, 0.24);
        border-radius: 999px;
        color: #fff !important;
        font-size: 0.95rem;
        font-weight: 550;
        text-shadow: 0 1px 2px rgba(0,0,0,.65);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.1), 0 8px 24px rgba(0,0,0,.18);
        backdrop-filter: blur(14px) saturate(135%);
        -webkit-backdrop-filter: blur(14px) saturate(135%);
        color-scheme: dark;
        transition: border-color .25s, box-shadow .25s, background .25s;
    }

    .form-control::placeholder { color: rgba(255,255,255,.68); opacity: 1; }
    select.form-control option { background: #202127; color: #fff; }
    
    .form-control:focus {
        outline: none;
        border-color: #e50914;
        background: linear-gradient(135deg, rgba(255,255,255,.16), rgba(229,9,20,.08));
        box-shadow: 0 0 0 3px rgba(229,9,20,.13), inset 0 1px 0 rgba(255,255,255,.12), 0 10px 28px rgba(0,0,0,.24);
    }
    
    .form-control:disabled {
        background: rgba(255,255,255,.055);
        border-color: rgba(255,255,255,.13);
        color: #d5d7de !important;
        opacity: 1;
        cursor: not-allowed;
    }

    .profile-field-note {
        display: block;
        margin-top: .45rem;
        color: #9ca3af;
        font-size: .8rem;
        line-height: 1.4;
    }
    .profile-field-note.locked { color: #f5b942; }
    .confirm-age-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        width: fit-content;
        margin-top: .65rem;
        border: 0;
        border-radius: 9px;
        padding: .65rem 1rem;
        color: #fff;
        background: #e50914;
        font-weight: 700;
        cursor: pointer;
    }

    .form-actions {
        position: sticky;
        bottom: 1rem;
        z-index: 20;
        display: flex;
        gap: .75rem;
        width: fit-content;
        margin-top: .5rem;
        padding: .65rem;
        border: 1px solid rgba(255,255,255,.1);
        border-radius: 12px;
        background: rgba(20,21,25,.94);
        box-shadow: 0 12px 35px rgba(0,0,0,.35);
        backdrop-filter: blur(10px);
    }
    .form-actions:not(.is-visible) { padding: 0; border: 0; box-shadow: none; }

    .address-input-group {
        display: grid;
        grid-template-columns: minmax(0, 1fr) auto;
        gap: .75rem;
        align-items: stretch;
    }
    .location-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .5rem;
        min-width: 145px;
        border: 1px solid rgba(229,9,20,.55);
        border-radius: 999px;
        padding: 0 1rem;
        color: #fff;
        background: rgba(229,9,20,.14);
        font-weight: 650;
        cursor: pointer;
    }
    .location-button:hover { background: rgba(229,9,20,.28); }
    .location-button:disabled { opacity: .6; cursor: wait; }
    
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
    
    .points-balance {
        color: #ffc107;
        font-weight: 700;
        font-size: 1.1rem;
    }

    .subscription-plans {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1rem;
        margin-top: 1.5rem;
    }

    .subscription-plan {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        background: linear-gradient(145deg, #242529, #18191d);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 1.25rem;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.06), 0 12px 30px rgba(0,0,0,.24);
        transition: transform .25s ease, border-color .25s ease, box-shadow .25s ease;
    }

    .subscription-plan:hover {
        transform: translateY(-4px);
        box-shadow: inset 0 1px 0 rgba(255,255,255,.1), 0 18px 38px rgba(0,0,0,.34);
    }

    .subscription-plan::after {
        content: '';
        position: absolute;
        z-index: -1;
        top: -70%;
        left: -35%;
        width: 55%;
        height: 220%;
        opacity: .22;
        transform: rotate(24deg);
        background: linear-gradient(90deg, transparent, rgba(255,255,255,.7), transparent);
        transition: left .55s ease;
        pointer-events: none;
    }
    .subscription-plan:hover::after { left: 115%; }

    .subscription-plan.plan-basic {
        border-color: rgba(96,165,250,.34);
        background: linear-gradient(145deg, rgba(30,58,95,.88), rgba(20,25,36,.96));
    }
    .subscription-plan.plan-basic h3 { color: #93c5fd; }

    .subscription-plan.plan-silver {
        border-color: rgba(226,232,240,.48);
        background: linear-gradient(145deg, rgba(91,101,117,.78), rgba(28,31,38,.97));
        box-shadow: inset 0 1px 0 rgba(255,255,255,.2), 0 12px 30px rgba(0,0,0,.28);
    }
    .subscription-plan.plan-silver h3 { color: #f1f5f9; text-shadow: 0 0 14px rgba(226,232,240,.32); }

    .subscription-plan.plan-gold {
        border-color: rgba(250,204,21,.58);
        background: radial-gradient(circle at 90% 5%, rgba(250,204,21,.2), transparent 38%), linear-gradient(145deg, #493815, #1e1b16 72%);
        box-shadow: inset 0 1px 0 rgba(255,225,115,.22), 0 14px 34px rgba(120,82,0,.28);
    }
    .subscription-plan.plan-gold h3 { color: #fde047; text-shadow: 0 0 16px rgba(250,204,21,.38); }

    .subscription-plan.plan-premium {
        border-color: rgba(192,132,252,.6);
        background: radial-gradient(circle at 92% 4%, rgba(236,72,153,.25), transparent 38%), linear-gradient(145deg, #3b1760, #1b1428 70%);
        box-shadow: inset 0 1px 0 rgba(233,213,255,.2), 0 16px 38px rgba(88,28,135,.34);
    }
    .subscription-plan.plan-premium h3 {
        color: #f0abfc;
        text-shadow: 0 0 18px rgba(217,70,239,.48);
    }

    .subscription-plan.is-current-plan {
        outline: 2px solid rgba(255,255,255,.15);
        outline-offset: -5px;
    }

    .subscription-plan h3 {
        color: #fff;
        font-size: 1.05rem;
        margin: 0 0 0.5rem;
    }

    .subscription-plan p {
        color: #bbb;
        margin: 0 0 1rem;
        font-size: 0.9rem;
    }

    .subscription-plan .plan-price {
        color: #e50914;
        font-weight: 700;
        font-size: 1.2rem;
    }

    .subscription-plan .plan-upgrade-cost-clean {
        color: #f5c542;
        font-weight: 600;
        margin-bottom: 0.75rem;
    }
    
    .subscription-plan .plan-benefits {
        color: #aaa;
        font-size: 0.85rem;
        line-height: 1.5;
    }
    
    .subscription-plan button[disabled] {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .upgrade-choice-form {
        display: grid;
        gap: .55rem;
        margin-top: .75rem;
    }

    .upgrade-choice-form .btn-primary,
    .upgrade-choice-form .btn-secondary {
        width: 100%;
        min-height: 34px;
        padding: .48rem .7rem;
        border-radius: 999px;
        font-size: .76rem;
        font-weight: 700;
        line-height: 1.25;
        align-self: stretch;
        white-space: normal;
        text-align: center;
        box-shadow: inset 0 1px 0 rgba(255,255,255,.16), 0 5px 14px rgba(0,0,0,.2);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
    }

    .upgrade-choice-form .btn-primary {
        background: linear-gradient(135deg, rgba(229,9,20,.88), rgba(172,5,14,.76));
        border: 1px solid rgba(255,255,255,.18);
    }

    .upgrade-choice-form .btn-secondary {
        color: #f4f4f5;
        background: linear-gradient(135deg, rgba(255,255,255,.14), rgba(255,255,255,.07));
        border-color: rgba(255,255,255,.3);
    }

    .upgrade-choice-form small {
        display: block;
        color: #e5e7eb;
        font-size: .72rem;
        line-height: 1.4;
        text-align: center;
    }

    .profile-confirm-modal {
        position: fixed;
        inset: 0;
        z-index: 10000;
        display: grid;
        place-items: center;
        padding: 1rem;
        visibility: hidden;
        opacity: 0;
        transition: opacity .2s ease, visibility .2s ease;
    }
    .profile-confirm-modal.is-open { visibility: visible; opacity: 1; }
    .profile-confirm-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(3,4,7,.72);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
    }
    .profile-confirm-dialog {
        position: relative;
        width: min(440px, 100%);
        padding: 1.6rem;
        border: 1px solid rgba(255,255,255,.22);
        border-radius: 24px;
        color: #fff;
        background: linear-gradient(145deg, rgba(39,40,47,.94), rgba(17,18,22,.9));
        box-shadow: inset 0 1px 0 rgba(255,255,255,.12), 0 28px 70px rgba(0,0,0,.55);
        backdrop-filter: blur(22px) saturate(135%);
        -webkit-backdrop-filter: blur(22px) saturate(135%);
        transform: translateY(10px) scale(.98);
        transition: transform .2s ease;
    }
    .profile-confirm-modal.is-open .profile-confirm-dialog { transform: none; }
    .profile-confirm-icon {
        display: grid;
        place-items: center;
        width: 46px;
        height: 46px;
        margin-bottom: 1rem;
        border-radius: 50%;
        color: #ffd24a;
        background: rgba(255,193,7,.13);
        border: 1px solid rgba(255,210,74,.3);
    }
    .profile-confirm-dialog h3 { margin: 0 0 .65rem; font-size: 1.25rem; }
    .profile-confirm-dialog > p { margin: 0 0 1.15rem; color: #d7d9df; line-height: 1.55; }
    .profile-confirm-summary {
        display: grid;
        gap: .65rem;
        padding: 1rem;
        border-radius: 16px;
        background: rgba(255,255,255,.065);
        border: 1px solid rgba(255,255,255,.1);
    }
    .profile-confirm-summary div { display: flex; justify-content: space-between; gap: 1rem; }
    .profile-confirm-summary span { color: #b9bdc7; }
    .profile-confirm-summary strong { color: #ffd24a; text-align: right; }
    .profile-confirm-actions { display: flex; justify-content: flex-end; gap: .65rem; margin-top: 1.25rem; }
    .profile-confirm-actions .btn-primary,
    .profile-confirm-actions .btn-secondary { border-radius: 999px; padding: .65rem 1.15rem; }
    
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

    /* Unified rounded profile design */
    .profile-luxury-container {
        width: min(1880px, calc(100vw - 32px));
        max-width: none;
        padding: 6.25rem 0 2rem;
    }

    .profile-luxury-container > .row {
        display: grid;
        grid-template-columns: minmax(280px, 340px) minmax(0, 1fr);
        gap: 24px;
        margin: 0;
    }

    .profile-luxury-container > .row > .col-lg-3,
    .profile-luxury-container > .row > .col-lg-9 {
        width: 100%;
        max-width: none;
        padding: 0;
    }

    .profile-luxury-sidebar,
    .profile-section {
        border: 1px solid rgba(255,255,255,.13);
        border-radius: 30px;
        background: linear-gradient(145deg,rgba(39,39,41,.94),rgba(17,18,21,.96));
        box-shadow: inset 0 1px 0 rgba(255,255,255,.08),0 24px 60px rgba(0,0,0,.32);
        backdrop-filter: blur(22px) saturate(120%);
        -webkit-backdrop-filter: blur(22px) saturate(120%);
    }

    .profile-luxury-sidebar { padding: 28px; }
    .profile-section { min-height: 540px; padding: 34px 36px; }
    .profile-avatar-wrapper { width: 132px; height: 132px; }
    .profile-avatar-container { border-width: 5px; border-color: rgba(255,255,255,.22); }

    .profile-nav-menu { gap: 10px; }
    .profile-nav-item,
    .profile-logout-btn,
    .interest-shortcut,
    .location-button,
    .confirm-age-button,
    .btn-edit,
    .profile-luxury-container .btn-primary,
    .profile-luxury-container .btn-secondary,
    .remove-interest-button,
    .empty-interest-state a {
        border-radius: 999px !important;
    }

    .profile-nav-item {
        min-height: 54px;
        padding: 0 20px;
        border: 1px solid transparent;
        background: transparent;
    }

    .profile-nav-item:hover {
        color: #fff;
        border-color: rgba(255,255,255,.16);
        background: rgba(255,255,255,.08);
        transform: translateX(4px);
    }

    .profile-nav-item.active {
        color: #fff;
        border-color: rgba(255,255,255,.2);
        background: linear-gradient(135deg,rgba(255,255,255,.17),rgba(255,255,255,.07));
        box-shadow: inset 0 1px 0 rgba(255,255,255,.12),0 10px 24px rgba(0,0,0,.2);
    }

    .profile-nav-interest { border-color: transparent; background: transparent; }
    .profile-nav-item i { width: 20px; text-align: center; }
    .interest-nav-count,
    .interest-shortcut b,
    .profile-ticket-status { border-radius: 999px !important; }

    .profile-form { gap: 24px; }
    .form-row { gap: 24px; }
    .form-control {
        min-height: 58px;
        padding: 0 22px;
        border-radius: 999px !important;
        background: linear-gradient(135deg,rgba(255,255,255,.12),rgba(255,255,255,.055));
    }
    textarea.form-control { min-height: 130px; padding-top: 18px; border-radius: 30px !important; }
    .form-control:focus {
        border-color: rgba(255,255,255,.52);
        background: linear-gradient(135deg,rgba(255,255,255,.18),rgba(255,255,255,.08));
        box-shadow: 0 0 0 4px rgba(255,255,255,.08),inset 0 1px 0 rgba(255,255,255,.14),0 12px 30px rgba(0,0,0,.25);
    }

    .location-button { min-width: 170px; padding-inline: 24px; }
    .btn-edit { width: 52px; height: 52px; padding: 0; display:grid; place-items:center; border:1px solid rgba(255,255,255,.24); background:linear-gradient(135deg,rgba(255,255,255,.16),rgba(255,255,255,.07)); }
    .interest-shortcut { min-height:48px; padding-inline:18px; }
    .profile-logout-btn { min-height: 52px; border:1px solid rgba(255,255,255,.14); background:linear-gradient(135deg,rgba(255,255,255,.13),rgba(255,255,255,.055)); }
    .profile-logout-btn:hover { background:rgba(255,255,255,.14); }

    .form-actions { border-radius:999px; }
    .profile-luxury-container .btn-primary,
    .profile-luxury-container .btn-secondary { min-height:46px; padding:0 22px; display:inline-flex; align-items:center; justify-content:center; }
    .checkbox-label { min-height:54px; padding:0 18px; border:1px solid rgba(255,255,255,.12); border-radius:999px; background:rgba(255,255,255,.05); }
    .checkbox-label input[type="checkbox"] { width:22px; height:22px; border-radius:50%; accent-color:#fff; }

    .interest-movie-card,
    .subscription-plan,
    .subscription-info,
    .balance-card,
    .deposit-form,
    .profile-ticket-card,
    .profile-ticket-empty,
    .profile-ticket-foods {
        border-radius: 28px !important;
    }
    .interest-poster,
    .profile-ticket-qr img { border-radius:22px !important; }
    .alert { border-radius:999px; }

    @media (max-width: 768px) {
        .profile-luxury-container {
            padding: 5.5rem 1rem 2rem;
        }
        
        .profile-luxury-container { width:calc(100vw - 20px); padding-inline:0; }
        .profile-luxury-container > .row { display:flex; flex-direction:column-reverse; gap:14px; }
        
        .col-lg-3,
        .col-lg-9 {
            max-width: 100%;
        }
        
        .profile-luxury-sidebar {
            position: static;
            margin-bottom: 2rem;
        }
        
        .profile-section {
            min-height:0;
            padding: 1.25rem;
            border-radius:24px;
        }

        .profile-section .form-row {
            grid-template-columns: 1fr;
        }

        .address-input-group { grid-template-columns: 1fr; }
        .location-button { min-height: 46px; }
        .profile-luxury-sidebar { padding:20px; border-radius:24px; }
    }
</style>

<script>
    // Auto-dismiss alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            setTimeout(() => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        });
        
        // Close button functionality
        const closeButtons = document.querySelectorAll('.alert .btn-close');
        closeButtons.forEach(btn => {
            btn.addEventListener('click', function() {
                const alert = this.closest('.alert');
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(() => alert.remove(), 300);
            });
        });
    });

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
        const activeNav = event
            ? event.target.closest('.profile-nav-item')
            : document.querySelector(`.profile-nav-item[href="#${tabId}"]`);
        if (activeNav) activeNav.classList.add('active');
    }

    const requestedProfileTab = window.location.hash.substring(1);
    if (requestedProfileTab && document.getElementById(requestedProfileTab)) {
        switchProfileTab(requestedProfileTab);
    }
    
    function toggleEditMode(section) {
        const form = document.getElementById(section + 'Form');
        const inputs = form.querySelectorAll('input:not([type="hidden"]), textarea, select');
        const isDisabled = inputs[0].disabled;
        
        inputs.forEach(input => {
            if (input.dataset.profileLocked !== 'true') {
                input.disabled = !isDisabled;
            }
        });
        
        // Toggle button visibility
        const buttons = form.querySelectorAll('.form-actions button');
        buttons.forEach(btn => {
            btn.style.display = isDisabled ? 'inline-flex' : 'none';
        });
        form.querySelector('.form-actions')?.classList.toggle('is-visible', isDisabled);
    }
    
    function cancelEdit(section) {
        toggleEditMode(section);
        // Reload page or reset form
        location.reload();
    }

    function enablePersonalField(fieldId) {
        const field = document.getElementById(fieldId);
        if (!field) return;
        if (field.disabled) toggleEditMode('personal');
        field.disabled = false;
        field.focus();
        if (typeof field.select === 'function') field.select();
    }

    async function fillCurrentAddress() {
        const addressInput = document.getElementById('profileAddress');
        const button = document.getElementById('locationButton');
        const status = document.getElementById('locationStatus');

        if (!navigator.geolocation) {
            status.textContent = 'Trình duyệt này không hỗ trợ định vị. Bạn vẫn có thể nhập địa chỉ thủ công.';
            status.classList.add('text-danger');
            return;
        }

        button.disabled = true;
        status.textContent = 'Đang xác định vị trí của bạn...';

        navigator.geolocation.getCurrentPosition(async position => {
            const latitude = position.coords.latitude;
            const longitude = position.coords.longitude;
            let address = `${latitude.toFixed(6)}, ${longitude.toFixed(6)}`;

            try {
                const url = new URL('https://api.bigdatacloud.net/data/reverse-geocode-client');
                url.searchParams.set('latitude', latitude);
                url.searchParams.set('longitude', longitude);
                url.searchParams.set('localityLanguage', 'vi');
                const response = await fetch(url);
                if (!response.ok) throw new Error('Reverse geocoding failed');
                const location = await response.json();
                address = [...new Set([
                    location.locality,
                    location.city,
                    location.principalSubdivision,
                    location.countryName,
                ].filter(Boolean))].join(', ') || address;
                status.textContent = 'Đã lấy địa chỉ gần đúng. Bạn có thể chỉnh lại số nhà hoặc tên đường trước khi lưu.';
            } catch (error) {
                status.textContent = 'Đã lấy tọa độ nhưng chưa đổi được thành địa chỉ. Bạn có thể chỉnh lại trước khi lưu.';
            }

            if (addressInput.disabled) toggleEditMode('personal');
            addressInput.value = address;
            addressInput.focus();
            button.disabled = false;
        }, error => {
            const messages = {
                1: 'Bạn đã từ chối quyền vị trí. Hãy cho phép vị trí hoặc nhập địa chỉ thủ công.',
                2: 'Không xác định được vị trí hiện tại. Vui lòng thử lại.',
                3: 'Quá thời gian lấy vị trí. Vui lòng thử lại.',
            };
            status.textContent = messages[error.code] || 'Không thể lấy vị trí hiện tại.';
            status.classList.add('text-danger');
            button.disabled = false;
        }, { enableHighAccuracy: true, timeout: 12000, maximumAge: 300000 });
    }

    function confirmUpgrade(event, upgradeCost, userPoints, packageName, isRenewal = false) {
        event.preventDefault();
        const packagePrice = upgradeCost;
        const action = isRenewal ? 'gia hạn thêm 1 tháng' : 'nâng cấp';
        if (userPoints < packagePrice) {
            openProfileConfirm(event.currentTarget, {
                title: 'Không đủ điểm',
                message: `Bạn chưa đủ điểm để ${action} gói "${packageName}".`,
                cost: packagePrice,
                balance: userPoints - packagePrice,
                canSubmit: false,
            });
            return false;
        }

        openProfileConfirm(event.currentTarget, {
            message: `Xác nhận ${action} gói "${packageName}"?`,
            cost: packagePrice,
            balance: userPoints - packagePrice,
        });
        return false;
    }

    function confirmUpgradeChoice(event, userPoints, packageName) {
        event.preventDefault();
        const button = event.submitter;
        const cost = Number(button?.dataset.cost || 0);
        const keepTime = button?.value === 'keep_time';
        const action = keepTime
            ? 'nâng cấp và giữ nguyên ngày hết hạn hiện tại'
            : 'hủy thời hạn gói cũ và mua mới 1 tháng';

        if (userPoints < cost) {
            openProfileConfirm(event.currentTarget, {
                title: 'Không đủ điểm',
                message: `Bạn chưa đủ điểm để ${action} lên gói "${packageName}".`,
                cost,
                balance: userPoints - cost,
                canSubmit: false,
            });
            return false;
        }

        openProfileConfirm(event.currentTarget, {
            message: `Xác nhận ${action} lên gói "${packageName}"?`,
            cost,
            balance: userPoints - cost,
            purchaseMode: button?.value,
        });
        return false;
    }

    const profileConfirmModal = document.getElementById('profileConfirmModal');
    const profileConfirmSubmit = document.getElementById('profileConfirmSubmit');
    let pendingProfileForm = null;

    function openProfileConfirm(form, options) {
        pendingProfileForm = options.canSubmit === false ? null : form;
        document.getElementById('profileConfirmTitle').textContent = options.title || 'Xác nhận thanh toán';
        document.getElementById('profileConfirmMessage').textContent = options.message;
        document.getElementById('profileConfirmCost').textContent = `${Number(options.cost).toLocaleString('vi-VN')} điểm`;
        document.getElementById('profileConfirmBalance').textContent = options.balance >= 0
            ? `${Number(options.balance).toLocaleString('vi-VN')} điểm`
            : `Thiếu ${Math.abs(options.balance).toLocaleString('vi-VN')} điểm`;
        profileConfirmSubmit.hidden = options.canSubmit === false;

        const oldMode = form.querySelector('input[data-confirm-purchase-mode]');
        oldMode?.remove();
        if (options.purchaseMode) {
            const mode = document.createElement('input');
            mode.type = 'hidden';
            mode.name = 'purchase_mode';
            mode.value = options.purchaseMode;
            mode.dataset.confirmPurchaseMode = 'true';
            form.appendChild(mode);
        }

        profileConfirmModal.classList.add('is-open');
        profileConfirmModal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        if (options.canSubmit !== false) profileConfirmSubmit.focus();
    }

    function closeProfileConfirm() {
        profileConfirmModal.classList.remove('is-open');
        profileConfirmModal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        pendingProfileForm = null;
    }

    document.querySelectorAll('[data-close-confirm]').forEach(button => button.addEventListener('click', closeProfileConfirm));
    profileConfirmSubmit.addEventListener('click', () => {
        if (!pendingProfileForm) return;
        const form = pendingProfileForm;
        closeProfileConfirm();
        HTMLFormElement.prototype.submit.call(form);
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && profileConfirmModal.classList.contains('is-open')) closeProfileConfirm();
    });

    document.querySelectorAll('.subscription-countdown').forEach(element => {
        const expiresAt = new Date(element.dataset.expiresAt).getTime();
        const serverNow = new Date(element.dataset.serverNow).getTime();
        const clockOffset = serverNow - Date.now();

        const renderCountdown = () => {
            const remaining = Math.max(0, expiresAt - (Date.now() + clockOffset));
            if (remaining <= 0) {
                element.textContent = 'Đã hết hạn';
                return;
            }

            const totalSeconds = Math.floor(remaining / 1000);
            const days = Math.floor(totalSeconds / 86400);
            const hours = Math.floor((totalSeconds % 86400) / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;
            element.textContent = `${days} ngày ${hours} giờ ${minutes} phút ${seconds} giây`;
        };

        renderCountdown();
        window.setInterval(renderCountdown, 1000);
    });
    
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
