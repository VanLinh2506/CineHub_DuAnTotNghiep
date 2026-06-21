@extends('layouts.app')

@section('content')
<div class="profile-container" style="padding: 40px 20px; max-width: 1200px; margin: 0 auto;">
    <div class="profile-header" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px; border-radius: 15px; margin-bottom: 30px; color: white;">
        <div style="display: flex; align-items: center; gap: 30px;">
            <div class="avatar" style="width: 100px; height: 100px; border-radius: 50%; background: white; display: flex; align-items: center; justify-content: center; font-size: 40px; color: #667eea;">
                @if($user->avatar)
                    <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
                @else
                    <i class="fas fa-user"></i>
                @endif
            </div>
            <div style="flex: 1;">
                <h1 style="font-size: 32px; margin-bottom: 10px;">{{ $user->name }}</h1>
                <p style="font-size: 16px; opacity: 0.9; margin-bottom: 5px;">
                    <i class="fas fa-envelope"></i> {{ $user->email }}
                </p>
                <p style="font-size: 16px; opacity: 0.9;">
                    <i class="fas fa-shield-alt"></i> {{ $userRole }}
                </p>
            </div>
            <div style="text-align: center;">
                <div style="font-size: 36px; font-weight: bold;">{{ number_format($balance) }}</div>
                <div style="font-size: 14px; opacity: 0.9;">Điểm tích lũy</div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="background: #d4edda; color: #155724; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger" style="background: #f8d7da; color: #721c24; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #f5c6cb;">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
    @endif

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
        <!-- Subscription Card -->
        <div class="card" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h3 style="font-size: 20px; margin-bottom: 20px; color: #333;">
                <i class="fas fa-crown" style="color: #ffd700;"></i> Gói thành viên
            </h3>
            @if($subscription)
                <div style="font-size: 24px; font-weight: bold; color: #667eea; margin-bottom: 10px;">
                    {{ $subscription->name }}
                </div>
                <p style="color: #666; font-size: 14px;">{{ $subscription->description }}</p>
            @else
                <p style="color: #999;">Chưa có gói thành viên</p>
            @endif
        </div>

        <!-- Watch History -->
        <div class="card" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h3 style="font-size: 20px; margin-bottom: 20px; color: #333;">
                <i class="fas fa-history"></i> Lịch sử xem
            </h3>
            <div style="font-size: 32px; font-weight: bold; color: #667eea; margin-bottom: 10px;">
                {{ $history->count() }}
            </div>
            <p style="color: #666; font-size: 14px;">Phim đã xem gần đây</p>
        </div>

        <!-- Tickets -->
        <div class="card" style="background: white; padding: 30px; border-radius: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <h3 style="font-size: 20px; margin-bottom: 20px; color: #333;">
                <i class="fas fa-ticket-alt"></i> Vé đã đặt
            </h3>
            <div style="font-size: 32px; font-weight: bold; color: #667eea; margin-bottom: 10px;">
                {{ $tickets->count() }}
            </div>
            <p style="color: #666; font-size: 14px;">Tổng số vé</p>
        </div>
    </div>

    <!-- Quick Actions -->
    <div style="margin-top: 40px;">
        <h3 style="font-size: 24px; margin-bottom: 20px; color: #333;">Thao tác nhanh</h3>
        <div style="display: flex; gap: 15px; flex-wrap: wrap;">
            <a href="{{ route('booking.history') }}" class="btn" style="display: inline-block; padding: 12px 24px; background: #667eea; color: white; text-decoration: none; border-radius: 8px; font-weight: 500;">
                <i class="fas fa-history"></i> Lịch sử đặt vé
            </a>
            <a href="{{ route('movies.index') }}" class="btn" style="display: inline-block; padding: 12px 24px; background: #764ba2; color: white; text-decoration: none; border-radius: 8px; font-weight: 500;">
                <i class="fas fa-film"></i> Xem phim
            </a>
            @if($isAdmin)
                <a href="{{ route('admin.index') }}" class="btn" style="display: inline-block; padding: 12px 24px; background: #f093fb; color: white; text-decoration: none; border-radius: 8px; font-weight: 500;">
                    <i class="fas fa-cog"></i> Admin Panel
                </a>
            @endif
            @if($isModerator)
                <a href="{{ route('moderator.index') }}" class="btn" style="display: inline-block; padding: 12px 24px; background: #4facfe; color: white; text-decoration: none; border-radius: 8px; font-weight: 500;">
                    <i class="fas fa-building"></i> Quản lý rạp
                </a>
            @endif
        </div>
    </div>

    <!-- Recent History -->
    @if($history->count() > 0)
        <div style="margin-top: 40px;">
            <h3 style="font-size: 24px; margin-bottom: 20px; color: #333;">Phim xem gần đây</h3>
            <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 20px;">
                @foreach($history->take(6) as $item)
                    <div class="movie-card" style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                        @if($item->movie)
                            <a href="{{ route('movies.introduce', $item->movie->id) }}" style="text-decoration: none; color: inherit;">
                                @if($item->movie->poster_path)
                                    <img src="{{ Storage::url($item->movie->poster_path) }}" alt="{{ $item->movie->title }}" style="width: 100%; height: 225px; object-fit: cover;">
                                @else
                                    <div style="width: 100%; height: 225px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 40px;">
                                        <i class="fas fa-film"></i>
                                    </div>
                                @endif
                                <div style="padding: 10px;">
                                    <h4 style="font-size: 14px; margin-bottom: 5px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                        {{ $item->movie->title }}
                                    </h4>
                                    <p style="font-size: 12px; color: #999;">
                                        {{ $item->created_at->diffForHumans() }}
                                    </p>
                                </div>
                            </a>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>

<style>
.btn:hover {
    opacity: 0.9;
    transform: translateY(-2px);
    transition: all 0.3s;
}

.movie-card:hover {
    transform: translateY(-5px);
    transition: all 0.3s;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
</style>
@endsection
