<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Transporte Veloz - Compra tus pasajes online')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- SweetAlert2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.32/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        /* Estilos personalizados para clientes */
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 4rem 0;
        }
        
        .navbar-brand {
            font-weight: bold;
            font-size: 1.8rem;
        }
        
        .card {
            box-shadow: 0 0.125rem 0.5rem rgba(0,0,0,0.1);
            border: none;
            border-radius: 1rem;
            transition: transform 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 0.75rem;
            padding: 0.75rem 2rem;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-2px);
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            margin: 2rem 0;
        }
        
        .step {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 1rem;
            font-weight: bold;
            position: relative;
        }
        
        .step.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        
        .step.completed {
            background: #28a745;
            color: white;
        }
        
        .step.pending {
            background: #e9ecef;
            color: #6c757d;
        }
        
        .step::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 100%;
            width: 4rem;
            height: 2px;
            background: #e9ecef;
            transform: translateY(-50%);
            z-index: -1;
        }
        
        .step:last-child::after {
            display: none;
        }
        
        .step.completed::after {
            background: #28a745;
        }
        
        .route-card {
            border-left: 4px solid #667eea;
            transition: all 0.3s ease;
        }
        
        .route-card:hover {
            border-left-color: #764ba2;
            box-shadow: 0 0.25rem 1rem rgba(0,0,0,0.15);
        }
        
        .price-badge {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
            padding: 0.5rem 1rem;
            border-radius: 2rem;
        }
        
        .footer {
            background: #2c3e50;
            color: white;
            padding: 2rem 0;
            margin-top: 4rem;
        }
        
        @media (max-width: 768px) {
            .hero-section {
                padding: 2rem 0;
            }
            
            .step-indicator {
                flex-wrap: wrap;
                gap: 1rem;
            }
            
            .step::after {
                display: none;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand text-primary" href="{{ route('cliente.index') }}">
                <i class="bi bi-bus-front me-2"></i>
                Transporte Veloz
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('cliente.index') }}">
                            <i class="bi bi-house me-1"></i>Inicio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#horarios">
                            <i class="bi bi-clock me-1"></i>Horarios
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contacto">
                            <i class="bi bi-envelope me-1"></i>Contacto
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">
                            <i class="bi bi-person-circle me-1"></i>Empleados
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <main>
        <!-- Alertas -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                <i class="bi bi-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>
                <strong>Por favor corrige los siguientes errores:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <h5>Transporte Veloz</h5>
                    <p class="text-light">Tu compañía de confianza para viajar por todo Uruguay con comodidad y seguridad.</p>
                </div>
                <div class="col-md-4">
                    <h5>Contacto</h5>
                    <p class="text-light mb-1">
                        <i class="bi bi-telephone me-2"></i>(02) 123-4567
                    </p>
                    <p class="text-light mb-1">
                        <i class="bi bi-envelope me-2"></i>info@transporteveloz.com.uy
                    </p>
                    <p class="text-light">
                        <i class="bi bi-geo-alt me-2"></i>Montevideo, Uruguay
                    </p>
                </div>
                <div class="col-md-4">
                    <h5>Síguenos</h5>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-light"><i class="bi bi-facebook fs-4"></i></a>
                        <a href="#" class="text-light"><i class="bi bi-instagram fs-4"></i></a>
                        <a href="#" class="text-light"><i class="bi bi-twitter fs-4"></i></a>
                    </div>
                </div>
            </div>
            <hr class="border-light">
            <div class="text-center">
                <p class="text-light mb-0">&copy; {{ date('Y') }} Transporte Veloz. Todos los derechos reservados.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.32/sweetalert2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    
    <script>
        // Helper global para formatear precios
        window.formatCurrency = function(amount) {
            return '$ ' + parseFloat(amount).toLocaleString('es-UY', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });
        };

        // Configurar CSRF token para AJAX
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Auto-ocultar alertas después de 5 segundos
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>
    
    @stack('scripts')
</body>
</html>