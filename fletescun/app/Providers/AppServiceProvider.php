<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Mail\EnvKeyProvisioner;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Crear keys mínimas de correo si faltan (sin valores sensibles)
        try {
            app(EnvKeyProvisioner::class)->ensureCotizacionMailKeysExist();
        } catch (\Throwable $e) {
            // No bloquear la app por fallos al escribir .env
        }
    }
}
