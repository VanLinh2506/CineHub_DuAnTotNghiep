/**
 * stream/main.js — Entry point cho module Stream & Device Management
 *
 * Mount các Vue component vào đúng DOM element nếu tồn tại trên trang.
 * Blade template chỉ cần thêm thẻ <div id="..."> là xong.
 *
 * Ví dụ sử dụng trong Blade:
 *
 *   VideoPlayer:
 *   <div
 *     id="video-player-app"
 *     data-src="/storage/movies/sample.mp4"
 *     data-device-id="{{ $deviceId }}"
 *     data-device-name="Chrome / Windows"
 *     data-poster="/storage/thumbs/movie.jpg"
 *   ></div>
 *
 *   DeviceManager:
 *   <div id="device-manager-app"></div>
 *
 * Cả hai đều có thể cùng tồn tại trên một trang.
 */

import { createApp, defineComponent, h } from 'vue';
import VideoPlayer    from './components/VideoPlayer.vue';
import DeviceManager  from './components/DeviceManager.vue';

// ----------------------------------------------------------------
// Mount VideoPlayer
// ----------------------------------------------------------------
const videoMount = document.getElementById('video-player-app');

if (videoMount) {
    // Đọc props từ data attributes của div — giúp Blade truyền dữ liệu an toàn
    const src        = videoMount.dataset.src        ?? '';
    const deviceId   = videoMount.dataset.deviceId   ?? crypto.randomUUID();
    const deviceName = videoMount.dataset.deviceName ?? '';
    const poster     = videoMount.dataset.poster     ?? '';
    const autoplay   = videoMount.dataset.autoplay   === 'true';

    createApp(VideoPlayer, { src, deviceId, deviceName, poster, autoplay })
        .mount(videoMount);
}

// ----------------------------------------------------------------
// Mount DeviceManager
// ----------------------------------------------------------------
const deviceMount = document.getElementById('device-manager-app');

if (deviceMount) {
    createApp(DeviceManager).mount(deviceMount);
}
