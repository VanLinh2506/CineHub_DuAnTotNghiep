<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Test suite để kiểm tra routing compatibility từ PHP cũ sang Laravel
 */
class LegacyRoutingTest extends TestCase
{
    /**
     * Test home route redirects
     */
    public function test_legacy_home_routes(): void
    {
        // Test index.php without params redirects to home
        $response = $this->get('/index.php?route=home/index');
        $response->assertRedirect('/');
        $response->assertStatus(301);
    }

    /**
     * Test movie routes redirects
     */
    public function test_legacy_movie_routes(): void
    {
        // Test movie index
        $response = $this->get('/index.php?route=movie/index');
        $response->assertRedirect('/movies');
        $response->assertStatus(301);

        // Test movie detail with ID
        $response = $this->get('/index.php?route=movie/detail&id=5');
        $response->assertRedirect('/movies/5');
        $response->assertStatus(301);

        // Test movie watch
        $response = $this->get('/index.php?route=movie/watch&id=10');
        $response->assertRedirect('/movies/10/watch');
        $response->assertStatus(301);

        // Test episode watch
        $response = $this->get('/index.php?route=movie/watchEpisode&movieId=15&episodeNumber=3');
        $response->assertRedirect('/movies/15/episode/3');
        $response->assertStatus(301);

        // Test theater movies
        $response = $this->get('/index.php?route=movie/theater');
        $response->assertRedirect('/movies/theater');
        $response->assertStatus(301);

        // Test phim le
        $response = $this->get('/index.php?route=movie/phimle');
        $response->assertRedirect('/movies/phim-le');
        $response->assertStatus(301);

        // Test phim bo
        $response = $this->get('/index.php?route=movie/phimbo');
        $response->assertRedirect('/movies/phim-bo');
        $response->assertStatus(301);

        // Test category
        $response = $this->get('/index.php?route=movie/category&categoryId=7');
        $response->assertRedirect('/movies/category/7');
        $response->assertStatus(301);
    }

    /**
     * Test auth routes redirects
     */
    public function test_legacy_auth_routes(): void
    {
        $response = $this->get('/index.php?route=auth/login');
        $response->assertRedirect('/login');
        $response->assertStatus(301);

        $response = $this->get('/index.php?route=auth/register');
        $response->assertRedirect('/register');
        $response->assertStatus(301);

        $response = $this->get('/index.php?route=auth/forgotPassword');
        $response->assertRedirect('/forgot-password');
        $response->assertStatus(301);
    }

    /**
     * Test booking routes redirects
     */
    public function test_legacy_booking_routes(): void
    {
        $response = $this->get('/index.php?route=booking/selectSeats&showtimeId=20');
        $response->assertRedirect('/booking/showtime/20');
        $response->assertStatus(301);

        $response = $this->get('/index.php?route=booking/payment&bookingId=100');
        $response->assertRedirect('/booking/100/payment');
        $response->assertStatus(301);

        $response = $this->get('/index.php?route=booking/success&bookingId=100');
        $response->assertRedirect('/booking/100/confirmation');
        $response->assertStatus(301);

        $response = $this->get('/index.php?route=booking/history');
        $response->assertRedirect('/booking/history');
        $response->assertStatus(301);
    }

    /**
     * Test profile routes redirects
     */
    public function test_legacy_profile_routes(): void
    {
        $response = $this->get('/index.php?route=profile/index');
        $response->assertRedirect('/profile');
        $response->assertStatus(301);

        $response = $this->get('/index.php?route=profile/bookingHistory');
        $response->assertRedirect('/profile/bookings');
        $response->assertStatus(301);

        $response = $this->get('/index.php?route=profile/watchHistory');
        $response->assertRedirect('/profile/watch-history');
        $response->assertStatus(301);
    }

    /**
     * Test admin routes redirects
     */
    public function test_legacy_admin_routes(): void
    {
        $response = $this->get('/index.php?route=admin/dashboard');
        $response->assertRedirect('/admin');
        $response->assertStatus(301);

        $response = $this->get('/index.php?route=admin/users');
        $response->assertRedirect('/admin/users');
        $response->assertStatus(301);

        $response = $this->get('/index.php?route=admin/movies');
        $response->assertRedirect('/admin/movies');
        $response->assertStatus(301);

        $response = $this->get('/index.php?route=admin/editMovie&id=50');
        $response->assertRedirect('/admin/movies/50/edit');
        $response->assertStatus(301);
    }

    /**
     * Test moderator routes redirects
     */
    public function test_legacy_moderator_routes(): void
    {
        $response = $this->get('/index.php?route=moderator/dashboard');
        $response->assertRedirect('/moderator');
        $response->assertStatus(301);

        $response = $this->get('/index.php?route=moderator/showtimes');
        $response->assertRedirect('/moderator/showtimes');
        $response->assertStatus(301);

        $response = $this->get('/index.php?route=moderator/editShowtime&id=30');
        $response->assertRedirect('/moderator/showtimes/30/edit');
        $response->assertStatus(301);
    }

    /**
     * Test counter staff routes redirects
     */
    public function test_legacy_counter_staff_routes(): void
    {
        $response = $this->get('/index.php?route=counterStaff/dashboard');
        $response->assertRedirect('/counter');
        $response->assertStatus(301);

        $response = $this->get('/index.php?route=counterStaff/scanQR');
        $response->assertRedirect('/counter/scan');
        $response->assertStatus(301);
    }

    /**
     * Test news routes redirects
     */
    public function test_legacy_news_routes(): void
    {
        $response = $this->get('/index.php?route=news/index');
        $response->assertRedirect('/news');
        $response->assertStatus(301);

        $response = $this->get('/index.php?route=news/detail&slug=tin-tuc-moi');
        $response->assertRedirect('/news/tin-tuc-moi');
        $response->assertStatus(301);

        $response = $this->get('/index.php?route=news/category&categoryId=3');
        $response->assertRedirect('/news/category/3');
        $response->assertStatus(301);
    }

    /**
     * Test unmapped route redirects to home
     */
    public function test_unmapped_legacy_route_redirects_to_home(): void
    {
        $response = $this->get('/index.php?route=unknown/action');
        $response->assertRedirect('/');
        $response->assertStatus(301);
    }

    /**
     * Test query parameters are preserved
     */
    public function test_query_parameters_preserved(): void
    {
        $response = $this->get('/index.php?route=movie/index&sort=latest&page=2');
        $response->assertRedirect('/movies?sort=latest&page=2');
        $response->assertStatus(301);
    }
}
