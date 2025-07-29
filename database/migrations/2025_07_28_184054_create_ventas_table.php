<?php
// database/migrations/2024_01_06_000001_create_ventas_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_venta')->unique(); // Código único de venta
            $table->foreignId('user_id')->constrained('users'); // Empleado que realizó la venta
            $table->foreignId('horario_id')->constrained('horarios');
            $table->integer('cantidad_pasajes'); // Número de pasajes vendidos
            $table->decimal('total', 10, 2); // Total pagado
            $table->enum('estado', ['completada', 'cancelada'])->default('completada');
            $table->timestamp('fecha_venta'); // Momento exacto de la venta
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
