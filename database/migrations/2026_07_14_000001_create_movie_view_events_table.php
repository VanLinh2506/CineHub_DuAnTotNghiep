<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('movie_view_events')) {
            return;
        }

        Schema::create('movie_view_events', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('movie_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('episode_id')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['movie_id', 'created_at'], 'movie_views_movie_date_index');
            $table->index(['user_id', 'created_at'], 'movie_views_user_date_index');
        });

        // Keep existing history as the initial popularity baseline. From this
        // migration onward, every successful watch-page visit is a new event.
        if (Schema::hasTable('watch_history')) {
            DB::table('movie_view_events')->insertUsing(
                ['movie_id', 'user_id', 'episode_id', 'created_at'],
                DB::table('watch_history')->selectRaw(
                    'movie_id, user_id, NULL, COALESCE(updated_at, created_at)'
                )
            );
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('movie_view_events');
    }
};
