<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('showtimes')) {
            return;
        }

        if (!Schema::hasColumn('showtimes', 'available_seats')) {
            Schema::table('showtimes', function (Blueprint $table) {
                $table->integer('available_seats')->nullable()->after('price');
            });
        }

        if (!Schema::hasTable('theater_screens') || !Schema::hasColumn('showtimes', 'screen_id')) {
            return;
        }

        // Backfill any null values; this query works in both MySQL and SQLite tests.
        DB::statement('
            UPDATE showtimes
            SET available_seats = (
                SELECT theater_screens.total_seats
                FROM theater_screens
                WHERE theater_screens.id = showtimes.screen_id
            )
            WHERE available_seats IS NULL
                AND screen_id IS NOT NULL
        ');
    }

    public function down(): void
    {
        // No-op rollback: this project mixes SQL-imported schema with migrations,
        // so we avoid dropping shared columns during rollback.
    }
};
