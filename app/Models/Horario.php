<?php
// app/Models/Horario.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Horario extends Model
{
    use HasFactory;

    protected $fillable = [
        'ruta_id', 'bus_id', 'hora_salida', 'fecha', 'asientos_disponibles', 'activo'
    ];

    protected $casts = [
        'fecha' => 'date',
        'hora_salida' => 'datetime:H:i',
        'activo' => 'boolean',
        'asientos_disponibles' => 'integer',
    ];

    // Relación: Un horario pertenece a una ruta
    public function ruta()
    {
        return $this->belongsTo(Ruta::class);
    }

    // Relación: Un horario pertenece a un bus
    public function bus()
    {
        return $this->belongsTo(Bus::class);
    }

    // Relación: Un horario puede tener muchas ventas
    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    // Método para verificar si hay asientos disponibles
    public function tieneAsientosDisponibles($cantidad = 1)
    {
        return $this->asientos_disponibles >= $cantidad;
    }
}