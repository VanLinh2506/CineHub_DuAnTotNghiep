<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('booking_pending') || !Schema::hasColumn('booking_pending', 'customer_email')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        // Modify customer_email to be nullable
        DB::statement('ALTER TABLE `booking_pending` MODIFY `customer_email` VARCHAR(255) NULL');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('booking_pending') || !Schema::hasColumn('booking_pending', 'customer_email')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        // Revert back to NOT NULL (optional)
        DB::statement('ALTER TABLE `booking_pending` MODIFY `customer_email` VARCHAR(255) NOT NULL');
    }
};
