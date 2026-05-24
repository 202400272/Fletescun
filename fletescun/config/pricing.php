<?php
/**
 * config/pricing.php
 * 
 * Configuración de costos operativos y tarifas para el cálculo
 * automático de precios en el cotizador de FletesCun.
 */

return [
    /*
    |--------------------------------------------------------------------------
    | TARIFA POR VOLUMEN (m³)
    |--------------------------------------------------------------------------
    | Costo fijo por metro cúbico de carga
    */
    'tarifa_por_m3' => 1300,  // $1,300 MXN por m³

    /*
    |--------------------------------------------------------------------------
    | COSTOS OPERATIVOS MENSUALES
    |--------------------------------------------------------------------------
    | Base para calcular costo por kilómetro recorrido
    | 
    | Fórmula:
    * Total Mensual = suma de todos estos costos
    * Km/mes estimados = 3,000 km
    * Costo por km = Total Mensual / Km/mes
    */
    'costos_operativos' => [
        'combustible'      => 8000,    // Gasolina/diésel mensual
        'neumaticos'       => 1200,    // Desgaste de neumáticos
        'mantenimiento'    => 2000,    // Servicio, reparaciones
        'seguro'           => 1500,    // Póliza vehicular
        'patente'          => 800,     // Tenencia / refrendo
        'amortizacion'     => 4000,    // Depreciación del vehículo
        'operador'         => 6000,    // Salario / prestaciones del chofer
    ],

    /*
    |--------------------------------------------------------------------------
    | KILÓMETROS ESTIMADOS MENSUALES
    |--------------------------------------------------------------------------
    | Base para normalizar costo/km
    */
    'km_mes_estimados' => 3000,

    /*
    |--------------------------------------------------------------------------
    | DISTANCIA ESTIMADA (km) - Para cálculos si no está disponible GPS
    |--------------------------------------------------------------------------
    | Si no se puede calcular automáticamente (falta integración Google Maps),
    | se usa esta distancia estimada como fallback.
    | Personaliza según tu zona geográfica típica (Cancún, Playa del Carmen, etc.)
    */
    'distancia_km_estimada' => 25,  // Default 25 km (Cancún y alrededores)

    /*
    |--------------------------------------------------------------------------
    | IVA (%)
    |--------------------------------------------------------------------------
    */
    'iva_pct' => 16,

    /*
    |--------------------------------------------------------------------------
    | COSTOS FIJOS ADICIONALES
    |--------------------------------------------------------------------------
    | Montos que se suman directamente al total (antes de IVA)
    */
    'costos_fijos' => [
        'maniobra_carga'      => 500,   // Por evento de carga
        'maniobra_descarga'   => 500,   // Por evento de descarga
        'proteccion'          => 300,   // Embalaje básico
    ],

    /*
    |--------------------------------------------------------------------------
    | AJUSTE POR MODALIDAD
    |--------------------------------------------------------------------------
    | Multiplicador de precio según tipo de servicio
    */
    'multiplicador_modalidad' => [
        'Exclusivo'   => 1.0,    // Precio completo
        'Compartido'  => 0.65,   // 35% descuento
    ],

    /*
    |--------------------------------------------------------------------------
    | SURCHARGE POR PISO (MXN)
    |--------------------------------------------------------------------------
    | Costo adicional por cada piso (después de considerar elevador)
    | Fórmula: (piso_origen + piso_destino - elevador_desc) * surcharge_por_piso
    */
    'surcharge_por_piso' => 200,

    /*
    |--------------------------------------------------------------------------
    | DESCUENTO POR ELEVADOR (pisos)
    |--------------------------------------------------------------------------
    | Si hay elevador, se descuentan estos pisos del surcharge
    */
    'descuento_elevador_pisos' => 2,

    /*
    |--------------------------------------------------------------------------
    | PISO MAP (para conversión a número)
    |--------------------------------------------------------------------------
    */
    'piso_map' => [
        'Planta baja'    => 0,
        '1er piso'       => 1,
        '2do piso'       => 2,
        '3er piso'       => 3,
        '4to piso'       => 4,
        '5to piso'       => 5,
        '6to piso o más' => 6,
    ],
];
