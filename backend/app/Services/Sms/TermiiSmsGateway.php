<?php

namespace App\Services\Sms;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class TermiiSmsGateway implements SmsGateway
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
            'channel' => 'generic',
        ]);

        if (! $response->successful()) {
            throw new RuntimeException('Termii could not send this SMS: '.$response->json('message', 'unknown error'));
        }
    }
}
