<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para asegurar que el usuario esté autenticado.
 * Si no hay usuario autenticado, retorna 401 en lugar de usar fallback.
 */
class EnsureAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            // Retornar 401 en lugar de permitir acceso con user_id = 1
            return response()->json([
                'error' => 'No autorizado',
                'message' => 'Debes iniciar sesión para acceder a este recurso.',
            ], 401);
        }

        return $next($request);
    }
}
