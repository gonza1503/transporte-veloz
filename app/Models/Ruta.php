<?php
// app/Models/Ruta.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ruta extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo', 'origen', 'destino', 'precio', 'duracion_minutos', 'descripcion', 'activa'
    ];

    protected $casts = [
        'activa' => 'boolean',
        'precio' => 'decimal:2',
    ];

    // Relación: Una ruta puede tener muchos horarios
    public function horarios()
    {
        return $this->hasMany(Horario::class);
    }

    // Método para obtener la duración formateada
    public function getDuracionFormateada()
    {
        $horas = floor($this->duracion_minutos / 60);
        $minutos = $this->duracion_minutos % 60;
        return $horas . 'h ' . $minutos . 'm';
    }
}
