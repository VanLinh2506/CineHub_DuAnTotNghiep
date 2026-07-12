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
