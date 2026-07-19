<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('theater_contracts', function (Blueprint $table) {
            if (!Schema::hasColumn('theater_contracts', 'hot_movie_price_min')) {
                $table->unsignedInteger('hot_movie_price_min')->default(120000)->after('new_release_price_max');
            }

            if (!Schema::hasColumn('theater_contracts', 'hot_movie_price_max')) {
                $table->unsignedInteger('hot_movie_price_max')->default(150000)->after('hot_movie_price_min');
            }

            if (!Schema::hasColumn('theater_contracts', 'party_terms')) {
                $table->text('party_terms')->nullable()->after('auto_revoke_terms');
            }
        });
    }

    public function down(): void
    {
        Schema::table('theater_contracts', function (Blueprint $table) {
            $columns = collect(['hot_movie_price_min', 'hot_movie_price_max', 'party_terms'])
                ->filter(fn ($column) => Schema::hasColumn('theater_contracts', $column))
                ->all();

            if ($columns) {
                $table->dropColumn($columns);
            }
        });
    }
};
