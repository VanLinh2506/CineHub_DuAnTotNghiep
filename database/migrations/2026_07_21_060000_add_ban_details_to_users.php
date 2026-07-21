<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'ban_reason')) $table->text('ban_reason')->nullable()->after('is_active');
            if (!Schema::hasColumn('users', 'banned_at')) $table->timestamp('banned_at')->nullable()->after('ban_reason');
            if (!Schema::hasColumn('users', 'banned_by')) $table->unsignedBigInteger('banned_by')->nullable()->after('banned_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = collect(['ban_reason', 'banned_at', 'banned_by'])
                ->filter(fn ($column) => Schema::hasColumn('users', $column))->all();
            if ($columns) $table->dropColumn($columns);
        });
    }
};
