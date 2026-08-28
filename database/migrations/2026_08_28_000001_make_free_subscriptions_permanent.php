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

        $freePlanIds = DB::table('subscriptions')
            ->whereRaw('LOWER(access_level) = ?', ['free'])
            ->pluck('id');

        DB::table('users')->whereIn('subscription_id', $freePlanIds)->update([
            'subscription_started_at' => null,
            'subscription_expires_at' => null,
            'subscription_auto_renew' => false,
        ]);
    }

    public function down(): void
    {
        // Free plans are intentionally permanent, so no expiry is restored.
    }
};
