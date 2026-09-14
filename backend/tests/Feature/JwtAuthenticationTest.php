<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class JwtAuthenticationTest extends TestCase
{
    use UsesMysqlInTransaction;

    public function test_login_returns_a_bearer_token_that_authenticates_the_member(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $response->assertOk()
            ->assertJsonPath('tokenType', 'Bearer')
            ->assertJsonPath('user.id', (string) $user->id)
            ->assertJsonStructure(['accessToken', 'expiresIn', 'user']);

        $this->withToken($response->json('accessToken'))
            ->getJson('/api/me')
            ->assertOk()
            ->assertJsonPath('id', (string) $user->id);
    }

    public function test_member_api_requires_a_bearer_token(): void
    {
        $this->getJson('/api/me')->assertUnauthorized();
    }
}
