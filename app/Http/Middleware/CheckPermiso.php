<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermiso
{
    /**
     * Uso en rutas: ->middleware('permiso:animales,puede_ver')
     *               ->middleware('permiso:articulos,puede_editar')
     */
    public function handle(Request $request, Closure $next, string $modulo, string $accion = 'puede_ver'): Response
    {
        $user = Auth::user();

        if (!$user) {
            abort(401);
        }

        // Super admin siempre tiene acceso total
        if ($user->is_super_admin) {
            return $next($request);
        }

        // Cargar permisos si no están cargados
        if (!$user->relationLoaded('permisos')) {
            $user->load('permisos');
        }

        if (!$user->tienePermiso($modulo, $accion)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'No tienes permiso para realizar esta acción.'], 403);
            }

            abort(403, 'No tienes permiso para acceder a este recurso.');
        }

        return $next($request);
    }
}
