<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('watch_history')) {
            return;
        }

        Schema::create('watch_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('movie_id');
            $table->integer('last_time')->default(0);
            $table->tinyInteger('rating')->nullable();
            $table->boolean('favorite')->default(false);
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable();

            $table->unique(['user_id', 'movie_id'], 'unique_user_movie');
            $table->index('user_id', 'idx_wh_user');
            $table->index('movie_id', 'idx_wh_movie');
        });
    }

    public function down(): void
    {
        // Keep rollback conservative because the table may come from imported SQL.
    }
};
