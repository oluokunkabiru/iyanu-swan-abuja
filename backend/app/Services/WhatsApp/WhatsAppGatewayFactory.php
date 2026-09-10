<?php

namespace App\Services\WhatsApp;

use App\Models\NotificationSetting;
use InvalidArgumentException;

class WhatsAppGatewayFactory
{
    /**
     * @return array<string, class-string<WhatsAppGateway>>
     */
    private static function drivers(): array
    {
        return [
            'termii' => TermiiWhatsAppGateway::class,
        ];
    }

    public static function options(): array
    {
        return array_keys(self::drivers());
    }

    public static function active(): WhatsAppGateway
    {
        return self::make(NotificationSetting::current()->whatsapp_provider ?? 'termii');
    }

    public static function make(string $provider): WhatsAppGateway
    {
        $driver = self::drivers()[$provider] ?? null;

        if ($driver === null) {
            throw new InvalidArgumentException("Unknown WhatsApp provider [{$provider}].");
        }

        return app($driver);
    }
}
