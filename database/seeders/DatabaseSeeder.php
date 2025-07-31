<?php
// database/seeders/DatabaseSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
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
        $this->command->info('🚌 Iniciando población de la base de datos de Transporte Veloz...');
        
        // Desactivar verificación de claves foráneas temporalmente
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        // Limpiar tablas existentes en orden correcto
        $this->command->info('🧹 Limpiando tablas existentes...');
        DB::table('tickets')->truncate();
        DB::table('ventas')->truncate();
        DB::table('horarios')->truncate();
        DB::table('pasajeros')->truncate();
        DB::table('buses')->truncate();
        DB::table('rutas')->truncate();
        DB::table('users')->truncate();
        
        // Reactivar verificación de claves foráneas
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        
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
        
        $this->command->info('✅ ¡Base de datos poblada exitosamente!');
        $this->mostrarResumen();
    }
    
    /**
     * Crear usuarios de prueba
     */
    private function crearUsuarios()
    {
        $this->command->info('👥 Creando usuarios...');
        
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
            ['name' => 'Pedro Sánchez', 'email' => 'pedro@transporteveloz.com'],
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
        
        $this->command->info('   ✓ Creados ' . User::count() . ' usuarios');
    }
    
    /**
     * Crear rutas de prueba
     */
    private function crearRutas()
    {
        $this->command->info('🗺️  Creando rutas...');
        
        $rutas = [
            [
                'codigo' => 'LIM-ARE-001',
                'origen' => 'Lima',
                'destino' => 'Arequipa',
                'precio' => 85.00,
                'duracion_minutos' => 960, // 16 horas
                'descripcion' => 'Ruta directa Lima - Arequipa con paradas en Ica y Nazca',
                'activa' => true
            ],
            [
                'codigo' => 'LIM-CUZ-002',
                'origen' => 'Lima',
                'destino' => 'Cusco',
                'precio' => 120.00,
                'duracion_minutos' => 1200, // 20 horas
                'descripcion' => 'Ruta Lima - Cusco vía Abancay',
                'activa' => true
            ],
            [
                'codigo' => 'LIM-TRU-003',
                'origen' => 'Lima',
                'destino' => 'Trujillo',
                'precio' => 55.00,
                'duracion_minutos' => 480, // 8 horas
                'descripcion' => 'Ruta Lima - Trujillo por Panamericana Norte',
                'activa' => true
            ],
            [
                'codigo' => 'ARE-CUZ-004',
                'origen' => 'Arequipa',
                'destino' => 'Cusco',
                'precio' => 45.00,
                'duracion_minutos' => 600, // 10 horas
                'descripcion' => 'Ruta Arequipa - Cusco vía Juliaca',
                'activa' => true
            ],
            [
                'codigo' => 'LIM-HUC-005',
                'origen' => 'Lima',
                'destino' => 'Huancayo',
                'precio' => 35.00,
                'duracion_minutos' => 420, // 7 horas
                'descripcion' => 'Ruta Lima - Huancayo por carretera central',
                'activa' => true
            ],
            [
                'codigo' => 'LIM-ICA-006',
                'origen' => 'Lima',
                'destino' => 'Ica',
                'precio' => 25.00,
                'duracion_minutos' => 300, // 5 horas
                'descripcion' => 'Ruta Lima - Ica por Panamericana Sur',
                'activa' => true
            ],
            [
                'codigo' => 'LIM-PAS-007',
                'origen' => 'Lima',
                'destino' => 'Paracas',
                'precio' => 30.00,
                'duracion_minutos' => 270, // 4.5 horas
                'descripcion' => 'Ruta Lima - Paracas, ideal para turismo',
                'activa' => true
            ],
            [
                'codigo' => 'CUZ-PUN-008',
                'origen' => 'Cusco',
                'destino' => 'Puno',
                'precio' => 40.00,
                'duracion_minutos' => 480, // 8 horas
                'descripcion' => 'Ruta Cusco - Puno por el altiplano',
                'activa' => true
            ]
        ];
        
        foreach ($rutas as $ruta) {
            Ruta::create($ruta);
        }
        
        $this->command->info('   ✓ Creadas ' . Ruta::count() . ' rutas');
    }
    
    /**
     * Crear buses de prueba
     */
    private function crearBuses()
    {
        $this->command->info('🚌 Creando buses...');
        
        $buses = [
            [
                'placa' => 'ABC-123',
                'modelo' => 'Mercedes Benz OH 1628',
                'anio' => 2020,
                'capacidad' => 44,
                'chofer' => 'José Martínez Peña',
                'estado' => 'activo',
                'observaciones' => 'Bus en excelente estado, mantenimiento al día'
            ],
            [
                'placa' => 'DEF-456',
                'modelo' => 'Volvo B290R',
                'anio' => 2019,
                'capacidad' => 40,
                'chofer' => 'Roberto Silva Guerrero',
                'estado' => 'activo',
                'observaciones' => 'Sistema de aire acondicionado renovado'
            ],
            [
                'placa' => 'GHI-789',
                'modelo' => 'Scania K380',
                'anio' => 2021,
                'capacidad' => 48,
                'chofer' => 'Miguel Torres Castillo',
                'estado' => 'activo',
                'observaciones' => 'Bus nuevo con sistema multimedia'
            ],
            [
                'placa' => 'JKL-012',
                'modelo' => 'Mercedes Benz OF 1721',
                'anio' => 2018,
                'capacidad' => 42,
                'chofer' => 'Fernando Vega López',
                'estado' => 'mantenimiento',
                'observaciones' => 'Mantenimiento preventivo programado - frenos y suspensión'
            ],
            [
                'placa' => 'MNO-345',
                'modelo' => 'Volvo B370R',
                'anio' => 2022,
                'capacidad' => 46,
                'chofer' => 'Carlos Ruiz Mendoza',
                'estado' => 'activo',
                'observaciones' => 'Bus premium con asientos reclinables'
            ],
            [
                'placa' => 'PQR-678',
                'modelo' => 'Scania K400',
                'anio' => 2021,
                'capacidad' => 50,
                'chofer' => 'Daniel Morales Jiménez',
                'estado' => 'activo',
                'observaciones' => 'Mayor capacidad, ideal para rutas populares'
            ],
            [
                'placa' => 'STU-901',
                'modelo' => 'Mercedes Benz O500R',
                'anio' => 2020,
                'capacidad' => 45,
                'chofer' => 'Andrés Ramírez Cruz',
                'estado' => 'activo',
                'observaciones' => 'Bus con baño y sistema de entretenimiento'
            ],
            [
                'placa' => 'VWX-234',
                'modelo' => 'Volvo B450R',
                'anio' => 2023,
                'capacidad' => 44,
                'chofer' => 'Ricardo Paredes Vásquez',
                'estado' => 'activo',
                'observaciones' => 'Último modelo con tecnología Euro VI'
            ]
        ];
        
        foreach ($buses as $bus) {
            Bus::create($bus);
        }
        
        $this->command->info('   ✓ Creados ' . Bus::count() . ' buses');
    }
    
    /**
     * Crear horarios de prueba
     */
    private function crearHorarios()
    {
        $this->command->info('⏰ Creando horarios...');
        
        $rutas = Ruta::all();
        $buses = Bus::where('estado', 'activo')->get();
        
        $horariosCreados = 0;
        
        // Crear horarios para los próximos 45 días
        for ($dia = 0; $dia < 45; $dia++) {
            $fecha = Carbon::now()->addDays($dia);
            
            // No crear horarios para domingos (día de descanso)
            if ($fecha->dayOfWeek === 0) {
                continue;
            }
            
            // Horarios típicos por ruta con mayor variedad
            $horariosBase = [
                'LIM-ARE-001' => ['08:00', '15:00', '22:00'],
                'LIM-CUZ-002' => ['10:00', '20:00'],
                'LIM-TRU-003' => ['06:00', '09:00', '14:00', '18:00', '23:00'],
                'ARE-CUZ-004' => ['07:00', '16:00'],
                'LIM-HUC-005' => ['05:00', '08:00', '12:00', '16:00', '20:00'],
                'LIM-ICA-006' => ['06:00', '10:00', '14:00', '18:00'],
                'LIM-PAS-007' => ['07:00', '11:00', '15:00'],
                'CUZ-PUN-008' => ['09:00', '17:00']
            ];
            
            foreach ($rutas as $ruta) {
                $horarios = $horariosBase[$ruta->codigo] ?? ['08:00'];
                
                foreach ($horarios as $hora) {
                    // Asignar bus aleatoriamente pero apropiado para la ruta
                    $busesDisponibles = $buses->filter(function($bus) use ($fecha, $hora) {
                        return !Horario::where('bus_id', $bus->id)
                                      ->where('fecha', $fecha->format('Y-m-d'))
                                      ->where('hora_salida', $hora)
                                      ->exists();
                    });
                    
                    if ($busesDisponibles->count() > 0) {
                        $bus = $busesDisponibles->random();
                        
                        Horario::create([
                            'ruta_id' => $ruta->id,
                            'bus_id' => $bus->id,
                            'fecha' => $fecha->format('Y-m-d'),
                            'hora_salida' => $hora,
                            'asientos_disponibles' => $bus->capacidad,
                            'activo' => true
                        ]);
                        
                        $horariosCreados++;
                    }
                }
            }
        }
        
        $this->command->info('   ✓ Creados ' . $horariosCreados . ' horarios');
    }
    
    /**
     * Crear datos de prueba (ventas y tickets) con control de unicidad
     */
    private function crearDatosPrueba()
    {
        $this->command->info('🎫 Creando datos de prueba (ventas y pasajeros)...');
        
        $empleados = User::where('role', 'empleado')->get();
        $horarios = Horario::where('fecha', '>=', Carbon::now()->subDays(14)->format('Y-m-d'))
                          ->where('fecha', '<=', Carbon::now()->format('Y-m-d'))
                          ->get();
        
        // Nombres y apellidos peruanos más realistas
        $nombres = [
            'Juan Carlos', 'María Elena', 'Carlos Alberto', 'Ana Lucía', 'Luis Fernando',
            'Carmen Rosa', 'José Antonio', 'Rosa María', 'Miguel Ángel', 'Elena Patricia',
            'Ricardo Manuel', 'Sofía Alejandra', 'Diego Sebastián', 'Valeria Nicole',
            'Alejandro David', 'Camila Andrea', 'Gabriel Eduardo', 'Isabella Fernanda'
        ];
        
        $apellidos = [
            'García Rodríguez', 'López Martínez', 'González Pérez', 'Rodríguez Sánchez',
            'Martínez González', 'Sánchez López', 'Pérez García', 'Ramírez Torres',
            'Torres Ramírez', 'Flores Morales', 'Morales Flores', 'Jiménez Castro',
            'Castro Jiménez', 'Vargas Herrera', 'Herrera Vargas', 'Mendoza Silva'
        ];
        
        // Contadores para garantizar unicidad
        $contadorGlobal = 1;
        $ventasCreadas = 0;
        $ticketsCreados = 0;
        $pasajerosCreados = 0;
        
        foreach ($horarios as $horario) {
            // Generar entre 1 y 15 ventas por horario (más realista)
            $numVentas = rand(1, min(15, intval($horario->bus->capacidad / 2.5)));
            
            for ($v = 0; $v < $numVentas; $v++) {
                // Cantidad de pasajes por venta (1-6, más común 1-2)
                $cantidadPasajes = $this->getCantidadPasajesRealista();
                
                // Verificar que haya asientos disponibles
                if ($horario->asientos_disponibles < $cantidadPasajes) {
                    continue;
                }
                
                // Crear la venta con código único garantizado
                $codigoVenta = $this->generarCodigoVentaUnicoSeeder($contadorGlobal);
                
                $fechaVenta = Carbon::now()
                    ->subDays(rand(0, 14))
                    ->subHours(rand(0, 23))
                    ->subMinutes(rand(0, 59));
                
                $venta = Venta::create([
                    'codigo_venta' => $codigoVenta,
                    'user_id' => $empleados->random()->id,
                    'horario_id' => $horario->id,
                    'cantidad_pasajes' => $cantidadPasajes,
                    'total' => $horario->ruta->precio * $cantidadPasajes,
                    'fecha_venta' => $fechaVenta,
                    'estado' => 'completada'
                ]);
                
                $ventasCreadas++;
                $contadorGlobal++;
                
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
                shuffle($asientosDisponibles);
                $asientosSeleccionados = array_slice($asientosDisponibles, 0, $cantidadPasajes);
                
                // Crear pasajeros y tickets
                for ($p = 0; $p < $cantidadPasajes; $p++) {
                    $nombre = $nombres[array_rand($nombres)];
                    $apellido = $apellidos[array_rand($apellidos)];
                    $nombreCompleto = $nombre . ' ' . $apellido;
                    
                    // Generar DNI único más realista
                    $dni = $this->generarDniUnico();
                    
                    // Crear o encontrar pasajero
                    $pasajero = Pasajero::firstOrCreate(
                        ['dni' => $dni],
                        [
                            'nombre' => $nombreCompleto,
                            'telefono' => $this->generarTelefonoPeruano(),
                            'email' => $this->generarEmailRealista($nombre, $apellido)
                        ]
                    );
                    
                    if ($pasajero->wasRecentlyCreated) {
                        $pasajerosCreados++;
                    }
                    
                    // Crear ticket con número único garantizado
                    $numeroTicket = $this->generarNumeroTicketUnicoSeeder($contadorGlobal, $p);
                    
                    Ticket::create([
                        'numero_ticket' => $numeroTicket,
                        'venta_id' => $venta->id,
                        'pasajero_id' => $pasajero->id,
                        'asiento' => $asientosSeleccionados[$p],
                        'qr_code' => $this->generarQRCodeRealista($venta->codigo_venta, $asientosSeleccionados[$p], $contadorGlobal)
                    ]);
                    
                    $ticketsCreados++;
                }
                
                // Actualizar asientos disponibles
                $horario->decrement('asientos_disponibles', $cantidadPasajes);
            }
        }
        
        $this->command->info('   ✓ Creadas ' . $ventasCreadas . ' ventas');
        $this->command->info('   ✓ Creados ' . $ticketsCreados . ' tickets');
        $this->command->info('   ✓ Creados ' . $pasajerosCreados . ' pasajeros únicos');
    }
    
    /**
     * Obtener cantidad de pasajes realista
     */
    private function getCantidadPasajesRealista()
    {
        $probabilidades = [
            1 => 45, // 45% - 1 pasajero
            2 => 25, // 25% - 2 pasajeros  
            3 => 15, // 15% - 3 pasajeros
            4 => 10, // 10% - 4 pasajeros
            5 => 3,  // 3% - 5 pasajeros
            6 => 2   // 2% - 6 pasajeros
        ];
        
        $random = rand(1, 100);
        $acumulado = 0;
        
        foreach ($probabilidades as $cantidad => $probabilidad) {
            $acumulado += $probabilidad;
            if ($random <= $acumulado) {
                return $cantidad;
            }
        }
        
        return 1; // Por defecto
    }
    
    /**
     * Generar código de venta único para seeder
     */
    private function generarCodigoVentaUnicoSeeder($contador)
    {
        $fecha = date('Ymd');
        $numero = str_pad($contador, 8, '0', STR_PAD_LEFT);
        return "V-{$fecha}-{$numero}";
    }
    
    /**
     * Generar número de ticket único para seeder
     */
    private function generarNumeroTicketUnicoSeeder($contadorGlobal, $subContador)
    {
        $fecha = date('YmdHis');
        $numero = str_pad($contadorGlobal, 6, '0', STR_PAD_LEFT);
        $sub = str_pad($subContador, 2, '0', STR_PAD_LEFT);
        return "T-{$fecha}-{$numero}{$sub}";
    }
    
    /**
     * Generar DNI único peruano realista
     */
    private function generarDniUnico()
    {
        do {
            // DNI peruano: 8 dígitos, no puede empezar con 0
            $dni = rand(10000000, 99999999);
        } while (Pasajero::where('dni', (string) $dni)->exists());
        
        return (string) $dni;
    }
    
    /**
     * Generar teléfono peruano realista
     */
    private function generarTelefonoPeruano()
    {
        // Formato peruano: 9XXXXXXXX (celular)
        $prefijos = ['9', '99', '98', '97', '96', '95'];
        $prefijo = $prefijos[array_rand($prefijos)];
        $resto = str_pad(rand(1000000, 9999999), 7, '0', STR_PAD_LEFT);
        
        return $prefijo . $resto;
    }
    
    /**
     * Generar email realista
     */
    private function generarEmailRealista($nombre, $apellido)
    {
        $dominios = ['gmail.com', 'hotmail.com', 'yahoo.com', 'outlook.com', 'live.com'];
        $nombreLimpio = strtolower(str_replace(' ', '', $nombre));
        $apellidoLimpio = strtolower(explode(' ', $apellido)[0]);
        
        $formatos = [
            $nombreLimpio . '.' . $apellidoLimpio,
            $nombreLimpio . $apellidoLimpio,
            $nombreLimpio . rand(1, 999),
            $apellidoLimpio . '.' . $nombreLimpio,
            substr($nombreLimpio, 0, 3) . $apellidoLimpio
        ];
        
        $usuario = $formatos[array_rand($formatos)];
        $dominio = $dominios[array_rand($dominios)];
        
        return $usuario . '@' . $dominio;
    }
    
    /**
     * Generar código QR realista
     */
    private function generarQRCodeRealista($codigoVenta, $asiento, $contador)
    {
        $data = $codigoVenta . '-' . $asiento . '-' . date('Y-m-d-H-i-s') . '-' . $contador;
        return base64_encode($data);
    }
    
    /**
     * Mostrar resumen final
     */
    private function mostrarResumen()
    {
        $this->command->info('');
        $this->command->info('📊 RESUMEN DE LA BASE DE DATOS:');
        $this->command->info('================================');
        $this->command->info('👥 Usuarios: ' . User::count() . ' (1 admin + ' . User::where('role', 'empleado')->count() . ' empleados)');
        $this->command->info('🗺️  Rutas: ' . Ruta::count());
        $this->command->info('🚌 Buses: ' . Bus::count() . ' (' . Bus::where('estado', 'activo')->count() . ' activos)');
        $this->command->info('⏰ Horarios: ' . Horario::count());
        $this->command->info('🎫 Ventas: ' . Venta::count());
        $this->command->info('🎟️  Tickets: ' . Ticket::count());
        $this->command->info('👤 Pasajeros: ' . Pasajero::count());
        $this->command->info('');
        $this->command->info('🔑 CREDENCIALES DE ACCESO:');
        $this->command->info('========================');
        $this->command->info('👑 Admin: admin@transporteveloz.com | password123');
        $this->command->info('👤 Empleado: empleado@transporteveloz.com | password123');
        $this->command->info('');
        $this->command->info('🚀 ¡Sistema listo para usar!');
    }
}