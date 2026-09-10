<?php

namespace App\Notifications\Channels;

use App\Models\User;
use App\Services\Sms\SmsGatewayFactory;
use Illuminate\Notifications\Notification;
use Throwable;

/**
 * A phone-less member or a provider outage should never break the rest of
 * a batch send (e.g. birthday greetings for the whole roll) — failures are
 * logged, not thrown.
 */
class SmsChannel
{
    public function send(User $notifiable, Notification $notification): void
    {
        $phone = $notifiable->memberProfile?->phone;

        if (! $phone) {
            return;
        }

        if (! method_exists($notification, 'toSms')) {
            return;
        }

        try {
            SmsGatewayFactory::active()->send($phone, $notification->toSms($notifiable));
        } catch (Throwable $e) {
            report($e);
        }
    }
}
