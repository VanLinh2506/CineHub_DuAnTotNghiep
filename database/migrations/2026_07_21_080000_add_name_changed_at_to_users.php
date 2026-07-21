<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'name_changed_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('name_changed_at')->nullable()->after('name');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('users', 'name_changed_at')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('name_changed_at');
            });
        }
    }
};
