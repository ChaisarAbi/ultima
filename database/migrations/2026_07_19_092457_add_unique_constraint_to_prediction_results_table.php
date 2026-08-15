<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prediction_results', function (Blueprint $table) {
            // Add unique constraint for (target_date, metric)
            $table->unique(['target_date', 'metric']);
        });
    }

    public function down(): void
    {
        Schema::table('prediction_results', function (Blueprint $table) {
            $table->dropUnique(['target_date', 'metric']);
        });
    }
};