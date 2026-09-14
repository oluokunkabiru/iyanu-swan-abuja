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
                && in_array('Android (recommended: IMAP): use your full email address as both the email address and username; use the temporary password above; incoming IMAP server: server.example.com, port 993, SSL/TLS; outgoing SMTP server: server.example.com, port 465, SSL/TLS, with authentication required.', $mail->introLines, true);
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
