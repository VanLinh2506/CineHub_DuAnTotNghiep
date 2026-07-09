<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('booking_pending')) {
            return;
        }

        Schema::table('booking_pending', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_pending', 'customer_name')) {
                $table->string('customer_name')->nullable()->after('user_id');
            }
            if (!Schema::hasColumn('booking_pending', 'customer_phone')) {
                $table->string('customer_phone', 20)->nullable()->after('customer_name');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('booking_pending')) {
            return;
        }

        Schema::table('booking_pending', function (Blueprint $table) {
            if (Schema::hasColumn('booking_pending', 'customer_name')) {
                $table->dropColumn('customer_name');
            }
            if (Schema::hasColumn('booking_pending', 'customer_phone')) {
                $table->dropColumn('customer_phone');
            }
        });
    }
};
