<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function createIfMissing(string $table, callable $definition): void
    {
        if (!Schema::hasTable($table)) {
            Schema::create($table, $definition);
        }
    }

    private function addTimestamps(Blueprint $table): void
    {
        $table->timestamp('created_at')->useCurrent();
        $table->timestamp('updated_at')->nullable();
    }

    public function up(): void
    {
        $this->createIfMissing('categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->unsignedInteger('parent_id')->nullable();
            $this->addTimestamps($table);

            $table->index('parent_id');
        });

        $this->createIfMissing('movie_category', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('movie_id');
            $table->unsignedInteger('category_id');
            $this->addTimestamps($table);

            $table->unique(['movie_id', 'category_id'], 'movie_category_unique');
            $table->index('movie_id');
            $table->index('category_id');
        });

        $this->createIfMissing('theaters', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('location')->nullable();
            $table->text('address')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('image')->nullable();
            $table->integer('total_screens')->default(1);
            $table->boolean('is_active')->default(true);
            $this->addTimestamps($table);

            $table->index('is_active');
        });

        $this->createIfMissing('theater_screens', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('theater_id');
            $table->string('screen_name');
            $table->string('screen_number')->nullable();
            $table->integer('total_seats');
            $table->text('seat_layout')->nullable();
            $table->text('seat_layout_config')->nullable();
            $table->string('screen_type', 20)->default('2D');
            $table->boolean('is_active')->default(true);
            $this->addTimestamps($table);

            $table->index('theater_id');
            $table->index(['theater_id', 'screen_name']);
        });

        $this->createIfMissing('movies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title');
            $table->unsignedInteger('category_id')->nullable();
            $table->string('level', 20)->default('Free');
            $table->integer('duration')->nullable();
            $table->text('description')->nullable();
            $table->string('director', 100)->nullable();
            $table->text('cast')->nullable();
            $table->text('actors')->nullable();
            $table->string('video_url')->nullable();
            $table->string('trailer_url')->nullable();
            $table->string('thumbnail')->nullable();
            $table->string('banner')->nullable();
            $table->string('status', 30)->default('Sắp chiếu');
            $table->float('rating')->default(0);
            $table->string('status_admin', 20)->default('draft');
            $table->dateTime('publish_date')->nullable();
            $table->text('geo_restriction')->nullable();
            $table->boolean('drm_enabled')->default(false);
            $table->string('country', 100)->nullable();
            $table->string('language', 50)->nullable();
            $table->string('age_rating', 10)->nullable();
            $table->string('type', 20)->default('phimle');
            $table->integer('max_tickets')->nullable();
            $table->integer('normal_price')->default(90000);
            $table->integer('vip_price')->default(120000);
            $table->integer('couple_price')->default(180000);
            $this->addTimestamps($table);

            $table->index('category_id');
            $table->index('status');
            $table->index('status_admin');
            $table->index('type');
        });

        $this->createIfMissing('episodes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('movie_id');
            $table->integer('episode_number');
            $table->string('title')->nullable();
            $table->string('video_url')->nullable();
            $table->string('thumbnail')->nullable();
            $table->integer('duration')->nullable();
            $table->text('description')->nullable();
            $this->addTimestamps($table);

            $table->unique(['movie_id', 'episode_number'], 'episode_unique');
            $table->index('movie_id');
        });

        $this->createIfMissing('roles', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->text('description')->nullable();
            $this->addTimestamps($table);

            $table->unique('name');
        });

        $this->createIfMissing('permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 100);
            $table->text('description')->nullable();
            $table->string('module', 50);
            $this->addTimestamps($table);

            $table->unique('name');
        });

        $this->createIfMissing('role_permissions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('role_id');
            $table->unsignedInteger('permission_id');
            $this->addTimestamps($table);

            $table->unique(['role_id', 'permission_id'], 'role_permission_unique');
            $table->index('role_id');
            $table->index('permission_id');
        });

        $this->createIfMissing('user_roles', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('role_id');
            $this->addTimestamps($table);

            $table->unique(['user_id', 'role_id'], 'user_role_unique');
            $table->index('user_id');
            $table->index('role_id');
        });

        $this->createIfMissing('subscriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 50);
            $table->decimal('price', 10, 2)->default(0);
            $table->text('description')->nullable();
            $table->text('benefits')->nullable();
            $this->addTimestamps($table);

            $table->unique('name');
        });

        $this->createIfMissing('user_tokens', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('token');
            $table->string('device_info', 500)->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamp('expires_at')->nullable();
            $this->addTimestamps($table);

            $table->unique('token');
            $table->index('user_id');
        });

        $this->createIfMissing('food_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('theater_id')->nullable();
            $table->string('name');
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2);
            $table->string('image')->nullable();
            $table->string('type', 20)->default('combo');
            $table->boolean('is_active')->default(true);
            $this->addTimestamps($table);

            $table->index('theater_id');
            $table->index('is_active');
            $table->index('type');
        });

        $this->createIfMissing('reviews', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('movie_id');
            $table->tinyInteger('rating')->nullable();
            $table->text('comment')->nullable();
            $table->boolean('is_pinned')->default(false);
            $this->addTimestamps($table);

            $table->unique(['user_id', 'movie_id'], 'review_unique');
            $table->index('movie_id');
            $table->index('user_id');
        });

        $this->createIfMissing('comments', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('movie_id');
            $table->unsignedInteger('parent_id')->nullable();
            $table->text('content');
            $table->string('status', 20)->default('pending');
            $table->integer('likes')->default(0);
            $table->integer('dislikes')->default(0);
            $this->addTimestamps($table);

            $table->index('movie_id');
            $table->index('parent_id');
            $table->index('status');
            $table->index('user_id');
        });

        $this->createIfMissing('comment_likes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('comment_id');
            $table->unsignedInteger('user_id');
            $table->string('type', 20);
            $this->addTimestamps($table);

            $table->unique(['comment_id', 'user_id'], 'comment_like_unique');
            $table->index('comment_id');
            $table->index('user_id');
        });

        $this->createIfMissing('notifications', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('type', 50)->default('info');
            $table->string('title');
            $table->text('message');
            $table->string('link')->nullable();
            $table->boolean('is_read')->default(false);
            $this->addTimestamps($table);

            $table->index('user_id');
            $table->index('is_read');
        });

        $this->createIfMissing('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('type', 20);
            $table->unsignedInteger('related_id')->nullable();
            $table->decimal('amount', 10, 2);
            $table->string('method', 50)->nullable();
            $table->string('status', 20)->default('Thành công');
            $this->addTimestamps($table);

            $table->index('user_id');
            $table->index('type');
            $table->index('status');
        });

        $this->createIfMissing('moderator_permission_requests', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('theater_id');
            $table->unsignedInteger('moderator_id')->nullable();
            $table->unsignedInteger('requested_by');
            $table->unsignedInteger('target_user_id');
            $table->string('action', 50);
            $table->text('old_data')->nullable();
            $table->text('new_data')->nullable();
            $table->string('status', 20)->default('pending');
            $table->dateTime('responded_at')->nullable();
            $this->addTimestamps($table);

            $table->index('theater_id');
            $table->index('moderator_id');
            $table->index('requested_by');
            $table->index('status');
            $table->index('target_user_id');
        });

        $this->createIfMissing('admin_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('action', 100);
            $table->string('module', 50);
            $table->string('target_type', 50)->nullable();
            $table->unsignedInteger('target_id')->nullable();
            $table->text('old_data')->nullable();
            $table->text('new_data')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $this->addTimestamps($table);

            $table->index('user_id');
            $table->index('module');
            $table->index('created_at');
        });

        $this->createIfMissing('promotions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('type', 20);
            $table->decimal('discount_value', 10, 2)->nullable();
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->string('status', 20)->default('draft');
            $table->string('target_audience', 20)->default('all');
            $this->addTimestamps($table);

            $table->index('status');
            $table->index('type');
        });

        $this->createIfMissing('support_tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('subject');
            $table->text('message');
            $table->string('status', 30)->default('Mới');
            $table->string('priority', 30)->default('Trung bình');
            $table->string('tags', 255)->nullable();
            $table->unsignedInteger('assigned_to')->nullable();
            $this->addTimestamps($table);

            $table->index('user_id');
            $table->index('assigned_to');
            $table->index('status');
        });

        $this->createIfMissing('coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 50);
            $table->string('name');
            $table->string('type', 20);
            $table->decimal('value', 10, 2);
            $table->decimal('min_amount', 10, 2)->default(0);
            $table->decimal('max_discount', 10, 2)->nullable();
            $table->integer('usage_limit')->nullable();
            $table->integer('used_count')->default(0);
            $table->dateTime('valid_from');
            $table->dateTime('valid_to');
            $table->string('status', 20)->default('active');
            $this->addTimestamps($table);

            $table->unique('code');
            $table->index('status');
            $table->index('type');
        });

        $this->createIfMissing('ip_blocks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ip_address', 45);
            $table->dateTime('expires_at')->nullable();
            $table->text('reason')->nullable();
            $this->addTimestamps($table);

            $table->unique('ip_address');
        });
    }

    public function down(): void
    {
        // No-op by design. These tables may contain imported data.
    }
};
