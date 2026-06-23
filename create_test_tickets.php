<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Http\Kernel');
$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

use App\Models\{User, Booking, Ticket, Showtime, Movie, Theater, Screen};
use Carbon\Carbon;

try {
    $user = User::where('email', 'user@test.com')->first();
    
    // Create test showtime (2 days from now)
    $futureShowtime = Showtime::updateOrCreate(
        ['id' => 9999],
        [
            'movie_id' => 1,
            'theater_id' => 1,
            'screen_id' => 1,
            'show_date' => Carbon::now()->addDays(2)->format('Y-m-d'),
            'show_time' => '14:00',
            'price' => 100000,
            'available_seats' => 100,
        ]
    );
    
    // Create past showtime (2 days ago)
    $pastShowtime = Showtime::updateOrCreate(
        ['id' => 9998],
        [
            'movie_id' => 1,
            'theater_id' => 1,
            'screen_id' => 1,
            'show_date' => Carbon::now()->subDays(2)->format('Y-m-d'),
            'show_time' => '14:00',
            'price' => 100000,
            'available_seats' => 100,
        ]
    );
    
    // Create booking for future showtime
    $futureBooking = Booking::create([
        'user_id' => $user->id,
        'showtime_id' => $futureShowtime->id,
        'seats' => ['A1'],
        'total_amount' => 100000,
        'status' => 'completed',
        'qr_code' => 'BOOKING_' . uniqid(),
        'payment_method' => 'VNPay',
    ]);
    
    // Create ticket for future booking
    Ticket::create([
        'user_id' => $user->id,
        'showtime_id' => $futureShowtime->id,
        'booking_pending_id' => $futureBooking->id,
        'seat' => 'A1',
        'seat_type' => 'normal',
        'price' => 100000,
        'qr_code' => 'TICKET_' . uniqid(),
        'status' => 'Đã đặt',
    ]);
    
    // Create booking for past showtime
    $pastBooking = Booking::create([
        'user_id' => $user->id,
        'showtime_id' => $pastShowtime->id,
        'seats' => ['B1'],
        'total_amount' => 100000,
        'status' => 'completed',
        'qr_code' => 'BOOKING_' . uniqid(),
        'payment_method' => 'VNPay',
    ]);
    
    // Create ticket for past booking
    Ticket::create([
        'user_id' => $user->id,
        'showtime_id' => $pastShowtime->id,
        'booking_pending_id' => $pastBooking->id,
        'seat' => 'B1',
        'seat_type' => 'normal',
        'price' => 100000,
        'qr_code' => 'TICKET_' . uniqid(),
        'status' => 'Đã đặt',
    ]);
    
    echo "Test tickets created successfully!\n";
    echo "Future ticket (with QR): " . $futureBooking->id . "\n";
    echo "Past ticket (expired): " . $pastBooking->id . "\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
