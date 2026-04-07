<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckSuperAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check() || !Auth::user()->is_super_admin) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'No autorizado.'], 403);
            }

            abort(403, 'Acceso restringido a Super Administradores.');
        }

        return $next($request);
    }
}
