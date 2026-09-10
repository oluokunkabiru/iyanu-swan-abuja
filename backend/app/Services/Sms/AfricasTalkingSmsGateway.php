<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Uses Africa's Talking's classic bulk-messaging endpoint directly rather
 * than their SDK, to stay consistent with how the payment gateways in this
 * app call out over HTTP. Sandbox apps send from username "sandbox" against
 * api.sandbox.africastalking.com instead of the production host below.
 */
class AfricasTalkingSmsGateway implements SmsGateway
{
    private const BASE_URL = 'https://api.africastalking.com/version1/messaging';

    public function key(): string
    {
        return 'africas_talking';
    }

    public function send(string $to, string $message): void
    {
        $response = Http::asForm()
            ->withHeaders([
                'apiKey' => config('services.africas_talking.api_key'),
                'Accept' => 'application/json',
            ])
            ->post(self::BASE_URL, [
                'username' => config('services.africas_talking.username'),
                'to' => $to,
                'message' => $message,
                'from' => config('services.africas_talking.sender_id'),
            ]);

        if (! $response->successful()) {
            throw new RuntimeException("Africa's Talking could not send this SMS: ".$response->body());
        }
    }
}
