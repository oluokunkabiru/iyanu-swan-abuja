<?php

namespace Tests\Feature;

use App\Models\MemberProfile;
use App\Models\MembershipLevel;
use App\Models\User;
use App\Services\IcanMembershipNumber;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class IcanMembershipLookupTest extends TestCase
{
    use UsesMysqlInTransaction;

    public function test_numbers_are_normalised_to_a_prefix_and_six_padded_digits(): void
    {
        $numbers = new IcanMembershipNumber;

        $this->assertSame('MB040071', $numbers->normalize('MB040071'));
        $this->assertSame('MB040071', $numbers->normalize('mb 40071'));
        $this->assertSame('MB040071', $numbers->normalize('40071'));
        $this->assertSame('MB000071', $numbers->normalize('71'));
        $this->assertSame('ICAN017234', $numbers->normalize('ICAN/017234'));
        $this->assertNull($numbers->normalize('MB'));
        $this->assertNull($numbers->normalize('1234567'));
    }

    public function test_lookup_finds_an_existing_member_by_digits_alone_and_masks_their_email(): void
    {
        $this->makeMember('MB999071', 'patofili@example.test');

        $this->postJson('/api/register/lookup', ['membership_number' => '999071'])
            ->assertOk()
            ->assertJsonPath('exists', true)
            ->assertJsonPath('membershipNumber', 'MB999071')
            ->assertJsonPath('lastName', 'Ofili')
            ->assertJsonPath('maskedEmail', 'pa******@example.test')
            ->assertJsonMissingPath('phone');
    }

    public function test_lookup_respects_a_typed_prefix(): void
    {
        $this->makeMember('MB999072', 'one@example.test');

        $this->postJson('/api/register/lookup', ['membership_number' => 'XY999072'])
            ->assertOk()
            ->assertJsonPath('exists', false)
            ->assertJsonPath('membershipNumber', 'XY999072');
    }

    public function test_lookup_reports_an_unknown_number_in_normalised_form(): void
    {
        $this->postJson('/api/register/lookup', ['membership_number' => '9'])
            ->assertOk()
            ->assertJsonPath('membershipNumber', 'MB000009');
    }

    public function test_register_rejects_a_differently_spelled_duplicate_and_stores_the_normalised_number(): void
    {
        $this->makeMember('MB999073', 'first@example.test');
        $level = MembershipLevel::factory()->create();

        $payload = [
            'last_name' => 'Ofili',
            'first_name' => 'Patricia',
            'email' => 'second@example.test',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'credential' => $level->name,
            'phone' => '08000000000',
            'residential_address' => 'Abuja',
            'place_of_work' => 'Firm',
            'date_of_birth' => '1980-01-01',
        ];

        $this->postJson('/api/register', $payload + ['membership_number' => 'mb 999073'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('membership_number');

        $this->postJson('/api/register', $payload + ['membership_number' => '999074'])
            ->assertCreated();

        $this->assertDatabaseHas('member_profiles', ['membership_number' => 'MB999074']);
    }

    private function makeMember(string $number, string $email): void
    {
        $user = User::factory()->create([
            'name' => 'Patricia Ofili', 'first_name' => 'Patricia', 'last_name' => 'Ofili', 'email' => $email,
        ]);
        MemberProfile::query()->create(['user_id' => $user->id, 'membership_number' => $number]);
    }
}
