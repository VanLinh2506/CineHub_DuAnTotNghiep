<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// Booking
Route::middleware('auth')->group(function () {
    Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');
    Route::post('/booking/process', [BookingController::class, 'processBooking'])->name('booking.process');
    Route::get('/booking/my-tickets', [BookingController::class, 'myTickets'])->name('booking.my-tickets');
    Route::get('/booking/ticket/{bookingId}', [BookingController::class, 'viewTicket'])->name('booking.view-ticket');
});

// VNPay callback (không cần auth)
Route::get('/booking/vnpay-return', [BookingController::class, 'vnpayReturn'])->name('booking.vnpay-return');

// Routes cũ (PHP thuần) - giữ lại để chuyển đổi dần
Route::get('/old', function () {
    return view('welcome');
});
