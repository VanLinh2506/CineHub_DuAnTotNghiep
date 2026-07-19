<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('transactions') || !Schema::hasColumn('transactions', 'method')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::statement(
                "ALTER TABLE `transactions` MODIFY `method` VARCHAR(50) NULL DEFAULT 'Momo'"
            );
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('transactions') || !Schema::hasColumn('transactions', 'method')) {
            return;
        }

        if (DB::getDriverName() === 'mysql') {
            DB::table('transactions')->where('method', 'VNPay')->update(['method' => 'Momo']);
            DB::table('transactions')->where('method', 'Points')->update(['method' => 'Bank']);
            DB::table('transactions')->where('method', 'Tiền mặt')->update(['method' => 'Cash']);

            DB::statement(
                "ALTER TABLE `transactions` MODIFY `method` " .
                "ENUM('Momo','ZaloPay','Stripe','Bank','Cash') NULL DEFAULT 'Momo'"
            );
        }
    }
};
