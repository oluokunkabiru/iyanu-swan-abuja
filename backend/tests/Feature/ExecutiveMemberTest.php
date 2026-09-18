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
            'is_principal' => true,
        ]);
        $selectedChairperson = ExecutiveMember::create([
            'name' => 'Selected Executive',
            'position' => 'Chairperson',
            'is_active' => true,
            'is_principal' => false,
        ]);

        $selectedChairperson->update(['is_principal' => true]);

        $this->assertFalse($existingChairperson->fresh()->is_principal);
        $this->assertTrue($selectedChairperson->fresh()->is_principal);
    }
}
