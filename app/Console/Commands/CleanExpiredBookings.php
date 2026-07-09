<?php

namespace App\Console\Commands;

use App\Events\SeatMapChanged;
use App\Models\BookingPending;
use App\Models\SeatReservation;
use App\Models\Ticket;
use Illuminate\Console\Command;

class CleanExpiredBookings extends Command
{
    protected $signature = 'bookings:clean-expired';
    protected $description = 'Clean up expired booking_pending records';

    public function handle()
    {
        $deleted = BookingPending::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->delete();
        $expiredReservations = SeatReservation::query()
            ->where('expires_at', '<=', now())
            ->get(['id', 'showtime_id', 'seat']);

        $released = $expiredReservations->count();
        $showtimeIds = $expiredReservations->pluck('showtime_id')->filter()->unique()->values();

        if ($released > 0) {
            SeatReservation::query()
                ->whereIn('id', $expiredReservations->pluck('id')->all())
                ->delete();
        }

        foreach ($showtimeIds as $showtimeId) {
            $bookedSeats = Ticket::where('showtime_id', $showtimeId)
                ->where('status', 'Đã đặt')
                ->pluck('seat')
                ->filter()
                ->unique()
                ->values()
                ->all();

            $reservedSeats = SeatReservation::where('showtime_id', $showtimeId)
                ->active()
                ->pluck('seat')
                ->filter()
                ->unique()
                ->values()
                ->all();

            broadcast(new SeatMapChanged(
                showtimeId: (int) $showtimeId,
                action: 'expired',
                seats: $expiredReservations
                    ->where('showtime_id', $showtimeId)
                    ->pluck('seat')
                    ->filter()
                    ->unique()
                    ->values()
                    ->all(),
                bookedSeats: $bookedSeats,
                reservedSeats: $reservedSeats
            ));
        }
        
        $this->info("Cleaned up {$deleted} expired booking(s) and released {$released} seat reservation(s).");
        
        return 0;
    }
}
