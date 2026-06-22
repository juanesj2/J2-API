<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class HubAuthMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verificar si está autenticado
        if (!Auth::check()) {
            return redirect('/hub/login')->with('error', 'Debes iniciar sesión para acceder al Hub.');
        }

        // Verificar si tiene rol de super administrador
        if (Auth::user()->rol !== 'SuperAdmin') {
            Auth::logout();
            return redirect('/hub/login')->with('error', 'No tienes permisos de SuperAdmin.');
        }

        return $next($request);
    }
}
