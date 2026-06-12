# 🚚 FletesCun - Sistema de Cotización de Mudanzas

**Extensión: Automatización PostCotización - Generación de Documentos y Cálculo de Precios**

---

## 📌 Qué es esta extensión

Se ha agregado al sistema existente de 4 pasos:

1. ✅ **Cálculo automático de precios** (basado en m³ y km)
2. ✅ **Generación de Carta Porte Word** (.docx) automática
3. ✅ **Generación de PDF anexo** (fotos + inventario)
4. ✅ **Envío automático por correo** a gerencia
5. ✅ **Registro en BD** de todos los documentos

### Lo que NO cambió
- ✓ Flujo de 4 pasos (intacto)
- ✓ Captura de datos (igual)
- ✓ Vistas y sesiones (sin cambios)

---

## 🚀 Instalación Rápida (15 min)

### 1. Instalar dependencias
```bash
cd c:\xampp\htdocs\Fletescun\fletescun

composer require phpoffice/phpword
composer require barryvdh/laravel-dompdf
composer dump-autoload
```

### 2. Crear directorios
```bash
mkdir -p storage/app/public/documentos
chmod -R 755 storage/app/public/
```

### 3. Enlazar storage
```bash
php artisan storage:link
```

### 4. Configurar correo en `.env`
```env
MAIL_FROM_ADDRESS=gerencia@fletescun.com
MAIL_FROM_NAME="FletesCun"
```

### 5. ¡Listo! Probar en: `http://localhost/cotizar/paso1`

---

## 📚 Documentación

- **[CHECKLIST_INSTALACION.md](CHECKLIST_INSTALACION.md)** ← **EMPEZAR AQUÍ**
  - Pasos de instalación numerados
  - Verificaciones

- **[DOCUMENTACION_EXTENSION_POSLICITACION.md](DOCUMENTACION_EXTENSION_POSLICITACION.md)** ← **Para entender todo**
  - Descripción de cada Service
  - Fórmulas de cálculo
  - Tablas BD utilizadas
  - Troubleshooting

---

## 🏗️ Arquitectura

```
Paso 4 → Acepta términos
   ↓
CotizadorController::paso4Post()
   ↓
   ├─ PricingService (calcula precio real)
   ├─ DocumentGenerationService (orquestador)
   │  ├─ CartaPorteService (genera Word)
   │  ├─ AnexoFotograficoService (genera PDF)
   │  ├─ CotizacionMailService (envía correo SMTP)
   │  ├─ CotizacionDocumentacionMail (mailable con adjuntos)
   │  └─ Registra en BD
   ↓
/cotizar/gracias (éxito)
```

---

## 💾 Archivos Creados

### Configuración
- `config/pricing.php` - Costos y tarifas

### Services (Lógica)
- `app/Services/PricingService.php`
- `app/Services/CartaPorteService.php`
- `app/Services/AnexoFotograficoService.php`
- `app/Services/DocumentGenerationService.php`

### Mail
- `app/Services/Mail/CotizacionMailService.php`
- `app/Mail/CotizacionDocumentacionMail.php`
- `resources/views/emails/cotizacion-documentacion.blade.php`

### Controlador
- `app/Http/Controllers/CotizadorController.php` (actualizado)

---

## 💰 Fórmula de Precio

```
TOTAL FINAL = ((Volumen + Distancia + Pisos + Fijos) × Modalidad) + IVA 16%

Donde:
- Volumen = m³ × $1,300
- Distancia = km × (Costos Operativos / km_mes)
- Pisos = surcharge si hay escaleras sin elevador
- Fijos = maniobra + protección
- Modalidad = 1.0 (Exclusivo) o 0.65 (Compartido)
- IVA = 16%
```

**Costos Operativos Mensuales:**
- Combustible: $8,000
- Neumáticos: $1,200
- Mantenimiento: $2,000
- Seguro: $1,500
- Patente: $800
- Amortización: $4,000
- Operador: $6,000
- **Total: $24,500**
- **Costo/km: $24,500 ÷ 3,000 km = $8.17/km**

---

## 📋 Checklist de Instalación

```
[ ] Instalar composer require phpoffice/phpword
[ ] Instalar composer require barryvdh/laravel-dompdf
[ ] Crear directorio storage/app/public/documentos/
[ ] Ejecutar php artisan storage:link
[ ] Configurar MAIL_FROM_ADDRESS en .env
[ ] Probar flujo completo (Paso 1 → Paso 4)
[ ] Verificar archivos en storage/app/public/documentos/
[ ] Verificar correo enviado a gerencia
[ ] Validar precios en BD
```

👉 **Detalle en [CHECKLIST_INSTALACION.md](CHECKLIST_INSTALACION.md)**

---

## 🧪 Probar Rápido

1. Abre `http://localhost/cotizar/paso1`
2. Completa todos los pasos
3. En Paso 4, marca checkboxes y envía
4. Espera redirección a `/cotizar/gracias`
5. Verifica:
   - ✓ Archivos en `storage/app/public/documentos/`
   - ✓ Correo en bandeja de gerencia
   - ✓ BD: `SELECT * FROM documentos_generados ORDER BY creado_en DESC LIMIT 2;`

---

## ⚙️ Personalizar Costos

Editar `config/pricing.php`:

```php
// Tarifa por m³
'tarifa_por_m3' => 1300,  // Cambiar aquí

// Costos mensuales
'costos_operativos' => [
    'combustible'   => 8000,
    'neumaticos'    => 1200,
    'mantenimiento' => 2000,
    'seguro'        => 1500,
    'patente'       => 800,
    'amortizacion'  => 4000,
    'operador'      => 6000,
],
```

Luego: `php artisan config:clear`

---

## 🐛 Problemas Comunes

| Problema | Solución |
|----------|----------|
| `Class PricingService not found` | `composer dump-autoload` |
| Archivos no se crean | Verificar permisos: `chmod -R 755 storage/app/public/` |
| Correos no se envían | Revisar `.env` MAIL_* |
| "Storage not found" | Ejecutar: `php artisan storage:link` |

👉 **Más troubleshooting en [DOCUMENTACION_EXTENSION_POSLICITACION.md](DOCUMENTACION_EXTENSION_POSLICITACION.md)**

---

## 📊 Tablas BD Utilizadas

Todas **ya existen** en esquema v2:
- `cotizaciones` - `precio_final` (nuevo campo)
- `aceptaciones_contrato` - registro con precio real
- `documentos_generados` - Word + PDF
- `notificaciones_correo` - log de envíos
- `inventario_articulos` - m³ para cálculo
- `fotos_anexo` - para PDF

---

## 🔍 Validar Instalación

```bash
# Ver si las clases existen
php artisan tinker

# Probar PricingService
$service = app('App\Services\PricingService');
echo "OK";

# Verificar config
config('pricing.tarifa_por_m3');  # Debe retornar 1300
```

---

## 📞 Soporte

**Logs:** `storage/logs/laravel.log`

**Verificar BD:**
```sql
SELECT * FROM documentos_generados ORDER BY creado_en DESC LIMIT 1;
SELECT * FROM notificaciones_correo ORDER BY creado_en DESC LIMIT 1;
```

---

## ✅ Estado del Proyecto

- ✅ Cálculo de precios automatizado
- ✅ Generación de Carta Porte Word
- ✅ Generación de PDF anexo
- ✅ Envío por correo a gerencia
- ✅ Registro de documentos en BD
- ✅ Manejo de errores y logging
- ✅ Documentación completa
- ⏳ **Próximo:** Configuración final y pruebas en producción

---

**Versión:** 1.0 PostCotización  
**Última actualización:** Mayo 2026  
**Framework:** Laravel 11  
**PHP:** 8.1+
