<?php

namespace App\Http\Requests;

use App\Rules\Turnstile;
use App\Services\Captcha\TurnstileService;
use Illuminate\Foundation\Http\FormRequest;

class ContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'email:rfc,dns', 'max:255'],
            'subject' => ['required', 'string', 'min:3', 'max:200'],
            'message' => ['required', 'string', 'min:10', 'max:5000'],
            'website' => ['nullable', 'string', 'max:0'], // Honeypot
        ];

        if (TurnstileService::isEnabled()) {
            $rules['cf-turnstile-response'] = ['required', new Turnstile($this->ip())];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Nama wajib diisi.',
            'name.min' => 'Nama minimal 2 karakter.',
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'subject.required' => 'Subjek wajib diisi.',
            'subject.min' => 'Subjek minimal 3 karakter.',
            'message.required' => 'Pesan wajib diisi.',
            'message.min' => 'Pesan minimal 10 karakter.',
            'cf-turnstile-response.required' => 'Silakan selesaikan verifikasi CAPTCHA.',
        ];
    }
}
