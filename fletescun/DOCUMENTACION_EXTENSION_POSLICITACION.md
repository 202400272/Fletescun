# Extensión de FletesCun - Pos Cotización

## 📋 Descripción General

Se ha extendido el sistema de FletesCun para agregar la lógica **posterior a la cotización**, incluyendo:

1. **Cálculo automático de precios** (basado en m³, distancia e IVA)
2. **Generación automática de Carta Porte Word** (.docx)
3. **Generación automática de PDF anexo** (fotos + inventario)
4. **Envío automático por correo** a gerencia

### ✅ Qué NO se modificó

- ✓ Flujo de 4 pasos del cotizador (intacto)
- ✓ Lógica de sesiones y captura de datos
- ✓ Base de datos existente (reutiliza campos preparados)
- ✓ Vistas (paso1 a paso4)

---

## 🗂️ Archivos Creados

### 1. **Configuración** (`config/pricing.php`)
Define todos los costos operativos y tarifas:
```php
// Tarifa por m³
'tarifa_por_m3' => 1300,

// Costos operativos mensuales
'costos_operativos' => [
    'combustible'   => 8000,
    'neumaticos'    => 1200,
    'mantenimiento' => 2000,
    'seguro'        => 1500,
    'patente'       => 800,
    'amortizacion'  => 4000,
    'operador'      => 6000,
],

// Km/mes para normalizar costo por km
'km_mes_estimados' => 3000,

// IVA
'iva_pct' => 16,
```

### 2. **Services** (Lógica de Negocio)

#### `app/Services/PricingService.php`
**Responsabilidad:** Calcular precio final automático

**Métodos principales:**
- `calcularPrecioFinal(cotizacionId)` → Retorna array con desglose completo
  - Costo por volumen: m³ × $1,300
  - Costo por distancia: km × (costos_operativos / km_mes)
  - Costo por piso (surcharge)
  - Costos fijos (maniobra, protección)
  - Aplicar multiplicador por modalidad (Exclusivo/Compartido)
  - Calcular IVA 16%
  - Total final

- `obtenerDesglose(cotizacionId)` → Retorna desglose legible para documentos

**Fórmulas implementadas:**
```
Costo Volumen = ∑(cantidad × m³) × $1,300

Costo Operativo Mensual = sum(combustible, neumáticos, mantenimiento, seguro, patente, amortización, operador)

Costo por km = Costo Operativo / 3,000 km
Costo Distancia = distancia_km × Costo por km

Costo Piso = max(0, piso_origen + piso_destino - descuento_elevador) × $200

Subtotal Base = Costo Volumen + Costo Distancia + Costo Piso + Costos Fijos

Subtotal con Modalidad = Subtotal Base × multiplicador (1.0 = Exclusivo, 0.65 = Compartido)

IVA = Subtotal con Modalidad × 16%

Total Final = Subtotal con Modalidad + IVA
```

#### `app/Services/CartaPorteService.php`
**Responsabilidad:** Generar documento Word (.docx)

**Método principal:**
- `generar(cotizacionId)` → Retorna info del archivo generado

**Contenido del documento:**
- **Hoja 1:**
  - Carátula (Folio, lugar de expedición)
  - Datos de Origen (Remitente, dirección, piso, elevador, fecha/hora)
  - Datos de Destino (Destinatario, dirección, piso, elevador, fecha/hora estimada)
  - Inventario Declarado (tabla dinámica)
  - Servicios Adicionales (si los hay)
  - Descripción de Servicio (Unidad, Protección, Maniobras, Operador)
  - Desglose de Precios (tabla con todos los conceptos + IVA + Total)
  - Placa de la unidad

- **Hoja 2:**
  - Contrato de Prestación de Servicios
  - Número de contrato
  - Nombre del consumidor
  - Lugar y fecha de firma
  - 11 Cláusulas (Primera a Décima Primera)
  - Firmas (Proveedor: Javier Ascencio Marquez, Consumidor)

**Archivos generados:**
- `CartaPorte_{FOLIO}.docx` en `storage/app/public/documentos/`

#### `app/Services/AnexoFotograficoService.php`
**Responsabilidad:** Generar PDF anexo

**Método principal:**
- `generar(cotizacionId)` → Retorna info del archivo generado

**Contenido del PDF:**
- Portada (Folio, cliente, datos de contacto)
- Información del Cliente (origen, destino, modalidad)
- Galería de Fotografías (las subidas en Paso 3)
- Tabla de Inventario Declarado
- Resumen de Cotización (Desglose de precios + Total)

**Archivos generados:**
- `AnexoFotografico_{FOLIO}.pdf` en `storage/app/public/documentos/`

#### `app/Services/DocumentGenerationService.php`
**Responsabilidad:** Orquestador central

**Método principal:**
- `generarTodo(cotizacionId)` → Coordina todo el flujo
  1. Calcula precio final (PricingService)
  2. Genera Carta Porte (CartaPorteService)
  3. Genera Anexo PDF (AnexoFotograficoService)
  4. Registra en BD (documentos_generados)
  5. Envía por correo (Mailable)
  6. Registra en log (notificaciones_correo)
  7. Retorna resultado completo

**Retorna:**
```php
[
    'success'       => true,
    'message'       => 'Documentos generados y enviados exitosamente',
    'precios'       => [...],  // Desglose completo
    'documentos'    => [
        'carta_porte' => ['nombre_archivo', 'ruta_relativa', 'ruta_absoluta', 'tamanio_bytes'],
        'anexo'       => [...]
    ],
    'email_enviado' => true,
]
```

### 3. **Mail** (Correos)

#### `app/Mail/CotizacionGenerada.php`
**Responsabilidad:** Mailable para envío de cotización a gerencia

**Características:**
- Implementa `ShouldQueue` (procesamiento asincrónico)
- Adjunta ambos documentos (Word + PDF)
- Automáticamente identifica MIME types

#### `resources/views/emails/cotizacion-generada.blade.php`
**Template HTML** del correo con:
- Saludo personalizando
- Datos de la cotización
- Detalles del cliente
- Acciones recomendadas

### 4. **Controlador Actualizado**

#### `app/Http/Controllers/CotizadorController.php`
**Cambios en `paso4Post()`:**

**ANTES:** Calculaba precio simple basado en pisos

**AHORA:** 
```php
// 1. Calcula precio real usando PricingService
$pricingService = app(PricingService::class);
$precios = $pricingService->calcularPrecioFinal($cotizacionId);

// 2. Guarda aceptación con precio real
DB::table('aceptaciones_contrato')->insert([...]);

// 3. Genera documentos automáticamente
$docService = app(DocumentGenerationService::class);
$resultado = $docService->generarTodo($cotizacionId);

// 4. Redirige a gracias
return redirect()->route('cotizar.gracias')->with('folio', $folio);
```

---

## 🔧 Instalación y Configuración

### Paso 1: Instalar Dependencias

```bash
cd c:\xampp\htdocs\Fletescun\fletescun

# Generar documentos Word
composer require phpoffice/phpword

# Generar PDFs
composer require barryvdh/laravel-dompdf
```

### Paso 2: Enlazar Storage (si aún no existe)

```bash
php artisan storage:link
```

Esto crea un enlace simbólico `public/storage` → `storage/app/public/`

### Paso 3: Crear Directorio para Documentos

```bash
mkdir -p storage/app/public/documentos
chmod 755 storage/app/public/documentos
```

### Paso 4: Configurar Correo

Editar `.env`:
```env
MAIL_FROM_ADDRESS=gerencia@fletescun.com  # Dirección de gerencia
MAIL_FROM_NAME="FletesCun"
```

O configurable en `config/mail.php`

### Paso 5: Migración de BD (Verificación)

Las siguientes tablas **ya existen** según el esquema v2:
- ✓ `cotizaciones` (campos: precio_final, estatus)
- ✓ `aceptaciones_contrato` (precio_estimado_min/max)
- ✓ `documentos_generados` (registro de Word + PDF)
- ✓ `notificaciones_correo` (log de envíos)

**No se requieren migraciones nuevas.**

### Paso 6: Configuración de Costos (Opcional)

Editar `config/pricing.php` para ajustar:
- Tarifa por m³: `$1,300` (línea 13)
- Costos operativos mensuales (línea 15-22)
- Kilómetros/mes estimados: `3,000` (línea 25)
- IVA: `16%` (línea 28)
- Costos fijos: maniobra, protección (línea 36-40)

---

## 📊 Flujo de Ejecución Completo

```
1. Usuario completa Paso 1, 2, 3 (sin cambios)
   ↓
2. Usuario acepta términos en Paso 4
   ↓
3. Envía POST a /cotizar/paso4
   ↓
4. CotizadorController::paso4Post()
   ├─ Valida aceptaciones
   ├─ Llama PricingService→calcularPrecioFinal()
   │  ├─ Suma m³ del inventario
   │  ├─ Calcula: volumen × $1,300
   │  ├─ Calcula: distancia × costo_operativo/km
   │  ├─ Calcula: surcharge por piso
   │  ├─ Aplica multiplicador (Exclusivo/Compartido)
   │  ├─ Calcula IVA 16%
   │  └─ Retorna desglose completo
   ├─ Guarda en aceptaciones_contrato
   ├─ Actualiza cotizaciones con precio_final
   └─ Llama DocumentGenerationService→generarTodo()
      ├─ CartaPorteService→generar()
      │  ├─ Crea documento Word con PhpOffice
      │  ├─ Inserta datos dinámicamente
      │  ├─ Guarda en storage/app/public/documentos/
      │  └─ Retorna info del archivo
      │
      ├─ AnexoFotograficoService→generar()
      │  ├─ Genera HTML con galería de fotos
      │  ├─ Convierte HTML → PDF con DomPDF
      │  ├─ Guarda en storage/app/public/documentos/
      │  └─ Retorna info del archivo
      │
      ├─ Registra ambos archivos en documentos_generados
      │
      ├─ Envía Mailable CotizacionGenerada
      │  ├─ A: config('mail.from.address') (gerencia)
      │  ├─ Subject: "Cotización Generada - Folio {folio}"
      │  ├─ Adjunto 1: CartaPorte_{folio}.docx
      │  ├─ Adjunto 2: AnexoFotografico_{folio}.pdf
      │  └─ Registra en notificaciones_correo
      │
      └─ Retorna resultado (success, documentos, email_enviado)
   ↓
5. Limpia sesión del wizard
   ↓
6. Redirige a /cotizar/gracias con folio
   ↓
7. Usuario ve pantalla de confirmación
```

---

## 🗄️ Tablas BD Utilizadas

### `cotizaciones`
- `id`: UUID
- `precio_estimado_min`: DECIMAL (costo base sin IVA)
- `precio_estimado_max`: DECIMAL (=precio_estimado_min)
- `precio_final`: DECIMAL (nuevo, costo final con IVA) ← **ACTUALIZADO**
- `estatus`: ENUM (Prospecto, Confirmado, ...)

### `aceptaciones_contrato`
- `cotizacion_id`: CHAR(36)
- `acepta_terminos`: TINYINT(1)
- `acepta_inventario`: TINYINT(1)
- `ip`: VARCHAR(45)
- `precio_estimado_min`: DECIMAL ← **USA PRECIO REAL**
- `precio_estimado_max`: DECIMAL ← **USA PRECIO REAL**
- `aceptado_en`: TIMESTAMP

### `documentos_generados`
- `cotizacion_id`: CHAR(36)
- `tipo`: ENUM ('Carta Porte Word', 'Anexo Fotográfico PDF')
- `nombre_archivo`: VARCHAR(255)
- `ruta_relativa`: VARCHAR(500)
- `tamanio_bytes`: INT
- `enviado_al_jefe`: TINYINT(1) (1 = sent)
- `creado_en`: TIMESTAMP

### `notificaciones_correo`
- `cotizacion_id`: CHAR(36) (NULL si es error del sistema)
- `destinatario`: VARCHAR(120)
- `asunto`: VARCHAR(255)
- `tipo`: ENUM ('Nuevo Prospecto', 'OTP Acceso', 'Comprobante Recibido', ...)
- `estatus`: ENUM ('Enviado', 'Fallido')
- `error_msg`: TEXT (si falló)
- `creado_en`: TIMESTAMP

---

## 🧪 Prueba de Funcionamiento

### Escenario 1: Flujo Completo

1. Abre navegador → `http://localhost/cotizar/paso1`
2. Completa Paso 1 (cliente, origen, destino)
3. Completa Paso 2 (pisos, modalidad, servicios)
4. Completa Paso 3 (inventario, fotos)
5. En Paso 4, marca checkboxes y envía
6. **Esperado:**
   - Redirección a `/cotizar/gracias` ✓
   - Archivos generados en `storage/app/public/documentos/` ✓
   - Correo enviado a gerencia ✓
   - Registros en BD (documentos_generados, notificaciones_correo) ✓

### Escenario 2: Revisar Cálculo de Precio

Después de enviar Paso 4:
1. Abre terminal MySQL
```sql
USE fletescun;

SELECT 
    folio,
    precio_estimado_min,
    precio_estimado_max,
    precio_final
FROM cotizaciones
ORDER BY creado_en DESC
LIMIT 1;

SELECT * FROM aceptaciones_contrato
WHERE cotizacion_id = (SELECT id FROM cotizaciones ORDER BY creado_en DESC LIMIT 1);

SELECT * FROM documentos_generados
WHERE cotizacion_id = (SELECT id FROM cotizaciones ORDER BY creado_en DESC LIMIT 1);

SELECT * FROM notificaciones_correo
WHERE cotizacion_id = (SELECT id FROM cotizaciones ORDER BY creado_en DESC LIMIT 1);
```

### Escenario 3: Revisar Archivos

1. Abre `storage/app/public/documentos/`
2. Verifica que existan:
   - `CartaPorte_FC-YYYYMMDD-XXXXX.docx`
   - `AnexoFotografico_FC-YYYYMMDD-XXXXX.pdf`
3. Abre archivos para validar contenido

---

## 🐛 Troubleshooting

### Error: "Class PricingService not found"
**Causa:** El archivo no está en la ruta correcta o composer no hizo autoload

**Solución:**
```bash
composer dump-autoload
```

### Error: "PhpWord not found" o "PDF not found"
**Causa:** Las dependencias no están instaladas

**Solución:**
```bash
composer require phpoffice/phpword
composer require barryvdh/laravel-dompdf
```

### Error: "Storage disk not found" o archivos no se guardan
**Causa:** Storage link no está creado o permisos insuficientes

**Solución:**
```bash
# Crear enlace simbólico
php artisan storage:link

# Verificar permisos
chmod -R 755 storage/app/public/
```

### Correos no se envían
**Causa:** Configuración de mail incorrecta en `.env`

**Solución:**
```env
MAIL_FROM_ADDRESS=gerencia@fletescun.com
MAIL_MAILER=smtp  # o sendmail, mailgun, etc.
MAIL_HOST=tu-host
MAIL_PORT=587
MAIL_USERNAME=tu-usuario
MAIL_PASSWORD=tu-contraseña
```

### Precios incorrectos
**Causa:** config/pricing.php mal configurado

**Solución:**
```php
// Limpiar cache
php artisan config:clear

// Verificar valores en config/pricing.php
// Recalcular fórmula manualmente
```

---

## 📝 Nota de Integración

Todos los archivos creados siguen **Laravel best practices**:
- ✓ Estructura modular (separación de concerns)
- ✓ Inyección de dependencias
- ✓ Servicios reutilizables
- ✓ Manejo de excepciones
- ✓ Logging automático
- ✓ Queueable Mailables (async)
- ✓ Tipos y documentación inline

El cotizador de 4 pasos **sigue intacto** sin cambios en la lógica de sesiones o captura de datos.

---

## 📞 Soporte

Para debugging:
1. Revisar `storage/logs/laravel.log`
2. Ejecutar `php artisan tinker` para probar servicios manualmente
3. Verificar tablas en BD con queries de prueba
