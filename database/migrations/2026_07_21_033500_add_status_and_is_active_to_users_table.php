<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'status')) {
                $table->string('status', 20)->default('active')->after('role');
            }

            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('status');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'is_active')) {
                $table->dropColumn('is_active');
            }

            if (Schema::hasColumn('users', 'status')) {
                $table->dropColumn('status');
            }
        });
    }
};
