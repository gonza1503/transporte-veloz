@extends('layouts.cliente')

@section('title', 'Compra tus pasajes online - Transporte Veloz')

@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold mb-4">
                    Viaja por Uruguay con <span class="text-warning">Transporte Veloz</span>
                </h1>
                <p class="lead mb-4">
                    Compra tus pasajes online de forma rápida y segura. 
                    Conectamos las principales ciudades de Uruguay con comodidad y puntualidad.
                </p>
                <a href="#buscar-pasajes" class="btn btn-warning btn-lg">
                    <i class="bi bi-search me-2"></i>Buscar Pasajes
                </a>
            </div>
            <div class="col-lg-6 text-center">
                <i class="bi bi-bus-front display-1 text-warning"></i>
            </div>
        </div>
    </div>
</section>

<!-- Indicador de pasos -->
<div class="container mt-5">
    <div class="step-indicator">
        <div class="step active">1</div>
        <div class="step pending">2</div>
        <div class="step pending">3</div>
        <div class="step pending">4</div>
    </div>
    <div class="row text-center">
        <div class="col-3">
            <small class="text-primary fw-bold">Elegir Ruta</small>
        </div>
        <div class="col-3">
            <small class="text-muted">Seleccionar Horario</small>
        </div>
        <div class="col-3">
            <small class="text-muted">Datos Pasajeros</small>
        </div>
        <div class="col-3">
            <small class="text-muted">Confirmación</small>
        </div>
    </div>
</div>

<!-- Formulario de búsqueda -->
<section id="buscar-pasajes" class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0 text-center">
                            <i class="bi bi-search me-2"></i>
                            Buscar Pasajes
                        </h4>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('cliente.buscar-horarios') }}" method="POST" id="busquedaForm">
                            @csrf
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="ruta_id" class="form-label">
                                        <i class="bi bi-geo-alt me-1"></i>Selecciona tu ruta
                                    </label>
                                    <select class="form-select form-select-lg @error('ruta_id') is-invalid @enderror" 
                                            id="ruta_id" 
                                            name="ruta_id" 
                                            required>
                                        <option value="">¿A dónde quieres viajar?</option>
                                        @foreach($rutas as $ruta)
                                            <option value="{{ $ruta->id }}" 
                                                    data-precio="{{ $ruta->precio }}"
                                                    data-duracion="{{ $ruta->getDuracionFormateada() }}"
                                                    {{ old('ruta_id') == $ruta->id ? 'selected' : '' }}>
                                                {{ $ruta->origen }} → {{ $ruta->destino }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('ruta_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                
                                <div class="col-md-6">
                                    <label for="fecha" class="form-label">
                                        <i class="bi bi-calendar me-1"></i>Fecha de viaje
                                    </label>
                                    <input type="date" 
                                           class="form-control form-control-lg @error('fecha') is-invalid @enderror" 
                                           id="fecha" 
                                           name="fecha" 
                                           value="{{ old('fecha', now()->format('Y-m-d')) }}"
                                           min="{{ now()->format('Y-m-d') }}"
                                           max="{{ now()->addMonths(2)->format('Y-m-d') }}"
                                           required>
                                    @error('fecha')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <!-- Info de la ruta seleccionada -->
                            <div id="rutaInfo" class="alert alert-info mt-3 d-none">
                                <div class="row align-items-center">
                                    <div class="col-md-8">
                                        <h6 class="mb-1">
                                            <i class="bi bi-info-circle me-1"></i>Información del viaje
                                        </h6>
                                        <div id="rutaDetalles"></div>
                                    </div>
                                    <div class="col-md-4 text-end">
                                        <div class="price-badge" id="rutaPrecio"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-grid mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-search me-2"></i>
                                    Buscar Horarios Disponibles
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Rutas populares -->
<section class="py-5 bg-white">
    <div class="container">
        <h2 class="text-center mb-5">Rutas Populares</h2>
        <div class="row">
            @foreach($rutas->take(6) as $ruta)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card route-card h-100" 
                         style="cursor: pointer;" 
                         data-ruta-id="{{ $ruta->id }}">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <h5 class="card-title text-primary">{{ $ruta->codigo }}</h5>
                                    <div class="d-flex align-items-center">
                                        <span class="me-2">{{ $ruta->origen }}</span>
                                        <i class="bi bi-arrow-right text-muted me-2"></i>
                                        <span>{{ $ruta->destino }}</span>
                                    </div>
                                </div>
                                <div class="price-badge">
                                    $ {{ number_format($ruta->precio, 2, ',', '.') }}
                                </div>
                            </div>
                            
                            <div class="row text-center">
                                <div class="col-6">
                                    <i class="bi bi-clock text-primary"></i>
                                    <small class="d-block text-muted">{{ $ruta->getDuracionFormateada() }}</small>
                                </div>
                                <div class="col-6">
                                    <i class="bi bi-geo-alt text-primary"></i>
                                    <small class="d-block text-muted">Directo</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>

<!-- Ventajas -->
<section class="py-5" style="background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);">
    <div class="container">
        <h2 class="text-center mb-5">¿Por qué elegir Transporte Veloz?</h2>
        <div class="row">
            <div class="col-md-4 text-center mb-4">
                <div class="card border-0 bg-transparent">
                    <div class="card-body">
                        <i class="bi bi-shield-check text-primary display-4 mb-3"></i>
                        <h5>Seguridad Garantizada</h5>
                        <p class="text-muted">Viaja con total tranquilidad. Nuestros buses cuentan con los más altos estándares de seguridad.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center mb-4">
                <div class="card border-0 bg-transparent">
                    <div class="card-body">
                        <i class="bi bi-clock text-primary display-4 mb-3"></i>
                        <h5>Puntualidad</h5>
                        <p class="text-muted">Respetamos tu tiempo. Salidas puntuales y llegadas en horario para que planifiques tu día.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 text-center mb-4">
                <div class="card border-0 bg-transparent">
                    <div class="card-body">
                        <i class="bi bi-wifi text-primary display-4 mb-3"></i>
                        <h5>Comodidad Total</h5>
                        <p class="text-muted">WiFi gratuito, asientos reclinables y aire acondicionado para un viaje placentero.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Información adicional -->
<section id="contacto" class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <h3 class="mb-4">Información importante</h3>
                <div class="list-group list-group-flush">
                    <div class="list-group-item border-0 px-0">
                        <i class="bi bi-clock-history text-primary me-2"></i>
                        <strong>Llegada temprana:</strong> Te recomendamos llegar 30 minutos antes de la salida
                    </div>
                    <div class="list-group-item border-0 px-0">
                        <i class="bi bi-person-vcard text-primary me-2"></i>
                        <strong>Documentación:</strong> Documento de identidad obligatorio para viajar
                    </div>
                    <div class="list-group-item border-0 px-0">
                        <i class="bi bi-luggage text-primary me-2"></i>
                        <strong>Equipaje:</strong> Hasta 20kg sin costo adicional
                    </div>
                    <div class="list-group-item border-0 px-0">
                        <i class="bi bi-ticket-perforated text-primary me-2"></i>
                        <strong>Cancelaciones:</strong> Hasta 2 horas antes del viaje
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <h3 class="mb-4">¿Necesitas ayuda?</h3>
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Centro de Atención</h5>
                        <p class="card-text">Nuestro equipo está disponible para ayudarte con cualquier consulta.</p>
                        <div class="d-grid gap-2">
                            <a href="tel:021234567" class="btn btn-outline-primary">
                                <i class="bi bi-telephone me-2"></i>(02) 123-4567
                            </a>
                            <a href="mailto:info@transporteveloz.com.uy" class="btn btn-outline-primary">
                                <i class="bi bi-envelope me-2"></i>info@transporteveloz.com.uy
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Mostrar información de la ruta cuando se selecciona
    $('#ruta_id').change(function() {
        const rutaSelect = $(this);
        const selectedOption = rutaSelect.find('option:selected');
        
        if (rutaSelect.val()) {
            const precio = selectedOption.data('precio');
            const duracion = selectedOption.data('duracion');
            const rutaTexto = selectedOption.text();
            
            $('#rutaDetalles').html(`
                <div><strong>Ruta:</strong> ${rutaTexto}</div>
                <div><strong>Duración aproximada:</strong> ${duracion}</div>
                <div><strong>Tipo:</strong> Servicio directo</div>
            `);
            
            $('#rutaPrecio').html(formatCurrency(precio));
            $('#rutaInfo').removeClass('d-none').addClass('fade-in');
        } else {
            $('#rutaInfo').addClass('d-none');
        }
    });
    
    // Seleccionar ruta al hacer clic en las tarjetas
    $('.route-card').click(function() {
        const rutaId = $(this).data('ruta-id');
        $('#ruta_id').val(rutaId).trigger('change');
        
        // Scroll suave al formulario
        $('html, body').animate({
            scrollTop: $('#buscar-pasajes').offset().top - 100
        }, 500);
        
        // Resaltar tarjeta seleccionada
        $('.route-card').removeClass('border-primary');
        $(this).addClass('border-primary');
    });
    
    // Validación del formulario
    $('#busquedaForm').submit(function(e) {
        const rutaId = $('#ruta_id').val();
        const fecha = $('#fecha').val();
        
        if (!rutaId) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Selecciona una ruta',
                text: 'Por favor elige tu destino para continuar',
                confirmButtonText: 'Entendido'
            });
            return false;
        }
        
        if (!fecha) {
            e.preventDefault();
            Swal.fire({
                icon: 'warning',
                title: 'Selecciona la fecha',
                text: 'Por favor elige cuándo quieres viajar',
                confirmButtonText: 'Entendido'
            });
            return false;
        }
        
        // Mostrar loading
        const submitBtn = $(this).find('button[type="submit"]');
        submitBtn.html('<i class="bi bi-hourglass-split me-2"></i>Buscando horarios...');
        submitBtn.prop('disabled', true);
    });
    
    // Animaciones al hacer scroll
    $(window).scroll(function() {
        $('.card').each(function() {
            const elementTop = $(this).offset().top;
            const elementBottom = elementTop + $(this).outerHeight();
            const viewportTop = $(window).scrollTop();
            const viewportBottom = viewportTop + $(window).height();
            
            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                $(this).addClass('animate__animated animate__fadeInUp');
            }
        });
    });
});
</script>
@endpush