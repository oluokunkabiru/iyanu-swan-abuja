<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventTicketType;
use App\Models\MembershipLevel;
use App\Models\NotificationSetting;
use App\Models\SiteSetting;
use App\Models\User;
use App\Notifications\DuesPaymentConfirmed;
use App\Notifications\EventRegistrationConfirmed;
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
            'last_name' => 'Member',
            'first_name' => 'Jane',
            'middle_name' => 'Ada',
            'email' => 'jane.member@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'membership_number' => 'ICAN/12345',
            'credential' => $level->name,
            'phone' => '08000000000',
            'residential_address' => '12 Chapter Close, Abuja',
            'place_of_work' => 'Federal Ministry of Finance',
            'specialisation' => 'Audit',
        ]);

        $response->assertCreated()->assertJsonPath('user.firstName', 'Jane');

        $user = User::where('email', 'jane.member@example.com')->firstOrFail();

        $this->assertDatabaseMissing('subscriptions', ['user_id' => $user->id]);
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'name' => 'Jane Ada Member',
            'last_name' => 'Member',
            'first_name' => 'Jane',
            'middle_name' => 'Ada',
        ]);
        $this->assertDatabaseHas('member_profiles', [
            'user_id' => $user->id,
            'membership_number' => 'ICAN/12345',
            'credential' => $level->name,
            'phone' => '08000000000',
            'residential_address' => '12 Chapter Close, Abuja',
            'place_of_work' => 'Federal Ministry of Finance',
            'specialisation' => 'Audit',
        ]);
    }

    public function test_registering_requires_the_ican_and_contact_details(): void
    {
        $response = $this->postJson('/api/register', [
            'last_name' => 'Member',
            'first_name' => 'Jane',
            'email' => 'jane.member@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors([
            'membership_number', 'credential', 'phone', 'residential_address', 'place_of_work',
        ]);
    }

    public function test_registering_accepts_a_membership_level_name_longer_than_the_old_credential_limit(): void
    {
        $level = MembershipLevel::factory()->create(['name' => 'AATWA (Associate Accounting Technician)']);

        $response = $this->withHeader('referer', 'http://localhost:5176')->postJson('/api/register', [
            'last_name' => 'Member',
            'first_name' => 'Jane',
            'email' => 'jane.member@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
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
            'last_name' => 'Member',
            'first_name' => 'Jane',
            'email' => 'jane.member@example.com',
            'password' => 'Password123!',
            'password_confirmation' => 'Password123!',
            'membership_number' => 'ICAN/12345',
            'credential' => 'Retired Level',
            'phone' => '08000000000',
            'residential_address' => '12 Chapter Close, Abuja',
            'place_of_work' => 'Federal Ministry of Finance',
            'specialisation' => 'Audit',
        ]);

        $response->assertStatus(422)->assertJsonValidationErrors(['credential']);
    }

    public function test_member_can_update_their_place_of_work_and_residential_address(): void
    {
        $user = User::factory()->create();
        $user->memberProfile()->create(['membership_status' => 'active']);

        $this->actingAsApi($user)->putJson('/api/me', [
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

        $response = $this->actingAsApi($user)->postJson('/api/me/subscriptions/'.now()->year.'/pay', [
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

    public function test_retrying_an_unpaid_subscription_generates_a_new_gateway_reference(): void
    {
        SiteSetting::current()->update(['active_payment_gateway' => 'paystack']);
        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response([
                'status' => true,
                'data' => ['authorization_url' => 'https://checkout.paystack.com/abc123'],
            ]),
        ]);

        $user = User::factory()->create(['role' => 'member']);
        $level = MembershipLevel::factory()->create(['subscription_amount' => 5_000, 'welfare_amount' => 12_000]);
        $subscription = $user->subscriptions()->create([
            'membership_level_id' => $level->id,
            'year' => now()->year,
            'subscription_amount' => $level->subscription_amount,
            'welfare_amount' => $level->welfare_amount,
            'status' => 'outstanding',
            'reference' => 'SUB-OLD-REFERENCE',
            'payment_gateway' => 'paystack',
        ]);

        $this->actingAsApi($user)->postJson('/api/me/subscriptions/'.now()->year.'/pay', [
            'membership_level_id' => $level->id,
        ])->assertOk();

        $newReference = $subscription->fresh()->reference;

        $this->assertNotSame('SUB-OLD-REFERENCE', $newReference);
        Http::assertSent(fn ($request): bool => $request['reference'] === $newReference);
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
        $this->actingAsApi($user)->postJson('/api/me/subscriptions/'.now()->year.'/pay', [
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
        $this->actingAsApi($user)->postJson('/api/me/subscriptions/'.now()->year.'/pay', [
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
        $this->actingAsApi($user)->postJson('/api/me/subscriptions/'.now()->year.'/pay', [
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
        $this->actingAsApi($user)->postJson('/api/me/subscriptions/'.now()->year.'/pay', [
            'membership_level_id' => $level->id,
        ])->assertOk();

        $subscription = $user->subscriptions()->first();

        $this->getJson('/api/payments/verify/'.$subscription->reference)->assertOk();

        $this->assertSame('active', $user->memberProfile->fresh()->membership_status);
        Notification::assertSentTo($user, MembershipActivated::class);
    }

    public function test_activating_membership_provisions_an_official_mailbox_when_cpanel_is_configured(): void
    {
        Notification::fake();
        config([
            'services.cpanel.host' => 'server.example.com',
            'services.cpanel.port' => 2083,
            'services.cpanel.username' => 'swanabuj',
            'services.cpanel.api_token' => 'test-token',
            'services.cpanel.email_domain' => 'swanabujachapter.org',
            'services.cpanel.quota_mb' => 250,
        ]);

        Http::fake([
            '*/execute/Email/add_pop*' => Http::response(['result' => ['status' => 1, 'errors' => null]]),
        ]);

        $user = User::factory()->create(['name' => 'Jane Doe', 'role' => 'member']);
        $user->memberProfile()->create(['membership_status' => 'pending']);
        $user->subscriptions()->create([
            'year' => now()->year,
            'subscription_amount' => 5_000,
            'welfare_amount' => 12_000,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $user->activateMembershipIfEligible();

        $this->assertSame('jane.doe@swanabujachapter.org', $user->fresh()->official_email);
        Notification::assertSentTo($user, MembershipActivated::class, function (MembershipActivated $notification) use ($user): bool {
            $mail = $notification->toMail($user);

            return $mail->actionText === 'Open your official mailbox'
                && $mail->actionUrl === 'https://server.example.com:2096'
                && in_array('Address: jane.doe@swanabujachapter.org', $mail->introLines, true)
                && in_array('Android phone: in Gmail or your Email app, choose Add account, then Other and IMAP. Enter your full email address and the temporary password above. If it asks for a server, enter server.example.com and choose port 993 with SSL/TLS.', $mail->introLines, true)
                && in_array('If IMAP is not available, choose POP instead and use the same details with port 995 and SSL/TLS.', $mail->introLines, true);
        });
    }

    public function test_admin_can_switch_off_official_email_provisioning_even_when_cpanel_is_configured(): void
    {
        Notification::fake();
        NotificationSetting::current()->update(['cpanel_email_provisioning_enabled' => false]);
        config([
            'services.cpanel.host' => 'server.example.com',
            'services.cpanel.port' => 2083,
            'services.cpanel.username' => 'swanabuj',
            'services.cpanel.api_token' => 'test-token',
            'services.cpanel.email_domain' => 'swanabujachapter.org',
            'services.cpanel.quota_mb' => 250,
        ]);

        Http::fake([
            '*/execute/Email/add_pop*' => Http::response(['result' => ['status' => 1, 'errors' => null]]),
        ]);

        $user = User::factory()->create(['name' => 'Jane Doe', 'role' => 'member']);
        $user->memberProfile()->create(['membership_status' => 'pending']);
        $user->subscriptions()->create([
            'year' => now()->year,
            'subscription_amount' => 5_000,
            'welfare_amount' => 12_000,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $user->activateMembershipIfEligible();

        $this->assertNull($user->fresh()->official_email);
        Http::assertNothingSent();
        Notification::assertSentTo($user, MembershipActivated::class, function (MembershipActivated $notification) use ($user): bool {
            $mail = $notification->toMail($user);

            return $mail->actionText === 'Go to your dashboard'
                && ! str_contains(implode(' ', $mail->introLines), 'Android phone:');
        });
    }

    public function test_activating_membership_without_cpanel_configured_still_notifies_normally(): void
    {
        Notification::fake();
        config([
            'services.cpanel.host' => null,
            'services.cpanel.username' => null,
            'services.cpanel.api_token' => null,
            'services.cpanel.email_domain' => null,
        ]);

        $user = User::factory()->create(['name' => 'Jane Doe', 'role' => 'member']);
        $user->memberProfile()->create(['membership_status' => 'pending']);
        $user->subscriptions()->create([
            'year' => now()->year,
            'subscription_amount' => 5_000,
            'welfare_amount' => 12_000,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $user->activateMembershipIfEligible();

        $this->assertNull($user->fresh()->official_email);
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
        $this->actingAsApi($user)->postJson('/api/me/subscriptions/'.now()->year.'/pay', [
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

    public function test_registering_for_a_free_ticket_sends_a_confirmation_email(): void
    {
        Notification::fake();
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

        $this->postJson("/api/events/{$event->slug}/register", [
            'event_ticket_type_id' => $ticketType->id,
            'name' => 'Free Attendee',
            'email' => 'free.attendee@example.com',
        ])->assertCreated();

        $registration = $event->registrations()->first();

        Notification::assertSentTo($registration, EventRegistrationConfirmed::class);
    }

    public function test_verifying_a_paid_ticket_reference_sends_a_confirmation_email(): void
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
                'data' => ['status' => 'success', 'amount' => 3_000_000, 'reference' => 'TKT-TEST-REF'],
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

        $this->postJson("/api/events/{$event->slug}/register", [
            'event_ticket_type_id' => $ticketType->id,
            'name' => 'Paying Attendee',
            'email' => 'paying.attendee@example.com',
        ])->assertCreated();

        $registration = $event->registrations()->first();

        $this->getJson('/api/payments/verify/'.$registration->reference)->assertOk();

        Notification::assertSentTo($registration->fresh(), EventRegistrationConfirmed::class);
    }

    public function test_re_verifying_an_already_paid_ticket_does_not_resend_the_confirmation_email(): void
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
                'data' => ['status' => 'success', 'amount' => 3_000_000, 'reference' => 'TKT-TEST-REF'],
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

        $this->postJson("/api/events/{$event->slug}/register", [
            'event_ticket_type_id' => $ticketType->id,
            'name' => 'Paying Attendee',
            'email' => 'paying.attendee@example.com',
        ])->assertCreated();

        $registration = $event->registrations()->first();

        $this->getJson('/api/payments/verify/'.$registration->reference)->assertOk();
        $this->getJson('/api/payments/verify/'.$registration->reference)->assertOk();

        Notification::assertSentTimes(EventRegistrationConfirmed::class, 1);
    }
}
