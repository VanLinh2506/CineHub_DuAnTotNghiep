<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Movie;
use App\Models\Screen;
use App\Models\Showtime;
use App\Models\Theater;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Tests\TestCase;

class BookingSeatReservationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_cannot_reserve_seats(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $showtime = $this->createShowtime();

        $this->postJson('/booking/seats/reserve', [
            'showtime_id' => $showtime->id,
            'seats' => ['A1', 'A2'],
        ])->assertUnauthorized();
    }

    public function test_authenticated_user_can_reserve_available_seats(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $user = User::factory()->create();
        $showtime = $this->createShowtime();

        $this->actingAs($user)
            ->postJson('/booking/seats/reserve', [
                'showtime_id' => $showtime->id,
                'seats' => ['A1', 'A2'],
            ])
            ->assertOk()
            ->assertJson([
                'success' => true,
                'lockedSeats' => ['A1', 'A2'],
            ]);

        $this->assertDatabaseHas('seat_reservations', [
            'showtime_id' => $showtime->id,
            'seat' => 'A1',
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('seat_reservations', [
            'showtime_id' => $showtime->id,
            'seat' => 'A2',
            'user_id' => $user->id,
        ]);
    }

    private function createShowtime(): Showtime
    {
        $category = Category::create([
            'name' => 'Action',
            'slug' => 'action',
        ]);

        $movie = Movie::create([
            'title' => 'Reservation Test Movie',
            'category_id' => $category->id,
            'status' => 'Online',
            'status_admin' => 'published',
            'type' => 'phimle',
            'level' => 'Free',
            'duration' => 120,
            'rating' => 8.0,
        ]);

        $theater = Theater::factory()->create();
        $screen = Screen::create([
            'theater_id' => $theater->id,
            'screen_name' => 'Screen 1',
            'screen_number' => '1',
            'screen_type' => '2D',
            'total_seats' => 120,
            'seat_layout_config' => [
                'rows' => range('A', 'J'),
                'cols' => range(1, 12),
                'vip_rows' => ['D', 'E', 'F'],
                'couple_rows' => ['J'],
            ],
        ]);

        return Showtime::create([
            'movie_id' => $movie->id,
            'theater_id' => $theater->id,
            'screen_id' => $screen->id,
            'show_date' => now()->addDay()->toDateString(),
            'show_time' => '19:00:00',
            'price' => 90000,
            'available_seats' => 120,
        ]);
    }
}
