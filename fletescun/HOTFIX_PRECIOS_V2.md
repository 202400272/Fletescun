# 🔧 HOTFIX - Cálculo de Precios Reales + Error de BD

**Fecha:** 24 May 2026  
**Versión:** 1.1 PostCotización  
**Estado:** ✅ CORREGIDO

---

## 🚨 Problemas Reportados

### 1️⃣ Error de BD - `aceptaciones_contrato`
```
SQLSTATE[42S22]: Unknown column 'precio_estimado_min' in 'field list'
```
**Causa:** El controlador intentaba insertar columnas que NO existen en la tabla `aceptaciones_contrato`.

**Solución:** Eliminé las 2 líneas que insertaban precios en esa tabla. Los precios se guardan correctamente en `cotizaciones`.

---

### 2️⃣ Precios Fijos - No Calculaban Realmente
```
Paso 4 siempre mostraba: $4,035 - $4,681 MXN (independientemente del inventario)
```
**Causa:** 
- Precios hardcodeados en `paso4.blade.php`: `$baseMin = 6500; $baseMax = 12000`
- No consultaban volumen (m³) del inventario
- No consideraban distancia entre origen/destino
- Distancia no se calculaba (faltaba integración Google Maps)

**Solución:** 
- ✅ Actualicé `paso4()` para calcular precios REALES usando `PricingService`
- ✅ Pasé precios calculados al blade
- ✅ Agregué distancia estimada (25 km default) en `config/pricing.php`
- ✅ PricingService ahora calcula volumen total desde inventario real

---

## 📝 Cambios Realizados

### 1. `app/Http/Controllers/CotizadorController.php`

#### paso4Post() - CORREGIDO
```php
// ❌ ANTES (líneas eliminadas)
DB::table('aceptaciones_contrato')->insert([
    'precio_estimado_min' => $priceMin,    // ← ELIMINADO
    'precio_estimado_max' => $priceMax,    // ← ELIMINADO
    // ... resto
]);

// ✅ AHORA (correcto)
DB::table('aceptaciones_contrato')->insert([
    'cotizacion_id'     => $cotizacionId,
    'acepta_terminos'   => 1,
    'acepta_inventario' => 1,
    'ip'                => $request->ip(),
    'aceptado_en'       => now(),
]);

// Los precios se guardan en la tabla CORRECTA: cotizaciones
DB::table('cotizaciones')->update([
    'precio_estimado_min' => $priceMin,
    'precio_estimado_max' => $priceMax,
    'precio_final'        => $totalFinal,
    // ...
]);
```

#### paso4() - ACTUALIZADO PARA CALCULAR PRECIOS REALES
```php
// ✅ NUEVO: Calcular precios usando PricingService
$pricingService = app(PricingService::class);
$preciosReales  = $pricingService->calcularPrecioFinal($cotizacionId);

$priceMin = (int) round($preciosReales['subtotal_con_modalidad']);
$priceMax = (int) round($preciosReales['total_final']);

// ✅ Pasar precios calculados al blade
return view('paso4', [
    'priceMin' => $priceMin,
    'priceMax' => $priceMax,
    'preciosDetalle' => $preciosReales,
]);
```

---

### 2. `resources/views/paso4.blade.php`

#### ANTES (precios fijos)
```php
$baseMin = 6500; $baseMax = 12000;
$mult    = (strtolower($modalidad) === 'compartido') ? 0.65 : 1.0;
$priceMin = (int)(round(($baseMin * $mult + $floorSurcharge) / 100) * 100);
$priceMax = (int)(round(($baseMax * $mult + $floorSurcharge) / 100) * 100);
```

#### AHORA (precios reales)
```php
/* Precios reales calculados en paso4() usando PricingService */
$priceMin = $priceMin ?? 6500;   // fallback si no viene
$priceMax = $priceMax ?? 12000;
```

---

### 3. `app/Services/PricingService.php`

#### Distancia Inteligente
```php
// ❌ ANTES
$costoDistancia = $this->calcularCostoDistancia($cotizacion->distancia_km ?? 0);

// ✅ AHORA
$distancia = $cotizacion->distancia_km ?? config('pricing.distancia_km_estimada', 25);
$costoDistancia = $this->calcularCostoDistancia($distancia);
```

---

### 4. `config/pricing.php`

#### AGREGADO: Distancia Estimada
```php
'distancia_km_estimada' => 25,  // Default 25 km (Cancún)
```

---

## 🧪 Cómo Probar

### Paso 1: Limpiar cache Laravel
```bash
cd c:\xampp\htdocs\Fletescun\fletescun
php artisan config:clear
php artisan cache:clear
composer dump-autoload
```

### Paso 2: Ir al cotizador
```
http://localhost/cotizar/paso1
```

### Paso 3: Completar todos los pasos
- **Paso 1:** Cliente, origen, destino, fecha
- **Paso 2:** Pisos, elevador, modalidad
- **Paso 3:** Inventario (IMPORTANTE: declara m³ reales)
  - Ejemplo: Sofá 3 cuerpos (2.5 m³) x 1
  - Ejemplo: Cama matrimonial (1.2 m³) x 1
  - Ejemplo: Escritorio (0.8 m³) x 1
  - **Total: 4.5 m³**
- **Paso 4:** Aceptar términos

### Paso 4: Verificar Precios Calculados

**Ejemplo de cálculo:**
```
Volumen:       4.5 m³ × $1,300 = $5,850
Distancia:     25 km × $8.17/km = $204
Pisos:         2 pisos × $200 = $400
Fijos:         $1,300
Subtotal:      $7,754

Modalidad:     Exclusivo (× 1.0)
Subtotal:      $7,754

IVA 16%:       $1,241
═════════════════════════════
TOTAL:         $8,995 MXN
```

El precio que veas en **Paso 4** debe cambiar según:
- ✅ Cantidad de artículos (más volumen = más caro)
- ✅ Cantidad de pisos (con/sin elevador)
- ✅ Modalidad (Compartido es 35% más barato)

---

## ✅ Verificaciones

### En la BD (después de enviar Paso 4)

```sql
-- Ver cotización con precios reales
SELECT folio, 
       precio_estimado_min, 
       precio_estimado_max, 
       precio_final 
FROM cotizaciones 
ORDER BY creado_en DESC LIMIT 1;

-- Ver aceptación (SIN precios, solo checkboxes)
SELECT * FROM aceptaciones_contrato ORDER BY aceptado_en DESC LIMIT 1;

-- Ver documentos generados
SELECT tipo, nombre_archivo FROM documentos_generados 
ORDER BY creado_en DESC LIMIT 2;
```

**Esperado:**
- `precio_estimado_min` y `precio_estimado_max` ≠ NULL ✅
- `aceptaciones_contrato` SIN columnas de precio ✅
- 2 documentos creados (Word + PDF) ✅

---

## 🔍 Troubleshooting

| Problema | Solución |
|----------|----------|
| "Class PricingService not found" | `composer dump-autoload` |
| Precios siguen fijos | Limpiar cache: `php artisan config:clear` |
| Column not found error | ✅ YA ESTÁ ARREGLADO (ver línea 8-9 del hotfix) |
| Precios negativos o 0 | Verificar que el inventario tenga m³ asignados |

---

## 📊 Fórmula Final (Correcta)

```
TOTAL = ((m³_real × $1,300) + (km × $8.17) + pisos + fijos) × modalidad + IVA 16%
```

### Componentes:
- **m³_real:** Volumen real del inventario declarado ✅
- **km:** 25 km estimado (o Google Maps si se integra) ✅
- **$8.17/km:** $24,500 costos mensuales ÷ 3,000 km ✅
- **pisos:** $200 por piso sin elevador ✅
- **fijos:** $1,300 (maniobra + protección) ✅
- **modalidad:** 1.0 (Exclusivo) o 0.65 (Compartido) ✅
- **IVA:** 16% México ✅

---

## 🚀 Próximas Mejoras (No Bloqueantes)

1. **Integración Google Maps API**
   - Calcular distancia real desde API
   - Validar direcciones
   - Archivo: `app/Services/GoogleMapsService.php` (TODO)

2. **Ajuste Manual de Precios**
   - Panel CMS para que Javier ajuste precios finales
   - Basados en fotos y análisis

3. **Validación de Direcciones**
   - Autocompletado en Paso 1
   - Verificar que origen/destino sean válidos

---

**Estado Final:** ✅ Sistema de precios funcionando correctamente
