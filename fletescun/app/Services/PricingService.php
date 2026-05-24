<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

/**
 * PricingService
 * 
 * Calcula automáticamente el precio final basado en:
 * - Volumen total del inventario (m³)
 * - Distancia entre origen y destino (km)
 * - Costos operativos
 * - Modalidad de servicio (Exclusivo/Compartido)
 * - Servicios adicionales
 * - IVA
 */
class PricingService
{
    /**
     * Calcula el precio final completo para una cotización
     * 
     * @param string $cotizacionId UUID de la cotización
     * @return array Desglose detallado de costos
     *   - costo_volumen: m³ × tarifa
     *   - costo_distancia: km × costo_por_km
     *   - costo_fijos: maniobra + protección
     *   - subtotal_base: sum de anteriores
     *   - multiplicador_modalidad: factor Exclusivo/Compartido
     *   - subtotal_con_modalidad: subtotal × factor
     *   - iva_monto: subtotal × 16%
     *   - total_final: subtotal + iva
     *   - desglose: array con breakdown detallado
     */
    public function calcularPrecioFinal(string $cotizacionId): array
    {
        // Recuperar datos de cotización
        $cotizacion = DB::table('cotizaciones')
            ->where('id', $cotizacionId)
            ->first();

        if (!$cotizacion) {
            throw new \Exception("Cotización {$cotizacionId} no encontrada");
        }

        // Calcular volumen total
        $costoVolumen = $this->calcularCostoVolumen($cotizacionId);

        // Calcular costo por distancia
        // NOTA: Si distancia_km no está disponible (NULL), usa estimación por defecto
        // TODO: Integrar Google Maps API para cálculo automático
        $distancia = $cotizacion->distancia_km ?? config('pricing.distancia_km_estimada', 25);
        $costoDistancia = $this->calcularCostoDistancia($distancia);

        // Calcular costo de piso/elevador
        $costoPiso = $this->calcularCostoPiso(
            $cotizacion->piso_origen ?? '',
            $cotizacion->piso_destino ?? '',
            $cotizacion->elevador_origen ?? 0,
            $cotizacion->elevador_destino ?? 0
        );

        // Costos fijos (maniobra, protección)
        $costosFijos = $this->calcularCostosFijos();

        // Subtotal antes de modalidad
        $subtotalBase = $costoVolumen + $costoDistancia + $costoPiso + $costosFijos;

        // Aplicar multiplicador por modalidad
        $modalidad = $cotizacion->tipo_servicio ?? 'Exclusivo';
        $multiplicador = config('pricing.multiplicador_modalidad.' . $modalidad, 1.0);
        $subtotalConModalidad = $subtotalBase * $multiplicador;

        // Calcular IVA
        $ivaPct = config('pricing.iva_pct', 16);
        $ivaMonto = $subtotalConModalidad * ($ivaPct / 100);

        // Total final
        $totalFinal = $subtotalConModalidad + $ivaMonto;

        return [
            // Costos componentes
            'costo_volumen'          => round($costoVolumen, 2),
            'costo_distancia'        => round($costoDistancia, 2),
            'costo_piso'             => round($costoPiso, 2),
            'costos_fijos'           => round($costosFijos, 2),
            'subtotal_base'          => round($subtotalBase, 2),

            // Aplicación de modalidad
            'modalidad'              => $modalidad,
            'multiplicador_modalidad'=> $multiplicador,
            'subtotal_con_modalidad' => round($subtotalConModalidad, 2),

            // Impuestos
            'iva_pct'                => $ivaPct,
            'iva_monto'              => round($ivaMonto, 2),

            // Totales
            'total_final'            => round($totalFinal, 2),
            'total_final_int'        => (int) round($totalFinal),

            // Datos para referencia
            'distancia_km'           => $cotizacion->distancia_km ?? 0,
            'volumen_m3'             => round($this->obtenerVolumenTotal($cotizacionId), 2),
            'piso_origen'            => $cotizacion->piso_origen,
            'piso_destino'           => $cotizacion->piso_destino,
            'elevador_origen'        => $cotizacion->elevador_origen,
            'elevador_destino'       => $cotizacion->elevador_destino,
        ];
    }

    /**
     * Calcula el costo basado en volumen (m³)
     * 
     * Fórmula: volumen_total × tarifa_por_m3
     */
    private function calcularCostoVolumen(string $cotizacionId): float
    {
        $volumenTotal = $this->obtenerVolumenTotal($cotizacionId);
        $tarifaPorM3 = config('pricing.tarifa_por_m3', 1300);

        return $volumenTotal * $tarifaPorM3;
    }

    /**
     * Obtiene el volumen total sumando m³ de todos los artículos
     */
    private function obtenerVolumenTotal(string $cotizacionId): float
    {
        return (float) DB::table('inventario_articulos')
            ->where('cotizacion_id', $cotizacionId)
            ->sum(DB::raw('COALESCE(cantidad * m3, 0)'));
    }

    /**
     * Calcula el costo basado en distancia recorrida
     * 
     * Fórmula:
     * - Total Costos Operativos Mensuales = suma config
     * - Costo por km = Total / km_mes_estimados
     * - Costo Distancia = distancia_km × costo_por_km
     */
    private function calcularCostoDistancia(float $distanciaKm): float
    {
        if ($distanciaKm <= 0) {
            return 0;
        }

        $costosOperativos = config('pricing.costos_operativos', []);
        $totalOperativo = array_sum($costosOperativos);
        $kmMesEstimados = config('pricing.km_mes_estimados', 3000);

        $costoPorKm = $totalOperativo / $kmMesEstimados;

        return $distanciaKm * $costoPorKm;
    }

    /**
     * Calcula el costo adicional por pisos (surcharge)
     * 
     * Fórmula:
     * - suma = piso_origen + piso_destino
     * - descuentoElevador = (elevador_origen ? 2 : 0) + (elevador_destino ? 2 : 0)
     * - pisos_netos = max(0, suma - descuentoElevador)
     * - costo = pisos_netos × surcharge_por_piso
     */
    private function calcularCostoPiso(
        string $pisoOrigen,
        string $pisoDestino,
        int $elevadorOrigen,
        int $elevadorDestino
    ): float {
        $pisoMap = config('pricing.piso_map', []);
        $surchargePerPiso = config('pricing.surcharge_por_piso', 200);
        $descuentoElevador = config('pricing.descuento_elevador_pisos', 2);

        $originFloor = $pisoMap[$pisoOrigen] ?? 0;
        $destFloor = $pisoMap[$pisoDestino] ?? 0;

        $descuento = ($elevadorOrigen ? $descuentoElevador : 0) +
                     ($elevadorDestino ? $descuentoElevador : 0);

        $pisosNetos = max(0, $originFloor + $destFloor - $descuento);

        return $pisosNetos * $surchargePerPiso;
    }

    /**
     * Calcula costos fijos (maniobra de carga, descarga, protección)
     */
    private function calcularCostosFijos(): float
    {
        $costos = config('pricing.costos_fijos', []);
        return (float) array_sum($costos);
    }

    /**
     * Obtiene el desglose detallado en formato legible para documentos
     */
    public function obtenerDesglose(string $cotizacionId): array
    {
        $precios = $this->calcularPrecioFinal($cotizacionId);

        return [
            'volumen' => [
                'm3_total'      => $precios['volumen_m3'],
                'tarifa_por_m3' => config('pricing.tarifa_por_m3'),
                'subtotal'      => $precios['costo_volumen'],
                'concepto'      => 'Volumen de carga (m³)',
            ],
            'distancia' => [
                'km'            => $precios['distancia_km'],
                'costo_por_km'  => round(
                    array_sum(config('pricing.costos_operativos', [])) /
                    config('pricing.km_mes_estimados', 3000),
                    4
                ),
                'subtotal'      => $precios['costo_distancia'],
                'concepto'      => 'Transporte (kilómetro)',
            ],
            'piso' => [
                'origen'        => $precios['piso_origen'],
                'destino'       => $precios['piso_destino'],
                'subtotal'      => $precios['costo_piso'],
                'concepto'      => 'Maniobra por piso',
            ],
            'fijos' => [
                'subtotal'      => $precios['costos_fijos'],
                'concepto'      => 'Carga, descarga y protección',
            ],
            'resumen' => [
                'subtotal_base'          => $precios['subtotal_base'],
                'multiplicador_modalidad'=> $precios['multiplicador_modalidad'],
                'modalidad'              => $precios['modalidad'],
                'subtotal_con_modalidad' => $precios['subtotal_con_modalidad'],
                'iva_pct'                => $precios['iva_pct'],
                'iva_monto'              => $precios['iva_monto'],
                'total_final'            => $precios['total_final'],
            ],
        ];
    }
}
