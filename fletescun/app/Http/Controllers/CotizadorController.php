<?php
// app/Http/Controllers/CotizadorController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Services\PricingService;
use App\Services\DocumentGenerationService;

class CotizadorController extends Controller
{
    // ────────────────────────────────────────────────────────
    //  GET  /cotizar  →  Mostrar Paso 1
    // ────────────────────────────────────────────────────────
    public function paso1()
    {
        $cotizacionId = session('cotizacion_id');
        if ($cotizacionId && ! session()->has('cotizador_paso1')) {
            $cotizacion = DB::table('cotizaciones')
                ->where('id', $cotizacionId)
                ->first();

            if ($cotizacion) {
                $cliente = DB::table('clientes')
                    ->where('id', $cotizacion->cliente_id)
                    ->first();

                if ($cliente) {
                    session([
                        'cotizacion_folio' => $cotizacion->folio,
                        'cotizador_paso1'  => [
                            'nombre'                 => $cliente->nombre,
                            'telefono'               => $cliente->telefono,
                            'correo'                 => $cliente->correo,
                            'direccion_origen'       => $cotizacion->direccion_origen,
                            'direccion_destino'      => $cotizacion->direccion_destino,
                            'fecha_ideal'            => $cotizacion->fecha_ideal,
                            'estacionamiento_origen' => $cotizacion->acceso_estacionamiento_origen,
                            'estacionamiento_destino'=> $cotizacion->acceso_estacionamiento_destino,
                            'distancia_km'           => $cotizacion->distancia_km,
                        ],
                    ]);
                }
            }
        }

        return view('paso1');
    }

    // ────────────────────────────────────────────────────────
    //  POST /cotizar  →  Procesar Paso 1
    //
    //  Tablas que se escriben:
    //    1. clientes     → nombre, telefono, correo, ip_origen
    //    2. cotizaciones → cliente_id, folio, direccion_origen,
    //                      direccion_destino, fecha_ideal,
    //                      acceso_estacionamiento_origen/destino,
    //                      distancia_km
    //
    //  La cotizacion_id se guarda en sesión para los pasos
    //  siguientes.
    // ────────────────────────────────────────────────────────
    public function paso1Post(Request $request)
    {
        // ── 1. Validación ──────────────────────────────────
        $request->validate([
            'nombre'                   => ['required', 'string',  'max:120'],
            'telefono'                 => ['required', 'string',  'max:20'],
            'correo'                   => ['required', 'email',   'max:120'],
            'direccion_origen'         => ['required', 'string',  'max:255'],
            'direccion_destino'        => ['required', 'string',  'max:255'],
            'fecha_ideal'              => ['required', 'date',    'after_or_equal:today'],
            'estacionamiento_origen'   => ['nullable', 'in:si,no'],
            'estacionamiento_destino'  => ['nullable', 'in:si,no'],
            // Coordenadas (opcionales; se llenan cuando Google Maps está activo)
            'lat_origen'               => ['nullable', 'numeric', 'between:-90,90'],
            'lng_origen'               => ['nullable', 'numeric', 'between:-180,180'],
            'lat_destino'              => ['nullable', 'numeric', 'between:-90,90'],
            'lng_destino'              => ['nullable', 'numeric', 'between:-180,180'],
            'distancia_km'             => ['nullable', 'numeric', 'min:0'],
        ], [
            // Mensajes personalizados en español
            'nombre.required'                  => 'El nombre es obligatorio.',
            'telefono.required'                => 'El teléfono es obligatorio.',
            'correo.required'                  => 'El correo electrónico es obligatorio.',
            'correo.email'                     => 'Ingresa un correo válido.',
            'direccion_origen.required'        => 'La dirección de origen es obligatoria.',
            'direccion_destino.required'       => 'La dirección de destino es obligatoria.',
            'fecha_ideal.required'             => 'La fecha es obligatoria.',
            'fecha_ideal.after_or_equal'       => 'La fecha no puede ser en el pasado.',
        ]);

        $cotizacionId = session('cotizacion_id');
        $cotizacion = null;
        if ($cotizacionId) {
            $cotizacion = DB::table('cotizaciones')
                ->where('id', $cotizacionId)
                ->first();
        }

        if ($cotizacion) {
            $clienteId = $cotizacion->cliente_id;

            DB::table('clientes')
                ->where('id', $clienteId)
                ->update([
                    'nombre'         => $request->nombre,
                    'telefono'       => $request->telefono,
                    'correo'         => $request->correo,
                    'actualizado_en' => now(),
                ]);

            DB::table('cotizaciones')
                ->where('id', $cotizacionId)
                ->update([
                    'direccion_origen'               => $request->direccion_origen,
                    'direccion_destino'              => $request->direccion_destino,
                    'fecha_ideal'                    => $request->fecha_ideal,
                    'acceso_estacionamiento_origen'  => $request->estacionamiento_origen ?: null,
                    'acceso_estacionamiento_destino' => $request->estacionamiento_destino ?: null,
                    'distancia_km'                   => $request->filled('distancia_km')
                        ? round((float) $request->distancia_km, 2)
                        : null,
                    'actualizado_en'                 => now(),
                ]);

            session([
                'cotizacion_id'      => $cotizacionId,
                'cotizacion_folio'   => $cotizacion->folio,
                'cliente_id'         => $clienteId,
                'lat_origen'         => $request->lat_origen,
                'lng_origen'         => $request->lng_origen,
                'lat_destino'        => $request->lat_destino,
                'lng_destino'        => $request->lng_destino,
                'distancia_km'       => $request->distancia_km,
                'cotizador_paso1'    => [
                    'nombre'                 => $request->nombre,
                    'telefono'               => $request->telefono,
                    'correo'                 => $request->correo,
                    'direccion_origen'       => $request->direccion_origen,
                    'direccion_destino'      => $request->direccion_destino,
                    'fecha_ideal'            => $request->fecha_ideal,
                    'estacionamiento_origen' => $request->estacionamiento_origen,
                    'estacionamiento_destino'=> $request->estacionamiento_destino,
                    'distancia_km'           => $request->distancia_km,
                ],
            ]);

            return redirect()->route('cotizar.paso2')
                ->with('success', "¡Actualizado! Puedes ajustar los detalles de tu mudanza.");
        }

        // ── 2. Insertar / reutilizar cliente ───────────────
        //
        //  Estrategia anti-duplicados: si ya existe un cliente
        //  con el mismo teléfono Y correo, se reutiliza su id
        //  en lugar de crear un registro duplicado.
        //
        $clienteId = DB::table('clientes')
            ->where('telefono', $request->telefono)
            ->where('correo',   $request->correo)
            ->value('id');

        if (! $clienteId) {
            $clienteId = (string) Str::uuid();
            DB::table('clientes')->insert([
                'id'        => $clienteId,
                'nombre'    => $request->nombre,
                'telefono'  => $request->telefono,
                'correo'    => $request->correo,
                'ip_origen' => $request->ip(),
            ]);
        } else {
            // Actualizar nombre por si cambió
            DB::table('clientes')
                ->where('id', $clienteId)
                ->update([
                    'nombre'         => $request->nombre,
                    'actualizado_en' => now(),
                ]);
        }

        // ── 3. Generar folio único ─────────────────────────
        //
        //  Formato: FC-YYYYMMDD-XXXXX
        //  Ej:      FC-20240801-A3F2K
        //
        do {
            $folio = 'FC-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
        } while (DB::table('cotizaciones')->where('folio', $folio)->exists());

        // ── 4. Crear cotización (Paso 1 completo) ──────────
        $cotizacionId = (string) Str::uuid();

        DB::table('cotizaciones')->insert([
            'id'                              => $cotizacionId,
            'folio'                           => $folio,
            'cliente_id'                      => $clienteId,

            // Ruta
            'direccion_origen'                => $request->direccion_origen,
            'direccion_destino'               => $request->direccion_destino,
            'fecha_ideal'                     => $request->fecha_ideal,

            // Estacionamiento
            'acceso_estacionamiento_origen'   => $request->estacionamiento_origen  ?: null,
            'acceso_estacionamiento_destino'  => $request->estacionamiento_destino ?: null,

            // Distancia calculada por Google Maps (puede ser null si el mapa no cargó)
            'distancia_km'                    => $request->filled('distancia_km')
                                                    ? round((float) $request->distancia_km, 2)
                                                    : null,

            // Defaults
            'tipo_servicio'                   => 'Exclusivo',
            'estatus'                         => 'Prospecto',
            'moneda'                          => 'MXN',
            'es_duplicado_alerta'             => 0,
            'portal_activo'                   => 1,
        ]);

        // ── 5. Guardar en sesión para pasos siguientes ─────
        session([
            'cotizacion_id'      => $cotizacionId,
            'cotizacion_folio'   => $folio,
            'cliente_id'         => $clienteId,
            // Coordenadas para reutilizar en Paso 4 (cálculo de precio)
            'lat_origen'         => $request->lat_origen,
            'lng_origen'         => $request->lng_origen,
            'lat_destino'        => $request->lat_destino,
            'lng_destino'        => $request->lng_destino,
            'distancia_km'       => $request->distancia_km,
            'cotizador_paso1'     => [
                'nombre'                 => $request->nombre,
                'telefono'               => $request->telefono,
                'correo'                 => $request->correo,
                'direccion_origen'       => $request->direccion_origen,
                'direccion_destino'      => $request->direccion_destino,
                'fecha_ideal'            => $request->fecha_ideal,
                'estacionamiento_origen' => $request->estacionamiento_origen,
                'estacionamiento_destino'=> $request->estacionamiento_destino,
                'distancia_km'           => $request->distancia_km,
            ],
        ]);

        // ── 6. Redirigir al Paso 2 ─────────────────────────
        return redirect()->route('cotizar.paso2')
            ->with('success', "¡Listo! Folio {$folio} creado. Continúa con los detalles de tu mudanza.");
    }
// ============================================================
//  AGREGA estos dos métodos a CotizadorController.php
//  (dentro de la clase, después de paso1Post)
// ============================================================

    // ────────────────────────────────────────────────────────
    //  GET /cotizar/paso2  →  Mostrar Paso 2
    // ────────────────────────────────────────────────────────
    public function paso2()
    {
        // Redirigir al inicio si no hay sesión activa
        if (! session('cotizacion_id')) {
            return redirect()->route('cotizar.paso1')
                ->with('error', 'Por favor completa el Paso 1 primero.');
        }

        $cotizacionId = session('cotizacion_id');
        if ($cotizacionId && ! session()->has('cotizador_paso2')) {
            $cotizacion = DB::table('cotizaciones')
                ->where('id', $cotizacionId)
                ->first();

            if ($cotizacion) {
                $servicios = DB::table('servicios_adicionales')
                    ->where('cotizacion_id', $cotizacionId)
                    ->pluck('servicio')
                    ->toArray();

                session([
                    'cotizador_paso2' => [
                        'piso_origen'        => $cotizacion->piso_origen,
                        'piso_destino'       => $cotizacion->piso_destino,
                        'elevador_origen'    => (string) $cotizacion->elevador_origen,
                        'elevador_destino'   => (string) $cotizacion->elevador_destino,
                        'modalidad'          => $cotizacion->tipo_servicio,
                        'servicio_embalaje'  => in_array('Embalaje de cajas',        $servicios) ? '1' : '0',
                        'servicio_desmontaje'=> in_array('Desmontaje de muebles',    $servicios) ? '1' : '0',
                        'servicio_volado'    => in_array('Volado / acarreo externo', $servicios) ? '1' : '0',
                        'servicio_seguro'    => in_array('Seguro de carga',          $servicios) ? '1' : '0',
                    ],
                ]);
            }
        }

        return view('paso2');
    }

    // ────────────────────────────────────────────────────────
    //  POST /cotizar/paso2  →  Procesar Paso 2
    //
    //  Tablas que se escriben:
    //    1. cotizaciones        → piso_origen, piso_destino,
    //                             elevador_origen, elevador_destino,
    //                             tipo_servicio (Exclusivo/Compartido)
    //
    //    2. servicios_adicionales → una fila por servicio marcado
    //       Valores ENUM válidos (exactos según el schema):
    //         'Embalaje de cajas'
    //         'Desmontaje de muebles'
    //         'Volado / acarreo externo'
    //         'Seguro de carga'
    // ────────────────────────────────────────────────────────
    public function paso2Post(Request $request)
    {
        // ── Guardia de sesión ──────────────────────────────
        $cotizacionId = session('cotizacion_id');
        if (! $cotizacionId) {
            return redirect()->route('cotizar.paso1')
                ->with('error', 'Sesión expirada. Por favor comienza de nuevo.');
        }

        // ── Validación ────────────────────────────────────
        $request->validate([
            'piso_origen'    => ['required', 'string', 'max:30'],
            'piso_destino'   => ['required', 'string', 'max:30'],
            'elevador_origen'  => ['nullable', 'in:0,1'],
            'elevador_destino' => ['nullable', 'in:0,1'],
            'modalidad'      => ['required', 'in:Exclusivo,Compartido'],
            // Servicios adicionales (checkboxes ocultos que envían '1' o '0')
            'servicio_embalaje'   => ['nullable', 'in:0,1'],
            'servicio_desmontaje' => ['nullable', 'in:0,1'],
            'servicio_volado'     => ['nullable', 'in:0,1'],
            'servicio_seguro'     => ['nullable', 'in:0,1'],
        ], [
            'piso_origen.required'  => 'Selecciona el piso de origen.',
            'piso_destino.required' => 'Selecciona el piso de destino.',
            'modalidad.required'    => 'Selecciona la modalidad de servicio.',
            'modalidad.in'          => 'Modalidad no válida.',
        ]);

        // ── 1. Actualizar cotizacion (campos del Paso 2) ──
        DB::table('cotizaciones')
            ->where('id', $cotizacionId)
            ->update([
                'piso_origen'      => $request->piso_origen,
                'piso_destino'     => $request->piso_destino,
                'elevador_origen'  => (int) ($request->elevador_origen  ?? 0),
                'elevador_destino' => (int) ($request->elevador_destino ?? 0),
                'tipo_servicio'    => $request->modalidad,  // 'Exclusivo' | 'Compartido'
                'actualizado_en'   => now(),
            ]);

        // ── 2. Insertar servicios adicionales ─────────────
        //
        //  Eliminamos primero los que pudiera haber de una
        //  edición anterior (por si el usuario regresó al paso).
        DB::table('servicios_adicionales')
            ->where('cotizacion_id', $cotizacionId)
            ->delete();

        // Mapa: campo del form  →  valor ENUM exacto del schema
        $mapaServicios = [
            'servicio_embalaje'   => 'Embalaje de cajas',
            'servicio_desmontaje' => 'Desmontaje de muebles',
            'servicio_volado'     => 'Volado / acarreo externo',
            'servicio_seguro'     => 'Seguro de carga',
        ];

        $filas = [];
        foreach ($mapaServicios as $campo => $valorEnum) {
            if ($request->input($campo) === '1') {
                $filas[] = [
                    'cotizacion_id' => $cotizacionId,
                    'servicio'      => $valorEnum,
                ];
            }
        }

        if (! empty($filas)) {
            DB::table('servicios_adicionales')->insert($filas);
        }

        // ── 3. Actualizar sesión ──────────────────────────
        session([
            'modalidad'        => $request->modalidad,
            'piso_origen'      => $request->piso_origen,
            'piso_destino'     => $request->piso_destino,
            'elevador_origen'  => (int) ($request->elevador_origen  ?? 0),
            'elevador_destino' => (int) ($request->elevador_destino ?? 0),
            'cotizador_paso2'  => [
                'piso_origen'         => $request->piso_origen,
                'piso_destino'        => $request->piso_destino,
                'elevador_origen'     => (string) ($request->elevador_origen  ?? 0),
                'elevador_destino'    => (string) ($request->elevador_destino ?? 0),
                'modalidad'           => $request->modalidad,
                'servicio_embalaje'   => $request->input('servicio_embalaje', '0'),
                'servicio_desmontaje' => $request->input('servicio_desmontaje', '0'),
                'servicio_volado'     => $request->input('servicio_volado', '0'),
                'servicio_seguro'     => $request->input('servicio_seguro', '0'),
            ],
        ]);

        // ── 4. Redirigir al Paso 3 ────────────────────────
        return redirect()->route('cotizar.paso3')
            ->with('success', 'Logística guardada. Ahora dinos qué vas a mover.');
    }

     public function paso3()
    {
        if (! session('cotizacion_id')) {
            return redirect()->route('cotizar.paso1')
                ->with('error', 'Por favor completa el Paso 1 primero.');
        }

        $cotizacionId = session('cotizacion_id');
        if ($cotizacionId && ! session()->has('cotizador_paso3')) {
            $catalogoRaw = DB::table('inventario_articulos')
                ->where('cotizacion_id', $cotizacionId)
                ->where('es_especial', 0)
                ->orderBy('orden')
                ->get();

            $catalogo = $catalogoRaw->map(fn($r) => [
                'nombre'       => $r->nombre,
                'cantidad'     => $r->cantidad,
                'm3'           => $r->m3,
                'categoria'    => $r->categoria,
                'observaciones'=> $r->observaciones,
            ])->toArray();

            $especialesRaw = DB::table('inventario_articulos')
                ->where('cotizacion_id', $cotizacionId)
                ->where('es_especial', 1)
                ->orderBy('orden')
                ->get();

            $especiales = $especialesRaw->map(fn($r) => [
                'articulo'     => $r->nombre,
                'cantidad'     => $r->cantidad,
                'observaciones'=> $r->observaciones,
            ])->toArray();

            $fotos = DB::table('fotos_anexo')
                ->where('cotizacion_id', $cotizacionId)
                ->orderBy('orden')
                ->pluck('ruta_relativa')
                ->toArray();

            session([
                'cotizador_paso3' => [
                    'inventario_catalogo'  => $catalogo,
                    'articulos_especiales' => $especiales,
                    'fotos_paths'          => $fotos,
                ],
            ]);
        }
        return view('paso3');
    }
    
 
// ────────────────────────────────────────────────────────
//  GET /cotizar/paso4  →  Mostrar Paso 4
// ────────────────────────────────────────────────────────
public function paso4()
{
    if (! session('cotizacion_id')) {
        return redirect()->route('cotizar.paso1')
            ->with('error', 'Por favor completa el Paso 1 primero.');
    }

    // Reconstruir los arrays que el blade de paso4 necesita
    // leyendo directo de BD (más fiable que confiar solo en sesión)
    $cotizacionId = session('cotizacion_id');

    $cotizacion = DB::table('cotizaciones')
        ->where('id', $cotizacionId)
        ->first();

    $cliente = DB::table('clientes')
        ->where('id', $cotizacion->cliente_id)
        ->first();

    // ── CALCULAR PRECIOS REALES USANDO PRICINGSERVICE ──
    $pricingService = app(PricingService::class);
    $preciosReales  = $pricingService->calcularPrecioFinal($cotizacionId);
    
    $priceMin = (int) round($preciosReales['subtotal_con_modalidad']);
    $priceMax = (int) round($preciosReales['total_final']);

    // Inventario catálogo
    $catalogoRaw = DB::table('inventario_articulos')
        ->where('cotizacion_id', $cotizacionId)
        ->where('es_especial', 0)
        ->orderBy('orden')
        ->get();

    $catalogo = $catalogoRaw->map(fn($r) => [
        'nombre'       => $r->nombre,
        'cantidad'     => $r->cantidad,
        'm3'           => $r->m3,
        'categoria'    => $r->categoria,
        'observaciones'=> $r->observaciones,
    ])->toArray();

    // Artículos especiales
    $especialesRaw = DB::table('inventario_articulos')
        ->where('cotizacion_id', $cotizacionId)
        ->where('es_especial', 1)
        ->orderBy('orden')
        ->get();

    $especiales = $especialesRaw->map(fn($r) => [
        'articulo'     => $r->nombre,
        'cantidad'     => $r->cantidad,
        'observaciones'=> $r->observaciones,
    ])->toArray();

    // Fotos
    $fotos = DB::table('fotos_anexo')
        ->where('cotizacion_id', $cotizacionId)
        ->orderBy('orden')
        ->pluck('ruta_relativa')
        ->toArray();

    // Servicios adicionales
    $servicios = DB::table('servicios_adicionales')
        ->where('cotizacion_id', $cotizacionId)
        ->pluck('servicio')
        ->toArray();

    // Armar las 3 "sesiones" que espera el blade
    session([
        'cotizador_paso1' => [
            'nombre'           => $cliente->nombre,
            'telefono'         => $cliente->telefono,
            'correo'           => $cliente->correo,
            'fecha_ideal'      => $cotizacion->fecha_ideal,
            'direccion_origen' => $cotizacion->direccion_origen,
            'direccion_destino'=> $cotizacion->direccion_destino,
        ],
        'cotizador_paso2' => [
            'piso_origen'        => $cotizacion->piso_origen,
            'piso_destino'       => $cotizacion->piso_destino,
            'elevador_origen'    => $cotizacion->elevador_origen,
            'elevador_destino'   => $cotizacion->elevador_destino,
            'modalidad'          => $cotizacion->tipo_servicio,
            'servicio_embalaje'  => in_array('Embalaje de cajas',        $servicios) ? '1' : '0',
            'servicio_desmontaje'=> in_array('Desmontaje de muebles',    $servicios) ? '1' : '0',
            'servicio_volado'    => in_array('Volado / acarreo externo', $servicios) ? '1' : '0',
            'servicio_seguro'    => in_array('Seguro de carga',          $servicios) ? '1' : '0',
        ],
        'cotizador_paso3' => [
            'inventario_catalogo'  => $catalogo,
            'articulos_especiales' => $especiales,
            'fotos_paths'          => $fotos,
        ],
    ]);

    // Pasar precios reales calculados al blade
    return view('paso4', [
        'priceMin' => $priceMin,
        'priceMax' => $priceMax,
        'preciosDetalle' => $preciosReales,
    ]);
}

// ────────────────────────────────────────────────────────
//  POST /cotizar/paso4  →  Guardar aceptación y finalizar
//
//  Tablas que se escriben:
//    1. aceptaciones_contrato → acepta_terminos, acepta_inventario,
//                               ip, precio_estimado_min/max
//    2. cotizaciones          → precio_estimado_min/max, estatus
//    3. documentos_generados  → Carta Porte + Anexo PDF
//    4. notificaciones_correo → Log de envíos
//
//  Servicios utilizados:
//    - PricingService           → Calcula precio final automático
//    - DocumentGenerationService → Orquesta generación y envío
// ────────────────────────────────────────────────────────
public function paso4Post(Request $request)
{
    $cotizacionId = session('cotizacion_id');
    if (! $cotizacionId) {
        return redirect()->route('cotizar.paso1')
            ->with('error', 'Sesión expirada. Por favor comienza de nuevo.');
    }

    $tieneInventario = DB::table('inventario_articulos')
        ->where('cotizacion_id', $cotizacionId)
        ->exists();
    if (! $tieneInventario) {
        return redirect()->route('cotizar.paso3')
            ->with('error', 'Debes agregar al menos un articulo al inventario para continuar.');
    }

    $request->validate([
        'acepta_terminos'   => ['required', 'in:1'],
        'acepta_inventario' => ['required', 'in:1'],
    ], [
        'acepta_terminos.required'   => 'Debes aceptar los términos del contrato.',
        'acepta_terminos.in'         => 'Debes aceptar los términos del contrato.',
        'acepta_inventario.required' => 'Debes confirmar que el inventario es veraz.',
        'acepta_inventario.in'       => 'Debes confirmar que el inventario es veraz.',
    ]);

    // ── 1. Calcular precio final usando PricingService ──
    $pricingService = app(PricingService::class);
    $precios = $pricingService->calcularPrecioFinal($cotizacionId);
    
    $totalFinal = $precios['total_final'];
    $priceMin = (int) round($precios['subtotal_con_modalidad']);
    $priceMax = (int) round($totalFinal);

    // ── 2. Guardar aceptación en tabla correspondiente ─
    DB::table('aceptaciones_contrato')->insert([
        'cotizacion_id'     => $cotizacionId,
        'acepta_terminos'   => 1,
        'acepta_inventario' => 1,
        'ip'                => $request->ip(),
        'aceptado_en'       => now(),
    ]);

    // ── 3. Actualizar cotización con precio final ──────
    DB::table('cotizaciones')
        ->where('id', $cotizacionId)
        ->update([
            'precio_estimado_min' => $priceMin,
            'precio_estimado_max' => $priceMax,
            'precio_final'        => $totalFinal,
            'estatus'             => 'Prospecto',
            'actualizado_en'      => now(),
        ]);

    // ── 4. Generar documentos (Word + PDF) y enviar ─────
    try {
        $docService = app(DocumentGenerationService::class);
        $resultado = $docService->generarTodo($cotizacionId);

        if (!$resultado['success']) {
            \Log::warning("Fallo al generar documentos: " . $resultado['message']);
            return back()->with('error', 'La cotización se guardó, pero hubo un error enviando los documentos. Nuestro equipo lo revisará.');
        }
    } catch (\Exception $e) {
        \Log::error("Error crítico en DocumentGenerationService: " . $e->getMessage());
        return back()->with('error', 'Error al procesar el envío de la cotización. Por favor, intente de nuevo.');
    }

    // ── 5. Limpiar sesión del wizard ───────────────────
    $folio = session('cotizacion_folio');
    session()->forget([
        'cotizacion_id', 'cliente_id',
        'cotizador_paso1', 'cotizador_paso2', 'cotizador_paso3',
        'modalidad', 'piso_origen', 'piso_destino',
        'elevador_origen', 'elevador_destino',
        'total_articulos_catalogo', 'total_articulos_especiales',
    ]);

    return redirect()->route('cotizar.gracias')
        ->with('folio', $folio);
}
    // ────────────────────────────────────────────────────────
    //  POST /cotizar/paso3  →  Procesar Paso 3
    //
    //  Tablas que se escriben:
    //
    //    1. inventario_articulos
    //       ├─ Artículos del catálogo (es_especial = 0)
    //       │    nombre, cantidad, m3, categoria
    //       └─ Artículos especiales   (es_especial = 1)
    //            nombre, cantidad, observaciones, fragil (auto)
    //
    //    2. fotos_anexo
    //         nombre_archivo, ruta_relativa, tipo_mime,
    //         tamanio_bytes, orden
    //
    //  El campo `fotos[]` llega como array de UploadedFile.
    //  Los JSON de inventario llegan como strings en:
    //    inventario_catalogo   → array de objetos del catálogo
    //    articulos_especiales  → array de objetos especiales
    // ────────────────────────────────────────────────────────
    public function paso3Post(Request $request)
    {
        // ── Guardia de sesión ──────────────────────────────
        $cotizacionId = session('cotizacion_id');
        $folio        = session('cotizacion_folio', 'SIN_FOLIO');
 
        if (! $cotizacionId) {
            return redirect()->route('cotizar.paso1')
                ->with('error', 'Sesión expirada. Por favor comienza de nuevo.');
        }
 
        // ── Validación ────────────────────────────────────
        $request->validate([
            'inventario_catalogo'  => ['nullable', 'string'],
            'articulos_especiales' => ['nullable', 'string'],
            'fotos'                => ['nullable', 'array', 'max:20'],
            'fotos.*'              => ['image', 'max:10240'],   // máx 10 MB c/u
        ], [
            'fotos.*.image' => 'Solo se permiten archivos de imagen (JPG, PNG, HEIC).',
            'fotos.*.max'   => 'Cada foto no debe superar los 10 MB.',
            'fotos.max'     => 'Puedes subir máximo 20 fotos.',
        ]);

        $catalogoJson = $request->input('inventario_catalogo', '[]');
        $especialesJson  = $request->input('articulos_especiales', '[]');
        $catalogoItems = json_decode($catalogoJson, true) ?? [];
        $especialesItems = json_decode($especialesJson, true) ?? [];

        if (count($catalogoItems) === 0 && count($especialesItems) === 0) {
            return back()
                ->withErrors(['inventario' => 'Debes agregar al menos un articulo al inventario para continuar.'])
                ->withInput();
        }
 
        // ── 1. Limpiar inventario anterior ────────────────
        //  (por si el usuario regresó y reenvió el paso)
        DB::table('inventario_articulos')
            ->where('cotizacion_id', $cotizacionId)
            ->delete();
 
        // Fotos anteriores: conservar las que el usuario mantuvo
        $keepFotos = json_decode($request->input('fotos_existentes', '[]'), true);
        $keepFotos = is_array($keepFotos) ? $keepFotos : [];
        $keepLookup = array_flip($keepFotos);

        $fotosAntiguas = DB::table('fotos_anexo')
            ->where('cotizacion_id', $cotizacionId)
            ->orderBy('orden')
            ->get();

        $fotosAConservar = [];
        foreach ($fotosAntiguas as $foto) {
            if (isset($keepLookup[$foto->ruta_relativa])) {
                $fotosAConservar[] = $foto;
                continue;
            }
            Storage::disk('public')->delete($foto->ruta_relativa);
        }

        DB::table('fotos_anexo')
            ->where('cotizacion_id', $cotizacionId)
            ->delete();
 
        // ── 2. Insertar artículos del catálogo ────────────
        $orden = 1;
 
        foreach ($catalogoItems as $item) {
            $nombre   = trim($item['nombre']   ?? '');
            $cantidad = (int) ($item['cantidad'] ?? 1);
            $m3       = isset($item['m3'])   ? (float) $item['m3']   : null;
            $cat      = trim($item['categoria'] ?? '');
 
            if ($nombre === '' || $cantidad <= 0) continue;
 
            DB::table('inventario_articulos')->insert([
                'cotizacion_id' => $cotizacionId,
                'nombre'        => $nombre,
                'cantidad'      => $cantidad,
                'm3'            => $m3,
                'categoria'     => $cat ?: null,
                'es_especial'   => 0,
                'fragil'        => 0,
                'observaciones' => $m3 ? "{$m3} m³" : null,
                'orden'         => $orden++,
            ]);
        }
 
        // ── 3. Insertar artículos especiales ──────────────
        foreach ($especialesItems as $item) {
            $nombre   = trim($item['articulo']       ?? '');
            $obs      = trim($item['observaciones']  ?? '');
            $cantidad = (int) ($item['cantidad']     ?? 1);
 
            if ($nombre === '' || $cantidad <= 0) continue;
 
            // Auto-detectar si menciona "frágil"
            $fragil = (int) (stripos($obs, 'frágil') !== false
                          || stripos($obs, 'fragil') !== false
                          || stripos($obs, 'delicado') !== false);
 
            DB::table('inventario_articulos')->insert([
                'cotizacion_id' => $cotizacionId,
                'nombre'        => $nombre,
                'cantidad'      => $cantidad,
                'm3'            => null,       // especiales no tienen m³ predefinido
                'categoria'     => null,
                'es_especial'   => 1,
                'fragil'        => $fragil,
                'observaciones' => $obs ?: null,
                'orden'         => $orden++,
            ]);
        }
 
        // ── 4. Guardar fotos ──────────────────────────────
        //
        //  Ruta en disco:  storage/app/public/cotizaciones/{folio}/
        //  URL pública:    /storage/cotizaciones/{folio}/FotoN.jpg
        //
        $fotoOrden = 1;

        foreach ($fotosAConservar as $foto) {
            DB::table('fotos_anexo')->insert([
                'cotizacion_id' => $cotizacionId,
                'nombre_archivo'=> $foto->nombre_archivo,
                'ruta_relativa' => $foto->ruta_relativa,
                'tipo_mime'     => $foto->tipo_mime,
                'tamanio_bytes' => $foto->tamanio_bytes,
                'orden'         => $fotoOrden,
            ]);
            $fotoOrden++;
        }

        if ($request->hasFile('fotos')) {
            foreach ($request->file('fotos') as $foto) {
                if (! $foto->isValid()) continue;
 
                // Nombre seguro: Folio_Foto01.jpg
                $ext       = strtolower($foto->getClientOriginalExtension()) ?: 'jpg';
                $filename  = "{$folio}_Foto" . str_pad($fotoOrden, 2, '0', STR_PAD_LEFT) . ".{$ext}";
                $carpeta   = "cotizaciones/{$folio}";
 
                // Guardar en storage/app/public/cotizaciones/{folio}/
                $ruta = $foto->storeAs($carpeta, $filename, 'public');
 
                DB::table('fotos_anexo')->insert([
                    'cotizacion_id' => $cotizacionId,
                    'nombre_archivo'=> $filename,
                    'ruta_relativa' => $ruta,
                    'tipo_mime'     => $foto->getMimeType(),
                    'tamanio_bytes' => $foto->getSize(),
                    'orden'         => $fotoOrden,
                ]);
 
                $fotoOrden++;
            }
        }
 
        // ── 5. Guardar conteo en sesión (para Paso 4) ─────
        session([
            'total_articulos_catalogo'  => count($catalogoItems),
            'total_articulos_especiales'=> count($especialesItems),
        ]);
 
        // ── 6. Redirigir al Paso 4 ────────────────────────
        return redirect()->route('cotizar.paso4')
            ->with('success', 'Inventario guardado. ¡Ya casi terminas!');
    }
 
// ============================================================
//  NOTAS DE CONFIGURACIÓN
// ============================================================
//
//  Para que las fotos sean accesibles públicamente:
//
//    php artisan storage:link
//
//  Esto crea el symlink: public/storage → storage/app/public
//
//  Las fotos quedarán en:
//    storage/app/public/cotizaciones/{folio}/
//
//  Y serán accesibles en:
//    https://tudominio.com/storage/cotizaciones/{folio}/archivo.jpg
//
//  En Hostinger con Laravel, asegúrate de que
//  APP_URL en .env apunte al dominio correcto.
// ============================================================

    public function gracias()
    {
        return view('gracias');
    }
}