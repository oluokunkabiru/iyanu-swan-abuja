<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventTicketType;
use App\Models\User;
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

    public function test_viewing_a_paid_ticket_does_not_mark_it_checked_in(): void
    {
        [, $registration] = $this->createPaidRegistration();

        $response = $this->getJson("/api/tickets/{$registration->reference}/verify");

        $response->assertOk()->assertJson([
            'valid' => true,
            'checkedIn' => false,
            'name' => 'Gala Attendee',
            'eventTitle' => 'Chapter Gala Night',
        ]);

        $this->assertNull($registration->fresh()->checked_in_at);
    }

    public function test_an_authorized_admin_can_check_in_a_paid_ticket(): void
    {
        [, $registration] = $this->createPaidRegistration();
        $admin = User::factory()->admin()->create();

        $response = $this->actingAsApi($admin)
            ->postJson("/api/tickets/{$registration->reference}/check-in");

        $response->assertOk()->assertJson(['valid' => true, 'checkedIn' => true]);
        $this->assertNotNull($registration->fresh()->checked_in_at);
    }

    public function test_member_cannot_check_in_a_ticket(): void
    {
        [, $registration] = $this->createPaidRegistration();
        $member = User::factory()->create();

        $this->actingAsApi($member)
            ->postJson("/api/tickets/{$registration->reference}/check-in")
            ->assertForbidden();

        $this->assertNull($registration->fresh()->checked_in_at);
    }

    public function test_guest_cannot_check_in_a_ticket(): void
    {
        [, $registration] = $this->createPaidRegistration();

        $this->postJson("/api/tickets/{$registration->reference}/check-in")
            ->assertUnauthorized();

        $this->assertNull($registration->fresh()->checked_in_at);
    }

    public function test_checking_in_an_already_checked_in_ticket_does_not_move_the_check_in_time(): void
    {
        [, $registration] = $this->createPaidRegistration();
        $admin = User::factory()->admin()->create();

        $this->actingAsApi($admin)
            ->postJson("/api/tickets/{$registration->reference}/check-in")
            ->assertOk();
        $firstCheckInTime = $registration->fresh()->checked_in_at;

        $this->actingAsApi($admin)
            ->postJson("/api/tickets/{$registration->reference}/check-in")
            ->assertOk()
            ->assertJson(['valid' => true, 'checkedIn' => true]);

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
