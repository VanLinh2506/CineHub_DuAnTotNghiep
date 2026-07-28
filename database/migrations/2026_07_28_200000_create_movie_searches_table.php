<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('movie_searches')) {
            return;
        }

        Schema::create('movie_searches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('movie_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('keyword', 255);
            $table->timestamp('searched_at')->useCurrent();

            $table->index(['movie_id', 'searched_at'], 'movie_searches_movie_date_index');
            $table->index(['user_id', 'searched_at'], 'movie_searches_user_date_index');
            $table->index('searched_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movie_searches');
    }
};
