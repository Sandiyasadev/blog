<?php

namespace App\Services\Captcha;

use Illuminate\Support\Facades\Http;

class TurnstileService
{
    private const VERIFY_URL = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    private string $secretKey;

    public function __construct()
    {
        $this->secretKey = config('services.turnstile.secret_key', '');
    }

    public function verify(?string $token, ?string $ip = null): bool
    {
        if (empty($this->secretKey)) {
            // If no secret key configured, skip validation (for development)
            return config('app.env') === 'local';
        }

        if (empty($token)) {
            return false;
        }

        $response = Http::asForm()->post(self::VERIFY_URL, [
            'secret' => $this->secretKey,
            'response' => $token,
            'remoteip' => $ip,
        ]);

        if (! $response->successful()) {
            return false;
        }

        $data = $response->json();

        return $data['success'] ?? false;
    }

    public static function getSiteKey(): string
    {
        return config('services.turnstile.site_key', '');
    }

    public static function isEnabled(): bool
    {
        // Check if CAPTCHA is explicitly disabled via config
        if (! config('blog.antispam.captcha_enabled', true)) {
            return false;
        }

        // Also check if credentials are configured
        return ! empty(config('services.turnstile.secret_key'));
    }
}
