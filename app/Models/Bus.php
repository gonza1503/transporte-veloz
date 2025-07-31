<?php
// app/Models/Bus.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    use HasFactory;

    protected $table = 'buses';

    protected $fillable = [
        'placa', 'modelo', 'anio', 'capacidad', 'chofer', 'estado', 'observaciones'
    ];

    protected $casts = [
        'anio' => 'integer',
        'capacidad' => 'integer',
    ];

    // Relación: Un bus puede tener muchos horarios
    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    // Método para verificar si el bus está disponible
    public function estaDisponible()
    {
        return $this->estado === 'activo';
    }
}