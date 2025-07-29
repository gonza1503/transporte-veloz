<?php
// database/migrations/2024_01_03_000001_create_buses_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('buses', function (Blueprint $table) {
            $table->id();
            $table->string('placa')->unique(); // Placa del bus (ej: "ABC-123")
            $table->string('modelo'); // Modelo del bus
            $table->year('anio'); // Año de fabricación
            $table->integer('capacidad'); // Número de asientos totales
            $table->string('chofer'); // Nombre del chofer
            $table->enum('estado', ['activo', 'mantenimiento', 'ocupado'])->default('activo');
            $table->text('observaciones')->nullable(); // Notas adicionales
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('buses');
    }
};