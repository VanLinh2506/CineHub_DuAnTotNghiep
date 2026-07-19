<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware để tương thích với routing cũ (PHP thuần)
 * Chuyển đổi index.php?route=controller/action sang Laravel routes
 */
class LegacyRouteMiddleware
{
    /**
     * Route mapping từ PHP thuần cũ sang Laravel routes
     * Format: 'controller/action' => 'laravel/route/path'
     */
    private array $routeMap = [
        // ==================== HOME ====================
        'home/index' => '/',
        '' => '/', // index.php without params
        
        // ==================== MOVIES ====================
        'movie/index' => '/movies',
        'movie/detail' => '/movies/{id}',
        'movie/watch' => '/movies/{id}/watch',
        'movie/watchEpisode' => '/movies/{movieId}/episode/{episodeNumber}',
        'movie/trailer' => '/movies/{id}#trailer',
        'movie/theater' => '/movies/theater',
        'movie/online' => '/movies/online',
        'movie/phimle' => '/movies/phim-le',
        'movie/phimbo' => '/movies/phim-bo',
        'movie/category' => '/movies/category/{categoryId}',
        
        // ==================== AUTH ====================
        'auth/login' => '/login',
        'auth/register' => '/register',
        'auth/logout' => '/logout',
        'auth/forgotPassword' => '/forgot-password',
        'auth/resetPassword' => '/forgot-password',
        
        // ==================== BOOKING ====================
        'booking/index' => '/booking',
        'booking/selectShowtime' => '/booking',
        'booking/selectSeats' => '/booking/showtime/{showtimeId}',
        'booking/selectFood' => '/booking/showtime/{showtimeId}',
        'booking/payment' => '/booking/{bookingId}/payment',
        'booking/success' => '/booking/{bookingId}/confirmation',
        'booking/confirmation' => '/booking/{bookingId}/confirmation',
        'booking/history' => '/booking/history',
        'booking/ticketDetail' => '/booking/history',
        'booking/api/showtimes' => '/api/booking/showtimes',
        
        // ==================== PROFILE ====================
        'profile/index' => '/profile',
        'profile/edit' => '/profile',
        'profile/bookingHistory' => '/profile/bookings',
        'profile/watchHistory' => '/profile/watch-history',
        'profile/points' => '/profile',
        'profile/changePassword' => '/profile',
        
        // ==================== REVIEWS ====================
        'review/create' => '/movies/{movieId}#reviews',
        'review/edit' => '/movies/{movieId}#reviews',
        'review/delete' => '/movies/{movieId}#reviews',
        
        // ==================== NOTIFICATIONS ====================
        'notification/index' => '/notifications',
        'notification/markAsRead' => '/notifications',
        
        // ==================== ADMIN ====================
        'admin/dashboard' => '/admin',
        'admin/index' => '/admin',
        'admin/analytics' => '/admin/analytics',
        'admin/users' => '/admin/users',
        'admin/movies' => '/admin/movies',
        'admin/categories' => '/admin/categories',
        'admin/theaters' => '/admin/theaters',
        'admin/rooms' => '/admin/theaters',
        'admin/seats' => '/admin/theaters',
        'admin/showtimes' => '/moderator/showtimes',
        'admin/foodItems' => '/admin/food-items',
        'admin/foodItemsCreate' => '/admin/food-items/create',
        'admin/foodItemsEdit' => '/admin/food-items/{id}/edit',
        'admin/foodItemsUpdate' => '/admin/food-items/{id}',
        'admin/foodItemsDelete' => '/admin/food-items/{id}',
        'admin/bookings' => '/admin/tickets',
        'admin/transactions' => '/admin/tickets',
        'admin/reviews' => '/admin/movies',
        'admin/logs' => '/admin/logs',
        
        // Admin CRUD
        'admin/createMovie' => '/admin/movies/create',
        'admin/editMovie' => '/admin/movies/{id}/edit',
        'admin/movies/create' => '/admin/movies/create',
        'admin/movies/scanEpisodes' => '/admin/movies/scan-episodes',
        'admin/movies/importEpisodes' => '/admin/movies/import-episodes',
        'admin/movies/edit' => '/admin/movies/{id}/edit',
        'admin/movies/view' => '/movies/{id}',
        'admin/movies/delete' => '/admin/movies/{id}',
        'admin/movies/delete-episode' => '/admin/movies/{movie_id}/episodes/{id}',
        'admin/movies/store' => '/admin/movies',
        'admin/movies/update' => '/admin/movies/{id}',
        'admin/createTheater' => '/admin/theaters/create',
        'admin/editTheater' => '/admin/theaters/{id}/edit',
        'admin/createFoodItem' => '/admin/food-items/create',
        'admin/editFoodItem' => '/admin/food-items/{id}/edit',
        
        // ==================== MODERATOR ====================
        'moderator/dashboard' => '/moderator',
        'moderator/showtimes' => '/moderator/showtimes',
        'moderator/movies' => '/moderator',
        'moderator/staff' => '/moderator',
        'moderator/reports' => '/moderator/revenue',
        'moderator/foodItems' => '/moderator/food-items',
        'moderator/foodItemsCreate' => '/moderator/food-items/create',
        'moderator/foodItemsEdit' => '/moderator/food-items/{id}/edit',
        'moderator/foodItemsStore' => '/moderator/food-items',
        'moderator/foodItemsUpdate' => '/moderator/food-items/{id}',
        'moderator/foodItemsDelete' => '/moderator/food-items/{id}',
        'moderator/createShowtime' => '/moderator/showtimes/create',
        'moderator/editShowtime' => '/moderator/showtimes/{id}/edit',
        'moderator/screens' => '/moderator/screens',
        'moderator/createScreen' => '/moderator/screens/create',
        'moderator/editScreen' => '/moderator/screens/{id}/edit',
        'moderator/screenEdit' => '/moderator/screens/{id}/edit',
        'moderator/screenLayoutUpdate' => '/moderator/screens/{id}',
        'moderator/screensStore' => '/moderator/screens',
        
        // ==================== COUNTER STAFF ====================
        'counterStaff/dashboard' => '/counter',
        'counterStaff/scanQR' => '/counter/scan',
        'counterStaff/sellTicket' => '/counter',
        'counterStaff/printTickets' => '/counter/bookings',
        'counterStaff/showtimes' => '/counter',
        'counterStaff/salesHistory' => '/counter/bookings',
        'counterStaff/scannedTickets' => '/counter/bookings',
        
        // ==================== NEWS ====================
        'news/index' => '/news',
        'news/detail' => '/news/{slug}',
        'news/category' => '/news/category/{categoryId}',
    ];

    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Kiểm tra xem có query param 'route' không (legacy PHP thuần)
        if ($request->has('route')) {
            $legacyRoute = $request->get('route');
            
            // Tìm Laravel route tương ứng
            if (isset($this->routeMap[$legacyRoute])) {
                $newRoute = $this->routeMap[$legacyRoute];
                $usedParams = ['route'];
                
                // Thay thế parameters động
                foreach ($request->except('route') as $key => $value) {
                    $placeholder = '{' . $key . '}';
                    if (str_contains($newRoute, $placeholder)) {
                        $newRoute = str_replace($placeholder, $value, $newRoute);
                        $usedParams[] = $key;
                    }
                }
                
                // Xóa các placeholder chưa được thay thế
                $newRoute = preg_replace('/\{[a-zA-Z0-9_]+\}/', '', $newRoute);
                $newRoute = str_replace('//', '/', $newRoute); // Clean double slashes
                
                // Preserve query parameters that were not consumed by path placeholders.
                $queryParams = $request->except($usedParams);
                if (!empty($queryParams)) {
                    $newRoute .= '?' . http_build_query($queryParams);
                }
                
                // Redirect 301 (permanent) để SEO friendly
                return redirect($newRoute, 301);
            }
            
            // Legacy route không map: bỏ param `route` và tiếp tục URL hiện tại
            // (tránh văng về trang chủ khi form/admin còn hidden input cũ)
            if ($request->path() !== '/' && $request->path() !== 'index.php') {
                $query = $request->except('route');

                return redirect($request->url() . (empty($query) ? '' : '?' . http_build_query($query)));
            }

            return redirect('/', 301);
        }
        
        return $next($request);
    }
}
