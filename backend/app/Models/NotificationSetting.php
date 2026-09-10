<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $fillable = [
        'email_enabled',
        'sms_enabled',
        'sms_provider',
        'whatsapp_enabled',
        'whatsapp_provider',
        'birthday_channels',
        'broadcast_channels',
        'newsletter_channels',
        'event_notification_channels',
    ];

    protected function casts(): array
    {
        return [
            'email_enabled' => 'boolean',
            'sms_enabled' => 'boolean',
            'whatsapp_enabled' => 'boolean',
            'birthday_channels' => 'array',
            'broadcast_channels' => 'array',
            'newsletter_channels' => 'array',
            'event_notification_channels' => 'array',
        ];
    }

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }

    /** @return string[] Channels this instance has globally enabled. */
    public function enabledChannels(): array
    {
        return array_keys(array_filter([
            'email' => $this->email_enabled,
            'sms' => $this->sms_enabled,
            'whatsapp' => $this->whatsapp_enabled,
        ]));
    }
}
