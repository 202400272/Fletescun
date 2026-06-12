<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cotizador (Paso 4) - Correo automático con documentación
    |--------------------------------------------------------------------------
    |
    | NOTA DE SEGURIDAD:
    | - No coloques credenciales en el código.
    | - SMTP_USER y SMTP_PASSWORD deben venir de variables de entorno.
    */

    'to' => env('COTIZACION_MAIL_TO'),

    'from' => [
        // Se usa la cuenta SMTP como remitente oficial
        'address' => env('SMTP_USER'),
        'name' => env('COTIZACION_MAIL_FROM_NAME', 'FletesCun'),
    ],

    'smtp' => [
        'host' => env('SMTP_HOST', 'smtp.gmail.com'),
        'port' => (int) env('SMTP_PORT', 587),
        'user' => env('SMTP_USER'),
        'password' => env('SMTP_PASSWORD'),
    ],

    // Path público del logo que se incrusta como imagen remota.
    'logo_path' => env('COTIZACION_MAIL_LOGO_PATH', 'img/logo_fletes.png'),
];
