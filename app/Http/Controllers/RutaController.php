<?php
// app/Http/Controllers/RutaController.php

namespace App\Http\Controllers;

use App\Models\Ruta;
use Illuminate\Http\Request;

class RutaController extends Controller
{
    /**
     * Mostrar todas las rutas
     */
    public function index()
    {
        $rutas = Ruta::orderBy('codigo')->paginate(10);
        return view('rutas.index', compact('rutas'));
    }

    /**
     * Mostrar formulario para crear nueva ruta
     */
    public function create()
    {
        return view('rutas.create');
    }

    /**
     * Guardar nueva ruta
     */
    public function store(Request $request)
    {
        // Validar los datos
        $request->validate([
            'codigo' => 'required|unique:rutas|max:20',
            'origen' => 'required|string|max:100',
            'destino' => 'required|string|max:100',
            'precio' => 'required|numeric|min:0',
            'duracion_minutos' => 'required|integer|min:1',
            'descripcion' => 'nullable|string|max:500'
        ], [
            'codigo.required' => 'El código es obligatorio',
            'codigo.unique' => 'Este código ya existe',
            'origen.required' => 'El origen es obligatorio',
            'destino.required' => 'El destino es obligatorio',
            'precio.required' => 'El precio es obligatorio',
            'precio.numeric' => 'El precio debe ser un número',
            'duracion_minutos.required' => 'La duración es obligatoria',
            'duracion_minutos.integer' => 'La duración debe ser un número entero'
        ]);

        // Crear la nueva ruta
        Ruta::create([
            'codigo' => $request->codigo,
            'origen' => $request->origen,
            'destino' => $request->destino,
            'precio' => $request->precio,
            'duracion_minutos' => $request->duracion_minutos,
            'descripcion' => $request->descripcion,
            'activa' => true
        ]);

        return redirect()->route('rutas.index')->with('success', 'Ruta creada correctamente');
    }

    /**
     * Mostrar una ruta específica
     */
    public function show(Ruta $ruta)
    {
        // Cargar los horarios relacionados
        $ruta->load(['horarios' => function($query) {
            $query->with('bus')->orderBy('fecha', 'desc')->take(10);
        }]);

        // Estadísticas de la ruta
        $stats = [
            'horarios_programados' => $ruta->horarios()->count(),
            'ventas_realizadas' => $ruta->horarios()->withCount('ventas')->get()->sum('ventas_count'),
            'ingresos_generados' => $ruta->horarios()->with('ventas')->get()->sum(function($horario) {
                return $horario->ventas->sum('total');
            }),
            'proximos_viajes' => $ruta->horarios()
                ->where('fecha', '>=', now()->format('Y-m-d'))
                ->where('activo', true)
                ->count()
        ];

        return view('rutas.show', compact('ruta', 'stats'));
    }

    /**
     * Mostrar formulario para editar ruta
     */
    public function edit(Ruta $ruta)
    {
        return view('rutas.edit', compact('ruta'));
    }

    /**
     * Actualizar ruta
     */
    public function update(Request $request, Ruta $ruta)
    {
        // Validar los datos (excepto el código si no ha cambiado)
        $request->validate([
            'codigo' => 'required|max:20|unique:rutas,codigo,' . $ruta->id,
            'origen' => 'required|string|max:100',
            'destino' => 'required|string|max:100',
            'precio' => 'required|numeric|min:0',
            'duracion_minutos' => 'required|integer|min:1',
            'descripcion' => 'nullable|string|max:500',
            'activa' => 'boolean'
        ]);

        // Actualizar la ruta
        $ruta->update([
            'codigo' => $request->codigo,
            'origen' => $request->origen,
            'destino' => $request->destino,
            'precio' => $request->precio,
            'duracion_minutos' => $request->duracion_minutos,
            'descripcion' => $request->descripcion,
            'activa' => $request->has('activa') ? true : false
        ]);

        return redirect()->route('rutas.index')->with('success', 'Ruta actualizada correctamente');
    }

    /**
     * Eliminar ruta
     */
    public function destroy(Ruta $ruta)
    {
        // Verificar si la ruta tiene horarios asociados
        if ($ruta->horarios()->count() > 0) {
            return back()->with('error', 'No se puede eliminar la ruta porque tiene horarios asociados');
        }

        $ruta->delete();
        return redirect()->route('rutas.index')->with('success', 'Ruta eliminada correctamente');
    }

    /**
     * Cambiar estado de la ruta (activa/inactiva)
     */
    public function toggleEstado(Ruta $ruta)
    {
        $ruta->update(['activa' => !$ruta->activa]);
        
        $estado = $ruta->activa ? 'activada' : 'desactivada';
        return back()->with('success', "Ruta {$estado} correctamente");
    }
    }
