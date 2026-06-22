<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Routing\Attributes\Middleware;

#[Middleware('vetado')]
class VetadoMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->vetado) {
            // Redirige a la ruta 'vetado'
            return redirect()->route('vetado');
        }

        return $next($request);
    }
}