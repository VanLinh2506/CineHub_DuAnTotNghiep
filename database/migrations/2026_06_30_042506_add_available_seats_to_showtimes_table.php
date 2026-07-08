<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('showtimes', function (Blueprint $table) {
            $table->integer('available_seats')->nullable()->after('price');
        });
        
        // Update existing showtimes to set available_seats from screen total_seats
        DB::statement('
            UPDATE showtimes 
            JOIN theater_screens ON showtimes.screen_id = theater_screens.id 
            SET showtimes.available_seats = theater_screens.total_seats
            WHERE showtimes.available_seats IS NULL
        ');
    }

    public function down(): void
    {
        Schema::table('showtimes', function (Blueprint $table) {
            $table->dropColumn('available_seats');
        });
    }
};
