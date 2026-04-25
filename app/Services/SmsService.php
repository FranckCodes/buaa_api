<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class SmsService
{
    public function sendOtp(string $telephone, string $code): void
    {
        $message = "Votre code OTP est {$code}";

        // Fallback log si pas de Sender ID configuré (dev/test)
        if (!config('termii.api_key') || config('termii.api_key') === 'YOUR_API_KEY') {
            Log::info('[OTP FALLBACK] SMS non envoyé — clé API manquante', [
                'telephone' => $telephone,
                'code'      => $code,
            ]);
            return;
        }

        $response = Http::timeout(15)
            ->acceptJson()
            ->when(app()->isLocal(), fn ($http) => $http->withoutVerifying())
            ->post(config('termii.base_url') . '/sms/send', [
                'to'      => $this->normalizePhoneNumber($telephone),
                'from'    => config('termii.sender_id'),
                'sms'     => $message,
                'type'    => 'plain',
                'channel' => config('termii.channel'),
                'api_key' => config('termii.api_key'),
            ]);

        if ($response->failed()) {
            Log::error('Erreur envoi SMS Termii', [
                'telephone' => $telephone,
                'status'    => $response->status(),
                'response'  => $response->json() ?? $response->body(),
            ]);

            // En local, on log le code et on continue sans bloquer
            if (app()->isLocal()) {
                Log::warning('[OTP LOCAL] Termii a échoué — code disponible dans les logs', [
                    'telephone' => $telephone,
                    'code'      => $code,
                ]);
                return;
            }

            throw new RuntimeException('Impossible d\'envoyer le code OTP pour le moment.');
        }

        Log::info('SMS OTP envoyé via Termii', [
            'telephone' => $telephone,
            'response'  => $response->json() ?? $response->body(),
        ]);
    }

    protected function normalizePhoneNumber(string $telephone): string
    {
        // Supprimer les espaces
        $telephone = preg_replace('/\s+/', '', trim($telephone));

        // Si commence par +, on enlève le +
        if (str_starts_with($telephone, '+')) {
            return ltrim($telephone, '+');
        }

        // Si commence par 0, on remplace par 243 (RDC)
        if (str_starts_with($telephone, '0')) {
            return '243' . substr($telephone, 1);
        }

        return $telephone;
    }
}
