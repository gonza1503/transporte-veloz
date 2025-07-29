<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Ruta;
use App\Models\Bus;
use App\Models\Horario;
use App\Models\Pasajero;
use App\Models\Venta;
use App\Models\Ticket;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    /**
     * Ejecutar los seeders de la base de datos
     */
    public function run(): void
    {
        // 1. Crear usuarios (empleados)
        $this->crearUsuarios();
        
        // 2. Crear rutas
        $this->crearRutas();
        
        // 3. Crear buses
        $this->crearBuses();
        
        // 4. Crear horarios
        $this->crearHorarios();
        
        // 5. Crear datos de prueba (pasajeros, ventas, tickets)
        $this->crearDatosPrueba();
        
        $this->command->info('¡Base de datos poblada exitosamente!');
    }
    
    /**
     * Crear usuarios de prueba
     */
    private function crearUsuarios()
    {
        $this->command->info('Creando usuarios...');
        
        // Administrador principal
        User::create([
            'name' => 'Administrador Principal',
            'email' => 'admin@transporteveloz.com',
            'password' => Hash::make('password123'),
            'role' => 'admin',
            'active' => true
        ]);
        
        // Empleados de prueba
        $empleados = [
            ['name' => 'Carlos Mendoza', 'email' => 'carlos@transporteveloz.com'],
            ['name' => 'Ana García', 'email' => 'ana@transporteveloz.com'],
            ['name' => 'Luis Rodríguez', 'email' => 'luis@transporteveloz.com'],
            ['name' => 'María López', 'email' => 'maria@transporteveloz.com'],
        ];
        
        foreach ($empleados as $empleado) {
            User::create([
                'name' => $empleado['name'],
                'email' => $empleado['email'],
                'password' => Hash::make('password123'),
                'role' => 'empleado',
                'active' => true
            ]);
        }
        
        // Usuario empleado genérico para login de prueba
        User::create([
            'name' => 'Empleado Demo',
            'email' => 'empleado@transporteveloz.com',
            'password' => Hash::make('password123'),
            'role' => 'empleado',
            'active' => true
        ]);
    }
    
    /**
     * Crear rutas de prueba
     */
    private function crearRutas()
    {
        $this->command->info('Creando rutas...');
        
        $rutas = [
            [
                'codigo' => 'LIM-ARE-001',
                'origen' => 'Lima',
                'destino' => 'Arequipa',
                'precio' => 85.00,
                'duracion_minutos' => 960, // 16 horas
                'descripcion' => 'Ruta directa Lima - Arequipa con paradas en Ica y Nazca'
            ],
            [
                'codigo' => 'LIM-CUZ-002',
                'origen' => 'Lima',
                'destino' => 'Cusco',
                'precio' => 120.00,
                'duracion_minutos' => 1200, // 20 horas
                'descripcion' => 'Ruta Lima - Cusco vía Abancay'
            ],
            [
                'codigo' => 'LIM-TRU-003',
                'origen' => 'Lima',
                'destino' => 'Trujillo',
                'precio' => 55.00,
                'duracion_minutos' => 480, // 8 horas
                'descripcion' => 'Ruta Lima - Trujillo por Panamericana Norte'
            ],
            [
                'codigo' => 'ARE-CUZ-004',
                'origen' => 'Arequipa',
                'destino' => 'Cusco',
                'precio' => 45.00,
                'duracion_minutos' => 600, // 10 horas
                'descripcion' => 'Ruta Arequipa - Cusco vía Juliaca'
            ],
            [
                'codigo' => 'LIM-HUC-005',
                'origen' => 'Lima',
                'destino' => 'Huancayo',
                'precio' => 35.00,
                'duracion_minutos' => 420, // 7 horas
                'descripcion' => 'Ruta Lima - Huancayo por carretera central'
            ],
            [
                'codigo' => 'LIM-ICA-006',
                'origen' => 'Lima',
                'destino' => 'Ica',
                'precio' => 25.00,
                'duracion_minutos' => 300, // 5 horas
                'descripcion' => 'Ruta Lima - Ica por Panamericana Sur'
            ]
        ];
        
        foreach ($rutas as $ruta) {
            Ruta::create($ruta);
        }
    }
    
    /**
     * Crear buses de prueba
     */
    private function crearBuses()
    {
        $this->command->info('Creando buses...');
        
        $buses = [
            [
                'placa' => 'ABC-123',
                'modelo' => 'Mercedes Benz OH 1628',
                'anio' => 2020,
                'capacidad' => 44,
                'chofer' => 'José Martínez',
                'estado' => 'activo'
            ],
            [
                'placa' => 'DEF-456',
                'modelo' => 'Volvo B290R',
                'anio' => 2019,
                'capacidad' => 40,
                'chofer' => 'Roberto Silva',
                'estado' => 'activo'
            ],
            [
                'placa' => 'GHI-789',
                'modelo' => 'Scania K380',
                'anio' => 2021,
                'capacidad' => 48,
                'chofer' => 'Miguel Torres',
                'estado' => 'activo'
            ],
            [
                'placa' => 'JKL-012',
                'modelo' => 'Mercedes Benz OF 1721',
                'anio' => 2018,
                'capacidad' => 42,
                'chofer' => 'Fernando Vega',
                'estado' => 'mantenimiento',
                'observaciones' => 'Mantenimiento preventivo programado'
            ],
            [
                'placa' => 'MNO-345',
                'modelo' => 'Volvo B370R',
                'anio' => 2022,
                'capacidad' => 46,
                'chofer' => 'Carlos Ruiz',
                'estado' => 'activo'
            ],
            [
                'placa' => 'PQR-678',
                'modelo' => 'Scania K400',
                'anio' => 2021,
                'capacidad' => 50,
                'chofer' => 'Daniel Morales',
                'estado' => 'activo'
            ]
        ];
        
        foreach ($buses as $bus) {
            Bus::create($bus);
        }
    }
    
    /**
     * Crear horarios de prueba
     */
    private function crearHorarios()
    {
        $this->command->info('Creando horarios...');
        
        $rutas = Ruta::all();
        $buses = Bus::where('estado', 'activo')->get();
        
        // Crear horarios para los próximos 30 días
        for ($dia = 0; $dia < 30; $dia++) {
            $fecha = Carbon::now()->addDays($dia);
            
            // No crear horarios para domingos (día de descanso)
            if ($fecha->dayOfWeek === 0) {
                continue;
            }
            
            // Horarios típicos por ruta
            $horariosBase = [
                'LIM-ARE-001' => ['08:00', '15:00', '22:00'],
                'LIM-CUZ-002' => ['10:00', '20:00'],
                'LIM-TRU-003' => ['06:00', '09:00', '14:00', '18:00', '23:00'],
                'ARE-CUZ-004' => ['07:00', '16:00'],
                'LIM-HUC-005' => ['05:00', '08:00', '12:00', '16:00', '20:00'],
                'LIM-ICA-006' => ['06:00', '10:00', '14:00', '18:00']
            ];
            
            foreach ($rutas as $ruta) {
                $horarios = $horariosBase[$ruta->codigo] ?? ['08:00'];
                
                foreach ($horarios as $hora) {
                    // Asignar bus aleatoriamente
                    $bus = $buses->random();
                    
                    // Verificar que el bus no esté ocupado en ese horario
                    $busOcupado = Horario::where('bus_id', $bus->id)
                        ->where('fecha', $fecha->format('Y-m-d'))
                        ->where('hora_salida', $hora)
                        ->exists();
                    
                    if (!$busOcupado) {
                        Horario::create([
                            'ruta_id' => $ruta->id,
                            'bus_id' => $bus->id,
                            'fecha' => $fecha->format('Y-m-d'),
                            'hora_salida' => $hora,
                            'asientos_disponibles' => $bus->capacidad,
                            'activo' => true
                        ]);
                    }
                }
            }
        }
    }
    
    /**
     * Crear datos de prueba (ventas y tickets)
     */
    private function crearDatosPrueba()
    {
        $this->command->info('Creando datos de prueba (ventas y pasajeros)...');
        
        $empleados = User::where('role', 'empleado')->get();
        $horarios = Horario::where('fecha', '>=', Carbon::now()->subDays(7)->format('Y-m-d'))
                          ->where('fecha', '<=', Carbon::now()->format('Y-m-d'))
                          ->get();
        
        // Nombres y apellidos para generar pasajeros
        $nombres = ['Juan', 'María', 'Carlos', 'Ana', 'Luis', 'Carmen', 'José', 'Rosa', 'Miguel', 'Elena'];
        $apellidos = ['García', 'Rodríguez', 'López', 'Martínez', 'González', 'Pérez', 'Sánchez', 'Ramírez', 'Torres', 'Flores'];
        
        foreach ($horarios as $horario) {
            // Generar entre 1 y 15 ventas por horario (aleatorio)
            $numVentas = rand(1, min(15, $horario->bus->capacidad / 3));
            
            for ($v = 0; $v < $numVentas; $v++) {
                // Cantidad de pasajes por venta (1-4)
                $cantidadPasajes = rand(1, 4);
                
                // Verificar que haya asientos disponibles
                if ($horario->asientos_disponibles < $cantidadPasajes) {
                    continue;
                }
                
                // Crear la venta
                $venta = Venta::create([
                    'codigo_venta' => Venta::generarCodigoVenta(),
                    'user_id' => $empleados->random()->id,
                    'horario_id' => $horario->id,
                    'cantidad_pasajes' => $cantidadPasajes,
                    'total' => $horario->ruta->precio * $cantidadPasajes,
                    'fecha_venta' => Carbon::now()->subDays(rand(0, 7))->subHours(rand(0, 23)),
                    'estado' => 'completada'
                ]);
                
                // Obtener asientos ocupados
                $asientosOcupados = Ticket::whereHas('venta', function($query) use ($horario) {
                    $query->where('horario_id', $horario->id);
                })->pluck('asiento')->toArray();
                
                // Generar asientos disponibles
                $asientosDisponibles = [];
                for ($i = 1; $i <= $horario->bus->capacidad; $i++) {
                    if (!in_array($i, $asientosOcupados)) {
                        $asientosDisponibles[] = $i;
                    }
                }
                
                // Seleccionar asientos aleatoriamente
                $asientosSeleccionados = array_slice($asientosDisponibles, 0, $cantidadPasajes);
                
                // Crear pasajeros y tickets
                for ($p = 0; $p < $cantidadPasajes; $p++) {
                    $nombre = $nombres[array_rand($nombres)] . ' ' . $apellidos[array_rand($apellidos)] . ' ' . $apellidos[array_rand($apellidos)];
                    $dni = '1' . str_pad(rand(0, 9999999), 7, '0', STR_PAD_LEFT);
                    
                    // Crear o encontrar pasajero
                    $pasajero = Pasajero::firstOrCreate(
                        ['dni' => $dni],
                        [
                            'nombre' => $nombre,
                            'telefono' => rand(900000000, 999999999),
                            'email' => strtolower(str_replace(' ', '.', $nombre)) . '@email.com'
                        ]
                    );
                    
                    // Crear ticket
                    Ticket::create([
                        'numero_ticket' => Ticket::generarNumeroTicket(),
                        'venta_id' => $venta->id,
                        'pasajero_id' => $pasajero->id,
                        'asiento' => $asientosSeleccionados[$p],
                        'qr_code' => base64_encode($venta->codigo_venta . '-' . $asientosSeleccionados[$p] . '-' . date('Y-m-d'))
                    ]);
                }
                
                // Actualizar asientos disponibles
                $horario->decrement('asientos_disponibles', $cantidadPasajes);
            }
        }
    }
}