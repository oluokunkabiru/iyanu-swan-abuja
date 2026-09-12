<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventTicketType;
use App\Models\MembershipLevel;
use App\Models\SiteSetting;
use App\Models\User;
use App\Notifications\DuesPaymentConfirmed;
use App\Notifications\MembershipActivated;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class PaymentFlowTest extends TestCase
{
    use UsesMysqlInTransaction;

    public function test_registering_is_free_and_creates_no_subscription(): void
    {
        $level = MembershipLevel::factory()->create();

        $response = $this->withHeader('referer', 'http://localhost:5176')->postJson('/api/register', [
            'name' => 'Jane Member',
            'email' => 'jane.member@example.com',
            'password' => 'password123',
            'membership_number' => 'ICAN/12345',
            'credential' => $level->name,
            'phone' => '08000000000',
            'residential_address' => '12 Chapter Close, Abuja',
            'place_of_work' => 'Federal Ministry of Finance',
        ]);

        $response->assertCreated();

        $user = User::where('email', 'jane.member@example.com')->firstOrFail();

        $this->assertDatabaseMissing('subscriptions', ['user_id' => $user->id]);
        $this->assertDatabaseHas('member_profiles', [
            'user_id' => $user->id,
            'membership_number' => 'ICAN/12345',
            'credential' => $level->name,
            'phone' => '08000000000',
            'residential_address' => '12 Chapter Close, Abuja',
            'place_of_work' => 'Federal Ministry of Finance',
        ]);
    }

    public function test_registering_requires_the_ican_and_contact_details(): void
    {
        $response = $this->postJson('/api/register', [
            'name' => 'Jane Member',
            'email' => 'jane.member@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors([
            'membership_number', 'credential', 'phone', 'residential_address', 'place_of_work',
        ]);
    }

    public function test_registering_accepts_a_membership_level_name_longer_than_the_old_credential_limit(): void
    {
        $level = MembershipLevel::factory()->create(['name' => 'AATWA (Associate Accounting Technician)']);

        $response = $this->withHeader('referer', 'http://localhost:5176')->postJson('/api/register', [
            'name' => 'Jane Member',
            'email' => 'jane.member@example.com',
            'password' => 'password123',
            'membership_number' => 'ICAN/12345',
            'credential' => $level->name,
            'phone' => '08000000000',
            'residential_address' => '12 Chapter Close, Abuja',
            'place_of_work' => 'Federal Ministry of Finance',
        ]);

        $response->assertCreated();
        $this->assertDatabaseHas('member_profiles', ['credential' => 'AATWA (Associate Accounting Technician)']);
    }

    public function test_registering_rejects_an_ican_level_that_is_not_an_active_membership_level(): void
    {
        MembershipLevel::factory()->create(['name' => 'Real Level', 'is_active' => true]);
        MembershipLevel::factory()->create(['name' => 'Retired Level', 'is_active' => false]);

        $response = $this->postJson('/api/register', [
            'name' => 'Jane Member',
            'email' => 'jane.member@example.com',
            'password' => 'password123',
            'membership_number' => 'ICAN/12345',
            'credential' => 'Retired Level',
            'phone' => '08000000000',
            'residential_address' => '12 Chapter Close, Abuja',
            'place_of_work' => 'Federal Ministry of Finance',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['credential']);
    }

    public function test_member_can_update_their_place_of_work_and_residential_address(): void
    {
        $user = User::factory()->create();
        $user->memberProfile()->create(['membership_status' => 'active']);

        $this->actingAs($user)->putJson('/api/me', [
            'residential_address' => '4 New Layout, Kubwa, Abuja',
            'place_of_work' => 'ABC Chartered Accountants',
        ])->assertOk();

        $this->assertDatabaseHas('member_profiles', [
            'user_id' => $user->id,
            'residential_address' => '4 New Layout, Kubwa, Abuja',
            'place_of_work' => 'ABC Chartered Accountants',
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
        $level = MembershipLevel::factory()->create(['subscription_amount' => 5_000, 'welfare_amount' => 12_000]);

        $response = $this->actingAs($user)->postJson('/api/me/subscriptions/'.now()->year.'/pay', [
            'membership_level_id' => $level->id,
        ]);

        $response->assertOk()->assertJsonStructure(['authorizationUrl']);
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'membership_level_id' => $level->id,
            'subscription_amount' => 5_000,
            'welfare_amount' => 12_000,
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
        $level = MembershipLevel::factory()->create();
        $this->actingAs($user)->postJson('/api/me/subscriptions/'.now()->year.'/pay', [
            'membership_level_id' => $level->id,
        ])->assertOk();

        $subscription = $user->subscriptions()->first();

        $response = $this->getJson('/api/payments/verify/'.$subscription->reference);

        $response->assertOk()->assertJson(['type' => 'subscription', 'status' => 'paid']);
        $this->assertSame('paid', $subscription->fresh()->status);
    }

    public function test_verifying_a_subscription_payment_sends_a_dues_confirmation_email(): void
    {
        Notification::fake();
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
        $level = MembershipLevel::factory()->create();
        $this->actingAs($user)->postJson('/api/me/subscriptions/'.now()->year.'/pay', [
            'membership_level_id' => $level->id,
        ])->assertOk();

        $subscription = $user->subscriptions()->first();

        $this->getJson('/api/payments/verify/'.$subscription->reference)->assertOk();

        Notification::assertSentTo($user, DuesPaymentConfirmed::class);
    }

    public function test_re_verifying_an_already_paid_subscription_does_not_resend_the_confirmation_email(): void
    {
        Notification::fake();
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
        $level = MembershipLevel::factory()->create();
        $this->actingAs($user)->postJson('/api/me/subscriptions/'.now()->year.'/pay', [
            'membership_level_id' => $level->id,
        ])->assertOk();

        $subscription = $user->subscriptions()->first();

        // Simulate the gateway webhook and the frontend's own callback both
        // verifying the same reference.
        $this->getJson('/api/payments/verify/'.$subscription->reference)->assertOk();
        $this->getJson('/api/payments/verify/'.$subscription->reference)->assertOk();

        Notification::assertSentTimes(DuesPaymentConfirmed::class, 1);
    }

    public function test_paying_dues_activates_membership_and_sends_a_welcome_email_once_verified(): void
    {
        Notification::fake();
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
        $user->memberProfile()->create(['membership_status' => 'pending']);
        $level = MembershipLevel::factory()->create();
        $this->actingAs($user)->postJson('/api/me/subscriptions/'.now()->year.'/pay', [
            'membership_level_id' => $level->id,
        ])->assertOk();

        $subscription = $user->subscriptions()->first();

        $this->getJson('/api/payments/verify/'.$subscription->reference)->assertOk();

        $this->assertSame('active', $user->memberProfile->fresh()->membership_status);
        Notification::assertSentTo($user, MembershipActivated::class);
    }

    public function test_renewing_dues_while_already_active_does_not_resend_the_welcome_email(): void
    {
        Notification::fake();
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
        $user->memberProfile()->create(['membership_status' => 'active']);
        $level = MembershipLevel::factory()->create();
        $this->actingAs($user)->postJson('/api/me/subscriptions/'.now()->year.'/pay', [
            'membership_level_id' => $level->id,
        ])->assertOk();

        $subscription = $user->subscriptions()->first();

        $this->getJson('/api/payments/verify/'.$subscription->reference)->assertOk();

        Notification::assertNotSentTo($user, MembershipActivated::class);
        Notification::assertSentTo($user, DuesPaymentConfirmed::class);
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
