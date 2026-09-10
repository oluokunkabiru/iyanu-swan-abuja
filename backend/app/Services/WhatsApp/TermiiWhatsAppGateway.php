<?php

namespace App\Services\WhatsApp;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Termii sends WhatsApp messages through the same messaging endpoint as
 * SMS, distinguished only by channel: "whatsapp" instead of "generic".
 * Free-form messages only deliver within Meta's 24-hour customer-service
 * window (i.e. as a reply to something the member sent first) unless the
 * sender ID has an approved WhatsApp template for outbound-first messages
 * — that's a WhatsApp Business API rule, not something this class can
 * bypass.
 */
class TermiiWhatsAppGateway implements WhatsAppGateway
{
    private const BASE_URL = 'https://api.ng.termii.com/api';

    public function key(): string
    {
        return 'termii';
    }

    public function send(string $to, string $message): void
    {
        $response = Http::post(self::BASE_URL.'/sms/send', [
            'api_key' => config('services.termii.api_key'),
            'to' => $to,
            'from' => config('services.termii.sender_id'),
            'sms' => $message,
            'type' => 'plain',
            'channel' => 'whatsapp',
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('Termii could not send this WhatsApp message: '.$response->json('message', 'unknown error'));
        }
    }
}
