<?php
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