<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    HomeController,
    AuthController,
    MovieController,
    BookingController,
    ProfileController,
    NotificationController,
    ReviewController,
    AdminController,
    ModeratorController,
    CounterStaffController,
    NewsController
};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Home
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeController::class, 'search'])->name('search');

// ==================== AUTH ROUTES ====================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    
    // Google OAuth routes
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// ==================== MOVIES ROUTES ====================
Route::prefix('movies')->name('movies.')->group(function () {
    Route::get('/', [MovieController::class, 'index'])->name('index');
    Route::get('/theater', [MovieController::class, 'theater'])->name('theater');
    Route::get('/online', [MovieController::class, 'online'])->name('online');
    Route::get('/phim-le', [MovieController::class, 'phimLe'])->name('phimle');
    Route::get('/phim-bo', [MovieController::class, 'phimBo'])->name('phimbo');
    Route::get('/category/{id}', [MovieController::class, 'category'])->name('category');
    Route::get('/{id}', [MovieController::class, 'show'])->name('show');
    
    // Watch movie (requires auth)
    Route::middleware('auth')->group(function () {
        Route::get('/{id}/watch', [MovieController::class, 'watch'])->name('watch');
        Route::get('/{movieId}/episode/{episodeNumber}', [MovieController::class, 'watchEpisode'])->name('watchEpisode');
    });
});

// ==================== BOOKING ROUTES ====================
// Public booking page (browsing movies and theaters)
Route::get('/booking', [BookingController::class, 'index'])->name('booking.index');

// API route for getting showtimes (no auth required for browsing)
Route::get('/api/booking/showtimes', [BookingController::class, 'getShowtimesByDate'])->name('api.booking.showtimes');

// API route for getting seat map
Route::get('/api/booking/seat-map', [BookingController::class, 'getSeatMap'])->name('api.booking.seatMap');

Route::middleware('auth')->prefix('booking')->name('booking.')->group(function () {
    Route::post('/process', [BookingController::class, 'processBooking'])->name('processBooking');
    Route::get('/showtime/{showtimeId}', [BookingController::class, 'selectSeats'])->name('selectSeats');
    Route::post('/create', [BookingController::class, 'create'])->name('create');
    Route::get('/{bookingId}/payment', [BookingController::class, 'payment'])->name('payment');
    Route::get('/{bookingId}/confirmation', [BookingController::class, 'confirmation'])->name('confirmation');
    Route::post('/{bookingId}/cancel', [BookingController::class, 'cancel'])->name('cancel');
    Route::get('/history', [BookingController::class, 'myTickets'])->name('history');
    Route::get('/my-tickets', [BookingController::class, 'myTickets'])->name('my-tickets');
});

// ==================== PAYMENT ROUTES ====================
Route::prefix('payment')->name('payment.')->group(function () {
    Route::get('/vnpay/callback', [BookingController::class, 'vnpayCallback'])->name('vnpay.callback');
    Route::post('/vnpay/create', [BookingController::class, 'createVnpayPayment'])->name('vnpay.create');
});

Route::get('/profile/vnpay-deposit-return', [ProfileController::class, 'handleVnpayDepositReturn'])
    ->name('profile.vnpay-deposit-return');

// ==================== PROFILE ROUTES ====================
Route::middleware('auth')->prefix('profile')->name('profile.')->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('index');
    Route::put('/update', [ProfileController::class, 'update'])->name('update');
    Route::put('/update-password', [ProfileController::class, 'updatePassword'])->name('updatePassword');
    Route::post('/upload-avatar', [ProfileController::class, 'uploadAvatar'])->name('uploadAvatar');
    Route::post('/deposit-vnpay', [ProfileController::class, 'startVnpayDeposit'])->name('depositVnpay');
    Route::get('/bookings', [ProfileController::class, 'bookings'])->name('bookings');
    Route::get('/watch-history', [ProfileController::class, 'watchHistory'])->name('watchHistory');
    Route::get('/subscriptions', [ProfileController::class, 'subscriptions'])->name('subscriptions');
});

// ==================== NOTIFICATIONS ====================
Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::post('/{id}/read', [NotificationController::class, 'markAsRead'])->name('markAsRead');
    Route::post('/read-all', [NotificationController::class, 'markAllAsRead'])->name('markAllAsRead');
});

// ==================== REVIEWS ====================
Route::middleware('auth')->prefix('reviews')->name('reviews.')->group(function () {
    Route::post('/', [ReviewController::class, 'store'])->name('store');
    Route::put('/{id}', [ReviewController::class, 'update'])->name('update');
    Route::delete('/{id}', [ReviewController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/like', [ReviewController::class, 'like'])->name('like');
});

// ==================== ADMIN ROUTES ====================
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'index'])->name('index');
    Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
    
    // Users Management
    Route::prefix('users')->name('users.')->group(function () {
        Route::get('/', [AdminController::class, 'users'])->name('index');
        Route::post('/update-points', [AdminController::class, 'usersUpdatePoints'])->name('updatePoints');
        Route::post('/update-role', [AdminController::class, 'usersUpdateRole'])->name('updateRole');
        Route::post('/toggle-status', [AdminController::class, 'usersToggleStatus'])->name('toggleStatus');
    });
    
    // Movies Management
    Route::prefix('movies')->name('movies.')->group(function () {
        Route::get('/', [AdminController::class, 'movies'])->name('index');
        Route::get('/create', [AdminController::class, 'moviesCreate'])->name('create');
        Route::post('/', [AdminController::class, 'moviesStore'])->name('store');
        Route::get('/{id}/edit', [AdminController::class, 'moviesEdit'])->name('edit');
        Route::put('/{id}', [AdminController::class, 'moviesUpdate'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'moviesDelete'])->name('destroy');
        Route::delete('/{movieId}/episodes/{id}', [AdminController::class, 'moviesDeleteEpisode'])->name('deleteEpisode');
        Route::get('/scan-episodes', [AdminController::class, 'moviesScanEpisodes'])->name('scanEpisodes');
        Route::post('/import-episodes', [AdminController::class, 'moviesImportEpisodes'])->name('importEpisodes');
    });
    
    // Theaters Management
    Route::prefix('theaters')->name('theaters.')->group(function () {
        Route::get('/', [AdminController::class, 'theaters'])->name('index');
        Route::get('/create', [AdminController::class, 'theatersCreate'])->name('create');
        Route::post('/', [AdminController::class, 'theatersStore'])->name('store');
        Route::get('/{id}/edit', [AdminController::class, 'theatersEdit'])->name('edit');
        Route::put('/{id}', [AdminController::class, 'theatersUpdate'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'theatersDelete'])->name('destroy');
    });
    
    // Categories Management
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [AdminController::class, 'categories'])->name('index');
        Route::post('/', [AdminController::class, 'categoriesStore'])->name('store');
        Route::put('/{id}', [AdminController::class, 'categoriesUpdate'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'categoriesDelete'])->name('destroy');
    });
    
    // Tickets Management
    Route::prefix('tickets')->name('tickets.')->group(function () {
        Route::get('/', [AdminController::class, 'tickets'])->name('index');
        Route::post('/update-movie', [AdminController::class, 'ticketsUpdateMovie'])->name('updateMovie');
    });
    
    // Food Items Management
    Route::prefix('food-items')->name('foodItems.')->group(function () {
        Route::get('/', [AdminController::class, 'foodItems'])->name('index');
        Route::get('/create', [AdminController::class, 'foodItemsCreate'])->name('create');
        Route::post('/', [AdminController::class, 'foodItemsStore'])->name('store');
        Route::put('/{id}', [AdminController::class, 'foodItemsUpdate'])->name('update');
        Route::delete('/{id}', [AdminController::class, 'foodItemsDelete'])->name('destroy');
    });
    
    // Logs
    Route::get('/logs', [AdminController::class, 'logs'])->name('logs');
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

// Test route
Route::get('/old', function () {
    return view('welcome');
});
