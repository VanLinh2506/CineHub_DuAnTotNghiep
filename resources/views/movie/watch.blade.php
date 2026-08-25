@extends('layouts.app')

@php
$current_page = 'movie';
// Convert collections/models to arrays recursively for backward compatibility
$movie = is_object($movie) ? json_decode(json_encode($movie), true) : $movie;
$episodes = isset($movie['episodes']) ? $movie['episodes'] : [];
$currentEpisode = isset($currentEpisode) ? json_decode(json_encode($currentEpisode), true) : null;
$reviews = isset($reviews) ? json_decode(json_encode($reviews), true) : [];
$comments = isset($comments) ? json_decode(json_encode($comments), true) : [];
$relatedMovies = isset($relatedMovies) ? json_decode(json_encode($relatedMovies), true) : [];
$viewer = auth()->user();
$viewerAvatar = $viewer?->avatar_url;

$title = htmlspecialchars($movie['title'] ?? 'Movie');
$baseUrl = url('/');
@endphp

@section('content')
@php
// Sắp xếp episodes từ tập nhỏ đến tập lớn
if (!empty($episodes)) {
    usort($episodes, function($a, $b) {
        return ($a['episode_number'] ?? 0) - ($b['episode_number'] ?? 0);
    });
}
@endphp
<section class="watch-section">
    <div class="container">
        <div class="watch-container">
            <!-- Header với nút quay lại và tên phim -->
            <div class="watch-header">
                <a href="javascript:history.back()" class="back-button">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="watch-movie-title">{{ $movie['title'] }}</h1>
            </div>

            <div class="video-wrapper">
                <?php
                // Xác định video URL để hiển thị
                $videoUrl = null;
                $noVideoMessage = null;
                $episodeNumber = null;
                $isPhimBo = ($movie['type'] ?? 'phimle') === 'phimbo';

                if ($isPhimBo) {
                    // Xử lý phim bộ
                    $folderPath = null;

                    // Debug: Kiểm tra episodes từ database
                    error_log("Watch view - Episodes count from DB: " . (isset($episodes) ? count($episodes) : 0));
                    error_log("Watch view - Current episode: " . (isset($currentEpisode) && $currentEpisode ? "Yes (ID: " . $currentEpisode['id'] . ", Number: " . $currentEpisode['episode_number'] . ")" : "No"));
                    error_log("Watch view - Movie video_url: " . ($movie['video_url'] ?? 'N/A'));

                    if (isset($currentEpisode) && $currentEpisode) {
                        // Có tập được chọn
                        $episodeNumber = $currentEpisode['episode_number'];

                        if (!empty($currentEpisode['video_url'])) {
                            // Sử dụng trực tiếp video_url từ episode
                            $videoUrl = $currentEpisode['video_url'];
                        } else {
                            // Tập được chọn nhưng chưa có video_url
                            $noVideoMessage = "Tập " . $currentEpisode['episode_number'] . " chưa có video. Vui lòng chọn tập khác hoặc đợi admin upload video.";
                        }
                    } elseif (!empty($episodes)) {
                        // Chưa chọn tập, tìm tập đầu tiên có video_url
                        $found = false;
                        foreach ($episodes as $ep) {
                            if (!empty($ep['video_url'])) {
                                $episodeNumber = $ep['episode_number'];
                                $videoUrl = $ep['video_url'];
                                $found = true;
                                break;
                            }
                        }

                        // Nếu không tìm thấy tập có video_url
                        if (!$found) {
                            $episodeNumber = $episodes[0]['episode_number'] ?? 1;
                            $noVideoMessage = "Chưa có tập nào có video. Vui lòng đợi admin upload video.";
                        }
                    } else {
                        // Không có episodes trong database
                        $noVideoMessage = "Chưa có tập nào. Vui lòng đợi admin thêm tập.";
                    }
                } else {
                    // Phim lẻ
                    $videoUrl = $movie['video_url'] ?? null;
                    if (!$videoUrl) {
                        $noVideoMessage = "Video chưa có sẵn.";
                    }
                }

                if ($videoUrl) {
                    // Sử dụng storage_url() helper để xử lý đúng đường dẫn
                    // Helper sẽ tự động thêm /storage/ prefix cho files trong storage
                    if (strpos($videoUrl, 'http') === 0) {
                        // Đã là URL đầy đủ
                        $finalVideoSrc = $videoUrl;
                    } else {
                        // Sử dụng storage_url() để xử lý
                        // Với video_url = "data/phim/phimbo/phamnhantutien/tap_1.mp4"
                        // storage_url() sẽ trả về: http://127.0.0.1:8000/storage/data/phim/phimbo/phamnhantutien/tap_1.mp4
                        $fullVideoUrl = storage_url($videoUrl);
                        $finalVideoSrc = $fullVideoUrl;
                    }
                }

                $qualitySources = [];
                $lockedQualities = [];
                if ($isPhimBo && !empty($currentEpisode['video_sources'])) {
                    foreach ($currentEpisode['video_sources'] as $quality => $source) {
                        if (!empty($source)) {
                            $qualitySources[$quality] = route('movies.video', [
                                'kind' => 'episode', 'sourceId' => $currentEpisode['id'], 'quality' => $quality,
                            ]);
                            $lockedQualities[$quality] = (int) $quality > ($maxVideoQuality ?? 480);
                        }
                    }
                    uksort($qualitySources, fn ($a, $b) => (int) $a <=> (int) $b);
                } elseif (!$isPhimBo && !empty($movie['video_sources'])) {
                    foreach ($movie['video_sources'] as $quality => $source) {
                        if (!empty($source)) {
                            $qualitySources[$quality] = route('movies.video', [
                                'kind' => 'movie', 'sourceId' => $movie['id'], 'quality' => $quality,
                            ]);
                            $lockedQualities[$quality] = (int) $quality > ($maxVideoQuality ?? 480);
                        }
                    }
                    uksort($qualitySources, fn ($a, $b) => (int) $a <=> (int) $b);
                }

                if (!empty($qualitySources)) {
                    $hasAllowedQuality = false;
                    foreach (array_reverse($qualitySources, true) as $quality => $source) {
                        if (empty($lockedQualities[$quality])) {
                            $finalVideoSrc = $source;
                            $hasAllowedQuality = true;
                            break;
                        }
                    }
                    if (!$hasAllowedQuality) {
                        $videoUrl = null;
                        $noVideoMessage = 'Gói hiện tại chưa hỗ trợ các chất lượng đã có của video này.';
                    }
                }

                $fullTrailerUrl = null;
                if (($movie['type'] ?? 'phimle') !== 'phimbo' && !empty($movie['trailer_url'])) {
                    $fullTrailerUrl = $movie['trailer_url'];
                    if (strpos($movie['trailer_url'], 'http') !== 0) {
                        $fullTrailerUrl = $baseUrl . '/' . ltrim($movie['trailer_url'], '/');
                    }
                }
                ?>
                @if($videoUrl)
                    <video id="videoPlayer" preload="metadata" playsinline>
                        <source src="{{ $finalVideoSrc }}" type="video/mp4">
                        Trình duyệt của bạn không hỗ trợ video.
                    </video>
                @elseif($noVideoMessage)
                    <div class="video-placeholder">
                        <i class="fas fa-exclamation-triangle" style="font-size: 3rem; color: #ff9800;"></i>
                        <p style="margin-top: 1rem; font-size: 1.1rem; color: var(--text-primary);">{{ $noVideoMessage }}</p>
                        @if(($movie['type'] ?? 'phimle') === 'phimbo' && !empty($episodes))
                            <p style="margin-top: 0.5rem; color: var(--text-secondary); font-size: 0.9rem;">
                                <i class="fas fa-info-circle"></i> Vui lòng chọn tập khác từ danh sách bên dưới.
                            </p>
                        @endif
                    </div>
                @elseif($fullTrailerUrl)
                    <video id="videoPlayer" preload="metadata" playsinline>
                        <source src="/storage/{{ $fullTrailerUrl }}" type="video/mp4">
                        Trình duyệt của bạn không hỗ trợ video.
                    </video>
                @else
                    <div class="video-placeholder">
                        <i class="fas fa-video"></i>
                        <p>Video chưa có sẵn</p>
                    </div>
                @endif

                @if($videoUrl || $fullTrailerUrl)
                <div id="videoLoadingSpinner" class="video-loading-spinner" hidden><i class="fas fa-spinner fa-spin"></i></div>
                <div class="cinehub-video-controls" aria-label="Điều khiển video">
                    <input id="videoSeek" class="video-seek" type="range" min="0" max="100" value="0" step="0.1" aria-label="Tua video">
                    <div class="video-controls-row">
                        <div class="video-controls-group">
                            <button type="button" id="videoPlayPause" class="video-control-button" title="Phát"><i class="fas fa-play"></i></button>
                            <button type="button" id="videoMute" class="video-control-button" title="Tắt tiếng"><i class="fas fa-volume-up"></i></button>
                            <input id="videoVolume" class="video-volume" type="range" min="0" max="1" value="1" step="0.05" aria-label="Âm lượng">
                            <span class="video-time"><span id="videoCurrentTime">0:00</span> / <span id="videoDuration">0:00</span></span>
                        </div>
                        <div class="video-quality-control" aria-label="Cài đặt video">
                    <div id="videoQualityMenu" class="video-quality-menu">
                        <div class="video-quality-menu-title"><i class="fas fa-sliders-h"></i> Cài đặt</div>
                        <button type="button" class="video-settings-section-toggle" data-panel="videoSpeedPanel" aria-expanded="false">
                            <span><i class="fas fa-tachometer-alt"></i> Tốc độ phát</span>
                            <span><strong id="currentSpeedLabel">Chuẩn</strong> <i class="fas fa-chevron-right"></i></span>
                        </button>
                        <div id="videoSpeedPanel" class="video-settings-panel">
                        <div class="video-speed-options">
                            @foreach([0.5, 0.75, 1, 1.25, 1.5, 1.75, 2] as $speed)
                                <button type="button" class="video-speed-option {{ $speed == 1 ? 'active' : '' }}" data-speed="{{ $speed }}">
                                    {{ $speed == 1 ? 'Chuẩn' : $speed . 'x' }}
                                </button>
                            @endforeach
                        </div>
                        </div>
                        @if(!empty($qualitySources))
                        <button type="button" class="video-settings-section-toggle" data-panel="videoQualityPanel" aria-expanded="false">
                            <span><i class="fas fa-sliders-h"></i> Chất lượng</span>
                            <span><strong id="currentQualityLabel">{{ isset($finalVideoSrc) ? (array_search($finalVideoSrc, $qualitySources, true) ?: 'Tự động') : 'Tự động' }}</strong> <i class="fas fa-chevron-right"></i></span>
                        </button>
                        <div id="videoQualityPanel" class="video-settings-panel">
                        @foreach($qualitySources as $quality => $source)
                            @php
                                $isLockedQuality = !empty($lockedQualities[$quality]);
                                $requiredPlan = (int) $quality <= 720 ? 'Silver' : ((int) $quality <= 1080 ? 'Gold' : 'Premium');
                            @endphp
                            <button type="button" class="video-quality-option {{ $source === ($finalVideoSrc ?? null) ? 'active' : '' }}"
                                data-source="{{ $source }}" data-label="{{ $quality === '2160p' ? '4K' : $quality }}"
                                {{ $isLockedQuality ? 'disabled' : '' }}>
                                <span><i class="fas {{ $source === ($finalVideoSrc ?? null) ? 'fa-check' : 'fa-circle' }}"></i> {{ $quality === '2160p' ? '4K (2160p)' : $quality }}</span>
                                @if($isLockedQuality)<small><i class="fas fa-lock"></i> {{ $requiredPlan }}</small>@endif
                            </button>
                        @endforeach
                        </div>
                        @endif
                    </div>
                    <button type="button" id="videoQualitySettings" class="video-settings-button"
                        aria-expanded="false" aria-controls="videoQualityMenu" title="Cài đặt video">
                        <i class="fas fa-cog"></i>
                    </button>
                    <button type="button" id="cinehubFullscreen" class="video-settings-button"
                        title="Toàn màn hình" aria-label="Toàn màn hình">
                        <i class="fas fa-expand"></i>
                    </button>
                </div>
                    </div>
                </div>
                @endif
            </div>

            @php
            // Luôn hiển thị phần episodes nếu phim có type là 'phimbo'
            $isPhimBo = ($movie['type'] ?? 'phimle') === 'phimbo';

            // Debug: Kiểm tra episodes
            error_log("Watch page - Movie ID: " . ($movie['id'] ?? 'N/A') . ", Type: " . ($movie['type'] ?? 'N/A') . ", Is Phim Bo: " . ($isPhimBo ? 'Yes' : 'No'));
            error_log("Watch page - Episodes count: " . (isset($episodes) ? count($episodes) : 0));
            if (isset($episodes) && !empty($episodes)) {
                error_log("Watch page - First episode: " . print_r($episodes[0], true));
            }
            @endphp

            @if($isPhimBo)
            <div class="episodes-section">
                <h3><i class="fas fa-list"></i> Danh sách tập
                    @if(isset($episodes) && !empty($episodes))
                        <span class="badge bg-primary ms-2">{{ count($episodes) }} tập</span>
                    @else
                        <span class="badge bg-warning ms-2">Chưa có tập</span>
                    @endif
                </h3>

                @if(isset($episodes) && !empty($episodes))
                    <div class="episodes-list">
                        @foreach($episodes as $episode)
                            @php
                                $isWatchedEpisode = in_array((int) $episode['id'], $watchedEpisodeIds ?? [], true);
                            @endphp
                            <a href="{{ route('movies.watch', ['id' => $movie['id'], 'episode_id' => $episode['id']]) }}"
                               class="episode-item {{ $isWatchedEpisode ? 'watched' : '' }} {{ (isset($currentEpisode) && $currentEpisode && $currentEpisode['id'] == $episode['id']) ? 'active' : '' }} {{ empty($episode['video_url']) ? 'episode-no-video' : '' }}"
                               title="{{ $episode['title'] ?? 'Tập ' . $episode['episode_number'] }}">
                                <div class="episode-number">{{ $episode['episode_number'] }}</div>
                            </a>
                        @endforeach
                    </div>
                @else
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Chưa có tập nào được thêm vào phim này.</strong>
                        <p class="mb-0 mt-2">Để hiển thị danh sách tập, vui lòng thêm các tập cho phim bộ này trong phần quản trị.</p>
                        @if(isset($isAdmin) && $isAdmin)
                            <a href="{{ route('admin.movies.edit', $movie['id']) }}" class="btn btn-primary btn-sm mt-2">
                                <i class="fas fa-plus"></i> Thêm tập ngay
                            </a>
                        @endif
                    </div>
                @endif
            </div>
            @endif

            <div class="movie-details">
                <div class="movie-meta-info">
                    @if($movie['rating'] !== null)
                    <span><i class="fas fa-star"></i> {{ number_format($movie['rating'], 1) }}</span>
                    @endif
                    @if(($movie['type'] ?? 'phimle') === 'phimbo')
                        <span><i class="fas fa-tv"></i> Phim bộ</span>
                    @else
                        <span><i class="fas fa-clock"></i> {{ $movie['duration'] }} phút</span>
                    @endif
                    <span><i class="fas fa-tag"></i> {{ $movie['category_name'] ?? 'Chưa phân loại' }}</span>
                    <span class="movie-type-badge-inline">{{ ($movie['type'] ?? 'phimle') === 'phimbo' ? 'Phim bộ' : 'Phim lẻ' }}</span>
                    <span><i class="fas fa-layer-group"></i> {{ $movie['level'] }}</span>
                </div>

                @if(isset($movie['status']) && $movie['status'] === 'Chiếu rạp')
                    <div class="mt-3 mb-3">
                        <a href="{{ route('booking.index', ['movie' => $movie['id']]) }}"
                           class="btn btn-primary btn-lg"
                           style="background: #e50914; border: none; padding: 12px 24px; border-radius: 8px; text-decoration: none; display: inline-block; color: white; font-weight: 600; font-size: 1.1rem;">
                            <i class="fas fa-ticket-alt"></i> Đặt vé xem phim
                        </a>
                    </div>
                @endif

                @if($movie['description'])
                    <div class="movie-description-full">
                        <h3>Nội dung</h3>
                        <p>{{ nl2br(htmlspecialchars($movie['description'])) }}</p>
                    </div>
                @endif

                @if($movie['director'] || $movie['actors'])
                    <div class="movie-cast">
                        @if($movie['director'])
                            <p><strong>Đạo diễn:</strong> {{ $movie['director'] }}</p>
                        @endif
                        @if($movie['actors'])
                            <p><strong>Diễn viên:</strong> {{ $movie['actors'] }}</p>
                        @endif
                    </div>
                @endif
            </div>

            <!-- PHẦN ĐÁNH GIÁ (có sao, mỗi user 1 lần) -->
            <div class="reviews-section" id="reviews">
                <h2><i class="fas fa-star"></i> Đánh giá phim</h2>

                @if($viewer)
                    @if(isset($userHasRated) && $userHasRated)
                        <div class="user-rating-info" style="background: rgba(212, 175, 55, 0.1); border: 1px solid rgba(212, 175, 55, 0.3); border-radius: 10px; padding: 15px; margin-bottom: 20px;">
                            <p style="margin: 0; color: #d4af37;">
                                <i class="fas fa-check-circle"></i> Bạn đã đánh giá phim này:
                                <strong>{{ $userRating }}/10</strong>
                            </p>
                        </div>
                    @else
                        <form method="POST" action="{{ route('reviews.store') }}" class="review-form" id="reviewForm">
                            @csrf
                            <input type="hidden" name="movie_id" value="{{ $movie['id'] }}">
                            <div class="rating-input">
                                <label>Đánh giá của bạn:</label>
                                <div class="star-rating" id="starRating">
                                    <input type="hidden" name="rating" id="ratingValue" value="" required>
                                    @for($s = 1; $s <= 10; $s++)
                                        <span class="star" data-value="{{ $s }}"><i class="far fa-star"></i></span>
                                    @endfor
                                    <span class="rating-text" id="ratingText">Chọn số sao</span>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary" id="submitReview">Gửi đánh giá</button>
                        </form>
                    @endif
                @else
                    <p style="color: var(--text-secondary);">Vui lòng <a href="{{ route('login') }}" style="color: #e50914;">đăng nhập</a> để đánh giá phim.</p>
                @endif

                <!-- Danh sách đánh giá -->
                <div class="reviews-list" style="margin-top: 20px;">
                    @if(empty($reviews))
                        <p class="no-reviews">Chưa có đánh giá nào.</p>
                    @else
                        @foreach($reviews as $review)
                            <div class="review-item" style="display: flex; gap: 15px; padding: 15px; background: #1f1f1f; border-radius: 10px; margin-bottom: 10px;">
                                <div class="review-avatar" style="flex-shrink: 0;">
                                    @if(!empty($review['user']['avatar_url']))
                                        <img src="{{ $review['user']['avatar_url'] }}" alt="Avatar" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover;">
                                    @else
                                        <div style="width: 45px; height: 45px; border-radius: 50%; background: #333; display: flex; align-items: center; justify-content: center;">
                                            <i class="fas fa-user" style="color: #666;"></i>
                                        </div>
                                    @endif
                                </div>
                                <div style="flex: 1;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px;">
                                        <div>
                                            <strong style="color: #fff;">{{ $review['user_name'] ?? ($review['user']['name'] ?? 'Anonymous') }}</strong>
                                            <span style="margin-left: 10px; color: #ffc107;" class="review-stars">
                                                @for($i = 1; $i <= 10; $i++)
                                                    @if(($review['rating'] ?? 0) >= $i)
                                                        <i class="fas fa-star"></i>
                                                    @else
                                                        <i class="far fa-star"></i>
                                                    @endif
                                                @endfor
                                                <small style="color: #aaa; margin-left: 5px;">{{ $review['rating'] ?? 0 }}/10</small>
                                            </span>
                                        </div>
                                        <span style="color: #888; font-size: 0.85rem;">{{ isset($review['created_at']) ? date('d/m/Y', strtotime($review['created_at'])) : '' }}</span>
                                    </div>
                                    @if(isset($review['comment']) && $review['comment'])
                                        <p style="margin: 0; color: #ccc;">{{ nl2br(htmlspecialchars($review['comment'])) }}</p>
                                    @endif
                                    @if($canModerateComments ?? false)
                                        <div style="margin-top: 10px;">
                                            <form method="POST" action="{{ route('reviews.destroy', $review['id']) }}" onsubmit="return confirm('Ẩn đánh giá này?')" style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" style="background: none; border: none; color: #888; cursor: pointer; display: inline-flex; align-items: center; gap: 5px; padding: 4px 10px; border-radius: 20px; font-size: 0.85rem; transition: all 0.3s;" onmouseover="this.style.background='rgba(244,67,54,0.2)';this.style.color='#f44336'" onmouseout="this.style.background='none';this.style.color='#888'">
                                                    <i class="fas fa-eye-slash"></i> Ẩn
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- PHẦN BÌNH LUẬN (không có sao, có reply, like/dislike) -->
            <div class="comments-section" id="comments" style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                <h2><i class="fas fa-comments"></i> Bình luận <span class="badge bg-secondary ms-2">{{ count($comments ?? []) }}</span></h2>

                @if($viewer)
                    <form method="POST" action="{{ route('comments.store') }}" class="comment-form" id="commentForm" style="margin-bottom: 25px;">
                        @csrf
                        <input type="hidden" name="movie_id" value="{{ $movie['id'] }}">
                        <div style="display: flex; gap: 15px; align-items: flex-start;">
                            <div style="flex-shrink: 0;">
                                @if($viewerAvatar)
                                    <img src="{{ $viewerAvatar }}" alt="Avatar" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #444;">
                                @else
                                    <div style="width: 45px; height: 45px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-user" style="color: #fff;"></i>
                                    </div>
                                @endif
                            </div>
                            <div style="flex: 1;">
                                <textarea name="content" placeholder="Viết bình luận của bạn..." rows="3" required style="width: 100%; padding: 12px 15px; border-radius: 12px; background: #2a2a2a; border: 1px solid #444; color: #fff; resize: none; font-size: 0.95rem; transition: border-color 0.3s;" onfocus="this.style.borderColor='#e50914'" onblur="this.style.borderColor='#444'"></textarea>
                                <button type="submit" class="btn btn-primary" style="margin-top: 10px; background: #e50914; border: none; padding: 10px 20px; border-radius: 8px;">
                                    <i class="fas fa-paper-plane"></i> Gửi bình luận
                                </button>
                            </div>
                        </div>
                    </form>
                @else
                    <div style="background: rgba(229, 9, 20, 0.1); border: 1px solid rgba(229, 9, 20, 0.3); border-radius: 10px; padding: 15px; margin-bottom: 20px; text-align: center;">
                        <p style="color: #ccc; margin: 0;">
                            <i class="fas fa-lock"></i> Vui lòng <a href="{{ route('login') }}" style="color: #e50914; font-weight: 600;">đăng nhập</a> để bình luận.
                        </p>
                    </div>
                @endif

                <!-- Danh sách bình luận -->
                <div class="comments-list" id="commentsList">
                    @if(empty($comments ?? []))
                        <div style="text-align: center; padding: 40px 20px; background: #1a1a1a; border-radius: 12px;">
                            <i class="fas fa-comments" style="font-size: 3rem; color: #444; margin-bottom: 15px;"></i>
                            <p style="color: #888; margin: 0;">Chưa có bình luận nào. Hãy là người đầu tiên bình luận!</p>
                        </div>
                    @else
                        @foreach($comments as $comment)
                            <div class="comment-item" id="comment-{{ $comment['id'] }}" style="margin-bottom: 20px;">
                                <div style="display: flex; gap: 15px;">
                                    <div style="flex-shrink: 0;">
                                        @if(!empty($comment['user']['avatar_url']))
                                            <img src="{{ $comment['user']['avatar_url'] }}" alt="Avatar" style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #333;">
                                        @else
                                            <div style="width: 45px; height: 45px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-user" style="color: #fff;"></i>
                                            </div>
                                        @endif
                                    </div>
                                    <div style="flex: 1; background: #1f1f1f; padding: 15px 18px; border-radius: 12px;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 10px;">
                                            <strong style="color: #fff; font-size: 0.95rem;">{{ $comment['user_name'] ?? ($comment['user']['name'] ?? 'Anonymous') }}</strong>
                                            <span style="color: #666; font-size: 0.8rem;"><i class="far fa-clock"></i> {{ isset($comment['created_at']) ? date('d/m/Y H:i', strtotime($comment['created_at'])) : '' }}</span>
                                        </div>
                                        <p style="margin: 0 0 12px 0; color: #ddd; line-height: 1.6;">{{ nl2br(htmlspecialchars($comment['content'] ?? '')) }}</p>

                                        <!-- Like/Dislike và Reply buttons -->
                                        <div class="comment-actions" style="display: flex; gap: 20px; align-items: center; padding-top: 10px; border-top: 1px solid #333;">
                                            <button class="like-btn" onclick="likeComment({{ $comment['id'] ?? 0 }}, 'like')" style="background: none; border: none; color: #888; cursor: pointer; display: flex; align-items: center; gap: 6px; padding: 5px 10px; border-radius: 20px; transition: all 0.3s;" onmouseover="this.style.background='rgba(76, 175, 80, 0.2)'; this.style.color='#4caf50'" onmouseout="this.style.background='none'; this.style.color='#888'">
                                                <i class="far fa-thumbs-up"></i> <span id="likes-{{ $comment['id'] ?? 0 }}">{{ $comment['likes'] ?? 0 }}</span>
                                            </button>
                                            <button class="dislike-btn" onclick="likeComment({{ $comment['id'] ?? 0 }}, 'dislike')" style="background: none; border: none; color: #888; cursor: pointer; display: flex; align-items: center; gap: 6px; padding: 5px 10px; border-radius: 20px; transition: all 0.3s;" onmouseover="this.style.background='rgba(244, 67, 54, 0.2)'; this.style.color='#f44336'" onmouseout="this.style.background='none'; this.style.color='#888'">
                                                <i class="far fa-thumbs-down"></i> <span id="dislikes-{{ $comment['id'] ?? 0 }}">{{ $comment['dislikes'] ?? 0 }}</span>
                                            </button>
                                            @if($viewer)
                                                <button onclick="toggleReplyForm({{ $comment['id'] ?? 0 }})" style="background: none; border: none; color: #888; cursor: pointer; display: flex; align-items: center; gap: 6px; padding: 5px 10px; border-radius: 20px; transition: all 0.3s;" onmouseover="this.style.background='rgba(33, 150, 243, 0.2)'; this.style.color='#2196f3'" onmouseout="this.style.background='none'; this.style.color='#888'">
                                                    <i class="fas fa-reply"></i> Trả lời
                                                </button>
                                            @endif
                                            @if($canModerateComments ?? false)
                                                <form method="POST" action="{{ route('comments.destroy', $comment['id']) }}" onsubmit="return confirm('Bạn có chắc muốn ẩn bình luận này?')" style="margin: 0;">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" style="background: none; border: none; color: #888; cursor: pointer; display: flex; align-items: center; gap: 6px; padding: 5px 10px; border-radius: 20px; transition: all 0.3s;" onmouseover="this.style.background='rgba(244, 67, 54, 0.2)'; this.style.color='#f44336'" onmouseout="this.style.background='none'; this.style.color='#888'">
                                                        <i class="fas fa-eye-slash"></i> Ẩn
                                                    </button>
                                                </form>
                                            @endif
                                        </div>

                                        <!-- Form trả lời (ẩn mặc định) -->
                                        @if($viewer)
                                        <div id="reply-form-{{ $comment['id'] }}" style="display: none; margin-top: 15px; padding-top: 15px; border-top: 1px solid #333;">
                                            <form method="POST" action="{{ route('comments.store') }}">
                                                @csrf
                                                <input type="hidden" name="movie_id" value="{{ $movie['id'] }}">
                                                <input type="hidden" name="parent_id" value="{{ $comment['id'] }}">
                                                <div style="display: flex; gap: 10px; align-items: flex-start;">
                                                    <div style="flex-shrink: 0;">
                                                        @if($viewerAvatar)
                                                            <img src="{{ $viewerAvatar }}" alt="Avatar" style="width: 32px; height: 32px; border-radius: 50%; object-fit: cover;">
                                                        @else
                                                            <div style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                                                                <i class="fas fa-user" style="color: #fff; font-size: 0.7rem;"></i>
                                                            </div>
                                                        @endif
                                                    </div>
                                                    <div style="flex: 1;">
                                                        <textarea name="content" placeholder="Viết trả lời..." rows="2" required style="width: 100%; padding: 10px 12px; border-radius: 8px; background: #2a2a2a; border: 1px solid #444; color: #fff; resize: none; font-size: 0.9rem;"></textarea>
                                                        <div style="margin-top: 8px;">
                                                            <button type="submit" class="btn btn-sm" style="background: #e50914; border: none; color: #fff; padding: 6px 15px; border-radius: 6px;">Gửi</button>
                                                            <button type="button" onclick="toggleReplyForm({{ $comment['id'] }})" class="btn btn-sm" style="background: #444; border: none; color: #fff; padding: 6px 15px; border-radius: 6px; margin-left: 5px;">Hủy</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                        @endif

                                        <!-- Replies -->
                                        @if(!empty($comment['replies']))
                                        <div class="replies" style="margin-top: 15px; padding-left: 15px; border-left: 3px solid #e50914;">
                                            @foreach($comment['replies'] as $reply)
                                            <div style="display: flex; gap: 12px; margin-bottom: 15px; padding: 12px; background: #252525; border-radius: 10px;">
                                                <div style="flex-shrink: 0;">
                                                    @if(!empty($reply['user']['avatar_url']))
                                                        <img src="{{ $reply['user']['avatar_url'] }}" alt="Avatar" style="width: 35px; height: 35px; border-radius: 50%; object-fit: cover; border: 2px solid #333;">
                                                    @else
                                                        <div style="width: 35px; height: 35px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                                                            <i class="fas fa-user" style="color: #fff; font-size: 0.7rem;"></i>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div style="flex: 1;">
                                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                                        <strong style="color: #fff; font-size: 0.9rem;">{{ $reply['user_name'] ?? ($reply['user']['name'] ?? 'Anonymous') }}</strong>
                                                        <span style="color: #666; font-size: 0.75rem;"><i class="far fa-clock"></i> {{ isset($reply['created_at']) ? date('d/m/Y H:i', strtotime($reply['created_at'])) : '' }}</span>
                                                    </div>
                                                    <p style="margin: 0 0 8px 0; color: #ccc; font-size: 0.9rem; line-height: 1.5;">{{ nl2br(htmlspecialchars($reply['content'] ?? '')) }}</p>

                                                    <!-- Like/Dislike cho reply -->
                                                    <div style="display: flex; gap: 15px; align-items: center;">
                                                        <button class="like-btn" onclick="likeComment({{ $reply['id'] ?? 0 }}, 'like')" style="background: none; border: none; color: #666; cursor: pointer; display: flex; align-items: center; gap: 5px; font-size: 0.85rem; padding: 3px 8px; border-radius: 15px; transition: all 0.3s;" onmouseover="this.style.background='rgba(76, 175, 80, 0.2)'; this.style.color='#4caf50'" onmouseout="this.style.background='none'; this.style.color='#666'">
                                                            <i class="far fa-thumbs-up"></i> <span id="likes-{{ $reply['id'] ?? 0 }}">{{ $reply['likes'] ?? 0 }}</span>
                                                        </button>
                                                        <button class="dislike-btn" onclick="likeComment({{ $reply['id'] ?? 0 }}, 'dislike')" style="background: none; border: none; color: #666; cursor: pointer; display: flex; align-items: center; gap: 5px; font-size: 0.85rem; padding: 3px 8px; border-radius: 15px; transition: all 0.3s;" onmouseover="this.style.background='rgba(244, 67, 54, 0.2)'; this.style.color='#f44336'" onmouseout="this.style.background='none'; this.style.color='#666'">
                                                            <i class="far fa-thumbs-down"></i> <span id="dislikes-{{ $reply['id'] ?? 0 }}">{{ $reply['dislikes'] ?? 0 }}</span>
                                                        </button>
                                                        @if($canModerateComments ?? false)
                                                            <form method="POST" action="{{ route('comments.destroy', $reply['id']) }}" onsubmit="return confirm('Bạn có chắc muốn ẩn trả lời này?')" style="margin: 0;">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" style="background: none; border: none; color: #666; cursor: pointer; display: flex; align-items: center; gap: 5px; font-size: 0.85rem; padding: 3px 8px; border-radius: 15px; transition: all 0.3s;" onmouseover="this.style.background='rgba(244, 67, 54, 0.2)'; this.style.color='#f44336'" onmouseout="this.style.background='none'; this.style.color='#666'">
                                                                    <i class="fas fa-eye-slash"></i> Ẩn
                                                                </button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Phim cùng thể loại -->
            @if(!empty($relatedMovies))
            <div class="related-movies-section" style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                <h2 style="margin-bottom: 1.5rem;"><i class="fas fa-film"></i> Phim cùng thể loại</h2>
                <div class="related-movies-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(150px, 1fr)); gap: 1rem;">
                    @foreach($relatedMovies as $related)
                    <a href="{{ route('movies.introduce', $related['id']) }}" class="related-movie-card" style="text-decoration: none; color: inherit;">
                        <div style="position: relative; border-radius: 8px; overflow: hidden; background: #1f1f1f;">
                            @if($related['thumbnail'])
                                <img src="{{ $related['thumbnail'] }}" alt="{{ $related['title'] }}" style="width: 100%; aspect-ratio: 2/3; object-fit: cover;">
                            @else
                                <div style="width: 100%; aspect-ratio: 2/3; background: #333; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-film" style="font-size: 2rem; color: #666;"></i>
                                </div>
                            @endif
                            <div style="position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(transparent, rgba(0,0,0,0.9)); padding: 1rem 0.5rem 0.5rem;">
                                <p style="margin: 0; font-size: 0.85rem; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; color: #fff;">{{ $related['title'] }}</p>
                                @if($related['rating'] !== null)
                                <p style="margin: 0; font-size: 0.75rem; color: #ffc107;"><i class="fas fa-star"></i> {{ number_format($related['rating'], 1) }}</p>
                                @endif
                            </div>
                        </div>
                    </a>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</section>

<style>
.video-wrapper { position: relative; }
.cinehub-video-controls {
    position: absolute; left: 0; right: 0; bottom: 0; z-index: 8;
    padding: 34px 14px 10px;
    background: linear-gradient(transparent, rgba(0,0,0,.88)); color: #fff;
    transition: opacity .2s ease;
}
.video-controls-row, .video-controls-group, .video-quality-control {
    display: flex; align-items: center;
}
.video-controls-row { justify-content: space-between; gap: 12px; }
.video-controls-group, .video-quality-control { gap: 8px; }
.video-control-button, .video-settings-button {
    width: 38px; height: 38px; border: 0; border-radius: 50%;
    background: transparent; color: #fff; font-size: 17px;
    display: grid; place-items: center; cursor: pointer;
}
.video-control-button:hover, .video-settings-button:hover,
.video-settings-button[aria-expanded="true"] { background: rgba(255,255,255,.18); }
.video-seek, .video-volume { accent-color: #2f6fed; cursor: pointer; }
.video-seek { width: 100%; height: 4px; margin: 0 0 7px; display: block; }
.video-volume { width: 78px; }
.video-time { font-size: 13px; white-space: nowrap; }
.video-loading-spinner {
    position: absolute; inset: 0; z-index: 7; display: grid; place-items: center;
    color: #fff; font-size: 42px; background: rgba(0,0,0,.12); pointer-events: none;
}
.video-loading-spinner[hidden] { display: none; }
.video-wrapper:fullscreen,
.video-wrapper:-webkit-full-screen {
    width: 100vw; height: 100vh; padding-top: 0; margin: 0; border-radius: 0; background: #000;
}
.video-wrapper:fullscreen video,
.video-wrapper:-webkit-full-screen video {
    width: 100%; height: 100%; object-fit: contain;
}
.video-quality-menu {
    position: absolute; right: 0; bottom: 52px; width: 250px;
    padding: 8px; border: 1px solid rgba(255,255,255,.14); border-radius: 12px;
    background: rgba(18,18,22,.68); color: #fff;
    box-shadow: 0 16px 42px rgba(0,0,0,.52);
    backdrop-filter: blur(22px) saturate(145%); -webkit-backdrop-filter: blur(22px) saturate(145%);
    opacity: 0; visibility: hidden; pointer-events: none;
    transform: translateY(10px) scale(.97); transform-origin: right bottom;
    transition: opacity .2s ease, transform .24s cubic-bezier(.2,.8,.2,1), visibility .2s;
}
.video-quality-menu.is-open { opacity: 1; visibility: visible; pointer-events: auto; transform: translateY(0) scale(1); }
.video-quality-menu-title { padding: 8px 10px 10px; font-size: 14px; font-weight: 700; border-bottom: 1px solid rgba(255,255,255,.12); }
.video-settings-section-toggle {
    width: 100%; padding: 12px 10px; border: 0; border-radius: 9px; background: transparent;
    color: #fff; display: flex; align-items: center; justify-content: space-between;
    gap: 12px; cursor: pointer; text-align: left;
}
.video-settings-section-toggle:hover, .video-settings-section-toggle[aria-expanded="true"] { background: rgba(255,255,255,.1); }
.video-settings-section-toggle > span { display: flex; align-items: center; gap: 9px; }
.video-settings-section-toggle strong { color: #c8ccd5; font-size: 12px; font-weight: 500; }
.video-settings-section-toggle .fa-chevron-right { font-size: 11px; transition: transform .22s ease; }
.video-settings-section-toggle[aria-expanded="true"] .fa-chevron-right { transform: rotate(90deg); }
.video-settings-panel { max-height: 0; opacity: 0; overflow: hidden; transform: translateY(-5px); transition: max-height .3s ease, opacity .2s ease, transform .25s ease; }
.video-settings-panel.is-open { max-height: 440px; opacity: 1; transform: translateY(0); }
.video-speed-options { display: grid; grid-template-columns: repeat(4, 1fr); gap: 5px; padding: 0 8px 4px; }
.video-speed-option {
    padding: 7px 3px; border: 0; border-radius: 7px; background: rgba(255,255,255,.07);
    color: #fff; font-size: 12px; cursor: pointer;
}
.video-speed-option:hover { background: rgba(255,255,255,.16); }
.video-speed-option.active { background: #2f6fed; color: #fff; }
.video-quality-option {
    width: 100%; padding: 9px 10px; border: 0; border-radius: 8px;
    background: transparent; color: #fff; display: flex; align-items: center;
    justify-content: space-between; gap: 10px; text-align: left; cursor: pointer;
}
.video-quality-option:hover { background: rgba(255,255,255,.1); }
.video-quality-option.active { background: rgba(47,111,237,.28); color: #dce8ff; }
.video-quality-option .fa-circle { font-size: 6px; opacity: .35; }
.video-quality-option small { color: #aeb4bf; }
.video-quality-option:disabled { opacity: .38; cursor: not-allowed; }
.video-quality-option:disabled:hover { background: transparent; }
.video-wrapper.controls-hidden { cursor: none; }
.video-wrapper.controls-hidden .cinehub-video-controls { opacity: 0; pointer-events: none; }
@media (max-width: 600px) {
    .video-volume { width: 55px; }
    .video-time { display: none; }
    .cinehub-video-controls { padding-left: 8px; padding-right: 8px; }
}

/* Star Rating Styles */
.star-rating {
    display: flex;
    align-items: center;
    gap: 3px;
    margin-top: 10px;
    flex-wrap: wrap;
}
.star-rating .star {
    font-size: 1.4rem;
    cursor: pointer;
    color: #444;
    transition: all 0.2s;
}
.star-rating .star:hover,
.star-rating .star.hover {
    color: #ffc107;
    transform: scale(1.1);
}
.star-rating .star.active {
    color: #ffc107;
}
.star-rating .star.active i {
    font-weight: 900;
}
/* Stars hiển thị trong review list */
.review-stars i {
    font-size: 0.75rem;
}
.rating-text {
    margin-left: 15px;
    color: var(--text-secondary);
    font-size: 0.9rem;
}
.related-movie-card:hover {
    transform: translateY(-5px);
    transition: transform 0.3s;
}
</style>

<script>
// Restore the last playback position and persist it every 10 seconds.
document.addEventListener('DOMContentLoaded', function () {
    const player = document.getElementById('videoPlayer');
    if (!player) return;

    const resumeSeconds = {{ (int) ($resumeSeconds ?? 0) }};
    const progressUrl = @json(route('movies.progress', $movie['id']));
    const episodeId = @json($currentEpisode['id'] ?? null);
    const settingsButton = document.getElementById('videoQualitySettings');
    const qualityMenu = document.getElementById('videoQualityMenu');
    const qualityOptions = document.querySelectorAll('.video-quality-option:not(:disabled)');
    const speedOptions = document.querySelectorAll('.video-speed-option');
    const fullscreenButton = document.getElementById('cinehubFullscreen');
    const videoWrapper = player.closest('.video-wrapper');
    const playButton = document.getElementById('videoPlayPause');
    const muteButton = document.getElementById('videoMute');
    const volumeSlider = document.getElementById('videoVolume');
    const seekSlider = document.getElementById('videoSeek');
    const currentTimeLabel = document.getElementById('videoCurrentTime');
    const durationLabel = document.getElementById('videoDuration');
    const loadingSpinner = document.getElementById('videoLoadingSpinner');
    let lastSavedSecond = -1;
    let controlsHideTimer = null;

    const formatVideoTime = function (seconds) {
        if (!Number.isFinite(seconds)) return '0:00';
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const secs = Math.floor(seconds % 60).toString().padStart(2, '0');
        return hours ? `${hours}:${minutes.toString().padStart(2, '0')}:${secs}` : `${minutes}:${secs}`;
    };
    const updatePlayButton = function () {
        if (!playButton) return;
        playButton.querySelector('i').className = player.paused ? 'fas fa-play' : 'fas fa-pause';
        playButton.title = player.paused ? 'Phát' : 'Tạm dừng';
    };
    const updateVolumeButton = function () {
        if (!muteButton) return;
        const icon = player.muted || player.volume === 0 ? 'fa-volume-mute' : (player.volume < .5 ? 'fa-volume-down' : 'fa-volume-up');
        muteButton.querySelector('i').className = `fas ${icon}`;
    };

    if (playButton) playButton.addEventListener('click', () => player.paused ? player.play() : player.pause());
    player.addEventListener('click', () => player.paused ? player.play() : player.pause());
    player.addEventListener('play', updatePlayButton);
    player.addEventListener('pause', updatePlayButton);
    player.addEventListener('timeupdate', function () {
        if (seekSlider && Number.isFinite(player.duration) && player.duration > 0) seekSlider.value = (player.currentTime / player.duration) * 100;
        if (currentTimeLabel) currentTimeLabel.textContent = formatVideoTime(player.currentTime);
    });
    player.addEventListener('durationchange', function () {
        if (durationLabel) durationLabel.textContent = formatVideoTime(player.duration);
    });
    if (seekSlider) seekSlider.addEventListener('input', function () {
        if (Number.isFinite(player.duration)) player.currentTime = (Number(this.value) / 100) * player.duration;
    });
    if (muteButton) muteButton.addEventListener('click', function () {
        player.muted = !player.muted;
        updateVolumeButton();
    });
    if (volumeSlider) volumeSlider.addEventListener('input', function () {
        player.volume = Number(this.value);
        player.muted = player.volume === 0;
        updateVolumeButton();
    });
    player.addEventListener('volumechange', function () {
        if (volumeSlider) volumeSlider.value = player.muted ? 0 : player.volume;
        updateVolumeButton();
    });
    const showVideoControls = function () {
        if (!videoWrapper) return;
        videoWrapper.classList.remove('controls-hidden');
        clearTimeout(controlsHideTimer);
        if (!player.paused) controlsHideTimer = setTimeout(function () {
            if (!qualityMenu || !qualityMenu.classList.contains('is-open')) videoWrapper.classList.add('controls-hidden');
        }, 2500);
    };
    if (videoWrapper) {
        videoWrapper.setAttribute('tabindex', '0');
        videoWrapper.addEventListener('mousemove', showVideoControls);
        videoWrapper.addEventListener('mouseleave', function () {
            if (!player.paused && (!qualityMenu || !qualityMenu.classList.contains('is-open'))) videoWrapper.classList.add('controls-hidden');
        });
        videoWrapper.addEventListener('keydown', function (event) {
            if (['INPUT', 'BUTTON'].includes(document.activeElement.tagName)) return;
            if (event.code === 'Space' || event.key.toLowerCase() === 'k') {
                event.preventDefault(); player.paused ? player.play() : player.pause();
            } else if (event.key === 'ArrowRight') {
                player.currentTime = Math.min(player.duration || Infinity, player.currentTime + 5);
            } else if (event.key === 'ArrowLeft') {
                player.currentTime = Math.max(0, player.currentTime - 5);
            } else if (event.key.toLowerCase() === 'm') {
                player.muted = !player.muted;
            } else if (event.key.toLowerCase() === 'f' && fullscreenButton) {
                fullscreenButton.click();
            }
        });
    }
    player.addEventListener('play', showVideoControls);
    player.addEventListener('pause', showVideoControls);
    ['waiting', 'seeking', 'loadstart'].forEach(eventName => player.addEventListener(eventName, () => {
        if (loadingSpinner) loadingSpinner.hidden = false;
    }));
    ['playing', 'canplay', 'seeked', 'loadeddata'].forEach(eventName => player.addEventListener(eventName, () => {
        if (loadingSpinner) loadingSpinner.hidden = true;
    }));

    if (fullscreenButton && videoWrapper) {
        const currentFullscreenElement = function () {
            return document.fullscreenElement || document.webkitFullscreenElement;
        };

        fullscreenButton.addEventListener('click', async function () {
            try {
                if (currentFullscreenElement() === videoWrapper) {
                    const exitFullscreen = document.exitFullscreen || document.webkitExitFullscreen;
                    if (exitFullscreen) await exitFullscreen.call(document);
                } else {
                    const requestFullscreen = videoWrapper.requestFullscreen || videoWrapper.webkitRequestFullscreen;
                    if (requestFullscreen) await requestFullscreen.call(videoWrapper);
                }
            } catch (error) {
                // Trình duyệt sẽ giữ nguyên chế độ hiện tại nếu không hỗ trợ Fullscreen API.
            }
        });

        const updateFullscreenButton = function () {
            const isFullscreen = currentFullscreenElement() === videoWrapper;
            const icon = fullscreenButton.querySelector('i');
            if (icon) {
                icon.classList.toggle('fa-expand', !isFullscreen);
                icon.classList.toggle('fa-compress', isFullscreen);
            }
            fullscreenButton.title = isFullscreen ? 'Thoát toàn màn hình' : 'Toàn màn hình';
            fullscreenButton.setAttribute('aria-label', fullscreenButton.title);
        };

        document.addEventListener('fullscreenchange', updateFullscreenButton);
        document.addEventListener('webkitfullscreenchange', updateFullscreenButton);
    }

    if (settingsButton && qualityMenu) {
        const sectionToggles = qualityMenu.querySelectorAll('.video-settings-section-toggle');
        const closeSettingsMenu = function () {
            qualityMenu.classList.remove('is-open');
            settingsButton.setAttribute('aria-expanded', 'false');
            showVideoControls();
        };
        settingsButton.addEventListener('click', function (event) {
            event.stopPropagation();
            const willOpen = !qualityMenu.classList.contains('is-open');
            qualityMenu.classList.toggle('is-open', willOpen);
            settingsButton.setAttribute('aria-expanded', String(willOpen));
            if (willOpen) clearTimeout(controlsHideTimer); else showVideoControls();
        });
        qualityMenu.addEventListener('click', event => event.stopPropagation());
        document.addEventListener('click', closeSettingsMenu);

        sectionToggles.forEach(function (toggle) {
            toggle.addEventListener('click', function () {
                const panel = document.getElementById(this.dataset.panel);
                const willOpen = panel && !panel.classList.contains('is-open');
                sectionToggles.forEach(function (item) {
                    const itemPanel = document.getElementById(item.dataset.panel);
                    item.setAttribute('aria-expanded', 'false');
                    if (itemPanel) itemPanel.classList.remove('is-open');
                });
                if (panel && willOpen) {
                    panel.classList.add('is-open');
                    this.setAttribute('aria-expanded', 'true');
                }
            });
        });

        qualityOptions.forEach(function (option) {
            option.addEventListener('click', function () {
                if (this.classList.contains('active')) {
                    return;
                }

            const currentSecond = Number.isFinite(player.currentTime) ? player.currentTime : 0;
            const shouldResume = !player.paused;
            const playbackRate = player.playbackRate;

            player.src = this.dataset.source;
            player.load();
            player.addEventListener('loadedmetadata', function restorePlaybackState() {
                player.currentTime = Math.min(currentSecond, Math.max(0, player.duration - 0.1));
                player.playbackRate = playbackRate;
                if (shouldResume) player.play().catch(function () {});
            }, { once: true });

                qualityOptions.forEach(item => {
                    item.classList.toggle('active', item === this);
                    const icon = item.querySelector('.fas');
                    if (icon) {
                        icon.classList.toggle('fa-check', item === this);
                        icon.classList.toggle('fa-circle', item !== this);
                    }
                });
                const qualityLabel = document.getElementById('currentQualityLabel');
                if (qualityLabel) qualityLabel.textContent = this.dataset.label;
                document.getElementById('videoQualityPanel')?.classList.remove('is-open');
                qualityMenu.querySelector('[data-panel="videoQualityPanel"]')?.setAttribute('aria-expanded', 'false');
            });
        });

        speedOptions.forEach(function (option) {
            option.addEventListener('click', function () {
                player.playbackRate = Number(this.dataset.speed);
                speedOptions.forEach(item => item.classList.toggle('active', item === this));
                const speedLabel = document.getElementById('currentSpeedLabel');
                if (speedLabel) speedLabel.textContent = Number(this.dataset.speed) === 1 ? 'Chuẩn' : `${this.dataset.speed}x`;
                document.getElementById('videoSpeedPanel')?.classList.remove('is-open');
                qualityMenu.querySelector('[data-panel="videoSpeedPanel"]')?.setAttribute('aria-expanded', 'false');
            });
        });
    }

    player.addEventListener('loadedmetadata', function () {
        if (resumeSeconds >= 10 && resumeSeconds < player.duration - 10) {
            player.currentTime = resumeSeconds;
        }
    }, { once: true });

    function savePlaybackProgress(force, overrideSeconds) {
        if (!Number.isFinite(player.currentTime)) return;
        const seconds = overrideSeconds === undefined
            ? Math.max(0, Math.floor(player.currentTime))
            : Math.max(0, Math.floor(overrideSeconds));
        if (!force && (seconds === lastSavedSecond || player.paused)) return;
        lastSavedSecond = seconds;

        fetch(progressUrl, {
            method: 'POST',
            keepalive: !!force,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': @json(csrf_token()),
            },
            body: JSON.stringify({ seconds: seconds, episode_id: episodeId }),
        }).catch(function () {});
    }

    const progressTimer = setInterval(function () { savePlaybackProgress(false); }, 10000);
    player.addEventListener('pause', function () { savePlaybackProgress(true); });
    player.addEventListener('ended', function () { savePlaybackProgress(true, 0); });
    window.addEventListener('pagehide', function () {
        clearInterval(progressTimer);
        savePlaybackProgress(true);
    });
});

// Star Rating functionality
document.addEventListener('DOMContentLoaded', function() {
    const starRating = document.getElementById('starRating');
    if (starRating) {
        const stars = starRating.querySelectorAll('.star');
        const ratingValue = document.getElementById('ratingValue');
        const ratingText = document.getElementById('ratingText');
        const submitBtn = document.getElementById('submitReview');

        const ratingTexts = {
            1: '1/10 - Rất tệ',
            2: '2/10 - Tệ',
            3: '3/10 - Kém',
            4: '4/10 - Dưới trung bình',
            5: '5/10 - Bình thường',
            6: '6/10 - Khá',
            7: '7/10 - Hay',
            8: '8/10 - Rất hay',
            9: '9/10 - Xuất sắc',
            10: '10/10 - Tuyệt vời'
        };

        stars.forEach(star => {
            star.addEventListener('mouseenter', function() {
                const value = this.dataset.value;
                highlightStars(value);
                ratingText.textContent = ratingTexts[value];
            });

            star.addEventListener('mouseleave', function() {
                const currentValue = ratingValue.value;
                if (currentValue) {
                    highlightStars(currentValue);
                    ratingText.textContent = ratingTexts[currentValue];
                } else {
                    resetStars();
                    ratingText.textContent = 'Chọn số sao';
                }
            });

            star.addEventListener('click', function() {
                const value = this.dataset.value;
                ratingValue.value = value;
                highlightStars(value);
                ratingText.textContent = ratingTexts[value] + ' - Đã chọn!';
            });
        });

        function highlightStars(value) {
            stars.forEach(s => {
                const starValue = parseInt(s.dataset.value);
                if (starValue <= parseInt(value)) {
                    s.classList.add('active');
                    s.querySelector('i').className = 'fas fa-star';
                } else {
                    s.classList.remove('active');
                    s.querySelector('i').className = 'far fa-star';
                }
            });
        }

        function resetStars() {
            stars.forEach(s => {
                s.classList.remove('active');
                s.querySelector('i').className = 'far fa-star';
            });
        }

        // Validate form before submit
        if (submitBtn) {
            submitBtn.closest('form').addEventListener('submit', function(e) {
                if (!ratingValue.value) {
                    e.preventDefault();
                    alert('Vui lòng chọn số sao đánh giá!');
                    return false;
                }
            });
        }
    }

    // Scroll đến phần reviews nếu có hash trong URL
    if (window.location.hash === '#reviews') {
        setTimeout(function() {
            const reviewsSection = document.getElementById('reviews');
            if (reviewsSection) {
                reviewsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 100);
    }

    // Scroll mượt đến reviews sau khi submit (fallback)
    const reviewForm = document.getElementById('reviewForm');
    if (reviewForm) {
        reviewForm.addEventListener('submit', function() {
            sessionStorage.setItem('scrollToReviews', 'true');
        });
    }

    // Kiểm tra nếu cần scroll sau khi reload
    if (sessionStorage.getItem('scrollToReviews') === 'true') {
        sessionStorage.removeItem('scrollToReviews');
        setTimeout(function() {
            const reviewsSection = document.getElementById('reviews');
            if (reviewsSection) {
                reviewsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 300);
    }

    // Scroll đến comments nếu có hash
    if (window.location.hash === '#comments') {
        setTimeout(function() {
            const commentsSection = document.getElementById('comments');
            if (commentsSection) {
                commentsSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }, 100);
    }
});

// Toggle reply form
function toggleReplyForm(commentId) {
    const form = document.getElementById('reply-form-' + commentId);
    if (form) {
        form.style.display = form.style.display === 'none' ? 'block' : 'none';
    }
}

// Like/Dislike comment
function likeComment(commentId, action) {
    fetch('{{ route('comments.like') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: new URLSearchParams({ comment_id: commentId, action: action })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('likes-' + commentId).textContent = data.likes;
            document.getElementById('dislikes-' + commentId).textContent = data.dislikes;
        } else {
            alert(data.message || 'Có lỗi xảy ra');
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}
</script>


@endsection
