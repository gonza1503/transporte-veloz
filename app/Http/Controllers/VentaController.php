<?php
// app/Http/Controllers/VentaController.php

namespace App\Http\Controllers;

use App\Models\Ruta;
use App\Models\Horario;
use App\Models\Pasajero;
use App\Models\Venta;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class VentaController extends Controller
{
    /**
     * Página principal de ventas - Seleccionar ruta
     */
    public function index()
    {
        $rutas = Ruta::where('activa', true)->get();
        return view('ventas.index', compact('rutas'));
    }

    /**
     * Mostrar horarios disponibles para una ruta
     */
    public function horarios(Request $request)
    {
        $request->validate([
            'ruta_id' => 'required|exists:rutas,id',
            'fecha' => 'required|date|after_or_equal:today'
        ]);

        $ruta = Ruta::findOrFail($request->ruta_id);
        
        // Obtener horarios disponibles para la fecha seleccionada
        $horarios = Horario::with('bus')
            ->where('ruta_id', $request->ruta_id)
            ->where('fecha', $request->fecha)
            ->where('activo', true)
            ->where('asientos_disponibles', '>', 0)
            ->orderBy('hora_salida')
            ->get();
            
        $fecha = $request->fecha;
        return view('ventas.horarios', compact('ruta', 'horarios', 'fecha'));
    }

    /**
     * Mostrar mapa de asientos y formulario de pasajeros
     */
    public function asientos(Request $request)
    {
        $request->validate([
            'horario_id' => 'required|exists:horarios,id',
            'cantidad' => 'required|integer|min:1|max:10'
        ]);

        $horario = Horario::with(['ruta', 'bus'])->findOrFail($request->horario_id);
        $cantidad = $request->cantidad;

        // Verificar disponibilidad
        if (!$horario->tieneAsientosDisponibles($cantidad)) {
            return back()->with('error', 'No hay suficientes asientos disponibles');
        }

        // Obtener asientos ocupados
        $asientosOcupados = $this->getAsientosOcupados($horario->id);
        
        // Generar mapa de asientos
        $asientos = $this->generarMapaAsientos($horario->bus->capacidad, $asientosOcupados);

        return view('ventas.asientos', compact('horario', 'cantidad', 'asientos'));
    }

    /**
     * Procesar la venta
     */
    public function procesarVenta(Request $request)
    {
        // DEBUG: Log de datos recibidos
        Log::info('=== PROCESANDO VENTA ===');
        Log::info('Request method: ' . $request->method());
        Log::info('Request URL: ' . $request->url());
        Log::info('Todos los datos:', $request->all());
        Log::info('Headers:', $request->headers->all());
        
        // Verificar que sea POST
        if (!$request->isMethod('post')) {
            Log::error('Método no es POST: ' . $request->method());
            return response()->json(['error' => 'Método no permitido'], 405);
        }

        try {
            // Validación inicial
            $validatedData = $request->validate([
                'horario_id' => 'required|exists:horarios,id',
                'asientos' => 'required|string',
                'pasajeros' => 'required|string'
            ]);
            
            Log::info('Datos validados correctamente:', $validatedData);

            DB::beginTransaction();

            $horario = Horario::with(['ruta', 'bus'])->findOrFail($request->horario_id);
            Log::info('Horario encontrado:', $horario->toArray());
            
            // Decodificar los datos JSON
            $asientos = json_decode($request->asientos, true);
            $datosPasajeros = json_decode($request->pasajeros, true);
            
            Log::info('Asientos decodificados:', $asientos);
            Log::info('Pasajeros decodificados:', $datosPasajeros);

            // Verificar que la decodificación fue exitosa
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception('Error al decodificar JSON: ' . json_last_error_msg());
            }

            // Verificar que la cantidad coincida
            if (!is_array($asientos) || !is_array($datosPasajeros)) {
                throw new \Exception('Los datos no son arrays válidos');
            }

            if (count($asientos) !== count($datosPasajeros)) {
                throw new \Exception('La cantidad de asientos no coincide con los pasajeros');
            }

            // Verificar disponibilidad de asientos
            $asientosOcupados = $this->getAsientosOcupados($horario->id);
            foreach ($asientos as $asiento) {
                if (in_array($asiento, $asientosOcupados)) {
                    throw new \Exception("El asiento {$asiento} ya está ocupado");
                }
            }

            // Crear la venta
            $venta = Venta::create([
                'codigo_venta' => $this->generarCodigoVentaUnico(),
                'user_id' => auth()->id(),
                'horario_id' => $horario->id,
                'cantidad_pasajes' => count($asientos),
                'total' => $horario->ruta->precio * count($asientos),
                'fecha_venta' => now(),
                'estado' => 'completada'
            ]);

            Log::info('Venta creada:', $venta->toArray());

            $tickets = [];

            // Crear pasajeros y tickets
            foreach ($datosPasajeros as $index => $datoPasajero) {
                Log::info("Procesando pasajero {$index}:", $datoPasajero);
                
                // Buscar o crear pasajero
                $pasajero = Pasajero::firstOrCreate(
                    ['dni' => $datoPasajero['dni']],
                    [
                        'nombre' => $datoPasajero['nombre'],
                        'telefono' => $datoPasajero['telefono'] ?? null,
                        'email' => $datoPasajero['email'] ?? null
                    ]
                );

                // Crear ticket
                $ticket = Ticket::create([
                    'numero_ticket' => $this->generarNumeroTicketUnico(),
                    'venta_id' => $venta->id,
                    'pasajero_id' => $pasajero->id,
                    'asiento' => $asientos[$index],
                    'qr_code' => $this->generarCodigoQR($venta->codigo_venta, $asientos[$index])
                ]);

                $tickets[] = $ticket;
                Log::info("Ticket creado para pasajero {$index}:", $ticket->toArray());
            }

            // Actualizar asientos disponibles
            $horario->decrement('asientos_disponibles', count($asientos));
            Log::info('Asientos actualizados. Disponibles ahora: ' . $horario->fresh()->asientos_disponibles);

            DB::commit();
            Log::info('Transacción confirmada exitosamente');

            // Generar PDF de los tickets
            $pdf = $this->generarTicketsPDF($venta, collect($tickets));

            Log::info('Redirigiendo a confirmación con venta ID: ' . $venta->id);
            
            return view('ventas.confirmacion', compact('venta', 'tickets', 'pdf'));

        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollback();
            Log::error('Error de validación:', $e->errors());
            return back()->withErrors($e->errors())->withInput();
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error general en procesarVenta:', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            return back()->with('error', 'Error al procesar la venta: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Generar código de venta único
     */
    private function generarCodigoVentaUnico()
    {
        do {
            $codigo = 'V-' . date('Ymd') . '-' . random_int(1000, 9999);
        } while (Venta::where('codigo_venta', $codigo)->exists());

        return $codigo;
    }

    /**
     * Generar número de ticket único
     */
    private function generarNumeroTicketUnico()
    {
        do {
            $numero = 'T-' . date('Ymd') . '-' . random_int(10000, 99999);
        } while (Ticket::where('numero_ticket', $numero)->exists());

        return $numero;
    }

    /**
     * Obtener asientos ocupados para un horario
     */
    private function getAsientosOcupados($horarioId)
    {
        return Ticket::whereHas('venta', function($query) use ($horarioId) {
            $query->where('horario_id', $horarioId)
                  ->where('estado', 'completada');
        })->pluck('asiento')->toArray();
    }

    /**
     * Generar mapa visual de asientos
     */
    private function generarMapaAsientos($capacidad, $asientosOcupados)
    {
        $asientos = [];
        $filas = ceil($capacidad / 4); // 4 asientos por fila (2-2)

        for ($fila = 1; $fila <= $filas; $fila++) {
            for ($columna = 1; $columna <= 4; $columna++) {
                $numeroAsiento = (($fila - 1) * 4) + $columna;
                
                if ($numeroAsiento <= $capacidad) {
                    $asientos[$fila][$columna] = [
                        'numero' => $numeroAsiento,
                        'ocupado' => in_array($numeroAsiento, $asientosOcupados),
                        'lado' => ($columna <= 2) ? 'izquierdo' : 'derecho'
                    ];
                }
            }
        }

        return $asientos;
    }

    /**
     * Generar código QR simple
     */
    private function generarCodigoQR($codigoVenta, $asiento)
    {
        return base64_encode($codigoVenta . '-' . $asiento . '-' . date('Y-m-d'));
    }

    /**
     * Generar PDF de tickets
     */
    private function generarTicketsPDF($venta, $tickets)
    {
        try {
            $data = [
                'venta' => $venta->load(['horario.ruta', 'horario.bus', 'user']),
                'tickets' => $tickets->load('pasajero'),
                'fecha_generacion' => now()
            ];

            $pdf = Pdf::loadView('tickets.pdf', $data);
            
            // Guardar PDF en storage/app/public/tickets
            $nombreArchivo = 'ticket_' . $venta->codigo_venta . '.pdf';
            $rutaArchivo = storage_path('app/public/tickets/' . $nombreArchivo);
            
            // Crear directorio si no existe
            if (!file_exists(dirname($rutaArchivo))) {
                mkdir(dirname($rutaArchivo), 0755, true);
            }
            
            $pdf->save($rutaArchivo);
            Log::info('PDF generado exitosamente: ' . $nombreArchivo);

            return $nombreArchivo;
        } catch (\Exception $e) {
            Log::error('Error generando PDF:', [
                'message' => $e->getMessage(),
                'venta_id' => $venta->id
            ]);
            return null;
        }
    }

    /**
     * Descargar PDF del ticket
     */
    public function descargarTicket($ventaId)
    {
        $venta = Venta::with(['tickets.pasajero', 'horario.ruta', 'horario.bus', 'user'])
                     ->findOrFail($ventaId);

        // Verificar que el usuario puede acceder a este ticket
        if (!auth()->user()->isAdmin() && $venta->user_id !== auth()->id()) {
            abort(403, 'No tienes permisos para descargar este ticket');
        }

        $data = [
            'venta' => $venta,
            'tickets' => $venta->tickets,
            'fecha_generacion' => now()
        ];

        $pdf = Pdf::loadView('tickets.pdf', $data);
        
        return $pdf->download('ticket_' . $venta->codigo_venta . '.pdf');
    }

    /**
     * Ver historial de ventas del empleado
     */
    public function historial()
    {
        $ventas = Venta::with(['horario.ruta', 'tickets'])
                      ->where('user_id', auth()->id())
                      ->orderBy('created_at', 'desc')
                      ->paginate(10);

        return view('ventas.historial', compact('ventas'));
    }
}