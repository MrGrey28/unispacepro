<?php

namespace App\Services;

use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class CorreoService
{
    /**
     * Envía un Mailable usando Brevo API (HTTPS puerto 443) si BREVO_API_KEY está configurada,
     * o mediante el mailer SMTP estándar de Laravel en entornos locales.
     */
    public static function enviar(string $destinatario, Mailable $mailable, ?string $nombreDestinatario = null): void
    {
        $brevoKey = config('services.brevo.key');

        if (!empty($brevoKey)) {
            $html = $mailable->render();
            $asunto = $mailable->envelope()->subject ?? config('app.name', 'UniSpacePro');

            $fromAddress = config('mail.from.address', 'greymelmoreno@gmail.com');
            $fromName = config('mail.from.name', 'UniSpacePro');

            $toItem = ['email' => $destinatario];
            if ($nombreDestinatario) {
                $toItem['name'] = $nombreDestinatario;
            }

            $response = Http::timeout(10)
                ->withHeaders([
                    'api-key' => $brevoKey,
                    'accept' => 'application/json',
                    'content-type' => 'application/json',
                ])
                ->post('https://api.brevo.com/v3/smtp/email', [
                    'sender' => [
                        'name' => $fromName,
                        'email' => $fromAddress,
                    ],
                    'to' => [$toItem],
                    'subject' => $asunto,
                    'htmlContent' => $html,
                ]);

            if ($response->failed()) {
                throw new \RuntimeException('Brevo API error (' . $response->status() . '): ' . $response->body());
            }

            return;
        }

        Mail::to($destinatario)->send($mailable);
    }
}
