<?php
// database/migrations/2024_01_01_000001_create_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecutar la migración (crear la tabla)
     */
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id(); // ID único autoincremental
            $table->string('name'); // Nombre del empleado
            $table->string('email')->unique(); // Email único
            $table->string('password'); // Contraseña encriptada
            $table->enum('role', ['admin', 'empleado'])->default('empleado'); // Rol del usuario
            $table->boolean('active')->default(true); // Si está activo
            $table->timestamps(); // created_at y updated_at automáticos
        });
    }

    /**
     * Revertir la migración (eliminar la tabla)
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};