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
        Schema::create('transaction_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payment_id')->nullable()->constrained('payments')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();

            $table->string('accion'); // Ej: 'creación', 'aprobación', 'error', 'reembolso'
            $table->text('descripcion')->nullable();
            $table->json('datos')->nullable();
            $table->string('ip')->nullable();
            $table->string('user_agent')->nullable();

            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */

    public function down(): void
    {
        Schema::dropIfExists('transaction_logs');
    }
};
