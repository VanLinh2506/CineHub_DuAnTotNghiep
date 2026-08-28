<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'subscription_id')) {
            return;
        }

        $startedAt = now();
        $expiresAt = $startedAt->copy()->addMonthNoOverflow();

        DB::table('users')->join('subscriptions', 'subscriptions.id', '=', 'users.subscription_id')
            ->whereRaw('LOWER(subscriptions.access_level) <> ?', ['free'])
            ->where(function ($query) {
                $query->whereNull('users.subscription_started_at')
                    ->orWhereNull('users.subscription_expires_at');
            })
            ->update([
                'users.subscription_started_at' => $startedAt,
                'users.subscription_expires_at' => $expiresAt,
                'users.subscription_auto_renew' => false,
            ]);
    }

    public function down(): void
    {
        // Existing subscription dates cannot be distinguished safely from backfilled dates.
    }
};
