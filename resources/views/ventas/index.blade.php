@extends('layouts.app')

@section('title', 'Nueva Venta - Transporte Veloz')

@section('breadcrumb')
    <li class="breadcrumb-item active">Nueva Venta</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">
                    <i class="bi bi-cart-plus text-primary me-2"></i>
                    Nueva Venta de Pasajes
                </h1>
                <p class="text-muted">Selecciona la ruta y fecha para comenzar la venta</p>
            </div>
            <div class="text-end">
                <small class="text-muted d-block">Empleado: {{ auth()->user()->name }}</small>
                <small class="text-muted">{{ now()->format('d/m/Y H:i') }}</small>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-1-circle me-2"></i>
                    Paso 1: Seleccionar Ruta y Fecha
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('ventas.horarios') }}" method="POST" id="rutaForm">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="ruta_id" class="form-label">
                                <i class="bi bi-map me-1"></i>Ruta de Viaje
                            </label>
                            <select class="form-select @error('ruta_id') is-invalid @enderror" 
                                    id="ruta_id" 
                                    name="ruta_id" 
                                    required>
                                <option value="">Selecciona una ruta...</option>
                                @foreach($rutas as $ruta)
                                    <option value="{{ $ruta->id }}" 
                                            data-precio="{{ $ruta->precio }}"
                                            data-duracion="{{ $ruta->getDuracionFormateada() }}"
                                            {{ old('ruta_id') == $ruta->id ? 'selected' : '' }}>
                                        {{ $ruta->codigo }} - {{ $ruta->origen }} → {{ $ruta->destino }}
                                    </option>
                                @endforeach
                            </select>
                            @error('ruta_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="fecha" class="form-label">
                                <i class="bi bi-calendar me-1"></i>Fecha de Viaje
                            </label>
                            <input type="date" 
                                   class="form-control @error('fecha') is-invalid @enderror" 
                                   id="fecha" 
                                   name="fecha" 
                                   value="{{ old('fecha', now()->format('Y-m-d')) }}"
                                   min="{{ now()->format('Y-m-d') }}"
                                   max="{{ now()->addMonths(3)->format('Y-m-d') }}"
                                   required>
                            @error('fecha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- Información de la ruta seleccionada -->
                    <div id="rutaInfo" class="alert alert-info d-none">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h6 class="mb-1">
                                    <i class="bi bi-info-circle me-1"></i>Información de la Ruta
                                </h6>
                                <div id="rutaDetalles"></div>
                            </div>
                            <div class="col-md-4 text-end">
                                <div class="fs-4 fw-bold text-success" id="rutaPrecio"></div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <i class="bi bi-arrow-right me-2"></i>
                            Buscar Horarios Disponibles
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Panel de ayuda -->
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-lightbulb me-2"></i>Guía Rápida
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-start mb-3">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                         style="min-width: 30px; height: 30px; font-size: 14px;">1</div>
                    <div>
                        <strong>Seleccionar Ruta</strong>
                        <p class="small text-muted mb-0">Elige el origen y destino del viaje</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-start mb-3">
                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                         style="min-width: 30px; height: 30px; font-size: 14px;">2</div>
                    <div>
                        <strong>Elegir Horario</strong>
                        <p class="small text-muted mb-0">Selecciona el horario de salida disponible</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-start mb-3">
                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                         style="min-width: 30px; height: 30px; font-size: 14px;">3</div>
                    <div>
                        <strong>Asignar Asientos</strong>
                        <p class="small text-muted mb-0">Selecciona los asientos en el mapa del bus</p>
                    </div>
                </div>
                
                <div class="d-flex align-items-start">
                    <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                         style="min-width: 30px; height: 30px; font-size: 14px;">4</div>
                    <div>
                        <strong>Datos del Pasajero</strong>
                        <p class="small text-muted mb-0">Ingresa la información de cada pasajero</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Estadísticas rápidas -->
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-success text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-graph-up me-2"></i>Mis Ventas Hoy
                </h6>
            </div>
            <div class="card-body text-center">
                @php
                    $ventasHoy = \App\Models\Venta::where('user_id', auth()->id())
                                                 ->whereDate('fecha_venta', today())
                                                 ->count();
                    $ingresosHoy = \App\Models\Venta::where('user_id', auth()->id())
                                                   ->whereDate('fecha_venta', today())
                                                   ->sum('total');
                @endphp
                
                <div class="row">
                    <div class="col-6">
                        <div class="fs-2 fw-bold text-primary">{{ $ventasHoy }}</div>
                        <small class="text-muted">Ventas</small>
                    </div>
                    <div class="col-6">
                        <div class="fs-2 fw-bold text-success currency" data-amount="{{ $ingresosHoy }}">
                            $ {{ number_format($ingresosHoy, 2, ',', '.') }}
                        </div>
                        <small class="text-muted">Ingresos</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Rutas más vendidas -->
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-warning text-dark">
                <h6 class="card-title mb-0">
                    <i class="bi bi-star me-2"></i>Rutas Populares
                </h6>
            </div>
            <div class="card-body">
                @php
                    $rutasPopulares = \App\Models\Ruta::withCount(['horarios as ventas_count' => function($query) {
                        $query->join('ventas', 'horarios.id', '=', 'ventas.horario_id')
                              ->whereMonth('ventas.fecha_venta', now()->month);
                    }])->orderBy('ventas_count', 'desc')->take(3)->get();
                @endphp
                
                @forelse($rutasPopulares as $ruta)
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div>
                            <small class="fw-bold">{{ $ruta->codigo }}</small><br>
                            <small class="text-muted">{{ $ruta->origen }} → {{ $ruta->destino }}</small>
                        </div>
                        <span class="badge bg-primary">{{ $ruta->ventas_count }} ventas</span>
                    </div>
                @empty
                    <p class="text-muted small mb-0">No hay datos disponibles</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Rutas disponibles -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header">
                <h6 class="card-title mb-0">
                    <i class="bi bi-list me-2"></i>Rutas Disponibles
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Código</th>
                                <th>Ruta</th>
                                <th>Duración</th>
                                <th>Precio</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rutas as $ruta)
                                <tr class="ruta-row" style="cursor: pointer;" data-ruta-id="{{ $ruta->id }}">
                                    <td><strong class="text-primary">{{ $ruta->codigo }}</strong></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <span class="me-2">{{ $ruta->origen }}</span>
                                            <i class="bi bi-arrow-right text-muted me-2"></i>
                                            <span>{{ $ruta->destino }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        <i class="bi bi-clock me-1"></i>
                                        {{ $ruta->getDuracionFormateada() }}
                                    </td>
                                    <td>
                                        <span class="fw-bold text-success currency" data-amount="{{ $ruta->precio }}">
                                            $ {{ number_format($ruta->precio, 2, ',', '.') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">Activa</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        No hay rutas disponibles
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Helper global para formatear precios en pesos uruguayos
window.formatCurrency = function(amount) {
    return '$ ' + parseFloat(amount).toLocaleString('es-UY', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
};

$(document).ready(function() {
    // Mostrar información de la ruta cuando se selecciona
    $('#ruta_id').change(function() {
        const rutaSelect = $(this);
        const selectedOption = rutaSelect.find('option:selected');
        
        if (rutaSelect.val()) {
            const precio = selectedOption.data('precio');
            const duracion = selectedOption.data('duracion');
            const rutaTexto = selectedOption.text();
            
            $('#rutaDetalles').html(`
                <div><strong>Ruta:</strong> ${rutaTexto}</div>
                <div><strong>Duración:</strong> ${duracion}</div>
            `);
            
            // Formatear precio en pesos uruguayos
            $('#rutaPrecio').html(formatCurrency(precio));
            $('#rutaInfo').removeClass('d-none').addClass('fade-in');
        } else {
            $('#rutaInfo').addClass('d-none');
        }
    });
    
    // Seleccionar ruta al hacer clic en la tabla
    $('.ruta-row').click(function() {
        const rutaId = $(this).data('ruta-id');
        $('#ruta_id').val(rutaId).trigger('change');
        
        // Scroll suave al formulario
        $('html, body').animate({
            scrollTop: $('#rutaForm').offset().top - 100
        }, 500);
        
        // Resaltar fila seleccionada
        $('.ruta-row').removeClass('table-active');
        $(this).addClass('table-active');
    });
    
    // Validación del formulario
    $('#rutaForm').submit(function(e) {
        const rutaId = $('#ruta_id').val();
        const fecha = $('#fecha').val();
        
        if (!rutaId) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Ruta requerida',
                text: 'Por favor selecciona una ruta de viaje',
                confirmButtonText: 'Entendido'
            });
            return false;
        }
        
        if (!fecha) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Fecha requerida',
                text: 'Por favor selecciona la fecha de viaje',
                confirmButtonText: 'Entendido'
            });
            return false;
        }
        
        // Mostrar loading
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.html('<i class="bi bi-hourglass-split me-2"></i>Buscando horarios...');
        submitBtn.prop('disabled', true);
    });
    
    // Restricciones de fecha
    const today = new Date().toISOString().split('T')[0];
    const maxDate = new Date();
    maxDate.setMonth(maxDate.getMonth() + 3);
    const maxDateStr = maxDate.toISOString().split('T')[0];
    
    $('#fecha').attr('min', today);
    $('#fecha').attr('max', maxDateStr);
    
    // Formatear todos los elementos con clase 'currency' al cargar la página
    $('.currency').each(function() {
        const amount = $(this).data('amount');
        if (amount && !isNaN(amount)) {
            $(this).text(formatCurrency(amount));
        }
    });
});
</script>
@endpush