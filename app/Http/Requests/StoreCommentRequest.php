<?php

namespace App\Http\Requests;

use App\Rules\Turnstile;
use App\Services\Captcha\TurnstileService;
use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'author_name' => [
                'required',
                'string',
                'min:2',
                'max:100',
                'regex:/^[\p{L}\p{M}\s\'\-\.]+$/u', // Only letters, spaces, apostrophe, hyphen, dot
            ],
            'author_email' => [
                'nullable',
                'email:rfc,dns', // Stricter email validation
                'max:255',
            ],
            'author_url' => [
                'nullable',
                'url:http,https', // Only http/https URLs
                'max:255',
                'regex:/^https?:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,}/', // Basic domain validation
            ],
            'body_raw' => [
                'required',
                'string',
                'min:3',
                'max:5000',
            ],
            'parent_id' => [
                'nullable',
                'integer',
                'exists:comments,id',
            ],
            'website' => [
                'nullable',
                'string',
                'max:0', // Honeypot - must be empty
            ],
        ];

        // Add Turnstile validation if enabled
        if (TurnstileService::isEnabled()) {
            $rules['cf-turnstile-response'] = ['required', new Turnstile($this->ip())];
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'author_name.required' => 'Nama wajib diisi.',
            'author_name.min' => 'Nama minimal 2 karakter.',
            'author_name.max' => 'Nama maksimal 100 karakter.',
            'author_name.regex' => 'Nama hanya boleh berisi huruf, spasi, apostrof, dan tanda hubung.',
            'author_email.email' => 'Format email tidak valid.',
            'author_url.url' => 'Format URL tidak valid.',
            'author_url.regex' => 'URL harus berupa alamat website yang valid.',
            'body_raw.required' => 'Komentar wajib diisi.',
            'body_raw.min' => 'Komentar minimal 3 karakter.',
            'body_raw.max' => 'Komentar maksimal 5000 karakter.',
            'parent_id.exists' => 'Komentar yang ingin dibalas tidak ditemukan.',
            'cf-turnstile-response.required' => 'Silakan selesaikan verifikasi CAPTCHA.',
        ];
    }

    protected function prepareForValidation(): void
    {
        // Trim whitespace from inputs
        $this->merge([
            'author_name' => trim($this->author_name ?? ''),
            'author_email' => trim($this->author_email ?? ''),
            'author_url' => trim($this->author_url ?? ''),
            'body_raw' => trim($this->body_raw ?? ''),
        ]);
    }
}
