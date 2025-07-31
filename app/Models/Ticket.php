<?php
// app/Models/Ticket.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_ticket', 'venta_id', 'pasajero_id', 'asiento', 'qr_code'
    ];

    protected $casts = [
        'asiento' => 'integer',
    ];

    // Relación: Un ticket pertenece a una venta
    public function venta()
    {
        return $this->belongsTo(Venta::class);
    }

    // Relación: Un ticket pertenece a un pasajero
    public function pasajero()
    {
        return $this->belongsTo(Pasajero::class);
    }

    /**
     * Generar número único de ticket con mejor algoritmo
     */
    public static function generarNumeroTicketUnico()
    {
        $maxIntentos = 20;
        $intentos = 0;
        
        do {
            // Usar timestamp más detallado + microsegundos + random
            $timestamp = now()->format('YmdHis');
            $microsegundos = substr(microtime(true) * 10000, -4);
            $random = str_pad(random_int(100, 999), 3, '0', STR_PAD_LEFT);
            
            $numero = 'T-' . $timestamp . $microsegundos . $random;
            
            $intentos++;
            
            if ($intentos >= $maxIntentos) {
                // Como último recurso, usar UUID
                $numero = 'T-' . strtoupper(str_replace('-', '', Str::uuid()->toString()));
                break;
            }
            
            // Agregar un pequeño delay para evitar colisiones en alta concurrencia
            if ($intentos > 5) {
                usleep(1000); // 1ms delay
            }
            
        } while (static::where('numero_ticket', $numero)->exists());

        return $numero;
    }
}