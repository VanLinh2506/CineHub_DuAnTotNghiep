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

// ==================== HOME ====================
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'index'])->name('search');
Route::get('/old', fn() => view('welcome'));

// ==================== AUTH ====================
Route::get('/dang-nhap', [AuthController::class, 'showLogin'])->name('login');
Route::post('/dang-nhap', [AuthController::class, 'login'])->name('login.post');
Route::get('/dang-ky', [AuthController::class, 'showRegister'])->name('register');
Route::post('/dang-ky', [AuthController::class, 'register'])->name('register.post');
Route::post('/dang-xuat', [AuthController::class, 'logout'])->name('logout');
Route::get('/quen-mat-khau', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/quen-mat-khau', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');

// ==================== MOVIES ====================
Route::prefix('phim')->name('movies.')->group(function () {
    Route::get('/', [MovieController::class, 'index'])->name('index');
    Route::get('/phim-le', [MovieController::class, 'phimLe'])->name('phimle');
    Route::get('/phim-bo', [MovieController::class, 'phimBo'])->name('phimbo');
    Route::get('/online', [MovieController::class, 'online'])->name('online');
    Route::get('/rap', [MovieController::class, 'theater'])->name('theater');
    Route::get('/the-loai/{id}', [MovieController::class, 'category'])->name('category');
    Route::post('/toggle-favorite', [MovieController::class, 'toggleFavorite'])->name('toggleFavorite');
    Route::get('/{id}', [MovieController::class, 'show'])->name('show');
    Route::get('/{id}/xem', [MovieController::class, 'watch'])->name('watch');
    Route::get('/{movieId}/tap/{episodeNumber}', [MovieController::class, 'watchEpisode'])->name('watchEpisode');
});

// ==================== PROFILE ====================
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');
    Route::post('/profile/avatar', [ProfileController::class, 'uploadAvatar'])->name('profile.uploadAvatar');
    Route::post('/profile/deposit-vnpay', [ProfileController::class, 'startVnpayDeposit'])->name('profile.depositVnpay');
    Route::get('/profile/deposit-vnpay/return', [ProfileController::class, 'handleVnpayDepositReturn'])->name('profile.depositVnpay.return');
    Route::post('/profile/upgrade-subscription', [ProfileController::class, 'upgradeSubscription'])->name('profile.upgradeSubscription');
    Route::get('/profile/bookings', [ProfileController::class, 'bookings'])->name('profile.bookings');
    Route::get('/profile/watch-history', [ProfileController::class, 'watchHistory'])->name('profile.watchHistory');
    Route::get('/profile/subscriptions', [ProfileController::class, 'subscriptions'])->name('profile.subscriptions');
});

// ==================== BOOKING ====================
Route::middleware('auth')->group(function () {
    Route::get('/dat-ve', [BookingController::class, 'index'])->name('booking.index');
    Route::post('/dat-ve/process', [BookingController::class, 'processBooking'])->name('booking.process');
    Route::post('/dat-ve/process-booking', [BookingController::class, 'processBooking'])->name('booking.processBooking');
    Route::get('/dat-ve/ve-cua-toi', [BookingController::class, 'myTickets'])->name('booking.my-tickets');
    Route::get('/dat-ve/lich-su', [BookingController::class, 'myTickets'])->name('booking.history');
    Route::get('/dat-ve/xem-ve/{bookingId}', [BookingController::class, 'viewTicket'])->name('booking.view-ticket');
});
Route::get('/dat-ve/vnpay-return', [BookingController::class, 'vnpayReturn'])->name('booking.vnpay-return');

// Booking API (AJAX - không cần auth)
Route::get('/api/booking/showtimes', [BookingController::class, 'getShowtimesByDate'])->name('api.booking.showtimes');
Route::get('/api/booking/seat-map', [BookingController::class, 'getSeatMap'])->name('api.booking.seatMap');

// ==================== NOTIFICATIONS ====================
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::delete('/notifications/{id}', [NotificationController::class, 'delete'])->name('notifications.delete');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unreadCount');
    Route::get('/notifications/list', [NotificationController::class, 'getNotifications'])->name('notifications.list');
});

// ==================== REVIEWS ====================
Route::middleware('auth')->group(function () {
    Route::post('/reviews', [ReviewController::class, 'store'])->name('review.store');
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy'])->name('review.destroy');
});

// ==================== ADMIN ====================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/users', [AdminController::class, 'users'])->name('users.index');
    Route::post('/users/update-points', [AdminController::class, 'usersUpdatePoints'])->name('users.updatePoints');
    Route::post('/users/update-role', [AdminController::class, 'usersUpdateRole'])->name('users.updateRole');
    Route::post('/users/toggle-status', [AdminController::class, 'usersToggleStatus'])->name('users.toggleStatus');
    Route::get('/movies', [AdminController::class, 'movies'])->name('movies.index');
    Route::get('/movies', [AdminController::class, 'movies'])->name('movies');
    Route::get('/movies/create', [AdminController::class, 'moviesCreate'])->name('movies.create');
    Route::post('/movies', [AdminController::class, 'moviesStore'])->name('movies.store');
    Route::get('/movies/{id}/edit', [AdminController::class, 'moviesEdit'])->name('movies.edit');
    Route::post('/movies/update', [AdminController::class, 'moviesUpdate'])->name('movies.update');
    Route::delete('/movies/{id}', [AdminController::class, 'moviesDelete'])->name('movies.delete');
    Route::post('/movies/import-episodes', [AdminController::class, 'importEpisodes'])->name('movies.importEpisodes');
    Route::get('/movies/delete-episode', [AdminController::class, 'deleteEpisode'])->name('movies.delete-episode');
    Route::get('/categories', [AdminController::class, 'categories'])->name('categories.index');
    Route::post('/categories', [AdminController::class, 'categoriesStore'])->name('categories.store');
    Route::put('/categories/{id}', [AdminController::class, 'categoriesUpdate'])->name('categories.update');
    Route::delete('/categories/{id}', [AdminController::class, 'categoriesDelete'])->name('categories.delete');
    Route::get('/theaters', [AdminController::class, 'theaters'])->name('theaters.index');
    Route::get('/theaters/create', [AdminController::class, 'theatersCreate'])->name('theaters.create');
    Route::post('/theaters', [AdminController::class, 'theatersStore'])->name('theaters.store');
    Route::get('/theaters/{id}/edit', [AdminController::class, 'theatersEdit'])->name('theaters.edit');
    Route::put('/theaters/{id}', [AdminController::class, 'theatersUpdate'])->name('theaters.update');
    Route::delete('/theaters/{id}', [AdminController::class, 'theatersDelete'])->name('theaters.delete');
    Route::get('/tickets', [AdminController::class, 'tickets'])->name('tickets.index');
    Route::get('/food-items', [AdminController::class, 'foodItems'])->name('foodItems.index');
    Route::get('/food-items/create', [AdminController::class, 'foodItemsCreate'])->name('foodItems.create');
    Route::post('/food-items', [AdminController::class, 'foodItemsStore'])->name('foodItems.store');
    Route::put('/food-items/{id}', [AdminController::class, 'foodItemsUpdate'])->name('foodItems.update');
    Route::delete('/food-items/{id}', [AdminController::class, 'foodItemsDelete'])->name('foodItems.delete');
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
    Route::get('/logs', [AdminController::class, 'logs'])->name('logs');
});

// ==================== MODERATOR ====================
Route::middleware(['auth', 'moderator'])->prefix('moderator')->name('moderator.')->group(function () {
    Route::get('/', [ModeratorController::class, 'index'])->name('index');
    Route::prefix('showtimes')->name('showtimes.')->group(function () {
        Route::get('/', [ModeratorController::class, 'showtimes'])->name('index');
        Route::get('/create', [ModeratorController::class, 'showtimesCreate'])->name('create');
        Route::post('/', [ModeratorController::class, 'showtimesStore'])->name('store');
        Route::get('/{id}/edit', [ModeratorController::class, 'showtimesEdit'])->name('edit');
        Route::put('/{id}', [ModeratorController::class, 'showtimesUpdate'])->name('update');
        Route::delete('/{id}', [ModeratorController::class, 'showtimesDelete'])->name('destroy');
    });
    Route::prefix('screens')->name('screens.')->group(function () {
        Route::get('/', [ModeratorController::class, 'screens'])->name('index');
        Route::get('/create', [ModeratorController::class, 'screensCreate'])->name('create');
        Route::post('/', [ModeratorController::class, 'screensStore'])->name('store');
        Route::get('/{id}/edit', [ModeratorController::class, 'screensEdit'])->name('edit');
        Route::put('/{id}', [ModeratorController::class, 'screensUpdate'])->name('update');
        Route::delete('/{id}', [ModeratorController::class, 'screensDelete'])->name('destroy');
    });
    Route::get('/theater', [ModeratorController::class, 'theater'])->name('theater');
    Route::put('/theater', [ModeratorController::class, 'theaterUpdate'])->name('theater.update');
    Route::get('/tickets', [ModeratorController::class, 'tickets'])->name('tickets');
    Route::prefix('counter-staff')->name('counterStaff.')->group(function () {
        Route::get('/', [ModeratorController::class, 'counterStaff'])->name('index');
        Route::post('/', [ModeratorController::class, 'counterStaffStore'])->name('store');
        Route::put('/{id}', [ModeratorController::class, 'counterStaffUpdate'])->name('update');
        Route::delete('/{id}', [ModeratorController::class, 'counterStaffDelete'])->name('destroy');
    });
    Route::get('/counter-staff', [ModeratorController::class, 'counterStaff'])->name('counterStaff');
    Route::prefix('food-items')->name('foodItems.')->group(function () {
        Route::get('/', [ModeratorController::class, 'foodItems'])->name('index');
        Route::post('/', [ModeratorController::class, 'foodItemsStore'])->name('store');
        Route::put('/{id}', [ModeratorController::class, 'foodItemsUpdate'])->name('update');
        Route::delete('/{id}', [ModeratorController::class, 'foodItemsDelete'])->name('destroy');
    });
    Route::get('/food-items', [ModeratorController::class, 'foodItems'])->name('foodItems');
    Route::get('/statistics', [ModeratorController::class, 'statistics'])->name('statistics');
    Route::get('/permission-requests', [ModeratorController::class, 'permissionRequests'])->name('permissionRequests');
    Route::post('/permission-requests/handle', [ModeratorController::class, 'handlePermissionRequest'])->name('permissionRequests.handle');
    Route::get('/api/available-time-slots', [ModeratorController::class, 'getAvailableTimeSlots'])->name('api.availableTimeSlots');
});

// ==================== COUNTER STAFF ====================
Route::middleware(['auth', 'counter_staff'])->prefix('counter')->name('counter.')->group(function () {
    Route::get('/', [CounterStaffController::class, 'index'])->name('index');
    Route::get('/scan-qr', [CounterStaffController::class, 'scanQR'])->name('scanQR');
    Route::post('/verify-ticket', [CounterStaffController::class, 'verifyTicket'])->name('verifyTicket');
    Route::get('/scanned-tickets', [CounterStaffController::class, 'scannedTickets'])->name('scannedTickets');
    Route::get('/sell-ticket', [CounterStaffController::class, 'sellTicket'])->name('sellTicket');
    Route::post('/process-sale', [CounterStaffController::class, 'processSale'])->name('processSale');
    Route::get('/sales-history', [CounterStaffController::class, 'salesHistory'])->name('salesHistory');
    Route::get('/showtimes', [CounterStaffController::class, 'showtimes'])->name('showtimes');
});

// ==================== NEWS ====================
Route::prefix('news')->name('news.')->group(function () {
    Route::get('/', [NewsController::class, 'index'])->name('index');
    Route::get('/category/{categoryId}', [NewsController::class, 'category'])->name('category');
    Route::get('/{slug}', [NewsController::class, 'show'])->name('show');
});

// ==================== STAFF ====================
Route::prefix('staff')->name('staff.')->middleware('auth')->group(function () {
    Route::resource('showtimes', StaffShowtimeController::class)->except(['show']);
    Route::get('tickets', [StaffTicketController::class, 'index'])->name('tickets.index');
    Route::get('tickets/scan', [StaffTicketController::class, 'scan'])->name('tickets.scan');
    Route::post('tickets/checkin', [StaffTicketController::class, 'checkIn'])->name('tickets.checkin');
    Route::resource('food', FoodItemController::class)->parameters(['food' => 'food']);
    Route::patch('food/{food}/toggle', [FoodItemController::class, 'toggle'])->name('food.toggle');
});
