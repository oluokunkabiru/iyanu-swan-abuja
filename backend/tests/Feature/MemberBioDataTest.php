<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class MemberBioDataTest extends TestCase
{
    use UsesMysqlInTransaction;

    public function test_member_can_save_bio_data_and_next_of_kin(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'api')
            ->putJson('/api/me', [
                'date_of_birth' => '1972-03-18',
                'title' => 'Mrs',
                'former_name' => 'Iyama',
                'alternate_phone' => '08033114964',
                'marital_status' => 'Married',
                'nationality' => 'Nigeria',
                'state_of_origin' => 'Delta',
                'lga_of_origin' => 'Aniocha North',
                'next_of_kin_name' => 'Ifeoma Ofili',
                'next_of_kin_relationship' => 'Daughter',
                'next_of_kin_phone' => '08033210836',
                'next_of_kin_address' => 'House 11, Gwarinpa, Abuja',
            ])
            ->assertOk()
            ->assertJsonPath('title', 'Mrs')
            ->assertJsonPath('stateOfOrigin', 'Delta')
            ->assertJsonPath('nextOfKinName', 'Ifeoma Ofili')
            ->assertJsonPath('nextOfKinPhone', '08033210836');

        $this->assertDatabaseHas('member_profiles', [
            'user_id' => $user->id,
            'marital_status' => 'Married',
            'lga_of_origin' => 'Aniocha North',
        ]);
    }

    public function test_bio_data_rejects_values_outside_the_allowed_lists(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user, 'api')
            ->putJson('/api/me', [
                'date_of_birth' => '1972-03-18',
                'title' => 'Sir',
                'state_of_origin' => 'Atlantis',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title', 'state_of_origin']);
    }
}
