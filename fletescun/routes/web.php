<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InicioController;
use App\Http\Controllers\CotizadorController;

// Ruta principal que dirige a la vista de inicio (el HTML que subiste)
Route::get('/', [InicioController::class, 'index'])->name('inicio');


// ── Grupo del cotizador (sin middleware de auth) ──────────────
Route::prefix('cotizar')->name('cotizar.')->group(function () {

    // Paso 1 – Contacto y Ruta
    Route::get( '/',       [CotizadorController::class, 'paso1']    )->name('paso1');
    Route::post('/',       [CotizadorController::class, 'paso1Post'])->name('paso1.post');

    // Paso 2 – Logística y Detalles  (pendiente de implementar)
    Route::get( '/paso2',  [CotizadorController::class, 'paso2']    )->name('paso2');
    Route::post('/paso2',  [CotizadorController::class, 'paso2Post'])->name('paso2.post');

    // Paso 3 – Inventario y Fotos  (pendiente de implementar)
    Route::get( '/paso3',  [CotizadorController::class, 'paso3']    )->name('paso3');
    Route::post('/paso3',  [CotizadorController::class, 'paso3Post'])->name('paso3.post');

    // Paso 4 – Cotización Final  (pendiente de implementar)
    Route::get( '/paso4',  [CotizadorController::class, 'paso4']    )->name('paso4');
    Route::post('/paso4',  [CotizadorController::class, 'paso4Post'])->name('paso4.post');

    // Ruta de agradecimiento
    Route::get('/gracias', [CotizadorController::class, 'gracias'])->name('gracias');

});


// ============================================================
//  .env  —  Agrega estas variables a tu archivo .env
// ============================================================
//
//  GOOGLE_MAPS_KEY=AIzaSy_TU_CLAVE_AQUI
//
//
//  config/services.php  —  Agrega esta entrada:
// ============================================================
//
//  'google_maps' => [
//      'key' => env('GOOGLE_MAPS_KEY', ''),
//  ],
//
//
// ============================================================
//  INSTRUCCIONES DE INSTALACIÓN
// ============================================================
//
//  1. Copia los archivos a sus rutas correctas:
//
//     paso1.blade.php        → resources/views/cotizador/paso1.blade.php
//     CotizadorController.php → app/Http/Controllers/CotizadorController.php
//
//  2. Crea la carpeta de vistas si no existe:
//     mkdir -p resources/views/cotizador
//
//  3. Agrega las rutas de este archivo a tu routes/web.php
//
//  4. Agrega en config/services.php:
//       'google_maps' => ['key' => env('GOOGLE_MAPS_KEY', '')],
//
//  5. Agrega en .env:
//       GOOGLE_MAPS_KEY=AIzaSy_TU_CLAVE_AQUI
//
//  6. Habilita estas APIs en Google Cloud Console:
//       - Maps JavaScript API
//       - Places API
//       - Directions API
//       - Geocoding API
//
//  7. Limpia caché:
//       php artisan config:clear
//       php artisan route:clear
//       php artisan view:clear
//
// ============================================================
//  CAMPOS QUE SE PERSISTEN EN ESTE PASO
// ============================================================
//
//  Tabla: clientes
//    id, nombre, telefono, correo, ip_origen
//
//  Tabla: cotizaciones
//    id, folio, cliente_id,
//    direccion_origen, direccion_destino, fecha_ideal,
//    acceso_estacionamiento_origen, acceso_estacionamiento_destino,
//    distancia_km  (calculada por Google Maps Directions API)
//    tipo_servicio = 'Exclusivo' (default, se ajusta en Paso 2)
//    estatus = 'Prospecto'
//
//  Sesión (para los pasos siguientes):
//    cotizacion_id, cotizacion_folio, cliente_id,
//    lat_origen, lng_origen, lat_destino, lng_destino, distancia_km
//