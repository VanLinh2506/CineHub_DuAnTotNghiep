<?php

use App\Http\Controllers\DeviceController;
use App\Http\Controllers\TicketQrController;
use App\Http\Controllers\VideoStreamController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

// ============================================================
// PUBLIC — Lấy Sanctum token (đăng nhập API)
// ============================================================

/**
 * POST /api/auth/token
 *
 * Cấp Sanctum token để dùng cho các API stream & device.
 * Client gửi: { email, password, device_id, device_name? }
 *
 * Lưu ý: Flow đăng nhập chính của app vẫn dùng session + OTP.
 * Route này dành riêng cho các client cần Bearer token
 * (ví dụ: mobile app, SPA tách biệt, VideoPlayer component).
 */
Route::post('/auth/token', function (Request $request) {
    $request->validate([
        'email'       => ['required', 'email'],
        'password'    => ['required', 'string'],
        'device_id'   => ['required', 'string', 'max:128'],
        'device_name' => ['nullable', 'string', 'max:200'],
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        throw ValidationException::withMessages([
            'email' => ['Thông tin xác thực không chính xác.'],
        ]);
    }

    if (isset($user->is_active) && !$user->is_active) {
        throw ValidationException::withMessages([
            'email' => ['Tài khoản của bạn đã bị khóa.'],
        ]);
    }

    // Tên token = device_name (hiển thị trong DeviceManager)
    // hoặc fallback về device_id nếu không truyền device_name
    $tokenName = $request->device_name ?: $request->device_id;

    $token = $user->createToken($tokenName);

    return response()->json([
        'token'      => $token->plainTextToken,
        'token_type' => 'Bearer',
        'user'       => [
            'id'          => $user->id,
            'name'        => $user->name,
            'email'       => $user->email,
            'max_devices' => $user->max_devices,
        ],
    ]);
});

// ============================================================
// PROTECTED — Yêu cầu Bearer token từ Sanctum
// ============================================================

Route::middleware('auth:sanctum')->group(function () {

    // ----------------------------------------------------------
    // Thông tin user hiện tại
    // ----------------------------------------------------------
    Route::get('/user', function (Request $request) {
        return response()->json($request->user()->only([
            'id', 'name', 'email', 'max_devices', 'subscription_id',
        ]));
    });

    // ----------------------------------------------------------
    // Stream video — giới hạn thiết bị đồng thời
    // ----------------------------------------------------------
    Route::prefix('video')->name('api.video.')->group(function () {
        // Xin phép bắt đầu stream; trả 403 nếu vượt giới hạn max_devices
        Route::post('/play',      [VideoStreamController::class, 'play'])->name('play');
        // Gia hạn TTL stream (gọi mỗi 30s); trả 403 nếu bị kick
        Route::post('/heartbeat', [VideoStreamController::class, 'heartbeat'])->name('heartbeat');
        // Giải phóng luồng chủ động khi thoát
        Route::post('/stop',      [VideoStreamController::class, 'stop'])->name('stop');
    });

    // ----------------------------------------------------------
    // Quản lý thiết bị đăng nhập
    // ----------------------------------------------------------
    Route::prefix('devices')->name('api.devices.')->group(function () {
        // Danh sách thiết bị đang đăng nhập + cờ is_streaming
        Route::get('/',            [DeviceController::class, 'index'])->name('index');
        // Đăng xuất & dừng stream tất cả thiết bị khác
        Route::post('/kick-other', [DeviceController::class, 'kickOther'])->name('kick-other');
        // Đăng xuất chính thiết bị đang dùng
        Route::post('/logout',     [DeviceController::class, 'logout'])->name('logout');
    });

    // ----------------------------------------------------------
    // QR vé xem phim (giữ nguyên từ trước)
    // ----------------------------------------------------------
    Route::get('/bookings/{bookingId}/qr',          [TicketQrController::class, 'bookingQr']);
    Route::get('/tickets/{ticketId}/qr',            [TicketQrController::class, 'ticketQr']);
    Route::get('/bookings/{bookingId}/tickets/qr',  [TicketQrController::class, 'allTicketsQr']);
});

