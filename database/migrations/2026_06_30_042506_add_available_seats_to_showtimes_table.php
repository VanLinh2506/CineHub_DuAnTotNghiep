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

        // Backfill any null values; this is safe on both old and new schemas.
        DB::statement('
            UPDATE showtimes 
            JOIN theater_screens ON showtimes.screen_id = theater_screens.id 
            SET showtimes.available_seats = theater_screens.total_seats
            WHERE showtimes.available_seats IS NULL
        ');
    }

    public function down(): void
    {
        // No-op rollback: this project mixes SQL-imported schema with migrations,
        // so we avoid dropping shared columns during rollback.
    }
};
