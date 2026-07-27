<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthRistikanza
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->bearerToken() !== config('services.ristikanza.token')) {
            return response()->json([
                'message' => 'Unauthorized',
            ], 401);
        }

        return $next($request);
    }
}
