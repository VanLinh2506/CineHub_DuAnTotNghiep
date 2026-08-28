<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Carbon;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('subscription_started_at')->nullable()->after('subscription_id');
        });

        DB::table('users')->whereNotNull('subscription_expires_at')
            ->select(['id', 'subscription_expires_at'])->orderBy('id')->chunkById(100, function ($users) {
                foreach ($users as $user) {
                    DB::table('users')->where('id', $user->id)->update([
                        'subscription_started_at' => Carbon::parse($user->subscription_expires_at)->subMonthNoOverflow(),
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn('subscription_started_at'));
    }
};
