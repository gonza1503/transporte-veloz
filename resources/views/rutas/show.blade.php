@extends('layouts.app')

@section('title', 'Detalles de Ruta - ' . $ruta->codigo)

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('rutas.index') }}">Rutas</a></li>
    <li class="breadcrumb-item active">{{ $ruta->codigo }}</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">
                    <i class="bi bi-map text-primary me-2"></i>
                    Ruta {{ $ruta->codigo }}
                </h1>
                <p class="text-muted">{{ $ruta->origen }} → {{ $ruta->destino }}</p>
            </div>
            <div>
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('admin.rutas.edit', $ruta) }}" class="btn btn-warning">
                        <i class="bi bi-pencil me-2"></i>Editar
                    </a>
                @endif
                <a href="{{ route('rutas.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-2"></i>Volver
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Información principal -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">
                    <i class="bi bi-info-circle me-2"></i>Información de la Ruta
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Detalles Generales</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <strong>Código:</strong> 
                                <span class="badge bg-primary fs-6">{{ $ruta->codigo }}</span>
                            </li>
                            <li class="mb-2">
                                <strong>Origen:</strong> {{ $ruta->origen }}
                            </li>
                            <li class="mb-2">
                                <strong>Destino:</strong> {{ $ruta->destino }}
                            </li>
                            <li class="mb-2">
                                <strong>Duración:</strong> {{ $ruta->getDuracionFormateada() }}
                            </li>
                            <li class="mb-2">
                                <strong>Estado:</strong>
                                @if($ruta->activa)
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>Activa
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="bi bi-x-circle me-1"></i>Inactiva
                                    </span>
                                @endif
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6>Información Comercial</h6>
                        <ul class="list-unstyled">
                            <li class="mb-2">
                                <strong>Precio por pasaje:</strong>
                                <span class="fs-4 fw-bold text-success">S/ {{ number_format($ruta->precio, 2) }}</span>
                            </li>
                            <li class="mb-2">
                                <strong>Fecha de creación:</strong> 
                                {{ $ruta->created_at->format('d/m/Y') }}
                            </li>
                            <li class="mb-2">
                                <strong>Última actualización:</strong> 
                                {{ $ruta->updated_at->format('d/m/Y H:i') }}
                            </li>
                        </ul>
                    </div>
                </div>
                
                @if($ruta->descripcion)
                    <hr>
                    <h6>Descripción</h6>
                    <p class="text-muted">{{ $ruta->descripcion }}</p>
                @endif
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Estadísticas -->
        <div class="card shadow-sm">
            <div class="card-header bg-success text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-graph-up me-2"></i>Estadísticas
                </h6>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6 mb-3">
                        <div class="fs-3 fw-bold text-primary">{{ $stats['horarios_programados'] }}</div>
                        <small class="text-muted">Horarios Programados</small>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="fs-3 fw-bold text-success">{{ $stats['ventas_realizadas'] }}</div>
                        <small class="text-muted">Ventas Realizadas</small>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="fs-3 fw-bold text-warning">{{ $stats['proximos_viajes'] }}</div>
                        <small class="text-muted">Próximos Viajes</small>
                    </div>
                    <div class="col-6 mb-3">
                        <div class="fs-3 fw-bold text-info">S/ {{ number_format($stats['ingresos_generados'], 2) }}</div>
                        <small class="text-muted">Ingresos Generados</small>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Acciones rápidas -->
        @if(auth()->user()->isAdmin())
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="card-title mb-0">
                        <i class="bi bi-lightning me-2"></i>Acciones Rápidas
                    </h6>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.horarios.create') }}?ruta_id={{ $ruta->id }}" class="btn btn-outline-primary">
                            <i class="bi bi-clock me-2"></i>Crear Horario
                        </a>
                        
                        <form method="POST" action="{{ route('admin.rutas.toggle', $ruta) }}" class="d-inline">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-outline-{{ $ruta->activa ? 'danger' : 'success' }} w-100">
                                <i class="bi bi-{{ $ruta->activa ? 'x-circle' : 'check-circle' }} me-2"></i>
                                {{ $ruta->activa ? 'Desactivar' : 'Activar' }} Ruta
                            </button>
                        </form>
                        
                        <a href="{{ route('admin.rutas.edit', $ruta) }}" class="btn btn-outline-warning">
                            <i class="bi bi-pencil me-2"></i>Editar Información
                        </a>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Horarios recientes -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="bi bi-clock me-2"></i>Horarios Recientes
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('admin.horarios.create') }}?ruta_id={{ $ruta->id }}" class="btn btn-sm btn-primary ms-3">
                            <i class="bi bi-plus me-1"></i>Nuevo Horario
                        </a>
                    @endif
                </h6>
            </div>
            <div class="card-body p-0">
                @if($ruta->horarios->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha</th>
                                    <th>Hora Salida</th>
                                    <th>Bus</th>
                                    <th>Asientos Disponibles</th>
                                    <th>Estado</th>
                                    @if(auth()->user()->isAdmin())
                                        <th>Acciones</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($ruta->horarios as $horario)
                                    <tr>
                                        <td>
                                            <strong>{{ \Carbon\Carbon::parse($horario->fecha)->format('d/m/Y') }}</strong>
                                            <div class="small text-muted">
                                                {{ \Carbon\Carbon::parse($horario->fecha)->locale('es')->isoFormat('dddd') }}
                                            </div>
                                        </td>
                                        <td>
                                            <span class="fs-5 fw-bold text-primary">
                                                {{ \Carbon\Carbon::parse($horario->hora_salida)->format('H:i') }}
                                            </span>
                                        </td>
                                        <td>
                                            <div>
                                                <strong>{{ $horario->bus->placa }}</strong>
                                            </div>
                                            <small class="text-muted">{{ $horario->bus->modelo }}</small>
                                        </td>
                                        <td>
                                            @php
                                                $porcentajeOcupacion = (($horario->bus->capacidad - $horario->asientos_disponibles) / $horario->bus->capacidad) * 100;
                                            @endphp
                                            
                                            <div class="d-flex align-items-center">
                                                <span class="me-2">{{ $horario->asientos_disponibles }}/{{ $horario->bus->capacidad }}</span>
                                                <div class="progress flex-grow-1" style="height: 6px;">
                                                    <div class="progress-bar bg-{{ $porcentajeOcupacion > 80 ? 'danger' : ($porcentajeOcupacion > 50 ? 'warning' : 'success') }}" 
                                                         style="width: {{ $porcentajeOcupacion }}%"></div>
                                                </div>
                                            </div>
                                            <small class="text-muted">{{ number_format($porcentajeOcupacion, 1) }}% ocupado</small>
                                        </td>
                                        <td>
                                            @if($horario->activo)
                                                @if(\Carbon\Carbon::parse($horario->fecha)->isPast())
                                                    <span class="badge bg-secondary">
                                                        <i class="bi bi-clock-history me-1"></i>Finalizado
                                                    </span>
                                                @else
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle me-1"></i>Activo
                                                    </span>
                                                @endif
                                            @else
                                                <span class="badge bg-danger">
                                                    <i class="bi bi-x-circle me-1"></i>Cancelado
                                                </span>
                                            @endif
                                        </td>
                                        @if(auth()->user()->isAdmin())
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.horarios.edit', $horario) }}" 
                                                       class="btn btn-sm btn-outline-warning"
                                                       title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    
                                                    @if($horario->activo && $horario->ventas()->count() == 0)
                                                        <form method="POST" action="{{ route('admin.horarios.cancelar', $horario) }}" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm btn-outline-danger"
                                                                    onclick="return confirm('¿Seguro que quieres cancelar este horario?')"
                                                                    title="Cancelar">
                                                                <i class="bi bi-x-circle"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-clock fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No hay horarios programados</h5>
                        <p class="text-muted">Los horarios para esta ruta aparecerán aquí</p>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.horarios.create') }}?ruta_id={{ $ruta->id }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Crear Primer Horario
                            </a>
                        @endif
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
    // Confirmación para cambio de estado
    $('form[action*="toggle"]').submit(function(e) {
        e.preventDefault();
        const form = this;
        const accion = $(form).find('button').text().trim();
        
        Swal.fire({
            title: '¿Confirmar acción?',
            text: `¿Estás seguro de ${accion.toLowerCase()} esta ruta?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, continuar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });
});
</script>
@endpush