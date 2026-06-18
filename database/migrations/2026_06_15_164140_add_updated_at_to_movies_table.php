<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('movies')) {
            return;
        }

        if (! Schema::hasColumn('movies', 'updated_at')) {
            Schema::table('movies', function (Blueprint $table) {
                $table->timestamp('updated_at')->nullable()->after('created_at');
            });
        }

        DB::table('movies')->whereNull('updated_at')->update([
            'updated_at' => DB::raw('created_at'),
        ]);
    }

    public function down(): void
    {
        if (Schema::hasTable('movies') && Schema::hasColumn('movies', 'updated_at')) {
            Schema::table('movies', function (Blueprint $table) {
                $table->dropColumn('updated_at');
            });
        }
    }
};
