<?php

namespace App\Services\Sms;

use App\Models\NotificationSetting;
use InvalidArgumentException;

class SmsGatewayFactory
{
    /**
     * @return array<string, class-string<SmsGateway>>
     */
    private static function drivers(): array
    {
        return [
            'termii' => TermiiSmsGateway::class,
            'africas_talking' => AfricasTalkingSmsGateway::class,
        ];
    }

    public static function options(): array
    {
        return array_keys(self::drivers());
    }

    public static function active(): SmsGateway
    {
        return self::make(NotificationSetting::current()->sms_provider ?? 'termii');
    }

    public static function make(string $provider): SmsGateway
    {
        $driver = self::drivers()[$provider] ?? null;

        if ($driver === null) {
            throw new InvalidArgumentException("Unknown SMS provider [{$provider}].");
        }

        return app($driver);
    }
}
