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
                ->orderByDesc('year')
                ->get()
                ->map(fn (Subscription $subscription): array => [
                    'id' => (string) $subscription->id,
                    'year' => $subscription->year,
                    'subscription' => $subscription->subscription_amount,
                    'welfare' => $subscription->welfare_amount,
                    'status' => ucfirst($subscription->status),
                    'paidOn' => $subscription->paid_at?->toDateString(),
                    'reference' => $subscription->reference,
                ])
        );
    }
}
