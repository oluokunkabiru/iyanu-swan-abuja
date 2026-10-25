<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MembershipLevel;
use App\Models\Subscription;
use App\Services\Payments\PaymentProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MemberSubscriptionController extends Controller
{
    public function quote(Request $request, int $year, PaymentProcessor $payments): JsonResponse
    {
        $data = $request->validate([
            'membership_level_id' => ['sometimes', 'nullable', 'integer', 'exists:membership_levels,id'],
        ]);
        $user = $request->user();
        $record = $user->subscriptions()->with('membershipLevel')->where('year', $year)->first();
        $levels = MembershipLevel::active()->orderBy('sort_order')->orderBy('id')->get();

        if ($record?->membershipLevel && ! $levels->contains('id', $record->membership_level_id)) {
            $levels->push($record->membershipLevel);
        }

        $locked = in_array($record?->status, ['paid', 'pending_review'], true);
        $selectedId = $locked ? $record?->membership_level_id : ($data['membership_level_id'] ?? $record?->membership_level_id);
        $level = $selectedId
            ? $levels->firstWhere('id', (int) $selectedId)
            : $levels->first(fn (MembershipLevel $candidate): bool => Str::lower(trim($candidate->name)) === Str::lower(trim($user->memberProfile?->credential ?? '')));

        if (isset($data['membership_level_id']) && ! $level && ! $locked) {
            return response()->json(['message' => 'That membership level is not available.'], 422);
        }

        $amounts = $level ? $payments->subscriptionAmounts($user, $level) : null;
        $subscription = $locked ? $record->subscription_amount : $amounts['subscription'] ?? null;
        $welfare = $locked ? $record->welfare_amount : $amounts['welfare'] ?? null;
        $total = $subscription !== null && $welfare !== null ? $subscription + $welfare : null;

        return response()->json([
            'year' => $year,
            'status' => $record?->status ?? 'outstanding',
            'membershipLevelId' => $level ? (string) $level->id : null,
            'membershipLevelName' => $level?->name,
            'subscription' => $subscription,
            'welfare' => $welfare,
            'total' => $total,
            'balanceDue' => $record?->status === 'paid' ? 0 : $total,
            'subscriptionWaived' => ($amounts['subscriptionWaived'] ?? false) && (! $locked || $subscription === 0),
            'reviewNote' => $record?->review_note,
            'options' => $levels->map(fn (MembershipLevel $option): array => [
                'id' => (string) $option->id,
                'name' => $option->name,
                'description' => $option->description,
                ...$payments->subscriptionAmounts($user, $option),
            ])->values(),
        ]);
    }

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
