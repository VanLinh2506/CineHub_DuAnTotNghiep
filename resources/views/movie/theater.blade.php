@extends('layouts.app')

@section('title', 'Vé xem phim - CineHub')

@section('content')
<div class="container" style="margin-top: 80px; padding: 20px;">
    <div class="page-header" style="margin-bottom: 30px;">
        <h1 style="font-size: 32px; font-weight: bold; color: #fff; margin-bottom: 10px;">
            <i class="fas fa-ticket-alt"></i> Phim đang chiếu
        </h1>
        <p style="color: #aaa; font-size: 16px;">Chọn phim để đặt vé ngay hôm nay</p>
    </div>

    @if($movies && count($movies) > 0)
        <div class="movie-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 25px; margin-top: 20px;">
            @foreach($movies as $movie)
            <div class="movie-card" style="background: #1a1a1a; border-radius: 12px; overflow: hidden; transition: transform 0.3s; cursor: pointer;">
                <a href="{{ route('booking.index', ['movie' => $movie['id']]) }}" style="text-decoration: none; color: inherit;">
                    <div class="movie-thumbnail" style="position: relative; width: 100%; padding-bottom: 150%; overflow: hidden;">
                        @if($movie['thumbnail'])
                            <img src="{{ $movie['thumbnail'] }}" 
                                 alt="{{ $movie['title'] }}" 
                                 style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;">
                        @else
                            <div style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: #2a2a2a; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-film" style="font-size: 48px; color: #555;"></i>
                            </div>
                        @endif
                        
                        <div class="movie-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.7); opacity: 0; transition: opacity 0.3s; display: flex; align-items: center; justify-content: center;">
                            <div style="text-align: center;">
                                <i class="fas fa-ticket-alt" style="font-size: 48px; color: #fff; margin-bottom: 10px;"></i>
                                <p style="color: #fff; font-weight: bold; font-size: 14px;">Đặt vé ngay</p>
                            </div>
                        </div>

                        <span class="movie-badge" style="position: absolute; top: 10px; right: 10px; background: #e50914; color: #fff; padding: 5px 10px; border-radius: 5px; font-size: 11px; font-weight: bold;">
                            ĐANG CHIẾU
                        </span>

                        @if($movie['level'] && $movie['level'] !== 'Free')
                            <span class="movie-level" style="position: absolute; top: 10px; left: 10px; background: {{ $movie['level'] === 'Gold' ? '#FFD700' : ($movie['level'] === 'Premium' ? '#9370DB' : '#C0C0C0') }}; color: #000; padding: 5px 10px; border-radius: 5px; font-size: 11px; font-weight: bold;">
                                {{ $movie['level'] }}
                            </span>
                        @endif
                    </div>

                    <div class="movie-info" style="padding: 15px;">
                        <h3 style="font-size: 16px; font-weight: bold; color: #fff; margin: 0 0 8px 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                            {{ $movie['title'] }}
                        </h3>
                        
                        <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                            @if(isset($movie['rating']) && $movie['rating'] > 0)
                                <div style="display: flex; align-items: center; gap: 5px;">
                                    <i class="fas fa-star" style="color: #f5c518; font-size: 14px;"></i>
                                    <span style="color: #fff; font-size: 14px;">{{ number_format($movie['rating'], 1) }}</span>
                                </div>
                            @endif
                            
                            @if(isset($movie['duration']) && $movie['duration'])
                                <div style="display: flex; align-items: center; gap: 5px;">
                                    <i class="fas fa-clock" style="color: #aaa; font-size: 12px;"></i>
                                    <span style="color: #aaa; font-size: 13px;">{{ $movie['duration'] }} phút</span>
                                </div>
                            @endif
                        </div>

                        @if(isset($movie['category_name']))
                            <div style="margin-bottom: 10px;">
                                <span style="background: #333; color: #aaa; padding: 4px 8px; border-radius: 4px; font-size: 11px;">
                                    {{ $movie['category_name'] }}
                                </span>
                            </div>
                        @endif

                        <button class="btn-book" style="width: 100%; background: #e50914; color: #fff; border: none; padding: 10px; border-radius: 100px; font-weight: bold; cursor: pointer; transition: background 0.3s;">
                            <i class="fas fa-ticket-alt"></i> Đặt vé ngay
                        </button>
                    </div>
                </a>
            </div>
            @endforeach
        </div>
    @else
        <div style="text-align: center; padding: 60px 20px; background: #1a1a1a; border-radius: 12px; margin-top: 30px;">
            <i class="fas fa-film" style="font-size: 64px; color: #555; margin-bottom: 20px;"></i>
            <h3 style="color: #fff; margin-bottom: 10px;">Chưa có phim nào đang chiếu</h3>
            <p style="color: #aaa;">Vui lòng quay lại sau để xem lịch chiếu mới nhất</p>
        </div>
    @endif
</div>

<style>
.movie-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(229, 9, 20, 0.3);
}

.movie-card:hover .movie-overlay {
    opacity: 1;
}

.btn-book:hover {
    background: #f40612 !important;
}
</style>
@endsection
