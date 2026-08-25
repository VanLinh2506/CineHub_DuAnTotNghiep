<?php

namespace App\Http\Controllers;

use App\Services\StreamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * VideoStreamController
 *
 * Xử lý vòng đời stream video:
 *   POST /api/video/play       — Xin phép mở luồng phát
 *   POST /api/video/heartbeat  — Gia hạn luồng đang phát (gọi mỗi 30s)
 *   POST /api/video/stop       — Chủ động kết thúc luồng phát
 *
 * Tất cả route đều được bảo vệ bởi middleware auth:sanctum.
 * StreamService được inject qua constructor (Dependency Injection).
 */
class VideoStreamController extends Controller
{
    public function __construct(private readonly StreamService $streamService)
    {
    }

    // ----------------------------------------------------------------
    // POST /api/video/play
    // ----------------------------------------------------------------

    /**
     * Xin phép bắt đầu phát video.
     *
     * Body JSON:
     *   device_id   string  required  — ID định danh thiết bị (UUID do client sinh)
     *   device_name string  optional  — Tên hiển thị (vd: "Chrome / Windows")
     *
     * Response 200: { allowed: true,  ttl: 60 }
     * Response 403: { allowed: false, message: "...", active_devices: [...] }
     */
    public function play(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id'   => ['required', 'string', 'max:128'],
            'device_name' => ['nullable', 'string', 'max:200'],
        ]);

        /** @var \App\Models\User $user */
        $user      = $request->user();
        $deviceId  = $validated['device_id'];
        $deviceName = $validated['device_name']
            ?? $this->guessDeviceName($request->userAgent() ?? '');

        // Thử đăng ký stream; StreamService kiểm tra giới hạn bên trong
        $allowed = $this->streamService->startStream(
            $user->id,
            $deviceId,
            $user->max_devices,
            $deviceName
        );

        if (!$allowed) {
            // Trả về danh sách thiết bị đang chiếm luồng để FE hiện thông báo rõ
            $activeIds = $this->streamService->getActiveDeviceIds($user->id);

            return response()->json([
                'allowed'        => false,
                'message'        => "Tài khoản đã đạt giới hạn {$user->max_devices} thiết bị stream đồng thời.",
                'active_devices' => $activeIds,
            ], 403);
        }

        return response()->json([
            'allowed' => true,
            'ttl'     => $this->streamService->getTtl(),
        ]);
    }

    // ----------------------------------------------------------------
    // POST /api/video/heartbeat
    // ----------------------------------------------------------------

    /**
     * Gia hạn luồng đang phát thêm TTL giây.
     * Frontend gọi silent mỗi 30s khi video đang play.
     *
     * Body JSON:
     *   device_id  string  required
     *
     * Response 200: { renewed: true }
     * Response 403: { renewed: false, message: "..." }
     *   → FE nhận 403 = stream đã bị thu hồi (bị kick hoặc TTL hết) → dừng video.
     */
    public function heartbeat(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => ['required', 'string', 'max:128'],
        ]);

        /** @var \App\Models\User $user */
        $user     = $request->user();
        $renewed  = $this->streamService->heartbeat($user->id, $validated['device_id']);

        if (!$renewed) {
            // Key đã biến mất — có thể bị kick hoặc hết TTL mà FE bị delay
            return response()->json([
                'renewed' => false,
                'message' => 'Luồng phát đã hết hạn hoặc bị thu hồi. Vui lòng tải lại trang.',
            ], 403);
        }

        return response()->json(['renewed' => true]);
    }

    // ----------------------------------------------------------------
    // POST /api/video/stop
    // ----------------------------------------------------------------

    /**
     * Chủ động giải phóng luồng khi người dùng thoát / đóng tab.
     * Không bắt buộc — TTL sẽ tự dọn nếu client không gọi được.
     *
     * Body JSON:
     *   device_id  string  required
     */
    public function stop(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => ['required', 'string', 'max:128'],
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();
        $this->streamService->stopStream($user->id, $validated['device_id']);

        return response()->json(['stopped' => true]);
    }

    // ----------------------------------------------------------------
    // Helper
    // ----------------------------------------------------------------

    /**
     * Rút gọn User-Agent thành tên thiết bị dễ đọc.
     * Ví dụ: "Mozilla/5.0 (Windows NT 10.0...) Chrome/..." → "Chrome / Windows"
     */
    private function guessDeviceName(string $userAgent): string
    {
        $browser = 'Unknown Browser';
        $os      = 'Unknown OS';

        // Nhận diện browser phổ biến
        $browsers = [
            'Edg'     => 'Edge',
            'OPR'     => 'Opera',
            'Chrome'  => 'Chrome',
            'Firefox' => 'Firefox',
            'Safari'  => 'Safari',
        ];
        foreach ($browsers as $token => $name) {
            if (str_contains($userAgent, $token)) {
                $browser = $name;
                break;
            }
        }

        // Nhận diện OS
        $platforms = [
            'Windows'   => 'Windows',
            'Macintosh' => 'macOS',
            'Linux'     => 'Linux',
            'Android'   => 'Android',
            'iPhone'    => 'iOS',
            'iPad'      => 'iPadOS',
        ];
        foreach ($platforms as $token => $name) {
            if (str_contains($userAgent, $token)) {
                $os = $name;
                break;
            }
        }

        return "{$browser} / {$os}";
    }
}
