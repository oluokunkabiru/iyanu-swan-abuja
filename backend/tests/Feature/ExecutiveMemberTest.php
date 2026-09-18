<?php

namespace Tests\Feature;

use App\Models\ExecutiveMember;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class ExecutiveMemberTest extends TestCase
{
    use UsesMysqlInTransaction;

    public function test_selecting_a_homepage_chairperson_unselects_every_other_executive(): void
    {
        $existingChairperson = ExecutiveMember::create([
            'name' => 'First Executive',
            'position' => 'Vice Chairperson',
            'is_active' => true,
            'is_chairperson' => true,
        ]);
        $selectedChairperson = ExecutiveMember::create([
            'name' => 'Selected Executive',
            'position' => 'Chairperson',
            'is_active' => true,
            'is_chairperson' => false,
        ]);

        $selectedChairperson->update(['is_chairperson' => true]);

        $this->assertFalse($existingChairperson->fresh()->is_chairperson);
        $this->assertTrue($selectedChairperson->fresh()->is_chairperson);
    }

    public function test_ex_officio_members_are_excluded_from_the_public_executive_list_and_the_chairperson_is_returned_separately(): void
    {
        $principalOfficer = ExecutiveMember::create([
            'name' => 'Principal Officer',
            'position' => 'Secretary',
            'is_active' => true,
            'is_principal' => true,
        ]);
        $exOfficioMember = ExecutiveMember::create([
            'name' => 'Ex Officio Member',
            'position' => 'Immediate Past Chairperson',
            'is_active' => true,
            'is_ex_officio' => true,
        ]);
        $chairperson = ExecutiveMember::create([
            'name' => 'Chairperson',
            'position' => 'Chairperson',
            'is_active' => true,
            'is_ex_officio' => true,
            'is_chairperson' => true,
        ]);

        $this->getJson('/api/executives')
            ->assertOk()
            ->assertJsonFragment(['id' => (string) $principalOfficer->id])
            ->assertJsonMissing(['id' => (string) $chairperson->id])
            ->assertJsonMissing(['id' => (string) $exOfficioMember->id]);

        $this->getJson('/api/chairperson')
            ->assertOk()
            ->assertJsonPath('id', (string) $chairperson->id);

        $this->assertTrue($exOfficioMember->fresh()->is_active);
        $this->assertTrue($principalOfficer->fresh()->is_principal);
    }
}
