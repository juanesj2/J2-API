<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AtlasAuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        $secret = env('ATLAS_BRIDGE_SECRET');

        if (!$secret || $token !== $secret) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Invalid or missing ATLAS_BRIDGE_SECRET.'
            ], 401);
        }

        return $next($request);
    }
}
