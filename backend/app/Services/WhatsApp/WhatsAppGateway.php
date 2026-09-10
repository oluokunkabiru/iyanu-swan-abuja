<?php

namespace App\Services\WhatsApp;

interface WhatsAppGateway
{
    /**
     * The key used as the NotificationSetting value that selects this
     * gateway (e.g. "termii").
     */
    public function key(): string;

    /**
     * Send a plain-text WhatsApp message. Implementations should throw on
     * a hard failure — callers decide whether that's fatal or just logged.
     */
    public function send(string $to, string $message): void;
}
