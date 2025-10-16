<?php

namespace App\Http\Middleware;

use App\Services\JWTService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JWTAuthenticate
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json([
                'success' => false,
                'error' => 'TOKEN_NOT_PROVIDED',
                'message' => 'Token is not provided in header.'
            ], 401);
        }

        $jwtService = new JWTService();
        $result = $jwtService->verifyToken($token);

        if (!$result['valid']) {
            return response()->json([
                'success' => false,
                'error' => $result['error'],
                'message' => $result['message'],
            ], 401);
        }

        return $next($request);
    }
}
