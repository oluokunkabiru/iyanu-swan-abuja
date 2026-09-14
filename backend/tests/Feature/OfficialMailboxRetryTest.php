<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\Pages\ListUsers;
use App\Models\NotificationSetting;
use App\Models\User;
use App\Notifications\OfficialMailboxProvisioned;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class OfficialMailboxRetryTest extends TestCase
{
    use UsesMysqlInTransaction;

    public function test_active_member_mailbox_can_be_retried_and_credentials_are_emailed(): void
    {
        Notification::fake();
        NotificationSetting::current()->update(['cpanel_email_provisioning_enabled' => true]);
        config([
            'services.cpanel.host' => 'server.example.com',
            'services.cpanel.port' => 2083,
            'services.cpanel.username' => 'swanabuj',
            'services.cpanel.api_token' => 'test-token',
            'services.cpanel.email_domain' => 'swanabujachapter.org',
            'services.cpanel.quota_mb' => 250,
        ]);
        Http::fake([
            '*/execute/Email/add_pop*' => Http::response(['result' => ['status' => 1, 'errors' => null]]),
        ]);

        $member = User::factory()->create(['name' => 'Jane Doe', 'role' => 'member']);
        $member->memberProfile()->create(['membership_status' => 'active']);

        $mailbox = $member->retryOfficialMailboxProvisioning();
        $member->notifyOfficialMailboxProvisioned($mailbox);

        $this->assertSame('jane.doe@swanabujachapter.org', $member->fresh()->official_email);
        Notification::assertSentTo($member, OfficialMailboxProvisioned::class, function (OfficialMailboxProvisioned $notification) use ($mailbox, $member): bool {
            $mail = $notification->toMail($member);

            return $mail->actionText === 'Open your official mailbox'
                && $mail->actionUrl === 'https://server.example.com:2096'
                && in_array('Temporary password: '.$mailbox->password, $mail->introLines, true)
                && in_array('Android phone: in Gmail or your Email app, choose Add account, then Other and IMAP. Enter your full email address and the temporary password above. If it asks for a server, enter server.example.com and choose port 993 with SSL/TLS.', $mail->introLines, true);
        });
    }

    public function test_authorized_admin_can_retry_an_active_members_official_mailbox_from_the_users_table(): void
    {
        Notification::fake();
        NotificationSetting::current()->update(['cpanel_email_provisioning_enabled' => true]);
        config([
            'services.cpanel.host' => 'server.example.com',
            'services.cpanel.port' => 2083,
            'services.cpanel.username' => 'swanabuj',
            'services.cpanel.api_token' => 'test-token',
            'services.cpanel.email_domain' => 'swanabujachapter.org',
            'services.cpanel.quota_mb' => 250,
        ]);
        Http::fake([
            '*/execute/Email/add_pop*' => Http::response(['result' => ['status' => 1, 'errors' => null]]),
        ]);
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $member = User::factory()->create(['name' => 'Jane Doe', 'role' => 'member']);
        $member->memberProfile()->create(['membership_status' => 'active']);

        Livewire::actingAs($admin)
            ->test(ListUsers::class)
            ->callTableAction('retryOfficialMailbox', $member)
            ->assertHasNoTableActionErrors();

        $this->assertSame('jane.doe@swanabujachapter.org', $member->fresh()->official_email);
        Notification::assertSentTo($member, OfficialMailboxProvisioned::class);
    }
}
