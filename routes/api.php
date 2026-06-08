<?php

use App\Http\Controllers\TicketQrController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    // QR cho toàn booking
    Route::get('/bookings/{bookingId}/qr', [TicketQrController::class, 'bookingQr']);

    // QR từng vé riêng lẻ
    Route::get('/tickets/{ticketId}/qr', [TicketQrController::class, 'ticketQr']);

    // QR tất cả vé trong booking
    Route::get('/bookings/{bookingId}/tickets/qr', [TicketQrController::class, 'allTicketsQr']);
});
