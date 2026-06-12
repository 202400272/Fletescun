# ✅ CHECKLIST - Instalación de Extensión PostCotización

## Requisitos Previos
- [ ] Laravel 11 instalado
- [ ] PHP 8.1+
- [ ] Composer actualizado
- [ ] Base de datos lista (esquema v2)

---

## 1️⃣ Instalar Dependencias (2 min)

```bash
cd c:\xampp\htdocs\Fletescun\fletescun

# Generar Word (.docx)
composer require phpoffice/phpword

# Generar PDF
composer require barryvdh/laravel-dompdf

# Actualizar autoload
composer dump-autoload
```

**Verificar:** `composer.json` debe incluir ambos paquetes

---

## 2️⃣ Crear Directorios (1 min)

```bash
# Crear carpeta para documentos
mkdir -p storage/app/public/documentos
chmod 755 storage/app/public/documentos

# Crear carpeta temporal (opcional)
mkdir -p storage/temp
chmod 755 storage/temp
```

---

## 3️⃣ Enlazar Storage (1 min)

```bash
php artisan storage:link
```

**Esperado:** Aparece un enlace simbólico `public/storage` → `storage/app/public/`

---

## 4️⃣ Configurar Correo (2 min)

Editar `.env`:

```env
# ← Cambiar estos valores
MAIL_FROM_ADDRESS=gerencia@fletescun.com
MAIL_FROM_NAME="FletesCun"

# Si usas Gmail, Mailgun, etc. → configurar credenciales adicionales
```

**Verificar:** `php artisan config:cache && php artisan config:clear`

---

## 5️⃣ Archivos Creados ✓ (YA CREADOS)

- [x] `config/pricing.php` - Configuración de costos
- [x] `app/Services/PricingService.php` - Cálculo de precios
- [x] `app/Services/CartaPorteService.php` - Generador de Word
- [x] `app/Services/AnexoFotograficoService.php` - Generador de PDF
- [x] `app/Services/DocumentGenerationService.php` - Orquestador
- [x] `app/Services/Mail/CotizacionMailService.php` - Envío SMTP (centralizado)
- [x] `app/Mail/CotizacionDocumentacionMail.php` - Mailable con adjuntos
- [x] `resources/views/emails/cotizacion-documentacion.blade.php` - Plantilla corporativa
- [x] `app/Http/Controllers/CotizadorController.php` - Controlador actualizado
- [x] `DOCUMENTACION_EXTENSION_POSLICITACION.md` - Documentación completa

---

## 6️⃣ Probar Flujo Completo (5 min)

```bash
# Iniciar servidor (en otra terminal)
php artisan serve

# O usar XAMPP → http://localhost/cotizar/paso1
```

**Pasos de prueba:**
1. Llena Paso 1 (cliente, origen, destino)
2. Llena Paso 2 (pisos, servicios)
3. Llena Paso 3 (inventario, fotos)
4. En Paso 4, marca checkboxes y envía

**Esperado:**
- ✓ Redirección a `/cotizar/gracias`
- ✓ 2 archivos en `storage/app/public/documentos/`
- ✓ Correo enviado a gerencia (verificar spam)
- ✓ Registros en `documentos_generados` y `notificaciones_correo`

---

## 7️⃣ Verificar en BD

```sql
-- Conectar a DB
USE fletescun;

-- Ver última cotización
SELECT folio, precio_final, estatus FROM cotizaciones ORDER BY creado_en DESC LIMIT 1;

-- Ver aceptación con precio real
SELECT precio_estimado_min, precio_estimado_max FROM aceptaciones_contrato ORDER BY aceptado_en DESC LIMIT 1;

-- Ver documentos generados
SELECT tipo, nombre_archivo FROM documentos_generados ORDER BY creado_en DESC LIMIT 2;

-- Ver correos
SELECT destinatario, estatus FROM notificaciones_correo ORDER BY creado_en DESC LIMIT 1;
```

---

## 8️⃣ Personalizar Precios (Opcional)

Editar `config/pricing.php`:

```php
// Tarifa por m³
'tarifa_por_m3' => 1300,  // ← Cambiar aquí

// Costos operativos mensuales
'costos_operativos' => [
    'combustible'      => 8000,   // ← Ajustar
    'neumaticos'       => 1200,   // ← Ajustar
    'mantenimiento'    => 2000,   // ← Ajustar
    // ... resto
],

// Km/mes
'km_mes_estimados' => 3000,  // ← Ajustar si aplica
```

**Limpiar cache después:**
```bash
php artisan config:clear
```

---

## 🚨 Troubleshooting Rápido

| Problema | Solución |
|----------|----------|
| `Class not found` | `composer dump-autoload` |
| Archivos no se crean | `chmod -R 755 storage/app/public/` |
| Correos no envían | Revisar `.env` MAIL_* |
| Precios incorrectos | Revisar `config/pricing.php` |
| PDF vacío/corrupto | Verificar que `barryvdh/laravel-dompdf` está instalado |

---

## 📊 Fórmula de Precio (Para Validar)

```
TOTAL = ((m³ × $1,300) + (km × costo_operativo/km) + $1,300) × modalidad × (1 + 16%)

Ejemplo:
- Volumen: 5 m³ × $1,300 = $6,500
- Distancia: 100 km × $11.33/km = $1,133
- Piso/otros: $1,300
- Subtotal: $8,933
- Modalidad Exclusivo: × 1.0 = $8,933
- IVA 16%: × 1.16 = $10,362.28

Total Final ≈ $10,362.28
```

---

## ✅ Estado Final

- [ ] Dependencias instaladas
- [ ] Directorios creados
- [ ] Storage linked
- [ ] Correo configurado
- [ ] Archivos en lugar
- [ ] Prueba exitosa
- [ ] Precios validados

---

**📌 Tiempo estimado:** 15-20 minutos  
**📚 Más detalles:** Ver `DOCUMENTACION_EXTENSION_POSLICITACION.md`

