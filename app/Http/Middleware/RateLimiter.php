<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para aplicar rate limiting a rutas sensibles.
 * Previene ataques de fuerza bruta y abuso de API.
 */
class RateLimiter
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  int  $maxAttempts  Número máximo de intentos (default: 60)
     * @param  int  $decayMinutes  Minutos para resetear el contador (default: 1)
     */
    public function handle(Request $request, Closure $next, int $maxAttempts = 60, int $decayMinutes = 1): Response
    {
        $key = 'rate-limit:'.$request->ip().':'.$request->path();

        $attempts = cache()->get($key, 0);

        if ($attempts >= $maxAttempts) {
            return response()->json([
                'error' => 'Demasiadas solicitudes',
                'message' => 'Has excedido el límite de solicitudes. Por favor intenta más tarde.',
                'retry_after' => $decayMinutes * 60,
            ], 429);
        }

        cache()->increment($key);
        cache()->put($key, $attempts + 1, now()->addMinutes($decayMinutes));

        return $next($request);
    }
}
