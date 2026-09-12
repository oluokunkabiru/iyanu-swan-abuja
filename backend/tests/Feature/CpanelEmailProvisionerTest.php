<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\Email\CpanelEmailProvisioner;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class CpanelEmailProvisionerTest extends TestCase
{
    private function configureCpanel(): void
    {
        config([
            'services.cpanel.host' => 'server.example.com',
            'services.cpanel.port' => 2083,
            'services.cpanel.username' => 'swanabuj',
            'services.cpanel.api_token' => 'test-token',
            'services.cpanel.email_domain' => 'swanabujachapter.org',
            'services.cpanel.quota_mb' => 250,
        ]);
    }

    public function test_is_not_configured_when_required_settings_are_missing(): void
    {
        config([
            'services.cpanel.host' => null,
            'services.cpanel.username' => null,
            'services.cpanel.api_token' => null,
            'services.cpanel.email_domain' => null,
        ]);

        $this->assertFalse(app(CpanelEmailProvisioner::class)->isConfigured());
    }

    public function test_is_configured_when_all_required_settings_are_present(): void
    {
        $this->configureCpanel();

        $this->assertTrue(app(CpanelEmailProvisioner::class)->isConfigured());
    }

    public function test_provisions_a_mailbox_using_first_dot_last_name(): void
    {
        $this->configureCpanel();

        Http::fake([
            '*/execute/Email/add_pop*' => Http::response(['result' => ['status' => 1, 'errors' => null]]),
        ]);

        $user = User::factory()->make(['name' => 'Jane Doe']);

        $mailbox = app(CpanelEmailProvisioner::class)->provisionFor($user);

        $this->assertSame('jane.doe@swanabujachapter.org', $mailbox->address);
        $this->assertNotEmpty($mailbox->password);

        Http::assertSent(function ($request) {
            return $request['email'] === 'jane.doe' && $request['domain'] === 'swanabujachapter.org';
        });
    }

    public function test_a_single_word_name_produces_a_bare_local_part(): void
    {
        $this->configureCpanel();

        Http::fake([
            '*/execute/Email/add_pop*' => Http::response(['result' => ['status' => 1, 'errors' => null]]),
        ]);

        $user = User::factory()->make(['name' => 'Madonna']);

        $mailbox = app(CpanelEmailProvisioner::class)->provisionFor($user);

        $this->assertSame('madonna@swanabujachapter.org', $mailbox->address);
    }

    public function test_retries_with_a_numeric_suffix_on_a_naming_collision(): void
    {
        $this->configureCpanel();

        Http::fake([
            '*/execute/Email/add_pop*' => Http::sequence()
                ->push(['result' => ['status' => 0, 'errors' => ['Email account "jane.doe" already exists.']]])
                ->push(['result' => ['status' => 1, 'errors' => null]]),
        ]);

        $user = User::factory()->make(['name' => 'Jane Doe']);

        $mailbox = app(CpanelEmailProvisioner::class)->provisionFor($user);

        $this->assertSame('jane.doe1@swanabujachapter.org', $mailbox->address);
    }

    public function test_throws_when_cpanel_rejects_for_a_reason_other_than_a_collision(): void
    {
        $this->configureCpanel();

        Http::fake([
            '*/execute/Email/add_pop*' => Http::response(['result' => ['status' => 0, 'errors' => ['Quota exceeded for this account.']]]),
        ]);

        $user = User::factory()->make(['name' => 'Jane Doe']);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Quota exceeded');

        app(CpanelEmailProvisioner::class)->provisionFor($user);
    }
}
