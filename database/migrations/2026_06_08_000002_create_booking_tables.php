<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Booking pending
        if (!Schema::hasTable('booking_pending')) {
            Schema::create('booking_pending', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('showtime_id')->constrained()->cascadeOnDelete();
                $table->json('seats');
                $table->json('food_items')->nullable();
                $table->string('customer_name')->nullable();
                $table->string('customer_email')->nullable();
                $table->string('customer_phone')->nullable();
                $table->decimal('total_amount', 10, 2)->default(0);
                $table->string('vnp_txn_ref')->nullable()->unique();
                $table->string('qr_code')->nullable();
                $table->string('status')->default('pending');
                $table->timestamp('expires_at')->nullable();
                $table->timestamps();
            });
        }

        // Tickets
        if (!Schema::hasTable('tickets')) {
            Schema::create('tickets', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
                $table->foreignId('showtime_id')->constrained()->cascadeOnDelete();
                $table->foreignId('booking_pending_id')->nullable()->constrained('booking_pending')->nullOnDelete();
                $table->string('seat');
                $table->string('seat_type')->default('normal');
                $table->decimal('price', 10, 2);
                $table->string('qr_code')->nullable()->unique();
                $table->string('status')->default('Đã đặt');
                $table->boolean('is_counter_sale')->default(false);
                $table->foreignId('sold_by')->nullable()->constrained('users')->nullOnDelete();
                $table->boolean('is_picked_up')->default(false);
                $table->timestamp('picked_up_at')->nullable();
                $table->foreignId('picked_up_by')->nullable()->constrained('users')->nullOnDelete();
                $table->timestamps();
            });
        }

        // Transactions
        if (!Schema::hasTable('transactions')) {
            Schema::create('transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->string('type')->default('ticket');
                $table->unsignedBigInteger('related_id')->nullable();
                $table->decimal('amount', 10, 2);
                $table->string('method')->nullable();
                $table->string('status')->default('Thành công');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('tickets');
        Schema::dropIfExists('booking_pending');
    }
};
