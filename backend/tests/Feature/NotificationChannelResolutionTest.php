<?php

namespace Tests\Feature;

use App\Models\NotificationSetting;
use App\Notifications\Channels\SmsChannel;
use App\Notifications\Channels\WhatsAppChannel;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class NotificationChannelResolutionTest extends TestCase
{
    use UsesMysqlInTransaction;

    public function test_resolve_channels_maps_admin_picks_to_channel_names(): void
    {
        $settings = NotificationSetting::current();
        $settings->update([
            'email_enabled' => true,
            'sms_enabled' => true,
            'whatsapp_enabled' => true,
            'birthday_channels' => ['email', 'sms', 'whatsapp'],
        ]);

        $this->assertSame(
            ['mail', SmsChannel::class, WhatsAppChannel::class],
            $settings->resolveChannels('birthday_channels'),
        );
    }

    public function test_resolve_channels_excludes_a_channel_disabled_globally(): void
    {
        $settings = NotificationSetting::current();
        $settings->update([
            'email_enabled' => true,
            'sms_enabled' => false,
            'whatsapp_enabled' => true,
            'birthday_channels' => ['email', 'sms', 'whatsapp'],
        ]);

        $this->assertSame(
            ['mail', WhatsAppChannel::class],
            $settings->resolveChannels('birthday_channels'),
        );
    }

    public function test_resolve_channels_is_empty_when_a_notification_type_has_no_channels_picked(): void
    {
        $settings = NotificationSetting::current();
        $settings->update([
            'email_enabled' => true,
            'sms_enabled' => true,
            'whatsapp_enabled' => true,
            'event_notification_channels' => [],
        ]);

        $this->assertSame([], $settings->resolveChannels('event_notification_channels'));
    }
}
