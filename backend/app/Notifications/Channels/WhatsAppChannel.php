<?php

namespace App\Notifications\Channels;

use App\Models\User;
use App\Services\WhatsApp\WhatsAppGatewayFactory;
use Illuminate\Notifications\Notification;
use Throwable;

/**
 * A phone-less member or a provider outage should never break the rest of
 * a batch send (e.g. birthday greetings for the whole roll) — failures are
 * logged, not thrown.
 */
class WhatsAppChannel
{
    public function send(User $notifiable, Notification $notification): void
    {
        $phone = $notifiable->memberProfile?->phone;

        if (! $phone) {
            return;
        }

        if (! method_exists($notification, 'toWhatsApp')) {
            return;
        }

        try {
            WhatsAppGatewayFactory::active()->send($phone, $notification->toWhatsApp($notifiable));
        } catch (Throwable $e) {
            report($e);
        }
    }
}
