<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Bảng showtimes đã có timestamps từ migration core — bỏ qua
        if (Schema::hasTable('showtimes') && !Schema::hasColumn('showtimes', 'created_at')) {
            Schema::table('showtimes', function (Blueprint $table) {
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('showtimes') && Schema::hasColumn('showtimes', 'created_at')) {
            Schema::table('showtimes', function (Blueprint $table) {
                $table->dropTimestamps();
            });
        }
    }
};
