<?php
// database/migrations/2024_01_02_000001_create_rutas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crear tabla de rutas
     */
    public function up(): void
    {
        Schema::create('rutas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique(); // Código único de la ruta (ej: "LIM-ARE-001")
            $table->string('origen'); // Ciudad de origen
            $table->string('destino'); // Ciudad de destino
            $table->decimal('precio', 8, 2); // Precio del pasaje (ej: 25.50)
            $table->integer('duracion_minutos'); // Duración en minutos
            $table->text('descripcion')->nullable(); // Descripción adicional
            $table->boolean('activa')->default(true); // Si la ruta está activa
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rutas');
    }
};