<?php

use App\Models\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('booking.showtime.{showtimeId}', function (?User $user, int $showtimeId): bool {
    return $user !== null;
});

Broadcast::channel('user.session.{userId}', function (User $user, int $userId): bool {
    return $user->id === $userId;
});
