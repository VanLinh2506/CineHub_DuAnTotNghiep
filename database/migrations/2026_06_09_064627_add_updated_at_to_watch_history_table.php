<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('watch_history')) {
            return;
        }

        Schema::table('watch_history', function (Blueprint $table) {
            if (! Schema::hasColumn('watch_history', 'updated_at')) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            }
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('watch_history') && Schema::hasColumn('watch_history', 'updated_at')) {
            Schema::table('watch_history', function (Blueprint $table) {
                $table->dropColumn('updated_at');
            });
        }
    }
};
