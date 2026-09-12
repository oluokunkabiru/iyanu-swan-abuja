<?php

namespace App\Notifications\Channels;

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
    public function send(object $notifiable, Notification $notification): void
    {
        // A member (User) keeps their phone on their MemberProfile; a
        // guest event registration keeps it on the registration record
        // itself. Either shape works here without the channel needing to
        // know which one it's dealing with.
        $phone = $notifiable->memberProfile?->phone ?? $notifiable->phone ?? null;

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
