<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('plate')->nullable()->unique();
            $table->string('model')->nullable();
            $table->unsignedInteger('mileage')->default(0);
            $table->enum('type', ['bike', 'scooter', 'moto', 'car']);
            $table->enum('status', ['available', 'reserved', 'in_service', 'disabled'])->default('available');
            $table->unsignedTinyInteger('battery')->nullable();
            $table->foreignId('branch_id')->nullable()->constrained('branches')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
