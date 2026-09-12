<?php

namespace Tests\Feature;

use App\Filament\Pages\SendBroadcast;
use App\Models\User;
use App\Notifications\AdminBroadcast;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class AdminBroadcastTest extends TestCase
{
    use UsesMysqlInTransaction;

    public function test_admin_can_send_a_broadcast_to_all_members(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $activeMember = User::factory()->create(['role' => 'member']);
        $activeMember->memberProfile()->create(['membership_status' => 'active']);
        $pendingMember = User::factory()->create(['role' => 'member']);
        $pendingMember->memberProfile()->create(['membership_status' => 'pending']);

        Livewire::actingAs($admin)
            ->test(SendBroadcast::class)
            ->fillForm(['audience' => 'all', 'subject' => 'Chapter update', 'body' => 'Something is happening.'])
            ->call('send');

        Notification::assertSentTo($activeMember, AdminBroadcast::class);
        Notification::assertSentTo($pendingMember, AdminBroadcast::class);
    }

    public function test_admin_can_scope_a_broadcast_to_active_members_only(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $activeMember = User::factory()->create(['role' => 'member']);
        $activeMember->memberProfile()->create(['membership_status' => 'active']);
        $pendingMember = User::factory()->create(['role' => 'member']);
        $pendingMember->memberProfile()->create(['membership_status' => 'pending']);

        Livewire::actingAs($admin)
            ->test(SendBroadcast::class)
            ->fillForm(['audience' => 'active', 'subject' => 'For active members', 'body' => 'Renewal reminder.'])
            ->call('send');

        Notification::assertSentTo($activeMember, AdminBroadcast::class);
        Notification::assertNotSentTo($pendingMember, AdminBroadcast::class);
    }

    public function test_sending_a_broadcast_to_an_empty_audience_sends_nothing(): void
    {
        Notification::fake();

        // This runs against the shared dev database inside a
        // transaction that's rolled back afterward — neutralize any
        // real members already on the roll so "no matching audience"
        // is genuinely empty for this assertion.
        User::where('role', 'member')->update(['role' => 'former-member']);

        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(SendBroadcast::class)
            ->fillForm(['audience' => 'active', 'subject' => 'No one home', 'body' => 'Test.'])
            ->call('send');

        Notification::assertNothingSent();
    }
}
