<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Services\JwtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function login(LoginRequest $request, JwtService $jwt): JsonResponse
    {
        $user = User::where('email', $request->validated('email'))->first();

        if (! $user || ! Hash::check($request->validated('password'), $user->password)) {
            return response()->json(['message' => 'Las credenciales proporcionadas son incorrectas.'], 401);
        }

        return response()->json([
            'data' => [
                'access_token' => $jwt->issue($user),
                'token_type' => 'Bearer',
                'expires_in' => config('jwt.ttl'),
            ],
        ]);
    }
}
