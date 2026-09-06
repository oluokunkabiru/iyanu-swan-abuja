<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventTicketType;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use UsesMysqlInTransaction;

    public function test_registering_creates_an_outstanding_subscription_for_the_current_year(): void
    {
        $response = $this->withHeader('referer', 'http://localhost:5176')->postJson('/api/register', [
            'name' => 'Jane Member',
            'email' => 'jane.member@example.com',
            'password' => 'password123',
        ]);

        $response->assertCreated();

        $user = User::where('email', 'jane.member@example.com')->firstOrFail();

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'year' => now()->year,
            'status' => 'outstanding',
        ]);
    }

    public function test_paying_subscription_dues_returns_a_checkout_url_from_the_active_gateway(): void
    {
        SiteSetting::current()->update(['active_payment_gateway' => 'paystack']);

        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => ['authorization_url' => 'https://checkout.paystack.com/abc123', 'reference' => 'SUB-TEST'],
            ]),
        ]);

        $user = User::factory()->create(['role' => 'member']);

        $response = $this->actingAs($user)->postJson('/api/me/subscriptions/'.now()->year.'/pay');

        $response->assertOk()->assertJsonStructure(['authorizationUrl']);
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'payment_gateway' => 'paystack',
        ]);
    }

    public function test_verifying_a_subscription_reference_marks_it_paid_on_gateway_success(): void
    {
        SiteSetting::current()->update(['active_payment_gateway' => 'paystack']);

        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => ['authorization_url' => 'https://checkout.paystack.com/abc123'],
            ]),
            'api.paystack.co/transaction/verify/*' => Http::response([
                'status' => true,
                'data' => ['status' => 'success', 'amount' => 1_700_000, 'reference' => 'SUB-TEST-REF'],
            ]),
        ]);

        $user = User::factory()->create(['role' => 'member']);
        $this->actingAs($user)->postJson('/api/me/subscriptions/'.now()->year.'/pay')->assertOk();

        $subscription = $user->subscriptions()->first();

        $response = $this->getJson('/api/payments/verify/'.$subscription->reference);

        $response->assertOk()->assertJson(['type' => 'subscription', 'status' => 'paid']);
        $this->assertSame('paid', $subscription->fresh()->status);
    }

    public function test_registering_for_a_free_ticket_skips_the_payment_gateway_entirely(): void
    {
        Http::fake();

        $event = Event::create([
            'title' => 'Chapter Open Day',
            'slug' => 'chapter-open-day-'.uniqid(),
            'summary' => 'A free chapter open day.',
            'starts_at' => now()->addWeek(),
            'status' => 'published',
        ]);

        $ticketType = EventTicketType::create([
            'event_id' => $event->id,
            'label' => 'General admission',
            'audience' => 'non-member',
            'mode' => 'physical',
            'price' => 0,
            'currency' => 'NGN',
        ]);

        $response = $this->postJson("/api/events/{$event->slug}/register", [
            'event_ticket_type_id' => $ticketType->id,
            'name' => 'Free Attendee',
            'email' => 'free.attendee@example.com',
        ]);

        $response->assertCreated()->assertJsonFragment(['payment_status' => 'paid']);
        $this->assertArrayNotHasKey('authorizationUrl', $response->json());
        Http::assertNothingSent();
    }

    public function test_registering_for_a_paid_ticket_initializes_the_active_gateway(): void
    {
        SiteSetting::current()->update(['active_payment_gateway' => 'flutterwave']);

        Http::fake([
            'api.flutterwave.com/v3/payments' => Http::response([
                'status' => 'success',
                'data' => ['link' => 'https://checkout.flutterwave.com/xyz456'],
            ]),
        ]);

        $event = Event::create([
            'title' => 'Annual Technical Conference',
            'slug' => 'annual-technical-conference-'.uniqid(),
            'summary' => 'A paid technical conference.',
            'starts_at' => now()->addWeek(),
            'status' => 'published',
        ]);

        $ticketType = EventTicketType::create([
            'event_id' => $event->id,
            'label' => 'Member — physical',
            'audience' => 'member',
            'mode' => 'physical',
            'price' => 30000,
            'currency' => 'NGN',
        ]);

        $response = $this->postJson("/api/events/{$event->slug}/register", [
            'event_ticket_type_id' => $ticketType->id,
            'name' => 'Paying Attendee',
            'email' => 'paying.attendee@example.com',
        ]);

        $response->assertCreated()->assertJson([
            'payment_status' => 'pending',
            'authorizationUrl' => 'https://checkout.flutterwave.com/xyz456',
        ]);

        $this->assertDatabaseHas('event_registrations', [
            'email' => 'paying.attendee@example.com',
            'payment_gateway' => 'flutterwave',
        ]);
    }
}
