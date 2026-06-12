{{-- resources/views/cotizador/paso2.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FletesCun · Paso 2 de 4</title>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: linear-gradient(135deg, #EFF6FF 0%, #F8FAFC 60%, #F0FDF4 100%);
            min-height: 100vh;
            padding: 24px 12px 60px;
        }

        /* ── Wrapper ─────────────────────────────────────────── */
        .wizard-wrapper { max-width: 820px; margin: 0 auto; }

        /* ── Brand ───────────────────────────────────────────── */
        .brand-header { text-align: center; margin-bottom: 24px; }
        .brand-logo   { display: inline-flex; align-items: center; gap: 10px; text-decoration: none; }
        .brand-icon   {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, #2563EB, #1D4ED8);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 12px rgba(37,99,235,0.35);
        }
        .brand-name    { font-size: 1.25rem; font-weight: 700; color: #1E293B; line-height: 1.2; }
        .brand-tagline { font-size: 0.72rem; color: #64748B; }

        /* ── Stepper ─────────────────────────────────────────── */
        .stepper-card {
            background: #fff; border-radius: 20px;
            padding: 20px 24px;
            box-shadow: 0 4px 20px rgba(15,23,42,0.06);
            margin-bottom: 20px;
        }
        .step-label { font-size: 0.72rem; font-weight: 600; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.06em; margin-bottom: 14px; }
        .step-label span { color: #2563EB; }
        .steps-row  { display: flex; align-items: flex-start; }
        .step-item  { flex: 1; display: flex; flex-direction: column; align-items: center; position: relative; }
        .step-item:not(:last-child)::after {
            content: ''; position: absolute;
            top: 15px; left: calc(50% + 14px); right: calc(-50% + 14px);
            height: 2px; background: #E2E8F0; z-index: 0;
        }
        .step-item.completed:not(:last-child)::after { background: #2563EB; }
        .step-circle {
            width: 32px; height: 32px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.8rem; font-weight: 700;
            border: 2px solid #E2E8F0; background: #F8FAFC; color: #94A3B8;
            position: relative; z-index: 1; transition: all 0.3s;
        }
        .step-item.active    .step-circle { background: #2563EB; border-color: #2563EB; color: #fff; box-shadow: 0 0 0 4px rgba(37,99,235,0.15); }
        .step-item.completed .step-circle { background: #2563EB; border-color: #2563EB; color: #fff; }
        .step-name { font-size: 0.68rem; font-weight: 500; color: #94A3B8; text-align: center; margin-top: 6px; line-height: 1.3; }
        .step-item.active    .step-name { color: #2563EB; font-weight: 700; }
        .step-item.completed .step-name { color: #1E293B; }

        /* ── Tarjeta principal ───────────────────────────────── */
        .form-card {
            background: #fff; border-radius: 24px;
            padding: 36px 32px;
            box-shadow: 0 8px 30px rgba(15,23,42,0.07);
        }
        @media (max-width: 576px) { .form-card { padding: 24px 18px; } }

        .section-icon  { width: 40px; height: 40px; background: #EFF6FF; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .section-title { font-size: 1.35rem; font-weight: 700; color: #1B3A6B; margin: 0; }
        .section-sub   { font-size: 0.88rem; color: #64748B; margin: 0; }

        /* ── Divider ─────────────────────────────────────────── */
        .divider-label { display: flex; align-items: center; gap: 12px; margin: 28px 0; }
        .divider-label::before, .divider-label::after { content: ''; flex: 1; height: 1px; background: #E2E8F0; }
        .divider-label span { font-size: 0.72rem; font-weight: 700; color: #94A3B8; text-transform: uppercase; letter-spacing: 0.07em; white-space: nowrap; }

        /* ── Tarjetas de origen/destino ──────────────────────── */
        .logistics-card-origen  { background: #F0FDF4; border: 1.5px solid #BBF7D0; border-radius: 18px; padding: 22px; }
        .logistics-card-destino { background: #FFF1F2; border: 1.5px solid #FECDD3; border-radius: 18px; padding: 22px; }
        .point-label { font-size: 0.78rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; }
        .point-dot   { width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0; }

        /* ── Select custom ───────────────────────────────────── */
        .field-label { font-size: 0.82rem; font-weight: 600; color: #374151; margin-bottom: 6px; display: block; }
        .field-label .req { color: #EF4444; }
        .select-custom {
            width: 100%; padding: 12px 36px 12px 14px;
            border: 1.5px solid #E2E8F0; border-radius: 10px;
            background: #F8FAFC url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%2394A3B8' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") no-repeat right 12px center;
            color: #1E293B; font-size: 0.93rem;
            font-family: 'DM Sans', sans-serif;
            appearance: none; -webkit-appearance: none;
            outline: none; cursor: pointer;
            transition: border-color 0.2s, box-shadow 0.2s;
        }
        .select-custom:focus { border-color: #2563EB; box-shadow: 0 0 0 3px rgba(37,99,235,0.1); background-color: #fff; }
        .select-custom.green:focus { border-color: #22C55E; box-shadow: 0 0 0 3px rgba(34,197,94,0.1); }
        .select-custom.red:focus   { border-color: #EF4444; box-shadow: 0 0 0 3px rgba(239,68,68,0.1); }
        .select-custom.is-invalid  { border-color: #EF4444; }
        .error-msg { font-size: 0.75rem; color: #EF4444; margin-top: 4px; display: block; }

        /* ── Toggle switch ───────────────────────────────────── */
        .toggle-row {
            display: flex; align-items: center; justify-content: space-between;
            padding: 12px 14px; border-radius: 12px;
            background: #F8FAFC; border: 1.5px solid #E2E8F0;
            gap: 10px;
        }
        .toggle-row .toggle-label { font-size: 0.88rem; color: #374151; font-weight: 500; }
        .toggle-track {
            width: 44px; height: 24px; border-radius: 12px;
            border: none; cursor: pointer;
            position: relative; flex-shrink: 0;
            transition: background-color 0.2s;
        }
        .toggle-track.on  { background: #2563EB; }
        .toggle-track.off { background: #CBD5E1; }
        .toggle-thumb {
            position: absolute; top: 2px;
            width: 20px; height: 20px; border-radius: 50%;
            background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.2);
            transition: left 0.2s;
        }
        .toggle-track.on  .toggle-thumb { left: 22px; }
        .toggle-track.off .toggle-thumb { left: 2px; }

        /* ── Servicios adicionales ───────────────────────────── */
        .service-check {
            display: flex; align-items: center; gap: 14px;
            padding: 13px 16px; border-radius: 12px;
            border: 1.5px solid #E2E8F0; background: #F8FAFC;
            cursor: pointer; transition: all 0.18s;
            width: 100%; text-align: left;
        }
        .service-check:hover { background: #F0F9FF; border-color: #BAE6FD; }
        .service-check.active { background: #EFF6FF; border-color: #2563EB; }
        .check-box {
            width: 24px; height: 24px; border-radius: 6px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; transition: background 0.18s;
        }
        .check-box.off { background: #E2E8F0; }
        .check-box.on  { background: #2563EB; }
        .service-name  { font-size: 0.88rem; font-weight: 500; color: #1E293B; flex: 1; }
        .service-emoji { font-size: 1.2rem; }

        /* ── Tarjetas de modalidad ───────────────────────────── */
        .modality-card {
            padding: 20px; border-radius: 14px;
            border: 2px solid #E2E8F0; background: #fff;
            cursor: pointer; transition: all 0.2s;
            width: 100%; text-align: left;
        }
        .modality-card:hover { border-color: #BAE6FD; }
        .modality-card.exclusivo.active {
            border-color: #2563EB;
            background: rgba(37,99,235,0.03);
            box-shadow: 0 0 0 4px rgba(37,99,235,0.1);
        }
        .modality-card.compartido.active {
            border-color: #059669;
            background: rgba(5,150,105,0.03);
            box-shadow: 0 0 0 4px rgba(5,150,105,0.1);
        }
        .modality-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            transition: background 0.2s;
        }
        .modality-card.exclusivo .modality-icon  { background: #EFF6FF; }
        .modality-card.exclusivo.active .modality-icon { background: rgba(37,99,235,0.12); }
        .modality-card.compartido .modality-icon { background: #ECFDF5; }
        .modality-card.compartido.active .modality-icon { background: rgba(5,150,105,0.12); }

        .modality-check {
            width: 22px; height: 22px; border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
        }
        .modality-card.exclusivo.active  .modality-check { background: #2563EB; }
        .modality-card.compartido.active .modality-check { background: #059669; }

        .modality-title { font-size: 1rem; font-weight: 700; margin-bottom: 3px; }
        .modality-card.exclusivo.active  .modality-title { color: #2563EB; }
        .modality-card.compartido.active .modality-title { color: #059669; }
        .modality-card:not(.active) .modality-title { color: #1E293B; }

        .modality-sub  { font-size: 0.78rem; color: #64748B; line-height: 1.4; margin-bottom: 12px; }
        .modality-feat { font-size: 0.76rem; color: #94A3B8; display: flex; align-items: center; gap: 6px; margin-bottom: 5px; }
        .modality-card.active .modality-feat { color: #374151; }
        .feat-dot { width: 5px; height: 5px; border-radius: 50%; background: #CBD5E1; flex-shrink: 0; }
        .modality-card.exclusivo.active  .feat-dot { background: #2563EB; }
        .modality-card.compartido.active .feat-dot { background: #059669; }

        .badge-pill {
            font-size: 0.68rem; font-weight: 700; padding: 2px 9px;
            border-radius: 20px; text-transform: uppercase; letter-spacing: 0.04em;
        }
        .badge-exclusivo  { background: rgba(37,99,235,0.1);  color: #2563EB; }
        .badge-compartido { background: rgba(5,150,105,0.1);  color: #059669; }

        /* ── Submit ──────────────────────────────────────────── */
        .btn-nav {
            border-radius: 13px; padding: 14px 24px;
            font-weight: 700; font-size: 0.95rem;
            font-family: 'DM Sans', sans-serif;
            border: none; cursor: pointer; transition: all 0.2s;
            display: flex; align-items: center; justify-content: center; gap: 8px;
        }
        .btn-back {
            background: #F1F5F9; color: #475569;
            border: 1.5px solid #E2E8F0;
        }
        .btn-back:hover { background: #E2E8F0; }
        .btn-next {
            flex: 1;
            background: linear-gradient(135deg, #2563EB, #1D4ED8);
            color: #fff;
            box-shadow: 0 6px 20px rgba(37,99,235,0.28);
        }
        .btn-next:hover { transform: translateY(-2px); box-shadow: 0 10px 28px rgba(37,99,235,0.33); }
        .btn-next:active { transform: translateY(0); }
    </style>
</head>
<body>

<div class="wizard-wrapper">

    {{-- ── BRAND ────────────────────────────────────────────── --}}
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

    {{-- ── STEPPER ──────────────────────────────────────────── --}}
    <div class="stepper-card">
        <div class="step-label">Paso <span>2</span> de 4</div>
        <div class="steps-row">
            <div class="step-item completed">
                <div class="step-circle"><i class="fa-solid fa-check" style="font-size:0.75rem;"></i></div>
                <div class="step-name">Contacto<br>y Ruta</div>
            </div>
            <div class="step-item active">
                <div class="step-circle">2</div>
                <div class="step-name">Logística<br>y Detalles</div>
            </div>
            <div class="step-item">
                <div class="step-circle">3</div>
                <div class="step-name">Inventario<br>y Fotos</div>
            </div>
            <div class="step-item">
                <div class="step-circle">4</div>
                <div class="step-name">Cotización<br>Final</div>
            </div>
        </div>
    </div>

    @php
        $p2 = session('cotizador_paso2', []);
        $modalidadValue = old('modalidad', $p2['modalidad'] ?? 'Exclusivo');
    @endphp

    {{-- ── FORM CARD ─────────────────────────────────────────── --}}
    <div class="form-card">

        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Header --}}
        <div class="d-flex align-items-center gap-3 mb-1">
            <div class="section-icon">
                <i class="fa-solid fa-building" style="color:#2563EB;"></i>
            </div>
            <h2 class="section-title">Detalles Logísticos</h2>
        </div>
        <p class="section-sub mb-4">Esta información nos ayuda a planificar el acceso y el equipo necesario.</p>

        <form action="{{ route('cotizar.paso2.post') }}" method="POST" id="formPaso2" novalidate>
            @csrf

            {{-- ── PISOS Y ELEVADORES ───────────────────────── --}}
            <div class="row g-3 mb-2">

                {{-- Origen --}}
                <div class="col-md-6">
                    <div class="logistics-card-origen h-100">
                        <div class="d-flex align-items-center gap-2 mb-4">
                            <div class="point-dot" style="background:#22C55E;"></div>
                            <span class="point-label" style="color:#15803D;">Origen — Carga</span>
                        </div>

                        <label class="field-label" for="piso_origen">
                            Piso de carga <span class="req">*</span>
                        </label>
                        <select id="piso_origen" name="piso_origen"
                                class="select-custom green @error('piso_origen') is-invalid @enderror"
                                required>
                            <option value="">Seleccionar piso</option>
                            @foreach(['Planta baja','1er piso','2do piso','3er piso','4to piso','5to piso','6to piso o más'] as $piso)
                                <option value="{{ $piso }}" {{ old('piso_origen', $p2['piso_origen'] ?? '') == $piso ? 'selected' : '' }}>
                                    {{ $piso }}
                                </option>
                            @endforeach
                        </select>
                        @error('piso_origen')<span class="error-msg">{{ $message }}</span>@enderror

                        <div class="toggle-row mt-3" id="rowElevOrigen">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-elevator"
                                   id="iconElevOrigen"
                                   style="color:#94A3B8; font-size:0.95rem;"></i>
                                <span class="toggle-label">¿Cuenta con elevador?</span>
                            </div>
                            <button type="button"
                                    class="toggle-track off"
                                    id="toggleElevOrigen"
                                    onclick="toggleSwitch('elevador_origen', 'toggleElevOrigen', 'iconElevOrigen')"
                                    aria-pressed="false">
                                <span class="toggle-thumb"></span>
                            </button>
                            <input type="hidden" name="elevador_origen" id="elevador_origen"
                                value="{{ old('elevador_origen', $p2['elevador_origen'] ?? '0') }}">
                        </div>
                    </div>
                </div>

                {{-- Destino --}}
                <div class="col-md-6">
                    <div class="logistics-card-destino h-100">
                        <div class="d-flex align-items-center gap-2 mb-4">
                            <div class="point-dot" style="background:#EF4444;"></div>
                            <span class="point-label" style="color:#B91C1C;">Destino — Descarga</span>
                        </div>

                        <label class="field-label" for="piso_destino">
                            Piso de descarga <span class="req">*</span>
                        </label>
                        <select id="piso_destino" name="piso_destino"
                                class="select-custom red @error('piso_destino') is-invalid @enderror"
                                required>
                            <option value="">Seleccionar piso</option>
                            @foreach(['Planta baja','1er piso','2do piso','3er piso','4to piso','5to piso','6to piso o más'] as $piso)
                                <option value="{{ $piso }}" {{ old('piso_destino', $p2['piso_destino'] ?? '') == $piso ? 'selected' : '' }}>
                                    {{ $piso }}
                                </option>
                            @endforeach
                        </select>
                        @error('piso_destino')<span class="error-msg">{{ $message }}</span>@enderror

                        <div class="toggle-row mt-3" id="rowElevDestino">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-elevator"
                                   id="iconElevDestino"
                                   style="color:#94A3B8; font-size:0.95rem;"></i>
                                <span class="toggle-label">¿Cuenta con elevador?</span>
                            </div>
                            <button type="button"
                                    class="toggle-track off"
                                    id="toggleElevDestino"
                                    onclick="toggleSwitch('elevador_destino', 'toggleElevDestino', 'iconElevDestino')"
                                    aria-pressed="false">
                                <span class="toggle-thumb"></span>
                            </button>
                            <input type="hidden" name="elevador_destino" id="elevador_destino"
                                value="{{ old('elevador_destino', $p2['elevador_destino'] ?? '0') }}">
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── SERVICIOS ADICIONALES ───────────────────── --}}
            <div class="divider-label"><span>Servicios adicionales</span></div>

            <label class="field-label mb-3">
                Servicios adicionales
                <span style="color:#94A3B8; font-weight:400;">(Opcional)</span>
            </label>

            @php
                $servicios = [
                    ['key' => 'servicio_embalaje',   'label' => 'Embalaje de cajas',        'emoji' => '📦'],
                    ['key' => 'servicio_desmontaje',  'label' => 'Desmontaje de muebles',    'emoji' => '🔧'],
                    ['key' => 'servicio_volado',      'label' => 'Volado / acarreo externo', 'emoji' => '🚚'],
                    ['key' => 'servicio_seguro',      'label' => 'Seguro de carga',          'emoji' => '🛡️'],
                ]
            @endphp

            <div class="d-flex flex-column gap-2 mb-2">
                @foreach($servicios as $srv)
                    @php $isActive = old($srv['key'], $p2[$srv['key']] ?? '0') === '1' @endphp
                    <button type="button"
                            class="service-check {{ $isActive ? 'active' : '' }}"
                            id="btn_{{ $srv['key'] }}"
                            onclick="toggleService('{{ $srv['key'] }}')">
                        <div class="check-box {{ $isActive ? 'on' : 'off' }}" id="box_{{ $srv['key'] }}">
                            @if($isActive)
                                <svg width="14" height="14" viewBox="0 0 12 12" fill="none">
                                    <path d="M2 6l3 3 5-5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            @endif
                        </div>
                        <span class="service-name">{{ $srv['label'] }}</span>
                        <span class="service-emoji">{{ $srv['emoji'] }}</span>
                        <input type="hidden" name="{{ $srv['key'] }}" id="{{ $srv['key'] }}"
                               value="{{ $isActive ? '1' : '0' }}">
                    </button>
                @endforeach
            </div>
            <p style="font-size:0.72rem; color:#94A3B8; margin-top:4px;">
                Selecciona los servicios adicionales que desees contratar para tu mudanza.
            </p>

            {{-- ── MODALIDAD ───────────────────────────────── --}}
            <div class="divider-label"><span>Modalidad de servicio</span></div>

            <label class="field-label mb-3">
                Modalidad de servicio <span class="req">*</span>
            </label>
            @error('modalidad')<span class="error-msg mb-2 d-block">{{ $message }}</span>@enderror

                 <input type="hidden" name="modalidad" id="modalidadInput"
                     value="{{ $modalidadValue }}">

            <div class="row g-3">
                {{-- Exclusivo --}}
                <div class="col-md-6">
                    <button type="button"
                            class="modality-card exclusivo {{ $modalidadValue === 'Exclusivo' ? 'active' : '' }}"
                            id="cardExclusivo"
                            onclick="setModalidad('Exclusivo')">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="modality-icon">
                                <i class="fa-solid fa-bolt"
                                   id="iconExclusivo"
                                                                     style="font-size:1.1rem; color:{{ $modalidadValue === 'Exclusivo' ? '#2563EB' : '#94A3B8' }};"></i>
                            </div>
                            <div id="checkExclusivo"
                                                                 class="modality-check {{ $modalidadValue === 'Exclusivo' ? '' : 'd-none' }}">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                    <path d="M2 6l3 3 5-5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                                                        <span class="badge-pill badge-exclusivo {{ $modalidadValue === 'Exclusivo' ? 'd-none' : '' }}"
                                  id="badgeExclusivo">
                                Más rápido
                            </span>
                        </div>
                        <div class="modality-title">Exclusivo</div>
                        <div class="modality-sub">Camión dedicado. Traslado directo al destino. Mayor rapidez y seguridad.</div>
                        @foreach(['Camión 100% para tu mudanza','Traslado directo sin escalas','Mayor rapidez en entrega','Máxima seguridad de tus bienes'] as $feat)
                            <div class="modality-feat">
                                <div class="feat-dot"></div>{{ $feat }}
                            </div>
                        @endforeach
                    </button>
                </div>

                {{-- Compartido --}}
                <div class="col-md-6">
                    <button type="button"
                            class="modality-card compartido {{ $modalidadValue === 'Compartido' ? 'active' : '' }}"
                            id="cardCompartido"
                            onclick="setModalidad('Compartido')">
                        <div class="d-flex align-items-start justify-content-between mb-3">
                            <div class="modality-icon">
                                <i class="fa-solid fa-users"
                                   id="iconCompartido"
                                                                     style="font-size:1.1rem; color:{{ $modalidadValue === 'Compartido' ? '#059669' : '#94A3B8' }};"></i>
                            </div>
                            <div id="checkCompartido"
                                                                 class="modality-check {{ $modalidadValue === 'Compartido' ? '' : 'd-none' }}">
                                <svg width="12" height="12" viewBox="0 0 12 12" fill="none">
                                    <path d="M2 6l3 3 5-5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </div>
                                                        <span class="badge-pill badge-compartido {{ $modalidadValue === 'Compartido' ? 'd-none' : '' }}"
                                  id="badgeCompartido">
                                Menor costo
                            </span>
                        </div>
                        <div class="modality-title">Compartido</div>
                        <div class="modality-sub">Menor costo. La unidad transporta otras cargas. Tiempo estimado de 4 a 12 días.</div>
                        @foreach(['Precio reducido considerablemente','La unidad transporta otras cargas','Tiempo estimado de 4 a 12 días','Excelente opción para presupuesto ajustado'] as $feat)
                            <div class="modality-feat">
                                <div class="feat-dot"></div>{{ $feat }}
                            </div>
                        @endforeach
                    </button>
                </div>
            </div>

            {{-- ── BOTONES DE NAVEGACIÓN ────────────────────── --}}
            <div class="d-flex gap-3 mt-4">
                <a href="{{ route('cotizar.paso1') }}" class="btn-nav btn-back" style="text-decoration:none; width:130px;">
                    <i class="fa-solid fa-arrow-left"></i> Atrás
                </a>
                <button type="submit" class="btn-nav btn-next">
                    Siguiente Paso <i class="fa-solid fa-arrow-right"></i>
                </button>
            </div>

        </form>
    </div>
</div>

<script>
    /* ─── Toggle elevador ───────────────────────────────────── */
    function toggleSwitch(hiddenId, trackId, iconId) {
        const hidden = document.getElementById(hiddenId);
        const track  = document.getElementById(trackId);
        const icon   = document.getElementById(iconId);
        const isOn   = hidden.value === '1';

        hidden.value = isOn ? '0' : '1';
        track.classList.toggle('on',  !isOn);
        track.classList.toggle('off',  isOn);
        track.setAttribute('aria-pressed', String(!isOn));
        icon.style.color = isOn ? '#94A3B8' : '#2563EB';
    }

    /* ─── Toggle servicio adicional ─────────────────────────── */
    function toggleService(key) {
        const hidden = document.getElementById(key);
        const btn    = document.getElementById('btn_' + key);
        const box    = document.getElementById('box_' + key);
        const isOn   = hidden.value === '1';

        hidden.value = isOn ? '0' : '1';
        btn.classList.toggle('active', !isOn);
        box.classList.toggle('on',  !isOn);
        box.classList.toggle('off',  isOn);
        box.innerHTML = isOn ? '' :
            `<svg width="14" height="14" viewBox="0 0 12 12" fill="none">
                <path d="M2 6l3 3 5-5" stroke="#fff" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
             </svg>`;
    }

    /* ─── Selección de modalidad ────────────────────────────── */
    function setModalidad(valor) {
        document.getElementById('modalidadInput').value = valor;

        // Exclusivo
        const cExc = document.getElementById('cardExclusivo');
        const isExc = valor === 'Exclusivo';
        cExc.classList.toggle('active', isExc);
        document.getElementById('iconExclusivo').style.color  = isExc ? '#2563EB' : '#94A3B8';
        document.getElementById('checkExclusivo').classList.toggle('d-none', !isExc);
        document.getElementById('badgeExclusivo').classList.toggle('d-none',  isExc);

        // Compartido
        const cCom = document.getElementById('cardCompartido');
        const isCom = valor === 'Compartido';
        cCom.classList.toggle('active', isCom);
        document.getElementById('iconCompartido').style.color  = isCom ? '#059669' : '#94A3B8';
        document.getElementById('checkCompartido').classList.toggle('d-none', !isCom);
        document.getElementById('badgeCompartido').classList.toggle('d-none',  isCom);
    }

    /* ─── Restaurar toggles desde old() en recarga ──────────── */
    window.addEventListener('DOMContentLoaded', () => {
        ['elevador_origen', 'elevador_destino'].forEach(id => {
            if (document.getElementById(id).value === '1') {
                const trackId = id === 'elevador_origen' ? 'toggleElevOrigen' : 'toggleElevDestino';
                const iconId  = id === 'elevador_origen' ? 'iconElevOrigen'   : 'iconElevDestino';
                const track   = document.getElementById(trackId);
                track.classList.replace('off', 'on');
                track.setAttribute('aria-pressed', 'true');
                document.getElementById(iconId).style.color = '#2563EB';
            }
        });
    });
</script>

</body>
</html>