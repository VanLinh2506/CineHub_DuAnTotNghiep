<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('news_categories')) {
            Schema::create('news_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('slug')->unique();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('news')) {
            Schema::create('news', function (Blueprint $table) {
                $table->id();
                $table->foreignId('news_category_id')->nullable()->constrained('news_categories')->nullOnDelete();
                // The imported cinehub.users schema does not match Laravel's default key type.
                $table->unsignedBigInteger('user_id')->nullable()->index();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('thumbnail')->nullable();
                $table->text('excerpt')->nullable();
                $table->longText('content');
                $table->enum('status', ['draft', 'published'])->default('draft');
                $table->timestamp('published_at')->nullable();
                $table->string('wp_id')->nullable()->comment('ID bai viet goc tu WordPress');
                $table->timestamps();

                $table->index(['status', 'published_at']);
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('news');
        Schema::dropIfExists('news_categories');
    }
};
