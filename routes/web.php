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

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth Routes
Route::match(['get', 'post'], '/login', [AuthController::class, 'login'])->name('login');
Route::match(['get', 'post'], '/register', [AuthController::class, 'register'])->name('register');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/logout-all', [AuthController::class, 'logoutAll'])->name('logoutAll');

// Movies
Route::get('/movies', [MovieController::class, 'index'])->name('movies.index');
Route::get('/movies/{id}/watch', [MovieController::class, 'watch'])->name('movies.watch');
Route::post('/movies/favorite', [MovieController::class, 'toggleFavorite'])->name('movies.favorite');

// User Protected Routes
Route::middleware(['auth'])->group(function () {
    // Profile
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'index'])->name('index');
        Route::post('/update', [ProfileController::class, 'update'])->name('update');
        Route::post('/avatar', [ProfileController::class, 'uploadAvatar'])->name('avatar');
        Route::post('/subscription', [ProfileController::class, 'upgradeSubscription'])->name('subscription');
        Route::post('/deposit', [ProfileController::class, 'depositVnpay'])->name('deposit');
        Route::get('/deposit/return', [ProfileController::class, 'vnpayDepositReturn'])->name('deposit.return');
    });

    // Booking
    Route::prefix('booking')->name('booking.')->group(function () {
        Route::get('/', [BookingController::class, 'index'])->name('index');
        Route::post('/process', [BookingController::class, 'processBooking'])->name('process');
        Route::get('/vnpay-return', [BookingController::class, 'vnpayReturn'])->name('vnpay-return');
        Route::get('/my-tickets', [BookingController::class, 'myTickets'])->name('my-tickets');
        Route::get('/ticket/{id}', [BookingController::class, 'viewTicket'])->name('ticket');
    });

    // Notifications
    Route::prefix('notifications')->name('notifications.')->group(function () {
        Route::get('/', [NotificationController::class, 'index'])->name('index');
        Route::get('/list', [NotificationController::class, 'getNotifications'])->name('list');
        Route::get('/count', [NotificationController::class, 'getUnreadCount'])->name('count');
        Route::post('/read', [NotificationController::class, 'markAsRead'])->name('read');
        Route::delete('/{id}', [NotificationController::class, 'delete'])->name('delete');
    });

    // Reviews & Comments
    Route::prefix('review')->name('review.')->group(function () {
        Route::post('/comment', [ReviewController::class, 'comment'])->name('comment');
        Route::post('/comment/like', [ReviewController::class, 'likeComment'])->name('comment.like');
        Route::delete('/comment/{id}', [ReviewController::class, 'deleteComment'])->name('comment.delete');
        Route::post('/create', [ReviewController::class, 'create'])->name('create');
        Route::delete('/{id}', [ReviewController::class, 'delete'])->name('delete');
        Route::post('/{id}/pin', [ReviewController::class, 'pin'])->name('pin');
    });
});

// Admin Routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
    Route::get('/tickets', [AdminController::class, 'tickets'])->name('tickets');
    Route::get('/logs', [AdminController::class, 'logs'])->name('logs');

    // Users
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminController::class, 'users'])->name('index');
        Route::post('/points', [AdminController::class, 'usersUpdatePoints'])->name('points');
        Route::post('/role', [AdminController::class, 'usersUpdateRole'])->name('role');
        Route::post('/status', [AdminController::class, 'usersToggleStatus'])->name('status');
    });

    // Movies
    Route::prefix('movies')->name('movies.')->group(function () {
        Route::get('/', [AdminController::class, 'movies'])->name('index');
        Route::get('/create', [AdminController::class, 'moviesCreate'])->name('create');
        Route::post('/store', [AdminController::class, 'moviesStore'])->name('store');
        Route::get('/{id}/edit', [AdminController::class, 'moviesEdit'])->name('edit');
        Route::match(['put', 'post'], '/{id}', [AdminController::class, 'moviesUpdate'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'moviesDelete'])->name('delete');
    });

    // Theaters
    Route::prefix('theaters')->name('theaters.')->group(function () {
        Route::get('/', [AdminController::class, 'theaters'])->name('index');
        Route::get('/create', [AdminController::class, 'theatersCreate'])->name('create');
        Route::post('/store', [AdminController::class, 'theatersStore'])->name('store');
        Route::get('/{id}/edit', [AdminController::class, 'theatersEdit'])->name('edit');
        Route::match(['put', 'post'], '/{id}', [AdminController::class, 'theatersUpdate'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'theatersDelete'])->name('delete');
    });

    // Food Items
    Route::prefix('food-items')->name('food-items.')->group(function () {
        Route::get('/', [AdminController::class, 'foodItems'])->name('index');
        Route::get('/create', [AdminController::class, 'foodItemsCreate'])->name('create');
        Route::post('/store', [AdminController::class, 'foodItemsStore'])->name('store');
        Route::match(['put', 'post'], '/{id}', [AdminController::class, 'foodItemsUpdate'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'foodItemsDelete'])->name('delete');
    });

    // Categories
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [AdminController::class, 'categories'])->name('index');
        Route::post('/store', [AdminController::class, 'categoriesStore'])->name('store');
        Route::match(['put', 'post'], '/{id}', [AdminController::class, 'categoriesUpdate'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'categoriesDelete'])->name('delete');
    });
});

// Moderator Routes
Route::middleware(['auth'])->prefix('moderator')->name('moderator.')->group(function () {
    Route::get('/', [ModeratorController::class, 'index'])->name('index');
    Route::get('/tickets', [ModeratorController::class, 'tickets'])->name('tickets');
    Route::get('/statistics', [ModeratorController::class, 'statistics'])->name('statistics');
    
    // Permission Requests
    Route::get('/permission-requests', [ModeratorController::class, 'permissionRequests'])->name('permission-requests.index');
    Route::post('/permission-requests/handle', [ModeratorController::class, 'handlePermissionRequest'])->name('permission-requests.handle');
    
    // Showtimes
    Route::prefix('showtimes')->name('showtimes.')->group(function () {
        Route::get('/', [ModeratorController::class, 'showtimes'])->name('index');
        Route::post('/store', [ModeratorController::class, 'showtimesStore'])->name('store');
        Route::match(['put', 'post'], '/{id}', [ModeratorController::class, 'showtimesUpdate'])->name('update');
        Route::delete('/{id}', [ModeratorController::class, 'showtimesDelete'])->name('delete');
    });
    
    // Screens
    Route::prefix('screens')->name('screens.')->group(function () {
        Route::get('/', [ModeratorController::class, 'screens'])->name('index');
        Route::post('/store', [ModeratorController::class, 'screensStore'])->name('store');
    });
    
    // Theater
    Route::prefix('theater')->name('theater.')->group(function () {
        Route::get('/', [ModeratorController::class, 'theater'])->name('index');
        Route::post('/update', [ModeratorController::class, 'theaterUpdate'])->name('update');
    });
});

// Counter Staff Routes
Route::middleware(['auth'])->prefix('staff')->name('staff.')->group(function () {
    Route::get('/scan-qr', [CounterStaffController::class, 'scanQR'])->name('scan-qr');
    Route::match(['get', 'post'], '/verify-ticket', [CounterStaffController::class, 'verifyTicket'])->name('verify-ticket');
    Route::get('/scanned-tickets', [CounterStaffController::class, 'scannedTickets'])->name('scanned-tickets');
    Route::get('/showtimes', [CounterStaffController::class, 'showtimes'])->name('showtimes');
    Route::get('/sell-ticket', [CounterStaffController::class, 'sellTicket'])->name('sell-ticket');
    Route::post('/process-sale', [CounterStaffController::class, 'processSale'])->name('process-sale');
    Route::get('/sales-history', [CounterStaffController::class, 'salesHistory'])->name('sales-history');
});

// Old route fallback for smooth transition
Route::get('/old', function () {
    return view('welcome');
});
