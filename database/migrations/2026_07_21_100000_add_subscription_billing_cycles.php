<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->unsignedSmallInteger('duration_months')->default(1)->after('price');
            $table->string('access_level', 20)->default('free')->after('name');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('subscription_expires_at')->nullable()->after('subscription_id');
            $table->boolean('subscription_auto_renew')->default(true)->after('subscription_expires_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', fn (Blueprint $table) => $table->dropColumn(['subscription_expires_at', 'subscription_auto_renew']));
        Schema::table('subscriptions', fn (Blueprint $table) => $table->dropColumn(['duration_months', 'access_level']));
    }
};
