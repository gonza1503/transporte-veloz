@extends('layouts.app')

@section('title', 'Crear Nuevo Horario - Transporte Veloz')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.horarios.index') }}">Horarios</a></li>
    <li class="breadcrumb-item active">Crear Nuevo Horario</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">
                    <i class="bi bi-plus-circle text-primary me-2"></i>
                    Crear Nuevo Horario
                </h1>
                <p class="text-muted">Programa un nuevo horario de salida para una ruta específica</p>
            </div>
            <a href="{{ route('admin.horarios.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver a Horarios
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-clock me-2"></i>Información del Horario
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.horarios.store') }}" method="POST" id="horarioForm">
                    @csrf
                    
                    <div class="row">
                        <!-- Selección de Ruta -->
                        <div class="col-md-6 mb-3">
                            <label for="ruta_id" class="form-label">
                                <i class="bi bi-map text-primary me-1"></i>Ruta <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('ruta_id') is-invalid @enderror" 
                                    id="ruta_id" 
                                    name="ruta_id" 
                                    required>
                                <option value="">Selecciona una ruta...</option>
                                @foreach($rutas as $ruta)
                                    <option value="{{ $ruta->id }}" 
                                            data-precio="{{ $ruta->precio }}"
                                            data-duracion="{{ $ruta->duracion_minutos }}"
                                            {{ old('ruta_id') == $ruta->id ? 'selected' : '' }}>
                                        {{ $ruta->codigo }} - {{ $ruta->origen }} → {{ $ruta->destino }} 
                                        ($U {{ number_format($ruta->precio, 0) }})
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Solo se muestran las rutas activas
                            </div>
                            @error('ruta_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Selección de Bus -->
                        <div class="col-md-6 mb-3">
                            <label for="bus_id" class="form-label">
                                <i class="bi bi-bus-front text-success me-1"></i>Bus <span class="text-danger">*</span>
                            </label>
                            <select class="form-select @error('bus_id') is-invalid @enderror" 
                                    id="bus_id" 
                                    name="bus_id" 
                                    required>
                                <option value="">Selecciona un bus...</option>
                                @foreach($buses as $bus)
                                    <option value="{{ $bus->id }}" 
                                            data-capacidad="{{ $bus->capacidad }}"
                                            data-chofer="{{ $bus->chofer }}"
                                            {{ old('bus_id') == $bus->id ? 'selected' : '' }}>
                                        {{ $bus->placa }} - {{ $bus->modelo }} 
                                        ({{ $bus->capacidad }} asientos)
                                    </option>
                                @endforeach
                            </select>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Solo se muestran los buses activos
                            </div>
                            @error('bus_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Fecha del Viaje -->
                        <div class="col-md-6 mb-3">
                            <label for="fecha" class="form-label">
                                <i class="bi bi-calendar text-info me-1"></i>Fecha del Viaje <span class="text-danger">*</span>
                            </label>
                            <input type="date" 
                                   class="form-control @error('fecha') is-invalid @enderror" 
                                   id="fecha" 
                                   name="fecha" 
                                   value="{{ old('fecha', now()->format('Y-m-d')) }}"
                                   min="{{ now()->format('Y-m-d') }}"
                                   max="{{ now()->addMonths(6)->format('Y-m-d') }}"
                                   required>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Fecha en que se realizará el viaje
                            </div>
                            @error('fecha')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Hora de Salida -->
                        <div class="col-md-6 mb-3">
                            <label for="hora_salida" class="form-label">
                                <i class="bi bi-clock text-warning me-1"></i>Hora de Salida <span class="text-danger">*</span>
                            </label>
                            <input type="time" 
                                   class="form-control @error('hora_salida') is-invalid @enderror" 
                                   id="hora_salida" 
                                   name="hora_salida" 
                                   value="{{ old('hora_salida') }}"
                                   required>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Hora programada de salida
                            </div>
                            @error('hora_salida')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Estado del Horario -->
                    <div class="mb-4">
                        <label for="activo" class="form-label">
                            <i class="bi bi-toggle-on text-success me-1"></i>Estado del Horario
                        </label>
                        <select class="form-select @error('activo') is-invalid @enderror" id="activo" name="activo">
                            <option value="1" {{ old('activo', '1') == '1' ? 'selected' : '' }}>Activo (Disponible para venta)</option>
                            <option value="0" {{ old('activo') == '0' ? 'selected' : '' }}>Inactivo (No disponible)</option>
                        </select>
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Solo los horarios activos aparecerán en el sistema de ventas
                        </div>
                        @error('activo')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Información calculada automáticamente -->
                    <div id="info-calculada" class="alert alert-info d-none">
                        <h6><i class="bi bi-info-circle me-2"></i>Información del Viaje</h6>
                        <div id="detalles-viaje"></div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.horarios.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-check-circle me-2"></i>Crear Horario
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Panel lateral con información -->
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-lightbulb me-2"></i>Guía para Crear Horarios
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6><i class="bi bi-1-circle text-primary me-2"></i>Selección de Ruta</h6>
                    <p class="small text-muted">
                        Elige la ruta para la cual quieres programar el horario. 
                        Solo aparecen las rutas activas.
                    </p>
                </div>
                
                <div class="mb-3">
                    <h6><i class="bi bi-2-circle text-success me-2"></i>Asignación de Bus</h6>
                    <p class="small text-muted">
                        Selecciona un bus disponible. El sistema verificará automáticamente 
                        la disponibilidad del bus en la fecha y hora seleccionada.
                    </p>
                </div>
                
                <div class="mb-3">
                    <h6><i class="bi bi-3-circle text-info me-2"></i>Fecha y Hora</h6>
                    <p class="small text-muted">
                        La fecha debe ser a partir de hoy y la hora debe ser realista 
                        para el servicio de transporte.
                    </p>
                </div>
                
                <div class="alert alert-warning p-2">
                    <small>
                        <strong><i class="bi bi-exclamation-triangle me-1"></i>Importante:</strong><br>
                        Verifica que el bus no tenga otro viaje programado 
                        en el mismo horario antes de crear el horario.
                    </small>
                </div>
            </div>
        </div>
        
        <!-- Horarios del día seleccionado -->
        <div class="card shadow-sm mt-4" id="horarios-del-dia" style="display: none;">
            <div class="card-header bg-warning text-dark">
                <h6 class="card-title mb-0">
                    <i class="bi bi-calendar-check me-2"></i>Horarios Existentes
                </h6>
            </div>
            <div class="card-body" id="lista-horarios">
                <!-- Se llena dinámicamente con JavaScript -->
            </div>
        </div>

        <!-- Rutas más populares -->
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-secondary text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-star me-2"></i>Rutas Más Solicitadas
                </h6>
            </div>
            <div class="card-body">
                @php
                    $rutasPopulares = $rutas->take(3);
                @endphp
                
                @foreach($rutasPopulares as $rutaPopular)
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                        <div>
                            <small class="fw-bold text-primary">{{ $rutaPopular->codigo }}</small><br>
                            <small class="text-muted">{{ $rutaPopular->origen }} → {{ $rutaPopular->destino }}</small>
                        </div>
                        <div class="text-end">
                            <small class="fw-bold text-success">$U {{ number_format($rutaPopular->precio, 0) }}</small><br>
                            <small class="text-muted">{{ floor($rutaPopular->duracion_minutos / 60) }}h {{ $rutaPopular->duracion_minutos % 60 }}m</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    
    // Actualizar información cuando se selecciona ruta o bus
    $('#ruta_id, #bus_id, #fecha, #hora_salida').on('change', function() {
        actualizarInformacion();
        verificarDisponibilidad();
    });
    
    function actualizarInformacion() {
        const rutaSelect = $('#ruta_id');
        const busSelect = $('#bus_id');
        const selectedRuta = rutaSelect.find('option:selected');
        const selectedBus = busSelect.find('option:selected');
        
        if (rutaSelect.val() && busSelect.val()) {
            const precio = selectedRuta.data('precio');
            const duracion = selectedRuta.data('duracion');
            const capacidad = selectedBus.data('capacidad');
            const chofer = selectedBus.data('chofer');
            
            const horas = Math.floor(duracion / 60);
            const minutos = duracion % 60;
            const duracionTexto = horas > 0 ? `${horas}h ${minutos}m` : `${minutos}m`;
            
            $('#detalles-viaje').html(`
                <div class="row">
                    <div class="col-6">
                        <strong>Precio por pasaje:</strong><br>
                        <span class="text-success fs-5">$U ${Number(precio).toLocaleString()}</span>
                    </div>
                    <div class="col-6">
                        <strong>Duración estimada:</strong><br>
                        <span class="text-info fs-6">${duracionTexto}</span>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-6">
                        <strong>Capacidad del bus:</strong><br>
                        <span class="text-primary">${capacidad} asientos</span>
                    </div>
                    <div class="col-6">
                        <strong>Chofer asignado:</strong><br>
                        <span class="text-secondary">${chofer}</span>
                    </div>
                </div>
            `);
            
            $('#info-calculada').removeClass('d-none');
        } else {
            $('#info-calculada').addClass('d-none');
        }
    }
    
    function verificarDisponibilidad() {
        const busId = $('#bus_id').val();
        const fecha = $('#fecha').val();
        const hora = $('#hora_salida').val();
        
        if (busId && fecha && hora) {
            // Aquí podrías hacer una consulta AJAX para verificar disponibilidad
            console.log('Verificando disponibilidad...', { busId, fecha, hora });
        }
    }
    
    // Cargar horarios existentes cuando cambie la fecha
    $('#fecha').on('change', function() {
        const fecha = $(this).val();
        if (fecha) {
            cargarHorariosDelDia(fecha);
        }
    });
    
    function cargarHorariosDelDia(fecha) {
        // Simulación - en producción harías una consulta AJAX
        $.get(`/api/horarios-fecha/${fecha}`)
            .done(function(horarios) {
                mostrarHorariosDelDia(horarios, fecha);
            })
            .fail(function() {
                // Si falla, ocultar el panel
                $('#horarios-del-dia').hide();
            });
    }
    
    function mostrarHorariosDelDia(horarios, fecha) {
        if (horarios.length > 0) {
            let html = `<h6>Horarios para ${fecha}:</h6>`;
            horarios.forEach(function(horario) {
                html += `
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                        <div>
                            <small class="fw-bold">${horario.hora_salida}</small><br>
                            <small class="text-muted">${horario.ruta.codigo} - ${horario.bus.placa}</small>
                        </div>
                        <small class="badge bg-${horario.activo ? 'success' : 'secondary'}">
                            ${horario.activo ? 'Activo' : 'Inactivo'}
                        </small>
                    </div>
                `;
            });
            $('#lista-horarios').html(html);
            $('#horarios-del-dia').show();
        } else {
            $('#horarios-del-dia').hide();
        }
    }
    
    // Validación del formulario
    $('#horarioForm').on('submit', function(e) {
        let valid = true;
        let errores = [];
        
        // Validar que todos los campos estén completos
        if (!$('#ruta_id').val()) {
            errores.push('Debe seleccionar una ruta');
            valid = false;
        }
        
        if (!$('#bus_id').val()) {
            errores.push('Debe seleccionar un bus');
            valid = false;
        }
        
        if (!$('#fecha').val()) {
            errores.push('Debe seleccionar una fecha');
            valid = false;
        }
        
        if (!$('#hora_salida').val()) {
            errores.push('Debe seleccionar una hora de salida');
            valid = false;
        }
        
        // Validar que la fecha no sea en el pasado
        const fechaSeleccionada = new Date($('#fecha').val());
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        
        if (fechaSeleccionada < hoy) {
            errores.push('La fecha no puede ser en el pasado');
            valid = false;
        }
        
        if (!valid) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Errores en el formulario',
                html: errores.join('<br>'),
                confirmButtonText: 'Corregir'
            });
            return false;
        }
        
        // Mostrar loading
        const submitBtn = $('#submitBtn');
        submitBtn.html('<i class="bi bi-hourglass-split me-2"></i>Creando Horario...');
        submitBtn.prop('disabled', true);
        
        return true;
    });
    
    // Inicializar información si hay valores preseleccionados
    if ($('#ruta_id').val() && $('#bus_id').val()) {
        actualizarInformacion();
    }
    
    // Cargar horarios del día actual si hay fecha
    if ($('#fecha').val()) {
        cargarHorariosDelDia($('#fecha').val());
    }
});
</script>
@endpush

@push('styles')
<style>
.form-label {
    font-weight: 600;
}

.form-control:focus,
.form-select:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

.card {
    border: none;
    border-radius: 0.75rem;
}

#info-calculada {
    background-color: #e3f2fd;
    border: 1px solid #2196f3;
}
</style>
@endpush