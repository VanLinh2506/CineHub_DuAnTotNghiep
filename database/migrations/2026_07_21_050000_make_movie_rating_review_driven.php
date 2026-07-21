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
            $table->float('rating')->nullable()->default(null)->change();
        });

        // Remove imported/admin defaults, then rebuild the value exclusively
        // from visible user reviews.
        DB::table('movies')->update(['rating' => null]);

        DB::table('reviews')
            ->select('movie_id', DB::raw('ROUND(AVG(rating), 1) as average_rating'))
            ->where('is_hidden', false)
            ->whereNotNull('rating')
            ->groupBy('movie_id')
            ->orderBy('movie_id')
            ->chunk(500, function ($ratings) {
                foreach ($ratings as $rating) {
                    DB::table('movies')
                        ->where('id', $rating->movie_id)
                        ->update(['rating' => $rating->average_rating]);
                }
            });
    }

    public function down(): void
    {
        DB::table('movies')->whereNull('rating')->update(['rating' => 0]);

        Schema::table('movies', function (Blueprint $table) {
            $table->float('rating')->default(0)->nullable(false)->change();
        });
    }
};
