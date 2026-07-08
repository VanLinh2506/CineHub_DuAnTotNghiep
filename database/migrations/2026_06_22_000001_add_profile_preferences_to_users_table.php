<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'address')) {
                $column = $table->string('address')->nullable();

                if (Schema::hasColumn('users', 'birthdate')) {
                    $column->after('birthdate');
                }
            }

            if (!Schema::hasColumn('users', 'newsletter')) {
                $column = $table->boolean('newsletter')->default(false);

                if (Schema::hasColumn('users', 'address')) {
                    $column->after('address');
                }
            }

            if (!Schema::hasColumn('users', 'notifications_enabled')) {
                $column = $table->boolean('notifications_enabled')->default(true);

                if (Schema::hasColumn('users', 'newsletter')) {
                    $column->after('newsletter');
                }
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('users')) {
            return;
        }

        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'notifications_enabled')) {
                $table->dropColumn('notifications_enabled');
            }

            if (Schema::hasColumn('users', 'newsletter')) {
                $table->dropColumn('newsletter');
            }

            if (Schema::hasColumn('users', 'address')) {
                $table->dropColumn('address');
            }
        });
    }
};
