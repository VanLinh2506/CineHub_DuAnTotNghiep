<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('theater_contracts', function (Blueprint $table) {
            $table->unsignedInteger('bestseller_price_min')->default(90000)->after('end_date');
            $table->unsignedInteger('bestseller_price_max')->default(100000)->after('bestseller_price_min');
            $table->unsignedInteger('new_release_price_min')->default(100000)->after('bestseller_price_max');
            $table->unsignedInteger('new_release_price_max')->default(120000)->after('new_release_price_min');
        });
    }

    public function down(): void
    {
        Schema::table('theater_contracts', function (Blueprint $table) {
            $table->dropColumn(['bestseller_price_min', 'bestseller_price_max', 'new_release_price_min', 'new_release_price_max']);
        });
    }
};
