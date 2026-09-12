<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Notifications\EventRegistrationConfirmed;
use App\Services\Payments\PaymentProcessor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class EventRegistrationController extends Controller
{
    public function __construct(private readonly PaymentProcessor $payments) {}

    public function store(Request $request, Event $event): JsonResponse
    {
        $data = $request->validate([
            'event_ticket_type_id' => ['required', 'exists:event_ticket_types,id'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'notes' => ['nullable', 'string'],
        ]);

        $ticketType = $event->ticketTypes()->findOrFail($data['event_ticket_type_id']);
        $isFree = $ticketType->price <= 0;

        $registration = $event->registrations()->create([
            'event_ticket_type_id' => $ticketType->id,
            'user_id' => $request->user()?->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'notes' => $data['notes'] ?? null,
            'amount' => $ticketType->price,
            'payment_status' => $isFree ? 'paid' : 'pending',
            'reference' => 'TKT-'.Str::upper(Str::random(10)),
            'issued_at' => now(),
        ]);

        if ($isFree) {
            $registration->notify(new EventRegistrationConfirmed($registration));

            return response()->json($registration, 201);
        }

        // Deliberately no query string of our own here: both gateways
        // append their own tracking params (Paystack: trxref/reference,
        // Flutterwave: tx_ref/transaction_id) to whatever we give them,
        // so adding our own "reference" would just collide with theirs.
        $callbackUrl = rtrim(config('app.frontend_url'), '/').'/payments/callback';

        try {
            $authorizationUrl = $this->payments->initializeForEventRegistration($registration, $callbackUrl);
        } catch (RuntimeException $e) {
            Log::error('Payment initialization failed', ['error' => $e->getMessage(), 'registration_id' => $registration->id]);

            return response()->json(['message' => 'We could not start this payment. Please try again shortly.'], 502);
        }

        return response()->json([...$registration->toArray(), 'authorizationUrl' => $authorizationUrl], 201);
    }
}
