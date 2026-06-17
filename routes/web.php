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
Route::get('/search', [HomeController::class, 'search'])->name('search');

Route::get('/old', function () {
    return view('welcome');
});

// ==================== MODERATOR ROUTES ====================
Route::middleware(['auth', 'moderator'])->prefix('moderator')->name('moderator.')->group(function () {
    Route::get('/', [ModeratorController::class, 'index'])->name('index');
    
    // Showtimes Management
    Route::prefix('showtimes')->name('showtimes.')->group(function () {
        Route::get('/', [ModeratorController::class, 'showtimes'])->name('index');
        Route::get('/create', [ModeratorController::class, 'showtimesCreate'])->name('create');
        Route::post('/', [ModeratorController::class, 'showtimesStore'])->name('store');
        Route::get('/{id}/edit', [ModeratorController::class, 'showtimesEdit'])->name('edit');
        Route::put('/{id}', [ModeratorController::class, 'showtimesUpdate'])->name('update');
        Route::delete('/{id}', [ModeratorController::class, 'showtimesDelete'])->name('destroy');
    });
    
    // Screens Management
    Route::prefix('screens')->name('screens.')->group(function () {
        Route::get('/', [ModeratorController::class, 'screens'])->name('index');
        Route::get('/create', [ModeratorController::class, 'screensCreate'])->name('create');
        Route::post('/', [ModeratorController::class, 'screensStore'])->name('store');
        Route::get('/{id}/edit', [ModeratorController::class, 'screensEdit'])->name('edit');
        Route::put('/{id}', [ModeratorController::class, 'screensUpdate'])->name('update');
        Route::delete('/{id}', [ModeratorController::class, 'screensDelete'])->name('destroy');
    });
    
    // Theater Management
    Route::get('/theater', [ModeratorController::class, 'theater'])->name('theater');
    Route::put('/theater', [ModeratorController::class, 'theaterUpdate'])->name('theater.update');
    
    // Tickets
    Route::get('/tickets', [ModeratorController::class, 'tickets'])->name('tickets');
    
    // Counter Staff Management
    Route::prefix('counter-staff')->name('counterStaff.')->group(function () {
        Route::get('/', [ModeratorController::class, 'counterStaff'])->name('index');
        Route::post('/', [ModeratorController::class, 'counterStaffStore'])->name('store');
        Route::put('/{id}', [ModeratorController::class, 'counterStaffUpdate'])->name('update');
        Route::delete('/{id}', [ModeratorController::class, 'counterStaffDelete'])->name('destroy');
    });
    Route::get('/counter-staff', [ModeratorController::class, 'counterStaff'])->name('counterStaff');
    
    // Food Items Management
    Route::prefix('food-items')->name('foodItems.')->group(function () {
        Route::get('/', [ModeratorController::class, 'foodItems'])->name('index');
        Route::post('/', [ModeratorController::class, 'foodItemsStore'])->name('store');
        Route::put('/{id}', [ModeratorController::class, 'foodItemsUpdate'])->name('update');
        Route::delete('/{id}', [ModeratorController::class, 'foodItemsDelete'])->name('destroy');
    });
    Route::get('/food-items', [ModeratorController::class, 'foodItems'])->name('foodItems');
    
    // Statistics
    Route::get('/statistics', [ModeratorController::class, 'statistics'])->name('statistics');
    
    // Permission Requests
    Route::get('/permission-requests', [ModeratorController::class, 'permissionRequests'])->name('permissionRequests');
    Route::post('/permission-requests/handle', [ModeratorController::class, 'handlePermissionRequest'])->name('permissionRequests.handle');
    
    // API - Get available time slots
    Route::get('/api/available-time-slots', [ModeratorController::class, 'getAvailableTimeSlots'])->name('api.availableTimeSlots');
});

// ==================== COUNTER STAFF ROUTES ====================
Route::middleware(['auth', 'counter_staff'])->prefix('counter')->name('counter.')->group(function () {
    Route::get('/', [CounterStaffController::class, 'index'])->name('index');
    
    // QR Code Scanning
    Route::get('/scan-qr', [CounterStaffController::class, 'scanQR'])->name('scanQR');
    Route::post('/verify-ticket', [CounterStaffController::class, 'verifyTicket'])->name('verifyTicket');
    Route::get('/scanned-tickets', [CounterStaffController::class, 'scannedTickets'])->name('scannedTickets');
    
    // Sell Tickets at Counter
    Route::get('/sell-ticket', [CounterStaffController::class, 'sellTicket'])->name('sellTicket');
    Route::post('/process-sale', [CounterStaffController::class, 'processSale'])->name('processSale');
    Route::get('/sales-history', [CounterStaffController::class, 'salesHistory'])->name('salesHistory');
    
    // Showtimes
    Route::get('/showtimes', [CounterStaffController::class, 'showtimes'])->name('showtimes');
});

// ==================== NEWS ROUTES ====================
Route::prefix('news')->name('news.')->group(function () {
    Route::get('/', [NewsController::class, 'index'])->name('index');
    Route::get('/category/{categoryId}', [NewsController::class, 'category'])->name('category');
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
