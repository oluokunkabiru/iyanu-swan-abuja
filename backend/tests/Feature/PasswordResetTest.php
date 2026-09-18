<?php

namespace Tests\Feature;

use App\Models\User;
use App\Notifications\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use UsesMysqlInTransaction;

    public function test_requesting_a_password_reset_sends_a_frontend_reset_link(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'member@example.com']);

        $this->postJson('/api/forgot-password', ['email' => $user->email])
            ->assertOk()
            ->assertJsonPath('message', 'If an account matches that email address, we have sent a password-reset link.');

        Notification::assertSentTo($user, PasswordReset::class, function (PasswordReset $notification) use ($user): bool {
            $url = $notification->toMail($user)->actionUrl;

            return str_starts_with($url, rtrim(config('app.frontend_url'), '/').'/reset-password?')
                && str_contains($url, 'token=')
                && str_contains($url, 'email=member%40example.com');
        });
    }

    public function test_requesting_a_password_reset_for_an_unknown_email_does_not_disclose_that_it_is_unknown(): void
    {
        Notification::fake();

        $this->postJson('/api/forgot-password', ['email' => 'unknown@example.com'])
            ->assertOk()
            ->assertJsonPath('message', 'If an account matches that email address, we have sent a password-reset link.');

        Notification::assertNothingSent();
    }

    public function test_a_valid_password_reset_token_changes_the_password(): void
    {
        $user = User::factory()->create([
            'password' => 'old-password',
            'must_change_password' => true,
        ]);
        $token = Password::broker()->createToken($user);

        $this->postJson('/api/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])->assertOk()->assertJsonPath('message', 'Your password has been reset. You can now sign in.');

        $user->refresh();

        $this->assertTrue(Hash::check('new-secure-password', $user->password));
        $this->assertFalse($user->must_change_password);
        $this->assertFalse(Password::broker()->tokenExists($user, $token));
    }

    public function test_an_invalid_password_reset_token_is_rejected(): void
    {
        $user = User::factory()->create(['password' => 'old-password']);

        $this->postJson('/api/reset-password', [
            'token' => 'invalid-token',
            'email' => $user->email,
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])->assertStatus(422);

        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
    }
}
