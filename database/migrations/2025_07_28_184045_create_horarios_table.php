<?php
// database/migrations/2024_01_04_000001_create_horarios_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ruta_id')->constrained('rutas')->onDelete('cascade'); // Relación con rutas
            $table->foreignId('bus_id')->constrained('buses')->onDelete('cascade'); // Relación con buses
            $table->time('hora_salida'); // Hora de salida (ej: 08:30)
            $table->date('fecha'); // Fecha del viaje
            $table->integer('asientos_disponibles'); // Asientos libres
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};

