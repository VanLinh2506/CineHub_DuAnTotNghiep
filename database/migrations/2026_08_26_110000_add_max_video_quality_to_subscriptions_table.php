<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->unsignedSmallInteger('max_video_quality')->default(480)->after('access_level');
        });

        DB::table('subscriptions')->whereIn('access_level', ['free', 'basic'])->update(['max_video_quality' => 480]);
        DB::table('subscriptions')->where('access_level', 'silver')->update(['max_video_quality' => 720]);
        DB::table('subscriptions')->where('access_level', 'gold')->update(['max_video_quality' => 1080]);
        DB::table('subscriptions')->where('access_level', 'premium')->update(['max_video_quality' => 2160]);
    }

    public function down(): void
    {
        Schema::table('subscriptions', fn (Blueprint $table) => $table->dropColumn('max_video_quality'));
    }
};
