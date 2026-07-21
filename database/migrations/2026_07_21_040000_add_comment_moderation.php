<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('users', 'comment_banned_until')) {
            Schema::table('users', function (Blueprint $table) {
                $table->timestamp('comment_banned_until')->nullable()->after('status');
            });
        }

        if (!Schema::hasTable('comment_violations')) {
            Schema::create('comment_violations', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('user_id');
                $table->unsignedBigInteger('moderator_id')->nullable();
                $table->string('content_type', 20);
                $table->unsignedInteger('content_id');
                $table->string('reason', 500);
                $table->timestamps();

                $table->unique(['content_type', 'content_id']);
                $table->index(['user_id', 'created_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('comment_violations');

        if (Schema::hasColumn('users', 'comment_banned_until')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('comment_banned_until');
            });
        }
    }
};
