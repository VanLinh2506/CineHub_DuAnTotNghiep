<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Categories
        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->timestamps();
            });
        }

        // Movies
        if (!Schema::hasTable('movies')) {
            Schema::create('movies', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
                $table->string('title');
                $table->string('slug')->nullable()->unique();
                $table->text('description')->nullable();
                $table->string('thumbnail')->nullable();
                $table->string('banner')->nullable();
                $table->string('video_url')->nullable();
                $table->string('trailer_url')->nullable();
                $table->float('rating')->default(0);
                $table->integer('duration')->default(90)->comment('phút');
                $table->date('release_date')->nullable();
                $table->string('country')->nullable();
                $table->string('director')->nullable();
                $table->text('cast')->nullable();
                $table->text('actors')->nullable();
                $table->string('status')->default('Chiếu online');
                $table->string('status_admin')->default('published');
                $table->string('type')->nullable()->comment('phimle, phimbo');
                $table->string('level')->nullable();
                $table->integer('total_episodes')->default(1);
                $table->string('language')->nullable();
                $table->string('age_rating')->nullable();
                $table->timestamps();
            });
        }

        // Episodes
        if (!Schema::hasTable('episodes')) {
            Schema::create('episodes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('movie_id')->constrained()->cascadeOnDelete();
                $table->integer('episode_number')->default(1);
                $table->string('title')->nullable();
                $table->string('video_url')->nullable();
                $table->integer('duration')->nullable();
                $table->timestamps();
            });
        }

        // Roles
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('description')->nullable();
                $table->timestamps();
            });
        }

        // Watch history
        if (!Schema::hasTable('watch_history')) {
            Schema::create('watch_history', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('movie_id')->constrained()->cascadeOnDelete();
                $table->integer('progress')->default(0);
                $table->boolean('favorite')->default(false);
                $table->timestamp('created_at')->nullable();
                $table->timestamp('updated_at')->nullable();
            });
        }

        // Reviews
        if (!Schema::hasTable('reviews')) {
            Schema::create('reviews', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('movie_id')->constrained()->cascadeOnDelete();
                $table->tinyInteger('rating')->unsigned();
                $table->text('content')->nullable();
                $table->timestamps();
            });
        }

        // Comments
        if (!Schema::hasTable('comments')) {
            Schema::create('comments', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('movie_id')->constrained()->cascadeOnDelete();
                $table->text('content');
                $table->foreignId('parent_id')->nullable()->constrained('comments')->nullOnDelete();
                $table->timestamps();
            });
        }

        // Comment likes
        if (!Schema::hasTable('comment_likes')) {
            Schema::create('comment_likes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('comment_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
                $table->unique(['user_id', 'comment_id']);
            });
        }

        // Theaters
        if (!Schema::hasTable('theaters')) {
            Schema::create('theaters', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('address')->nullable();
                $table->string('city')->nullable();
                $table->string('phone')->nullable();
                $table->timestamps();
            });
        }

        // Theater screens
        if (!Schema::hasTable('theater_screens')) {
            Schema::create('theater_screens', function (Blueprint $table) {
                $table->id();
                $table->foreignId('theater_id')->constrained()->cascadeOnDelete();
                $table->string('screen_name');
                $table->integer('screen_number')->nullable();
                $table->string('screen_type')->default('2D');
                $table->integer('total_seats')->default(100);
                $table->text('seat_layout_config')->nullable();
                $table->timestamps();
            });
        }

        // Showtimes
        if (!Schema::hasTable('showtimes')) {
            Schema::create('showtimes', function (Blueprint $table) {
                $table->id();
                $table->foreignId('movie_id')->constrained()->cascadeOnDelete();
                $table->foreignId('theater_id')->constrained()->cascadeOnDelete();
                $table->foreignId('screen_id')->references('id')->on('theater_screens')->cascadeOnDelete();
                $table->date('show_date');
                $table->time('show_time');
                $table->decimal('price', 10, 2)->default(90000);
                $table->integer('available_seats')->default(100);
                $table->timestamps();
            });
        }

        // Food items
        if (!Schema::hasTable('food_items')) {
            Schema::create('food_items', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('type')->default('food')->comment('food, drink, combo');
                $table->decimal('price', 10, 2);
                $table->text('description')->nullable();
                $table->string('image')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Subscriptions
        if (!Schema::hasTable('subscriptions')) {
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('plan')->default('basic');
                $table->timestamp('starts_at')->nullable();
                $table->timestamp('ends_at')->nullable();
                $table->string('status')->default('active');
                $table->timestamps();
            });
        }

        // Notifications
        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('title');
                $table->text('message')->nullable();
                $table->string('type')->nullable();
                $table->boolean('is_read')->default(false);
                $table->timestamps();
            });
        }

        // User tokens
        if (!Schema::hasTable('user_tokens')) {
            Schema::create('user_tokens', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('token')->unique();
                $table->string('type')->default('access');
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('user_tokens');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('food_items');
        Schema::dropIfExists('showtimes');
        Schema::dropIfExists('theater_screens');
        Schema::dropIfExists('theaters');
        Schema::dropIfExists('comment_likes');
        Schema::dropIfExists('comments');
        Schema::dropIfExists('reviews');
        Schema::dropIfExists('watch_history');
        Schema::dropIfExists('roles');
        Schema::dropIfExists('episodes');
        Schema::dropIfExists('movies');
        Schema::dropIfExists('categories');
    }
};
