<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('report_archives', function (Blueprint $table) {
            // Make report_date nullable with no default required
            $table->date('report_date')->nullable()->change();
            
            // Add missing columns
            $table->string('period')->nullable()->after('report_data');
            $table->integer('month')->nullable()->after('period');
            $table->integer('year')->nullable()->after('month');
        });
    }

    public function down(): void
    {
        Schema::table('report_archives', function (Blueprint $table) {
            $table->dropColumn(['period', 'month', 'year']);
            $table->date('report_date')->nullable(false)->change();
        });
    }
};