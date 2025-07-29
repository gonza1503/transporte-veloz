<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Transporte Veloz')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.0/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- SweetAlert2 para alertas bonitas -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.32/sweetalert2.min.css" rel="stylesheet">
    
    <style>
        /* Estilos personalizados */
        .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }
        
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        
        .sidebar .nav-link {
            color: rgba(255,255,255,0.8);
            padding: 0.75rem 1rem;
            margin: 0.2rem 0;
            border-radius: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255,255,255,0.1);
            transform: translateX(5px);
        }
        
        .card {
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
            border: none;
            border-radius: 0.75rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-1px);
        }
        
        .seat {
            width: 40px;
            height: 40px;
            margin: 2px;
            border: 2px solid #ddd;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 12px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .seat.available {
            background-color: #e8f5e8;
            border-color: #28a745;
            color: #28a745;
        }
        
        .seat.available:hover {
            background-color: #28a745;
            color: white;
            transform: scale(1.1);
        }
        
        .seat.occupied {
            background-color: #f8d7da;
            border-color: #dc3545;
            color: #dc3545;
            cursor: not-allowed;
        }
        
        .seat.selected {
            background-color: #007bff;
            border-color: #007bff;
            color: white;
            transform: scale(1.1);
        }
        
        .bus-layout {
            background: #f8f9fa;
            border-radius: 15px;
            padding: 20px;
            margin: 20px 0;
        }
        
        .driver-seat {
            background: #6c757d;
            color: white;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }
        
        .alert {
            border-radius: 0.75rem;
            border: none;
        }
        
        .table {
            border-radius: 0.5rem;
            overflow: hidden;
        }
        
        .badge {
            font-size: 0.75em;
        }
        
        /* Animaciones */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                min-height: auto;
            }
            
            .seat {
                width: 35px;
                height: 35px;
                font-size: 10px;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand" href="{{ auth()->user()->isAdmin() ? route('admin.dashboard') : route('ventas.index') }}">
                <i class="bi bi-bus-front me-2"></i>
                Transporte Veloz
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-person-circle me-1"></i>
                            {{ auth()->user()->name }}
                            <span class="badge bg-secondary ms-1">{{ ucfirst(auth()->user()->role) }}</span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Mi Perfil</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger">
                                        <i class="bi bi-box-arrow-right me-2"></i>Cerrar Sesión
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-0">
                <div class="p-3">
                    <h6 class="text-white-50 text-uppercase small mb-3">Navegación</h6>
                    <nav class="nav flex-column">
                        @if(auth()->user()->isAdmin())
                            <!-- Menú para Administradores -->
                            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                               href="{{ route('admin.dashboard') }}">
                                <i class="bi bi-speedometer2 me-2"></i>Dashboard
                            </a>
                            
                            <a class="nav-link {{ request()->routeIs('admin.empleados*') ? 'active' : '' }}" 
                               href="{{ route('admin.empleados') }}">
                                <i class="bi bi-people me-2"></i>Empleados
                            </a>
                            
                            <a class="nav-link {{ request()->routeIs('admin.rutas*') ? 'active' : '' }}" 
                               href="{{ route('admin.rutas.index') }}">
                                <i class="bi bi-map me-2"></i>Rutas
                            </a>
                            
                            <a class="nav-link {{ request()->routeIs('admin.buses*') ? 'active' : '' }}" 
                               href="{{ route('admin.buses.index') }}">
                                <i class="bi bi-bus-front me-2"></i>Buses
                            </a>
                            
                            <a class="nav-link {{ request()->routeIs('admin.horarios*') ? 'active' : '' }}" 
                               href="{{ route('admin.horarios.index') }}">
                                <i class="bi bi-clock me-2"></i>Horarios
                            </a>
                            
                            <a class="nav-link {{ request()->routeIs('admin.reportes*') ? 'active' : '' }}" 
                               href="{{ route('admin.reportes') }}">
                                <i class="bi bi-graph-up me-2"></i>Reportes
                            </a>
                            
                            <hr class="border-light">
                            
                            <a class="nav-link {{ request()->routeIs('ventas*') ? 'active' : '' }}" 
                               href="{{ route('ventas.index') }}">
                                <i class="bi bi-cart-plus me-2"></i>Vender Pasajes
                            </a>
                        @else
                            <!-- Menú para Empleados -->
                            <a class="nav-link {{ request()->routeIs('ventas.index') ? 'active' : '' }}" 
                               href="{{ route('ventas.index') }}">
                                <i class="bi bi-cart-plus me-2"></i>Nueva Venta
                            </a>
                            
                            <a class="nav-link {{ request()->routeIs('ventas.historial') ? 'active' : '' }}" 
                               href="{{ route('ventas.historial') }}">
                                <i class="bi bi-clock-history me-2"></i>Mis Ventas
                            </a>
                            
                            <a class="nav-link {{ request()->routeIs('rutas.index') ? 'active' : '' }}" 
                               href="{{ route('rutas.index') }}">
                                <i class="bi bi-map me-2"></i>Ver Rutas
                            </a>
                            
                            <a class="nav-link {{ request()->routeIs('buses.index') ? 'active' : '' }}" 
                               href="{{ route('buses.index') }}">
                                <i class="bi bi-bus-front me-2"></i>Ver Buses
                            </a>
                        @endif
                    </nav>
                </div>
            </div>

            <!-- Contenido Principal -->
            <div class="col-md-9 col-lg-10 ms-sm-auto px-4 py-3">
                <!-- Breadcrumb -->
                @hasSection('breadcrumb')
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb">
                            @yield('breadcrumb')
                        </ol>
                    </nav>
                @endif

                <!-- Alertas -->
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="bi bi-check-circle me-2"></i>
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
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

                <!-- Contenido de la página -->
                <main class="fade-in">
                    @yield('content')
                </main>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <!-- Bootstrap 5 JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/limonte-sweetalert2/11.7.32/sweetalert2.min.js"></script>
    <!-- jQuery (para facilitar AJAX) -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    
    <script>
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
        
        // Confirmación para eliminar elementos
        $('.btn-delete').on('click', function(e) {
            e.preventDefault();
            const form = $(this).closest('form');
            
            Swal.fire({
                title: '¿Estás seguro?',
                text: "Esta acción no se puede deshacer",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Sí, eliminar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        });
    </script>
    
    @stack('scripts')
</body>
</html>