{{-- resources/views/cotizador/paso3.blade.php --}}
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FletesCun · Paso 3 de 4</title>

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

        /* ── Form card ── */
        .form-card     { background:#fff;border-radius:24px;padding:36px 32px;box-shadow:0 8px 30px rgba(15,23,42,.07); }
        @media(max-width:576px){ .form-card { padding:24px 18px; } }
        .section-icon  { width:40px;height:40px;background:#EFF6FF;border-radius:10px;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
        .section-title { font-size:1.35rem;font-weight:700;color:#1B3A6B;margin:0; }
        .section-sub   { font-size:.88rem;color:#64748B;margin:0; }

        /* ── Divider ── */
        .divider-label { display:flex;align-items:center;gap:12px;margin:28px 0; }
        .divider-label::before,.divider-label::after { content:'';flex:1;height:1px;background:#E2E8F0; }
        .divider-label span { font-size:.72rem;font-weight:700;color:#94A3B8;text-transform:uppercase;letter-spacing:.07em;white-space:nowrap; }

        /* ── Resumen catálogo ── */
        .inv-header    { display:flex;align-items:center;justify-content:space-between;margin-bottom:14px; }
        .inv-count     { font-size:.72rem;color:#64748B;background:#F1F5F9;padding:3px 10px;border-radius:20px; }
        .inv-item      { display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-radius:10px;background:#F8FAFC;border:1.5px solid #E2E8F0;margin-bottom:8px;gap:10px; }
        .inv-item-name { font-size:.88rem;font-weight:500;color:#1E293B;flex:1;min-width:0; }
        .inv-item-m3   { font-size:.72rem;color:#94A3B8;margin-left:6px; }
        .qty-wrap      { display:flex;align-items:center;gap:6px;flex-shrink:0; }
        .qty-btn       { width:28px;height:28px;border-radius:6px;border:1.5px solid;cursor:pointer;display:flex;align-items:center;justify-content:center;background:#fff;transition:opacity .15s; }
        .qty-btn:hover { opacity:.75; }
        .qty-btn.minus { border-color:#FECDD3;background:#FEF2F2;color:#EF4444; }
        .qty-btn.plus  { border-color:#93C5FD;background:#EFF6FF;color:#2563EB; }
        .qty-num       { font-size:.88rem;font-weight:700;color:#1E293B;min-width:22px;text-align:center; }
        .del-btn       { width:28px;height:28px;border-radius:6px;border:1.5px solid #FECDD3;background:#FEF2F2;color:#EF4444;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background .15s;margin-left:4px; }
        .del-btn:hover { background:#FECDD3; }

        /* ── Acordeón catálogo ── */
        .cat-accordion { border:1.5px solid #E2E8F0;border-radius:12px;overflow:hidden;background:#fff;margin-bottom:8px; }
        .cat-header    { width:100%;display:flex;align-items:center;justify-content:space-between;padding:14px 16px;background:#F8FAFC;cursor:pointer;border:none;font-size:.92rem;font-weight:600;color:#1B3A6B;transition:background .15s;font-family:'DM Sans',sans-serif; }
        .cat-header:hover { background:#F1F5F9; }
        .cat-header.open  { background:#EFF6FF; }
        .cat-chevron   { font-size:.8rem;color:#94A3B8;transition:transform .2s; }
        .cat-header.open .cat-chevron { transform:rotate(180deg); }
        .cat-body      { display:none;padding:14px;border-top:1.5px solid #E2E8F0;background:#FAFBFC; }
        .cat-body.open { display:grid;grid-template-columns:repeat(auto-fill,minmax(140px,1fr));gap:8px; }
        @media(max-width:480px){ .cat-body.open { grid-template-columns:repeat(auto-fill,minmax(120px,1fr)); } }
        .cat-item      { display:flex;flex-direction:column;align-items:center;gap:5px;padding:10px 8px;border-radius:10px;border:1.5px solid #E2E8F0;background:#fff;cursor:pointer;transition:all .15s;text-align:center; }
        .cat-item:hover { border-color:#2563EB;background:#EFF6FF; }
        .cat-item-name { font-size:.77rem;font-weight:500;color:#1E293B;line-height:1.3; }
        .cat-item-m3   { font-size:.7rem;color:#94A3B8;display:flex;align-items:center;gap:3px; }

        /* ── Artículos especiales ── */
        .special-header { font-size:.85rem;font-weight:600;color:#1E293B;display:flex;align-items:center;gap:6px; }
        .special-table-head { display:grid;grid-template-columns:60px 1fr 1fr 36px;gap:8px;padding:0 12px;margin-bottom:6px; }
        .special-table-head span { font-size:.72rem;font-weight:700;color:#94A3B8;text-transform:uppercase;letter-spacing:.05em; }
        .special-row   { display:grid;grid-template-columns:60px 1fr 1fr 36px;gap:8px;align-items:center;padding:10px 12px;border-radius:12px;border:1.5px solid #E2E8F0;margin-bottom:8px; }
        @media(max-width:600px){
            .special-table-head,.special-row { grid-template-columns:52px 1fr 36px; }
            .sp-obs { display:none; }
        }
        .field-input-sm { width:100%;padding:8px 10px;border-radius:8px;border:1.5px solid #E2E8F0;background:#fff;color:#1E293B;font-size:.88rem;font-family:'DM Sans',sans-serif;outline:none;transition:border-color .2s,box-shadow .2s; }
        .field-input-sm:focus { border-color:#2563EB;box-shadow:0 0 0 3px rgba(37,99,235,.1); }
        .field-input-sm.qty   { text-align:center;font-weight:600; }
        .btn-add-special { display:inline-flex;align-items:center;gap:8px;padding:10px 18px;border-radius:10px;border:1.5px dashed #FED7AA;background:#FEF3C7;color:#D97706;font-size:.88rem;font-weight:600;font-family:'DM Sans',sans-serif;cursor:pointer;transition:background .15s; }
        .btn-add-special:hover { background:#FEE9B6; }

        /* ── Dropzone fotos ── */
        .dropzone      { border:2px dashed #CBD5E1;border-radius:16px;background:#F8FAFC;padding:40px 24px;text-align:center;cursor:pointer;transition:border-color .2s,background .2s; }
        .dropzone:hover,.dropzone.drag-over { border-color:#2563EB;background:#EFF6FF; }
        .dropzone-icon { width:56px;height:56px;border-radius:50%;background:#E2E8F0;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;transition:background .2s; }
        .dropzone:hover .dropzone-icon,.dropzone.drag-over .dropzone-icon { background:#DBEAFE; }
        .dropzone-title { font-size:1rem;font-weight:600;color:#374151;margin-bottom:6px; }
        .dropzone:hover .dropzone-title,.dropzone.drag-over .dropzone-title { color:#2563EB; }
        .photo-grid    { display:flex;flex-wrap:wrap;gap:10px;margin-top:14px; }
        .photo-thumb   { width:80px;height:80px;border-radius:10px;overflow:hidden;position:relative;border:1.5px solid #E2E8F0;flex-shrink:0; }
        .photo-thumb img { width:100%;height:100%;object-fit:cover;display:block; }
        .photo-del     { position:absolute;top:4px;right:4px;width:20px;height:20px;border-radius:50%;background:rgba(0,0,0,.6);border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:#fff;font-size:.55rem; }

        /* ── Tip ── */
        .tip-box  { display:flex;gap:12px;padding:16px;border-radius:14px;background:#FFFBEB;border:1.5px solid #FDE68A; }
        .tip-title { font-size:.82rem;font-weight:600;color:#92400E;margin-bottom:3px; }
        .tip-body  { font-size:.78rem;color:#B45309;line-height:1.5;margin:0; }

        /* ── Nav buttons ── */
        .btn-nav  { border-radius:13px;padding:14px 24px;font-weight:700;font-size:.95rem;font-family:'DM Sans',sans-serif;border:none;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:8px; }
        .btn-back { background:#F1F5F9;color:#475569;border:1.5px solid #E2E8F0;text-decoration:none;width:130px; }
        .btn-back:hover { background:#E2E8F0; }
        .btn-next { flex:1;background:linear-gradient(135deg,#2563EB,#1D4ED8);color:#fff;box-shadow:0 6px 20px rgba(37,99,235,.28); }
        .btn-next:hover { transform:translateY(-2px);box-shadow:0 10px 28px rgba(37,99,235,.33); }
        .btn-next:active { transform:translateY(0); }
    </style>
</head>
<body>
<div class="wizard-wrapper">

    {{-- ── BRAND ── --}}
    <div class="brand-header">
        <a class="brand-logo" href="#">
            <div class="brand-icon"><i class="fa-solid fa-truck-moving" style="color:#fff;font-size:1.1rem;"></i></div>
            <div>
                <div class="brand-name">FletesCun</div>
                <div class="brand-tagline">Mudanzas confiables · Cancún</div>
            </div>
        </a>
    </div>

    {{-- ── STEPPER ── --}}
    <div class="stepper-card">
        <div class="step-label">Paso <span>3</span> de 4</div>
        <div class="steps-row">
            <div class="step-item completed">
                <div class="step-circle"><i class="fa-solid fa-check" style="font-size:.75rem;"></i></div>
                <div class="step-name">Contacto<br>y Ruta</div>
            </div>
            <div class="step-item completed">
                <div class="step-circle"><i class="fa-solid fa-check" style="font-size:.75rem;"></i></div>
                <div class="step-name">Logística<br>y Detalles</div>
            </div>
            <div class="step-item active">
                <div class="step-circle">3</div>
                <div class="step-name">Inventario<br>y Fotos</div>
            </div>
            <div class="step-item">
                <div class="step-circle">4</div>
                <div class="step-name">Cotización<br>Final</div>
            </div>
        </div>
    </div>

    {{-- ── FORM CARD ── --}}
    <div class="form-card">

        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center gap-2 mb-4">
                <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="alert alert-danger mb-4">
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                Revisa los campos marcados antes de continuar.
            </div>
        @endif

        <div class="d-flex align-items-center gap-3 mb-1">
            <div class="section-icon"><i class="fa-solid fa-box-open" style="color:#2563EB;"></i></div>
            <h2 class="section-title">Inventario y Fotografías</h2>
        </div>
        <p class="section-sub mb-4">
            Selecciona tus artículos desde nuestro catálogo o descríbelos aquí.
            Sube fotos para una cotización más precisa.
        </p>

        <form action="{{ route('cotizar.paso3.post') }}"
              method="POST"
              enctype="multipart/form-data"
              id="formPaso3"
              novalidate>
            @csrf

            {{-- ══════════════════════════════════
                 SECCIÓN 1 — CATÁLOGO VISUAL
            ══════════════════════════════════ --}}
            <div class="divider-label"><span>Catálogo Visual Rápido</span></div>

            {{-- Resumen artículos seleccionados --}}
            <div id="selectedSummary" style="display:none;" class="mb-4">
                <div class="inv-header">
                    <label style="font-size:.85rem;font-weight:600;color:#1E293B;">Artículos seleccionados</label>
                    <span class="inv-count" id="catalogCount">0 artículos</span>
                </div>
                <div id="selectedList"></div>
            </div>

            {{-- Acordeón --}}
            @php
            $cats = [
              ['id'=>'camas','nombre'=>'1. Camas','items'=>[
                ['nombre'=>'Basecama + colchón King','m3'=>2.5],['nombre'=>'Basecama + colchón Queen','m3'=>2.2],
                ['nombre'=>'Basecama + colchón matrimonial','m3'=>2.0],['nombre'=>'Basecama + colchón individual','m3'=>1.3],
                ['nombre'=>'Sólo colchón (King/Queen)','m3'=>0.8],['nombre'=>'Sólo colchón (matrimonial/doble)','m3'=>0.7],
                ['nombre'=>'Sólo colchón (individual)','m3'=>0.4],['nombre'=>'Cabecera','m3'=>0.6],
                ['nombre'=>'Cuna desarmable','m3'=>0.5],['nombre'=>'Cuna no desarmable','m3'=>0.8],
                ['nombre'=>'Sólo base cama King','m3'=>1.8],['nombre'=>'Sólo base cama Queen','m3'=>1.6],
                ['nombre'=>'Sólo base cama matrimonial','m3'=>1.4],['nombre'=>'Sólo base cama individual','m3'=>0.9],
              ]],
              ['id'=>'mesas','nombre'=>'2. Mesas','items'=>[
                ['nombre'=>'Mesa de comedor pequeña','m3'=>1.2],['nombre'=>'Mesa de comedor mediana','m3'=>1.8],
                ['nombre'=>'Mesa de comedor grande','m3'=>2.5],['nombre'=>'Mesa de centro','m3'=>0.6],
                ['nombre'=>'Mesa de cocina','m3'=>1.0],['nombre'=>'Mesa plegable','m3'=>0.4],['nombre'=>'Mesa redonda','m3'=>1.5],
              ]],
              ['id'=>'muebles','nombre'=>'3. Muebles, almacenaje y otros','items'=>[
                ['nombre'=>'Librero','m3'=>1.2],['nombre'=>'Ropero','m3'=>2.0],['nombre'=>'Armario','m3'=>1.8],
                ['nombre'=>'Estantería','m3'=>0.8],['nombre'=>'Vitrina','m3'=>1.5],['nombre'=>'Zapatera','m3'=>0.4],
                ['nombre'=>'Perchero','m3'=>0.3],['nombre'=>'Chifonier','m3'=>1.0],['nombre'=>'Cómoda','m3'=>0.8],['nombre'=>'Escritorio','m3'=>1.2],
              ]],
              ['id'=>'sillas','nombre'=>'4. Sillas','items'=>[
                ['nombre'=>'Silla de comedor (unitaria)','m3'=>0.25],['nombre'=>'Sillas de comedor (set 4)','m3'=>0.9],
                ['nombre'=>'Silla de oficina','m3'=>0.4],['nombre'=>'Silla gamer','m3'=>0.5],
                ['nombre'=>'Banco de cocina','m3'=>0.2],['nombre'=>'Taburete','m3'=>0.15],['nombre'=>'Silla plegable','m3'=>0.1],
              ]],
              ['id'=>'sofas','nombre'=>'5. Sofás','items'=>[
                ['nombre'=>'Sofá individual','m3'=>1.0],['nombre'=>'Sofá 2 cuerpos','m3'=>2.0],['nombre'=>'Sofá 3 cuerpos','m3'=>3.0],
                ['nombre'=>'Sofá cama','m3'=>2.5],['nombre'=>'Loveseat','m3'=>1.2],
                ['nombre'=>'Seccional pequeña','m3'=>3.5],['nombre'=>'Seccional grande','m3'=>5.0],
              ]],
              ['id'=>'cocina','nombre'=>'6. Cocina','items'=>[
                ['nombre'=>'Refrigerador','m3'=>0.9],['nombre'=>'Estufa / Cocina','m3'=>0.8],
                ['nombre'=>'Horno microondas','m3'=>0.35],['nombre'=>'Lavadora','m3'=>0.7],
                ['nombre'=>'Secadora','m3'=>0.7],['nombre'=>'Lavavajillas','m3'=>0.6],
                ['nombre'=>'Comedor pequeño','m3'=>1.5],['nombre'=>'Comedor mediano','m3'=>2.0],
              ]],
              ['id'=>'electronica','nombre'=>'7. Electrónica','items'=>[
                ['nombre'=>'Televisión 40-50 pulgadas','m3'=>0.25],['nombre'=>'Televisión 55-65 pulgadas','m3'=>0.35],
                ['nombre'=>'Equipo de sonido','m3'=>0.3],['nombre'=>'Computadora de escritorio','m3'=>0.2],
                ['nombre'=>'Monitor','m3'=>0.15],['nombre'=>'Impresora','m3'=>0.1],
              ]],
              ['id'=>'lamparas','nombre'=>'8. Lámparas','items'=>[
                ['nombre'=>'Lámpara de piso','m3'=>0.2],['nombre'=>'Lámpara de mesa','m3'=>0.1],
                ['nombre'=>'Lámpara grande','m3'=>0.35],['nombre'=>'Lámpara de techo','m3'=>0.15],['nombre'=>'Aplique de pared','m3'=>0.08],
              ]],
              ['id'=>'miscelaneos','nombre'=>'9. Misceláneos','items'=>[
                ['nombre'=>'Espejo grande','m3'=>0.3],['nombre'=>'Espejo pequeño','m3'=>0.1],
                ['nombre'=>'Cuadro/Pintura','m3'=>0.05],['nombre'=>'Bicicleta','m3'=>0.3],
                ['nombre'=>'Piano','m3'=>3.0],['nombre'=>'Guitarra','m3'=>0.15],
                ['nombre'=>'Carro de golf','m3'=>1.8],['nombre'=>'Aire acondicionado','m3'=>0.3],
              ]],
              ['id'=>'terraza','nombre'=>'10. Terraza','items'=>[
                ['nombre'=>'Juego de patio (4 sillas + mesa)','m3'=>1.5],['nombre'=>'Silla de patio','m3'=>0.25],
                ['nombre'=>'Mesa de patio','m3'=>0.5],['nombre'=>'Sombrilla','m3'=>0.2],
                ['nombre'=>'Bancas de terraza','m3'=>0.8],['nombre'=>'Hamaca','m3'=>0.3],
              ]],
              ['id'=>'oficina','nombre'=>'11. Oficinas corporativas','items'=>[
                ['nombre'=>'Escritorio de oficina','m3'=>1.2],['nombre'=>'Archivero 4 cajones','m3'=>0.6],
                ['nombre'=>'Repisa de oficina','m3'=>0.5],['nombre'=>'Cajonera de oficina','m3'=>0.4],
                ['nombre'=>'Cubículo completo','m3'=>2.5],['nombre'=>'Mesa de reuniones','m3'=>2.0],
              ]],
              ['id'=>'cajas','nombre'=>'12. Cajas y maletas','items'=>[
                ['nombre'=>'Caja mediana','m3'=>0.04],['nombre'=>'Caja grande','m3'=>0.08],
                ['nombre'=>'Caja de archivo','m3'=>0.03],['nombre'=>'Maleta grande','m3'=>0.12],
                ['nombre'=>'Maleta mediana','m3'=>0.08],['nombre'=>'Baúl','m3'=>0.5],
              ]],
              ['id'=>'restaurante','nombre'=>'13. Restaurantes','items'=>[
                ['nombre'=>'Estufa comercial','m3'=>1.5],['nombre'=>'Refrigerador comercial','m3'=>2.0],
                ['nombre'=>'Extractor de humo','m3'=>1.0],['nombre'=>'Mesas altas','m3'=>0.8],
                ['nombre'=>'Banquetas altas','m3'=>0.3],['nombre'=>'Mostrador','m3'=>2.0],
              ]],
            ];
            @endphp

            @foreach($cats as $cat)
            <div class="cat-accordion">
                <button type="button" class="cat-header" onclick="toggleCat('{{ $cat['id'] }}')">
                    <span>{{ $cat['nombre'] }}</span>
                    <i class="fa-solid fa-chevron-down cat-chevron" id="chevron_{{ $cat['id'] }}"></i>
                </button>
                <div class="cat-body" id="body_{{ $cat['id'] }}">
                    @foreach($cat['items'] as $item)
                    <button type="button"
                            class="cat-item"
                            onclick="addCatalogItem({{ json_encode($item['nombre']) }}, {{ $item['m3'] }}, '{{ $cat['id'] }}')">
                        <span class="cat-item-name">{{ $item['nombre'] }}</span>
                        <span class="cat-item-m3">
                            {{ $item['m3'] }} m³
                            <i class="fa-solid fa-plus" style="color:#2563EB;font-size:.6rem;"></i>
                        </span>
                    </button>
                    @endforeach
                </div>
            </div>
            @endforeach

            <input type="hidden" name="inventario_catalogo" id="inventarioCatalogo" value="">

            {{-- ══════════════════════════════════
                 SECCIÓN 2 — ARTÍCULOS ESPECIALES
            ══════════════════════════════════ --}}
            <div class="divider-label"><span>Artículos Especiales</span></div>

            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="special-header">
                    <i class="fa-solid fa-pen-to-square" style="color:#2563EB;font-size:.85rem;"></i>
                    Artículos delicados, pesados o no listados
                </div>
                <span class="inv-count" id="specialCount">0 artículos</span>
            </div>

            <div class="special-table-head" id="specialTableHead" style="display:none;">
                <span>Cant.</span>
                <span>Artículo</span>
                <span class="sp-obs">Observaciones</span>
                <span></span>
            </div>
            <div id="specialRows"></div>

            <button type="button" class="btn-add-special mt-1" onclick="addSpecialRow()">
                <i class="fa-solid fa-plus"></i> Agregar artículo especial
            </button>

            <input type="hidden" name="articulos_especiales" id="articulosEspeciales" value="">

            {{-- ══════════════════════════════════
                 SECCIÓN 3 — FOTOS
            ══════════════════════════════════ --}}
            <div class="divider-label"><span>Fotos de muebles voluminosos o frágiles</span></div>

            <label style="font-size:.85rem;font-weight:600;color:#1E293B;display:block;margin-bottom:8px;">
                Fotos de referencia
                <span style="font-size:.75rem;font-weight:400;color:#94A3B8;margin-left:8px;">(Opcional pero recomendado)</span>
            </label>

            <div class="dropzone" id="dropzone"
                 onclick="document.getElementById('fileInput').click()"
                 ondragover="handleDragOver(event)"
                 ondragleave="handleDragLeave(event)"
                 ondrop="handleDrop(event)">
                <div class="dropzone-icon">
                    <i class="fa-solid fa-camera" style="font-size:1.4rem;color:#94A3B8;"></i>
                </div>
                <p class="dropzone-title">Arrastra y suelta tus fotos aquí</p>
                <p style="font-size:.82rem;color:#94A3B8;margin-bottom:12px;line-height:1.5;">
                    Sube fotos de tus muebles voluminosos o frágiles desde tu celular o computadora
                </p>
                <div style="display:inline-flex;align-items:center;gap:6px;padding:8px 18px;border-radius:8px;background:#2563EB;color:#fff;font-size:.82rem;font-weight:600;">
                    <i class="fa-solid fa-upload" style="font-size:.8rem;"></i>
                    Seleccionar archivos
                </div>
                <p style="font-size:.7rem;color:#CBD5E1;margin-top:10px;margin-bottom:0;">
                    PNG, JPG, HEIC · Máx. 10 MB por archivo
                </p>
            </div>

            <input type="file" id="fileInput" name="fotos[]"
                   accept="image/*" multiple
                   style="display:none;"
                   onchange="handleFileInput(this.files)">

            <div class="photo-grid" id="photoGrid"></div>

            {{-- Tip --}}
            <div class="tip-box mt-4">
                <span style="font-size:1.2rem;flex-shrink:0;">💡</span>
                <div>
                    <p class="tip-title">Consejo para una mejor cotización</p>
                    <p class="tip-body">
                        Las fotos de artículos como pianos, jacuzzis, vitrinas de cristal o maquinaria nos permiten
                        enviarte un presupuesto más exacto y evitar sorpresas el día de tu mudanza.
                    </p>
                </div>
            </div>

            {{-- ── BOTONES NAV ── --}}
            <div class="d-flex gap-3 mt-4">
                <a href="{{ route('cotizar.paso2') }}" class="btn-nav btn-back">
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
/* ══════════════════════════════════════════════════════════
   ESTADO
══════════════════════════════════════════════════════════ */
let catalogItems  = {};   // { nombre: { nombre, m3, cantidad, categoria } }
let specialItems  = [];   // [ { id, cantidad, articulo, observaciones } ]
let photoFiles    = [];   // File[]
let photoDataUrls = [];   // { name, url }[]
let specialIdCtr  = 0;

/* ══════════════════════════════════════════════════════════
   ACORDEÓN
══════════════════════════════════════════════════════════ */
function toggleCat(id) {
    const body   = document.getElementById('body_' + id);
    const header = body.previousElementSibling;
    const isOpen = body.classList.contains('open');

    document.querySelectorAll('.cat-body').forEach(b => b.classList.remove('open'));
    document.querySelectorAll('.cat-header').forEach(h => h.classList.remove('open'));

    if (!isOpen) {
        body.classList.add('open');
        header.classList.add('open');
    }
}

/* ══════════════════════════════════════════════════════════
   CATÁLOGO — agregar / ajustar cantidad / eliminar
══════════════════════════════════════════════════════════ */
function addCatalogItem(nombre, m3, categoria) {
    if (catalogItems[nombre]) {
        catalogItems[nombre].cantidad++;
    } else {
        catalogItems[nombre] = { nombre, m3: parseFloat(m3), cantidad: 1, categoria };
    }
    renderCatalogSummary();
}

function changeQty(nombre, delta) {
    if (!catalogItems[nombre]) return;
    catalogItems[nombre].cantidad += delta;
    if (catalogItems[nombre].cantidad <= 0) delete catalogItems[nombre];
    renderCatalogSummary();
}

function removeFromCatalog(nombre) {
    delete catalogItems[nombre];
    renderCatalogSummary();
}

function renderCatalogSummary() {
    const list    = document.getElementById('selectedList');
    const summary = document.getElementById('selectedSummary');
    const counter = document.getElementById('catalogCount');
    const items   = Object.values(catalogItems);

    if (items.length === 0) {
        summary.style.display = 'none';
        list.innerHTML = '';
        counter.textContent = '0 artículos';
        return;
    }
    summary.style.display = 'block';
    counter.textContent = items.length + ' artículo' + (items.length !== 1 ? 's' : '');

    list.innerHTML = items.map(item => `
        <div class="inv-item">
            <div class="inv-item-name">
                ${escHtml(item.nombre)}
                <span class="inv-item-m3">(${item.m3} m³ c/u)</span>
            </div>
            <div class="qty-wrap">
                <button type="button" class="qty-btn minus"
                        onclick="changeQty(${JSON.stringify(item.nombre)}, -1)">
                    <i class="fa-solid fa-minus" style="font-size:.65rem;"></i>
                </button>
                <span class="qty-num">${item.cantidad}</span>
                <button type="button" class="qty-btn plus"
                        onclick="changeQty(${JSON.stringify(item.nombre)}, 1)">
                    <i class="fa-solid fa-plus" style="font-size:.65rem;"></i>
                </button>
                <button type="button" class="del-btn"
                        onclick="removeFromCatalog(${JSON.stringify(item.nombre)})">
                    <i class="fa-solid fa-xmark" style="font-size:.65rem;"></i>
                </button>
            </div>
        </div>`).join('');
}

/* ══════════════════════════════════════════════════════════
   ARTÍCULOS ESPECIALES
══════════════════════════════════════════════════════════ */
function addSpecialRow() {
    specialIdCtr++;
    specialItems.push({ id: specialIdCtr, cantidad: 1, articulo: '', observaciones: '' });
    renderSpecialRows();
}

function updateSpecial(id, field, value) {
    const item = specialItems.find(i => i.id === id);
    if (!item) return;
    item[field] = (field === 'cantidad') ? (parseInt(value) || 1) : value;
}

function removeSpecialRow(id) {
    specialItems = specialItems.filter(i => i.id !== id);
    renderSpecialRows();
}

function renderSpecialRows() {
    const container = document.getElementById('specialRows');
    const head      = document.getElementById('specialTableHead');
    const counter   = document.getElementById('specialCount');


    container.innerHTML = specialItems.map((item, idx) => `
        <div class="special-row" style="background:${idx % 2 === 0 ? '#F8FAFC' : '#fff'};">
            <input type="number" min="1" value="${item.cantidad}"
                   class="field-input-sm qty"
                   oninput="updateSpecial(${item.id},'cantidad',this.value)">
            <input type="text" value="${escHtml(item.articulo)}"
                   placeholder="Ej. Piano, Jacuzzi, Vitrina..."
                   class="field-input-sm"
                   oninput="updateSpecial(${item.id},'articulo',this.value)">
            <input type="text" value="${escHtml(item.observaciones)}"
                   placeholder="Frágil, requiere cuidado..."
                   class="field-input-sm sp-obs"
                   oninput="updateSpecial(${item.id},'observaciones',this.value)">
            <button type="button"
                    onclick="removeSpecialRow(${item.id})"
                    style="width:32px;height:32px;border-radius:8px;border:none;background:#FEF2F2;color:#EF4444;cursor:pointer;display:flex;align-items:center;justify-content:center;"
                    onmouseover="this.style.background='#FECDD3'"
                    onmouseout="this.style.background='#FEF2F2'">
                <i class="fa-solid fa-xmark" style="font-size:.75rem;"></i>
            </button>
        </div>`).join('');
}

/* ══════════════════════════════════════════════════════════
   FOTOS
══════════════════════════════════════════════════════════ */
function handleDragOver(e) {
    e.preventDefault();
    document.getElementById('dropzone').classList.add('drag-over');
}
function handleDragLeave() {
    document.getElementById('dropzone').classList.remove('drag-over');
}
function handleDrop(e) {
    e.preventDefault();
    document.getElementById('dropzone').classList.remove('drag-over');
    addPhotos(e.dataTransfer.files);
}
function handleFileInput(files) { addPhotos(files); }

function addPhotos(files) {
    Array.from(files).forEach(file => {
        if (!file.type.startsWith('image/')) return;
        if (file.size > 10 * 1024 * 1024) {
            alert('"' + file.name + '" supera los 10 MB y no se pudo agregar.');
            return;
        }
        photoFiles.push(file);
        const reader = new FileReader();
        reader.onload = e => {
            photoDataUrls.push({ name: file.name, url: e.target.result });
            renderPhotos();
        };
        reader.readAsDataURL(file);
    });
}

function removePhoto(idx) {
    photoFiles.splice(idx, 1);
    photoDataUrls.splice(idx, 1);
    renderPhotos();
}

function renderPhotos() {
    document.getElementById('photoGrid').innerHTML =
        photoDataUrls.map((p, i) => `
            <div class="photo-thumb">
                <img src="${p.url}" alt="foto-${i}">
                <button type="button" class="photo-del" onclick="removePhoto(${i})">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>`).join('');
}

/* ══════════════════════════════════════════════════════════
   SUBMIT — serializar JSON + reconstruir fotos en FormData
══════════════════════════════════════════════════════════ */
document.getElementById('formPaso3').addEventListener('submit', function(e) {
    e.preventDefault();

    // Serializar catálogo (eliminar filas con cantidad 0)
    document.getElementById('inventarioCatalogo').value =
        JSON.stringify(Object.values(catalogItems));

    // Serializar especiales (filtrar filas sin nombre)
    document.getElementById('articulosEspeciales').value =
        JSON.stringify(specialItems.filter(i => i.articulo.trim() !== ''));

    // Construir FormData con los archivos de foto que quedan en memoria
    const fd = new FormData(this);
    fd.delete('fotos[]');
    photoFiles.forEach(file => fd.append('fotos[]', file, file.name));

    // Enviar vía fetch para manejar los archivos y el JSON simultáneamente
    fetch(this.action, {
        method: 'POST',
        body: fd,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (response.redirected) {
            // Si Laravel redirigió (a Paso 4 o de vuelta a Paso 1 por sesión expirada)
            window.location.href = response.url;
            return;
        }
        return response.text().then(text => {
            if (response.ok) {
                // Si no hubo redirección pero la respuesta es exitosa, 
                // forzamos la navegación a la URL final
                window.location.href = response.url;
            } else {
                // Si hay errores de validación, recargamos para que Laravel los muestre
                // o podrías manejarlos aquí. Por simplicidad, recargamos.
                document.open();
                document.write(text);
                document.close();
            }
        });
    })
    .catch(err => {
        console.error('Error:', err);
        // Fallback al submit tradicional si falla el fetch
        this.submit();
    });
});

/* ══════════════════════════════════════════════════════════
   UTILIDAD
══════════════════════════════════════════════════════════ */
function escHtml(str) {
    return String(str)
        .replace(/&/g,'&amp;').replace(/</g,'&lt;')
        .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
</script>
</body>
</html>