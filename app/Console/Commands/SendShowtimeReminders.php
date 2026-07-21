<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Notification;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendShowtimeReminders extends Command
{
    protected $signature = 'bookings:send-reminders';
    protected $description = 'Send each customer one reminder at the calculated lead time';

    public function handle(): int
    {
        $now = now();
        $count = 0;

        Booking::with(['showtime.movie', 'showtime.theater', 'showtime.screen'])
            ->where('status', 'completed')
            ->whereHas('showtime', function ($query) use ($now) {
                $query->where(function ($dates) use ($now) {
                    $dates->whereDate('show_date', '>', $now->toDateString())
                        ->orWhere(function ($today) use ($now) {
                            $today->whereDate('show_date', $now->toDateString())
                                ->whereTime('show_time', '>', $now->format('H:i:s'));
                        });
                });
            })
            ->orderBy('id')
            ->chunkById(200, function ($bookings) use ($now, &$count) {
                foreach ($bookings as $booking) {
                    $showtime = $booking->showtime;
                    if (!$showtime) continue;

                    $startsAt = Carbon::parse($showtime->show_date->toDateString().' '.$showtime->show_time);
                    $bookedAt = Carbon::parse($booking->created_at);
                    $totalSeconds = max($bookedAt->diffInSeconds($startsAt, false), 0);
                    $thirtyPercentSeconds = (int) floor($totalSeconds * 0.30);
                    $leadMinutes = $thirtyPercentSeconds >= 3600 ? 60 : 30;
                    $notifyAt = $startsAt->copy()->subMinutes($leadMinutes);
                    if ($now->lt($notifyAt) || $now->gte($startsAt)) continue;

                    $dedupeLink = route('booking.history').'?booking_id='.$booking->id;
                    $alreadySent = Notification::where('user_id', $booking->user_id)
                        ->where('type', 'showtime_reminder')->where('link', $dedupeLink)->exists();
                    if ($alreadySent) continue;

                    Notification::create([
                        'user_id' => $booking->user_id,
                        'type' => 'showtime_reminder',
                        'title' => 'Sắp đến giờ xem phim',
                        'message' => 'Còn '.$leadMinutes.' phút nữa phim “'.($showtime->movie?->title ?? 'phim').'” sẽ bắt đầu tại '.($showtime->theater?->name ?? 'rạp').', phòng '.($showtime->screen?->screen_name ?? 'chưa xác định').', lúc '.$startsAt->format('H:i d/m/Y').'. Vui lòng đến sớm để quét mã QR.',
                        'link' => $dedupeLink,
                        'is_read' => false,
                    ]);
                    $count++;
                }
            });

        $this->info("Sent {$count} showtime reminder(s).");
        return self::SUCCESS;
    }
}
