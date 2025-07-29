@extends('layouts.app')

@section('title', 'Gestión de Rutas - Transporte Veloz')

@section('breadcrumb')
    <li class="breadcrumb-item active">Rutas</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">
                    <i class="bi bi-map text-primary me-2"></i>
                    Gestión de Rutas
                </h1>
                <p class="text-muted">Administra las rutas disponibles en la flota</p>
            </div>
            @if(auth()->user()->isAdmin())
                <a href="{{ route('admin.rutas.create') }}" class="btn btn-primary">
                    <i class="bi bi-plus-circle me-2"></i>Registrar Nueva Ruta
                </a>
            @endif
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
                            <option value="inactivo" {{ request('estado') == 'inactivo' ? 'selected' : '' }}>Inactivo</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label for="buscar" class="form-label small">Buscar</label>
                        <input type="text" class="form-control" id="buscar" name="buscar" 
                               value="{{ request('buscar') }}" placeholder="Código, origen o destino...">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-search me-1"></i>Filtrar
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.rutas.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-x-circle me-1"></i>Limpiar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Tabla de rutas -->
<div class="row">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="bi bi-list me-2"></i>Lista de Rutas
                    <span class="badge bg-secondary ms-2">{{ $rutas->total() }} total</span>
                </h6>
            </div>
            <div class="card-body p-0">
                @if($rutas->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Código</th>
                                    <th>Origen</th>
                                    <th>Destino</th>
                                    <th>Distancia (km)</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($rutas as $ruta)
                                    <tr>
                                        <td><strong class="text-primary">{{ $ruta->codigo }}</strong></td>
                                        <td>{{ $ruta->origen }}</td>
                                        <td>{{ $ruta->destino }}</td>
                                        <td>{{ number_format($ruta->distancia, 1, ',', '.') }}</td>
                                        <td>
                                            @if($ruta->estado === 'activo')
                                                <span class="badge bg-success">
                                                    <i class="bi bi-check-circle me-1"></i>Activo
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="bi bi-x-circle me-1"></i>Inactivo
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('admin.rutas.show', $ruta) }}" class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Ver detalles">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if(auth()->user()->isAdmin())
                                                    <a href="{{ route('admin.rutas.edit', $ruta) }}" class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" title="Editar">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.rutas.destroy', $ruta) }}" class="d-inline" onsubmit="return confirm('¿Seguro que quieres eliminar esta ruta?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
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
                        {{ $rutas->withQueryString()->links() }}
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                        <h5 class="text-muted">No hay rutas registradas</h5>
                        <p class="text-muted">Las rutas aparecerán aquí cuando las registres.</p>
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('admin.rutas.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Registrar Primera Ruta
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
document.addEventListener('DOMContentLoaded', function () {
    // Tooltips Bootstrap
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush

