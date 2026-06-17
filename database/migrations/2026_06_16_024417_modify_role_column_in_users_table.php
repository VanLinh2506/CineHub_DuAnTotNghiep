<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // SQLite không hỗ trợ MODIFY COLUMN — role đã là VARCHAR từ migration users
        // Với MySQL: bỏ comment dòng dưới
        // DB::statement("ALTER TABLE users MODIFY COLUMN role VARCHAR(50) DEFAULT 'user'");
    }

    public function down(): void {}
};
