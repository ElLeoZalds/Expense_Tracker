<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para verificar ownership de modelos usando Laravel Policies.
 * Este middleware actúa como respaldo global de autorización.
 * Nota: Se recomienda usar directamente $this->authorize() en los controladores.
 */
class EnsureOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     * @param  string  $ability  La habilidad a verificar (view, update, delete)
     */
    public function handle(Request $request, Closure $next, string $ability = 'view'): Response
    {
        $user = Auth::user();

        if (! $user) {
            return response()->json([
                'error' => 'No autorizado',
                'message' => 'Debes iniciar sesión para acceder a este recurso.',
            ], 401);
        }

        // Obtener el modelo de la ruta (route parameter)
        $routeParameters = $request->route()->parameters();

        foreach ($routeParameters as $param) {
            if ($param instanceof Model && method_exists($param, 'getAttribute')) {
                // Usar Gate para verificar la autorización mediante Policy
                if (! Gate::forUser($user)->allows($ability, $param)) {
                    return response()->json([
                        'error' => 'Prohibido',
                        'message' => 'No tienes permiso para acceder a este recurso.',
                    ], 403);
                }
            }
        }

        return $next($request);
    }
}
