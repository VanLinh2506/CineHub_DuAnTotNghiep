<?php

use App\Models\Booking;
use App\Models\Showtime;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Contracts\Console\Kernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

require dirname(__DIR__) . '/vendor/autoload.php';

$app = require_once dirname(__DIR__) . '/bootstrap/app.php';
$app->make(Kernel::class)->bootstrap();

$quantity = max(1, min(10, (int) ($argv[1] ?? 3)));

$created = DB::transaction(function () use ($quantity): array {
    $user = User::query()->orderBy('id')->firstOrFail();
    $showtime = Showtime::query()
        ->with(['movie', 'theater', 'screen'])
        ->where(function ($query) {
            $query->whereDate('show_date', '>', today())
                ->orWhere(function ($today) {
                    $today->whereDate('show_date', today())
                        ->whereTime('show_time', '>', now()->format('H:i:s'));
                });
        })
        ->orderBy('show_date')
        ->orderBy('show_time')
        ->first();

    if ($showtime === null) {
        $template = Showtime::query()
            ->with(['movie', 'theater', 'screen'])
            ->latest('id')
            ->firstOrFail();

        $showtime = $template->replicate();
        $showtime->show_date = today()->addDay()->toDateString();
        $showtime->available_seats = $template->screen?->total_seats ?? $template->available_seats;
        $showtime->save();
        $showtime->load(['movie', 'theater', 'screen']);
    }

    $occupied = Ticket::query()
        ->where('showtime_id', $showtime->id)
        ->pluck('seat')
        ->all();

    $available = [];
    foreach (range('A', 'J') as $row) {
        foreach (range(1, 20) as $number) {
            $seat = $row . $number;
            if (!in_array($seat, $occupied, true)) {
                $available[] = $seat;
            }
        }
    }

    if (count($available) < $quantity) {
        throw new RuntimeException("Chỉ còn " . count($available) . " ghế trống.");
    }

    $result = [];
    foreach (array_slice($available, 0, $quantity) as $seat) {
        $reference = 'TEST_' . now()->format('YmdHis') . '_' . Str::upper(Str::random(6));
        $booking = Booking::create([
            'user_id' => $user->id,
            'showtime_id' => $showtime->id,
            'seats' => [$seat],
            'food_items' => [],
            'customer_email' => $user->email,
            'customer_name' => $user->name,
            'total_amount' => $showtime->price,
            'vnp_txn_ref' => $reference,
            'qr_code' => 'BOOKING_' . Str::random(24),
            'status' => 'completed',
        ]);

        $ticket = Ticket::create([
            'user_id' => $user->id,
            'showtime_id' => $showtime->id,
            'booking_pending_id' => $booking->id,
            'seat' => $seat,
            'seat_type' => 'normal',
            'price' => $showtime->price,
            'qr_code' => 'TICKET_' . Str::random(24),
            'status' => 'Đã đặt',
        ]);

        $result[] = [
            'ticket_id' => $ticket->id,
            'booking_id' => $booking->id,
            'seat' => $seat,
            'qr_code' => $ticket->qr_code,
        ];
    }

    return [
        'user' => $user->email,
        'movie' => $showtime->movie?->title,
        'theater' => $showtime->theater?->name,
        'screen' => $showtime->screen?->screen_name,
        'showtime' => $showtime->show_date->format('Y-m-d') . ' ' . $showtime->show_time,
        'price_each' => (float) $showtime->price,
        'tickets' => $result,
    ];
});

echo json_encode($created, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;
