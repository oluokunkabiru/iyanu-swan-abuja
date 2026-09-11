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
        'member_email_default',
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

    /**
     * This table only ever holds one row. Filtering firstOrCreate() by
     * id=1 silently failed to reuse that row whenever it didn't already
     * exist — 'id' isn't mass-assignable, so create() dropped it and let
     * auto-increment pick a different id, spawning a fresh row on every
     * call that found nothing. Keying off "the first row, or make one"
     * instead means there's never more than one to find.
     */
    public static function current(): self
    {
        return static::query()->first() ?? static::create([])->refresh();
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
