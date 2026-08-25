<?php

namespace App\Services;

use Illuminate\Support\Facades\Redis;

/**
 * StreamService
 *
 * Toàn bộ logic Redis cho tính năng giới hạn stream đồng thời được
 * tập trung tại đây — tuân theo nguyên tắc Single Responsibility.
 *
 * Cấu trúc key Redis:
 *   stream:{userId}:{deviceId}  →  value = device_name (string)
 *   TTL mặc định: STREAM_TTL giây (60s).
 *   Heartbeat sẽ gia hạn thêm STREAM_TTL mỗi 30s.
 *
 * Prefix "stream:" được dùng để scan toàn bộ thiết bị của một user
 * mà không cần maintain một SET riêng, tránh race condition.
 */
class StreamService
{
    /** TTL cho mỗi stream (giây). Heartbeat cần gọi trước khi hết. */
    private const STREAM_TTL = 60;

    /** Prefix chung cho tất cả key stream. */
    private const KEY_PREFIX = 'stream';

    // ----------------------------------------------------------------
    // Key builder
    // ----------------------------------------------------------------

    /**
     * Tạo Redis key cho một cặp (user, device).
     */
    public function buildKey(int $userId, string $deviceId): string
    {
        return self::KEY_PREFIX . ":{$userId}:{$deviceId}";
    }

    /**
     * Pattern để scan tất cả key của một user.
     */
    public function buildPattern(int $userId): string
    {
        return self::KEY_PREFIX . ":{$userId}:*";
    }

    // ----------------------------------------------------------------
    // Core stream operations
    // ----------------------------------------------------------------

    /**
     * Kiểm tra số stream hiện tại của user.
     *
     * @return string[]  Danh sách deviceId đang stream.
     */
    public function getActiveDeviceIds(int $userId): array
    {
        $keys = Redis::keys($this->buildPattern($userId));

        if (empty($keys)) {
            return [];
        }

        // Bóc tách deviceId từ cuối key: stream:{userId}:{deviceId}
        $deviceIds = [];
        foreach ($keys as $key) {
            // Loại bỏ prefix do Redis driver thêm vào (vd: "laravel_database_")
            $bare = $this->stripRedisPrefix($key);
            $parts = explode(':', $bare, 3);
            if (isset($parts[2])) {
                $deviceIds[] = $parts[2];
            }
        }

        return $deviceIds;
    }

    /**
     * Đăng ký một device bắt đầu stream.
     * Trả về false nếu đã đạt giới hạn max_devices.
     *
     * @param  string  $deviceName  Tên thiết bị hiển thị (user-agent rút gọn)
     */
    public function startStream(int $userId, string $deviceId, int $maxDevices, string $deviceName = ''): bool
    {
        $key = $this->buildKey($userId, $deviceId);

        // Nếu device này đã stream rồi → gia hạn luôn (idempotent)
        if (Redis::exists($key)) {
            Redis::expire($key, self::STREAM_TTL);
            return true;
        }

        // Đếm số device đang stream (không tính device hiện tại)
        $activeCount = count($this->getActiveDeviceIds($userId));

        if ($activeCount >= $maxDevices) {
            return false;
        }

        // Cấp phép stream: lưu key với TTL
        Redis::setex($key, self::STREAM_TTL, $deviceName ?: $deviceId);

        return true;
    }

    /**
     * Gia hạn TTL cho một stream đang active.
     * Trả về false nếu key không tồn tại (stream đã hết hạn hoặc bị kick).
     */
    public function heartbeat(int $userId, string $deviceId): bool
    {
        $key = $this->buildKey($userId, $deviceId);

        if (!Redis::exists($key)) {
            return false;
        }

        Redis::expire($key, self::STREAM_TTL);
        return true;
    }

    /**
     * Xóa stream của một device cụ thể.
     */
    public function stopStream(int $userId, string $deviceId): void
    {
        Redis::del($this->buildKey($userId, $deviceId));
    }

    /**
     * Xóa tất cả stream của user, ngoại trừ device hiện tại.
     * Dùng khi kick tất cả thiết bị khác.
     *
     * @return int Số lượng key đã xóa.
     */
    public function kickOtherStreams(int $userId, string $currentDeviceId): int
    {
        $keys = Redis::keys($this->buildPattern($userId));
        $deleted = 0;

        foreach ($keys as $key) {
            $bare = $this->stripRedisPrefix($key);
            $parts = explode(':', $bare, 3);
            $deviceId = $parts[2] ?? null;

            // Giữ lại key của thiết bị hiện tại
            if ($deviceId === $currentDeviceId) {
                continue;
            }

            Redis::del($bare);
            $deleted++;
        }

        return $deleted;
    }

    /**
     * Lấy danh sách thiết bị đang stream kèm device_name.
     *
     * @return array<string, string>  ['deviceId' => 'deviceName']
     */
    public function getActiveStreamsMap(int $userId): array
    {
        $keys = Redis::keys($this->buildPattern($userId));

        if (empty($keys)) {
            return [];
        }

        $map = [];
        foreach ($keys as $key) {
            $bare = $this->stripRedisPrefix($key);
            $parts = explode(':', $bare, 3);
            $deviceId = $parts[2] ?? null;

            if ($deviceId) {
                $map[$deviceId] = Redis::get($bare) ?: $deviceId;
            }
        }

        return $map;
    }

    // ----------------------------------------------------------------
    // Helpers
    // ----------------------------------------------------------------

    /**
     * Xóa prefix mà Redis driver tự thêm vào key khi dùng phpredis.
     * Ví dụ: "laravel_database_stream:1:abc" → "stream:1:abc"
     */
    private function stripRedisPrefix(string $key): string
    {
        $prefix = config('database.redis.options.prefix', '');
        if ($prefix && str_starts_with($key, $prefix)) {
            return substr($key, strlen($prefix));
        }
        return $key;
    }

    /** Expose TTL constant để controller/test có thể đọc. */
    public function getTtl(): int
    {
        return self::STREAM_TTL;
    }
}
