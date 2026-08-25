<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Thêm cột max_devices vào bảng users.
 *
 * Cột này quy định số lượng thiết bị tối đa được phép
 * stream video đồng thời cho mỗi tài khoản.
 * Giá trị mặc định = 1 (phù hợp với gói cơ bản).
 * Admin có thể nâng lên theo gói cước (ví dụ: 2, 4).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedTinyInteger('max_devices')
                  ->default(1)
                  ->after('subscription_id')
                  ->comment('Số thiết bị stream đồng thời tối đa');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('max_devices');
        });
    }
};
