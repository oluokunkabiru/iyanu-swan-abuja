<?php

namespace App\Services\Payments;

use App\Models\EventRegistration;
use App\Models\Subscription;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Bridges the gateway-agnostic PaymentGateway contract to the two kinds
 * of thing this app takes payment for: a Subscription (membership dues,
 * whether at registration or renewal) and an EventRegistration (a ticket
 * purchase). A record always remembers which gateway it was started
 * with, so a later admin switch of the active gateway never orphans a
 * transaction that is already in flight.
 */
class PaymentProcessor
{
    public function initializeForSubscription(Subscription $subscription, string $callbackUrl): string
    {
        $gateway = PaymentGatewayFactory::active();
        $reference = $subscription->reference ?: 'SUB-'.Str::upper(Str::random(10));
        $amount = $subscription->subscription_amount + $subscription->welfare_amount;

        $url = $gateway->initialize(
            $reference,
            $amount,
            $subscription->user->email,
            $callbackUrl,
            ['type' => 'subscription', 'subscription_id' => $subscription->id],
        );

        $subscription->update(['reference' => $reference, 'payment_gateway' => $gateway->key()]);

        return $url;
    }

    public function initializeForEventRegistration(EventRegistration $registration, string $callbackUrl): string
    {
        $gateway = PaymentGatewayFactory::active();

        $url = $gateway->initialize(
            $registration->reference,
            (int) $registration->amount,
            $registration->email,
            $callbackUrl,
            ['type' => 'event_registration', 'registration_id' => $registration->id],
        );

        $registration->update(['payment_gateway' => $gateway->key()]);

        return $url;
    }

    /**
     * Look up whichever record owns this reference, re-verify it against
     * the gateway it was actually started with, and persist the result.
     *
     * @return array{type: string, status: string, record: Subscription|EventRegistration}
     */
    public function finalize(string $reference): array
    {
        if ($subscription = Subscription::where('reference', $reference)->first()) {
            $result = PaymentGatewayFactory::make($subscription->payment_gateway ?? 'paystack')->verify($reference);

            $subscription->update([
                'status' => $result->successful ? 'paid' : 'outstanding',
                'paid_at' => $result->successful ? now() : null,
            ]);

            return ['type' => 'subscription', 'status' => $subscription->status, 'record' => $subscription];
        }

        if ($registration = EventRegistration::where('reference', $reference)->first()) {
            $result = PaymentGatewayFactory::make($registration->payment_gateway ?? 'paystack')->verify($reference);

            $registration->update([
                'payment_status' => $result->successful ? 'paid' : 'failed',
            ]);

            return ['type' => 'event_registration', 'status' => $registration->payment_status, 'record' => $registration];
        }

        throw new RuntimeException("No payment record found for reference [{$reference}].");
    }
}
