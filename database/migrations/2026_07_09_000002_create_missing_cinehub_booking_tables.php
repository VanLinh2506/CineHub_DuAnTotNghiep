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
        $this->createIfMissing('showtimes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('movie_id');
            $table->unsignedInteger('theater_id');
            $table->unsignedInteger('screen_id')->nullable();
            $table->date('show_date');
            $table->time('show_time');
            $table->decimal('price', 10, 2);
            $table->integer('available_seats')->nullable();
            $this->addTimestamps($table);

            $table->unique(['screen_id', 'show_date', 'show_time'], 'unique_showtime');
            $table->index('movie_id');
            $table->index('theater_id');
            $table->index('screen_id');
            $table->index(['theater_id', 'screen_id']);
        });

        $this->createIfMissing('watch_history', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('movie_id');
            $table->unsignedInteger('episode_id')->nullable();
            $table->integer('last_time')->default(0);
            $table->integer('watch_time')->default(0);
            $table->tinyInteger('rating')->nullable();
            $table->boolean('favorite')->default(false);
            $this->addTimestamps($table);

            $table->unique(['user_id', 'movie_id'], 'unique_user_movie');
            $table->index('user_id');
            $table->index('movie_id');
        });

        $this->createIfMissing('disabled_seats', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('screen_id');
            $table->string('seat_row', 2);
            $table->integer('seat_number');
            $table->string('reason')->nullable();
            $table->unsignedInteger('disabled_by')->nullable();
            $table->timestamp('disabled_at')->useCurrent();
            $table->timestamp('enabled_at')->nullable();
            $table->boolean('is_active')->default(true);
            $this->addTimestamps($table);

            $table->index('screen_id');
            $table->index(['screen_id', 'is_active']);
        });

        $this->createIfMissing('booking_pending', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('customer_name')->nullable();
            $table->string('customer_phone', 20)->nullable();
            $table->unsignedInteger('showtime_id');
            $table->text('seats');
            $table->text('food_items')->nullable();
            $table->string('customer_email')->nullable();
            $table->decimal('total_amount', 10, 2);
            $table->string('vnp_txn_ref', 100)->nullable();
            $table->string('booking_code', 100)->nullable();
            $table->string('status', 20)->default('pending');
            $table->timestamp('expires_at')->nullable();
            $table->string('qr_code')->nullable();
            $this->addTimestamps($table);

            $table->unique('vnp_txn_ref');
            $table->unique('booking_code');
            $table->index('user_id');
            $table->index('showtime_id');
            $table->index('status');
        });

        $this->createIfMissing('tickets', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('showtime_id');
            $table->unsignedInteger('booking_pending_id')->nullable();
            $table->string('seat', 10);
            $table->string('seat_type', 20)->default('normal');
            $table->string('qr_code')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('status', 20)->default('Đã đặt');
            $table->boolean('is_counter_sale')->default(false);
            $table->unsignedInteger('sold_by')->nullable();
            $table->boolean('is_picked_up')->default(false);
            $table->dateTime('picked_up_at')->nullable();
            $table->unsignedInteger('picked_up_by')->nullable();
            $this->addTimestamps($table);

            $table->unique(['showtime_id', 'seat'], 'ticket_seat_unique');
            $table->index('user_id');
            $table->index('showtime_id');
            $table->index('booking_pending_id');
            $table->index('status');
        });

        $this->createIfMissing('booking_food_items', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('ticket_id')->nullable();
            $table->unsignedInteger('booking_pending_id')->nullable();
            $table->unsignedInteger('food_item_id');
            $table->integer('quantity')->default(1);
            $table->decimal('price', 10, 2);
            $this->addTimestamps($table);

            $table->index('ticket_id');
            $table->index('booking_pending_id');
            $table->index('food_item_id');
        });

        $this->createIfMissing('booking_session_tracking', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->unsignedInteger('showtime_id');
            $table->unsignedInteger('screen_id');
            $table->dateTime('session_start');
            $table->dateTime('session_end')->nullable();
            $table->integer('total_duration_seconds')->default(0);
            $table->integer('violation_count')->default(0);
            $table->boolean('is_banned')->default(false);
            $table->dateTime('ban_until')->nullable();
            $this->addTimestamps($table);

            $table->index('user_id');
            $table->index('showtime_id');
            $table->index('screen_id');
            $table->index('session_start');
            $table->index('is_banned');
        });

        $this->createIfMissing('seat_reservations', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('showtime_id');
            $table->string('seat', 10);
            $table->unsignedInteger('user_id');
            $table->string('session_id')->nullable();
            $table->timestamp('reserved_at')->useCurrent();
            $table->timestamp('expires_at')->nullable();
            $this->addTimestamps($table);

            $table->unique(['showtime_id', 'seat'], 'seat_reservation_unique');
            $table->index('showtime_id');
            $table->index('user_id');
            $table->index('expires_at');
        });

        $this->createIfMissing('seat_selection_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('ip_address', 45)->nullable();
            $table->unsignedInteger('showtime_id');
            $table->integer('seat_count');
            $table->text('seats')->nullable();
            $table->boolean('is_spam')->default(false);
            $this->addTimestamps($table);

            $table->index('user_id');
            $table->index('showtime_id');
            $table->index('created_at');
        });

        $this->createIfMissing('ip_room_tracking', function (Blueprint $table) {
            $table->increments('id');
            $table->string('ip_address', 45);
            $table->unsignedInteger('screen_id');
            $table->unsignedInteger('showtime_id');
            $table->dateTime('first_enter_time');
            $table->dateTime('last_enter_time');
            $table->integer('total_duration_seconds')->default(0);
            $table->boolean('is_banned')->default(false);
            $table->dateTime('ban_until')->nullable();
            $this->addTimestamps($table);

            $table->index('ip_address');
            $table->index('screen_id');
            $table->index('showtime_id');
            $table->index('is_banned');
        });
    }

    public function down(): void
    {
        // No-op by design. These tables may contain imported data.
    }
};
