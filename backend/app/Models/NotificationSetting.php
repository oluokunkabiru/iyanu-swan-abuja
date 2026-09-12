<?php

namespace App\Models;

use App\Notifications\Channels\SmsChannel;
use App\Notifications\Channels\WhatsAppChannel;
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

    /**
     * Which Laravel notification channels (channel names/classes) a
     * notification type should actually go out through: the admin's
     * per-type picks (e.g. `birthday_channels`), narrowed to whichever
     * channels are globally enabled — a channel checked for a type but
     * disabled globally never sends.
     *
     * @return array<int, string>
     */
    public function resolveChannels(string $settingKey): array
    {
        $requested = $this->{$settingKey} ?? [];
        $available = array_intersect($requested, $this->enabledChannels());

        return array_values(array_filter(array_map(
            fn (string $channel): ?string => match ($channel) {
                'email' => 'mail',
                'sms' => SmsChannel::class,
                'whatsapp' => WhatsAppChannel::class,
                default => null,
            },
            $available,
        )));
    }
}
