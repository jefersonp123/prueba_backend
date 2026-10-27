<?php

namespace App\Services;

use App\Models\User;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use RuntimeException;
use stdClass;

class JwtService
{
    public function issue(User $user): string
    {
        $now = time();

        return JWT::encode([
            'iss' => config('app.url'),
            'sub' => (string) $user->getKey(),
            'iat' => $now,
            'exp' => $now + config('jwt.ttl'),
        ], $this->secret(), config('jwt.algorithm'));
    }

    public function decode(string $token): stdClass
    {
        return JWT::decode($token, new Key($this->secret(), config('jwt.algorithm')));
    }

    private function secret(): string
    {
        $secret = config('jwt.secret');

        if (! is_string($secret) || strlen($secret) < 32) {
            throw new RuntimeException('JWT_SECRET must contain at least 32 characters.');
        }

        return $secret;
    }
}
