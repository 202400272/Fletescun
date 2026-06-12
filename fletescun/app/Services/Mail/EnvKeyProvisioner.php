<?php

namespace App\Services\Mail;

use Illuminate\Support\Facades\Log;

class EnvKeyProvisioner
{
    /**
     * Asegura que ciertas keys existan en el archivo .env.
     * - Nunca escribe valores sensibles por defecto.
     * - No sobreescribe keys existentes.
     *
     * @return string[] lista de keys agregadas
     */
    public function ensureKeysExist(array $defaultsByKey): array
    {
        $envPath = base_path('.env');
        if (!is_file($envPath) || !is_readable($envPath) || !is_writable($envPath)) {
            return [];
        }

        $envContent = (string) file_get_contents($envPath);
        $added = [];

        foreach ($defaultsByKey as $key => $defaultValue) {
            $pattern = '/^\s*' . preg_quote((string) $key, '/') . '\s*=/m';
            if (preg_match($pattern, $envContent)) {
                continue;
            }

            $line = $key . '=' . $this->formatEnvValue($defaultValue);
            $envContent .= (str_ends_with($envContent, "\n") ? '' : "\n") . $line . "\n";
            $added[] = (string) $key;
        }

        if (!empty($added)) {
            file_put_contents($envPath, $envContent, LOCK_EX);
            Log::info('Variables de entorno añadidas para correo (sin valores sensibles).', [
                'keys' => $added,
            ]);
        }

        return $added;
    }

    public function ensureCotizacionMailKeysExist(): array
    {
        return $this->ensureKeysExist([
            // Credenciales (sin valores por defecto)
            'SMTP_USER' => '',
            'SMTP_PASSWORD' => '',

            // SMTP (no sensible)
            'SMTP_HOST' => 'smtp.gmail.com',
            'SMTP_PORT' => '587',

            // Destinatario de documentación
            'COTIZACION_MAIL_TO' => 'javierascenciomarquez@gmail.com',
            'COTIZACION_MAIL_FROM_NAME' => 'FletesCun',
            'COTIZACION_MAIL_LOGO_PATH' => 'img/logo_fletes.png',
        ]);
    }

    private function formatEnvValue(string $value): string
    {
        // Respetar valores vacíos
        if ($value === '') {
            return '';
        }

        // Comillas si trae espacios o caracteres problemáticos
        if (preg_match('/\s|#|"|\\\\/', $value)) {
            $escaped = str_replace('"', '\\"', $value);
            return '"' . $escaped . '"';
        }

        return $value;
    }
}
