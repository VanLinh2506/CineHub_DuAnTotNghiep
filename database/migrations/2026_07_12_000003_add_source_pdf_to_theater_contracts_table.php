<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('theater_contracts', function (Blueprint $table) {
            if (!Schema::hasColumn('theater_contracts', 'source_pdf_path')) {
                $table->string('source_pdf_path')->nullable()->after('pdf_path');
            }
            if (!Schema::hasColumn('theater_contracts', 'extracted_text')) {
                $table->longText('extracted_text')->nullable()->after('source_pdf_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('theater_contracts', function (Blueprint $table) {
            $table->dropColumn(['source_pdf_path', 'extracted_text']);
        });
    }
};
