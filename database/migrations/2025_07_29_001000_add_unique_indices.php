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
        // Agregar índices para mejorar el rendimiento de búsquedas de códigos únicos
        Schema::table('ventas', function (Blueprint $table) {
            // Agregar índice en fecha_venta para consultas por fecha
            $table->index('fecha_venta');
            $table->index(['user_id', 'fecha_venta']);
        });

        Schema::table('tickets', function (Blueprint $table) {
            // Agregar índices compuestos para consultas comunes
            $table->index(['venta_id', 'asiento']);
        });

        Schema::table('horarios', function (Blueprint $table) {
            // Índices para búsquedas frecuentes
            $table->index(['fecha', 'activo']);
            $table->index(['ruta_id', 'fecha', 'activo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            $table->dropIndex(['fecha_venta']);
            $table->dropIndex(['user_id', 'fecha_venta']);
        });

        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['venta_id', 'asiento']);
        });

        Schema::table('horarios', function (Blueprint $table) {
            $table->dropIndex(['fecha', 'activo']);
            $table->dropIndex(['ruta_id', 'fecha', 'activo']);
        });
    }
};