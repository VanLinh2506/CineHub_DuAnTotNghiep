<script setup>
/**
 * VideoPlayer.vue
 *
 * Component phát video có tích hợp:
 *   - Gọi POST /api/video/play trước khi phát
 *   - setInterval gọi /heartbeat mỗi HEARTBEAT_INTERVAL ms khi đang play
 *   - clearInterval khi pause hoặc onUnmounted
 *   - Lắng nghe global event "stream:revoked" từ Axios interceptor → dừng video
 *   - Gọi POST /api/video/stop khi người dùng rời trang
 *
 * Props:
 *   src        string   required  — URL nguồn video
 *   deviceId   string   required  — UUID thiết bị (sinh từ ngoài, truyền vào)
 *   deviceName string   optional  — Tên thiết bị hiển thị
 *   poster     string   optional  — Ảnh thumbnail
 *   autoplay   boolean  optional  — Mặc định false
 */

import { ref, onMounted, onUnmounted } from 'vue';
import http, { getDeviceId } from '../http.js';

// ----------------------------------------------------------------
// Props
// ----------------------------------------------------------------
const props = defineProps({
    src:        { type: String,  required: true },
    deviceId:   { type: String,  required: true },
    deviceName: { type: String,  default: '' },
    poster:     { type: String,  default: '' },
    autoplay:   { type: Boolean, default: false },
});

// ----------------------------------------------------------------
// Constants
// ----------------------------------------------------------------
/** Gọi heartbeat mỗi 30 giây — nhỏ hơn TTL Redis (60s) */
const HEARTBEAT_INTERVAL = 30_000;

// ----------------------------------------------------------------
// Refs
// ----------------------------------------------------------------
const videoEl = ref(null);   // Template ref đến <video>

/** Trạng thái giao diện */
const isAllowed    = ref(false);   // Đã được cấp phép stream
const isLoading    = ref(false);   // Đang chờ API /play
const errorMessage = ref('');      // Thông báo lỗi hiển thị cho user
const warningBanner = ref('');     // Banner cảnh báo (stream bị thu hồi)

/** ID của setInterval heartbeat */
let heartbeatTimer = null;

// ----------------------------------------------------------------
// Lifecycle
// ----------------------------------------------------------------
onMounted(() => {
    // Lắng nghe event "stream:revoked" từ Axios interceptor (HTTP 403)
    window.addEventListener('stream:revoked', onStreamRevoked);
});

onUnmounted(() => {
    // Dọn dẹp interval + event listener
    stopHeartbeat();
    window.removeEventListener('stream:revoked', onStreamRevoked);

    // Giải phóng luồng khi component bị destroy (thoát trang, đổi route)
    releaseStream();
});

// ----------------------------------------------------------------
// Play flow
// ----------------------------------------------------------------

/**
 * Gọi API /play để xin phép stream.
 * Được gọi khi user nhấn nút Play hoặc khi có autoplay.
 */
async function requestPlay() {
    if (isLoading.value) return;

    isLoading.value  = true;
    errorMessage.value = '';
    warningBanner.value = '';

    try {
        await http.post('/video/play', {
            device_id:   props.deviceId,
            device_name: props.deviceName || undefined,
        });

        // Được phép → bắt đầu phát thật sự
        isAllowed.value = true;
        videoEl.value?.play();
        startHeartbeat();
    } catch (err) {
        // 403 → đã vượt giới hạn thiết bị đồng thời
        if (err.response?.status === 403) {
            const active = err.response.data?.active_devices ?? [];
            errorMessage.value = err.response.data?.message
                ?? `Tài khoản đang stream trên ${active.length} thiết bị khác. Vui lòng dừng một thiết bị để tiếp tục.`;
        } else if (err.response?.status !== 401) {
            // 401 đã redirect về login qua interceptor
            errorMessage.value = 'Không thể khởi động luồng phát. Vui lòng thử lại.';
        }
        isAllowed.value = false;
    } finally {
        isLoading.value = false;
    }
}

// ----------------------------------------------------------------
// Heartbeat
// ----------------------------------------------------------------

function startHeartbeat() {
    // Tránh tạo nhiều interval nếu gọi lại
    if (heartbeatTimer !== null) return;

    heartbeatTimer = setInterval(async () => {
        try {
            await http.post('/video/heartbeat', { device_id: props.deviceId });
        } catch {
            // 401 → interceptor đã xử lý redirect
            // 403 → interceptor đã dispatch event "stream:revoked" → onStreamRevoked chạy
            // Không cần làm gì thêm ở đây
        }
    }, HEARTBEAT_INTERVAL);
}

function stopHeartbeat() {
    if (heartbeatTimer !== null) {
        clearInterval(heartbeatTimer);
        heartbeatTimer = null;
    }
}

// ----------------------------------------------------------------
// Video element event handlers
// ----------------------------------------------------------------

function onVideoPause() {
    // Pause → ngừng gia hạn. Stream sẽ tự hết hạn sau TTL nếu không resume.
    stopHeartbeat();
}

function onVideoPlay() {
    // Resume sau pause → khởi động lại heartbeat
    if (isAllowed.value) {
        startHeartbeat();
    }
}

function onVideoEnded() {
    stopHeartbeat();
    releaseStream();
    isAllowed.value = false;
}

// ----------------------------------------------------------------
// Stream revoked handler (lắng nghe từ global event)
// ----------------------------------------------------------------

function onStreamRevoked(event) {
    // Dừng video ngay lập tức
    videoEl.value?.pause();
    stopHeartbeat();
    isAllowed.value = false;

    // Hiện banner cảnh báo nổi bật
    warningBanner.value = event.detail?.message
        ?? 'Luồng phát bị thu hồi do đăng nhập từ thiết bị khác.';
}

// ----------------------------------------------------------------
// Release stream
// ----------------------------------------------------------------

async function releaseStream() {
    try {
        // Fire-and-forget: không chặn UI
        await http.post('/video/stop', { device_id: props.deviceId });
    } catch {
        // Bỏ qua lỗi — TTL Redis sẽ tự dọn sau 60s
    }
}

// ----------------------------------------------------------------
// Expose để parent component có thể gọi nếu cần
// ----------------------------------------------------------------
defineExpose({ requestPlay });
</script>

<template>
    <div class="video-player-wrapper">

        <!-- Banner cảnh báo: stream bị thu hồi (HTTP 403 từ heartbeat) -->
        <Transition name="fade">
            <div
                v-if="warningBanner"
                class="stream-warning-banner"
                role="alert"
                aria-live="assertive"
            >
                <span class="stream-warning-icon" aria-hidden="true">⚠️</span>
                <span class="stream-warning-text">{{ warningBanner }}</span>
                <button
                    class="stream-warning-dismiss"
                    aria-label="Đóng cảnh báo"
                    @click="warningBanner = ''"
                >
                    ✕
                </button>
            </div>
        </Transition>

        <!-- Lỗi khi xin phép stream thất bại (HTTP 403 từ /play) -->
        <Transition name="fade">
            <div
                v-if="errorMessage"
                class="stream-error-box"
                role="alert"
            >
                <p class="stream-error-title">Không thể phát video</p>
                <p class="stream-error-body">{{ errorMessage }}</p>
                <button class="stream-retry-btn" @click="requestPlay">
                    Thử lại
                </button>
            </div>
        </Transition>

        <!-- Màn hình chờ khi đang gọi API /play -->
        <div
            v-if="isLoading"
            class="stream-loading-overlay"
            aria-label="Đang kết nối luồng phát..."
        >
            <span class="stream-spinner" aria-hidden="true"></span>
            <span>Đang kiểm tra giới hạn thiết bị...</span>
        </div>

        <!-- Nút Play thủ công khi chưa được cấp phép và không có lỗi -->
        <div
            v-if="!isAllowed && !isLoading && !errorMessage && !warningBanner"
            class="stream-play-gate"
        >
            <button
                class="stream-play-btn"
                :style="poster ? `background-image: url('${poster}')` : ''"
                aria-label="Phát video"
                @click="requestPlay"
            >
                <span class="stream-play-icon" aria-hidden="true">▶</span>
            </button>
        </div>

        <!-- Thẻ video thật — ẩn cho đến khi được cấp phép -->
        <video
            v-show="isAllowed"
            ref="videoEl"
            class="stream-video"
            :src="src"
            :poster="poster"
            controls
            preload="metadata"
            :autoplay="autoplay"
            @pause="onVideoPause"
            @play="onVideoPlay"
            @ended="onVideoEnded"
        >
            Trình duyệt của bạn không hỗ trợ thẻ video HTML5.
        </video>
    </div>
</template>

<style scoped>
/* ---------------------------------------------------------------
   VideoPlayer — scoped styles (dùng Tailwind-compatible vars)
--------------------------------------------------------------- */
.video-player-wrapper {
    position: relative;
    width: 100%;
    background: #000;
    border-radius: 8px;
    overflow: hidden;
}

/* Video element */
.stream-video {
    width: 100%;
    display: block;
    aspect-ratio: 16 / 9;
}

/* Play gate — thumbnail + nút play trung tâm */
.stream-play-gate {
    display: flex;
    align-items: center;
    justify-content: center;
    aspect-ratio: 16 / 9;
    background: #111;
}

.stream-play-btn {
    width: 100%;
    height: 100%;
    border: none;
    cursor: pointer;
    background: #111 center/cover no-repeat;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: filter 0.2s;
}
.stream-play-btn:hover { filter: brightness(1.2); }

.stream-play-icon {
    font-size: 64px;
    color: rgba(255 255 255 / 0.9);
    text-shadow: 0 2px 12px rgba(0 0 0 / 0.7);
    pointer-events: none;
}

/* Loading overlay */
.stream-loading-overlay {
    aspect-ratio: 16 / 9;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 12px;
    color: #ccc;
    font-size: 14px;
    background: #111;
}

.stream-spinner {
    width: 36px;
    height: 36px;
    border: 3px solid rgba(255 255 255 / 0.15);
    border-top-color: #e50914;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* Error box */
.stream-error-box {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 10px;
    padding: 32px 24px;
    background: #1a0505;
    aspect-ratio: 16 / 9;
    text-align: center;
}
.stream-error-title {
    font-size: 18px;
    font-weight: 700;
    color: #ff4444;
    margin: 0;
}
.stream-error-body {
    font-size: 14px;
    color: #ccc;
    margin: 0;
    max-width: 420px;
    line-height: 1.5;
}
.stream-retry-btn {
    margin-top: 8px;
    padding: 8px 24px;
    border: none;
    border-radius: 6px;
    background: #e50914;
    color: #fff;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s;
}
.stream-retry-btn:hover { background: #c1030f; }

/* Warning banner (stream bị thu hồi) */
.stream-warning-banner {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 12px 16px;
    background: #7c2d12;
    border-bottom: 2px solid #ea580c;
    color: #fed7aa;
    font-size: 14px;
}
.stream-warning-icon { font-size: 18px; flex-shrink: 0; }
.stream-warning-text { flex: 1; line-height: 1.4; }
.stream-warning-dismiss {
    background: none;
    border: none;
    color: #fed7aa;
    cursor: pointer;
    font-size: 16px;
    padding: 0 4px;
    flex-shrink: 0;
}
.stream-warning-dismiss:hover { color: #fff; }

/* Transition */
.fade-enter-active,
.fade-leave-active { transition: opacity 0.25s ease; }
.fade-enter-from,
.fade-leave-to    { opacity: 0; }
</style>
