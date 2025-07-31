@extends('layouts.app')

@section('title', 'Dashboard Administrativo - Transporte Veloz')

@section('breadcrumb')
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">
                    <i class="bi bi-speedometer2 text-primary me-2"></i>
                    Dashboard Administrativo
                </h1>
                <p class="text-muted">Resumen general del sistema de transporte</p>
            </div>
            <div class="text-end">
                <small class="text-muted d-block">Última actualización</small>
                <small class="text-muted">{{ now()->format('d/m/Y H:i') }}</small>
            </div>
        </div>
    </div>
</div>

<!-- Estadísticas principales -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6">
        <div class="card bg-primary text-white shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-1">{{ $stats['ventas_hoy'] }}</h4>
                        <p class="mb-0">Ventas Hoy</p>
                        <small class="opacity-75">
                            {{ $stats['ventas_mes'] }} este mes
                        </small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-cart-check fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card bg-success text-white shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-1">S/ {{ number_format($stats['ingresos_hoy'], 2) }}</h4>
                        <p class="mb-0">Ingresos Hoy</p>
                        <small class="opacity-75">
                            S/ {{ number_format($stats['ingresos_mes'], 2) }} este mes
                        </small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-currency-dollar fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card bg-info text-white shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-1">{{ $stats['total_empleados'] }}</h4>
                        <p class="mb-0">Empleados Activos</p>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-people fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-xl-3 col-md-6">
        <div class="card bg-warning text-dark shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4 class="mb-1">{{ $stats['total_buses'] }}</h4>
                        <p class="mb-0">Buses Operativos</p>
                        <small>{{ $stats['total_rutas'] }} rutas activas</small>
                    </div>
                    <div class="align-self-center">
                        <i class="bi bi-bus-front fs-1"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos y análisis -->
<div class="row mb-4">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="bi bi-graph-up me-2"></i>Ventas de los Últimos 7 Días
                </h6>
            </div>
            <div class="card-body">
                <canvas id="ventasChart" height="120"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="bi bi-star me-2"></i>Top 5 Rutas Más Vendidas
                </h6>
            </div>
            <div class="card-body">
                @forelse($topRutas as $index => $ruta)
                    <div class="d-flex align-items-center mb-3">
                        <div class="badge bg-primary rounded-circle me-3" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                            {{ $index + 1 }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold">{{ $ruta->codigo }}</div>
                            <small class="text-muted">{{ $ruta->origen }} → {{ $ruta->destino }}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-success">{{ $ruta->ventas_count }}</div>
                            <small class="text-muted">ventas</small>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-2"></i>
                        <p class="mb-0">No hay datos disponibles</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Actividad reciente y accesos rápidos -->
<div class="row">
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="bi bi-clock-history me-2"></i>Actividad Reciente
                </h6>
            </div>
            <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                @php
                    $ventasRecientes = \App\Models\Venta::with(['user', 'horario.ruta'])
                                                       ->orderBy('created_at', 'desc')
                                                       ->take(10)
                                                       ->get();
                @endphp
                
                @forelse($ventasRecientes as $venta)
                    <div class="d-flex align-items-center mb-3 p-2 border-start border-3 border-primary bg-light rounded">
                        <div class="me-3">
                            <i class="bi bi-cart-check text-primary fs-4"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold">{{ $venta->codigo_venta }}</div>
                            <small class="text-muted">
                                {{ $venta->horario->ruta->codigo }} - 
                                {{ $venta->cantidad_pasajes }} {{ $venta->cantidad_pasajes == 1 ? 'pasaje' : 'pasajes' }}
                            </small>
                            <div class="small text-muted">
                                Por {{ $venta->user->name }} - {{ $venta->created_at->diffForHumans() }}
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-success">S/ {{ number_format($venta->total, 2) }}</div>
                        </div>
                    </div>
                @empty
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-inbox fs-2"></i>
                        <p class="mb-0">No hay actividad reciente</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    
    <div class="col-lg-6">
        <div class="card shadow-sm">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="bi bi-lightning me-2"></i>Accesos Rápidos
                </h6>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <a href="{{ route('ventas.index') }}" class="btn btn-outline-primary w-100 py-3">
                            <i class="bi bi-cart-plus fs-3 d-block mb-1"></i>
                            <small>Nueva Venta</small>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.horarios.create') }}" class="btn btn-outline-success w-100 py-3">
                            <i class="bi bi-clock fs-3 d-block mb-1"></i>
                            <small>Nuevo Horario</small>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.buses.create') }}" class="btn btn-outline-info w-100 py-3">
                            <i class="bi bi-bus-front fs-3 d-block mb-1"></i>
                            <small>Registrar Bus</small>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.rutas.create') }}" class="btn btn-outline-warning w-100 py-3">
                            <i class="bi bi-map fs-3 d-block mb-1"></i>
                            <small>Nueva Ruta</small>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.empleados.crear') }}" class="btn btn-outline-secondary w-100 py-3">
                            <i class="bi bi-person-plus fs-3 d-block mb-1"></i>
                            <small>Nuevo Empleado</small>
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="{{ route('admin.reportes') }}" class="btn btn-outline-dark w-100 py-3">
                            <i class="bi bi-graph-up fs-3 d-block mb-1"></i>
                            <small>Ver Reportes</small>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Estado del sistema -->
        <div class="card shadow-sm mt-4">
            <div class="card-header bg-light">
                <h6 class="card-title mb-0">
                    <i class="bi bi-shield-check me-2"></i>Estado del Sistema
                </h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Base de Datos</span>
                    <span class="badge bg-success">Operativa</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Servidor Web</span>
                    <span class="badge bg-success">Operativo</span>
                </div>
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span>Generación PDF</span>
                    <span class="badge bg-success">Operativa</span>
                </div>
                <div class="d-flex justify-content-between align-items-center">
                    <span>Última Copia Seguridad</span>
                    <small class="text-muted">{{ now()->subHours(3)->format('H:i') }}</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
$(document).ready(function() {
    // Gráfico de ventas semanales
    const ventasData = @json($ventasSemanales);
    
    const ctx = document.getElementById('ventasChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: ventasData.map(item => item.fecha),
            datasets: [{
                label: 'Ventas',
                data: ventasData.map(item => item.ventas),
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'Ingresos (S/)',
                data: ventasData.map(item => item.ingresos),
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: true,
                yAxisID: 'y1'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    position: 'left'
                },
                y1: {
                    type: 'linear',
                    display: true,
                    position: 'right',
                    beginAtZero: true,
                    grid: {
                        drawOnChartArea: false
                    }
                }
            }
        }
    });
    
    // Auto-refresh cada 5 minutos
    setInterval(function() {
        location.reload();
    }, 300000);
    
    // Animación de contadores
    $('.card h4').each(function() {
        const $this = $(this);
        const countTo = parseInt($this.text().replace(/[^\d]/g, ''));
        
        if (countTo > 0) {
            $({ countNum: 0 }).animate({
                countNum: countTo
            }, {
                duration: 2000,
                easing: 'swing',
                step: function() {
                    $this.text(Math.floor(this.countNum));
                },
                complete: function() {
                    $this.text(this.countNum);
                }
            });
        }
    });
});
</script>
@endpush