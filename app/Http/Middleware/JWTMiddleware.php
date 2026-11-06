<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Str;


class JWTMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     */
    public function handle(Request $request, Closure $next)
    {
        try {
            $auth = $request->bearerToken() ?? null;
            if ($auth == null) {
                return response()->json(['token_invalid_format'], 401);
            }
            $token = str_replace('Bearer ', '', $auth);
            $key = env('JWT_SECRET');
            JWT::decode($token, new Key($key, 'HS256'));
        } catch (\Exception $e) {

            return response()->json([$e->getMessage()], 401);

        }
        return $next($request);
    }
}
