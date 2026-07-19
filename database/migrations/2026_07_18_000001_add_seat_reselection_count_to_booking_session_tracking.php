<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('booking_session_tracking') || Schema::hasColumn('booking_session_tracking', 'seat_reselection_count')) {
            return;
        }

        Schema::table('booking_session_tracking', function (Blueprint $table) {
            $table->unsignedTinyInteger('seat_reselection_count')->default(0)->after('violation_count');
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('booking_session_tracking') || !Schema::hasColumn('booking_session_tracking', 'seat_reselection_count')) {
            return;
        }

        Schema::table('booking_session_tracking', function (Blueprint $table) {
            $table->dropColumn('seat_reselection_count');
        });
    }
};
