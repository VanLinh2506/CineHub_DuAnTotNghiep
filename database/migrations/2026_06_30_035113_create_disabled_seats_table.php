<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('disabled_seats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('screen_id');
            $table->string('seat_row', 2); // A, B, C...
            $table->integer('seat_number'); // 1, 2, 3...
            $table->string('reason')->nullable(); // Lý do khóa ghế
            $table->unsignedBigInteger('disabled_by')->nullable(); // User ID người khóa
            $table->timestamp('disabled_at')->useCurrent();
            $table->timestamp('enabled_at')->nullable(); // Khi mở khóa
            $table->boolean('is_active')->default(true); // true = đang khóa
            
            $table->index('screen_id');
            $table->index(['screen_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('disabled_seats');
    }
};
