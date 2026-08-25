<script setup>
/**
 * DeviceManager.vue
 *
 * Hiển thị danh sách tất cả thiết bị đang đăng nhập vào tài khoản,
 * kèm trạng thái stream (từ Redis) và nút "Đăng xuất khỏi tất cả thiết bị khác".
 *
 * Toàn bộ logic được tách vào composable useDevices để giữ component sạch.
 */

import { onMounted } from 'vue';
import { useDevices } from '../composables/useDevices.js';

const {
    devices,
    loading,
    kicking,
    error,
    successMessage,
    streamingCount,
    hasOtherDevices,
    fetchDevices,
    kickOtherDevices,
    logoutCurrentDevice,
} = useDevices();

// Tải danh sách thiết bị ngay khi component mount
onMounted(fetchDevices);

// ----------------------------------------------------------------
// Helpers format
// ----------------------------------------------------------------

/**
 * Format ISO date string → thời gian tương đối dễ đọc (tiếng Việt).
 * Ví dụ: "5 phút trước", "2 giờ trước", "3 ngày trước"
 */
function formatRelativeTime(isoString) {
    if (!isoString) return 'Chưa sử dụng';
    const diffMs  = Date.now() - new Date(isoString).getTime();
    const diffMin = Math.floor(diffMs / 60_000);

    if (diffMin < 1)  return 'Vừa xong';
    if (diffMin < 60) return `${diffMin} phút trước`;

    const diffHour = Math.floor(diffMin / 60);
    if (diffHour < 24) return `${diffHour} giờ trước`;

    const diffDay = Math.floor(diffHour / 24);
    if (diffDay < 30) return `${diffDay} ngày trước`;

    return new Date(isoString).toLocaleDateString('vi-VN');
}
</script>

<template>
    <div class="dm-wrapper">

        <!-- Tiêu đề -->
        <div class="dm-header">
            <h2 class="dm-title">
                Thiết bị đăng nhập
            </h2>
            <p class="dm-subtitle">
                Quản lý các thiết bị đang truy cập tài khoản của bạn.
                <span v-if="streamingCount > 0" class="dm-stream-badge">
                    {{ streamingCount }} đang phát
                </span>
            </p>
        </div>

        <!-- Loading skeleton -->
        <div v-if="loading" class="dm-skeleton-list" aria-busy="true" aria-label="Đang tải danh sách thiết bị">
            <div v-for="i in 3" :key="i" class="dm-skeleton-item">
                <div class="dm-skeleton-icon"></div>
                <div class="dm-skeleton-lines">
                    <div class="dm-skeleton-line dm-skeleton-line--wide"></div>
                    <div class="dm-skeleton-line dm-skeleton-line--narrow"></div>
                </div>
            </div>
        </div>

        <!-- Thông báo lỗi -->
        <Transition name="fade">
            <div v-if="error" class="dm-alert dm-alert--error" role="alert">
                <span aria-hidden="true">❌</span> {{ error }}
            </div>
        </Transition>

        <!-- Thông báo thành công -->
        <Transition name="fade">
            <div v-if="successMessage" class="dm-alert dm-alert--success" role="status">
                <span aria-hidden="true">✅</span> {{ successMessage }}
            </div>
        </Transition>

        <!-- Danh sách thiết bị -->
        <ul v-if="!loading && devices.length > 0" class="dm-list" role="list">
            <li
                v-for="device in devices"
                :key="device.token_id"
                class="dm-item"
                :class="{
                    'dm-item--current':   device.is_current,
                    'dm-item--streaming': device.is_streaming,
                }"
            >
                <!-- Icon thiết bị -->
                <div class="dm-item-icon" aria-hidden="true">
                    {{ device.is_streaming ? '📺' : '💻' }}
                </div>

                <!-- Thông tin thiết bị -->
                <div class="dm-item-info">
                    <div class="dm-item-name">
                        {{ device.device_name }}
                        <span v-if="device.is_current" class="dm-badge dm-badge--current">
                            Thiết bị này
                        </span>
                        <span v-if="device.is_streaming" class="dm-badge dm-badge--streaming">
                            Đang phát
                        </span>
                    </div>
                    <div class="dm-item-meta">
                        <span>Đăng nhập {{ formatRelativeTime(device.created_at) }}</span>
                        <span class="dm-meta-sep">·</span>
                        <span>Hoạt động {{ formatRelativeTime(device.last_used_at) }}</span>
                    </div>
                </div>
            </li>
        </ul>

        <!-- Trống -->
        <div v-if="!loading && devices.length === 0" class="dm-empty">
            Không có thiết bị nào đang đăng nhập.
        </div>

        <!-- Khu vực hành động -->
        <div v-if="!loading && devices.length > 0" class="dm-actions">

            <!-- Nút kick all other -->
            <button
                class="dm-btn dm-btn--danger"
                :disabled="kicking || !hasOtherDevices"
                :aria-busy="kicking"
                @click="kickOtherDevices"
            >
                <span v-if="kicking" class="dm-btn-spinner" aria-hidden="true"></span>
                <span>
                    {{ kicking ? 'Đang đăng xuất...' : 'Đăng xuất khỏi tất cả thiết bị khác' }}
                </span>
            </button>

            <!-- Nút refresh danh sách -->
            <button
                class="dm-btn dm-btn--ghost"
                :disabled="loading"
                aria-label="Tải lại danh sách thiết bị"
                @click="fetchDevices"
            >
                🔄 Làm mới
            </button>
        </div>

        <!-- Cảnh báo nếu không có thiết bị khác -->
        <p
            v-if="!loading && !hasOtherDevices && devices.length > 0"
            class="dm-no-other"
        >
            Không có thiết bị nào khác đang đăng nhập.
        </p>
    </div>
</template>

<style scoped>
/* ---------------------------------------------------------------
   DeviceManager — scoped styles
--------------------------------------------------------------- */

.dm-wrapper {
    padding: 24px;
    background: #141414;
    border-radius: 12px;
    color: #e5e5e5;
    font-family: Arial, sans-serif;
    max-width: 640px;
}

/* Header */
.dm-header   { margin-bottom: 20px; }
.dm-title    { font-size: 20px; font-weight: 700; margin: 0 0 6px; }
.dm-subtitle { font-size: 14px; color: #999; margin: 0; display: flex; align-items: center; gap: 8px; }

.dm-stream-badge {
    display: inline-flex;
    align-items: center;
    padding: 2px 8px;
    border-radius: 999px;
    background: rgba(229 9 20 / 0.2);
    color: #ff4444;
    font-size: 12px;
    font-weight: 600;
}

/* Skeleton */
.dm-skeleton-list { display: flex; flex-direction: column; gap: 12px; }
.dm-skeleton-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px;
    border-radius: 8px;
    background: #1f1f1f;
    animation: pulse 1.4s ease-in-out infinite;
}
.dm-skeleton-icon  { width: 40px; height: 40px; border-radius: 50%; background: #2e2e2e; flex-shrink: 0; }
.dm-skeleton-lines { flex: 1; display: flex; flex-direction: column; gap: 8px; }
.dm-skeleton-line  { height: 12px; border-radius: 4px; background: #2e2e2e; }
.dm-skeleton-line--wide   { width: 65%; }
.dm-skeleton-line--narrow { width: 40%; }

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.5; }
}

/* Alerts */
.dm-alert {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    border-radius: 8px;
    margin-bottom: 16px;
    font-size: 14px;
}
.dm-alert--error   { background: rgba(239 68 68 / 0.15); color: #fca5a5; border: 1px solid rgba(239 68 68 / 0.3); }
.dm-alert--success { background: rgba(34 197 94 / 0.15); color: #86efac; border: 1px solid rgba(34 197 94 / 0.3); }

/* Device list */
.dm-list {
    list-style: none;
    margin: 0 0 20px;
    padding: 0;
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.dm-item {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 16px;
    border-radius: 8px;
    background: #1f1f1f;
    border: 1px solid transparent;
    transition: border-color 0.2s;
}
.dm-item--current   { border-color: rgba(99 102 241 / 0.5); background: rgba(99 102 241 / 0.08); }
.dm-item--streaming { border-color: rgba(229 9 20 / 0.4); }

.dm-item-icon {
    font-size: 28px;
    flex-shrink: 0;
    width: 40px;
    text-align: center;
}

.dm-item-info { flex: 1; min-width: 0; }

.dm-item-name {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 6px;
    font-size: 15px;
    font-weight: 600;
    color: #f5f5f5;
    margin-bottom: 4px;
    word-break: break-word;
}

.dm-item-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 4px;
    font-size: 12px;
    color: #777;
}
.dm-meta-sep { color: #444; }

/* Badges */
.dm-badge {
    display: inline-flex;
    align-items: center;
    padding: 1px 7px;
    border-radius: 999px;
    font-size: 11px;
    font-weight: 600;
    white-space: nowrap;
}
.dm-badge--current   { background: rgba(99 102 241 / 0.2); color: #a5b4fc; }
.dm-badge--streaming { background: rgba(229 9 20 / 0.2);   color: #fca5a5; }

/* Empty state */
.dm-empty {
    text-align: center;
    color: #555;
    font-size: 14px;
    padding: 32px 0;
}

/* Actions */
.dm-actions {
    display: flex;
    flex-wrap: wrap;
    gap: 10px;
    align-items: center;
}

.dm-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 8px;
    border: none;
    font-size: 14px;
    font-weight: 600;
    cursor: pointer;
    transition: background 0.2s, opacity 0.2s;
}
.dm-btn:disabled { opacity: 0.5; cursor: not-allowed; }

.dm-btn--danger  { background: #e50914; color: #fff; }
.dm-btn--danger:not(:disabled):hover  { background: #c1030f; }

.dm-btn--ghost   { background: #2a2a2a; color: #ccc; }
.dm-btn--ghost:not(:disabled):hover   { background: #333; }

.dm-btn-spinner {
    width: 16px;
    height: 16px;
    border: 2px solid rgba(255 255 255 / 0.3);
    border-top-color: #fff;
    border-radius: 50%;
    animation: spin 0.7s linear infinite;
    flex-shrink: 0;
}
@keyframes spin { to { transform: rotate(360deg); } }

.dm-no-other {
    font-size: 13px;
    color: #555;
    margin: 12px 0 0;
}

/* Transition */
.fade-enter-active,
.fade-leave-active { transition: opacity 0.25s; }
.fade-enter-from,
.fade-leave-to     { opacity: 0; }
</style>
