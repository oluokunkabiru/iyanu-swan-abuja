<?php

namespace App\Services\Payments;

readonly class PaymentVerificationResult
{
    public function __construct(
        public bool $successful,
        public string $reference,
        public int $amount,
        public array $raw = [],
    ) {}
}
