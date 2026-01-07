<?php

namespace App\Rules;

use App\Services\Captcha\TurnstileService;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class Turnstile implements ValidationRule
{
    public function __construct(
        private ?string $ip = null
    ) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $service = app(TurnstileService::class);

        if (! $service->verify($value, $this->ip)) {
            $fail('Verifikasi CAPTCHA gagal. Silakan coba lagi.');
        }
    }
}
