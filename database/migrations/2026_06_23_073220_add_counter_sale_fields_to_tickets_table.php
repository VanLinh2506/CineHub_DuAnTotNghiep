<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            // Chỉ thêm 2 cột thiếu (is_picked_up và picked_up_* đã có rồi)
            if (!Schema::hasColumn('tickets', 'is_counter_sale')) {
                $table->boolean('is_counter_sale')->default(false);
            }
            if (!Schema::hasColumn('tickets', 'sold_by')) {
                $table->unsignedBigInteger('sold_by')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'is_counter_sale')) {
                $table->dropColumn('is_counter_sale');
            }
            if (Schema::hasColumn('tickets', 'sold_by')) {
                $table->dropColumn('sold_by');
            }
        });
    }
};
