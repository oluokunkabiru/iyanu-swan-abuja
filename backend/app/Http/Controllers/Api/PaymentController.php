<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Services\Payments\PaymentProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class PaymentController extends Controller
{
    public function __construct(private readonly PaymentProcessor $payments) {}

    /**
     * Start (or resume) payment for a member's dues in a given year —
     * used both right after registration and for renewing a past-due
     * year from the member portal.
     */
    public function paySubscriptionDues(Request $request, int $year): JsonResponse
    {
        $settings = SiteSetting::current();

        $subscription = $request->user()->subscriptions()->firstOrCreate(
            ['year' => $year],
            [
                'subscription_amount' => $settings->membership_subscription_fee ?? 0,
                'welfare_amount' => $settings->membership_welfare_fee ?? 0,
                'status' => 'outstanding',
            ]
        );

        if ($subscription->status === 'paid') {
            return response()->json(['message' => "Dues for {$year} are already paid."], 422);
        }

        // Deliberately no query string of our own here: both gateways
        // append their own tracking params (Paystack: trxref/reference,
        // Flutterwave: tx_ref/transaction_id) to whatever we give them,
        // so adding our own "reference" would just collide with theirs.
        $callbackUrl = rtrim(config('app.frontend_url'), '/').'/payments/callback';

        try {
            $url = $this->payments->initializeForSubscription($subscription, $callbackUrl);
        } catch (RuntimeException $e) {
            Log::error('Payment initialization failed', ['error' => $e->getMessage(), 'subscription_id' => $subscription->id]);

            return response()->json(['message' => 'We could not start this payment. Please try again shortly.'], 502);
        }

        return response()->json(['authorizationUrl' => $url]);
    }

    public function verify(string $reference): JsonResponse
    {
        try {
            $result = $this->payments->finalize($reference);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 404);
        }

        return response()->json([
            'type' => $result['type'],
            'status' => $result['status'],
        ]);
    }

    public function webhookPaystack(Request $request): JsonResponse
    {
        $signature = $request->header('x-paystack-signature');
        $expected = hash_hmac('sha512', $request->getContent(), config('services.paystack.secret_key'));

        if (! $signature || ! hash_equals($expected, $signature)) {
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $reference = $request->input('data.reference');

        if ($reference) {
            try {
                $this->payments->finalize($reference);
            } catch (RuntimeException $e) {
                Log::warning('Paystack webhook could not finalize payment', ['error' => $e->getMessage(), 'reference' => $reference]);
            }
        }

        return response()->json(['received' => true]);
    }

    public function webhookFlutterwave(Request $request): JsonResponse
    {
        $signature = $request->header('verif-hash');
        $expected = config('services.flutterwave.secret_hash');

        if (! $expected || ! $signature || ! hash_equals($expected, $signature)) {
            return response()->json(['message' => 'Invalid signature'], 401);
        }

        $reference = $request->input('data.tx_ref');

        if ($reference) {
            try {
                $this->payments->finalize($reference);
            } catch (RuntimeException $e) {
                Log::warning('Flutterwave webhook could not finalize payment', ['error' => $e->getMessage(), 'reference' => $reference]);
            }
        }

        return response()->json(['received' => true]);
    }
}
