/**
 * useDevices.js — Composable quản lý danh sách thiết bị đăng nhập
 *
 * Đóng gói toàn bộ state + logic của DeviceManager.vue,
 * giữ component sạch chỉ còn template và gọi composable.
 */

import { ref, computed } from 'vue';
import http, { getDeviceId } from '../http.js';

export function useDevices() {
    // ----------------------------------------------------------------
    // State
    // ----------------------------------------------------------------

    /** @type {import('vue').Ref<Array>} Danh sách thiết bị từ API */
    const devices = ref([]);

    /** @type {import('vue').Ref<number|null>} ID token hiện tại */
    const currentTokenId = ref(null);

    /** @type {import('vue').Ref<boolean>} */
    const loading = ref(false);

    /** @type {import('vue').Ref<boolean>} Loading riêng cho hành động kick */
    const kicking = ref(false);

    /** @type {import('vue').Ref<string|null>} Thông báo lỗi */
    const error = ref(null);

    /** @type {import('vue').Ref<string|null>} Thông báo thành công */
    const successMessage = ref(null);

    // ----------------------------------------------------------------
    // Computed
    // ----------------------------------------------------------------

    /** Số thiết bị đang stream */
    const streamingCount = computed(
        () => devices.value.filter((d) => d.is_streaming).length,
    );

    /** Có thiết bị khác ngoài thiết bị hiện tại không */
    const hasOtherDevices = computed(
        () => devices.value.some((d) => !d.is_current),
    );

    // ----------------------------------------------------------------
    // Actions
    // ----------------------------------------------------------------

    /**
     * Tải danh sách thiết bị từ GET /api/devices.
     */
    async function fetchDevices() {
        loading.value = true;
        error.value = null;

        try {
            const { data } = await http.get('/devices');
            devices.value = data.devices ?? [];
            currentTokenId.value = data.current_token_id ?? null;
        } catch (err) {
            // 401/403 đã được interceptor xử lý redirect/event
            // Chỉ set error cho các lỗi network / 422 / 500
            if (err.response?.status !== 401 && err.response?.status !== 403) {
                error.value = err.response?.data?.message
                    ?? 'Không thể tải danh sách thiết bị. Vui lòng thử lại.';
            }
        } finally {
            loading.value = false;
        }
    }

    /**
     * Đăng xuất tất cả thiết bị khác — POST /api/devices/kick-other.
     * Sau khi thành công, tải lại danh sách.
     */
    async function kickOtherDevices() {
        const deviceId = getDeviceId();
        if (!deviceId) {
            error.value = 'Không xác định được thiết bị hiện tại.';
            return;
        }

        kicking.value = true;
        error.value = null;
        successMessage.value = null;

        try {
            const { data } = await http.post('/devices/kick-other', {
                device_id: deviceId,
            });

            successMessage.value = data.message
                ?? 'Đã đăng xuất tất cả thiết bị khác thành công.';

            // Tải lại để phản ánh trạng thái mới
            await fetchDevices();

            // Tự động xóa thông báo thành công sau 4 giây
            setTimeout(() => { successMessage.value = null; }, 4000);
        } catch (err) {
            if (err.response?.status !== 401 && err.response?.status !== 403) {
                error.value = err.response?.data?.message
                    ?? 'Đăng xuất thiết bị khác thất bại. Vui lòng thử lại.';
            }
        } finally {
            kicking.value = false;
        }
    }

    /**
     * Đăng xuất chính thiết bị này — POST /api/devices/logout.
     * Interceptor 401 sẽ redirect về login sau khi token bị xóa.
     */
    async function logoutCurrentDevice() {
        const deviceId = getDeviceId();
        if (!deviceId) return;

        try {
            await http.post('/devices/logout', { device_id: deviceId });
        } finally {
            // Dù request lỗi hay không, xóa auth cục bộ và về login
            localStorage.removeItem('sanctum_token');
            localStorage.removeItem('sanctum_device_id');
            window.location.href = '/login';
        }
    }

    // ----------------------------------------------------------------
    // Return API của composable
    // ----------------------------------------------------------------
    return {
        // State
        devices,
        currentTokenId,
        loading,
        kicking,
        error,
        successMessage,
        // Computed
        streamingCount,
        hasOtherDevices,
        // Actions
        fetchDevices,
        kickOtherDevices,
        logoutCurrentDevice,
    };
}
