<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// Routes cũ (PHP thuần) - giữ lại để chuyển đổi dần
// Bạn có thể xóa dần khi đã chuyển đổi xong
Route::get('/old', function () {
    return view('welcome');
});
