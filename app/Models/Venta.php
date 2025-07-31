<?php
// app/Models/Venta.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo_venta', 'user_id', 'horario_id', 'cantidad_pasajes', 'total', 'estado', 'fecha_venta'
    ];

    protected $casts = [
        'fecha_venta' => 'datetime',
        'total' => 'decimal:2',
        'cantidad_pasajes' => 'integer',
    ];

    // Relación: Una venta pertenece a un empleado
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relación: Una venta pertenece a un horario
    public function horario()
    {
        return $this->belongsTo(Horario::class);
    }

    // Relación: Una venta puede tener muchos tickets
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }

    /**
     * Generar código único de venta con mejor algoritmo
     */
    public static function generarCodigoVentaUnico()
    {
        $maxIntentos = 15;
        $intentos = 0;
        
        do {
            // Usar fecha + hora + microsegundos + random
            $fecha = date('Ymd');
            $hora = date('His');
            $microsegundos = substr(microtime(true) * 1000, -3);
            $random = str_pad(random_int(100, 999), 3, '0', STR_PAD_LEFT);
            
            $codigo = 'V-' . $fecha . '-' . $hora . $microsegundos . $random;
            
            $intentos++;
            
            if ($intentos >= $maxIntentos) {
                // Como último recurso, usar UUID truncado
                $codigo = 'V-' . $fecha . '-' . strtoupper(substr(Str::uuid()->toString(), 0, 12));
                break;
            }
            
            // Agregar delay para evitar colisiones
            if ($intentos > 3) {
                usleep(500); // 0.5ms delay
            }
            
        } while (static::where('codigo_venta', $codigo)->exists());

        return $codigo;
    }
}