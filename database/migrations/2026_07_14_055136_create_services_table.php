<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('vehicle_plate');
            $table->string('type');
            $table->dateTime('entry_date');
            $table->dateTime('completion_date')->nullable();
            $table->enum('status', ['pending', 'progress', 'done', 'cancelled'])->default('pending');
            $table->decimal('total_cost', 15, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};