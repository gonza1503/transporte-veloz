@extends('layouts.app')

@section('title', 'Venta Confirmada - Transporte Veloz')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('ventas.index') }}">Nueva Venta</a></li>
    <li class="breadcrumb-item active">Venta Confirmada</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">
        <!-- Mensaje de éxito -->
        <div class="card bg-success text-white shadow-lg mb-4">
            <div class="card-body text-center py-5">
                <i class="bi bi-check-circle-fill fs-1 mb-3"></i>
                <h2 class="card-title mb-3">¡Venta Procesada Exitosamente!</h2>
                <p class="fs-5 mb-0">
                    Código de venta: <strong>{{ $venta->codigo_venta }}</strong>
                </p>
                <p class="mb-0 opacity-75">
                    Procesado el {{ $venta->fecha_venta->format('d/m/Y H:i') }} por {{ $venta->user->name }}
                </p>
            </div>
        </div>
        
        <!-- Información de la venta -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-primary text-white">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-info-circle me-2"></i>Información del Viaje
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-5 fw-bold">Ruta:</div>
                            <div class="col-7">{{ $venta->horario->ruta->codigo }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 fw-bold">Origen:</div>
                            <div class="col-7">{{ $venta->horario->ruta->origen }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 fw-bold">Destino:</div>
                            <div class="col-7">{{ $venta->horario->ruta->destino }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 fw-bold">Fecha:</div>
                            <div class="col-7">
                                {{ \Carbon\Carbon::parse($venta->horario->fecha)->locale('es')->isoFormat('dddd, D [de] MMMM [de] YYYY') }}
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 fw-bold">Horario:</div>
                            <div class="col-7">{{ \Carbon\Carbon::parse($venta->horario->hora_salida)->format('H:i') }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-5 fw-bold">Bus:</div>
                            <div class="col-7">{{ $venta->horario->bus->placa }} - {{ $venta->horario->bus->modelo }}</div>
                        </div>
                        <div class="row mb-0">
                            <div class="col-5 fw-bold">Chofer:</div>
                            <div class="col-7">{{ $venta->horario->bus->chofer }}</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-success text-white">
                        <h6 class="card-title mb-0">
                            <i class="bi bi-receipt me-2"></i>Resumen de la Venta
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-6 fw-bold">Cantidad:</div>
                            <div class="col-6">{{ $venta->cantidad_pasajes }} {{ $venta->cantidad_pasajes == 1 ? 'pasaje' : 'pasajes' }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6 fw-bold">Precio unitario:</div>
                            <div class="col-6">/ {{ number_format($venta->horario->ruta->precio) }}</div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-6 fw-bold">Asientos:</div>
                            <div class="col-6">
                                @foreach($tickets as $ticket)
                                    <span class="badge bg-primary me-1">{{ $ticket->asiento }}</span>
                                @endforeach
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-6 fw-bold fs-5">Total Pagado:</div>
                            <div class="col-6 fs-5 fw-bold text-success">S/ {{ number_format($venta->total, 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Lista de tickets/pasajeros -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-info text-white">
                <h6 class="card-title mb-0">
                    <i class="bi bi-people me-2"></i>Tickets Generados
                </h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nº Ticket</th>
                                <th>Pasajero</th>
                                <th>DNI</th>
                                <th>Asiento</th>
                                <th>Teléfono</th>
                                <th>Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tickets as $ticket)
                                <tr>
                                    <td>
                                        <strong class="text-primary">{{ $ticket->numero_ticket }}</strong>
                                    </td>
                                    <td>{{ $ticket->pasajero->nombre }}</td>
                                    <td>{{ $ticket->pasajero->dni }}</td>
                                    <td>
                                        <span class="badge bg-success">{{ $ticket->asiento }}</span>
                                    </td>
                                    <td>{{ $ticket->pasajero->telefono ?? '-' }}</td>
                                    <td>{{ $ticket->pasajero->email ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Instrucciones importantes -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-warning text-dark">
                <h6 class="card-title mb-0">
                    <i class="bi bi-exclamation-triangle me-2"></i>Instrucciones Importantes
                </h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="bi bi-clock text-warning me-2"></i>
                                <strong>Llegada:</strong> Presentarse 30 minutos antes de la salida
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-card-text text-warning me-2"></i>
                                <strong>Documento:</strong> Llevar documento de identidad original
                            </li>
                            <li class="mb-0">
                                <i class="bi bi-ticket text-warning me-2"></i>
                                <strong>Ticket:</strong> Presentar el ticket impreso o digital
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <i class="bi bi-bag text-warning me-2"></i>
                                <strong>Equipaje:</strong> Máximo 20kg por pasajero
                            </li>
                            <li class="mb-2">
                                <i class="bi bi-x-circle text-warning me-2"></i>
                                <strong>Cancelaciones:</strong> Hasta 2 horas antes de la salida
                            </li>
                            <li class="mb-0">
                                <i class="bi bi-telephone text-warning me-2"></i>
                                <strong>Consultas:</strong> (01) 123-4567
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Acciones -->
        <div class="card shadow-sm">
            <div class="card-body text-center">
                <div class="row g-3">
                    <div class="col-md-3">
                        <a href="{{ route('ventas.ticket.pdf', $venta->id) }}" 
                           class="btn btn-primary btn-lg w-100" target="_blank">
                            <i class="bi bi-file-earmark-pdf me-2"></i>
                            Descargar PDF
                        </a>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-success btn-lg w-100" onclick="imprimirTickets()">
                            <i class="bi bi-printer me-2"></i>
                            Imprimir
                        </button>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-info btn-lg w-100" onclick="enviarPorEmail()">
                            <i class="bi bi-envelope me-2"></i>
                            Enviar Email
                        </button>
                    </div>
                    <div class="col-md-3">
                        <a href="{{ route('ventas.index') }}" class="btn btn-outline-primary btn-lg w-100">
                            <i class="bi bi-plus-circle me-2"></i>
                            Nueva Venta
                        </a>
                    </div>
                </div>
                
                <div class="mt-3">
                    <a href="{{ route('ventas.historial') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-clock-history me-2"></i>Ver Mis Ventas
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Pie de página de agradecimiento -->
        <div class="text-center mt-4">
            <h4 class="text-success fw-bold">
                ¡Gracias por confiar en Transporte Veloz!
            </h4>
            <p class="text-muted">Le deseamos un feliz viaje 🚍✨</p>
        </div>
    </div>
</div>

<!-- Modal para enviar email -->
<div class="modal fade" id="emailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">
                    <i class="bi bi-envelope me-2"></i>Enviar Tickets por Email
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="emailForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="email_destino" class="form-label">Email de destino:</label>
                        <input type="email" class="form-control" id="email_destino" required 
                               placeholder="destinatario@email.com">
                    </div>
                    <div class="mb-3">
                        <label for="mensaje_adicional" class="form-label">Mensaje adicional (opcional):</label>
                        <textarea class="form-control" id="mensaje_adicional" rows="3" 
                                  placeholder="Mensaje personalizado para el pasajero..."></textarea>
                    </div>
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Se enviará el PDF con todos los tickets de esta venta.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-info">
                        <i class="bi bi-send me-2"></i>Enviar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function imprimirTickets() {
    // Abrir PDF en nueva ventana para imprimir
    const ventana = window.open('{{ route("ventas.ticket.pdf", $venta->id) }}', '_blank');
    
    // Intentar imprimir automáticamente cuando se carga
    ventana.onload = function() {
        setTimeout(() => {
            ventana.print();
        }, 500);
    };
}

function enviarPorEmail() {
    // Obtener emails de los pasajeros
    const emailsPasajeros = [
        @foreach($tickets as $ticket)
            @if($ticket->pasajero->email)
                '{{ $ticket->pasajero->email }}',
            @endif
        @endforeach
    ];
    
    // Pre-llenar con el primer email encontrado
    if (emailsPasajeros.length > 0) {
        $('#email_destino').val(emailsPasajeros[0]);
    }
    
    $('#emailModal').modal('show');
}

$('#emailForm').submit(function(e) {
    e.preventDefault();
    
    const email = $('#email_destino').val();
    const mensaje = $('#mensaje_adicional').val();
    
    // Mostrar loading
    const submitBtn = $(this).find('button[type="submit"]');
    const originalText = submitBtn.html();
    submitBtn.html('<i class="bi bi-hourglass-split me-2"></i>Enviando...').prop('disabled', true);
    
    // Simular envío (aquí implementar la lógica real)
    setTimeout(() => {
        $('#emailModal').modal('hide');
        
        Swal.fire({
            icon: 'success',
            title: '¡Email enviado!',
            text: `Los tickets han sido enviados a ${email}`,
            timer: 3000,
            showConfirmButton: false
        });
        
        // Restaurar botón
        submitBtn.html(originalText).prop('disabled', false);
        
        // Limpiar formulario
        $('#emailForm')[0].reset();
    }, 2000);
});

// Auto-focus en el botón de nueva venta después de 10 segundos
setTimeout(() => {
    $('a[href="{{ route("ventas.index") }}"]').focus().addClass('btn-pulse');
}, 10000);

// Mostrar notificación de éxito
$(document).ready(function() {
    // Efecto de confeti (opcional)
    if (typeof confetti !== 'undefined') {
        confetti({
            particleCount: 100,
            spread: 70,
            origin: { y: 0.6 }
        });
    }
    
    // Notificación push si está disponible
    if ('Notification' in window && Notification.permission === 'granted') {
        new Notification('Venta procesada', {
            body: 'Venta {{ $venta->codigo_venta }} confirmada exitosamente',
            icon: '/favicon.ico'
        });
    }
});
</script>

<style>
.btn-pulse {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}
</style>
@endpush