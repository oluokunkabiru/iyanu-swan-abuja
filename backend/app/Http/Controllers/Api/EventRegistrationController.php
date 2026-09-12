<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\EventRegistration;
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

    /**
     * Public, unauthenticated ticket verification — the link embedded in
     * the confirmation email/QR code. Deliberately no login gate (the
     * reference is an unguessable bearer token, the same trust model this
     * app already uses for payment references) so door staff can scan and
     * check a ticket without needing an admin account on hand. The first
     * successful scan of a paid ticket marks it checked in; later scans
     * report that it was already checked in rather than re-marking it.
     */
    public function verifyTicket(string $reference): JsonResponse
    {
        $registration = EventRegistration::with(['event', 'ticketType'])
            ->where('reference', $reference)
            ->first();

        if (! $registration) {
            return response()->json(['valid' => false, 'reason' => 'not_found'], 404);
        }

        if ($registration->payment_status !== 'paid') {
            return response()->json([
                'valid' => false,
                'reason' => 'not_paid',
                'name' => $registration->name,
                'eventTitle' => $registration->event->title,
            ]);
        }

        $wasAlreadyCheckedIn = $registration->checked_in_at !== null;

        if (! $wasAlreadyCheckedIn) {
            $registration->update(['checked_in_at' => now()]);
        }

        return response()->json([
            'valid' => true,
            'alreadyCheckedIn' => $wasAlreadyCheckedIn,
            'checkedInAt' => $registration->checked_in_at->toIso8601String(),
            'name' => $registration->name,
            'ticketLabel' => $registration->ticketType?->label,
            'eventTitle' => $registration->event->title,
            'venue' => $registration->event->location,
            'startsAt' => $registration->event->starts_at->toIso8601String(),
        ]);
    }
}
