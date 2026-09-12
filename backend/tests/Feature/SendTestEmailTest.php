<?php

namespace Tests\Feature;

use App\Filament\Pages\ManageNotificationSettings;
use App\Models\User;
use App\Notifications\TestEmail;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class SendTestEmailTest extends TestCase
{
    use UsesMysqlInTransaction;

    public function test_admin_can_send_a_test_email_to_any_address(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(ManageNotificationSettings::class)
            ->callAction('sendTestEmail', data: ['address' => 'inbox-check@example.com'])
            ->assertHasNoActionErrors();

        Notification::assertSentOnDemand(TestEmail::class);
    }

    public function test_sending_a_test_email_requires_a_valid_address(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['role' => 'admin']);

        Livewire::actingAs($admin)
            ->test(ManageNotificationSettings::class)
            ->callAction('sendTestEmail', data: ['address' => 'not-an-email'])
            ->assertHasActionErrors(['address']);

        Notification::assertNothingSent();
    }
}
