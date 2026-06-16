<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Notification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendShowtimeReminders extends Command
{
    protected $signature = 'bookings:send-reminders';
    protected $description = 'Send showtime reminders to users 2 hours before their movie starts';

    public function handle()
    {
        // Get bookings with showtimes starting in 2 hours
        $twoHoursLater = now()->addHours(2);
        
        $bookings = Booking::with(['user', 'showtime.movie', 'showtime.theater'])
            ->whereHas('showtime', function($query) use ($twoHoursLater) {
                $query->whereDate('show_date', $twoHoursLater->toDateString())
                    ->whereTime('show_time', '>=', $twoHoursLater->subMinutes(5)->toTimeString())
                    ->whereTime('show_time', '<=', $twoHoursLater->addMinutes(10)->toTimeString());
            })
            ->where('status', 'confirmed')
            ->get();

        $count = 0;
        
        foreach ($bookings as $booking) {
            // Create notification
            Notification::create([
                'user_id' => $booking->user_id,
                'type' => 'showtime_reminder',
                'title' => 'Nhắc nhở suất chiếu',
                'message' => "Phim '{$booking->showtime->movie->title}' sẽ bắt đầu lúc {$booking->showtime->show_time}. Vui lòng đến rạp sớm!",
                'link' => route('booking.confirmation', $booking->id),
            ]);
            
            $count++;
        }
        
        $this->info("Sent {$count} showtime reminder(s).");
        
        return 0;
    }
}
