<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users') || Schema::hasColumn('users', 'theater_id')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            $table->unsignedInteger('theater_id')->nullable()->after('role');
            $table->index('theater_id');
        });
    }

    public function down(): void
    {
        // No-op rollback: imported databases may already own this shared column.
    }
};
