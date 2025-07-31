<?php

namespace App\Http\Controllers;

use App\Models\Ruta;
use App\Models\Horario;
use App\Models\Pasajero;
use App\Models\Venta;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class ClienteController extends Controller
{
    /**
     * Página principal del cliente
     */
    public function index()
    {
        $rutas = Ruta::where('activa', true)->get();
        return view('cliente.index', compact('rutas'));
    }

    /**
     * Buscar horarios disponibles
     */
    public function buscarHorarios(Request $request)
    {
        $request->validate([
            'ruta_id' => 'required|exists:rutas,id',
            'fecha' => 'required|date|after_or_equal:today'
        ]);

        $ruta = Ruta::findOrFail($request->ruta_id);
        
        $horarios = Horario::with('bus')
            ->where('ruta_id', $request->ruta_id)
            ->where('fecha', $request->fecha)
            ->where('activo', true)
            ->where('asientos_disponibles', '>', 0)
            ->orderBy('hora_salida')
            ->get();
            
        $fecha = $request->fecha;
        
        return view('cliente.horarios', compact('ruta', 'horarios', 'fecha'));
    }

    /**
     * Mostrar formulario de compra
     */
    public function formularioCompra(Request $request)
    {
        $request->validate([
            'horario_id' => 'required|exists:horarios,id',
            'cantidad' => 'required|integer|min:1|max:5'
        ]);

        $horario = Horario::with(['ruta', 'bus'])->findOrFail($request->horario_id);
        $cantidad = $request->cantidad;

        // Verificar disponibilidad
        if ($horario->asientos_disponibles < $cantidad) {
            return back()->with('error', 'No hay suficientes asientos disponibles');
        }

        return view('cliente.formulario', compact('horario', 'cantidad'));
    }

    /**
     * Procesar la compra del cliente
     */
    public function procesarCompra(Request $request)
    {
        $request->validate([
            'horario_id' => 'required|exists:horarios,id',
            'cantidad' => 'required|integer|min:1|max:5',
            'pasajeros' => 'required|array',
            'pasajeros.*.nombre' => 'required|string|max:255',
            'pasajeros.*.dni' => 'required|string|max:20',
            'pasajeros.*.telefono' => 'nullable|string|max:15',
            'pasajeros.*.email' => 'nullable|email',
            'email_contacto' => 'required|email',
            'telefono_contacto' => 'required|string|max:15',
        ]);

        try {
            DB::beginTransaction();

            $horario = Horario::with(['ruta', 'bus'])->findOrFail($request->horario_id);
            $cantidad = $request->cantidad;
            $pasajeros = $request->pasajeros;

            // Verificar disponibilidad nuevamente
            if ($horario->asientos_disponibles < $cantidad) {
                throw new \Exception('No hay suficientes asientos disponibles');
            }

            // Asignar asientos automáticamente (los primeros disponibles)
            $asientosOcupados = $this->getAsientosOcupados($horario->id);
            $asientosDisponibles = [];
            
            for ($i = 1; $i <= $horario->bus->capacidad; $i++) {
                if (!in_array($i, $asientosOcupados)) {
                    $asientosDisponibles[] = $i;
                }
                if (count($asientosDisponibles) >= $cantidad) {
                    break;
                }
            }

            // Crear la venta (sin usuario, es cliente web)
            $venta = Venta::create([
                'codigo_venta' => $this->generarCodigoVentaUnico(),
                'user_id' => null, // Venta de cliente web
                'horario_id' => $horario->id,
                'cantidad_pasajes' => $cantidad,
                'total' => $horario->ruta->precio * $cantidad,
                'fecha_venta' => now(),
                'estado' => 'completada'
            ]);

            $tickets = [];

            // Crear pasajeros y tickets
            foreach ($pasajeros as $index => $datoPasajero) {
                // Crear pasajero
                $pasajero = Pasajero::create([
                    'nombre' => $datoPasajero['nombre'],
                    'dni' => $datoPasajero['dni'],
                    'telefono' => $datoPasajero['telefono'] ?? $request->telefono_contacto,
                    'email' => $datoPasajero['email'] ?? $request->email_contacto
                ]);

                // Crear ticket con asiento asignado automáticamente
                $ticket = Ticket::create([
                    'numero_ticket' => $this->generarNumeroTicketUnico(),
                    'venta_id' => $venta->id,
                    'pasajero_id' => $pasajero->id,
                    'asiento' => $asientosDisponibles[$index],
                    'qr_code' => $this->generarCodigoQR($venta->codigo_venta, $asientosDisponibles[$index])
                ]);

                $tickets[] = $ticket;
            }

            // Actualizar asientos disponibles
            $horario->decrement('asientos_disponibles', $cantidad);

            DB::commit();

            // Generar PDF
            $pdf = $this->generarTicketsPDF($venta, collect($tickets));

            return view('cliente.confirmacion', compact('venta', 'tickets', 'pdf'));

        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Error al procesar la compra: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Descargar ticket del cliente
     */
    public function descargarTicket($codigoVenta)
    {
        $venta = Venta::with(['tickets.pasajero', 'horario.ruta', 'horario.bus'])
                     ->where('codigo_venta', $codigoVenta)
                     ->firstOrFail();

        $data = [
            'venta' => $venta,
            'tickets' => $venta->tickets,
            'fecha_generacion' => now(),
            'es_cliente' => true
        ];

        $pdf = Pdf::loadView('tickets.pdf', $data);
        
        return $pdf->download('tickets_' . $venta->codigo_venta . '.pdf');
    }

    // Métodos auxiliares similares a VentaController
    private function generarCodigoVentaUnico()
    {
        do {
            $codigo = 'WEB-' . date('Ymd') . '-' . random_int(1000, 9999);
        } while (Venta::where('codigo_venta', $codigo)->exists());

        return $codigo;
    }

    private function generarNumeroTicketUnico()
    {
        do {
            $numero = 'TW-' . date('Ymd') . '-' . random_int(10000, 99999);
        } while (Ticket::where('numero_ticket', $numero)->exists());

        return $numero;
    }

    private function getAsientosOcupados($horarioId)
    {
        return Ticket::whereHas('venta', function($query) use ($horarioId) {
            $query->where('horario_id', $horarioId)
                  ->where('estado', 'completada');
        })->pluck('asiento')->toArray();
    }

    private function generarCodigoQR($codigoVenta, $asiento)
    {
        return base64_encode($codigoVenta . '-' . $asiento . '-' . date('Y-m-d'));
    }

    private function generarTicketsPDF($venta, $tickets)
    {
        try {
            $data = [
                'venta' => $venta->load(['horario.ruta', 'horario.bus']),
                'tickets' => $tickets->load('pasajero'),
                'fecha_generacion' => now(),
                'es_cliente' => true
            ];

            $pdf = Pdf::loadView('tickets.pdf', $data);
            
            $nombreArchivo = 'tickets_web_' . $venta->codigo_venta . '.pdf';
            $rutaArchivo = storage_path('app/public/tickets/' . $nombreArchivo);
            
            if (!file_exists(dirname($rutaArchivo))) {
                mkdir(dirname($rutaArchivo), 0755, true);
            }
            
            $pdf->save($rutaArchivo);

            return $nombreArchivo;
        } catch (\Exception $e) {
            return null;
        }
    }
}