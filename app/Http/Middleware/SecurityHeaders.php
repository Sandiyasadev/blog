<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Security headers to apply to all responses.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent clickjacking - page cannot be embedded in iframes
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent MIME type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Enable XSS filter (legacy browsers)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Referrer policy - don't leak full URL to external sites
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions policy - disable unused browser features
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Content Security Policy
        $csp = $this->buildContentSecurityPolicy();
        $response->headers->set('Content-Security-Policy', $csp);

        return $response;
    }

    /**
     * Build Content Security Policy directive.
     */
    private function buildContentSecurityPolicy(): string
    {
        $directives = [
            // Default fallback
            "default-src 'self'",

            // Scripts - allow self and inline (for Alpine.js)
            "script-src 'self' 'unsafe-inline' 'unsafe-eval'",

            // Styles - allow self and inline (for Tailwind)
            "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",

            // Images - allow self, data URIs, and common image sources
            "img-src 'self' data: https: blob:",

            // Fonts - allow self and Google Fonts
            "font-src 'self' https://fonts.gstatic.com https://fonts.googleapis.com",

            // Connect - allow self only
            "connect-src 'self'",

            // Form actions - only allow posting to self
            "form-action 'self'",

            // Base URI - restrict base tag
            "base-uri 'self'",

            // Object/embed - disable
            "object-src 'none'",

            // Upgrade insecure requests in production
            config('app.env') === 'production' ? 'upgrade-insecure-requests' : '',
        ];

        return implode('; ', array_filter($directives));
    }
}
