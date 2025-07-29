<?php
// database/migrations/2024_01_07_000001_create_tickets_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('numero_ticket')->unique(); // Número único del ticket
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->foreignId('pasajero_id')->constrained('pasajeros');
            $table->integer('asiento'); // Número de asiento asignado
            $table->string('qr_code')->nullable(); // Código QR generado
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
