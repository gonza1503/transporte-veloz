<?php
// app/Http/Controllers/AdminController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Ruta;
use App\Models\Bus;
use App\Models\Venta;
use App\Models\Horario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Panel principal del administrador
     */
    public function dashboard()
    {
        // Estadísticas generales
        $stats = [
            'ventas_hoy' => Venta::whereDate('fecha_venta', today())->count(),
            'ingresos_hoy' => Venta::whereDate('fecha_venta', today())->sum('total'),
            'ventas_mes' => Venta::whereMonth('fecha_venta', now()->month)->count(),
            'ingresos_mes' => Venta::whereMonth('fecha_venta', now()->month)->sum('total'),
            'total_empleados' => User::where('active', true)->count(),
            'total_rutas' => Ruta::where('activa', true)->count(),
            'total_buses' => Bus::where('estado', 'activo')->count(),
        ];

        // Ventas de los últimos 7 días para gráfico
        $ventasSemanales = [];
        for ($i = 6; $i >= 0; $i--) {
            $fecha = Carbon::now()->subDays($i);
            $ventasSemanales[] = [
                'fecha' => $fecha->format('d/m'),
                'ventas' => Venta::whereDate('fecha_venta', $fecha)->count(),
                'ingresos' => Venta::whereDate('fecha_venta', $fecha)->sum('total')
            ];
        }

        // Top 5 rutas más vendidas este mes
        $topRutas = Ruta::withCount(['horarios as ventas_count' => function($query) {
            $query->join('ventas', 'horarios.id', '=', 'ventas.horario_id')
                  ->whereMonth('ventas.fecha_venta', now()->month);
        }])->orderBy('ventas_count', 'desc')->take(5)->get();

        return view('admin.dashboard', compact('stats', 'ventasSemanales', 'topRutas'));
    }

    /**
     * Gestión de empleados
     */
    public function empleados()
    {
        $users = User::orderBy('name')->paginate(10);
        return view('admin.empleados.index', compact('users'));
    }

    /**
     * Crear nuevo empleado
     */
    public function crearEmpleado()
    {
        return view('admin.empleados.create');
    }

    /**
     * Guardar nuevo empleado
     */
    public function guardarEmpleado(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed',
            'role' => 'required|in:admin,empleado'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'active' => true
        ]);

        return redirect()->route('admin.empleados')->with('success', 'Empleado creado correctamente');
    }

    /**
     * Editar empleado
     */
    public function editarEmpleado(User $empleado)
    {
        return view('admin.empleados.edit', compact('empleado'));
    }

    /**
     * Actualizar empleado
     */
    public function actualizarEmpleado(Request $request, User $empleado)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $empleado->id,
            'role' => 'required|in:admin,empleado',
            'active' => 'boolean'
        ]);

        $data = $request->only(['name', 'email', 'role', 'active']);

        // Solo actualizar contraseña si se proporcionó
        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6|confirmed']);
            $data['password'] = Hash::make($request->password);
        }

        $empleado->update($data);

        return redirect()->route('admin.empleados')->with('success', 'Empleado actualizado correctamente');
    }

    /**
     * Reportes de ventas
     */
    public function reportes(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));
        $empleadoId = $request->get('empleado_id');

        // Query base
        $query = Venta::with(['user', 'horario.ruta', 'tickets'])
                     ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);

        // Filtrar por empleado si se especifica
        if ($empleadoId) {
            $query->where('user_id', $empleadoId);
        }

        $ventas = $query->orderBy('fecha_venta', 'desc')->paginate(15);

        // Resumen
        $resumen = [
            'total_ventas' => $query->count(),
            'total_ingresos' => $query->sum('total'),
            'total_pasajes' => $query->sum('cantidad_pasajes'),
            'promedio_venta' => $query->avg('total')
        ];

        $empleados = User::where('active', true)->get();

        return view('admin.reportes', compact('ventas', 'resumen', 'empleados', 'fechaInicio', 'fechaFin', 'empleadoId'));
    }

    /**
     * Gestión de horarios
     */
    public function horarios()
    {
        $horarios = Horario::with(['ruta', 'bus'])
                          ->orderBy('fecha', 'desc')
                          ->orderBy('hora_salida')
                          ->paginate(15);

        return view('admin.horarios.index', compact('horarios'));
    }

    /**
     * Crear nuevo horario
     */
    public function crearHorario()
    {
        $rutas = Ruta::where('activa', true)->get();
        $buses = Bus::where('estado', 'activo')->get();
        
        return view('admin.horarios.create', compact('rutas', 'buses'));
    }

    /**
     * Guardar nuevo horario
     */
    public function guardarHorario(Request $request)
    {
        $request->validate([
            'ruta_id' => 'required|exists:rutas,id',
            'bus_id' => 'required|exists:buses,id',
            'fecha' => 'required|date|after_or_equal:today',
            'hora_salida' => 'required'
        ]);

        $bus = Bus::findOrFail($request->bus_id);

        Horario::create([
            'ruta_id' => $request->ruta_id,
            'bus_id' => $request->bus_id,
            'fecha' => $request->fecha,
            'hora_salida' => $request->hora_salida,
            'asientos_disponibles' => $bus->capacidad,
            'activo' => true
        ]);

        return redirect()->route('admin.horarios.index')->with('success', 'Horario creado correctamente');
    }

    /**
     * Editar horario
     */
    public function editarHorario(Horario $horario)
    {
        $rutas = Ruta::where('activa', true)->get();
        $buses = Bus::where('estado', 'activo')->get();
        
        return view('admin.horarios.edit', compact('horario', 'rutas', 'buses'));
    }

    /**
     * Actualizar horario
     */
    public function actualizarHorario(Request $request, Horario $horario)
    {
        $request->validate([
            'ruta_id' => 'required|exists:rutas,id',
            'bus_id' => 'required|exists:buses,id',
            'fecha' => 'required|date|after_or_equal:today',
            'hora_salida' => 'required',
            'activo' => 'boolean'
        ]);

        $horario->update($request->all());

        return redirect()->route('admin.horarios.index')->with('success', 'Horario actualizado correctamente');
    }

    /**
     * Cancelar horario
     */
    public function cancelarHorario(Horario $horario)
    {
        // Verificar si ya tiene ventas
        if ($horario->ventas()->count() > 0) {
            return back()->with('error', 'No se puede cancelar un horario que ya tiene ventas');
        }

        $horario->update(['activo' => false]);
        
        return back()->with('success', 'Horario cancelado correctamente');
    }

    /**
     * Exportar reporte a Excel
     */
    public function exportarExcel(Request $request)
    {
        // Implementación básica - puedes usar maatwebsite/excel después
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));
        
        return redirect()->back()->with('info', 'Funcionalidad de exportación Excel en desarrollo');
    }

    /**
     * Exportar reporte a PDF
     */
    public function exportarPDF(Request $request)
    {
        $fechaInicio = $request->get('fecha_inicio', now()->startOfMonth()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', now()->format('Y-m-d'));
        $empleadoId = $request->get('empleado_id');

        $query = Venta::with(['user', 'horario.ruta', 'tickets'])
                     ->whereBetween('fecha_venta', [$fechaInicio, $fechaFin]);

        if ($empleadoId) {
            $query->where('user_id', $empleadoId);
        }

        $ventas = $query->orderBy('fecha_venta', 'desc')->get();

        $resumen = [
            'total_ventas' => $ventas->count(),
            'total_ingresos' => $ventas->sum('total'),
            'total_pasajes' => $ventas->sum('cantidad_pasajes'),
            'promedio_venta' => $ventas->avg('total'),
            'fecha_inicio' => $fechaInicio,
            'fecha_fin' => $fechaFin
        ];

        // Implementación básica - requiere configurar DomPDF
        return redirect()->back()->with('info', 'Funcionalidad de exportación PDF en desarrollo');
    }
}