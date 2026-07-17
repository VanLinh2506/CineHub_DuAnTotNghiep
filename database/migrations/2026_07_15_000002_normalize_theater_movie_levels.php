<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('movies') || !Schema::hasColumn('movies', 'level')) {
            return;
        }

        DB::table('movies')
            ->where('status', 'Chiếu rạp')
            ->where('level', '!=', 'Free')
            ->update(['level' => 'Free']);
    }

    public function down(): void
    {
        // The previous subscription level cannot be reconstructed safely.
    }
};
