@extends('layouts.app')

@section('title', 'Historial de Ventas - Transporte Veloz')

@section('breadcrumb')
    <li class="breadcrumb-item active">Mis Ventas</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">
                    <i class="bi bi-clock-history text-primary me-2"></i>
                    Mi Historial de Ventas
                </h1>
                <p class="text-muted">Revisa todas las ventas que has realizado</p>
            </div>
            <a href="{{ route('ventas.index') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle me-2"></i>Nueva Venta
            </a>
        </div>
    </div>
</div>

@if($ventas->count() > 0)
    <div class="row">
        <div class="col-12">
            @foreach($ventas as $venta)
                @php
                    $ruta = $venta->horario->ruta;
                    $horario = $venta->horario;
                @endphp
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <h5 class="card-title mb-1">
                                    <i class="bi bi-map me-2"></i>{{ $ruta->codigo }} - {{ $venta->codigo_venta }}
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
                                            {{ \Carbon\Carbon::parse($horario->fecha)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                                        </small>
                                    </div>
                                    <div class="col-sm-6">
                                        <small class="opacity-75">
                                            <i class="bi bi-clock me-1"></i>
                                            Salida: {{ \Carbon\Carbon::parse($horario->hora_salida)->format('H:i') }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-md-end">
                                <div class="fs-3 fw-bold">S/ {{ number_format($venta->total, 2) }}</div>
                                <small class="opacity-75">{{ $venta->cantidad_pasajes }} {{ $venta->cantidad_pasajes == 1 ? 'pasaje' : 'pasajes' }}</small>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3">
                                    <i class="bi bi-info-circle me-2"></i>Información del Viaje
                                </h6>
                                <div class="row">
                                    <div class="col-6">
                                        <small class="text-muted">Bus:</small><br>
                                        <strong>{{ $horario->bus->placa }}</strong>
                                    </div>
                                    <div class="col-6">
                                        <small class="text-muted">Modelo:</small><br>
                                        <span>{{ $horario->bus->modelo }}</span>
                                    </div>
                                    <div class="col-6 mt-2">
                                        <small class="text-muted">Chofer:</small><br>
                                        <span>{{ $horario->bus->chofer }}</span>
                                    </div>
                                    <div class="col-6 mt-2">
                                        <small class="text-muted">Duración:</small><br>
                                        <span>{{ $ruta->getDuracionFormateada() }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h6 class="fw-bold mb-3">
                                    <i class="bi bi-people me-2"></i>Pasajeros y Asientos
                                </h6>
                                <div class="row">
                                    @foreach($venta->tickets as $ticket)
                                        <div class="col-12 mb-2">
                                            <div class="d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                                <div>
                                                    <strong>{{ $ticket->pasajero->nombre }}</strong><br>
                                                    <small class="text-muted">DNI: {{ $ticket->pasajero->dni }}</small>
                                                </div>
                                                <div class="text-end">
                                                    <span class="badge bg-success fs-6">Asiento {{ $ticket->asiento }}</span><br>
                                                    <small class="text-muted">{{ $ticket->numero_ticket }}</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <i class="bi bi-clock-history me-1"></i>
                                    Venta realizada el {{ $venta->fecha_venta->format('d/m/Y H:i') }}
                                </small>
                                <br>
                                <span class="badge bg-{{ $venta->estado == 'completada' ? 'success' : 'danger' }}">
                                    {{ ucfirst($venta->estado) }}
                                </span>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <a href="{{ route('ventas.ticket.pdf', $venta->id) }}" 
                                   class="btn btn-outline-primary btn-sm" target="_blank">
                                    <i class="bi bi-file-earmark-pdf me-1"></i>
                                    Descargar PDF
                                </a>
                                <button type="button" class="btn btn-outline-info btn-sm" 
                                        onclick="verDetalles({{ $venta->id }})">
                                    <i class="bi bi-eye me-1"></i>
                                    Ver Detalles
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    
    <!-- Paginación -->
    <div class="row">
        <div class="col-12">
            {{ $ventas->links() }}
        </div>
    </div>
@else
    <!-- No hay ventas -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center py-5">
                    <i class="bi bi-inbox fs-1 text-muted mb-3"></i>
                    <h4 class="text-muted">No tienes ventas registradas</h4>
                    <p class="text-muted mb-4">
                        Cuando realices tu primera venta aparecerá aquí.
                    </p>
                    <a href="{{ route('ventas.index') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle me-2"></i>Realizar Primera Venta
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

<!-- Modal para ver detalles -->
<div class="modal fade" id="detallesModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-receipt me-2"></i>Detalles de la Venta
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="modal-detalles-content">
                <!-- Contenido cargado dinámicamente -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function verDetalles(ventaId) {
    // Aquí puedes cargar detalles adicionales via AJAX si es necesario
    // Por ahora, simplemente mostrar información básica
    $('#modal-detalles-content').html(`
        <div class="text-center">
            <div class="spinner-border" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
        </div>
    `);
    
    $('#detallesModal').modal('show');
    
    // Simular carga de datos
    setTimeout(() => {
        $('#modal-detalles-content').html(`
            <div class="alert alert-info">
                <i class="bi bi-info-circle me-2"></i>
                Los detalles completos están disponibles en el PDF del ticket.
            </div>
            <p>Para obtener información detallada de esta venta, puedes descargar el PDF del ticket.</p>
        `);
    }, 1000);
}

$(document).ready(function() {
    // Auto-refresh cada 30 segundos si estás en la primera página
    @if(request()->get('page', 1) == 1)
        setInterval(() => {
            if (!$('.modal').is(':visible')) {
                location.reload();
            }
        }, 30000);
    @endif
});
</script>
@endpush