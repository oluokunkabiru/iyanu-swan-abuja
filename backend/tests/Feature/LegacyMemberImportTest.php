<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Models\MembershipLevel;
use App\Models\User;
use App\Notifications\LegacyMemberImported;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class LegacyMemberImportTest extends TestCase
{
    use UsesMysqlInTransaction;

    public function test_admin_can_import_an_existing_member_with_paid_current_year_dues(): void
    {
        Notification::fake();
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $level = MembershipLevel::factory()->create([
            'subscription_amount' => 5_000,
            'welfare_amount' => 12_000,
        ]);

        Livewire::actingAs($admin)
            ->test(CreateUser::class)
            ->fillForm([
                'name' => 'Legacy Member',
                'email' => 'legacy.member@example.com',
                'role' => 'member',
                'is_legacy_member' => true,
                'legacy_membership_level_id' => $level->id,
                'memberProfile' => [
                    'membership_number' => 'ICAN/LEGACY-001',
                    'credential' => $level->name,
                    'phone' => '08000000000',
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $member = User::query()->where('email', 'legacy.member@example.com')->firstOrFail();

        $this->assertTrue($member->hasVerifiedEmail());
        $this->assertTrue($member->must_change_password);
        $this->assertSame('active', $member->memberProfile->membership_status);
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $member->id,
            'membership_level_id' => $level->id,
            'year' => now()->year,
            'subscription_amount' => 5_000,
            'welfare_amount' => 12_000,
            'status' => 'paid',
            'payment_gateway' => 'legacy_import',
        ]);
        Notification::assertSentTo($member, LegacyMemberImported::class);
    }

    public function test_member_with_a_temporary_password_cannot_access_member_data_until_it_is_changed(): void
    {
        $member = User::factory()->create([
            'password' => Hash::make('temporary-password'),
            'must_change_password' => true,
        ]);

        $this->actingAs($member)
            ->getJson('/api/me/subscriptions')
            ->assertStatus(423)
            ->assertJsonPath('mustChangePassword', true);

        $this->actingAs($member)
            ->putJson('/api/me/password', [
                'current_password' => 'temporary-password',
                'password' => 'new-secure-password',
                'password_confirmation' => 'new-secure-password',
            ])
            ->assertOk()
            ->assertJsonPath('mustChangePassword', false);

        $this->assertFalse($member->fresh()->must_change_password);
        $this->assertTrue(Hash::check('new-secure-password', $member->fresh()->password));
    }
}
