@extends('layouts.app')

@section('title', 'Seleccionar Horario - Transporte Veloz')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Nueva Venta</a></li>
    <li class="breadcrumb-item active">Seleccionar Horario</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">
                    <i class="bi bi-clock text-primary me-2"></i>
                    Seleccionar Horario
                </h1>
                <p class="text-muted">Elige el horario de salida para tu viaje</p>
            </div>
            <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Cambiar Ruta
            </a>
        </div>
    </div>
</div>

<!-- Información de la ruta seleccionada -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h5 class="card-title mb-1">
                            <i class="bi bi-map me-2"></i>{{ $ruta->codigo }}
                        </h5>
                        <div class="d-flex align-items-center mb-2">
                            <span class="fs-5 me-3">{{ $ruta->origen }}</span>
                            <i class="bi bi-arrow-right me-3"></i>
                            <span class="fs-5">{{ $ruta->destino }}</span>
                        </div>
                        <div class="row">
                            <div class="col-sm-6">
                                <small class="opacity-75">
                                    <i class="bi bi-calendar me-1"></i>
                                    {{ \Carbon\Carbon::parse($fecha)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                                </small>
                            </div>
                            <div class="col-sm-6">
                                <small class="opacity-75">
                                    <i class="bi bi-clock me-1"></i>
                                    Duración: {{ $ruta->getDuracionFormateada() }}
                                </small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="fs-2 fw-bold">S/ {{ number_format($ruta->precio, 2) }}</div>
                        <small class="opacity-75">por pasaje</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@if($horarios->count() > 0)
    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-2-circle me-2"></i>
                        Paso 2: Horarios Disponibles
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('ventas.asientos') }}" method="POST" id="horarioForm">
                        @csrf
                        <input type="hidden" name="horario_id" id="horario_id">
                        
                        <!-- Selector de cantidad de pasajes -->
                        <div class="mb-4">
                            <label for="cantidad" class="form-label">
                                <i class="bi bi-people me-1"></i>Cantidad de Pasajes
                            </label>
                            <select class="form-select @error('cantidad') is-invalid @enderror" 
                                    id="cantidad" 
                                    name="cantidad" 
                                    required>
                                <option value="">Selecciona la cantidad...</option>
                                @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ old('cantidad') == $i ? 'selected' : '' }}>
                                        {{ $i }} {{ $i == 1 ? 'pasaje' : 'pasajes' }}
                                    </option>
                                @endfor
                            </select>
                            @error('cantidad')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Lista de horarios -->
                        <div class="row g-3">
                            @foreach($horarios as $horario)
                                <div class="col-md-6">
                                    <div class="card horario-card border-2" 
                                         data-horario-id="{{ $horario->id }}"
                                         data-asientos="{{ $horario->asientos_disponibles }}"
                                         style="cursor: pointer; transition: all 0.3s ease;">
                                        <div class="card-body text-center">
                                            <div class="fs-3 fw-bold text-primary mb-2">
                                                {{ \Carbon\Carbon::parse($horario->hora_salida)->format('H:i') }}
                                            </div>
                                            
                                            <div class="mb-3">
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <small class="text-muted">Bus:</small>
                                                    <strong>{{ $horario->bus->placa }}</strong>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <small class="text-muted">Modelo:</small>
                                                    <span>{{ $horario->bus->modelo }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between align-items-center mb-1">
                                                    <small class="text-muted">Chofer:</small>
                                                    <span>{{ $horario->bus->chofer }}</span>
                                                </div>
                                            </div>
                                            
                                            <div class="asientos-info">
                                                @if($horario->asientos_disponibles > 10)
                                                    <span class="badge bg-success fs-6">
                                                        {{ $horario->asientos_disponibles }} asientos disponibles
                                                    </span>
                                                @elseif($horario->asientos_disponibles > 5)
                                                    <span class="badge bg-warning text-dark fs-6">
                                                        {{ $horario->asientos_disponibles }} asientos disponibles
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger fs-6">
                                                        ¡Solo {{ $horario->asientos_disponibles }} asientos!
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <div class="selected-indicator d-none mt-3">
                                                <i class="bi bi-check-circle-fill text-success fs-4"></i>
                                                <div class="text-success fw-bold">Seleccionado</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="d-grid mt-4">
                            <button type="submit" class="btn btn-success btn-lg" id="continuar-btn" disabled>
                                <i class="bi bi-arrow-right me-2"></i>
                                Continuar con Selección de Asientos
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <!-- Resumen de la compra -->
            <div class="card shadow-sm sticky-top" style="top: 100px;">
                <div class="card-header bg-info text-white">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-receipt me-2"></i>Resumen de Compra
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Ruta:</span>
                            <strong>{{ $ruta->codigo }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Origen:</span>
                            <span>{{ $ruta->origen }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Destino:</span>
                            <span>{{ $ruta->destino }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Fecha:</span>
                            <span>{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Horario:</span>
                            <span id="horario-seleccionado" class="text-muted">No seleccionado</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Cantidad:</span>
                            <span id="cantidad-seleccionada" class="text-muted">No seleccionada</span>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between mb-3">
                        <span>Precio unitario:</span>
                        <strong>S/ {{ number_format($ruta->precio, 2) }}</strong>
                    </div>
                    
                    <div class="d-flex justify-content-between fs-5 fw-bold text-success">
                        <span>Total:</span>
                        <span id="total-compra">S/ 0.00</span>
                    </div>
                </div>
            </div>
            
            <!-- Información adicional -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-info-circle me-2"></i>Información Importante
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Los asientos se asignan en el siguiente paso
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Llegada 30 minutos antes de la salida
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Documento de identidad obligatorio
                        </li>
                        <li class="mb-0">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            Ticket válido solo para la fecha y horario seleccionado
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@else
    <!-- No hay horarios disponibles -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-clock-history text-muted fs-1 mb-3"></i>
                    <h4 class="text-muted">No hay horarios disponibles</h4>
                    <p class="text-muted mb-4">
                        No se encontraron horarios disponibles para la ruta 
                        <strong>{{ $ruta->codigo }}</strong> 
                        el día <strong>{{ \Carbon\Carbon::parse($fecha)->format('d/m/Y') }}</strong>
                    </p>
                    <div class="d-flex justify-content-center gap-3">
                        <a href="{{ route('ventas.index') }}" class="btn btn-primary">
                            <i class="bi bi-arrow-left me-2"></i>Seleccionar Otra Ruta
                        </a>
                        <button type="button" class="btn btn-outline-primary" onclick="cambiarFecha()">
                            <i class="bi bi-calendar me-2"></i>Cambiar Fecha
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const precioUnitario = {{ $ruta->precio }};
    
    // Manejar selección de horario
    $('.horario-card').click(function() {
        const horarioId = $(this).data('horario-id');
        const asientosDisponibles = $(this).data('asientos');
        const hora = $(this).find('.fs-3').text();
        
        // Remover selección anterior
        $('.horario-card').removeClass('border-success bg-light');
        $('.selected-indicator').addClass('d-none');
        
        // Seleccionar nuevo horario
        $(this).addClass('border-success bg-light');
        $(this).find('.selected-indicator').removeClass('d-none');
        
        // Actualizar form
        $('#horario_id').val(horarioId);
        $('#horario-seleccionado').text(hora).removeClass('text-muted');
        
        // Verificar si se puede continuar
        verificarFormularioCompleto();
        
        // Actualizar límite de cantidad según asientos disponibles
        actualizarCantidadMaxima(asientosDisponibles);
    });
    
    // Manejar cambio de cantidad
    $('#cantidad').change(function() {
        const cantidad = $(this).val();
        
        if (cantidad) {
            $('#cantidad-seleccionada').text(cantidad + (cantidad == 1 ? ' pasaje' : ' pasajes')).removeClass('text-muted');
            
            // Calcular total
            const total = cantidad * precioUnitario;
            $('#total-compra').text('S/ ' + total.toFixed(2));
        } else {
            $('#cantidad-seleccionada').text('No seleccionada').addClass('text-muted');
            $('#total-compra').text('S/ 0.00');
        }
        
        verificarFormularioCompleto();
    });
    
    function verificarFormularioCompleto() {
        const horarioSeleccionado = $('#horario_id').val();
        const cantidadSeleccionada = $('#cantidad').val();
        
        if (horarioSeleccionado && cantidadSeleccionada) {
            $('#continuar-btn').prop('disabled', false);
        } else {
            $('#continuar-btn').prop('disabled', true);
        }
    }
    
    function actualizarCantidadMaxima(asientosDisponibles) {
        const cantidadSelect = $('#cantidad');
        const cantidadActual = cantidadSelect.val();
        
        // Limpiar opciones
        cantidadSelect.find('option:not(:first)').remove();
        
        // Agregar opciones hasta el máximo disponible o 10
        const maxCantidad = Math.min(asientosDisponibles, 10);
        
        for (let i = 1; i <= maxCantidad; i++) {
            const selected = (cantidadActual == i) ? 'selected' : '';
            cantidadSelect.append(`<option value="${i}" ${selected}>${i} ${i == 1 ? 'pasaje' : 'pasajes'}</option>`);
        }
        
        // Si la cantidad actual excede el máximo, resetear
        if (cantidadActual > maxCantidad) {
            cantidadSelect.val('');
            $('#cantidad-seleccionada').text('No seleccionada').addClass('text-muted');
            $('#total-compra').text('S/ 0.00');
        }
    }
    
    // Validación del formulario
    $('#horarioForm').submit(function(e) {
        const horarioId = $('#horario_id').val();
        const cantidad = $('#cantidad').val();
        
        if (!horarioId) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Horario requerido',
                text: 'Por favor selecciona un horario de salida',
                confirmButtonText: 'Entendido'
            });
            return false;
        }
        
        if (!cantidad) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Cantidad requerida',
                text: 'Por favor selecciona la cantidad de pasajes',
                confirmButtonText: 'Entendido'
            });
            return false;
        }
        
        // Mostrar loading
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.html('<i class="bi bi-hourglass-split me-2"></i>Cargando mapa de asientos...');
        submitBtn.prop('disabled', true);
    });
});

function cambiarFecha() {
    Swal.fire({
        title: 'Seleccionar nueva fecha',
        html: '<input type="date" id="nueva-fecha" class="form-control" min="' + new Date().toISOString().split('T')[0] + '">',
        showCancelButton: true,
        confirmButtonText: 'Buscar',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const nuevaFecha = document.getElementById('nueva-fecha').value;
            if (!nuevaFecha) {
                Swal.showValidationMessage('Por favor selecciona una fecha');
                return false;
            }
            return nuevaFecha;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Crear form y enviar
            const form = $('<form>', {
                method: 'POST',
                action: '{{ route("ventas.horarios") }}'
            });
            
            form.append('@csrf');
            form.append($('<input>', { type: 'hidden', name: 'ruta_id', value: '{{ $ruta->id }}' }));
            form.append($('<input>', { type: 'hidden', name: 'fecha', value: result.value }));
            
            $('body').append(form);
            form.submit();
        }
    });
}
</script>
@endpush