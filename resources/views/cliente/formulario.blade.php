@extends('layouts.cliente')
@section('title', 'Datos de Pasajeros - Transporte Veloz')
@section('content')
<!-- Indicador de pasos -->
<div class="container mt-4">
    <div class="step-indicator">
        <div class="step completed">1</div>
        <div class="step completed">2</div>
        <div class="step active">3</div>
        <div class="step pending">4</div>
    </div>
    <div class="row text-center">
        <div class="col-3">
            <small class="text-success fw-bold">Ruta Elegida</small>
        </div>
        <div class="col-3">
            <small class="text-success fw-bold">Horario Seleccionado</small>
        </div>
        <div class="col-3">
            <small class="text-primary fw-bold">Datos Pasajeros</small>
        </div>
        <div class="col-3">
            <small class="text-muted">Confirmación</small>
        </div>
    </div>
</div>

<!-- Resumen del viaje -->
<section class="py-4" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
    <div class="container">
        <div class="card shadow-lg border-0">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="text-success mb-3">Resumen de tu viaje</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Ruta:</strong> {{ $horario->ruta->codigo }}</p>
                                <p class="mb-1"><strong>Origen:</strong> {{ $horario->ruta->origen }}</p>
                                <p class="mb-1"><strong>Destino:</strong> {{ $horario->ruta->destino }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-1"><strong>Fecha:</strong> {{ \Carbon\Carbon::parse($horario->fecha)->format('d/m/Y') }}</p>
                                <p class="mb-1"><strong>Horario:</strong> {{ \Carbon\Carbon::parse($horario->hora_salida)->format('H:i') }}</p>
                                <p class="mb-1"><strong>Bus:</strong> {{ $horario->bus->placa }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <div class="price-badge">
                            $ {{ number_format($horario->ruta->precio * $cantidad, 2, ',', '.') }}
                        </div>
                        <small class="text-muted d-block">{{ $cantidad }} {{ $cantidad == 1 ? 'pasaje' : 'pasajes' }}</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Formulario de pasajeros -->
<section class="py-5">
    <div class="container">
        <form action="{{ route('cliente.procesar-compra') }}" method="POST" id="pasajerosForm">
            @csrf
            <input type="hidden" name="horario_id" value="{{ $horario->id }}">
            <input type="hidden" name="cantidad" value="{{ $cantidad }}">
            
            <div class="row">
                <div class="col-lg-8">
                    <h4 class="mb-4">
                        <i class="bi bi-people me-2"></i>Datos de los Pasajeros
                    </h4>
                    
                    @for($i = 0; $i < $cantidad; $i++)
                        <div class="card shadow-sm mb-4">
                            <div class="card-header bg-primary text-white">
                                <h6 class="mb-0">
                                    <i class="bi bi-person me-2"></i>
                                    Pasajero {{ $i + 1 }}
                                    <small class="opacity-75 ms-2">({{ $i == 0 ? 'Titular' : 'Acompañante' }})</small>
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="nombres_{{ $i }}" class="form-label">
                                                <i class="bi bi-person-badge me-1"></i>Nombres *
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('nombres.' . $i) is-invalid @enderror" 
                                                   id="nombres_{{ $i }}" 
                                                   name="nombres[{{ $i }}]" 
                                                   value="{{ old('nombres.' . $i) }}"
                                                   placeholder="Ingrese los nombres"
                                                   required>
                                            @error('nombres.' . $i)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="apellidos_{{ $i }}" class="form-label">
                                                <i class="bi bi-person-badge me-1"></i>Apellidos *
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('apellidos.' . $i) is-invalid @enderror" 
                                                   id="apellidos_{{ $i }}" 
                                                   name="apellidos[{{ $i }}]" 
                                                   value="{{ old('apellidos.' . $i) }}"
                                                   placeholder="Ingrese los apellidos"
                                                   required>
                                            @error('apellidos.' . $i)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="documento_{{ $i }}" class="form-label">
                                                <i class="bi bi-card-text me-1"></i>Número de Documento *
                                            </label>
                                            <input type="text" 
                                                   class="form-control @error('documento.' . $i) is-invalid @enderror" 
                                                   id="documento_{{ $i }}" 
                                                   name="documento[{{ $i }}]" 
                                                   value="{{ old('documento.' . $i) }}"
                                                   placeholder="Cédula o pasaporte"
                                                   required>
                                            @error('documento.' . $i)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="telefono_{{ $i }}" class="form-label">
                                                <i class="bi bi-telephone me-1"></i>Teléfono {{ $i == 0 ? '*' : '' }}
                                            </label>
                                            <input type="tel" 
                                                   class="form-control @error('telefono.' . $i) is-invalid @enderror" 
                                                   id="telefono_{{ $i }}" 
                                                   name="telefono[{{ $i }}]" 
                                                   value="{{ old('telefono.' . $i) }}"
                                                   placeholder="Número de contacto"
                                                   {{ $i == 0 ? 'required' : '' }}>
                                            @error('telefono.' . $i)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="fecha_nacimiento_{{ $i }}" class="form-label">
                                                <i class="bi bi-calendar me-1"></i>Fecha de Nacimiento *
                                            </label>
                                            <input type="date" 
                                                   class="form-control @error('fecha_nacimiento.' . $i) is-invalid @enderror" 
                                                   id="fecha_nacimiento_{{ $i }}" 
                                                   name="fecha_nacimiento[{{ $i }}]" 
                                                   value="{{ old('fecha_nacimiento.' . $i) }}"
                                                   max="{{ date('Y-m-d') }}"
                                                   required>
                                            @error('fecha_nacimiento.' . $i)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="genero_{{ $i }}" class="form-label">
                                                <i class="bi bi-gender-ambiguous me-1"></i>Género *
                                            </label>
                                            <select class="form-select @error('genero.' . $i) is-invalid @enderror" 
                                                    id="genero_{{ $i }}" 
                                                    name="genero[{{ $i }}]" 
                                                    required>
                                                <option value="">Seleccione...</option>
                                                <option value="M" {{ old('genero.' . $i) == 'M' ? 'selected' : '' }}>Masculino</option>
                                                <option value="F" {{ old('genero.' . $i) == 'F' ? 'selected' : '' }}>Femenino</option>
                                                <option value="O" {{ old('genero.' . $i) == 'O' ? 'selected' : '' }}>Otro</option>
                                            </select>
                                            @error('genero.' . $i)
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                @if($i == 0)
                                <div class="row">
                                    <div class="col-12">
                                        <div class="mb-3">
                                            <label for="email_{{ $i }}" class="form-label">
                                                <i class="bi bi-envelope me-1"></i>Correo Electrónico *
                                            </label>
                                            <input type="email" 
                                                   class="form-control @error('email') is-invalid @enderror" 
                                                   id="email_{{ $i }}" 
                                                   name="email" 
                                                   value="{{ old('email') }}"
                                                   placeholder="correo@ejemplo.com"
                                                   required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <div class="form-text">
                                                <i class="bi bi-info-circle me-1"></i>
                                                Se enviará la confirmación y boletos a este correo
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    @endfor

                    <!-- Términos y condiciones -->
                    <div class="card border-warning shadow-sm mb-4">
                        <div class="card-body">
                            <div class="form-check">
                                <input class="form-check-input @error('terminos') is-invalid @enderror" 
                                       type="checkbox" 
                                       id="terminos" 
                                       name="terminos" 
                                       value="1" 
                                       {{ old('terminos') ? 'checked' : '' }} 
                                       required>
                                <label class="form-check-label" for="terminos">
                                    <i class="bi bi-shield-check me-1"></i>
                                    Acepto los <a href="#" class="text-decoration-none" data-bs-toggle="modal" data-bs-target="#terminosModal">términos y condiciones</a> del servicio
                                </label>
                                @error('terminos')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar con resumen -->
                <div class="col-lg-4">
                    <div class="sticky-top" style="top: 2rem;">
                        <div class="card shadow-lg border-0">
                            <div class="card-header bg-success text-white">
                                <h6 class="mb-0">
                                    <i class="bi bi-receipt me-2"></i>Resumen de Compra
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Precio por pasaje:</span>
                                    <span>${{ number_format($horario->ruta->precio, 2, ',', '.') }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>Cantidad de pasajes:</span>
                                    <span>{{ $cantidad }}</span>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between mb-3 fs-5 fw-bold text-success">
                                    <span>Total a pagar:</span>
                                    <span>${{ number_format($horario->ruta->precio * $cantidad, 2, ',', '.') }}</span>
                                </div>
                                
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle me-2"></i>
                                    <small>
                                        <strong>Importante:</strong> Verifica que todos los datos estén correctos antes de continuar.
                                    </small>
                                </div>

                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    <i class="bi bi-credit-card me-2"></i>
                                    Proceder al Pago
                                </button>

                                <div class="text-center mt-3">
                                    <a href="{{ route('cliente.seleccionar-horario', $horario->ruta->id) }}" 
                                       class="btn btn-outline-secondary btn-sm">
                                        <i class="bi bi-arrow-left me-1"></i>Volver a Horarios
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<!-- Modal de Términos y Condiciones -->
<div class="modal fade" id="terminosModal" tabindex="-1" aria-labelledby="terminosModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="terminosModalLabel">
                    <i class="bi bi-file-text me-2"></i>Términos y Condiciones
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <h6>1. Condiciones Generales</h6>
                <p>Al adquirir un boleto de Transporte Veloz, usted acepta las siguientes condiciones de viaje...</p>
                
                <h6>2. Políticas de Cancelación</h6>
                <p>Las cancelaciones deben realizarse con al menos 2 horas de anticipación...</p>
                
                <h6>3. Equipaje</h6>
                <p>Cada pasajero tiene derecho a transportar equipaje de mano y una maleta...</p>
                
                <h6>4. Responsabilidades</h6>
                <p>La empresa no se hace responsable por retrasos debido a condiciones climáticas...</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<style>
.step-indicator {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-bottom: 2rem;
}

.step {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 10px;
    font-weight: bold;
    position: relative;
}

.step.completed {
    background-color: #28a745;
    color: white;
}

.step.active {
    background-color: #007bff;
    color: white;
}

.step.pending {
    background-color: #e9ecef;
    color: #6c757d;
}

.step:not(:last-child):after {
    content: '';
    position: absolute;
    top: 50%;
    left: 100%;
    width: 20px;
    height: 2px;
    background-color: #dee2e6;
    transform: translateY(-50%);
}

.step.completed:not(:last-child):after {
    background-color: #28a745;
}

.price-badge {
    background: linear-gradient(135deg, #28a745, #20c997);
    color: white;
    padding: 15px 25px;
    border-radius: 15px;
    font-size: 1.5rem;
    font-weight: bold;
    text-align: center;
    box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
}

.card {
    transition: transform 0.2s;
}

.card:hover {
    transform: translateY(-2px);
}

.form-control:focus, .form-select:focus {
    border-color: #28a745;
    box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
}

.sticky-top {
    z-index: 1020;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validación en tiempo real
    const form = document.getElementById('pasajerosForm');
    const inputs = form.querySelectorAll('input[required], select[required]');
    
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
    });
    
    function validateField(field) {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
        } else {
            field.classList.remove('is-invalid');
            field.classList.add('is-valid');
        }
    }
    
    // Validación del formulario antes del envío
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                input.classList.add('is-invalid');
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Por favor, complete todos los campos obligatorios.');
        }
    });
    
    // Auto-formato para números de documento
    const documentInputs = form.querySelectorAll('input[name^="documento"]');
    documentInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    });
    
    // Auto-formato para teléfonos
    const phoneInputs = form.querySelectorAll('input[name^="telefono"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9+\-\s]/g, '');
        });
    });
});
</script>

@endsection