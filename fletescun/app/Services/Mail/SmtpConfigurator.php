<?php

namespace App\Services\Mail;

use Illuminate\Support\Arr;

class SmtpConfigurator
{
    /**
     * Aplica configuración SMTP en tiempo de ejecución sin exponer secretos.
     */
    public function apply(array $settings): void
    {
        $smtp = Arr::get($settings, 'smtp', []);
        $from = Arr::get($settings, 'from', []);

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => (string) Arr::get($smtp, 'host'),
            'mail.mailers.smtp.port' => (int) Arr::get($smtp, 'port'),
            'mail.mailers.smtp.username' => (string) Arr::get($smtp, 'user'),
            'mail.mailers.smtp.password' => (string) Arr::get($smtp, 'password'),
            'mail.from.address' => (string) Arr::get($from, 'address'),
            'mail.from.name' => (string) Arr::get($from, 'name'),
        ]);
    }
}
