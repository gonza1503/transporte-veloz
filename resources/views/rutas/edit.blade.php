@extends('layouts.app')

@section('title', 'Editar Ruta - ' . $ruta->codigo)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('rutas.index') }}">Rutas</a></li>
    <li class="breadcrumb-item"><a href="{{ route('rutas.show', $ruta) }}">{{ $ruta->codigo }}</a></li>
    <li class="breadcrumb-item active">Editar</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-warning text-dark">
                <h5 class="card-title mb-0">
                    <i class="bi bi-pencil me-2"></i>Editar Ruta: {{ $ruta->codigo }}
                </h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.rutas.update', $ruta) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="codigo" class="form-label">
                                <i class="bi bi-tag me-1"></i>Código de Ruta *
                            </label>
                            <input type="text" 
                                   class="form-control @error('codigo') is-invalid @enderror" 
                                   id="codigo" 
                                   name="codigo" 
                                   value="{{ old('codigo', $ruta->codigo) }}" 
                                   required
                                   placeholder="Ej: LIM-ARE-001">
                            @error('codigo')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="precio" class="form-label">
                                <i class="bi bi-currency-dollar me-1"></i>Precio por Pasaje (S/) *
                            </label>
                            <input type="number" 
                                   class="form-control @error('precio') is-invalid @enderror" 
                                   id="precio" 
                                   name="precio" 
                                   value="{{ old('precio', $ruta->precio) }}" 
                                   step="0.01" 
                                   min="0" 
                                   required
                                   placeholder="Ej: 85.00">
                            @error('precio')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="origen" class="form-label">
                                <i class="bi bi-geo-alt me-1"></i>Ciudad de Origen *
                            </label>
                            <input type="text" 
                                   class="form-control @error('origen') is-invalid @enderror" 
                                   id="origen" 
                                   name="origen" 
                                   value="{{ old('origen', $ruta->origen) }}" 
                                   required
                                   placeholder="Ej: Lima">
                            @error('origen')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="destino" class="form-label">
                                <i class="bi bi-geo-alt-fill me-1"></i>Ciudad de Destino *
                            </label>
                            <input type="text" 
                                   class="form-control @error('destino') is-invalid @enderror" 
                                   id="destino" 
                                   name="destino" 
                                   value="{{ old('destino', $ruta->destino) }}" 
                                   required
                                   placeholder="Ej: Arequipa">
                            @error('destino')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="duracion_minutos" class="form-label">
                                <i class="bi bi-clock me-1"></i>Duración del Viaje *
                            </label>
                            <div class="input-group">
                                <input type="number" 
                                       class="form-control @error('duracion_minutos') is-invalid @enderror" 
                                       id="duracion_minutos" 
                                       name="duracion_minutos" 
                                       value="{{ old('duracion_minutos', $ruta->duracion_minutos) }}" 
                                       min="1" 
                                       required
                                       placeholder="960">
                                <span class="input-group-text">minutos</span>
                            </div>
                            <div class="form-text">
                                <i class="bi bi-info-circle me-1"></i>
                                Duración actual: {{ $ruta->getDuracionFormateada() }}
                            </div>
                            @error('duracion_minutos')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">
                                <i class="bi bi-toggle-on me-1"></i>Estado de la Ruta
                            </label>
                            <div class="form-check form-switch">
                                <input class="form-check-input" 
                                       type="checkbox" 
                                       id="activa" 
                                       name="activa" 
                                       value="1"
                                       {{ old('activa', $ruta->activa) ? 'checked' : '' }}>
                                <label class="form-check-label" for="activa">
                                    Ruta activa (disponible para ventas)
                                </label>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="descripcion" class="form-label">
                            <i class="bi bi-text-paragraph me-1"></i>Descripción (Opcional)
                        </label>
                        <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                  id="descripcion" 
                                  name="descripcion" 
                                  rows="3"
                                  placeholder="Descripción adicional de la ruta, paradas intermedias, etc.">{{ old('descripcion', $ruta->descripcion) }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <!-- Información de impacto -->
                    @if($ruta->horarios()->count() > 0)
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            <strong>Atención:</strong> Esta ruta tiene {{ $ruta->horarios()->count() }} horarios programados. 
                            Los cambios en el precio afectarán solo a las nuevas ventas.
                        </div>
                    @endif
                    
                    <div class="d-flex justify-content-between">
                        <div>
                            <a href="{{ route('rutas.show', $ruta) }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Cancelar
                            </a>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-warning">
                                <i class="bi bi-check-lg me-2"></i>Actualizar Ruta
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Información adicional -->
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-info text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-lightbulb me-2"></i>Información Adicional
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Creación y Modificación</h6>
                        <ul class="list-unstyled small">
                            <li><strong>Creada:</strong> {{ $ruta->created_at->format('d/m/Y H:i') }}</li>
                            <li><strong>Última modificación:</strong> {{ $ruta->updated_at->format('d/m/Y H:i') }}</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Estadísticas de Uso</h6>
                        <ul class="list-unstyled small">
                            <li><strong>Horarios programados:</strong> {{ $ruta->horarios()->count() }}</li>
                            <li><strong>Ventas realizadas:</strong> {{ $ruta->horarios()->withCount('ventas')->get()->sum('ventas_count') }}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Calcular duración en tiempo real
    $('#duracion_minutos').on('input', function() {
        const minutos = parseInt($(this).val()) || 0;
        const horas = Math.floor(minutos / 60);
        const mins = minutos % 60;
        const duracionTexto = horas + 'h ' + mins + 'm';
        
        $(this).siblings('.form-text').html(
            '<i class="bi bi-info-circle me-1"></i>Duración: ' + duracionTexto
        );
    });
    
    // Validación en tiempo real
    $('form').submit(function(e) {
        let valid = true;
        
        // Validar campos requeridos
        $(this).find('[required]').each(function() {
            if (!$(this).val().trim()) {
                valid = false;
                $(this).addClass('is-invalid');
            } else {
                $(this).removeClass('is-invalid');
            }
        });
        
        // Validar que origen y destino sean diferentes
        const origen = $('#origen').val().trim().toLowerCase();
        const destino = $('#destino').val().trim().toLowerCase();
        
        if (origen && destino && origen === destino) {
            valid = false;
            $('#destino').addClass('is-invalid');
            
            Swal.fire({
                icon: 'error',
                title: 'Error de validación',
                text: 'El origen y destino no pueden ser iguales'
            });
        }
        
        if (!valid) {
            e.preventDefault();
        }
    });
    
    // Formatear código en mayúsculas
    $('#codigo').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });
});
</script>
@endpush