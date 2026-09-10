<?php

namespace App\Services\Sms;

interface SmsGateway
{
    /**
     * The key used as the NotificationSetting value that selects this
     * gateway (e.g. "termii", "africas_talking").
     */
    public function key(): string;

    /**
     * Send a plain-text SMS. Implementations should throw on a hard
     * failure (bad credentials, provider outage) — callers decide
     * whether that's fatal or just logged.
     */
    public function send(string $to, string $message): void;
}
