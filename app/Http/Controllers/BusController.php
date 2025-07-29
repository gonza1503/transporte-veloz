<?php
// app/Http/Controllers/BusController.php

namespace App\Http\Controllers;

use App\Models\Bus;
use Illuminate\Http\Request;

class BusController extends Controller
{
    /**
     * Mostrar lista de buses
     */
    public function index()
    {
        $buses = Bus::orderBy('placa')->paginate(10);
        return view('buses.index', compact('buses'));
    }

    /**
     * Mostrar formulario para crear nuevo bus
     */
    public function create()
    {
        return view('buses.create');
    }

    /**
     * Guardar nuevo bus
     */
    public function store(Request $request)
    {
        $request->validate([
            'placa' => 'required|unique:buses|regex:/^[A-Z0-9\-]+$/|max:10',
            'modelo' => 'required|string|max:100',
            'anio' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'capacidad' => 'required|integer|min:10|max:60',
            'chofer' => 'required|string|max:255',
            'observaciones' => 'nullable|string|max:500'
        ], [
            'placa.required' => 'La placa es obligatoria',
            'placa.unique' => 'Esta placa ya está registrada',
            'placa.regex' => 'La placa debe contener solo letras, números y guiones',
            'modelo.required' => 'El modelo es obligatorio',
            'anio.required' => 'El año es obligatorio',
            'anio.integer' => 'El año debe ser un número',
            'anio.min' => 'El año debe ser mayor a 1990',
            'anio.max' => 'El año no puede ser mayor al próximo año',
            'capacidad.required' => 'La capacidad es obligatoria',
            'capacidad.integer' => 'La capacidad debe ser un número entero',
            'capacidad.min' => 'La capacidad mínima es 10 asientos',
            'capacidad.max' => 'La capacidad máxima es 60 asientos',
            'chofer.required' => 'El nombre del chofer es obligatorio'
        ]);

        Bus::create([
            'placa' => strtoupper($request->placa),
            'modelo' => $request->modelo,
            'anio' => $request->anio,
            'capacidad' => $request->capacidad,
            'chofer' => $request->chofer,
            'estado' => 'activo',
            'observaciones' => $request->observaciones
        ]);

        return redirect()->route('admin.buses.index')->with('success', 'Bus registrado correctamente');
    }

    /**
     * Mostrar detalles de un bus específico
     */
    public function show(Bus $bus)
    {
        // Cargar horarios con relaciones
        $bus->load(['horarios' => function($query) {
            $query->with('ruta')->orderBy('fecha', 'desc')->limit(10);
        }]);

        // Estadísticas del bus
        $stats = [
            'viajes_realizados' => $bus->horarios()->whereHas('ventas')->count(),
            'total_pasajeros' => $bus->horarios()->withSum('ventas', 'cantidad_pasajes')->get()->sum('ventas_sum_cantidad_pasajes'),
            'ingresos_generados' => $bus->horarios()->withSum('ventas', 'total')->get()->sum('ventas_sum_total'),
            'proximo_viaje' => $bus->horarios()->where('fecha', '>=', now()->format('Y-m-d'))->where('activo', true)->orderBy('fecha')->orderBy('hora_salida')->first()
        ];

        return view('buses.show', compact('bus', 'stats'));
    }

    /**
     * Mostrar formulario para editar bus
     */
    public function edit(Bus $bus)
    {
        return view('buses.edit', compact('bus'));
    }

    /**
     * Actualizar bus
     */
    public function update(Request $request, Bus $bus)
    {
        $request->validate([
            'placa' => 'required|regex:/^[A-Z0-9\-]+$/|max:10|unique:buses,placa,' . $bus->id,
            'modelo' => 'required|string|max:100',
            'anio' => 'required|integer|min:1990|max:' . (date('Y') + 1),
            'capacidad' => 'required|integer|min:10|max:60',
            'chofer' => 'required|string|max:255',
            'estado' => 'required|in:activo,mantenimiento,ocupado',
            'observaciones' => 'nullable|string|max:500'
        ]);

        // Verificar si se puede cambiar la capacidad
        if ($request->capacidad != $bus->capacidad) {
            $horariosConVentas = $bus->horarios()->whereHas('ventas')->count();
            if ($horariosConVentas > 0) {
                return back()->with('error', 'No se puede cambiar la capacidad porque el bus tiene ventas registradas');
            }
        }

        $bus->update([
            'placa' => strtoupper($request->placa),
            'modelo' => $request->modelo,
            'anio' => $request->anio,
            'capacidad' => $request->capacidad,
            'chofer' => $request->chofer,
            'estado' => $request->estado,
            'observaciones' => $request->observaciones
        ]);

        return redirect()->route('admin.buses.index')->with('success', 'Bus actualizado correctamente');
    }

    /**
     * Eliminar bus
     */
    public function destroy(Bus $bus)
    {
        // Verificar si el bus tiene horarios asociados
        if ($bus->horarios()->count() > 0) {
            return back()->with('error', 'No se puede eliminar el bus porque tiene horarios asociados');
        }

        $bus->delete();
        return redirect()->route('admin.buses.index')->with('success', 'Bus eliminado correctamente');
    }

    /**
     * Cambiar estado del bus
     */
    public function cambiarEstado(Request $request, Bus $bus)
    {
        $request->validate([
            'estado' => 'required|in:activo,mantenimiento,ocupado'
        ]);

        $estadoAnterior = $bus->estado;
        $bus->update(['estado' => $request->estado]);

        // Si se pone en mantenimiento, desactivar horarios futuros
        if ($request->estado === 'mantenimiento') {
            $bus->horarios()
                ->where('fecha', '>=', now()->format('Y-m-d'))
                ->where('activo', true)
                ->update(['activo' => false]);
        }

        return back()->with('success', "Estado del bus cambiado de '{$estadoAnterior}' a '{$request->estado}'");
    }

    /**
     * Obtener disponibilidad del bus para una fecha
     */
    public function disponibilidad(Request $request, Bus $bus)
    {
        $fecha = $request->get('fecha', now()->format('Y-m-d'));
        
        $horarios = $bus->horarios()
            ->where('fecha', $fecha)
            ->with('ruta')
            ->orderBy('hora_salida')
            ->get();

        return response()->json([
            'fecha' => $fecha,
            'bus' => [
                'id' => $bus->id,
                'placa' => $bus->placa,
                'modelo' => $bus->modelo,
                'capacidad' => $bus->capacidad,
                'estado' => $bus->estado
            ],
            'horarios' => $horarios->map(function($horario) {
                return [
                    'id' => $horario->id,
                    'hora_salida' => $horario->hora_salida,
                    'ruta' => $horario->ruta->codigo . ' - ' . $horario->ruta->origen . ' → ' . $horario->ruta->destino,
                    'asientos_disponibles' => $horario->asientos_disponibles,
                    'activo' => $horario->activo
                ];
            })
        ]);
    }

    /**
     * Generar reporte de un bus específico
     */
    public function reporte(Bus $bus, Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));

        $horarios = $bus->horarios()
            ->whereBetween('fecha', [$fechaInicio, $fechaFin])
            ->with(['ruta', 'ventas'])
            ->orderBy('fecha', 'desc')
            ->get();

        $estadisticas = [
            'total_viajes' => $horarios->count(),
            'viajes_con_ventas' => $horarios->filter(function($horario) {
                return $horario->ventas->count() > 0;
            })->count(),
            'total_pasajeros' => $horarios->sum(function($horario) {
                return $horario->ventas->sum('cantidad_pasajes');
            }),
            'total_ingresos' => $horarios->sum(function($horario) {
                return $horario->ventas->sum('total');
            }),
            'promedio_ocupacion' => $horarios->count() > 0 ? 
                ($bus->capacidad - $horarios->avg('asientos_disponibles')) / $bus->capacidad * 100 : 0
        ];

        return view('buses.reporte', compact('bus', 'horarios', 'estadisticas', 'fechaInicio', 'fechaFin'));
    }
}