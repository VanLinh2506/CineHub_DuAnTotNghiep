<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('movie_category') || !Schema::hasTable('movie_categories')) {
            return;
        }

        DB::table('movie_categories')
            ->select(['movie_id', 'category_id', 'created_at'])
            ->orderBy('id')
            ->chunk(500, function ($rows) {
                $records = $rows->map(function ($row) {
                    return [
                        'movie_id' => $row->movie_id,
                        'category_id' => $row->category_id,
                        'created_at' => $row->created_at ?? now(),
                        'updated_at' => now(),
                    ];
                })->all();

                DB::table('movie_category')->insertOrIgnore($records);
            });
    }

    public function down(): void
    {
        //
    }
};
