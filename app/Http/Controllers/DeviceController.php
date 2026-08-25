<?php

namespace App\Http\Controllers;

use App\Services\StreamService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Laravel\Sanctum\PersonalAccessToken;

/**
 * DeviceController
 *
 * Quản lý thiết bị đăng nhập và stream:
 *   GET  /api/devices            — Danh sách thiết bị đăng nhập + trạng thái stream
 *   POST /api/devices/kick-other — Đăng xuất & dừng stream tất cả thiết bị khác
 *
 * Tất cả route bảo vệ bởi auth:sanctum.
 */
class DeviceController extends Controller
{
    public function __construct(private readonly StreamService $streamService)
    {
    }

    // ----------------------------------------------------------------
    // GET /api/devices
    // ----------------------------------------------------------------

    /**
     * Trả về danh sách token (thiết bị đang đăng nhập) của user,
     * kèm cờ is_streaming cho mỗi thiết bị.
     *
     * Response shape:
     * {
     *   "current_token_id": 5,
     *   "devices": [
     *     {
     *       "token_id":     5,
     *       "device_name":  "Chrome / Windows",
     *       "last_used_at": "2026-08-25T10:30:00.000000Z",
     *       "created_at":   "2026-08-25T08:00:00.000000Z",
     *       "is_current":   true,
     *       "is_streaming": true
     *     },
     *     ...
     *   ]
     * }
     */
    public function index(Request $request): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();

        // Map deviceId → đang stream hay không
        $streamingDeviceIds = $this->streamService->getActiveDeviceIds($user->id);
        $streamingSet = array_flip($streamingDeviceIds); // O(1) lookup

        // Token hiện tại đang dùng để call API này
        $currentToken = $request->user()->currentAccessToken();
        $currentTokenId = $currentToken instanceof PersonalAccessToken
            ? $currentToken->id
            : null;

        // Lấy tất cả token của user, sắp xếp mới nhất trước
        $devices = $user->tokens()
            ->orderByDesc('last_used_at')
            ->get()
            ->map(function (PersonalAccessToken $token) use ($currentTokenId, $streamingSet) {
                // device_id lưu trong name của token (xem API login bên dưới)
                // Fallback: dùng token id để kiểm tra stream
                $deviceId = $token->name;

                return [
                    'token_id'     => $token->id,
                    'device_name'  => $token->name,
                    'last_used_at' => $token->last_used_at?->toIso8601String(),
                    'created_at'   => $token->created_at->toIso8601String(),
                    'is_current'   => $token->id === $currentTokenId,
                    // Thiết bị được coi là streaming nếu device_id của nó có trong Redis
                    'is_streaming' => isset($streamingSet[$deviceId]),
                ];
            });

        return response()->json([
            'current_token_id' => $currentTokenId,
            'devices'          => $devices,
        ]);
    }

    // ----------------------------------------------------------------
    // POST /api/devices/kick-other
    // ----------------------------------------------------------------

    /**
     * Đăng xuất + dừng stream tất cả thiết bị KHÁC thiết bị hiện tại.
     *
     * Body JSON:
     *   device_id  string  required  — deviceId của thiết bị đang gọi (giữ lại)
     *
     * Logic thực hiện 2 bước:
     *   1. Xóa tất cả key Redis stream của user, ngoại trừ device hiện tại.
     *   2. Thu hồi (delete) tất cả PersonalAccessToken, ngoại trừ token hiện tại.
     *      → Client bị kick sẽ nhận 401 ở request tiếp theo → FE tự redirect về Login.
     *
     * Response: { kicked_streams: 3, revoked_tokens: 2 }
     */
    public function kickOther(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => ['required', 'string', 'max:128'],
        ]);

        /** @var \App\Models\User $user */
        $user            = $request->user();
        $currentDeviceId = $validated['device_id'];

        // Bước 1: Dừng tất cả stream khác trong Redis
        $kickedStreams = $this->streamService->kickOtherStreams($user->id, $currentDeviceId);

        // Bước 2: Thu hồi token Sanctum của tất cả thiết bị khác
        $currentToken = $request->user()->currentAccessToken();
        $currentTokenId = $currentToken instanceof PersonalAccessToken
            ? $currentToken->id
            : null;

        $revokedCount = 0;

        if ($currentTokenId) {
            // Xóa tất cả token trừ token hiện tại
            $revokedCount = $user->tokens()
                ->where('id', '!=', $currentTokenId)
                ->delete();
        } else {
            // Fallback: không xác định được token hiện tại → không xóa gì để an toàn
        }

        return response()->json([
            'success'        => true,
            'kicked_streams' => $kickedStreams,
            'revoked_tokens' => $revokedCount,
            'message'        => "Đã đăng xuất {$revokedCount} thiết bị khác thành công.",
        ]);
    }

    // ----------------------------------------------------------------
    // POST /api/devices/logout  (tuỳ chọn: đăng xuất chính thiết bị đang dùng)
    // ----------------------------------------------------------------

    /**
     * Đăng xuất thiết bị hiện tại — thu hồi token + dừng stream.
     *
     * Body JSON:
     *   device_id  string  required
     */
    public function logout(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => ['required', 'string', 'max:128'],
        ]);

        /** @var \App\Models\User $user */
        $user = $request->user();

        // Dừng stream
        $this->streamService->stopStream($user->id, $validated['device_id']);

        // Thu hồi token hiện tại
        $request->user()->currentAccessToken()->delete();

        return response()->json(['logged_out' => true]);
    }
}
