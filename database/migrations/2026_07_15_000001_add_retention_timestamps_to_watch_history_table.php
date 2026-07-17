<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('watch_history')) {
            return;
        }

        Schema::table('watch_history', function (Blueprint $table) {
            if (!Schema::hasColumn('watch_history', 'episode_id')) {
                $table->unsignedBigInteger('episode_id')->nullable()->after('movie_id');
            }
            if (!Schema::hasColumn('watch_history', 'last_time')) {
                $table->unsignedInteger('last_time')->default(0)->after('episode_id');
            }
            if (!Schema::hasColumn('watch_history', 'playback_updated_at')) {
                $table->timestamp('playback_updated_at')->nullable()->after('last_time')->index();
            }
            if (!Schema::hasColumn('watch_history', 'episode_updated_at')) {
                $table->timestamp('episode_updated_at')->nullable()->after('playback_updated_at')->index();
            }
        });

        DB::table('watch_history')->where('last_time', '>', 0)->update([
            'playback_updated_at' => DB::raw('COALESCE(updated_at, created_at)'),
        ]);
        DB::table('watch_history')->whereNotNull('episode_id')->update([
            'episode_updated_at' => DB::raw('COALESCE(updated_at, created_at)'),
        ]);
    }

    public function down(): void
    {
        // Retain user progress on rollback.
    }
};
