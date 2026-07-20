<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Category;
use App\Models\Movie;
use App\Models\Screen;
use App\Models\SeatReservation;
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

    public function test_authenticated_user_can_reselect_seats_only_twice(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $user = User::factory()->create();
        $showtime = $this->createShowtime();
        $this->actingAs($user);

        for ($attempt = 1; $attempt <= 2; $attempt++) {
            $this->postJson('/booking/seats/reserve', [
                'showtime_id' => $showtime->id,
                'seats' => ['A1', 'A2'],
            ])->assertOk();

            $this->postJson('/booking/seats/release', [
                'showtime_id' => $showtime->id,
                'seats' => ['A1', 'A2'],
                'reselection' => true,
            ])
                ->assertOk()
                ->assertJsonPath('seatReselection.used', $attempt)
                ->assertJsonPath('seatReselection.remaining', 2 - $attempt);
        }

        $this->postJson('/booking/seats/reserve', [
            'showtime_id' => $showtime->id,
            'seats' => ['A1', 'A2'],
        ])->assertOk();

        $this->postJson('/booking/seats/release', [
            'showtime_id' => $showtime->id,
            'seats' => ['A1', 'A2'],
            'reselection' => true,
        ])
            ->assertStatus(409)
            ->assertJson([
                'success' => false,
                'error' => 'seat_reselection_limit_reached',
            ])
            ->assertJsonPath('seatReselection.remaining', 0);

        $this->assertDatabaseHas('seat_reservations', [
            'showtime_id' => $showtime->id,
            'seat' => 'A1',
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('booking_session_tracking', [
            'showtime_id' => $showtime->id,
            'user_id' => $user->id,
            'seat_reselection_count' => 2,
        ]);
    }

    public function test_reserved_seats_are_returned_when_the_booking_page_is_reloaded(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $user = User::factory()->create();
        $showtime = $this->createShowtime();
        $this->actingAs($user);

        $this->postJson('/booking/seats/reserve', [
            'showtime_id' => $showtime->id,
            'seats' => ['A1', 'A2'],
        ])->assertOk();

        $this->get('/booking?' . http_build_query([
            'movie' => $showtime->movie_id,
            'theater' => $showtime->theater_id,
            'date' => $showtime->show_date->toDateString(),
            'showtime_id' => $showtime->id,
        ]))
            ->assertOk()
            ->assertSee('myReservedSeats: ["A1","A2"]', false);

        $this->getJson('/api/booking/seat-map?showtime_id=' . $showtime->id)
            ->assertOk()
            ->assertJsonPath('myReservedSeats.0', 'A1')
            ->assertJsonPath('myReservedSeats.1', 'A2')
            ->assertJsonPath('remainingSeconds', fn ($seconds) => $seconds > 0);
    }

    public function test_browsing_another_theater_keeps_the_existing_seat_reservation(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $user = User::factory()->create();
        $originalShowtime = $this->createShowtime();
        $otherTheater = Theater::factory()->create();
        $otherScreen = Screen::create([
            'theater_id' => $otherTheater->id,
            'screen_name' => 'Screen 2',
            'screen_number' => '2',
            'screen_type' => '2D',
            'total_seats' => 120,
            'seat_layout_config' => [
                'rows' => range('A', 'J'),
                'cols' => range(1, 12),
                'vip_rows' => ['D', 'E', 'F'],
                'couple_rows' => ['J'],
            ],
        ]);
        $otherShowtime = Showtime::create([
            'movie_id' => $originalShowtime->movie_id,
            'theater_id' => $otherTheater->id,
            'screen_id' => $otherScreen->id,
            'show_date' => $originalShowtime->show_date->toDateString(),
            'show_time' => '20:30:00',
            'price' => 95000,
            'available_seats' => 120,
        ]);

        $this->actingAs($user)
            ->postJson('/booking/seats/reserve', [
                'showtime_id' => $originalShowtime->id,
                'seats' => ['A1', 'A2'],
            ])
            ->assertOk();

        $this->getJson('/api/booking/seat-map?showtime_id='.$otherShowtime->id)
            ->assertOk();

        $this->assertDatabaseHas('seat_reservations', [
            'showtime_id' => $originalShowtime->id,
            'seat' => 'A1',
            'user_id' => $user->id,
        ]);
        $this->assertDatabaseHas('seat_reservations', [
            'showtime_id' => $originalShowtime->id,
            'seat' => 'A2',
            'user_id' => $user->id,
        ]);

        $this->getJson('/api/booking/seat-map?showtime_id='.$originalShowtime->id)
            ->assertOk()
            ->assertJsonPath('myReservedSeats.0', 'A1')
            ->assertJsonPath('myReservedSeats.1', 'A2')
            ->assertJsonPath('remainingSeconds', fn ($seconds) => $seconds > 0);
    }

    public function test_room_countdown_does_not_reset_when_same_showtime_is_loaded_again(): void
    {
        $user = User::factory()->create();
        $showtime = $this->createShowtime();
        $this->actingAs($user);
        $startedAt = now()->startOfSecond();

        $this->travelTo($startedAt);

        $this->getJson('/api/booking/seat-map?showtime_id=' . $showtime->id)
            ->assertOk()
            ->assertJsonPath('roomRemainingSeconds', 600);

        $this->travel(75)->seconds();

        $this->getJson('/api/booking/seat-map?showtime_id=' . $showtime->id)
            ->assertOk()
            ->assertJsonPath('roomRemainingSeconds', 525);

        $this->assertDatabaseCount('booking_session_tracking', 1);
        $this->assertDatabaseHas('booking_session_tracking', [
            'user_id' => $user->id,
            'showtime_id' => $showtime->id,
            'session_start' => $startedAt->toDateTimeString(),
        ]);

        $this->travelBack();
    }

    public function test_cancelled_vnpay_payment_keeps_reserved_seats_until_their_expiration(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);
        config(['services.vnpay.hash_secret' => 'test-vnpay-secret']);

        $user = User::factory()->create();
        $showtime = $this->createShowtime();
        $this->actingAs($user);

        $this->postJson('/booking/seats/reserve', [
            'showtime_id' => $showtime->id,
            'seats' => ['A1', 'A2'],
        ])->assertOk();

        $originalExpiresAt = SeatReservation::query()
            ->where('showtime_id', $showtime->id)
            ->where('user_id', $user->id)
            ->where('seat', 'A1')
            ->value('expires_at');

        $booking = Booking::create([
            'user_id' => $user->id,
            'showtime_id' => $showtime->id,
            'seats' => ['A1', 'A2'],
            'food_items' => [],
            'customer_email' => $user->email,
            'total_amount' => 180000,
            'vnp_txn_ref' => 'BKG-CANCEL-TEST',
            'status' => 'pending',
            'expires_at' => now()->addMinutes(10),
        ]);

        $callbackParams = [
            'vnp_Amount' => 18000000,
            'vnp_ResponseCode' => '24',
            'vnp_TxnRef' => $booking->vnp_txn_ref,
        ];
        ksort($callbackParams);
        $callbackParams['vnp_SecureHash'] = hash_hmac(
            'sha512',
            http_build_query($callbackParams),
            'test-vnpay-secret'
        );

        $response = $this->get('/payment/vnpay/callback?' . http_build_query($callbackParams));

        $response
            ->assertRedirect(route('booking.index', ['showtime_id' => $showtime->id]))
            ->assertSessionHas('error', 'Bạn đã hủy thanh toán. Ghế vẫn được giữ trong thời gian còn lại.');

        $this->assertDatabaseHas('booking_pending', [
            'id' => $booking->id,
            'status' => 'cancelled',
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
        $this->assertSame(
            $originalExpiresAt?->timestamp,
            SeatReservation::query()
                ->where('showtime_id', $showtime->id)
                ->where('user_id', $user->id)
                ->where('seat', 'A1')
                ->value('expires_at')?->timestamp
        );

        $this->get(route('booking.index', ['showtime_id' => $showtime->id]))
            ->assertOk()
            ->assertSee('myReservedSeats: ["A1","A2"]', false);
    }

    public function test_pending_vnpay_booking_is_shown_in_my_tickets_and_can_be_resumed(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);
        config([
            'services.vnpay.url' => 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html',
            'services.vnpay.tmn_code' => 'TESTCODE',
            'services.vnpay.hash_secret' => 'test-secret',
            'services.vnpay.return_url' => 'http://localhost/payment/vnpay/callback',
        ]);

        $user = User::factory()->create();
        $showtime = $this->createShowtime();
        $this->actingAs($user);

        $this->postJson('/booking/seats/reserve', [
            'showtime_id' => $showtime->id,
            'seats' => ['A1', 'A2'],
        ])->assertOk();

        $booking = Booking::create([
            'user_id' => $user->id,
            'showtime_id' => $showtime->id,
            'seats' => ['A1', 'A2'],
            'food_items' => [],
            'customer_email' => $user->email,
            'total_amount' => 180000,
            'vnp_txn_ref' => 'BKG-ABANDONED',
            'status' => 'pending',
            'expires_at' => now()->addMinutes(10),
        ]);

        $this->get(route('booking.my-tickets'))
            ->assertOk()
            ->assertSee('Chờ thanh toán')
            ->assertSee('Tiếp tục thanh toán VNPay')
            ->assertSee(route('booking.payment', $booking->id), false);

        $response = $this->get(route('booking.payment', $booking->id));

        $response->assertRedirectContains('sandbox.vnpayment.vn/paymentv2/vpcpay.html');
        $this->assertNotSame('BKG-ABANDONED', $booking->fresh()->vnp_txn_ref);
        $this->assertSame($booking->id, session('pending_booking_id'));
    }

    public function test_expired_pending_booking_cannot_be_resumed(): void
    {
        $user = User::factory()->create();
        $showtime = $this->createShowtime();
        $booking = Booking::create([
            'user_id' => $user->id,
            'showtime_id' => $showtime->id,
            'seats' => ['A1'],
            'food_items' => [],
            'customer_email' => $user->email,
            'total_amount' => 90000,
            'vnp_txn_ref' => 'BKG-EXPIRED',
            'status' => 'pending',
            'expires_at' => now()->subSecond(),
        ]);

        $this->actingAs($user)
            ->get(route('booking.payment', $booking->id))
            ->assertRedirect(route('booking.index', ['showtime_id' => $showtime->id]))
            ->assertSessionHas('error', 'Thời gian giữ ghế đã hết. Vui lòng chọn lại ghế.');

        $this->assertDatabaseHas('booking_pending', [
            'id' => $booking->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_user_cannot_resume_another_users_booking(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();
        $showtime = $this->createShowtime();
        $booking = Booking::create([
            'user_id' => $owner->id,
            'showtime_id' => $showtime->id,
            'seats' => ['A1'],
            'food_items' => [],
            'customer_email' => $owner->email,
            'total_amount' => 90000,
            'vnp_txn_ref' => 'BKG-PRIVATE',
            'status' => 'pending',
            'expires_at' => now()->addMinutes(10),
        ]);

        $this->actingAs($otherUser)
            ->get(route('booking.payment', $booking->id))
            ->assertNotFound();
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
