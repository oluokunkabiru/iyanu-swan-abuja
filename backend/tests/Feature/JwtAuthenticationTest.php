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

    public function test_member_can_log_in_with_their_official_email_and_swan_password(): void
    {
        $user = User::factory()->create([
            'official_email' => 'member@swanabujachapter.org',
            'password' => 'password123',
        ]);

        $this->postJson('/api/login', [
            'email' => $user->official_email,
            'password' => 'password123',
        ])
            ->assertOk()
            ->assertJsonPath('user.id', (string) $user->id);
    }
}
