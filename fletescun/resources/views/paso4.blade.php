{{-- resources/views/cotizador/paso4.blade.php --}}
{{-- ──────────────────────────────────────────────────────────────────────────
     PASO 4 / 4 · Resumen y Cotización — FletesCun
     Datos leídos de session('cotizador_paso1'), session('cotizador_paso2')
     y session('cotizador_paso3').
     Al enviar: guarda en BD (clientes + cotizaciones + servicios_adicionales +
     inventario_articulos + aceptaciones_contrato) y redirige a /gracias.
────────────────────────────────────────────────────────────────────────────── --}}

@php
    /* ── Recuperar sesión de los 3 pasos anteriores ─────────── */
    $p1 = session('cotizador_paso1', []);
    $p2 = session('cotizador_paso2', []);
    $p3 = session('cotizador_paso3', []);

    $nombre   = $p1['nombre']             ?? '';
    $telefono = $p1['telefono']           ?? '';
    $correo   = $p1['correo']             ?? '';
    $fecha    = $p1['fecha_ideal']        ?? '';
    $origen   = $p1['direccion_origen']   ?? '';
    $destino  = $p1['direccion_destino']  ?? '';

    $pisoOrigen    = $p2['piso_origen']       ?? '';
    $pisoDestino   = $p2['piso_destino']      ?? '';
    $elevOrigen    = !empty($p2['elevador_origen'])  && $p2['elevador_origen']  == '1';
    $elevDestino   = !empty($p2['elevador_destino']) && $p2['elevador_destino'] == '1';
    $modalidad     = $p2['modalidad']         ?? 'Exclusivo'; // 'Exclusivo' | 'Compartido'

    $srvEmbalaje   = !empty($p2['servicio_embalaje'])   && $p2['servicio_embalaje']   == '1';
    $srvDesmontaje = !empty($p2['servicio_desmontaje']) && $p2['servicio_desmontaje'] == '1';
    $srvVolado     = !empty($p2['servicio_volado'])     && $p2['servicio_volado']     == '1';
    $srvSeguro     = !empty($p2['servicio_seguro'])     && $p2['servicio_seguro']     == '1';

    /* inventario_catalogo: [ {nombre, m3, cantidad, categoria}, ... ] */
    $catalogo  = $p3['inventario_catalogo']  ?? [];
    /* articulos_especiales: [ {articulo, cantidad, observaciones}, ... ] */
    $especiales = $p3['articulos_especiales'] ?? [];
    /* fotos_paths: rutas públicas guardadas en paso3 */
    $fotos     = $p3['fotos_paths']          ?? [];

    /* ── Cálculo de precio estimado (refleja la lógica del React) */
    $floorMap = [
        'Planta baja' => 0, '1er piso' => 1, '2do piso' => 2,
        '3er piso' => 3,    '4to piso' => 4, '5to piso' => 5, '6to piso o más' => 6,
    ];
    $originFloor = $floorMap[$pisoOrigen] ?? 0;
    $destFloor   = $floorMap[$pisoDestino] ?? 0;
    $floorSurcharge = max(0, $originFloor + $destFloor
        - ($elevOrigen  ? 2 : 0)
        - ($elevDestino ? 2 : 0)) * 200;

    /* Precios reales calculados en paso4() usando PricingService */
    $priceMin = $priceMin ?? 6500;   // fallback si no viene del controlador
    $priceMax = $priceMax ?? 12000;

    /* Fecha formateada */
    $fechaFormateada = '';
    if ($fecha) {
        $ts = strtotime($fecha);
        $dias   = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
        $meses  = ['','enero','febrero','marzo','abril','mayo','junio',
                   'julio','agosto','septiembre','octubre','noviembre','diciembre'];
        $fechaFormateada = $dias[date('w',$ts)] . ', '
                         . (int)date('j',$ts) . ' de '
                         . $meses[(int)date('n',$ts)] . ' de ' . date('Y',$ts);
    }

    $hayEspeciales = count($especiales) > 0;
@endphp

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FletesCun · Paso 4 de 4 — Resumen</title>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: linear-gradient(135deg, #EFF6FF 0%, #F8FAFC 60%, #F0FDF4 100%);
            min-height: 100vh;
            padding: 24px 12px 80px;
        }

        /* ── Wrapper ── */
        .wizard-wrapper { max-width: 820px; margin: 0 auto; }

        /* ── Brand ── */
        .brand-header  { text-align: center; margin-bottom: 24px; }
        .brand-logo    { display: inline-flex; align-items: center; gap: 10px; text-decoration: none; }
        .brand-icon    { width:44px;height:44px;background:linear-gradient(135deg,#2563EB,#1D4ED8);border-radius:12px;display:flex;align-items:center;justify-content:center;box-shadow:0 4px 12px rgba(37,99,235,.35); }
        .brand-name    { font-size:1.25rem;font-weight:700;color:#1E293B;line-height:1.2; }
        .brand-tagline { font-size:.72rem;color:#64748B; }

        /* ── Stepper ── */
        .stepper-card  { background:#fff;border-radius:20px;padding:20px 24px;box-shadow:0 4px 20px rgba(15,23,42,.06);margin-bottom:20px; }
        .step-label    { font-size:.72rem;font-weight:600;color:#94A3B8;text-transform:uppercase;letter-spacing:.06em;margin-bottom:14px; }
        .step-label span { color:#2563EB; }
        .steps-row     { display:flex;align-items:flex-start; }
        .step-item     { flex:1;display:flex;flex-direction:column;align-items:center;position:relative; }
        .step-item:not(:last-child)::after { content:'';position:absolute;top:15px;left:calc(50% + 14px);right:calc(-50% + 14px);height:2px;background:#E2E8F0;z-index:0; }
        .step-item.completed:not(:last-child)::after { background:#2563EB; }
        .step-circle   { width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;border:2px solid #E2E8F0;background:#F8FAFC;color:#94A3B8;position:relative;z-index:1;transition:all .3s; }
        .step-item.active    .step-circle { background:#2563EB;border-color:#2563EB;color:#fff;box-shadow:0 0 0 4px rgba(37,99,235,.15); }
        .step-item.completed .step-circle { background:#2563EB;border-color:#2563EB;color:#fff; }
        .step-name     { font-size:.68rem;font-weight:500;color:#94A3B8;text-align:center;margin-top:6px;line-height:1.3; }
        .step-item.active    .step-name { color:#2563EB;font-weight:700; }
        .step-item.completed .step-name { color:#1E293B; }

        /* ── Tarjeta principal ── */
        .form-card     { background:#fff;border-radius:24px;padding:36px 32px;box-shadow:0 8px 30px rgba(15,23,42,.07); }
        @media (max-width:576px) { .form-card { padding:24px 18px; } }

        /* ── Cabecera de sección ── */
        .section-icon  { width:40px;height:40px;background:#EFF6FF;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
        .section-title { font-size:1.35rem;font-weight:700;color:#1B3A6B;margin:0; }
        .section-sub   { font-size:.88rem;color:#64748B;margin:0; }

        /* ── Divider ── */
        .divider-label { display:flex;align-items:center;gap:12px;margin:28px 0; }
        .divider-label::before,.divider-label::after { content:'';flex:1;height:1px;background:#E2E8F0; }
        .divider-label span { font-size:.72rem;font-weight:700;color:#94A3B8;text-transform:uppercase;letter-spacing:.07em;white-space:nowrap; }

        /* ── Tarjeta de precio ── */
        .price-card {
            border-radius: 20px;
            padding: 28px;
            position: relative;
            overflow: hidden;
            transition: all .3s;
        }
        .price-card.normal    { background: linear-gradient(135deg,#1B3A6B 0%,#2563EB 100%); box-shadow:0 4px 15px rgba(0,0,0,.1); }
        .price-card.especial  { background: linear-gradient(135deg,#1E40AF 0%,#3B82F6 100%); border-top:3px solid #FCD34D; box-shadow:0 0 20px rgba(252,211,77,.2); }
        .price-deco-1 { position:absolute;top:-30px;right:-30px;width:120px;height:120px;border-radius:50%;background:rgba(255,255,255,.05); }
        .price-deco-2 { position:absolute;bottom:-20px;left:-20px;width:80px;height:80px;border-radius:50%;background:rgba(255,255,255,.05); }
        .price-especial-badge {
            display:inline-flex;align-items:center;gap:6px;padding:6px 12px;
            border-radius:8px;margin-bottom:14px;
            background:rgba(252,211,77,.15);border:1px solid rgba(252,211,77,.4);
        }
        .price-especial-badge span { font-size:.7rem;font-weight:700;color:#FCD34D; }
        .price-stars { display:flex;align-items:center;justify-content:center;gap:8px;margin-bottom:16px; }
        .price-stars span { font-size:.78rem;font-weight:700;color:#93C5FD;text-transform:uppercase;letter-spacing:.08em; }
        .price-amount-row { display:flex;align-items:baseline;gap:4px; margin-bottom:6px; }
        .price-currency { font-size:1rem;color:#93C5FD;font-weight:400; }
        .price-amount   { font-size:2.5rem;font-weight:800;color:#fff;line-height:1; }
        .price-unit     { font-size:.9rem;color:#93C5FD;font-weight:500; }
        .price-range    { font-size:.82rem;color:#93C5FD;margin-bottom:14px; }
        .price-range strong { color:#BFDBFE;font-weight:700; }
        .price-badge-mod {
            display:inline-flex;align-items:center;gap:6px;padding:6px 14px;
            border-radius:999px;background:rgba(255,255,255,.12);
        }
        .price-badge-mod span { font-size:.75rem;color:#E0F2FE;font-weight:600; }
        .price-divider { width:1px;background:rgba(255,255,255,.1);align-self:stretch; }
        .price-payment-title { font-size:.75rem;font-weight:700;color:#93C5FD;text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px; }
        .price-payment-row  { display:flex;align-items:center;justify-content:space-between;margin-bottom:6px; }
        .price-payment-row span:first-child { font-size:.78rem;color:#E0F2FE; }
        .price-payment-row span:last-child  { font-size:.78rem;font-weight:700;color:#fff; }
        .price-note { font-size:.72rem;color:#93C5FD;margin-top:14px;line-height:1.4; }
        .price-note strong { color:#FCD34D;font-weight:600; }

        /* ── Summary rows ── */
        .summary-block {
            background:#F8FAFC;border:1.5px solid #E2E8F0;border-radius:20px;padding:20px;
        }
        .summary-block h3 { color:#1B3A6B;font-size:.92rem;font-weight:700;margin-bottom:10px; }

        .sum-row {
            display:flex;align-items:flex-start;gap:12px;
            padding:10px 0;border-bottom:1px solid #F1F5F9;
        }
        .sum-row:last-child { border-bottom:none; }
        .sum-icon {
            width:32px;height:32px;flex-shrink:0;
            background:#F1F5F9;border-radius:8px;
            display:flex;align-items:center;justify-content:center;
            margin-top:2px;
        }
        .sum-label { font-size:.72rem;font-weight:600;color:#94A3B8;text-transform:uppercase;letter-spacing:.04em; display:block; }
        .sum-value { font-size:.88rem;color:#1E293B;font-weight:500;display:block; }
        .sum-value.green { color:#15803D; }
        .sum-value.red   { color:#DC2626; }

        /* ── Inventario ── */
        .inv-item {
            display:flex;align-items:center;gap:10px;margin-bottom:8px;
        }
        .inv-dot {
            width:20px;height:20px;border-radius:4px;flex-shrink:0;
            display:flex;align-items:center;justify-content:center;
        }
        .inv-dot.normal   { background:#EFF6FF; }
        .inv-dot.especial { background:#FEF3C7; }
        .inv-label { font-size:.82rem;color:#374151;flex:1; }
        .inv-obs   { font-size:.7rem;color:#94A3B8;background:#F1F5F9;padding:2px 8px;border-radius:20px; }
        .inv-obs.esp { background:#FEF3C7; }

        /* ── Servicios adicionales ── */
        .serv-item { display:flex;align-items:center;gap:8px;margin-bottom:6px; }
        .serv-item span { font-size:.82rem;color:#374151; }

        /* ── Trust badges ── */
        .trust-grid { display:grid;grid-template-columns:repeat(3,1fr);gap:12px; }
        .trust-card {
            display:flex;flex-direction:column;align-items:center;text-align:center;
            padding:12px 8px;border-radius:14px;
            background:#F8FAFC;border:1px solid #E2E8F0;
        }
        .trust-emoji { font-size:1.4rem;margin-bottom:4px; }
        .trust-title { font-size:.72rem;font-weight:700;color:#374151; }
        .trust-sub   { font-size:.65rem;color:#94A3B8; }

        /* ── Contrato ── */
        .contract-wrap {
            background:#FFFBEB;border:1.5px solid #FDE68A;border-radius:20px;padding:24px;
        }
        .contract-head { display:flex;align-items:center;gap:10px;margin-bottom:16px; }
        .contract-head h3 { color:#92400E;margin:0;font-size:1rem;font-weight:700; }

        .contract-scroll {
            background:#fff;border:1px solid #FCD34D;border-radius:14px;
            padding:16px;max-height:400px;overflow-y:auto;
            font-size:.75rem;line-height:1.6;color:#374151;
            margin-bottom:16px;
        }
        .contract-scroll p { margin-bottom:10px; }
        .contract-scroll .c-title { font-weight:700;color:#1B3A6B;margin-bottom:12px; }
        .contract-scroll strong   { color:#1E293B; }

        /* ── Checkboxes de aceptación ── */
        .accept-btn {
            display:flex;align-items:flex-start;gap:12px;
            padding:12px 14px;border-radius:12px;margin-bottom:12px;
            border:1.5px solid #FCD34D;background:#fff;
            cursor:pointer;width:100%;text-align:left;
            transition:background .2s,border-color .2s;
            font-family:'DM Sans',sans-serif;
        }
        .accept-btn.checked { background:#F0FDF4;border-color:#22C55E; }
        .check-box {
            width:20px;height:20px;border-radius:4px;flex-shrink:0;margin-top:2px;
            display:flex;align-items:center;justify-content:center;
            background:#fff;border:2px solid #FCD34D;transition:all .2s;
        }
        .accept-btn.checked .check-box { background:#22C55E;border-color:#22C55E; }
        .accept-label-title { font-size:.88rem;font-weight:600;color:#92400E; }
        .accept-label-sub   { font-size:.75rem;color:#B45309;display:block;margin-top:2px; }

        /* ── Botones de navegación ── */
        .btn-nav {
            display:inline-flex;align-items:center;justify-content:center;gap:8px;
            border-radius:14px;padding:14px 22px;font-weight:700;font-size:.95rem;
            font-family:'DM Sans',sans-serif;cursor:pointer;transition:all .2s;
            text-decoration:none;border:none;
        }
        .btn-back {
            background:#F1F5F9;color:#475569;
            border:1.5px solid #E2E8F0;
            flex:0 0 auto;
        }
        .btn-back:hover { background:#E2E8F0;color:#1E293B; }

        .btn-submit {
            background:linear-gradient(135deg,#1B3A6B,#2563EB);
            color:#fff;flex:1;
            box-shadow:0 4px 20px rgba(37,99,235,.35);
        }
        .btn-submit:hover { transform:translateY(-1px);box-shadow:0 8px 28px rgba(37,99,235,.45); }
        .btn-submit:active { transform:translateY(0); }
        .btn-submit:disabled {
            background:#CBD5E1;box-shadow:none;
            cursor:not-allowed;opacity:.6;transform:none;
        }

        /* ── Error de sesión ── */
        .session-alert {
            background:#FEF2F2;border:1.5px solid #FECACA;border-radius:14px;
            padding:16px 20px;margin-bottom:20px;
            display:flex;align-items:center;gap:10px;
            font-size:.88rem;color:#DC2626;font-weight:500;
        }

        /* ── Fotos ── */
        .foto-thumb {
            width:48px;height:48px;border-radius:8px;object-fit:cover;
            border:1.5px solid #E2E8F0;
        }
        .foto-placeholder {
            width:48px;height:48px;border-radius:8px;background:#EFF6FF;
            display:flex;align-items:center;justify-content:center;
            border:1.5px solid #E2E8F0;font-size:.65rem;color:#2563EB;text-align:center;
        }

        @media (max-width:576px) {
            .trust-grid { grid-template-columns:repeat(3,1fr); }
            .trust-title { font-size:.65rem; }
            .trust-emoji { font-size:1.1rem; }
            .price-amount { font-size:2rem; }
        }
    </style>
</head>
<body>

<div class="wizard-wrapper">

    {{-- ── BRAND ── --}}
    <div class="brand-header">
        <a class="brand-logo" href="#">
            <div class="brand-icon">
                <i class="fa-solid fa-truck-moving" style="color:#fff;font-size:1.1rem;"></i>
            </div>
            <div>
                <div class="brand-name">FletesCun</div>
                <div class="brand-tagline">Mudanzas confiables · Cancún</div>
            </div>
        </a>
    </div>

    {{-- ── STEPPER ── --}}
    <div class="stepper-card">
        <p class="step-label">Paso <span>4</span> de 4</p>
        <div class="steps-row">
            @foreach([
                ['num'=>1,'name'=>'Contacto & Ruta'],
                ['num'=>2,'name'=>'Logística'],
                ['num'=>3,'name'=>'Inventario'],
                ['num'=>4,'name'=>'Resumen'],
            ] as $step)
                @php
                    $cls = $step['num'] < 4 ? 'completed' : ($step['num'] == 4 ? 'active' : '');
                @endphp
                <div class="step-item {{ $cls }}">
                    <div class="step-circle">
                        @if($step['num'] < 4)
                            <i class="fa-solid fa-check" style="font-size:.7rem;"></i>
                        @else
                            {{ $step['num'] }}
                        @endif
                    </div>
                    <span class="step-name">{{ $step['name'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ── ALERTA si falta sesión ── --}}
    @if(empty($p1) || empty($p2))
        <div class="session-alert">
            <i class="fa-solid fa-triangle-exclamation"></i>
            Tu sesión expiró o accediste directamente a esta página.
            <a href="{{ route('cotizar.paso1') }}" style="color:#DC2626;font-weight:700;margin-left:6px;">
                Reiniciar cotización
            </a>
        </div>
    @endif

    {{-- ── ERRORES DE VALIDACIÓN ── --}}
    @if($errors->any())
        <div class="session-alert">
            <i class="fa-solid fa-circle-exclamation"></i>
            <div>
                @foreach($errors->all() as $err)
                    <div>{{ $err }}</div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ── FORM CARD ── --}}
    <div class="form-card">

        {{-- Cabecera ──────────────────────────────────────────── --}}
        <div class="d-flex align-items-center gap-2 mb-2">
            <div class="section-icon">
                <i class="fa-solid fa-file-contract" style="color:#2563EB;font-size:.95rem;"></i>
            </div>
            <h1 class="section-title" style="font-size:1.35rem;">Resumen y Cotización</h1>
        </div>
        <p class="section-sub mb-4">Revisa los detalles de tu mudanza antes de generar tu contrato.</p>

        {{-- ── TARJETA DE PRECIO ─────────────────────────────── --}}
        <div class="price-card mb-4 {{ $hayEspeciales ? 'especial' : 'normal' }}">
            <div class="price-deco-1"></div>
            <div class="price-deco-2"></div>

            <div class="position-relative">
                @if($hayEspeciales)
                    <div class="price-especial-badge">
                        <i class="fa-solid fa-bolt" style="color:#FCD34D;font-size:.7rem;"></i>
                        <span>ARTÍCULOS ESPECIALES INCLUIDOS ({{ count($especiales) }})</span>
                    </div>
                @endif

                <div class="price-stars">
                    <i class="fa-solid fa-star" style="color:#FCD34D;font-size:.75rem;"></i>
                    <span>Precio Estimado Aproximado</span>
                    <i class="fa-solid fa-star" style="color:#FCD34D;font-size:.75rem;"></i>
                </div>

                <div class="row g-0">
                    {{-- Precio ──────────────────────── --}}
                    <div class="col-12 col-sm-6 pe-sm-4" style="border-right:1px solid rgba(255,255,255,.1);">
                        <div class="price-amount-row">
                            <span class="price-currency">$</span>
                            <span class="price-amount">{{ number_format($priceMin, 0, '.', ',') }}</span>
                            <span class="price-unit">MXN</span>
                        </div>
                        <div class="price-range mb-3">
                            hasta <strong>${{ number_format($priceMax, 0, '.', ',') }} MXN</strong>
                        </div>
                        <div class="price-badge-mod">
                            @if(strtolower($modalidad) === 'exclusivo')
                                <i class="fa-solid fa-bolt" style="color:#FCD34D;font-size:.8rem;"></i>
                            @else
                                <i class="fa-solid fa-users" style="color:#86EFAC;font-size:.8rem;"></i>
                            @endif
                            <span>{{ $modalidad }}</span>
                        </div>
                    </div>

                    {{-- Esquema de pago ───────────────── --}}
                    <div class="col-12 col-sm-6 ps-sm-4 mt-3 mt-sm-0">
                        <p class="price-payment-title">Esquema de pago:</p>
                        <div class="price-payment-row">
                            <span>10% Anticipo</span>
                            <span>${{ number_format(round($priceMin * 0.1), 0, '.', ',') }}</span>
                        </div>
                        <div class="price-payment-row">
                            <span>60% Al cargar</span>
                            <span>${{ number_format(round($priceMin * 0.6), 0, '.', ',') }}</span>
                        </div>
                        <div class="price-payment-row">
                            <span>30% Al entregar</span>
                            <span>${{ number_format(round($priceMin * 0.3), 0, '.', ',') }}</span>
                        </div>
                    </div>
                </div>

                <p class="price-note">
                    * El costo final se ajustará tras revisar
                    @if($hayEspeciales)
                        <strong>los artículos especiales,</strong>
                    @endif
                    las fotografías e inventario de artículos.
                </p>
            </div>
        </div>

        {{-- ── DATOS DE CONTACTO + RUTA ──────────────────────── --}}
        <div class="row g-3 mb-3">
            {{-- Contacto ──────────────── --}}
            <div class="col-12 col-md-6">
                <div class="summary-block h-100">
                    <h3>Datos de Contacto</h3>
                    <div class="sum-row">
                        <div class="sum-icon"><span style="font-size:.85rem;">👤</span></div>
                        <div>
                            <span class="sum-label">Nombre</span>
                            <span class="sum-value">{{ $nombre ?: '—' }}</span>
                        </div>
                    </div>
                    <div class="sum-row">
                        <div class="sum-icon"><i class="fa-solid fa-phone" style="color:#2563EB;font-size:.8rem;"></i></div>
                        <div>
                            <span class="sum-label">Teléfono</span>
                            <span class="sum-value">{{ $telefono ?: '—' }}</span>
                        </div>
                    </div>
                    <div class="sum-row">
                        <div class="sum-icon"><i class="fa-solid fa-envelope" style="color:#2563EB;font-size:.8rem;"></i></div>
                        <div>
                            <span class="sum-label">Correo</span>
                            <span class="sum-value" style="word-break:break-all;">{{ $correo ?: '—' }}</span>
                        </div>
                    </div>
                    <div class="sum-row">
                        <div class="sum-icon"><i class="fa-solid fa-calendar-day" style="color:#2563EB;font-size:.8rem;"></i></div>
                        <div>
                            <span class="sum-label">Fecha ideal</span>
                            <span class="sum-value">{{ $fechaFormateada ?: '—' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Ruta & Logística ──────── --}}
            <div class="col-12 col-md-6">
                <div class="summary-block h-100">
                    <h3>Ruta y Logística</h3>
                    <div class="sum-row">
                        <div class="sum-icon"><i class="fa-solid fa-location-dot" style="color:#22C55E;font-size:.8rem;"></i></div>
                        <div>
                            <span class="sum-label">Origen</span>
                            <span class="sum-value green">{{ $origen ?: '—' }}</span>
                        </div>
                    </div>
                    <div class="sum-row">
                        <div class="sum-icon"><i class="fa-solid fa-location-dot" style="color:#EF4444;font-size:.8rem;"></i></div>
                        <div>
                            <span class="sum-label">Destino</span>
                            <span class="sum-value red">{{ $destino ?: '—' }}</span>
                        </div>
                    </div>
                    <div class="sum-row">
                        <div class="sum-icon"><i class="fa-solid fa-building" style="color:#2563EB;font-size:.8rem;"></i></div>
                        <div>
                            <span class="sum-label">Pisos</span>
                            <span class="sum-value">
                                Carga: {{ $pisoOrigen ?: '—' }}
                                @if($elevOrigen) <span style="font-size:.7rem;color:#2563EB;"> + elevador</span> @endif
                                &nbsp;·&nbsp;
                                Descarga: {{ $pisoDestino ?: '—' }}
                                @if($elevDestino) <span style="font-size:.7rem;color:#2563EB;"> + elevador</span> @endif
                            </span>
                        </div>
                    </div>
                    <div class="sum-row">
                        <div class="sum-icon"><i class="fa-solid fa-truck" style="color:#2563EB;font-size:.8rem;"></i></div>
                        <div>
                            <span class="sum-label">Modalidad</span>
                            <span class="sum-value">{{ $modalidad }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── INVENTARIO Y SERVICIOS ─────────────────────────── --}}
        @php
            $catFilled = array_filter($catalogo, fn($i) => !empty($i['nombre']));
            $haySrvs   = $srvEmbalaje || $srvDesmontaje || $srvVolado || $srvSeguro;
            $hayFotos  = count($fotos) > 0;
            $mostrarInventario = count($catFilled) > 0 || $hayEspeciales || $haySrvs || $hayFotos;
        @endphp

        @if($mostrarInventario)
        <div class="summary-block mb-3">
            <h3>📋 Inventario declarado</h3>

            {{-- Artículos del catálogo ── --}}
            @if(count($catFilled) > 0)
                <p style="font-size:.78rem;font-weight:600;color:#64748B;margin-bottom:8px;">
                    Artículos del catálogo ({{ count($catFilled) }}):
                </p>
                @foreach(array_slice($catFilled, 0, 6) as $item)
                    <div class="inv-item">
                        <div class="inv-dot normal">
                            <i class="fa-solid fa-box" style="color:#2563EB;font-size:.6rem;"></i>
                        </div>
                        <span class="inv-label"><strong>{{ $item['cantidad'] }}x</strong> {{ $item['nombre'] }}</span>
                        @if(!empty($item['observaciones']))
                            <span class="inv-obs">{{ $item['observaciones'] }}</span>
                        @endif
                    </div>
                @endforeach
                @if(count($catFilled) > 6)
                    <p style="font-size:.75rem;color:#94A3B8;margin-top:4px;">
                        + {{ count($catFilled) - 6 }} artículo(s) más…
                    </p>
                @endif
            @endif

            {{-- Artículos especiales ──── --}}
            @if($hayEspeciales)
                <div style="border-top:1px solid #E2E8F0;margin-top:12px;padding-top:12px;">
                    <p style="font-size:.78rem;font-weight:600;color:#D97706;margin-bottom:8px;">
                        ⚠️ Artículos especiales ({{ count($especiales) }}):
                    </p>
                    @foreach(array_slice($especiales, 0, 6) as $item)
                        <div class="inv-item">
                            <div class="inv-dot especial">
                                <span style="font-size:.7rem;color:#D97706;font-weight:700;">!</span>
                            </div>
                            <span class="inv-label"><strong>{{ $item['cantidad'] }}x</strong> {{ $item['articulo'] }}</span>
                            @if(!empty($item['observaciones']))
                                <span class="inv-obs esp">{{ $item['observaciones'] }}</span>
                            @endif
                        </div>
                    @endforeach
                    @if(count($especiales) > 6)
                        <p style="font-size:.75rem;color:#94A3B8;margin-top:4px;">
                            + {{ count($especiales) - 6 }} artículo(s) más…
                        </p>
                    @endif
                </div>
            @endif

            {{-- Servicios adicionales ─── --}}
            @if($haySrvs)
                <div style="border-top:1px solid #E2E8F0;margin-top:12px;padding-top:12px;">
                    <p style="font-size:.78rem;font-weight:600;color:#64748B;margin-bottom:8px;">
                        Servicios adicionales:
                    </p>
                    @if($srvEmbalaje)
                        <div class="serv-item">
                            <i class="fa-solid fa-check" style="color:#22C55E;font-size:.8rem;"></i>
                            <span>Embalaje de cajas 📦</span>
                        </div>
                    @endif
                    @if($srvDesmontaje)
                        <div class="serv-item">
                            <i class="fa-solid fa-check" style="color:#22C55E;font-size:.8rem;"></i>
                            <span>Desmontaje de muebles 🔧</span>
                        </div>
                    @endif
                    @if($srvVolado)
                        <div class="serv-item">
                            <i class="fa-solid fa-check" style="color:#22C55E;font-size:.8rem;"></i>
                            <span>Volado / acarreo externo 🚚</span>
                        </div>
                    @endif
                    @if($srvSeguro)
                        <div class="serv-item">
                            <i class="fa-solid fa-check" style="color:#22C55E;font-size:.8rem;"></i>
                            <span>Seguro de carga 🛡️</span>
                        </div>
                    @endif
                </div>
            @endif

            {{-- Fotos ─────────────────── --}}
            @if($hayFotos)
                <div style="border-top:1px solid #E2E8F0;margin-top:14px;padding-top:14px;">
                    <p style="font-size:.78rem;font-weight:600;color:#64748B;margin-bottom:8px;">
                        📷 {{ count($fotos) }} foto{{ count($fotos) !== 1 ? 's' : '' }} adjunta{{ count($fotos) !== 1 ? 's' : '' }}
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach(array_slice($fotos, 0, 5) as $path)
                            <img src="{{ asset('storage/' . $path) }}"
                                 alt="Foto artículo"
                                 class="foto-thumb">
                        @endforeach
                        @if(count($fotos) > 5)
                            <div class="foto-placeholder">
                                +{{ count($fotos) - 5 }}<br>más
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
        @endif

        {{-- ── TRUST BADGES ───────────────────────────────────── --}}
        <div class="trust-grid mb-4">
            <div class="trust-card">
                <div class="trust-emoji">🔒</div>
                <div class="trust-title">Datos seguros</div>
                <div class="trust-sub">Privacidad garantizada</div>
            </div>
            <div class="trust-card">
                <div class="trust-emoji">⚡</div>
                <div class="trust-title">Respuesta en 2h</div>
                <div class="trust-sub">Cotización confirmada</div>
            </div>
            <div class="trust-card">
                <div class="trust-emoji">✅</div>
                <div class="trust-title">Sin compromiso</div>
                <div class="trust-sub">Cancela cuando quieras</div>
            </div>
        </div>

        {{-- ── FORM: aceptación + envío ───────────────────────── --}}
        <form id="formPaso4"
              method="POST"
              action="{{ route('cotizar.paso4.post') }}">
            @csrf

            {{-- ── BLOQUE CONTRATO ─────────────────────────────── --}}
            <div class="contract-wrap mb-4">
                <div class="contract-head">
                    <i class="fa-solid fa-file-contract" style="color:#D97706;font-size:1.1rem;"></i>
                    <h3>Aceptación del Contrato</h3>
                </div>

                {{-- Scroll del contrato ──────────── --}}
                <div class="contract-scroll">
                    <p class="c-title">Aceptación del Contrato</p>
                    <p class="c-subtitle"><strong>CONTRATO DE PRESTACIÓN DE SERVICIOS DE AUTOTRANSPORTE DE CARGA</strong></p>

                    <p>
                        QUE CELEBRAN POR UNA PARTE <strong>MUDANZA FLETESCUN TU EMPRESA</strong> COMO "EL PROVEEDOR"
                        Y POR LA OTRA PARTE COMO "EL CONSUMIDOR", CUYO NOMBRE,
                        <strong>{{ strtoupper($nombre ?: 'EL CLIENTE') }}</strong>,
                        Y DATOS CONSTAN EN LA CARÁTULA DE ESTE CONTRATO COMO PARTE INTEGRAL DEL MISMO,
                        SUJETÁNDOSE AL TENOR DE LAS SIGUIENTES:
                    </p>

                    <p><strong>CLÁUSULAS</strong></p>

                    <p>
                        <strong>PRIMERA.-</strong> El objeto del presente contrato es la prestación del servicio de Transporte de Carga,
                        el cual podrá ser local o foráneo, según se determine en la carátula.
                    </p>

                    <p>
                        <strong>SEGUNDA.-</strong> EL PROVEEDOR se obliga a entregar a EL CONSUMIDOR una copia del presente contrato de adhesión al momento de su firma.
                    </p>

                    <p>
                        <strong>TERCERA.-</strong> Es obligación de EL PROVEEDOR que el personal que se encargará de las maniobras
                        de carga y descarga de los bienes se identifique plenamente ante EL CONSUMIDOR, antes de iniciar las
                        operaciones contratadas y especifique las maniobras junto con el inventario que viene señalado en el contrato.
                        TODO SERVICIO ADICIONAL NO CONSIDERADO EN EL INVENTARIO Y DESCRIPCIÓN DEL SERVICIO CAUSARÁ UN CARGO ADICIONAL
                        SUJETO AL ENCARGADO DE MANIOBRAS O AL DEPARTAMENTO DE LOGÍSTICA DE LA EMPRESA.
                    </p>

                    <p>
                        <strong>CUARTA.-</strong> Es obligación de EL CONSUMIDOR declarar verazmente al proveedor toda la información
                        relativa a la descripción, valor, cantidad, peso y demás características de la mercancía que pretende transportar,
                        en caso de falsedad, EL CONSUMIDOR asumirá la responsabilidad respectiva y las repercusiones en costos adicionales.
                    </p>

                    <p>
                        <strong>QUINTA.-</strong> Es obligación de EL PROVEEDOR entregar por escrito la Carta de Garantías respectivas
                        a la prestación del servicio. En caso de alguna eventualidad o daño en el tipo de servicio EXCLUSIVO, la empresa
                        solo se hace responsable de cubrir dicho imprevisto con el 5% al 15% del daño del mueble, tomando en cuenta el
                        estado en el que se encuentra dicho objeto (menaje de casa en uso), solo aplica al siguiente día después de la
                        entrega de su mercancía y que es responsable del cliente la contratación de un seguro para su carga (cubre trayecto
                        de origen y destino y solo cubre robo o siniestro).
                    </p>

                    <p>
                        <strong>SEXTA.-</strong> En caso de algún daño en los bienes del consumidor en el tipo de servicio COMPARTIDO,
                        la empresa solo aplica un reembolso del 1% al 10% tomando en cuenta el daño generado a los bienes y que es
                        responsabilidad del cliente la contratación de un seguro para su carga (cubre trayecto de origen y destino y solo cubre
                        robo o siniestro).
                    </p>

                    <p>
                        <strong>SÉPTIMA.-</strong> La empresa no se hace responsable en el funcionamiento de electrónica, línea blanca
                        y electrodomésticos. Solo se hace responsable en caso de golpes visibles causados en las maniobras. Las plantas
                        y cristales viajan por cuenta y riesgo del cliente; ninguna ruptura, abolladura o despostillamiento de algún ónix,
                        piedra, mármol, etc. NO se pagarán ya que estos por su naturaleza son susceptibles a romperse.
                    </p>

                    <p>
                        <strong>OCTAVA.-</strong> Se establece como penalización por incumplimiento de contrato para cualquiera de las partes con el 15% del valor total de la operación.
                    </p>

                    <p>
                        <strong>NOVENA.-</strong> Es responsabilidad del CLIENTE revisar el tipo de maniobras (volados, acarreos, etc.)
                        y servicios que incluyen nuestros vendedores antes de hacer el primer depósito a cuenta de su servicio, para evitar situaciones de conflicto.
                    </p>

                    <p>
                        <strong>DÉCIMA.-</strong> El cliente enviará por correo o mediante un mensaje de texto el comprobante del 10% y el 60% cuando esté el producto en la unidad Y EL 30% antes de entregar en destino.
                    </p>

                    <p>
                        <strong>DÉCIMA PRIMERA.-</strong> La Procuraduría Federal del Consumidor es competente en la vía administrativa
                        para resolver controversia que se suscite sobre la interpretación o cumplimiento del presente contrato, sin perjuicio
                        de lo anterior, las partes se someten a la jurisdicción de los tribunales competentes en la Ciudad de México, renunciando
                        expresamente a cualquier otra jurisdicción que pudiera corresponderles, por razón de sus domicilios presentes o futuros o por cualquier otra razón.
                    </p>
                </div>

                {{-- Checkbox 1: términos ────────── --}}
                <button type="button"
                        class="accept-btn {{ old('acepta_terminos') ? 'checked' : '' }}"
                        id="btnTerminos"
                        onclick="toggleAccept('acepta_terminos','btnTerminos')">
                    <div class="check-box" id="boxTerminos">
                        @if(old('acepta_terminos'))
                            <i class="fa-solid fa-check" style="color:#fff;font-size:.75rem;"></i>
                        @endif
                    </div>
                    <div>
                        <span class="accept-label-title">
                            He leído y acepto todos los términos y cláusulas
                            <span style="color:#EF4444;">*</span>
                        </span>
                        <span class="accept-label-sub">Incluyendo esquema de pagos y políticas de daños.</span>
                    </div>
                    <input type="hidden" name="acepta_terminos" id="acepta_terminos"
                           value="{{ old('acepta_terminos', '0') }}">
                </button>
                @error('acepta_terminos')
                    <div style="font-size:.75rem;color:#EF4444;margin-top:-8px;margin-bottom:8px;">{{ $message }}</div>
                @enderror

                {{-- Checkbox 2: inventario ─────── --}}
                <button type="button"
                        class="accept-btn {{ old('acepta_inventario') ? 'checked' : '' }}"
                        id="btnInventario"
                        onclick="toggleAccept('acepta_inventario','btnInventario')">
                    <div class="check-box" id="boxInventario">
                        @if(old('acepta_inventario'))
                            <i class="fa-solid fa-check" style="color:#fff;font-size:.75rem;"></i>
                        @endif
                    </div>
                    <div>
                        <span class="accept-label-title">
                            Declaro que el inventario listado es veraz y completo
                            <span style="color:#EF4444;">*</span>
                        </span>
                        <span class="accept-label-sub">Artículos no declarados pueden generar cargos adicionales.</span>
                    </div>
                    <input type="hidden" name="acepta_inventario" id="acepta_inventario"
                           value="{{ old('acepta_inventario', '0') }}">
                </button>
                @error('acepta_inventario')
                    <div style="font-size:.75rem;color:#EF4444;margin-top:-8px;margin-bottom:8px;">{{ $message }}</div>
                @enderror
            </div>

            {{-- ── BOTONES ─────────────────────────────────────── --}}
            <div class="d-flex gap-3 mt-2">
                <a href="{{ route('cotizar.paso3') }}" class="btn-nav btn-back">
                    <i class="fa-solid fa-arrow-left"></i> Atrás
                </a>
                <button type="submit"
                        id="btnSubmit"
                        class="btn-nav btn-submit"
                        disabled>
                    <i class="fa-solid fa-file-contract"></i>
                    Generar contrato formal
                    <i class="fa-solid fa-circle-check" style="opacity:.8;"></i>
                </button>
            </div>

            <p id="submitHint" style="font-size:.72rem;color:#94A3B8;text-align:center;margin-top:10px;">
                Por favor, acepta ambos términos para continuar con la generación del contrato.
            </p>

        </form>
    </div>{{-- /form-card --}}

</div>{{-- /wizard-wrapper --}}

<script>
/* ══════════════════════════════════════════════════════════
   CHECKBOXES DE ACEPTACIÓN
══════════════════════════════════════════════════════════ */
function toggleAccept(inputId, btnId) {
    const input = document.getElementById(inputId);
    const btn   = document.getElementById(btnId);
    const box   = btn.querySelector('.check-box');

    const checked = input.value === '1';
    input.value   = checked ? '0' : '1';

    if (!checked) {
        btn.classList.add('checked');
        box.innerHTML = '<i class="fa-solid fa-check" style="color:#fff;font-size:.75rem;"></i>';
    } else {
        btn.classList.remove('checked');
        box.innerHTML = '';
    }

    evaluateSubmit();
}

function evaluateSubmit() {
    const t = document.getElementById('acepta_terminos').value   === '1';
    const i = document.getElementById('acepta_inventario').value === '1';
    const btn  = document.getElementById('btnSubmit');
    const hint = document.getElementById('submitHint');

    btn.disabled = !(t && i);

    if (t && i) {
        hint.textContent = 'Al generar el contrato, un asesor de FletesCun se pondrá en contacto contigo para confirmar la cotización final.';
    } else {
        hint.textContent = 'Por favor, acepta ambos términos para continuar con la generación del contrato.';
    }
}

/* Restaurar estado si hay old() values (validación server-side) */
window.addEventListener('DOMContentLoaded', evaluateSubmit);
</script>

</body>
</html>