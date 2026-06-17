<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ModeratorController;
use App\Http\Controllers\CounterStaffController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\Staff\StaffShowtimeController;
use App\Http\Controllers\Staff\StaffTicketController;
use App\Http\Controllers\Staff\FoodItemController;

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

Route::get('/old', function () {
    return view('welcome');
});

// Tin tức
Route::prefix('tin-tuc')->name('news.')->group(function () {
    Route::get('/', [NewsController::class, 'index'])->name('index');
    Route::get('/{slug}', [NewsController::class, 'show'])->name('show');
});

// Staff
Route::prefix('staff')->name('staff.')->middleware('auth')->group(function () {
    // Suất chiếu
    Route::resource('showtimes', StaffShowtimeController::class)
        ->except(['show']);

    // Vé
    Route::get('tickets', [StaffTicketController::class, 'index'])->name('tickets.index');
    Route::get('tickets/scan', [StaffTicketController::class, 'scan'])->name('tickets.scan');
    Route::post('tickets/checkin', [StaffTicketController::class, 'checkIn'])->name('tickets.checkin');

    // Combo & Đồ ăn
    Route::resource('food', FoodItemController::class)->parameters(['food' => 'food']);
    Route::patch('food/{food}/toggle', [FoodItemController::class, 'toggle'])->name('food.toggle');
});
