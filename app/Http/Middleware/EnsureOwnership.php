<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para verificar ownership de modelos.
 * Asegura que el recurso solicitado pertenezca al usuario autenticado.
 */
class EnsureOwnership
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $modelClass  La clase del modelo a verificar (ej: 'expense', 'category')
     */
    public function handle(Request $request, Closure $next, string $modelClass = ''): Response
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'error' => 'No autorizado',
                'message' => 'Debes iniciar sesión para acceder a este recurso.'
            ], 401);
        }

        // Obtener el modelo de la ruta (route parameter)
        $routeParameters = $request->route()->parameters();

        foreach ($routeParameters as $param) {
            if (is_object($param) && method_exists($param, 'getAttribute')) {
                // Verificar si el modelo tiene user_id y pertenece al usuario actual
                if (property_exists($param, 'user_id') || $param instanceof \Illuminate\Database\Eloquent\Model) {
                    $model = $param;
                    if ($model->user_id !== $user->id) {
                        return response()->json([
                            'error' => 'Prohibido',
                            'message' => 'No tienes permiso para acceder a este recurso.'
                        ], 403);
                    }
                }
            }
        }

        return $next($request);
    }
}