<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MemberSubscriptionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        return response()->json(
            $request->user()
                ->subscriptions()
                ->with('membershipLevel')
                ->orderByDesc('year')
                ->get()
                ->map(fn (Subscription $subscription): array => [
                    'id' => (string) $subscription->id,
                    'year' => $subscription->year,
                    'subscription' => $subscription->subscription_amount,
                    'welfare' => $subscription->welfare_amount,
                    'status' => match ($subscription->status) {
                        'paid' => 'Paid',
                        'pending_review' => 'Pending review',
                        default => 'Outstanding',
                    },
                    'paidOn' => $subscription->paid_at?->toDateString(),
                    'reference' => $subscription->reference,
                    'membershipLevelId' => $subscription->membership_level_id ? (string) $subscription->membership_level_id : null,
                    'membershipLevelName' => $subscription->membershipLevel?->name,
                    'paymentMethod' => $subscription->payment_gateway,
                    'bankTransferReference' => $subscription->bank_transfer_reference,
                    'reviewNote' => $subscription->review_note,
                    'evidenceUrl' => $subscription->evidence_url,
                ])
        );
    }
}
