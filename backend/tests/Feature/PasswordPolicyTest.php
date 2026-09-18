<?php

namespace Tests\Feature;

use App\Models\MembershipLevel;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class PasswordPolicyTest extends TestCase
{
    use UsesMysqlInTransaction;

    public function test_default_password_policy_rejects_a_weak_registration_password(): void
    {
        $level = MembershipLevel::factory()->create();

        $this->postJson('/api/register', [
            'name' => 'Jane Member',
            'email' => 'jane.member@example.com',
            'password' => 'password123',
            'membership_number' => 'ICAN/12345',
            'credential' => $level->name,
            'phone' => '08000000000',
            'residential_address' => '12 Chapter Close, Abuja',
            'place_of_work' => 'Federal Ministry of Finance',
        ])->assertUnprocessable()->assertJsonValidationErrors(['password']);
    }

    public function test_password_policy_settings_control_password_reset_validation(): void
    {
        SiteSetting::current()->update([
            'password_min_length' => 8,
            'password_require_mixed_case' => false,
            'password_require_numbers' => false,
            'password_require_symbols' => false,
        ]);
        $user = User::factory()->create(['password' => 'old-password']);
        $token = Password::broker()->createToken($user);

        $this->postJson('/api/reset-password', [
            'token' => $token,
            'email' => $user->email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertOk();

        $this->assertTrue(Hash::check('password', $user->fresh()->password));
    }
}
