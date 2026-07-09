<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('showtimes')) {
            return;
        }

        Schema::create('showtimes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('movie_id');
            $table->unsignedBigInteger('theater_id');
            $table->date('show_date');
            $table->time('show_time');
            $table->decimal('price', 10, 2);
            $table->integer('available_seats')->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->unsignedBigInteger('screen_id')->nullable();

            $table->unique(['screen_id', 'show_date', 'show_time'], 'unique_showtime');
            $table->index('movie_id', 'idx_movie');
            $table->index('theater_id', 'idx_theater');
            $table->index('screen_id', 'screen_id');
            $table->index(['theater_id', 'screen_id'], 'idx_theater_screen');
        });
    }

    public function down(): void
    {
        // Intentionally left as a no-op.
        // This project mixes SQL-imported schemas with migrations, so we avoid
        // dropping a shared table during rollback unless we can guarantee it
        // was created by this migration.
    }
};
