<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tables already exist from SQL import
        // Just verify they exist
    }

    public function down(): void
    {
        // Don't drop - contains imported data
    }
};
