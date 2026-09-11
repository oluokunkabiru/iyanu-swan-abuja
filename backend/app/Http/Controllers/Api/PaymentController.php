<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MembershipLevel;
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
     * year from the member portal. The member picks their membership
     * level here; its price becomes this year's amount owed.
     */
    public function paySubscriptionDues(Request $request, int $year): JsonResponse
    {
        $data = $request->validate([
            'membership_level_id' => ['required', 'exists:membership_levels,id'],
        ]);

        $level = MembershipLevel::findOrFail($data['membership_level_id']);

        try {
            $subscription = $this->payments->resolveSubscriptionForLevel($request->user(), $year, $level);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
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

    /**
     * A member's alternative to paying through a gateway: they submit a
     * reference plus evidence of a manual bank transfer, and the
     * subscription sits in "pending_review" — not counted as paid —
     * until an admin approves or rejects it in the admin panel.
     */
    public function submitBankTransfer(Request $request, int $year): JsonResponse
    {
        $data = $request->validate([
            'membership_level_id' => ['required', 'exists:membership_levels,id'],
            'reference' => ['required', 'string', 'max:255'],
            'evidence' => ['required', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
        ]);

        $level = MembershipLevel::findOrFail($data['membership_level_id']);

        try {
            $subscription = $this->payments->resolveSubscriptionForLevel($request->user(), $year, $level);
        } catch (RuntimeException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }

        $subscription->update([
            'status' => 'pending_review',
            'payment_gateway' => 'bank_transfer',
            'bank_transfer_reference' => $data['reference'],
            'review_note' => null,
        ]);

        $subscription->addMediaFromRequest('evidence')->toMediaCollection('payment_evidence');

        return response()->json(['message' => 'Payment evidence submitted. We will review it shortly.']);
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
