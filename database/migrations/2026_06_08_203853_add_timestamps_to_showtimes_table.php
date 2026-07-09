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
        if (!Schema::hasTable('showtimes')) {
            return;
        }

        // `showtimes` only uses `created_at` in this project.
        if (!Schema::hasColumn('showtimes', 'created_at')) {
            Schema::table('showtimes', function (Blueprint $table) {
                $table->timestamp('created_at')->useCurrent()->after('price');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // No-op rollback: this project mixes SQL-imported schema with migrations,
        // so we avoid dropping shared columns during rollback.
    }
};
