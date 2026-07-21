<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('bookings:clean-expired')
    ->everyTenSeconds()
    ->withoutOverlapping();

Schedule::command('contracts:expire-theater-admins')
    ->dailyAt('00:10')
    ->withoutOverlapping();

Schedule::command('movies:update-status')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command('subscriptions:renew')
    ->hourly()
    ->withoutOverlapping();

Schedule::command('bookings:send-reminders')
    ->everyMinute()
    ->withoutOverlapping();

Schedule::command('comments:moderate-existing --days=1')
    ->everyFiveMinutes()
    ->withoutOverlapping();

Schedule::call(function () {
    \App\Models\WatchHistory::where('playback_updated_at', '<', now()->subDays(30))
        ->update(['last_time' => 0, 'playback_updated_at' => null]);

    \App\Models\WatchHistory::where('episode_updated_at', '<', now()->subYear())
        ->update(['episode_id' => null, 'episode_updated_at' => null]);
})->name('watch-history:expire-progress')->dailyAt('02:15')->withoutOverlapping();
