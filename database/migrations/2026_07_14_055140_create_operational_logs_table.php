<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('operational_logs', function (Blueprint $table) {
            $table->id();
            $table->date('log_date')->unique();
            $table->integer('total_services')->default(0);
            $table->integer('completed_services')->default(0);
            $table->float('avg_completion_hours')->default(0);
            $table->integer('total_spare_used')->default(0);
            $table->decimal('total_revenue', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('operational_logs');
    }
};