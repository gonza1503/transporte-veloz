@extends('layouts.app')

@section('title', 'Crear Nueva Ruta - Transporte Veloz')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item"><a href="{{ route('admin.rutas.index') }}">Rutas</a></li>
    <li class="breadcrumb-item active">Crear Nueva Ruta</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">
                    <i class="bi bi-plus-circle text-primary me-2"></i>
                    Crear Nueva Ruta
                </h1>
                <p class="text-muted">Registra una nueva ruta de transporte en el sistema</p>
            </div>
            <a href="{{ route('admin.rutas.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-2"></i>Volver a Rutas
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-map me-2"></i>Información de la Ruta
                </h5>
            </div>
            <div class="card-body">
                <form action="{{ route('admin.rutas.store') }}" method="POST" id="rutaForm">
                    @csrf
                    
                    <div class="row">
                        <!-- Código de Ruta -->
                        <div class="col-md-6 mb-3">
                            <label for="codigo" class="form-label">
                                <i class="bi bi-hash text-primary me-1"></i>Código de Ruta <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('codigo') is-invalid @enderror" 
                                   id="codigo" 
                                   name="codigo" 
                                   value="{{ old('codigo') }}"
                                   placeholder="Ej: MVD-PDP-001"
                                   required>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Código único para identificar la ruta (ej: MVD-PDP-001)
                            </div>
                            @error('codigo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Estado -->
                        <div class="col-md-6 mb-3">
                            <label for="activa" class="form-label">
                                <i class="bi bi-toggle-on text-success me-1"></i>Estado de la Ruta
                            </label>
                            <select class="form-select @error('activa') is-invalid @enderror" id="activa" name="activa">
                                <option value="1" {{ old('activa', '1') == '1' ? 'selected' : '' }}>Activa</option>
                                <option value="0" {{ old('activa') == '0' ? 'selected' : '' }}>Inactiva</option>
                            </select>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Solo las rutas activas estarán disponibles para venta
                            </div>
                            @error('activa')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Ciudad de Origen -->
                        <div class="col-md-6 mb-3">
                            <label for="origen" class="form-label">
                                <i class="bi bi-geo-alt text-success me-1"></i>Ciudad de Origen <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('origen') is-invalid @enderror" 
                                   id="origen" 
                                   name="origen" 
                                   value="{{ old('origen') }}"
                                   placeholder="Ej: Montevideo"
                                   required>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Ciudad donde inicia el viaje
                            </div>
                            @error('origen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Ciudad de Destino -->
                        <div class="col-md-6 mb-3">
                            <label for="destino" class="form-label">
                                <i class="bi bi-geo-alt-fill text-danger me-1"></i>Ciudad de Destino <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control @error('destino') is-invalid @enderror" 
                                   id="destino" 
                                   name="destino" 
                                   value="{{ old('destino') }}"
                                   placeholder="Ej: Punta del Este"
                                   required>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Ciudad donde termina el viaje
                            </div>
                            @error('destino')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <!-- Precio -->
                        <div class="col-md-6 mb-3">
                            <label for="precio" class="form-label">
                                <i class="bi bi-currency-dollar text-warning me-1"></i>Precio por Pasaje <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">$U</span>
                                <input type="number" 
                                       class="form-control @error('precio') is-invalid @enderror" 
                                       id="precio" 
                                       name="precio" 
                                       value="{{ old('precio') }}"
                                       step="0.01" 
                                       min="0"
                                       placeholder="850.00"
                                       required>
                                <span class="input-group-text">UYU</span>
                            </div>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Precio en pesos uruguayos (UYU)
                            </div>
                            @error('precio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <!-- Duración -->
                        <div class="col-md-6 mb-3">
                            <label for="duracion_minutos" class="form-label">
                                <i class="bi bi-clock text-info me-1"></i>Duración del Viaje <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control @error('duracion_minutos') is-invalid @enderror" 
                                       id="duracion_minutos" 
                                       name="duracion_minutos" 
                                       value="{{ old('duracion_minutos') }}"
                                       min="1"
                                       placeholder="120"
                                       required>
                                <span class="input-group-text">minutos</span>
                            </div>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Duración estimada del viaje en minutos
                            </div>
                            <div class="mt-1">
                                <small class="text-muted" id="duracion-helper">
                                    <!-- Se llena dinámicamente con JavaScript -->
                                </small>
                            </div>
                            @error('duracion_minutos')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Descripción -->
                    <div class="mb-4">
                        <label for="descripcion" class="form-label">
                            <i class="bi bi-card-text text-secondary me-1"></i>Descripción de la Ruta
                        </label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                  id="descripcion" 
                                  name="descripcion" 
                                  rows="3"
                                  placeholder="Ej: Ruta directa Montevideo - Punta del Este con paradas en Ciudad de la Costa y Atlántida">{{ old('descripcion') }}</textarea>
                        <div class="form-text">
                            <i class="bi bi-info-circle me-1"></i>
                            Información adicional sobre la ruta, paradas intermedias, etc. (Opcional)
                        </div>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Botones de acción -->
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('admin.rutas.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-2"></i>Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="bi bi-check-circle me-2"></i>Crear Ruta
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Panel lateral con información de ayuda -->
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-info text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-lightbulb me-2"></i>Guía para Crear Rutas
                </h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6><i class="bi bi-1-circle text-primary me-2"></i>Código de Ruta</h6>
                    <p class="small text-muted mb-2">
                        Use un formato claro como: <code>ORIGEN-DESTINO-###</code>
                    </p>
                    <p class="small text-muted">
                        <strong>Ejemplos:</strong><br>
                        • MVD-PDP-001<br>
                        • MON-MAL-002<br>
                        • COL-TAC-001
                    </p>
                </div>
                
                <div class="mb-3">
                    <h6><i class="bi bi-2-circle text-success me-2"></i>Ciudades</h6>
                    <p class="small text-muted">
                        Escriba el nombre completo de las ciudades uruguayas.
                    </p>
                </div>
                
                <div class="mb-3">
                    <h6><i class="bi bi-3-circle text-warning me-2"></i>Precios</h6>
                    <p class="small text-muted">
                        Los precios se manejan en <strong>pesos uruguayos (UYU)</strong>. 
                        Considere la distancia y calidad del servicio.
                    </p>
                    <div class="alert alert-light p-2">
                        <small>
                            <strong>Referencia de precios:</strong><br>
                            • Trayectos cortos: $U 200-500<br>
                            • Trayectos medios: $U 500-1000<br>
                            • Trayectos largos: $U 1000+
                        </small>
                    </div>
                </div>
                
                <div class="mb-3">
                    <h6><i class="bi bi-4-circle text-info me-2"></i>Duración</h6>
                    <p class="small text-muted">
                        Calcule el tiempo estimado de viaje considerando:
                    </p>
                    <ul class="small text-muted">
                        <li>Distancia entre ciudades</li>
                        <li>Condiciones del tráfico</li>
                        <li>Paradas intermedias</li>
                        <li>Descansos del conductor</li>
                    </ul>
                </div>
            </div>
        </div>
        
        <!-- Panel de rutas existentes -->
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-secondary text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-list me-2"></i>Rutas Existentes
                </h6>
            </div>
            <div class="card-body">
                @php
                    $rutasExistentes = \App\Models\Ruta::orderBy('codigo')->take(5)->get();
                @endphp
                
                @forelse($rutasExistentes as $rutaExistente)
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                        <div>
                            <small class="fw-bold text-primary">{{ $rutaExistente->codigo }}</small><br>
                            <small class="text-muted">{{ $rutaExistente->origen }} → {{ $rutaExistente->destino }}</small>
                        </div>
                        <div class="text-end">
                            <small class="fw-bold text-success">$U {{ number_format($rutaExistente->precio, 0) }}</small><br>
                            <small class="text-muted">{{ floor($rutaExistente->duracion_minutos / 60) }}h {{ $rutaExistente->duracion_minutos % 60 }}m</small>
                        </div>
                    </div>
                @empty
                    <p class="text-muted small mb-0">No hay rutas registradas aún.</p>
                @endforelse
                
                @if($rutasExistentes->count() > 0)
                    <div class="text-center mt-3">
                        <a href="{{ route('admin.rutas.index') }}" class="btn btn-outline-secondary btn-sm">
                            Ver todas las rutas
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Convertir minutos a formato legible
    function formatearDuracion(minutos) {
        if (!minutos || minutos <= 0) {
            return '';
        }
        
        const horas = Math.floor(minutos / 60);
        const mins = minutos % 60;
        
        let texto = '';
        if (horas > 0) {
            texto += horas + (horas === 1 ? ' hora' : ' horas');
        }
        if (mins > 0) {
            if (horas > 0) texto += ' y ';
            texto += mins + (mins === 1 ? ' minuto' : ' minutos');
        }
        
        return texto;
    }
    
    // Actualizar helper de duración en tiempo real
    $('#duracion_minutos').on('input', function() {
        const minutos = parseInt($(this).val());
        const helper = $('#duracion-helper');
        
        if (minutos && minutos > 0) {
            helper.html(`
                <i class="bi bi-clock text-info me-1"></i>
                Equivale a: <strong>${formatearDuracion(minutos)}</strong>
            `);
        } else {
            helper.empty();
        }
    });
    
    // Formatear código automáticamente
    $('#codigo').on('input', function() {
        let valor = $(this).val().toUpperCase();
        // Permitir solo letras, números y guiones
        valor = valor.replace(/[^A-Z0-9\-]/g, '');
        $(this).val(valor);
    });
    
    // Formatear nombres de ciudades
    $('#origen, #destino').on('input', function() {
        let valor = $(this).val();
        // Capitalizar primera letra de cada palabra
        valor = valor.toLowerCase().replace(/\b\w/g, l => l.toUpperCase());
        $(this).val(valor);
    });
    
    // Validación del formulario
    $('#rutaForm').on('submit', function(e) {
        let valid = true;
        let errores = [];
        
        // Validar código único (simulado - en producción se haría en el servidor)
        const codigo = $('#codigo').val().trim();
        if (codigo.length < 5) {
            errores.push('El código debe tener al menos 5 caracteres');
            valid = false;
        }
        
        // Validar que origen y destino sean diferentes
        const origen = $('#origen').val().trim().toLowerCase();
        const destino = $('#destino').val().trim().toLowerCase();
        if (origen === destino) {
            errores.push('El origen y destino deben ser diferentes');
            valid = false;
        }
        
        // Validar precio
        const precio = parseFloat($('#precio').val());
        if (precio <= 0) {
            errores.push('El precio debe ser mayor a 0');
            valid = false;
        }
        
        // Validar duración
        const duracion = parseInt($('#duracion_minutos').val());
        if (duracion <= 0) {
            errores.push('La duración debe ser mayor a 0 minutos');
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
        submitBtn.html('<i class="bi bi-hourglass-split me-2"></i>Creando Ruta...');
        submitBtn.prop('disabled', true);
        
        // Permitir envío
        return true;
    });
    
    // Preview de la ruta
    $('#origen, #destino, #precio, #duracion_minutos').on('input', function() {
        actualizarPreview();
    });
    
    function actualizarPreview() {
        const origen = $('#origen').val().trim();
        const destino = $('#destino').val().trim();
        const precio = $('#precio').val();
        const duracion = $('#duracion_minutos').val();
        
        if (origen && destino && precio && duracion) {
            // Aquí podrías agregar un preview de la ruta
            console.log('Preview:', { origen, destino, precio, duracion });
        }
    }
    
    // Inicializar helper de duración si hay valor
    if ($('#duracion_minutos').val()) {
        $('#duracion_minutos').trigger('input');
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

.input-group-text {
    background-color: #f8f9fa;
    font-weight: 600;
}

.card {
    border: none;
    border-radius: 0.75rem;
}

.alert-light {
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
}

code {
    background-color: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.875em;
}

#duracion-helper {
    min-height: 20px;
}
</style>
@endpush