<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::table('users')->whereNotNull('subscription_started_at')
            ->whereNotNull('subscription_expires_at')
            ->select(['id', 'subscription_started_at', 'subscription_expires_at'])
            ->orderBy('id')->chunkById(100, function ($users) {
                foreach ($users as $user) {
                    $startedAt = Carbon::parse($user->subscription_started_at);
                    $expiresAt = Carbon::parse($user->subscription_expires_at);
                    $months = max(1, (int) ceil($startedAt->diffInDays($expiresAt) / 30));

                    DB::table('users')->where('id', $user->id)->update([
                        'subscription_expires_at' => $startedAt->copy()->addMonthsNoOverflow($months),
                    ]);
                }
            });
    }

    public function down(): void
    {
        // The previous 30-day expiry cannot be restored without losing renewals.
    }
};
