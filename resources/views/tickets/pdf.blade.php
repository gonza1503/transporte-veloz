<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tickets - {{ $venta->codigo_venta }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
        }
        
        .ticket {
            width: 100%;
            max-width: 800px;
            border: 2px solid #007bff;
            border-radius: 10px;
            margin: 0 auto 30px auto;
            background: #fff;
            page-break-inside: avoid;
            position: relative;
            overflow: hidden;
        }
        
        .ticket::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 5px;
            background: linear-gradient(90deg, #007bff, #28a745, #ffc107, #dc3545);
        }
        
        .ticket-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            padding: 20px;
            text-align: center;
            position: relative;
        }
        
        .company-logo {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .company-subtitle {
            font-size: 14px;
            opacity: 0.9;
        }
        
        .ticket-number {
            position: absolute;
            top: 20px;
            right: 20px;
            background: rgba(255,255,255,0.2);
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 10px;
            font-weight: bold;
        }
        
        .ticket-body {
            padding: 25px;
        }
        
        .passenger-info {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            border-left: 4px solid #007bff;
        }
        
        .passenger-name {
            font-size: 16px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        
        .passenger-details {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        .detail-item {
            flex: 1;
            min-width: 120px;
        }
        
        .detail-label {
            font-weight: bold;
            color: #666;
            font-size: 10px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }
        
        .detail-value {
            font-size: 14px;
            color: #333;
        }
        
        .route-info {
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px 0;
            padding: 20px;
            background: #e9ecef;
            border-radius: 8px;
        }
        
        .route-point {
            text-align: center;
            flex: 1;
        }
        
        .route-city {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 5px;
        }
        
        .route-time {
            font-size: 12px;
            color: #666;
        }
        
        .route-arrow {
            margin: 0 20px;
            font-size: 24px;
            color: #007bff;
        }
        
        .journey-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }
        
        .detail-box {
            background: #fff;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 12px;
            text-align: center;
        }
        
        .detail-icon {
            font-size: 20px;
            color: #007bff;
            margin-bottom: 8px;
        }
        
        .seat-number {
            background: #28a745;
            color: white;
            font-size: 24px;
            font-weight: bold;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin: 15px 0;
        }
        
        .qr-section {
            text-align: center;
            margin: 20px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 8px;
        }
        
        .qr-code {
            width: 100px;
            height: 100px;
            background: #333;
            color: white;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            text-align: center;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        
        .important-notes {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            padding: 15px;
            margin-top: 20px;
        }
        
        .important-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 10px;
            font-size: 14px;
        }
        
        .important-list {
            list-style: none;
            padding: 0;
        }
        
        .important-list li {
            margin-bottom: 5px;
            padding-left: 15px;
            position: relative;
            font-size: 11px;
            color: #856404;
        }
        
        .important-list li::before {
            content: '→';
            position: absolute;
            left: 0;
            color: #ffc107;
            font-weight: bold;
        }
        
        .ticket-footer {
            text-align: center;
            padding: 15px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
            font-size: 10px;
            color: #666;
        }
        
        .price-total {
            background: #28a745;
            color: white;
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 15px 0;
        }
        
        /* Estilos para impresión */
        @media print {
            body {
                margin: 0;
                padding: 20px;
            }
            
            .ticket {
                margin-bottom: 40px;
                break-inside: avoid;
            }
        }
        
        /* Línea punteada para cortar */
        .cut-line {
            border-top: 2px dashed #ccc;
            margin: 30px 0;
            position: relative;
        }
        
        .cut-line::before {
            content: '✂ CORTAR AQUÍ';
            position: absolute;
            top: -10px;
            left: 50%;
            transform: translateX(-50%);
            background: white;
            padding: 0 10px;
            font-size: 8px;
            color: #999;
        }
    </style>
</head>
<body>
    @foreach($tickets as $index => $ticket)
        <div class="ticket">
            <!-- Header del ticket -->
            <div class="ticket-header">
                <div class="company-logo">🚌 TRANSPORTE VELOZ</div>
                <div class="company-subtitle">Sistema de Transporte Interprovincial</div>
                <div class="ticket-number">{{ $ticket->numero_ticket }}</div>
            </div>
            
            <!-- Información del pasajero -->
            <div class="ticket-body">
                <div class="passenger-info">
                    <div class="passenger-name">{{ strtoupper($ticket->pasajero->nombre) }}</div>
                    <div class="passenger-details">
                        <div class="detail-item">
                            <div class="detail-label">DNI/Documento</div>
                            <div class="detail-value">{{ $ticket->pasajero->dni }}</div>
                        </div>
                        @if($ticket->pasajero->telefono)
                        <div class="detail-item">
                            <div class="detail-label">Teléfono</div>
                            <div class="detail-value">{{ $ticket->pasajero->telefono }}</div>
                        </div>
                        @endif
                        @if($ticket->pasajero->email)
                        <div class="detail-item">
                            <div class="detail-label">Email</div>
                            <div class="detail-value">{{ strtolower($ticket->pasajero->email) }}</div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <!-- Información de la ruta -->
                <div class="route-info">
                    <div class="route-point">
                        <div class="route-city">{{ strtoupper($venta->horario->ruta->origen) }}</div>
                        <div class="route-time">Origen</div>
                    </div>
                    <div class="route-arrow">→</div>
                    <div class="route-point">
                        <div class="route-city">{{ strtoupper($venta->horario->ruta->destino) }}</div>
                        <div class="route-time">Destino</div>
                    </div>
                </div>
                
                <!-- Detalles del viaje -->
                <div class="journey-details">
                    <div class="detail-box">
                        <div class="detail-icon">📅</div>
                        <div class="detail-label">Fecha</div>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($venta->horario->fecha)->format('d/m/Y') }}</div>
                    </div>
                    
                    <div class="detail-box">
                        <div class="detail-icon">🕐</div>
                        <div class="detail-label">Hora Salida</div>
                        <div class="detail-value">{{ \Carbon\Carbon::parse($venta->horario->hora_salida)->format('H:i') }}</div>
                    </div>
                    
                    <div class="detail-box">
                        <div class="detail-icon">🚌</div>
                        <div class="detail-label">Bus</div>
                        <div class="detail-value">{{ $venta->horario->bus->placa }}</div>
                    </div>
                    
                    <div class="detail-box">
                        <div class="detail-icon">⏱</div>
                        <div class="detail-label">Duración</div>
                        <div class="detail-value">{{ $venta->horario->ruta->getDuracionFormateada() }}</div>
                    </div>
                </div>
                
                <!-- Número de asiento -->
                <div class="seat-number">
                    ASIENTO Nº {{ $ticket->asiento }}
                </div>
                
                <!-- Precio -->
                <div class="price-total">
                    PRECIO: S/ {{ number_format($venta->horario->ruta->precio, 2) }}
                </div>
                
                <!-- Código QR -->
                <div class="qr-section">
                    <div class="qr-code">
                        {{ $ticket->qr_code ?? 'QR_' . $ticket->numero_ticket }}
                    </div>
                    <div style="font-size: 10px; color: #666;">
                        Código de verificación
                    </div>
                </div>
                
                <!-- Notas importantes -->
                <div class="important-notes">
                    <div class="important-title">INSTRUCCIONES IMPORTANTES:</div>
                    <ul class="important-list">
                        <li>Presentarse 30 minutos antes de la hora de salida</li>
                        <li>Llevar documento de identidad original</li>
                        <li>El ticket es personal e intransferible</li>
                        <li>Válido solo para la fecha y horario indicado</li>
                        <li>Equipaje máximo: 20kg por pasajero</li>
                        <li>Prohibido el transporte de materiales peligrosos</li>
                    </ul>
                </div>
            </div>
            
            <!-- Footer del ticket -->
            <div class="ticket-footer">
                <div>
                    <strong>TRANSPORTE VELOZ SAC</strong> | RUC: 20123456789 | 
                    Teléfono: (01) 123-4567 | www.transporteveloz.com
                </div>
                <div style="margin-top: 5px;">
                    Venta: {{ $venta->codigo_venta }} | 
                    Vendido por: {{ $venta->user->name }} | 
                    {{ $venta->fecha_venta->format('d/m/Y H:i') }}
                </div>
                <div style="margin-top: 5px; font-size: 8px;">
                    Generado el {{ $fecha_generacion->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>
        
        @if(!$loop->last)
            <div class="cut-line"></div>
        @endif
    @endforeach
    
    <!-- Información adicional al final -->
    <div style="text-align: center; margin-top: 40px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
        <h3 style="color: #007bff; margin-bottom: 15px;">¡Gracias por elegir Transporte Veloz!</h3>
        <p style="margin-bottom: 10px;">Para consultas o reclamos:</p>
        <p style="font-weight: bold;">📞 (01) 123-4567 | 📧 info@transporteveloz.com</p>
        <p style="margin-top: 15px; font-size: 10px; color: #666;">
            Síguenos en redes sociales: @TransporteVeloz
        </p>
    </div>
</body>
</html>