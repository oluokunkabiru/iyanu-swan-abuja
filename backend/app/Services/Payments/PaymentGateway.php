<?php

namespace App\Services\Payments;

interface PaymentGateway
{
    /**
     * The key used in config/services.php and as the SiteSetting value
     * that selects this gateway (e.g. "paystack", "flutterwave").
     */
    public function key(): string;

    /**
     * Start a transaction and return the URL the browser should be sent
     * to complete payment.
     */
    public function initialize(string $reference, int $amountNaira, string $email, string $callbackUrl, array $metadata = []): string;

    /**
     * Confirm a transaction's status directly with the gateway. Always
     * re-verify server-side rather than trusting the redirect alone.
     */
    public function verify(string $reference): PaymentVerificationResult;
}
