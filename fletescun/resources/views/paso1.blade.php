{{-- resources/views/cotizador/paso1.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FletesCun · Paso 1 de 4</title>

    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

    <style>
        /* ── Base ─────────────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; }
        body {
            font-family: 'DM Sans', sans-serif;
            background: linear-gradient(135deg, #EFF6FF 0%, #F8FAFC 60%, #F0FDF4 100%);
            min-height: 100vh;
            padding: 24px 12px 60px;
        }

        /* ── Wrapper ──────────────────────────────────────────── */
        .wizard-wrapper {
            max-width: 820px;
            margin: 0 auto;
        }

        /* ── Header de marca ──────────────────────────────────── */
        .brand-header {
            text-align: center;
            margin-bottom: 24px;
        }
        .brand-logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        .brand-icon {
            width: 44px; height: 44px;
            background: linear-gradient(135deg, #2563EB, #1D4ED8);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            box-shadow: 0 4px 12px rgba(37,99,235,0.35);
        }
        .brand-text { text-align: left; line-height: 1.2; }
        .brand-name  { font-size: 1.25rem; font-weight: 700; color: #1E293B; }
        .brand-tagline { font-size: 0.72rem; color: #64748B; font-weight: 400; }

        /* ── Stepper ──────────────────────────────────────────── */
        .stepper-card {
            background: #ffffff;
            border-radius: 20px;
            padding: 20px 24px;
            box-shadow: 0 4px 20px rgba(15,23,42,0.06);
            margin-bottom: 20px;
        }
        .step-label {
            font-size: 0.72rem;
            font-weight: 600;
            color: #94A3B8;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            margin-bottom: 14px;
        }
        .step-label span { color: #2563EB; }

        .steps-row {
            display: flex;
            align-items: flex-start;
            gap: 0;
        }
        .step-item {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }
        /* Línea conectora */
        .step-item:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 16px;
            left: calc(50% + 14px);
            right: calc(-50% + 14px);
            height: 2px;
            background: #E2E8F0;
            z-index: 0;
        }
        .step-item.completed:not(:last-child)::after { background: #2563EB; }

        .step-circle {
            width: 32px; height: 32px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.8rem; font-weight: 700;
            border: 2px solid #E2E8F0;
            background: #F8FAFC;
            color: #94A3B8;
            position: relative; z-index: 1;
            transition: all 0.3s;
        }
        .step-item.active   .step-circle { background: #2563EB; border-color: #2563EB; color: #fff; box-shadow: 0 0 0 4px rgba(37,99,235,0.15); }
        .step-item.completed .step-circle { background: #2563EB; border-color: #2563EB; color: #fff; }

        .step-name {
            font-size: 0.68rem;
            font-weight: 500;
            color: #94A3B8;
            text-align: center;
            margin-top: 6px;
            line-height: 1.3;
        }
        .step-item.active    .step-name { color: #2563EB; font-weight: 700; }
        .step-item.completed .step-name { color: #1E293B; }

        /* ── Formulario principal ─────────────────────────────── */
        .form-card {
            background: #ffffff;
            border-radius: 24px;
            padding: 36px 32px;
            box-shadow: 0 8px 30px rgba(15,23,42,0.07);
        }
        @media (max-width: 576px) {
            .form-card { padding: 24px 18px; }
        }

        .section-icon {
            width: 40px; height: 40px;
            background: #EFF6FF; border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .section-title { font-size: 1.35rem; font-weight: 700; color: #1B3A6B; margin: 0; }
        .section-sub   { font-size: 0.88rem; color: #64748B; margin: 0; }

        /* ── Campos ───────────────────────────────────────────── */
        .field-label {
            font-size: 0.82rem; font-weight: 600; color: #1E293B;
            margin-bottom: 6px; display: block;
        }
        .field-label .req { color: #EF4444; margin-left: 2px; }
        .field-hint { font-size: 0.71rem; color: #94A3B8; margin-top: 5px; display: block; }

        .input-wrap { position: relative; }
        .input-wrap .field-icon {
            position: absolute; left: 13px; top: 50%; transform: translateY(-50%);
            color: #94A3B8; font-size: 15px; pointer-events: none; z-index: 2;
        }
        .field-input {
            width: 100%;
            padding: 12px 14px 12px 40px;
            border: 1.5px solid #E2E8F0;
            border-radius: 10px;
            background: #F8FAFC;
            color: #1E293B;
            font-size: 0.93rem;
            font-family: 'DM Sans', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s, background 0.2s;
            outline: none;
            -webkit-appearance: none;
        }
        .field-input::placeholder { color: #CBD5E1; }
        .field-input:focus {
            border-color: #2563EB;
            box-shadow: 0 0 0 3px rgba(37,99,235,0.1);
            background: #fff;
        }
        .field-input.is-invalid { border-color: #EF4444; }
        .field-input.is-invalid:focus { box-shadow: 0 0 0 3px rgba(239,68,68,0.1); }

        /* ── Separador sección ───────────────────────────────── */
        .divider-label {
            display: flex; align-items: center; gap: 12px; margin: 28px 0;
        }
        .divider-label::before,
        .divider-label::after { content: ''; flex: 1; height: 1px; background: #E2E8F0; }
        .divider-label span {
            font-size: 0.72rem; font-weight: 700; color: #94A3B8;
            text-transform: uppercase; letter-spacing: 0.07em; white-space: nowrap;
        }

        /* ── Mapa Google ─────────────────────────────────────── */
        .map-container {
            border-radius: 16px;
            overflow: hidden;
            border: 1.5px solid #E2E8F0;
            margin-top: 12px;
            box-shadow: 0 4px 16px rgba(15,23,42,0.06);
            position: relative;
        }
        #google-map { width: 100%; height: 260px; display: block; }

        .map-overlay-badge {
            position: absolute; bottom: 12px; left: 50%; transform: translateX(-50%);
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(6px);
            border: 1px solid #E2E8F0;
            border-radius: 20px;
            padding: 5px 14px;
            font-size: 0.72rem; font-weight: 600; color: #475569;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
            white-space: nowrap;
            display: none; /* se muestra cuando hay 2 puntos */
        }
        .map-overlay-badge i { color: #2563EB; margin-right: 4px; }

        /* ── Tarjetas de estacionamiento ─────────────────────── */
        .parking-card {
            border: 1.5px solid #E2E8F0;
            border-radius: 16px;
            padding: 18px;
            background: #F8FAFC;
            transition: border-color 0.2s, background 0.2s, box-shadow 0.2s;
        }
        .parking-btn {
            flex: 1; padding: 11px 8px;
            border-radius: 10px;
            border: 1.5px solid #CBD5E1;
            background: #fff;
            cursor: pointer;
            transition: all 0.2s;
            display: flex; flex-direction: column; align-items: center; gap: 4px;
        }
        .parking-btn:focus { outline: none; }
        .parking-btn i  { font-size: 1rem; color: #94A3B8; transition: color 0.2s; }
        .parking-btn span { font-size: 0.72rem; font-weight: 600; color: #64748B; transition: color 0.2s; }

        /* Si */
        .parking-btn.si.active { border-color: #16A34A; background: #DCFCE7; }
        .parking-btn.si.active i, .parking-btn.si.active span { color: #16A34A; }
        .parking-btn.si:hover:not(.active) { background: #F0FDF4; border-color: #86EFAC; }
        /* No */
        .parking-btn.no.active { border-color: #DC2626; background: #FEE2E2; }
        .parking-btn.no.active i, .parking-btn.no.active span { color: #DC2626; }
        .parking-btn.no:hover:not(.active) { background: #FEF2F2; border-color: #FECACA; }

        /* ── Errores de validación ───────────────────────────── */
        .error-msg { font-size: 0.75rem; color: #EF4444; margin-top: 4px; display: block; }

        /* ── Botón submit ────────────────────────────────────── */
        .btn-next {
            background: linear-gradient(135deg, #2563EB, #1D4ED8);
            color: #fff; border: none; border-radius: 14px;
            padding: 15px 28px; font-weight: 700; font-size: 1rem;
            width: 100%; margin-top: 28px;
            box-shadow: 0 6px 20px rgba(37,99,235,0.3);
            transition: transform 0.2s, box-shadow 0.2s;
            cursor: pointer;
            font-family: 'DM Sans', sans-serif;
        }
        .btn-next:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 28px rgba(37,99,235,0.35);
        }
        .btn-next:active { transform: translateY(0); }

        /* Autocomplete dropdown de Google */
        .pac-container { z-index: 9999 !important; font-family: 'DM Sans', sans-serif !important; border-radius: 10px !important; }
    </style>
</head>
<body>

<div class="wizard-wrapper">

    {{-- ── HEADER DE MARCA ─────────────────────────────────── --}}
    <div class="brand-header">
        <a class="brand-logo" href="#">
            <div class="brand-icon">
                <i class="fa-solid fa-truck-moving" style="color:#fff; font-size:1.1rem;"></i>
            </div>
            <div class="brand-text">
                <div class="brand-name">FletesCun</div>
                <div class="brand-tagline">Mudanzas confiables · Cancún</div>
            </div>
        </a>
    </div>

    {{-- ── STEPPER ──────────────────────────────────────────── --}}
    <div class="stepper-card">
        <div class="step-label">Paso <span>1</span> de 4</div>
        <div class="steps-row">
            <div class="step-item active">
                <div class="step-circle">1</div>
                <div class="step-name">Contacto<br>y Ruta</div>
            </div>
            <div class="step-item">
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
        $p1 = session('cotizador_paso1', []);
    @endphp

    {{-- ── TARJETA PRINCIPAL ────────────────────────────────── --}}
    <div class="form-card">

        {{-- Flash de éxito --}}
        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center gap-2 mb-4" role="alert">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        {{-- Encabezado de sección --}}
        <div class="d-flex align-items-center gap-3 mb-1">
            <div class="section-icon">
                <i class="fa-solid fa-user" style="color:#2563EB;"></i>
            </div>
            <div>
                <h2 class="section-title">Datos de Contacto y Ruta</h2>
            </div>
        </div>
        <p class="section-sub mb-4">Cuéntanos quién eres y de dónde a dónde necesitas tu mudanza.</p>

        <form action="{{ route('cotizar.paso1.post') }}" method="POST" id="formPaso1" novalidate>
            @csrf

            {{-- Campos ocultos para coordenadas y distancia --}}
            <input type="hidden" name="lat_origen"         id="latOrigen"       value="{{ old('lat_origen', session('lat_origen')) }}">
            <input type="hidden" name="lng_origen"         id="lngOrigen"       value="{{ old('lng_origen', session('lng_origen')) }}">
            <input type="hidden" name="lat_destino"        id="latDestino"      value="{{ old('lat_destino', session('lat_destino')) }}">
            <input type="hidden" name="lng_destino"        id="lngDestino"      value="{{ old('lng_destino', session('lng_destino')) }}">
            <input type="hidden" name="distancia_km"       id="distanciaKm"     value="{{ old('distancia_km', $p1['distancia_km'] ?? '') }}">
            <input type="hidden" name="estacionamiento_origen"  id="valEstOrigen"  value="{{ old('estacionamiento_origen', $p1['estacionamiento_origen'] ?? '') }}">
            <input type="hidden" name="estacionamiento_destino" id="valEstDestino" value="{{ old('estacionamiento_destino', $p1['estacionamiento_destino'] ?? '') }}">

            {{-- ── DATOS DE CONTACTO ─────────────────────────── --}}
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="field-label" for="nombre">Nombre Completo<span class="req">*</span></label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-user field-icon"></i>
                        <input type="text" id="nombre" name="nombre"
                               class="field-input @error('nombre') is-invalid @enderror"
                               placeholder="Ej. Juan Pérez López"
                               value="{{ old('nombre', $p1['nombre'] ?? '') }}" required autocomplete="name">
                    </div>
                    @error('nombre')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
                <div class="col-md-6">
                    <label class="field-label" for="telefono">Teléfono<span class="req">*</span></label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-phone field-icon"></i>
                        <input type="tel" id="telefono" name="telefono"
                               class="field-input @error('telefono') is-invalid @enderror"
                               placeholder="Ej. 998 123 4567"
                               value="{{ old('telefono', $p1['telefono'] ?? '') }}" required autocomplete="tel">
                    </div>
                    @error('telefono')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
            </div>

            <div class="mb-2">
                <label class="field-label" for="correo">Correo Electrónico<span class="req">*</span></label>
                <div class="input-wrap">
                    <i class="fa-solid fa-envelope field-icon"></i>
                    <input type="email" id="correo" name="correo"
                           class="field-input @error('correo') is-invalid @enderror"
                           placeholder="Ej. juan@correo.com"
                           value="{{ old('correo', $p1['correo'] ?? '') }}" required autocomplete="email">
                </div>
                <span class="field-hint">Te enviaremos tu cotización a este correo.</span>
                @error('correo')<span class="error-msg">{{ $message }}</span>@enderror
            </div>

            {{-- ── DIVIDER RUTA ──────────────────────────────── --}}
            <div class="divider-label"><span>Ruta de mudanza</span></div>

            {{-- ── DIRECCIONES ───────────────────────────────── --}}
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="field-label" for="direccion_origen">
                        Dirección de Origen<span class="req">*</span>
                    </label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-location-dot field-icon" style="color:#16A34A;"></i>
                        <input type="text" id="direccion_origen" name="direccion_origen"
                               class="field-input @error('direccion_origen') is-invalid @enderror"
                               placeholder="Ej. Av. Insurgentes Sur 1234, CDMX"
                               value="{{ old('direccion_origen', $p1['direccion_origen'] ?? '') }}" required autocomplete="off">
                    </div>
                    <span class="field-hint">Escribe la dirección o selecciónala en el mapa.</span>
                    @error('direccion_origen')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
                <div class="col-md-6">
                    <label class="field-label" for="direccion_destino">
                        Dirección de Destino<span class="req">*</span>
                    </label>
                    <div class="input-wrap">
                        <i class="fa-solid fa-location-dot field-icon" style="color:#DC2626;"></i>
                        <input type="text" id="direccion_destino" name="direccion_destino"
                               class="field-input @error('direccion_destino') is-invalid @enderror"
                               placeholder="Ej. Blvd. Kukulcán km 14, Cancún"
                               value="{{ old('direccion_destino', $p1['direccion_destino'] ?? '') }}" required autocomplete="off">
                    </div>
                    <span class="field-hint">Escribe la dirección o selecciónala en el mapa.</span>
                    @error('direccion_destino')<span class="error-msg">{{ $message }}</span>@enderror
                </div>
            </div>

            {{-- ── MAPA GOOGLE ───────────────────────────────── --}}
            <div class="map-container">
                <div id="google-map"></div>
                <div class="map-overlay-badge" id="distanceBadge">
                    <i class="fa-solid fa-route"></i>
                    <span id="distanceText">Calculando ruta…</span>
                </div>
            </div>
            <span class="field-hint mt-2 d-block">
                <i class="fa-solid fa-circle-info me-1" style="color:#2563EB;"></i>
                También puedes hacer clic en el mapa para colocar los puntos de origen y destino.
            </span>

            {{-- ── DIVIDER ESTACIONAMIENTO ───────────────────── --}}
            <div class="divider-label"><span>Accesibilidad de Estacionamiento</span></div>

            <div class="row g-3 mb-4">
                {{-- Origen --}}
                <div class="col-md-6">
                    <div class="parking-card" id="cardParkOrigen">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="fa-solid fa-location-dot" style="color:#16A34A; font-size:1.1rem;"></i>
                            <p style="margin:0; font-size:0.88rem; font-weight:600; color:#1E293B;">En Origen</p>
                        </div>
                        <p style="margin:0 0 10px; font-size:0.78rem; font-weight:600; color:#475569;">
                            ¿El camión puede estacionar a menos de 40 m?
                        </p>
                        <div class="d-flex gap-2">
                            <button type="button" class="parking-btn si" id="btnOrigenSi" onclick="setParking('origen','si')">
                                <i class="fa-solid fa-check"></i>
                                <span>Sí (&lt;40 m)</span>
                            </button>
                            <button type="button" class="parking-btn no" id="btnOrigenNo" onclick="setParking('origen','no')">
                                <i class="fa-solid fa-xmark"></i>
                                <span>No (&gt;40 m)</span>
                            </button>
                        </div>
                    </div>
                </div>
                {{-- Destino --}}
                <div class="col-md-6">
                    <div class="parking-card" id="cardParkDestino">
                        <div class="d-flex align-items-center gap-2 mb-3">
                            <i class="fa-solid fa-location-dot" style="color:#DC2626; font-size:1.1rem;"></i>
                            <p style="margin:0; font-size:0.88rem; font-weight:600; color:#1E293B;">En Destino</p>
                        </div>
                        <p style="margin:0 0 10px; font-size:0.78rem; font-weight:600; color:#475569;">
                            ¿El camión puede estacionar a menos de 40 m?
                        </p>
                        <div class="d-flex gap-2">
                            <button type="button" class="parking-btn si" id="btnDestinoSi" onclick="setParking('destino','si')">
                                <i class="fa-solid fa-check"></i>
                                <span>Sí (&lt;40 m)</span>
                            </button>
                            <button type="button" class="parking-btn no" id="btnDestinoNo" onclick="setParking('destino','no')">
                                <i class="fa-solid fa-xmark"></i>
                                <span>No (&gt;40 m)</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── FECHA ─────────────────────────────────────── --}}
            <div>
                <label class="field-label" for="fecha_ideal">Fecha Ideal de Mudanza<span class="req">*</span></label>
                <div class="input-wrap">
                    <i class="fa-solid fa-calendar-days field-icon"></i>
                    <input type="date" id="fecha_ideal" name="fecha_ideal"
                           class="field-input @error('fecha_ideal') is-invalid @enderror"
                           min="{{ date('Y-m-d') }}"
                           value="{{ old('fecha_ideal', $p1['fecha_ideal'] ?? '') }}" required>
                </div>
                <span class="field-hint">Podemos ajustar la fecha según disponibilidad.</span>
                @error('fecha_ideal')<span class="error-msg">{{ $message }}</span>@enderror
            </div>

            {{-- ── SUBMIT ────────────────────────────────────── --}}
            <button type="submit" class="btn-next" id="btnSubmit">
                Siguiente Paso &nbsp;<i class="fa-solid fa-arrow-right"></i>
            </button>

        </form>
    </div>{{-- /form-card --}}
</div>{{-- /wizard-wrapper --}}


{{-- ── SCRIPTS ──────────────────────────────────────────────── --}}
<script>
    /* ─────────────────────────────────────────────────────────
       CONFIG
       Coloca tu API Key de Google Maps aquí o mejor en .env:
         GOOGLE_MAPS_KEY=AIza...
       y usa: "{{ config('services.google_maps.key') }}"
    ───────────────────────────────────────────────────────── */
    const GOOGLE_MAPS_KEY = "{{ config('services.google_maps.key') }}";

    /* ─────────────────────────────────────────────────────────
       ESTADO DE LA APP
    ───────────────────────────────────────────────────────── */
    let map, directionsService, directionsRenderer;
    let markerOrigen  = null;
    let markerDestino = null;
    let acStep = 'origen'; // cuál campo se está llenando (para clic en mapa)

    const latOrigenField  = document.getElementById('latOrigen');
    const lngOrigenField  = document.getElementById('lngOrigen');
    const latDestinoField = document.getElementById('latDestino');
    const lngDestinoField = document.getElementById('lngDestino');
    const distKmField     = document.getElementById('distanciaKm');

    /* ─────────────────────────────────────────────────────────
       INICIALIZAR MAPA
    ───────────────────────────────────────────────────────── */
    function initMap() {
        const defaultCenter = { lat: 21.1619, lng: -86.8515 }; // Cancún

        map = new google.maps.Map(document.getElementById('google-map'), {
            zoom: 12,
            center: defaultCenter,
            mapTypeControl: false,
            streetViewControl: false,
            fullscreenControl: false,
            styles: [
                { featureType: 'poi', elementType: 'labels', stylers: [{ visibility: 'off' }] },
                { featureType: 'transit', elementType: 'labels', stylers: [{ visibility: 'off' }] }
            ]
        });

        directionsService  = new google.maps.DirectionsService();
        directionsRenderer = new google.maps.DirectionsRenderer({
            map,
            suppressMarkers: true,
            polylineOptions: { strokeColor: '#2563EB', strokeWeight: 5, strokeOpacity: 0.8 }
        });

        /* Autocomplete para Origen */
        const acOrigen = new google.maps.places.Autocomplete(
            document.getElementById('direccion_origen'),
            { componentRestrictions: { country: 'mx' } }
        );
        acOrigen.addListener('place_changed', () => {
            const place = acOrigen.getPlace();
            if (!place.geometry) return;
            setPoint('origen', place.geometry.location, place.formatted_address);
        });

        /* Autocomplete para Destino */
        const acDestino = new google.maps.places.Autocomplete(
            document.getElementById('direccion_destino'),
            { componentRestrictions: { country: 'mx' } }
        );
        acDestino.addListener('place_changed', () => {
            const place = acDestino.getPlace();
            if (!place.geometry) return;
            setPoint('destino', place.geometry.location, place.formatted_address);
        });

        /* Clic en el mapa: alterna entre colocar origen y destino */
        map.addListener('click', (e) => {
            geocodeLatLng(e.latLng);
        });

        /* Restaurar puntos si hay valores previos (old()) */
        const oldOrigen  = document.getElementById('direccion_origen').value;
        const oldDestino = document.getElementById('direccion_destino').value;
        if (oldOrigen && latOrigenField.value && lngOrigenField.value) {
            const ll = new google.maps.LatLng(
                parseFloat(latOrigenField.value), parseFloat(lngOrigenField.value)
            );
            setPoint('origen', ll, oldOrigen, false);
        }
        if (oldDestino && latDestinoField.value && lngDestinoField.value) {
            const ll = new google.maps.LatLng(
                parseFloat(latDestinoField.value), parseFloat(lngDestinoField.value)
            );
            setPoint('destino', ll, oldDestino, false);
        }
    }

    /* ─────────────────────────────────────────────────────────
       COLOCAR PUNTO EN EL MAPA
    ───────────────────────────────────────────────────────── */
    function setPoint(tipo, latLng, address, updateInput = true) {
        if (tipo === 'origen') {
            if (markerOrigen) markerOrigen.setMap(null);
            markerOrigen = new google.maps.Marker({
                position: latLng,
                map,
                title: 'Origen',
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 10,
                    fillColor: '#16A34A',
                    fillOpacity: 1,
                    strokeColor: '#fff',
                    strokeWeight: 3
                }
            });
            latOrigenField.value = latLng.lat();
            lngOrigenField.value = latLng.lng();
            if (updateInput) document.getElementById('direccion_origen').value = address;
            acStep = 'destino'; // próximo clic coloca el destino
        } else {
            if (markerDestino) markerDestino.setMap(null);
            markerDestino = new google.maps.Marker({
                position: latLng,
                map,
                title: 'Destino',
                icon: {
                    path: google.maps.SymbolPath.CIRCLE,
                    scale: 10,
                    fillColor: '#DC2626',
                    fillOpacity: 1,
                    strokeColor: '#fff',
                    strokeWeight: 3
                }
            });
            latDestinoField.value = latLng.lat();
            lngDestinoField.value = latLng.lng();
            if (updateInput) document.getElementById('direccion_destino').value = address;
            acStep = 'origen'; // cicla
        }

        /* Si tenemos los dos puntos, calcular ruta */
        if (markerOrigen && markerDestino) {
            calcularRuta();
        } else {
            map.panTo(latLng);
        }
    }

    /* ─────────────────────────────────────────────────────────
       GEOCODIFICACIÓN INVERSA (clic en mapa)
    ───────────────────────────────────────────────────────── */
    function geocodeLatLng(latLng) {
        const geocoder = new google.maps.Geocoder();
        geocoder.geocode({ location: latLng }, (results, status) => {
            if (status === 'OK' && results[0]) {
                setPoint(acStep, latLng, results[0].formatted_address);
            }
        });
    }

    /* ─────────────────────────────────────────────────────────
       CALCULAR Y DIBUJAR RUTA
    ───────────────────────────────────────────────────────── */
    function calcularRuta() {
        directionsService.route({
            origin:      markerOrigen.getPosition(),
            destination: markerDestino.getPosition(),
            travelMode:  google.maps.TravelMode.DRIVING
        }, (result, status) => {
            if (status === 'OK') {
                directionsRenderer.setDirections(result);
                const leg = result.routes[0].legs[0];
                const km  = (leg.distance.value / 1000).toFixed(1);
                distKmField.value = km;
                document.getElementById('distanceText').textContent =
                    `${leg.distance.text} · aprox. ${leg.duration.text}`;
                document.getElementById('distanceBadge').style.display = 'block';
            } else {
                // Si no hay ruta de manejo, al menos centra el mapa
                const bounds = new google.maps.LatLngBounds();
                bounds.extend(markerOrigen.getPosition());
                bounds.extend(markerDestino.getPosition());
                map.fitBounds(bounds);
            }
        });
    }

    /* ─────────────────────────────────────────────────────────
       CARGAR GOOGLE MAPS SDK DINÁMICAMENTE
    ───────────────────────────────────────────────────────── */
    (function loadGoogleMaps() {
        if (!GOOGLE_MAPS_KEY) {
            document.getElementById('google-map').innerHTML =
                '<div style="display:flex;align-items:center;justify-content:center;height:100%;color:#94A3B8;font-size:0.85rem;gap:8px;">' +
                '<i class="fa-solid fa-triangle-exclamation" style="color:#F59E0B;"></i>' +
                'Configura GOOGLE_MAPS_KEY en tu .env para activar el mapa.</div>';
            return;
        }
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${GOOGLE_MAPS_KEY}&libraries=places&callback=initMap&loading=async`;
        script.defer = true;
        script.async = true;
        document.head.appendChild(script);
    })();

    /* ─────────────────────────────────────────────────────────
       ESTACIONAMIENTO
    ───────────────────────────────────────────────────────── */
    function setParking(lugar, valor) {
        const hiddenId = lugar === 'origen' ? 'valEstOrigen' : 'valEstDestino';
        const btnSiId  = lugar === 'origen' ? 'btnOrigenSi'  : 'btnDestinoSi';
        const btnNoId  = lugar === 'origen' ? 'btnOrigenNo'  : 'btnDestinoNo';
        const cardId   = lugar === 'origen' ? 'cardParkOrigen' : 'cardParkDestino';

        document.getElementById(hiddenId).value = valor;
        document.getElementById(btnSiId).classList.remove('active');
        document.getElementById(btnNoId).classList.remove('active');
        document.getElementById(valor === 'si' ? btnSiId : btnNoId).classList.add('active');

        const card = document.getElementById(cardId);
        if (valor === 'si') {
            card.style.borderColor    = '#86EFAC';
            card.style.backgroundColor = '#F0FDF4';
            card.style.boxShadow       = '0 4px 16px rgba(34,197,94,0.1)';
        } else {
            card.style.borderColor    = '#FECACA';
            card.style.backgroundColor = '#FEF2F2';
            card.style.boxShadow       = '0 4px 16px rgba(220,38,38,0.08)';
        }
    }

    /* ─────────────────────────────────────────────────────────
       RESTAURAR ESTADO EN RECARGA (validación Laravel old())
    ───────────────────────────────────────────────────────── */
    window.addEventListener('DOMContentLoaded', () => {
        const oldEstO = document.getElementById('valEstOrigen').value;
        const oldEstD = document.getElementById('valEstDestino').value;
        if (oldEstO) setParking('origen',  oldEstO);
        if (oldEstD) setParking('destino', oldEstD);
    });
</script>

</body>
</html>