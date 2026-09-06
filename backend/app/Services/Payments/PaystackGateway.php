<?php

namespace App\Services\Payments;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class PaystackGateway implements PaymentGateway
{
    private const BASE_URL = 'https://api.paystack.co';

    public function key(): string
    {
        return 'paystack';
    }

    public function initialize(string $reference, int $amountNaira, string $email, string $callbackUrl, array $metadata = []): string
    {
        $response = Http::withToken(config('services.paystack.secret_key'))
            ->post(self::BASE_URL.'/transaction/initialize', [
                'email' => $email,
                'amount' => $amountNaira * 100, // Paystack expects kobo.
                'reference' => $reference,
                'callback_url' => $callbackUrl,
                'metadata' => $metadata,
            ]);

        if (! $response->successful() || ! $response->json('status')) {
            throw new RuntimeException('Paystack could not initialize this transaction: '.$response->json('message', 'unknown error'));
        }

        return $response->json('data.authorization_url');
    }

    public function verify(string $reference): PaymentVerificationResult
    {
        $response = Http::withToken(config('services.paystack.secret_key'))
            ->get(self::BASE_URL."/transaction/verify/{$reference}");

        if (! $response->successful() || ! $response->json('status')) {
            throw new RuntimeException('Paystack could not verify this transaction: '.$response->json('message', 'unknown error'));
        }

        $data = $response->json('data');

        return new PaymentVerificationResult(
            successful: ($data['status'] ?? null) === 'success',
            reference: $data['reference'] ?? $reference,
            amount: (int) round(($data['amount'] ?? 0) / 100),
            raw: $data ?? [],
        );
    }
}
