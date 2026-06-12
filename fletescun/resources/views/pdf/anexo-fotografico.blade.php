<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Anexo Fotografico - {{ $cotizacion->folio }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; line-height: 1.5; color: #222; }
        .container { max-width: 210mm; margin: 0 auto; padding: 20px; }
        .header { text-align: center; padding: 20px; border: 1px solid #ddd; }
        .header h1 { font-size: 22px; margin-bottom: 8px; }
        .header .folio { font-weight: bold; margin-top: 6px; }
        .section { margin-top: 18px; }
        .section h2 { font-size: 14px; border-bottom: 1px solid #ddd; padding-bottom: 6px; margin-bottom: 10px; }
        .info-grid { width: 100%; border-collapse: collapse; }
        .info-grid td { padding: 4px 6px; vertical-align: top; }
        .info-label { width: 140px; font-weight: bold; }
        .gallery { width: 100%; border-collapse: separate; border-spacing: 10px; }
        .gallery td { width: 33.33%; border: 1px solid #ddd; padding: 6px; text-align: center; }
        .gallery img { width: 100%; max-height: 200px; object-fit: contain; }
        .gallery .label { font-size: 11px; color: #666; margin-top: 4px; }
        .empty { color: #888; text-align: center; padding: 10px; }
        .table { width: 100%; border-collapse: collapse; }
        .table th, .table td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        .table th { background: #f3f3f3; }
        .prices { width: 100%; border-collapse: collapse; }
        .prices td { padding: 6px; border-bottom: 1px solid #eee; }
        .prices .label { width: 70%; }
        .prices .value { width: 30%; text-align: right; font-weight: bold; }
        .footer { margin-top: 20px; font-size: 11px; color: #666; text-align: center; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>ANEXO FOTOGRAFICO</h1>
            <div>FletesCun - Mudanza</div>
            <div class="folio">Folio: {{ $cotizacion->folio }}</div>
            <div>Generado: {{ $fecha_actual }}</div>
        </div>

        <div class="section">
            <h2>Datos del cliente</h2>
            <table class="info-grid">
                <tr>
                    <td class="info-label">Nombre:</td>
                    <td>{{ $cliente->nombre }}</td>
                </tr>
                <tr>
                    <td class="info-label">Telefono:</td>
                    <td>{{ $cliente->telefono }}</td>
                </tr>
                <tr>
                    <td class="info-label">Correo:</td>
                    <td>{{ $cliente->correo }}</td>
                </tr>
                <tr>
                    <td class="info-label">Origen:</td>
                    <td>{{ $cotizacion->direccion_origen }}</td>
                </tr>
                <tr>
                    <td class="info-label">Destino:</td>
                    <td>{{ $cotizacion->direccion_destino }}</td>
                </tr>
                <tr>
                    <td class="info-label">Modalidad:</td>
                    <td>{{ $cotizacion->tipo_servicio }}</td>
                </tr>
            </table>
        </div>

        <div class="section">
            <h2>Fotografias</h2>
            @if (empty($imagenes))
                <div class="empty">No hay fotografias adjuntas.</div>
            @else
                <table class="gallery">
                    <tr>
                    @foreach ($imagenes as $index => $foto)
                        <td>
                            <img src="{{ $foto['data_uri'] }}" alt="{{ $foto['label'] }}">
                            <div class="label">{{ $foto['label'] }}</div>
                        </td>
                        @if (($index + 1) % 3 === 0)
                    </tr><tr>
                        @endif
                    @endforeach
                    </tr>
                </table>
            @endif
        </div>

        <div class="section">
            <h2>Inventario declarado</h2>
            @if (empty($inventario))
                <div class="empty">No hay inventario registrado.</div>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>Cantidad</th>
                            <th>Articulo</th>
                            <th>Observacion</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($inventario as $item)
                        <tr>
                            <td>{{ $item->cantidad }}</td>
                            <td>{{ $item->nombre }}</td>
                            <td>{{ $item->observaciones ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="section">
            <h2>Servicios adicionales</h2>
            @if (empty($servicios))
                <div class="empty">Sin servicios adicionales.</div>
            @else
                <table class="table">
                    <thead>
                        <tr>
                            <th>Servicio</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($servicios as $servicio)
                        <tr>
                            <td>{{ $servicio }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="section">
            <h2>Resumen de cotizacion</h2>
            <table class="prices">
                <tr>
                    <td class="label">Volumen ({{ $precios['volumen_m3'] ?? 0 }} m3)</td>
                    <td class="value">${{ number_format($precios['costo_volumen'] ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">Distancia ({{ $precios['distancia_km'] ?? 0 }} km)</td>
                    <td class="value">${{ number_format($precios['costo_distancia'] ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">Maniobra por piso</td>
                    <td class="value">${{ number_format($precios['costo_piso'] ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">Costos fijos</td>
                    <td class="value">${{ number_format($precios['costos_fijos'] ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">Subtotal</td>
                    <td class="value">${{ number_format($precios['subtotal_con_modalidad'] ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">IVA ({{ $precios['iva_pct'] ?? 16 }}%)</td>
                    <td class="value">${{ number_format($precios['iva_monto'] ?? 0, 2) }}</td>
                </tr>
                <tr>
                    <td class="label">TOTAL</td>
                    <td class="value">${{ number_format($precios['total_final'] ?? 0, 2) }}</td>
                </tr>
            </table>
        </div>

        <div class="footer">
            Documento generado automaticamente por el sistema de cotizacion FletesCun.
        </div>
    </div>
</body>
</html>
