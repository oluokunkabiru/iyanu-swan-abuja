<?php

namespace Tests\Feature;

use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class MemberProfileDetailsTest extends TestCase
{
    use UsesMysqlInTransaction;

    public function test_members_can_save_and_reload_each_profile_section(): void
    {
        $user = User::factory()->create();
        $details = [
            'bio' => ['gender' => 'Female'],
            'contact' => ['city' => 'Wuse', 'state' => 'FCT', 'country' => 'Nigeria', 'officeAddress' => '20 Chapter Close'],
            'academic' => ['qualification' => 'BSc', 'institution' => 'Test University', 'course' => 'Accountancy', 'graduationYear' => '2005'],
            'professional' => ['body' => 'Professional institute', 'membershipNumber' => 'P-123', 'certifications' => 'Audit certification'],
            'faculty' => ['name' => 'Taxation', 'membershipNumber' => 'F-123', 'joinedYear' => '2015'],
            'licence' => ['number' => 'L-123', 'category' => 'Practice', 'issuingBody' => 'Institute', 'issuedOn' => '2025-01-01', 'expiresOn' => '2027-01-01', 'practiceName' => 'Test Practice'],
            'career' => ['jobTitle' => 'Finance manager', 'employmentType' => 'Full-time', 'industry' => 'Finance', 'yearsOfExperience' => '12', 'summary' => 'Member career record.'],
        ];

        $this->actingAsApi($user)->putJson('/api/me', [
            'date_of_birth' => '1985-05-10',
            'sector' => 'Financial services',
            'specialisation' => 'Audit',
            'year_admitted' => '2006',
            'profile_details' => $details,
        ])->assertOk()->assertJsonPath('profileDetails', $details)
            ->assertJsonPath('specialisation', 'Audit');

        $this->assertSame($details, $user->fresh()->memberProfile->profile_details);
        $this->getJson('/api/me')->assertOk()->assertJsonPath('profileDetails', $details);
        $this->assertDatabaseHas('member_profiles', ['user_id' => $user->id, 'year_admitted' => 2006]);
    }

    public function test_updating_one_section_preserves_other_sections_and_omitted_fields(): void
    {
        $user = User::factory()->create();
        $user->memberProfile()->create([
            'profile_details' => [
                'academic' => ['qualification' => 'BSc', 'institution' => 'Original University'],
                'contact' => ['city' => 'Abuja'],
            ],
        ]);

        $this->actingAsApi($user)->putJson('/api/me', [
            'date_of_birth' => '1985-05-10',
            'profile_details' => ['academic' => ['institution' => 'Updated University']],
        ])->assertOk()
            ->assertJsonPath('profileDetails.academic.qualification', 'BSc')
            ->assertJsonPath('profileDetails.academic.institution', 'Updated University')
            ->assertJsonPath('profileDetails.contact.city', 'Abuja');
    }

    public function test_optional_profile_fields_can_be_cleared(): void
    {
        $user = User::factory()->create();
        $user->memberProfile()->create(['profile_details' => ['faculty' => ['name' => 'Taxation']]]);

        $this->actingAsApi($user)->putJson('/api/me', [
            'date_of_birth' => '1985-05-10',
            'profile_details' => ['faculty' => ['name' => null]],
        ])->assertOk()->assertJsonPath('profileDetails.faculty.name', null);
        $this->assertNull($user->fresh()->memberProfile->profile_details['faculty']['name']);
    }

    public function test_members_cannot_insert_privileged_or_unknown_profile_detail_fields(): void
    {
        $user = User::factory()->create();

        $this->actingAsApi($user)->putJson('/api/me', [
            'date_of_birth' => '1985-05-10',
            'profile_details' => ['career' => ['membership_status' => 'active']],
        ])->assertUnprocessable()->assertJsonValidationErrors(['profile_details.career']);

        $this->assertDatabaseMissing('member_profiles', ['user_id' => $user->id]);
    }

    /** @return array<string, array{0: array<string, mixed>, 1: string}> */
    public static function invalidDetails(): array
    {
        return [
            'invalid graduation year' => [['academic' => ['graduationYear' => 1800]], 'profile_details.academic.graduationYear'],
            'invalid career experience' => [['career' => ['yearsOfExperience' => -1]], 'profile_details.career.yearsOfExperience'],
            'invalid employment type' => [['career' => ['employmentType' => 'Unknown']], 'profile_details.career.employmentType'],
            'licence expires before issue' => [['licence' => ['issuedOn' => '2026-01-01', 'expiresOn' => '2025-01-01']], 'profile_details.licence.expiresOn'],
            'oversized career summary' => [['career' => ['summary' => str_repeat('x', 3001)]], 'profile_details.career.summary'],
        ];
    }

    #[DataProvider('invalidDetails')]
    public function test_invalid_profile_details_return_422(array $details, string $field): void
    {
        $user = User::factory()->create();

        $this->actingAsApi($user)->putJson('/api/me', [
            'date_of_birth' => '1985-05-10',
            'profile_details' => $details,
        ])->assertUnprocessable()->assertJsonValidationErrors([$field]);

        $this->assertDatabaseMissing('member_profiles', ['user_id' => $user->id]);
    }

    public function test_profile_updates_only_change_the_authenticated_members_record(): void
    {
        $other = User::factory()->create();
        $other->memberProfile()->create(['profile_details' => ['academic' => ['institution' => 'Other University']]]);
        $user = User::factory()->create();

        $this->actingAsApi($user)->putJson('/api/me', [
            'date_of_birth' => '1985-05-10',
            'user_id' => $other->id,
            'profile_details' => ['academic' => ['institution' => 'My University']],
        ])->assertOk();

        $this->assertSame('Other University', $other->fresh()->memberProfile->profile_details['academic']['institution']);
        $this->assertSame('My University', $user->fresh()->memberProfile->profile_details['academic']['institution']);
    }
}
