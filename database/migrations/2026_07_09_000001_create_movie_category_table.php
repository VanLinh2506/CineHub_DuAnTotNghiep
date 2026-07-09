<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('movie_category')) {
            Schema::create('movie_category', function (Blueprint $table) {
                $table->id();
                $table->integer('movie_id');
                $table->integer('category_id');
                $table->timestamps();

                $table->unique(['movie_id', 'category_id']);
                $table->foreign('movie_id')->references('id')->on('movies')->cascadeOnDelete();
                $table->foreign('category_id')->references('id')->on('categories')->cascadeOnDelete();
            });
        }

        if (Schema::hasColumn('movies', 'category_id')) {
            DB::table('movies')
                ->whereNotNull('category_id')
                ->orderBy('id')
                ->select(['id', 'category_id'])
                ->chunk(200, function ($movies) {
                    foreach ($movies as $movie) {
                        DB::table('movie_category')->updateOrInsert(
                            [
                                'movie_id' => $movie->id,
                                'category_id' => $movie->category_id,
                            ],
                            [
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]
                        );
                    }
                });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('movie_category');
    }
};
