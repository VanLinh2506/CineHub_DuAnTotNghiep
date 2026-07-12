<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('seat_reservations')) {
            return;
        }

        if (Schema::getConnection()->getDriverName() === 'sqlite') {
            return;
        }

        DB::statement('SET @OLD_SEAT_RESERVATION_SQL_MODE=@@SESSION.sql_mode');
        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(REPLACE(@@SESSION.sql_mode, 'NO_ZERO_DATE', ''), 'NO_ZERO_IN_DATE', ''))");

        try {
            if (Schema::hasColumn('seat_reservations', 'expires_at')) {
                DB::statement('ALTER TABLE `seat_reservations` MODIFY `expires_at` TIMESTAMP NULL DEFAULT NULL');
            }

            if (Schema::hasColumn('seat_reservations', 'session_id')) {
                DB::statement('ALTER TABLE `seat_reservations` MODIFY `session_id` VARCHAR(255) NULL');
            }
        } finally {
            DB::statement('SET SESSION sql_mode=@OLD_SEAT_RESERVATION_SQL_MODE');
        }
    }

    public function down(): void
    {
        // Keep the realtime-safe nullable timestamp shape.
    }
};
