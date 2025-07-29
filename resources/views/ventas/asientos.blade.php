@extends('layouts.app')

@section('title', 'Seleccionar Asientos - Transporte Veloz')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Nueva Venta</a></li>
    <li class="breadcrumb-item"><a href="javascript:history.back()">Horarios</a></li>
    <li class="breadcrumb-item active">Seleccionar Asientos</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">
                    <i class="bi bi-car-front text-primary me-2"></i>
                    Seleccionar Asientos
                </h1>
                <p class="text-muted">Selecciona {{ $cantidad }} {{ $cantidad == 1 ? 'asiento' : 'asientos' }} en el mapa del bus</p>
            </div>
            <button type="button" class="btn btn-outline-secondary" onclick="history.back()">
                <i class="bi bi-arrow-left me-2"></i>Cambiar Horario
            </button>
        </div>
    </div>
</div>

<!-- Información del viaje -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white shadow-sm">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="fs-4 fw-bold">{{ $horario->ruta->codigo }}</div>
                            <small class="opacity-75">
                                {{ $horario->ruta->origen }} → {{ $horario->ruta->destino }}
                            </small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="fs-4 fw-bold">
                                {{ \Carbon\Carbon::parse($horario->hora_salida)->format('H:i') }}
                            </div>
                            <small class="opacity-75">
                                {{ \Carbon\Carbon::parse($horario->fecha)->format('d/m/Y') }}
                            </small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="fs-4 fw-bold">{{ $horario->bus->placa }}</div>
                            <small class="opacity-75">{{ $horario->bus->modelo }}</small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <div class="fs-4 fw-bold">S/ {{ number_format($horario->ruta->precio * $cantidad, 2) }}</div>
                            <small class="opacity-75">{{ $cantidad }} {{ $cantidad == 1 ? 'pasaje' : 'pasajes' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="bi bi-3-circle me-2"></i>
                    Paso 3: Mapa de Asientos del Bus
                </h5>
            </div>
            <div class="card-body">
                <!-- Leyenda -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="d-flex justify-content-center flex-wrap gap-4">
                            <div class="d-flex align-items-center">
                                <div class="seat available me-2">1</div>
                                <span>Disponible</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="seat occupied me-2">X</div>
                                <span>Ocupado</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <div class="seat selected me-2">S</div>
                                <span>Seleccionado</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mapa del bus -->
                <div class="bus-layout">
                    <div class="text-center mb-3">
                        <div class="driver-seat mx-auto">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <small class="text-muted">Conductor</small>
                    </div>
                    
                    <div class="asientos-container">
                        @foreach($asientos as $fila => $asientosFila)
                            <div class="d-flex justify-content-center align-items-center mb-2">
                                <!-- Fila de la izquierda (asientos 1 y 2) -->
                                <div class="d-flex me-4">
                                    @for($col = 1; $col <= 2; $col++)
                                        @if(isset($asientosFila[$col]))
                                            @php $asiento = $asientosFila[$col]; @endphp
                                            <div class="seat {{ $asiento['ocupado'] ? 'occupied' : 'available' }}" 
                                                 data-asiento="{{ $asiento['numero'] }}"
                                                 data-ocupado="{{ $asiento['ocupado'] ? 'true' : 'false' }}">
                                                {{ $asiento['ocupado'] ? 'X' : $asiento['numero'] }}
                                            </div>
                                        @else
                                            <div style="width: 44px;"></div>
                                        @endif
                                    @endfor
                                </div>
                                
                                <!-- Pasillo -->
                                <div class="text-center text-muted" style="width: 40px;">
                                    <small>{{ $fila }}</small>
                                </div>
                                
                                <!-- Fila de la derecha (asientos 3 y 4) -->
                                <div class="d-flex ms-4">
                                    @for($col = 3; $col <= 4; $col++)
                                        @if(isset($asientosFila[$col]))
                                            @php $asiento = $asientosFila[$col]; @endphp
                                            <div class="seat {{ $asiento['ocupado'] ? 'occupied' : 'available' }}" 
                                                 data-asiento="{{ $asiento['numero'] }}"
                                                 data-ocupado="{{ $asiento['ocupado'] ? 'true' : 'false' }}">
                                                {{ $asiento['ocupado'] ? 'X' : $asiento['numero'] }}
                                            </div>
                                        @else
                                            <div style="width: 44px;"></div>
                                        @endif
                                    @endfor
                                </div>
                            </div>
                        @endforeach
                    </div>
                    
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            <i class="bi bi-door-open me-1"></i>Puerta de Entrada
                        </small>
                    </div>
                </div>
                
                <!-- Asientos seleccionados -->
                <div class="mt-4">
                    <h6>Asientos Seleccionados:</h6>
                    <div id="asientos-seleccionados-display" class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Selecciona {{ $cantidad }} {{ $cantidad == 1 ? 'asiento' : 'asientos' }} para continuar
                    </div>
                </div>
                
                <div class="d-grid">
                    <button type="button" class="btn btn-success btn-lg" id="continuar-btn" disabled>
                        <i class="bi bi-arrow-right me-2"></i>
                        Continuar con Datos de Pasajeros
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Formulario de datos de pasajeros -->
        <div class="card shadow-sm sticky-top" style="top: 100px;">
            <div class="card-header bg-info text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-4-circle me-2"></i>
                    Paso 4: Datos de Pasajeros
                </h6>
            </div>
            <div class="card-body" id="formulario-pasajeros">
                <div class="text-center text-muted py-4">
                    <i class="bi bi-person-plus fs-2 mb-2"></i>
                    <p class="mb-0">Primero selecciona los asientos</p>
                </div>
            </div>
        </div>
        
        <!-- Resumen -->
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-secondary text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-receipt me-2"></i>Resumen Final
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <span>Ruta:</span>
                    <strong>{{ $horario->ruta->codigo }}</strong>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Fecha:</span>
                    <span>{{ \Carbon\Carbon::parse($horario->fecha)->format('d/m/Y') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Horario:</span>
                    <span>{{ \Carbon\Carbon::parse($horario->hora_salida)->format('H:i') }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Bus:</span>
                    <span>{{ $horario->bus->placa }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Asientos:</span>
                    <span id="asientos-resumen">-</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fs-5 fw-bold text-success">
                    <span>Total:</span>
                    <span>S/ {{ number_format($horario->ruta->precio * $cantidad, 2) }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de confirmación -->
<div class="modal fade" id="confirmarVentaModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle me-2"></i>Confirmar Venta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>Importante:</strong> Verifica todos los datos antes de confirmar. Una vez procesada, la venta no podrá modificarse.
                </div>
                
                <div id="resumen-venta"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-success" id="confirmar-venta-final">
                    <i class="bi bi-check2 me-2"></i>Confirmar y Procesar Venta
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Formulario oculto para enviar datos -->
<form id="ventaForm" action="{{ route('ventas.procesar') }}" method="POST" style="display: none;">
    @csrf
    <input type="hidden" name="horario_id" value="{{ $horario->id }}">
    <input type="hidden" name="asientos" id="asientos-input">
    <input type="hidden" name="pasajeros" id="pasajeros-input">
</form>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    const cantidadRequerida = {{ $cantidad }};
    let asientosSeleccionados = [];
    let datosPasajeros = [];
    
    // Manejar clic en asientos
    $('.seat.available').click(function() {
        const numeroAsiento = $(this).data('asiento');
        
        if ($(this).hasClass('selected')) {
            // Deseleccionar asiento
            $(this).removeClass('selected');
            $(this).text(numeroAsiento);
            asientosSeleccionados = asientosSeleccionados.filter(a => a != numeroAsiento);
        } else {
            // Seleccionar asiento si no se ha alcanzado el límite
            if (asientosSeleccionados.length < cantidadRequerida) {
                $(this).addClass('selected');
                $(this).text('S');
                asientosSeleccionados.push(numeroAsiento);
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Límite alcanzado',
                    text: `Solo puedes seleccionar ${cantidadRequerida} ${cantidadRequerida == 1 ? 'asiento' : 'asientos'}`,
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        }
        
        actualizarInterfaz();
    });
    
    function actualizarInterfaz() {
        // Actualizar display de asientos seleccionados
        if (asientosSeleccionados.length > 0) {
            const asientosTexto = asientosSeleccionados.sort((a, b) => a - b).join(', ');
            $('#asientos-seleccionados-display').html(`
                <i class="bi bi-check-circle text-success me-2"></i>
                Asientos seleccionados: <strong>${asientosTexto}</strong>
            `).removeClass('alert-info').addClass('alert-success');
            
            $('#asientos-resumen').text(asientosTexto);
        } else {
            $('#asientos-seleccionados-display').html(`
                <i class="bi bi-info-circle me-2"></i>
                Selecciona ${cantidadRequerida} ${cantidadRequerida == 1 ? 'asiento' : 'asientos'} para continuar
            `).removeClass('alert-success').addClass('alert-info');
            
            $('#asientos-resumen').text('-');
        }
        
        // Mostrar formulario de pasajeros si se completó la selección
        if (asientosSeleccionados.length === cantidadRequerida) {
            mostrarFormularioPasajeros();
            $('#continuar-btn').prop('disabled', false);
        } else {
            ocultarFormularioPasajeros();
            $('#continuar-btn').prop('disabled', true);
        }
    }
    
    function mostrarFormularioPasajeros() {
        let formularioHTML = '<div class="mb-3">';
        formularioHTML += `<p class="mb-3"><i class="bi bi-info-circle text-info me-2"></i>Ingresa los datos para cada pasajero:</p>`;
        
        asientosSeleccionados.sort((a, b) => a - b).forEach((asiento, index) => {
            formularioHTML += `
                <div class="card mb-3">
                    <div class="card-header bg-light">
                        <small class="fw-bold">
                            <i class="bi bi-person me-1"></i>Pasajero ${index + 1} - Asiento ${asiento}
                        </small>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <label class="form-label small">Nombre Completo *</label>
                            <input type="text" class="form-control form-control-sm pasajero-nombre" 
                                   data-index="${index}" required placeholder="Ej: Juan Pérez García">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">DNI/Documento *</label>
                            <input type="text" class="form-control form-control-sm pasajero-dni" 
                                   data-index="${index}" required placeholder="Ej: 12345678">
                        </div>
                        <div class="mb-2">
                            <label class="form-label small">Teléfono</label>
                            <input type="text" class="form-control form-control-sm pasajero-telefono" 
                                   data-index="${index}" placeholder="Ej: 987654321">
                        </div>
                        <div class="mb-0">
                            <label class="form-label small">Email</label>
                            <input type="email" class="form-control form-control-sm pasajero-email" 
                                   data-index="${index}" placeholder="Ej: juan@email.com">
                        </div>
                    </div>
                </div>
            `;
        });
        
        formularioHTML += '</div>';
        
        $('#formulario-pasajeros').html(formularioHTML);
        
        // Agregar eventos para validación en tiempo real
        $('.pasajero-nombre, .pasajero-dni').on('input', validarFormularioPasajeros);
    }
    
    function ocultarFormularioPasajeros() {
        $('#formulario-pasajeros').html(`
            <div class="text-center text-muted py-4">
                <i class="bi bi-person-plus fs-2 mb-2"></i>
                <p class="mb-0">Primero selecciona los asientos</p>
            </div>
        `);
    }
    
    function validarFormularioPasajeros() {
        let formularioValido = true;
        datosPasajeros = [];
        
        asientosSeleccionados.forEach((asiento, index) => {
            const nombre = $(`.pasajero-nombre[data-index="${index}"]`).val().trim();
            const dni = $(`.pasajero-dni[data-index="${index}"]`).val().trim();
            const telefono = $(`.pasajero-telefono[data-index="${index}"]`).val().trim();
            const email = $(`.pasajero-email[data-index="${index}"]`).val().trim();
            
            if (!nombre || !dni) {
                formularioValido = false;
            }
            
            datosPasajeros.push({
                asiento: asiento,
                nombre: nombre,
                dni: dni,
                telefono: telefono,
                email: email
            });
        });
        
        $('#continuar-btn').prop('disabled', !formularioValido);
        return formularioValido;
    }
    
    // Manejar clic en continuar
    $('#continuar-btn').click(function() {
        if (!validarFormularioPasajeros()) {
            Swal.fire({
                icon: 'warning',
                title: 'Datos incompletos',
                text: 'Por favor completa el nombre y DNI de todos los pasajeros',
                confirmButtonText: 'Entendido'
            });
            return;
        }
        
        // Mostrar modal de confirmación
        mostrarModalConfirmacion();
    });
    
    function mostrarModalConfirmacion() {
        let resumenHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6>Información del Viaje</h6>
                    <ul class="list-unstyled">
                        <li><strong>Ruta:</strong> {{ $horario->ruta->codigo }}</li>
                        <li><strong>Origen:</strong> {{ $horario->ruta->origen }}</li>
                        <li><strong>Destino:</strong> {{ $horario->ruta->destino }}</li>
                        <li><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($horario->fecha)->format('d/m/Y') }}</li>
                        <li><strong>Horario:</strong> {{ \Carbon\Carbon::parse($horario->hora_salida)->format('H:i') }}</li>
                        <li><strong>Bus:</strong> {{ $horario->bus->placa }} ({{ $horario->bus->modelo }})</li>
                    </ul>
                </div>
                <div class="col-md-6">
                    <h6>Pasajeros y Asientos</h6>
                    <ul class="list-unstyled">
        `;
        
        datosPasajeros.forEach((pasajero, index) => {
            resumenHTML += `
                <li class="mb-2">
                    <strong>Asiento ${pasajero.asiento}:</strong><br>
                    <small>${pasajero.nombre} (${pasajero.dni})</small>
                </li>
            `;
        });
        
        resumenHTML += `
                    </ul>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <h4 class="text-success">Total: S/ {{ number_format($horario->ruta->precio * $cantidad, 2) }}</h4>
            </div>
        `;
        
        $('#resumen-venta').html(resumenHTML);
        $('#confirmarVentaModal').modal('show');
    }
    
    // Confirmar venta final
    $('#confirmar-venta-final').click(function() {
        // Preparar datos para envío
        $('#asientos-input').val(JSON.stringify(asientosSeleccionados));
        $('#pasajeros-input').val(JSON.stringify(datosPasajeros));
        
        // Mostrar loading
        $(this).html('<i class="bi bi-hourglass-split me-2"></i>Procesando...').prop('disabled', true);
        
        // Enviar formulario
        $('#ventaForm').submit();
    });
});
</script>
@endpush