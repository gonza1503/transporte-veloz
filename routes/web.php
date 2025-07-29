<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\RutaController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas de Autenticación
|--------------------------------------------------------------------------
*/

// Redirigir la página principal al login
Route::get('/', function () {
    return redirect()->route('login');
});

// Rutas de login/logout (accesibles sin autenticación)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Requieren Autenticación)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | Rutas para EMPLEADOS (Ventas)
    |--------------------------------------------------------------------------
    */

    // Panel principal de ventas
    Route::get('/ventas', [VentaController::class, 'index'])->name('ventas.index');

    // Proceso de venta paso a paso
    Route::post('/ventas/horarios', [VentaController::class, 'horarios'])->name('ventas.horarios');
    Route::post('/ventas/asientos', [VentaController::class, 'asientos'])->name('ventas.asientos');
    Route::post('/ventas/procesar', [VentaController::class, 'procesarVenta'])->name('ventas.procesar');

    // Descargar ticket
    Route::get('/ventas/ticket/{venta}/pdf', [VentaController::class, 'descargarTicket'])->name('ventas.ticket.pdf');

    // Historial de ventas del empleado
    Route::get('/ventas/historial', [VentaController::class, 'historial'])->name('ventas.historial');


    /*
    |--------------------------------------------------------------------------
    | Rutas para ADMINISTRADORES
    |--------------------------------------------------------------------------
    */

    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {

        // Dashboard principal
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Gestión de empleados
        Route::get('/empleados', [AdminController::class, 'empleados'])->name('empleados');
        Route::get('/empleados/crear', [AdminController::class, 'crearEmpleado'])->name('empleados.crear');
        Route::post('/empleados', [AdminController::class, 'guardarEmpleado'])->name('empleados.guardar');
        Route::get('/empleados/{empleado}/editar', [AdminController::class, 'editarEmpleado'])->name('empleados.editar');
        Route::put('/empleados/{empleado}', [AdminController::class, 'actualizarEmpleado'])->name('empleados.actualizar');

        // Gestión de rutas (CRUD completo)
        Route::resource('rutas', RutaController::class);
        Route::patch('/rutas/{ruta}/toggle', [RutaController::class, 'toggleEstado'])->name('rutas.toggle');

        // Gestión de buses (CRUD completo)
        Route::resource('buses', BusController::class);
        Route::patch('/buses/{bus}/estado', [BusController::class, 'cambiarEstado'])->name('buses.cambiarEstado');

        // Gestión de horarios (CRUD manual para mantener nombres y rutas claras)
        Route::get('/horarios', [AdminController::class, 'horarios'])->name('horarios.index');
        Route::get('/horarios/crear', [AdminController::class, 'crearHorario'])->name('horarios.create');
        Route::post('/horarios', [AdminController::class, 'guardarHorario'])->name('horarios.store');
        Route::get('/horarios/{horario}/editar', [AdminController::class, 'editarHorario'])->name('horarios.edit');
        Route::put('/horarios/{horario}', [AdminController::class, 'actualizarHorario'])->name('horarios.update');
        Route::patch('/horarios/{horario}/cancelar', [AdminController::class, 'cancelarHorario'])->name('horarios.cancelar');

        // Reportes y exportaciones
        Route::get('/reportes', [AdminController::class, 'reportes'])->name('reportes');
        Route::get('/reportes/excel', [AdminController::class, 'exportarExcel'])->name('reportes.excel');
        Route::get('/reportes/pdf', [AdminController::class, 'exportarPDF'])->name('reportes.pdf');
    });

    /*
    |--------------------------------------------------------------------------
    | Rutas Compartidas (Admins y Empleados)
    |--------------------------------------------------------------------------
    */

    // Ver información de rutas (solo lectura para empleados)
    Route::get('/rutas', [RutaController::class, 'index'])->name('rutas.index');
    Route::get('/rutas/{ruta}', [RutaController::class, 'show'])->name('rutas.show');

    // Ver información de buses (solo lectura para empleados)
    Route::get('/buses', [BusController::class, 'index'])->name('buses.index');
    Route::get('/buses/{bus}', [BusController::class, 'show'])->name('buses.show');
});

/*
|--------------------------------------------------------------------------
| Rutas de API (Para AJAX)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->prefix('api')->group(function () {

    // Obtener horarios disponibles por ruta y fecha (para JavaScript)
    Route::get('/horarios-disponibles', function (Illuminate\Http\Request $request) {
        $horarios = \App\Models\Horario::with('bus')
            ->where('ruta_id', $request->ruta_id)
            ->where('fecha', $request->fecha)
            ->where('activo', true)
            ->where('asientos_disponibles', '>', 0)
            ->orderBy('hora_salida')
            ->get();

        return response()->json($horarios);
    });

    // Obtener asientos ocupados por horario
    Route::get('/asientos-ocupados/{horario}', function ($horarioId) {
        $asientosOcupados = \App\Models\Ticket::whereHas('venta', function ($query) use ($horarioId) {
            $query->where('horario_id', $horarioId)
                ->where('estado', 'completada');
        })->pluck('asiento');

        return response()->json($asientosOcupados);
    });
});

/*
|--------------------------------------------------------------------------
| Manejo de Errores
|--------------------------------------------------------------------------
*/

// Ruta para errores 404 personalizada
Route::fallback(function () {
    return view('errors.404');
});
