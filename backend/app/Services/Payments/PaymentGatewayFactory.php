<?php

namespace App\Services\Payments;

use App\Models\SiteSetting;
use InvalidArgumentException;

class PaymentGatewayFactory
{
    /**
     * @return array<string, class-string<PaymentGateway>>
     */
    private static function drivers(): array
    {
        return [
            'paystack' => PaystackGateway::class,
            'flutterwave' => FlutterwaveGateway::class,
        ];
    }

    public static function options(): array
    {
        return array_keys(self::drivers());
    }

    public static function active(): PaymentGateway
    {
        return self::make(SiteSetting::current()->active_payment_gateway ?? 'paystack');
    }

    public static function make(string $gateway): PaymentGateway
    {
        $driver = self::drivers()[$gateway] ?? null;

        if ($driver === null) {
            throw new InvalidArgumentException("Unknown payment gateway [{$gateway}].");
        }

        return app($driver);
    }
}
