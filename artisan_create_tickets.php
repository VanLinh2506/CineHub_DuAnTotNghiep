use App\Models\{User, Booking, Ticket, Showtime};
use Carbon\Carbon;

$user = User::where('email', 'user@test.com')->first();

// Create future booking
$futureBooking = Booking::create([
    'user_id' => $user->id,
    'showtime_id' => 1,
    'seats' => json_encode(['A1']),
    'total_amount' => 100000,
    'status' => 'completed',
    'qr_code' => 'BOOKING_TEST_' . time(),
]);

// Create future ticket
Ticket::create([
    'user_id' => $user->id,
    'showtime_id' => 1,
    'booking_pending_id' => $futureBooking->id,
    'seat' => 'A1',
    'seat_type' => 'normal',
    'price' => 100000,
    'qr_code' => 'TICKET_TEST_' . time(),
    'status' => 'Đã đặt',
]);

echo "Created booking: " . $futureBooking->id . "\n";
