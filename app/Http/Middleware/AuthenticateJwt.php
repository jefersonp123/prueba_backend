<?php

namespace App\Http\Middleware;

use App\Models\User;
use App\Services\JwtService;
use Closure;
use Firebase\JWT\ExpiredException;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuthenticateJwt
{
    public function __construct(private readonly JwtService $jwt) {}

    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();

        if (! $token) {
            return response()->json(['message' => 'Token de autenticación no proporcionado.'], 401);
        }

        try {
            $payload = $this->jwt->decode($token);
            $user = User::find($payload->sub ?? null);
        } catch (ExpiredException) {
            return response()->json(['message' => 'El token de autenticación expiró.'], 401);
        } catch (Throwable) {
            return response()->json(['message' => 'Token de autenticación inválido.'], 401);
        }

        if (! $user) {
            return response()->json(['message' => 'Usuario no autorizado.'], 401);
        }

        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
