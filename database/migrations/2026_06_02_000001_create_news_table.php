<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('news_categories')) {
            Schema::create('news_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('news') && Schema::hasTable('news_categories') && Schema::hasTable('users')) {
            Schema::create('news', function (Blueprint $table) {
                $table->id();
                // Avoid engine-specific foreign key issues on imported/local MySQL setups.
                $table->unsignedBigInteger('news_category_id')->nullable();
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('thumbnail')->nullable();
                $table->text('excerpt')->nullable();
                $table->longText('content');
                $table->enum('status', ['draft', 'published'])->default('draft');
                $table->timestamp('published_at')->nullable();
                $table->string('wp_id')->nullable()->comment('ID bai viet goc tu WordPress');
                $table->timestamps();

                $table->index('news_category_id');
                $table->index('user_id');
                $table->index(['status', 'published_at']);
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('news')) {
            Schema::dropIfExists('news');
        }

        if (Schema::hasTable('news_categories')) {
            Schema::dropIfExists('news_categories');
        }
    }
};
