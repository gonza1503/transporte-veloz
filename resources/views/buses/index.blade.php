@extends('layouts.app')

@section('title', 'Gestión de Buses - Transporte Veloz')

@section('breadcrumb')
    <li class="breadcrumb-item active">Buses</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">
                    <i class="bi bi-bus-front text-primary me-2"></i>
                    Gestión de Buses
                </h1>
                <p class="text-muted">Administra la flota de vehículos</p>
            </div>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.buses.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Registrar Nuevo Bus
                </a>
            @endif
        </div>
    </div>
</div>

<!-- Estadísticas rápidas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-success text-white shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-check-circle fs-2 mb-2"></i>
                <h3 class="mb-1">{{ $buses->where('estado', 'activo')->count() }}</h3>
                <small>Buses Activos</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-dark shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-wrench fs-2 mb-2"></i>
                <h3 class="mb-1">{{ $buses->where('estado', 'mantenimiento')->count() }}</h3>
                <small>En Mantenimiento</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-hourglass-half fs-2 mb-2"></i>
                <h3 class="mb-1">{{ $buses->where('estado', 'ocupado')->count() }}</h3>
                <small>Ocupados</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-primary text-white shadow-sm">
            <div class="card-body text-center">
                <i class="bi bi-bus-front fs-2 mb-2"></i>
                <h3 class="mb-1">{{ $buses->count() }}</h3>
                <small>Total Buses</small>
            </div>
        </div>
    </div>
</div>

<!-- Filtros -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-body">
                <form method="GET" class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label for="estado" class="form-label small">Filtrar por Estado</label>
                        <select class="form-select" id="estado" name="estado">
                            <option value="">Todos los estados</option>
                            <option value="activo" {{ request('estado') == 'activo' ? 'selected' : '' }}>Activo</option>
                            <option value="mantenimiento" {{ request('estado') == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                            <option value="ocupado" {{ request('estado') == 'ocupado' ? 'selected' : '' }}>Ocupado</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="buscar" class="form-label small">Buscar</label>
                        <input type="text" class="form-control" id="buscar" name="buscar" 
                               value="{{ request('buscar') }}" placeholder="Placa, modelo o chofer...">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i>Filtrar
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('buses.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-x-circle me-1"></i>Limpiar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Lista de buses -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="bi bi-list me-2"></i>Lista de Buses
                    <span class="badge bg-secondary ms-2">{{ $buses->total() }} total</span>
                </h6>
            </div>
            <div class="card-body p-0">
                @if($buses->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Placa</th>
                                    <th>Modelo</th>
                                    <th>Año</th>
                                    <th>Capacidad</th>
                                    <th>Chofer</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($buses as $bus)
                                    <tr>
                                        <td>
                                            <strong class="text-primary">{{ $bus->placa }}</strong>
                                        </td>
                                        <td>{{ $bus->modelo }}</td>
                                        <td>{{ $bus->anio }}</td>
                                        <td>
                                            <span class="badge bg-info">{{ $bus->capacidad }} asientos</span>
                                        </td>
                                        <td>
                                            <i class="bi bi-person me-1"></i>{{ $bus->chofer }}
                                        </td>
                                        <td>
                                            @switch($bus->estado)
                                                @case('activo')
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-check-circle me-1"></i>Activo
                                                    </span>
                                                    @break
                                                @case('mantenimiento')
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="bi bi-wrench me-1"></i>Mantenimiento
                                                    </span>
                                                    @break
                                                @case('ocupado')
                                                    <span class="badge bg-info">
                                                        <i class="bi bi-hourglass-half me-1"></i>Ocupado
                                                    </span>
                                                    @break
                                            @endswitch
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('buses.show', $bus) }}" 
                                                   class="btn btn-sm btn-outline-info" 
                                                   data-bs-toggle="tooltip" title="Ver detalles">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                
                                                @if(auth()->user()->isAdmin())
                                                    <a href="{{ route('admin.buses.edit', $bus) }}" 
                                                       class="btn btn-sm btn-outline-warning"
                                                       data-bs-toggle="tooltip" title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    
                                                    <!-- Estado rápido -->
                                                    <div class="btn-group">
                                                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                                data-bs-toggle="dropdown">
                                                            <i class="bi bi-gear"></i>
                                                        </button>
                                                        <ul class="dropdown-menu">
                                                            @if($bus->estado !== 'activo')
                                                                <li>
                                                                    <form method="POST" action="{{ route('admin.buses.cambiarEstado', $bus) }}" class="d-inline">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <input type="hidden" name="estado" value="activo">
                                                                        <button type="submit" class="dropdown-item">
                                                                            <i class="bi bi-check-circle text-success me-2"></i>Activar
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @endif
                                                            @if($bus->estado !== 'mantenimiento')
                                                                <li>
                                                                    <form method="POST" action="{{ route('admin.buses.cambiarEstado', $bus) }}" class="d-inline">
                                                                        @csrf
                                                                        @method('PATCH')
                                                                        <input type="hidden" name="estado" value="mantenimiento">
                                                                        <button type="submit" class="dropdown-item">
                                                                            <i class="bi bi-wrench text-warning me-2"></i>Mantenimiento
                                                                        </button>
                                                                    </form>
                                                                </li>
                                                            @endif
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li>
                                                                <form method="POST" action="{{ route('admin.buses.destroy', $bus) }}" class="d-inline">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <button type="submit" class="dropdown-item text-danger btn-delete">
                                                                        <i class="bi bi-trash me-2"></i>Eliminar
                                                                    </button>
                                                                </form>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginación -->
                    <div class="card-footer">
                        {{ $buses->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No hay buses registrados</h5>
                        <p class="text-muted">Los buses registrados aparecerán aquí</p>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.buses.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Registrar Primer Bus
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
    // Habilitar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Búsqueda en tiempo real
    let timeout;
    $('#buscar').on('input', function() {
        clearTimeout(timeout);
        timeout = setTimeout(() => {
            if ($(this).val().length >= 3 || $(this).val().length === 0) {
                $(this).closest('form').submit();
            }
        }, 800);
    });
    
    // Confirmación de cambio de estado
    $('form[action*="cambiarEstado"]').submit(function(e) {
        e.preventDefault();
        const form = this;
        const estado = $(form).find('input[name="estado"]').val();
        
        Swal.fire({
            title: '¿Cambiar estado?',
            text: `¿Estás seguro de cambiar el estado del bus a "${estado}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, cambiar',
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