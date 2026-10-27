<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_obtain_a_jwt_with_valid_credentials(): void
    {
        User::factory()->create(['email' => 'admin@example.com', 'password' => 'password']);

        $this->postJson('/api/auth/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ])->assertOk()
            ->assertJsonPath('data.token_type', 'Bearer')
            ->assertJsonStructure(['data' => ['access_token', 'token_type', 'expires_in']]);
    }

    public function test_invalid_credentials_are_rejected(): void
    {
        $this->postJson('/api/auth/login', [
            'email' => 'unknown@example.com',
            'password' => 'wrong-password',
        ])->assertUnauthorized();
    }
}
