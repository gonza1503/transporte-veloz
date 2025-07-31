<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\RutaController;
use App\Http\Controllers\BusController;
use App\Http\Controllers\AdminController;

/*
|--------------------------------------------------------------------------
| Rutas públicas
|--------------------------------------------------------------------------
*/

// Redirige "/" al login
Route::get('/', fn() => redirect()->route('login'));

// Autenticación
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Rutas protegidas (requieren login)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | RUTAS PARA EMPLEADOS (VENTAS)
    |--------------------------------------------------------------------------
    */

    Route::prefix('ventas')->name('ventas.')->group(function () {
        Route::get('/', [VentaController::class, 'index'])->name('index');
        Route::post('/horarios', [VentaController::class, 'horarios'])->name('horarios');
        Route::post('/asientos', [VentaController::class, 'asientos'])->name('asientos');
        Route::post('/procesar', [VentaController::class, 'procesarVenta'])->name('procesar');
        Route::get('/ticket/{venta}/pdf', [VentaController::class, 'descargarTicket'])->name('ticket.pdf');
        Route::get('/historial', [VentaController::class, 'historial'])->name('historial');
    });

    /*
    |--------------------------------------------------------------------------
    | RUTAS PARA ADMINISTRADORES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {

        // Dashboard
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

        // Empleados
        Route::get('/empleados', [AdminController::class, 'empleados'])->name('empleados');
        Route::get('/empleados/crear', [AdminController::class, 'crearEmpleado'])->name('empleados.crear');
        Route::post('/empleados', [AdminController::class, 'guardarEmpleado'])->name('empleados.guardar');
        Route::get('/empleados/{empleado}/editar', [AdminController::class, 'editarEmpleado'])->name('empleados.editar');
        Route::put('/empleados/{empleado}', [AdminController::class, 'actualizarEmpleado'])->name('empleados.actualizar');

        // Rutas
        Route::resource('rutas', RutaController::class);
        Route::patch('/rutas/{ruta}/toggle', [RutaController::class, 'toggleEstado'])->name('rutas.toggle');

        // Buses
        Route::resource('buses', BusController::class);
        Route::patch('/buses/{bus}/estado', [BusController::class, 'cambiarEstado'])->name('buses.cambiarEstado');

        // Horarios - Rutas completas para CRUD
        Route::get('/horarios', [AdminController::class, 'horarios'])->name('horarios.index');
        Route::get('/horarios/crear', [AdminController::class, 'crearHorario'])->name('horarios.create');
        Route::post('/horarios', [AdminController::class, 'guardarHorario'])->name('horarios.store');
        Route::get('/horarios/{horario}/editar', [AdminController::class, 'editarHorario'])->name('horarios.edit');
        Route::put('/horarios/{horario}', [AdminController::class, 'actualizarHorario'])->name('horarios.update');
        Route::patch('/horarios/{horario}/cancelar', [AdminController::class, 'cancelarHorario'])->name('horarios.cancelar');
        Route::delete('/horarios/{horario}', [AdminController::class, 'eliminarHorario'])->name('horarios.destroy');

        // Reportes
        Route::get('/reportes', [AdminController::class, 'reportes'])->name('reportes');
        Route::get('/reportes/excel', [AdminController::class, 'exportarExcel'])->name('reportes.excel');
        Route::get('/reportes/pdf', [AdminController::class, 'exportarPDF'])->name('reportes.pdf');

        // Obtener horarios por fecha (para el panel lateral)
        Route::get('/horarios-fecha/{fecha}', function ($fecha) {
            $horarios = \App\Models\Horario::with(['ruta', 'bus'])
                ->where('fecha', $fecha)
                ->orderBy('hora_salida')
                ->get();

            return response()->json($horarios);
        });

    });

    /*
    |--------------------------------------------------------------------------
    | RUTAS COMPARTIDAS (Admin y Empleados)
    |--------------------------------------------------------------------------
    */

    // Rutas
    Route::get('/rutas', [RutaController::class, 'index'])->name('rutas.index');
    Route::get('/rutas/{ruta}', [RutaController::class, 'show'])->name('rutas.show');

    // Buses
    Route::get('/buses', [BusController::class, 'index'])->name('buses.index');
    Route::get('/buses/{bus}', [BusController::class, 'show'])->name('buses.show');
});

/*
|--------------------------------------------------------------------------
| Ruta de depuración (opcional)
|--------------------------------------------------------------------------
*/
Route::get('/debug-routes', function () {
    return response()->json([
        'ventas.procesar' => route('ventas.procesar'),
        'method' => 'POST',
        'middleware' => ['auth'],
        'timestamp' => now()
    ]);
})->middleware('auth');
