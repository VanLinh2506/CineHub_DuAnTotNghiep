<?php

namespace Tests\Feature;

use App\Http\Controllers\AdminController;
use App\Models\Ticket;
use App\Models\Transaction;
use App\Models\Movie;
use App\Models\MovieViewEvent;
use App\Models\Showtime;
use App\Models\Theater;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use ReflectionMethod;
use Tests\TestCase;

class AdminRevenueCalculationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_revenue_is_five_percent_of_tickets_plus_all_subscription_revenue(): void
    {
        Ticket::create([
            'user_id' => 1,
            'showtime_id' => 1,
            'seat' => 'A1',
            'price' => 200000,
            'status' => 'Đã đặt',
        ]);

        Transaction::create([
            'user_id' => 1,
            'type' => 'subscription',
            'related_id' => 1,
            'amount' => 300000,
            'method' => 'Wallet',
            'status' => 'Thành công',
        ]);

        // Deposits and the booking transaction (which may include food) are
        // deliberately excluded from the supreme admin's revenue.
        foreach ([['deposit', 500000], ['ticket', 260000]] as [$type, $amount]) {
            Transaction::create([
                'user_id' => 1,
                'type' => $type,
                'related_id' => 2,
                'amount' => $amount,
                'method' => 'VNPay',
                'status' => 'Thành công',
            ]);
        }

        $method = new ReflectionMethod(AdminController::class, 'adminRevenue');
        $revenue = $method->invoke(app(AdminController::class));

        $this->assertSame(310000.0, $revenue);
    }

    public function test_dashboard_shows_real_views_upcoming_showtimes_and_theater_revenue(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $viewer = User::factory()->create();
        $theater = Theater::factory()->create(['name' => 'Rạp kiểm thử']);
        $movie = Movie::create([
            'title' => 'Phim dashboard',
            'type' => 'phimle',
            'projection_format' => '2D',
            'status' => 'Chiếu rạp',
            'status_admin' => 'published',
        ]);
        $showtime = Showtime::create([
            'movie_id' => $movie->id,
            'theater_id' => $theater->id,
            'screen_id' => 1,
            'show_date' => now()->addDay()->toDateString(),
            'show_time' => '19:00:00',
            'price' => 100000,
        ]);
        MovieViewEvent::create([
            'movie_id' => $movie->id,
            'user_id' => $viewer->id,
            'created_at' => now(),
        ]);
        Ticket::create([
            'user_id' => $viewer->id,
            'showtime_id' => $showtime->id,
            'seat' => 'A1',
            'price' => 100000,
            'status' => 'Đã đặt',
        ]);

        $this->actingAs($admin)
            ->get('/admin')
            ->assertOk()
            ->assertSee('Phim dashboard')
            ->assertSee('Rạp kiểm thử')
            ->assertSee('Doanh thu phim chiếu rạp theo rạp')
            ->assertViewHas('stats', fn (array $stats) => $stats['total_views'] === 1
                && $stats['total_showtimes'] === 1
                && $stats['upcoming_showtimes'] === 1);
    }
}
