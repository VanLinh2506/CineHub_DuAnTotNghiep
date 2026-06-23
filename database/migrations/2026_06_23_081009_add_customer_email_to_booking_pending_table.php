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
        Schema::table('booking_pending', function (Blueprint $table) {
            if (!Schema::hasColumn('booking_pending', 'customer_email')) {
                $table->string('customer_email')->nullable()->after('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('booking_pending', function (Blueprint $table) {
            if (Schema::hasColumn('booking_pending', 'customer_email')) {
                $table->dropColumn('customer_email');
            }
        });
    }
};
