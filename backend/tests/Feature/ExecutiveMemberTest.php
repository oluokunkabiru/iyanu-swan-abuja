<?php

namespace Tests\Feature;

use App\Filament\Resources\PastChairpersons\Pages\ManagePastChairpersons;
use App\Models\ExecutiveMember;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\Testing\TestAction;
use Filament\Facades\Filament;
use Livewire\Livewire;
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

    public function test_public_executive_list_uses_the_admin_display_order(): void
    {
        $firstExecutive = ExecutiveMember::create([
            'name' => 'First Executive',
            'position' => 'Treasurer',
            'is_active' => true,
            'sort_order' => 10,
        ]);
        $secondExecutive = ExecutiveMember::create([
            'name' => 'Second Executive',
            'position' => 'Secretary',
            'is_active' => true,
            'sort_order' => 20,
        ]);

        $response = $this->getJson('/api/executives')->assertOk();
        $executiveIds = collect($response->json())->pluck('id')->all();

        $this->assertLessThan(
            array_search((string) $secondExecutive->id, $executiveIds, true),
            array_search((string) $firstExecutive->id, $executiveIds, true),
        );
    }

    public function test_past_chairpersons_come_from_their_own_flag_and_stay_off_the_council_list(): void
    {
        ExecutiveMember::create([
            'name' => 'Former Chair',
            'position' => 'Chairperson',
            'is_active' => true,
            'is_past_chairperson' => true,
            'term_start_year' => 2018,
            'term_end_year' => 2020,
        ]);
        ExecutiveMember::create([
            'name' => 'Inactive Treasurer',
            'position' => 'Treasurer',
            'is_active' => false,
        ]);

        $this->getJson('/api/past-chairpersons')
            ->assertOk()
            ->assertJsonFragment(['name' => 'Former Chair', 'termStartYear' => 2018])
            ->assertJsonMissing(['name' => 'Inactive Treasurer']);

        $this->getJson('/api/executives')
            ->assertOk()
            ->assertJsonMissing(['name' => 'Former Chair']);
    }

    public function test_admin_can_add_a_past_chairperson_from_its_own_section(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::actingAs(User::factory()->admin()->create())
            ->test(ManagePastChairpersons::class)
            ->callAction(CreateAction::class, [
                'name' => 'Added Past Chair',
                'credential' => 'FCA',
                'term_start_year' => 2014,
                'term_end_year' => 2016,
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('executive_members', [
            'name' => 'Added Past Chair',
            'position' => 'Chairperson',
            'is_past_chairperson' => true,
            'is_active' => false,
        ]);
    }

    public function test_past_chairpersons_are_returned_in_historical_order_with_their_tenure_positions(): void
    {
        $second = ExecutiveMember::create([
            'name' => 'Archive Second Chair',
            'position' => '2nd Chairperson',
            'is_past_chairperson' => true,
            'term_start_year' => 2011,
            'term_end_year' => 2013,
        ]);
        $founder = ExecutiveMember::create([
            'name' => 'Archive Founding Member',
            'position' => 'Founding Mother',
            'is_past_chairperson' => true,
            'term_start_year' => 1999,
            'term_end_year' => 2002,
        ]);
        $first = ExecutiveMember::create([
            'name' => 'Archive First Chair',
            'position' => '1st Chairperson',
            'is_past_chairperson' => true,
            'term_start_year' => 2008,
            'term_end_year' => 2010,
        ]);

        $response = $this->getJson('/api/past-chairpersons')->assertOk();
        $archive = collect($response->json())
            ->whereIn('id', [(string) $second->id, (string) $founder->id, (string) $first->id])
            ->values();

        $this->assertSame(
            ['Archive Founding Member', 'Archive First Chair', 'Archive Second Chair'],
            $archive->pluck('name')->all(),
        );
        $this->assertSame(
            ['Founding Mother', '1st Chairperson', '2nd Chairperson'],
            $archive->pluck('position')->all(),
        );
    }

    public function test_admin_can_save_and_edit_a_past_chairpersons_tenure_position(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ManagePastChairpersons::class)
            ->callAction(CreateAction::class, [
                'name' => 'Archive Coordinator',
                'position' => 'Co-ordinator',
                'term_start_year' => 2005,
                'term_end_year' => 2007,
            ])
            ->assertHasNoActionErrors();

        $coordinator = ExecutiveMember::query()->where('name', 'Archive Coordinator')->firstOrFail();
        $this->assertSame('Co-ordinator', $coordinator->position);

        Livewire::actingAs($admin)
            ->test(ManagePastChairpersons::class)
            ->callAction(TestAction::make(EditAction::class)->table($coordinator), data: [
                'position' => 'Founding Mother',
            ])
            ->assertHasNoActionErrors();

        $this->assertDatabaseHas('executive_members', [
            'id' => $coordinator->id,
            'position' => 'Founding Mother',
            'is_past_chairperson' => true,
        ]);
        $this->getJson('/api/past-chairpersons')
            ->assertOk()
            ->assertJsonFragment([
                'id' => (string) $coordinator->id,
                'position' => 'Founding Mother',
                'termStartYear' => 2005,
                'termEndYear' => 2007,
            ]);
    }
}
