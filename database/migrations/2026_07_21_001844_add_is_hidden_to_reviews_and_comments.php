<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->boolean('is_hidden')->default(false)->after('is_pinned');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->boolean('is_hidden')->default(false)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $table) {
            $table->dropColumn('is_hidden');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('is_hidden');
        });
    }
};
