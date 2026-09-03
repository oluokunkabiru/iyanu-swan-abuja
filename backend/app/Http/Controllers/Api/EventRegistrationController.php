<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EventRegistrationController extends Controller
{
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

        $registration = $event->registrations()->create([
            'event_ticket_type_id' => $ticketType->id,
            'user_id' => $request->user()?->id,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'notes' => $data['notes'] ?? null,
            'amount' => $ticketType->price,
            'payment_status' => 'pending',
            'reference' => 'TKT-'.Str::upper(Str::random(10)),
            'issued_at' => now(),
        ]);

        return response()->json($registration, 201);
    }
}
