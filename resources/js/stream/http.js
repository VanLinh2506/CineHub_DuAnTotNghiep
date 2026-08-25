/**
 * http.js — Axios instance dùng riêng cho Stream & Device APIs
 *
 * Tách biệt hoàn toàn khỏi window.axios của app chính để tránh
 * ảnh hưởng đến các request session-based khác.
 *
 * Interceptors xử lý tự động:
 *   • 401 → xóa token, redirect Login với thông báo
 *   • 403 → emit event "stream:revoked" để VideoPlayer tự dừng
 */

import axios from 'axios';

// ----------------------------------------------------------------
// Tạo instance riêng
// ----------------------------------------------------------------
const http = axios.create({
    baseURL: '/api',
    headers: {
        'Accept':       'application/json',
        'Content-Type': 'application/json',
        'X-Requested-With': 'XMLHttpRequest',
    },
    timeout: 10_000, // 10s timeout
});

// ----------------------------------------------------------------
// Request interceptor — đính kèm Bearer token từ localStorage
// ----------------------------------------------------------------
http.interceptors.request.use(
    (config) => {
        const token = localStorage.getItem('sanctum_token');
        if (token) {
            config.headers['Authorization'] = `Bearer ${token}`;
        }
        return config;
    },
    (error) => Promise.reject(error),
);

// ----------------------------------------------------------------
// Response interceptor — xử lý lỗi tập trung
// ----------------------------------------------------------------
http.interceptors.response.use(
    // Phản hồi thành công — trả qua nguyên vẹn
    (response) => response,

    (error) => {
        const status = error.response?.status;

        if (status === 401) {
            // Token không hợp lệ hoặc đã bị thu hồi (bị kick)
            // → xóa token cục bộ và đẩy về trang đăng nhập
            localStorage.removeItem('sanctum_token');
            localStorage.removeItem('sanctum_device_id');

            // Dùng CustomEvent để Blade layout có thể lắng nghe nếu cần
            window.dispatchEvent(new CustomEvent('auth:unauthenticated', {
                detail: { message: 'Phiên đăng nhập đã hết hạn hoặc tài khoản vừa bị đăng xuất từ xa. Vui lòng đăng nhập lại.' },
            }));

            // Redirect về trang login sau 100ms (cho event handler kịp chạy)
            setTimeout(() => {
                window.location.href = '/login?reason=kicked';
            }, 100);
        }

        if (status === 403) {
            // Stream bị thu hồi (bị kick hoặc TTL hết) — VideoPlayer lắng nghe event này
            window.dispatchEvent(new CustomEvent('stream:revoked', {
                detail: {
                    message: error.response?.data?.message
                        ?? 'Luồng phát đã bị thu hồi. Vui lòng kiểm tra thiết bị đang xem.',
                },
            }));
        }

        // Luôn reject để component xử lý tiếp nếu cần
        return Promise.reject(error);
    },
);

// ----------------------------------------------------------------
// Helpers lưu/xóa thông tin auth
// ----------------------------------------------------------------

/**
 * Lưu token + deviceId sau khi đăng nhập API thành công.
 * @param {string} token
 * @param {string} deviceId
 */
export function saveAuth(token, deviceId) {
    localStorage.setItem('sanctum_token', token);
    localStorage.setItem('sanctum_device_id', deviceId);
}

/**
 * Xóa auth khỏi localStorage.
 */
export function clearAuth() {
    localStorage.removeItem('sanctum_token');
    localStorage.removeItem('sanctum_device_id');
}

/**
 * Lấy deviceId đã lưu.
 * @returns {string|null}
 */
export function getDeviceId() {
    return localStorage.getItem('sanctum_device_id');
}

export default http;
