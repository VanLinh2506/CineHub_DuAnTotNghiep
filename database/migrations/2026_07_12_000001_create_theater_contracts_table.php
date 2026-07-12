<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('theater_contracts')) {
            return;
        }

        Schema::create('theater_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('contract_code', 50)->unique();
            $table->unsignedInteger('theater_id');
            $table->unsignedInteger('representative_user_id');
            $table->unsignedInteger('super_admin_id')->nullable();
            $table->unsignedBigInteger('renewed_from_id')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->json('admin_permissions')->nullable();
            $table->text('auto_revoke_terms')->nullable();
            $table->string('super_admin_signature')->nullable();
            $table->string('representative_signature')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('status', 30)->default('pending');
            $table->timestamp('activated_at')->nullable();
            $table->timestamp('revoked_at')->nullable();
            $table->timestamps();

            $table->index('theater_id');
            $table->index('representative_user_id');
            $table->index('super_admin_id');
            $table->index('renewed_from_id');
            $table->index(['status', 'start_date', 'end_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('theater_contracts');
    }
};
