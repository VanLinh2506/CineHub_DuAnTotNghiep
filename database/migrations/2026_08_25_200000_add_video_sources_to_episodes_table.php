<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('episodes', function (Blueprint $table) {
            $table->json('video_sources')->nullable()->after('video_url');
        });
    }

    public function down(): void
    {
        Schema::table('episodes', fn (Blueprint $table) => $table->dropColumn('video_sources'));
    }
};
