<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('movies', function (Blueprint $table) {
            $table->string('projection_format', 10)->nullable()->default(null)->change();
        });

        DB::table('movies')
            ->where('status', '!=', 'Chiếu rạp')
            ->update(['projection_format' => null]);

        DB::table('movies')
            ->where('type', 'phimle')
            ->where('status', 'Sắp chiếu')
            ->whereNotNull('publish_date')
            ->update(['scheduled_status' => 'Chiếu online']);
    }

    public function down(): void
    {
        DB::table('movies')
            ->whereNull('projection_format')
            ->update(['projection_format' => '2D']);

        Schema::table('movies', function (Blueprint $table) {
            $table->string('projection_format', 10)->default('2D')->nullable(false)->change();
        });
    }
};
