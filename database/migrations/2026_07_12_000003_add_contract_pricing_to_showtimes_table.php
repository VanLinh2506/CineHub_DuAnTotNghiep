<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('showtimes', function (Blueprint $table) {
            $table->unsignedBigInteger('theater_contract_id')->nullable()->after('theater_id')->index();
            $table->string('contract_price_type', 30)->nullable()->after('price');
        });
    }

    public function down(): void
    {
        Schema::table('showtimes', function (Blueprint $table) {
            $table->dropColumn(['theater_contract_id', 'contract_price_type']);
        });
    }
};
