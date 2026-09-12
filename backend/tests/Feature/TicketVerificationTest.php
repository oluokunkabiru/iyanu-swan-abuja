<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventTicketType;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class TicketVerificationTest extends TestCase
{
    use UsesMysqlInTransaction;

    private function createPaidRegistration(string $paymentStatus = 'paid'): array
    {
        $event = Event::create([
            'title' => 'Chapter Gala Night',
            'slug' => 'chapter-gala-night-'.uniqid(),
            'summary' => 'An evening event.',
            'location' => 'Congress Hall',
            'starts_at' => now()->addWeek(),
            'status' => 'published',
        ]);

        $ticketType = EventTicketType::create([
            'event_id' => $event->id,
            'label' => 'General admission',
            'audience' => 'member',
            'mode' => 'physical',
            'price' => 20000,
            'currency' => 'NGN',
        ]);

        $registration = $event->registrations()->create([
            'event_ticket_type_id' => $ticketType->id,
            'name' => 'Gala Attendee',
            'email' => 'gala.attendee@example.com',
            'amount' => 20000,
            'payment_status' => $paymentStatus,
            'reference' => 'TKT-VERIFY-'.uniqid(),
            'issued_at' => now(),
        ]);

        return [$event, $registration];
    }

    public function test_verifying_a_paid_ticket_marks_it_checked_in(): void
    {
        [, $registration] = $this->createPaidRegistration();

        $response = $this->getJson("/api/tickets/{$registration->reference}/verify");

        $response->assertOk()->assertJson([
            'valid' => true,
            'alreadyCheckedIn' => false,
            'name' => 'Gala Attendee',
            'eventTitle' => 'Chapter Gala Night',
        ]);

        $this->assertNotNull($registration->fresh()->checked_in_at);
    }

    public function test_verifying_an_already_checked_in_ticket_does_not_move_the_check_in_time(): void
    {
        [, $registration] = $this->createPaidRegistration();

        $this->getJson("/api/tickets/{$registration->reference}/verify")->assertOk();
        $firstCheckInTime = $registration->fresh()->checked_in_at;

        $response = $this->getJson("/api/tickets/{$registration->reference}/verify");

        $response->assertOk()->assertJson(['valid' => true, 'alreadyCheckedIn' => true]);
        $this->assertTrue($firstCheckInTime->equalTo($registration->fresh()->checked_in_at));
    }

    public function test_verifying_an_unpaid_ticket_is_reported_invalid(): void
    {
        [, $registration] = $this->createPaidRegistration('pending');

        $response = $this->getJson("/api/tickets/{$registration->reference}/verify");

        $response->assertOk()->assertJson(['valid' => false, 'reason' => 'not_paid']);
        $this->assertNull($registration->fresh()->checked_in_at);
    }

    public function test_verifying_an_unknown_reference_returns_not_found(): void
    {
        $response = $this->getJson('/api/tickets/TKT-DOES-NOT-EXIST/verify');

        $response->assertNotFound()->assertJson(['valid' => false, 'reason' => 'not_found']);
    }
}
