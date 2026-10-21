<?php

namespace Tests\Feature;

use App\Filament\Resources\Events\Pages\CreateEvent;
use App\Filament\Resources\Events\Pages\EditEvent;
use App\Filament\Resources\Events\Pages\ListEvents;
use App\Filament\Resources\Faqs\Pages\ManageFaqs;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Models\CpdRecord;
use App\Models\Event;
use App\Models\Faq;
use App\Models\User;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class AdminCpdVisibilityTest extends TestCase
{
    use UsesMysqlInTransaction;

    public function test_admin_navigation_excludes_cpd_records_and_training(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get('/admin')
            ->assertOk()
            ->assertDontSee('/admin/cpd-records', false)
            ->assertDontSee('/admin/trainings', false);
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function disabledAdminPages(): array
    {
        return [
            'CPD records' => ['/admin/cpd-records'],
            'CPD training calendar' => ['/admin/trainings'],
        ];
    }

    #[DataProvider('disabledAdminPages')]
    public function test_disabled_admin_pages_return_404_even_for_super_admins(string $path): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)->get($path)->assertNotFound();
    }

    public function test_events_can_be_created_without_a_cpd_hours_field(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CreateEvent::class)
            ->assertFormFieldDoesNotExist('cpd_hours')
            ->fillForm([
                'title' => 'Chapter networking meeting',
                'slug' => 'chapter-networking-meeting-without-cpd',
                'summary' => 'Meet the chapter community.',
                'body' => '<p>A meeting for chapter members.</p>',
                'category' => 'Meeting',
                'starts_at' => '2026-12-01 10:00:00',
                'status' => 'draft',
                'speakers' => [],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('events', [
            'slug' => 'chapter-networking-meeting-without-cpd',
            'cpd_hours' => 0,
        ]);
    }

    public function test_event_edits_preserve_existing_cpd_hours_without_showing_them(): void
    {
        $admin = User::factory()->admin()->create();
        $event = Event::create([
            'title' => 'Chapter seminar',
            'slug' => 'chapter-seminar-with-hidden-hours',
            'summary' => 'Original summary.',
            'body' => '<p>Seminar details.</p>',
            'category' => 'Seminar',
            'starts_at' => '2026-12-01 10:00:00',
            'status' => 'draft',
            'cpd_hours' => 7,
        ]);

        Livewire::actingAs($admin)
            ->test(EditEvent::class, ['record' => $event->getRouteKey()])
            ->assertFormFieldDoesNotExist('cpd_hours')
            ->fillForm(['summary' => 'Updated summary.'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'summary' => 'Updated summary.',
            'cpd_hours' => 7,
        ]);
        Livewire::actingAs($admin)
            ->test(ListEvents::class)
            ->assertTableColumnDoesNotExist('cpd_hours');
    }

    public function test_member_profiles_hide_cpd_history_and_preserve_existing_targets(): void
    {
        $admin = User::factory()->admin()->create();
        $member = User::factory()->create();
        $member->memberProfile()->create([
            'membership_status' => 'active',
            'date_of_birth' => '1985-05-10',
            'cpd_target' => 240,
            'next_of_kin_name' => 'Family contact',
        ]);
        $record = CpdRecord::factory()->for($member)->create([
            'activity' => 'Hidden learning activity',
        ]);

        Livewire::actingAs($admin)
            ->test(ViewUser::class, ['record' => $member->getRouteKey()])
            ->assertDontSee('CPD records')
            ->assertDontSee('CPD target')
            ->assertDontSee('Hidden learning activity');

        Livewire::actingAs($admin)
            ->test(EditUser::class, ['record' => $member->getRouteKey()])
            ->assertFormFieldDoesNotExist('memberProfile.cpd_target')
            ->assertFormFieldExists('memberProfile.next_of_kin_name')
            ->fillForm(['name' => 'Updated member name'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('Updated member name', $member->fresh()->name);
        $this->assertSame(240, $member->fresh()->memberProfile->cpd_target);
        $this->assertSame('Family contact', $member->fresh()->memberProfile->next_of_kin_name);
        $this->assertModelExists($record);
    }

    public function test_cpd_faqs_are_hidden_without_deleting_their_records(): void
    {
        $admin = User::factory()->admin()->create();
        $general = Faq::factory()->create([
            'topic' => 'General',
            'question' => 'Admin visibility general question?',
        ]);
        $cpd = Faq::factory()->create([
            'topic' => 'CPD',
            'question' => 'Admin visibility learning question?',
        ]);

        Livewire::actingAs($admin)
            ->test(ManageFaqs::class)
            ->searchTable('Admin visibility')
            ->assertCanSeeTableRecords([$general])
            ->assertCanNotSeeTableRecords([$cpd]);

        $this->assertModelExists($cpd);
    }

    public function test_admin_cannot_create_a_faq_with_the_disabled_cpd_topic(): void
    {
        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(ManageFaqs::class)
            ->callAction('create', data: [
                'topic' => 'CPD',
                'question' => 'Disabled learning topic question?',
                'answer' => 'This topic is disabled.',
            ])
            ->assertHasActionErrors(['topic' => 'in']);

        $this->assertDatabaseMissing('faqs', [
            'question' => 'Disabled learning topic question?',
        ]);
    }
}
