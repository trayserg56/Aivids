<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YandexSmartCaptchaValidator
{
    public function isEnabled(): bool
    {
        return filled(config('captcha.yandex.server_key'));
    }

    public function verify(?string $token, ?string $ip = null): bool
    {
        $serverKey = config('captcha.yandex.server_key');

        if (! $serverKey) {
            return true;
        }

        if (blank($token)) {
            return false;
        }

        try {
            $response = Http::asForm()
                ->timeout(3)
                ->post(config('captcha.yandex.validate_url'), [
                    'secret' => $serverKey,
                    'token' => $token,
                    'ip' => $ip ?? '',
                ]);

            if (! $response->successful()) {
                Log::warning('Yandex SmartCaptcha HTTP error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return true;
            }

            return $response->json('status') === 'ok';
        } catch (\Throwable $exception) {
            Log::warning('Yandex SmartCaptcha request failed', [
                'message' => $exception->getMessage(),
            ]);

            return true;
        }
    }
}
