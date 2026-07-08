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
        if (!Schema::hasTable('tickets')) {
            return;
        }

        Schema::table('tickets', function (Blueprint $table) {
            // Thêm các cột picked_up nếu chưa tồn tại
            if (!Schema::hasColumn('tickets', 'picked_up_at')) {
                $table->timestamp('picked_up_at')->nullable();
            }
            if (!Schema::hasColumn('tickets', 'picked_up_by')) {
                $table->unsignedBigInteger('picked_up_by')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (!Schema::hasTable('tickets')) {
            return;
        }

        Schema::table('tickets', function (Blueprint $table) {
            if (Schema::hasColumn('tickets', 'picked_up_at')) {
                $table->dropColumn('picked_up_at');
            }
            if (Schema::hasColumn('tickets', 'picked_up_by')) {
                $table->dropColumn('picked_up_by');
            }
        });
    }
};
