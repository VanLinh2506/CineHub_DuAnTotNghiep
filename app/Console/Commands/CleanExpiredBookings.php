<?php

namespace App\Console\Commands;

use App\Models\BookingPending;
use Illuminate\Console\Command;

class CleanExpiredBookings extends Command
{
    protected $signature = 'bookings:clean-expired';
    protected $description = 'Clean up expired booking_pending records';

    public function handle()
    {
        $deleted = BookingPending::where('expires_at', '<', now())->delete();
        
        $this->info("Cleaned up {$deleted} expired booking(s).");
        
        return 0;
    }
}
