<?php

namespace App\Services\Payments;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class FlutterwaveGateway implements PaymentGateway
{
    private const BASE_URL = 'https://api.flutterwave.com/v3';

    public function key(): string
    {
        return 'flutterwave';
    }

    public function initialize(string $reference, int $amountNaira, string $email, string $callbackUrl, array $metadata = []): string
    {
        $response = Http::withToken(config('services.flutterwave.secret_key'))
            ->post(self::BASE_URL.'/payments', [
                'tx_ref' => $reference,
                'amount' => $amountNaira,
                'currency' => 'NGN',
                'redirect_url' => $callbackUrl,
                'customer' => ['email' => $email],
                'meta' => $metadata,
            ]);

        if (! $response->successful() || $response->json('status') !== 'success') {
            throw new RuntimeException('Flutterwave could not initialize this transaction: '.$response->json('message', 'unknown error'));
        }

        return $response->json('data.link');
    }

    public function verify(string $reference): PaymentVerificationResult
    {
        $response = Http::withToken(config('services.flutterwave.secret_key'))
            ->get(self::BASE_URL.'/transactions/verify_by_reference', [
                'tx_ref' => $reference,
            ]);

        if (! $response->successful() || $response->json('status') !== 'success') {
            throw new RuntimeException('Flutterwave could not verify this transaction: '.$response->json('message', 'unknown error'));
        }

        $data = $response->json('data');

        return new PaymentVerificationResult(
            successful: ($data['status'] ?? null) === 'successful',
            reference: $data['tx_ref'] ?? $reference,
            amount: (int) round($data['amount'] ?? 0),
            raw: $data ?? [],
        );
    }
}
