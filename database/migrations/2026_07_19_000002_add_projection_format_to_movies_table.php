<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('movies', 'projection_format')) {
            Schema::table('movies', function (Blueprint $table) {
                $table->string('projection_format', 10)->default('2D')->after('type');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('movies', 'projection_format')) {
            Schema::table('movies', fn (Blueprint $table) => $table->dropColumn('projection_format'));
        }
    }
};
