<!doctype html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <meta name="x-apple-disable-message-reformatting">
    <title>Documentación de cotización</title>
    <style>
        /* Email-safe reset */
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; }
        img { border: 0; height: auto; line-height: 100%; outline: none; text-decoration: none; }
        table { border-collapse: collapse !important; }
        body { margin: 0 !important; padding: 0 !important; width: 100% !important; height: 100% !important; }
        a { color: inherit; }
        @media screen and (max-width: 600px) {
            .container { width: 100% !important; }
            .p-24 { padding: 16px !important; }
            .stack { display: block !important; width: 100% !important; }
            .btn { display: block !important; width: 100% !important; }
            .btn + .btn { margin-top: 10px !important; }
        }
    </style>
</head>
<body style="background:#F8FAFC;">

<table role="presentation" width="100%" bgcolor="#F8FAFC" cellpadding="0" cellspacing="0">
    <tr>
        <td align="center" style="padding: 20px 12px;">

            <table role="presentation" class="container" width="600" cellpadding="0" cellspacing="0" style="width:600px;max-width:600px;background:#ffffff;border-radius:14px;overflow:hidden;">
                <!-- Header -->
                <tr>
                    <td bgcolor="#1E3A8A" style="background: linear-gradient(135deg,#1E3A8A,#2563EB); padding: 22px 24px;" class="p-24">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td class="stack" align="left" style="vertical-align: middle;">
                                    <img src="{{ $logoUrl }}" alt="FletesCun" style="height:48px;display:block;">
                                </td>
                                <td class="stack" align="right" style="vertical-align: middle; color:#FFFFFF; font-family: Arial, sans-serif;">
                                    <div style="font-size:14px; opacity:0.9;">Documentación generada</div>
                                    <div style="font-size:18px; font-weight:700;">Folio {{ $cotizacion->folio ?? 'SIN_FOLIO' }}</div>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="padding: 22px 24px; font-family: Arial, sans-serif; color:#0F172A;" class="p-24">
                        <p style="margin:0 0 14px; font-size:14px; color:#334155;">
                            Se generó una nueva cotización en el sistema. Este correo incluye la información capturada y los documentos adjuntos.
                        </p>

                        <!-- Highlights -->
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #E2E8F0; border-radius:12px; overflow:hidden; margin: 0 0 18px;">
                            <tr>
                                <td style="padding: 14px 14px; background:#F1F5F9; font-size:12px; text-transform:uppercase; letter-spacing:0.06em; color:#64748B; font-weight:700;">
                                    Resumen
                                </td>
                            </tr>
                            <tr>
                                <td style="padding: 14px 14px;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px; color:#334155;">
                                        <tr>
                                            <td style="padding:6px 0; width: 36%; color:#0F172A; font-weight:700;">Fecha de generación</td>
                                            <td style="padding:6px 0;">{{ $fechaGeneracion }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:6px 0; width: 36%; color:#0F172A; font-weight:700;">Cliente</td>
                                            <td style="padding:6px 0;">{{ $cliente->nombre ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:6px 0; width: 36%; color:#0F172A; font-weight:700;">Teléfono</td>
                                            <td style="padding:6px 0;">{{ $cliente->telefono ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:6px 0; width: 36%; color:#0F172A; font-weight:700;">Correo del cliente</td>
                                            <td style="padding:6px 0;">{{ $cliente->correo ?? '-' }}</td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <!-- Contact buttons -->
                        @if(!empty($waLink) || !empty($telLink))
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="margin: 0 0 18px;">
                                <tr>
                                    <td align="center">
                                        @if(!empty($waLink))
                                            <a class="btn" href="{{ $waLink }}" style="background:#16A34A; color:#FFFFFF; text-decoration:none; padding: 12px 16px; border-radius:10px; display:inline-block; font-weight:700; font-size:14px;">Contactar por WhatsApp</a>
                                        @endif
                                        @if(!empty($telLink))
                                            <a class="btn" href="{{ $telLink }}" style="background:#0F172A; color:#FFFFFF; text-decoration:none; padding: 12px 16px; border-radius:10px; display:inline-block; font-weight:700; font-size:14px; margin-left: 10px;">Llamar</a>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        @endif

                        <!-- Datos del traslado -->
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #E2E8F0; border-radius:12px; overflow:hidden; margin: 0 0 18px;">
                            <tr>
                                <td style="padding: 14px 14px; background:#F1F5F9; font-size:12px; text-transform:uppercase; letter-spacing:0.06em; color:#64748B; font-weight:700;">Ruta y logística</td>
                            </tr>
                            <tr>
                                <td style="padding: 14px 14px;">
                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="font-size:14px; color:#334155;">
                                        <tr>
                                            <td style="padding:6px 0; width: 36%; color:#0F172A; font-weight:700;">Origen</td>
                                            <td style="padding:6px 0;">{{ $cotizacion->direccion_origen ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:6px 0; width: 36%; color:#0F172A; font-weight:700;">Destino</td>
                                            <td style="padding:6px 0;">{{ $cotizacion->direccion_destino ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:6px 0; width: 36%; color:#0F172A; font-weight:700;">Fecha ideal</td>
                                            <td style="padding:6px 0;">{{ $cotizacion->fecha_ideal ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:6px 0; width: 36%; color:#0F172A; font-weight:700;">Piso origen / destino</td>
                                            <td style="padding:6px 0;">{{ $cotizacion->piso_origen ?? '-' }} / {{ $cotizacion->piso_destino ?? '-' }}</td>
                                        </tr>
                                        <tr>
                                            <td style="padding:6px 0; width: 36%; color:#0F172A; font-weight:700;">Modalidad</td>
                                            <td style="padding:6px 0;">{{ $cotizacion->tipo_servicio ?? '-' }}</td>
                                        </tr>
                                        @if(!is_null(data_get($cotizacion, 'acceso_estacionamiento_origen')) || !is_null(data_get($cotizacion, 'acceso_estacionamiento_destino')))
                                            <tr>
                                                <td style="padding:6px 0; width: 36%; color:#0F172A; font-weight:700;">Estacionamiento</td>
                                                <td style="padding:6px 0;">Origen: {{ data_get($cotizacion,'acceso_estacionamiento_origen') ?? '-' }} · Destino: {{ data_get($cotizacion,'acceso_estacionamiento_destino') ?? '-' }}</td>
                                            </tr>
                                        @endif
                                        @if(!is_null(data_get($cotizacion, 'elevador_origen')) || !is_null(data_get($cotizacion, 'elevador_destino')))
                                            <tr>
                                                <td style="padding:6px 0; width: 36%; color:#0F172A; font-weight:700;">Elevador</td>
                                                <td style="padding:6px 0;">Origen: {{ data_get($cotizacion,'elevador_origen') ? 'Sí' : 'No' }} · Destino: {{ data_get($cotizacion,'elevador_destino') ? 'Sí' : 'No' }}</td>
                                            </tr>
                                        @endif
                                        @if(!is_null(data_get($cotizacion, 'distancia_km')))
                                            <tr>
                                                <td style="padding:6px 0; width: 36%; color:#0F172A; font-weight:700;">Distancia (km)</td>
                                                <td style="padding:6px 0;">{{ data_get($cotizacion,'distancia_km') }}</td>
                                            </tr>
                                        @endif
                                        @if(!is_null(data_get($cotizacion, 'precio_final')))
                                            <tr>
                                                <td style="padding:6px 0; width: 36%; color:#0F172A; font-weight:700;">Precio final</td>
                                                <td style="padding:6px 0;">{{ data_get($cotizacion,'precio_final') }}</td>
                                            </tr>
                                        @endif
                                        @if(!empty(data_get($cotizacion, 'estatus')))
                                            <tr>
                                                <td style="padding:6px 0; width: 36%; color:#0F172A; font-weight:700;">Estatus</td>
                                                <td style="padding:6px 0;">{{ data_get($cotizacion,'estatus') }}</td>
                                            </tr>
                                        @endif
                                    </table>
                                </td>
                            </tr>
                        </table>

                        <!-- Inventario -->
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #E2E8F0; border-radius:12px; overflow:hidden; margin: 0 0 18px;">
                            <tr>
                                <td style="padding: 14px 14px; background:#F1F5F9; font-size:12px; text-transform:uppercase; letter-spacing:0.06em; color:#64748B; font-weight:700;">Inventario y servicios</td>
                            </tr>
                            <tr>
                                <td style="padding: 14px 14px;">
                                    <div style="font-size:14px; color:#334155; margin:0 0 10px;">
                                        Artículos declarados: <strong>{{ (int) $inventario->count() }}</strong> · Fotos: <strong>{{ (int) $fotosCount }}</strong>
                                    </div>
                                    @if(!empty($servicios))
                                        <div style="font-size:14px; color:#0F172A; font-weight:700; margin: 0 0 6px;">Servicios adicionales</div>
                                        <div style="font-size:14px; color:#334155;">
                                            {{ implode(' · ', $servicios) }}
                                        </div>
                                    @else
                                        <div style="font-size:14px; color:#334155;">Sin servicios adicionales.</div>
                                    @endif
                                </td>
                            </tr>
                        </table>

                        <!-- Adjuntos -->
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border:1px solid #E2E8F0; border-radius:12px; overflow:hidden;">
                            <tr>
                                <td style="padding: 14px 14px; background:#F1F5F9; font-size:12px; text-transform:uppercase; letter-spacing:0.06em; color:#64748B; font-weight:700;">Archivos adjuntos</td>
                            </tr>
                            <tr>
                                <td style="padding: 14px 14px; font-size:14px; color:#334155;">
                                    Se adjuntan los siguientes documentos para revisión:
                                    <ul style="margin: 10px 0 0 20px; padding:0;">
                                        <li><strong>Documento Word (.docx)</strong> — Carta Porte / Contrato</li>
                                        <li><strong>PDF</strong> — Anexo fotográfico</li>
                                    </ul>
                                </td>
                            </tr>
                        </table>

                        <p style="margin:16px 0 0; font-size:12px; color:#64748B; line-height:1.5;">
                            Este mensaje fue generado automáticamente por el sistema. No respondas a este correo.
                        </p>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td bgcolor="#F1F5F9" style="padding: 16px 24px; font-family: Arial, sans-serif; color:#64748B; font-size:12px; text-align:center;" class="p-24">
                        &copy; {{ date('Y') }} FletesCun · Cancún
                    </td>
                </tr>
            </table>

        </td>
    </tr>
</table>

</body>
</html>
