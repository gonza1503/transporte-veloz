<?php
// app/Models/User.php (Empleados)
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'active'
    ];

    protected $hidden = [
        'password', 'remember_token'
    ];

    // Relación: Un empleado puede hacer muchas ventas
    public function ventas()
    {
        return $this->hasMany(Venta::class);
    }

    // Método para verificar si es administrador
    public function isAdmin()
    {
        return $this->role === 'admin';
    }
}

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

// app/Models/Bus.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    use HasFactory;

    protected $table = 'buses'; // Nombre de la tabla

    protected $fillable = [
        'placa', 'modelo', 'anio', 'capacidad', 'chofer', 'estado', 'observaciones'
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
        'hora_salida' => 'datetime:H:i'
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

// app/Models/Pasajero.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pasajero extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre', 'dni', 'telefono', 'email'
    ];

    // Relación: Un pasajero puede tener muchos tickets
    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}

// app/Models/Venta.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo_venta', 'user_id', 'horario_id', 'cantidad_pasajes', 'total', 'estado', 'fecha_venta'
    ];

    protected $casts = [
        'fecha_venta' => 'datetime'
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

    // Método para generar código único de venta
    public static function generarCodigoVenta()
    {
        return 'V-' . date('Ymd') . '-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
    }
}

// app/Models/Ticket.php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'numero_ticket', 'venta_id', 'pasajero_id', 'asiento', 'qr_code'
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

    // Método para generar número único de ticket
    public static function generarNumeroTicket()
    {
        return 'T-' . date('Ymd') . '-' . str_pad(rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }
}