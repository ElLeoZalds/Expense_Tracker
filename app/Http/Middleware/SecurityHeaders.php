<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para agregar cabeceras de seguridad HTTP.
 * Protege contra ataques comunes como clickjacking, XSS, MIME sniffing, etc.
 */
class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Previene clickjacking - no permite que la página sea embebida en iframes
        $response->headers->set('X-Frame-Options', 'DENY');

        // Previene MIME type sniffing - fuerza al navegador a respetar el Content-Type declarado
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Protección XSS básica para navegadores antiguos (IE, Chrome antiguo)
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Controla la información de referer enviada con las solicitudes
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Content Security Policy - restringe fuentes de contenido ejecutable
        $response->headers->set(
            'Content-Security-Policy',
            "default-src 'self'; script-src 'self' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.bunny.net https://fonts.googleapis.com; font-src 'self' https://fonts.bunny.net https://fonts.gstatic.com; img-src 'self' data: https:; connect-src 'self'; frame-ancestors 'none';"
        );

        // Permisos de características - limita qué APIs puede usar el navegador
        $response->headers->set(
            'Permissions-Policy',
            'geolocation=(), microphone=(), camera=(), payment=(), usb=()'
        );

        return $response;
    }
}