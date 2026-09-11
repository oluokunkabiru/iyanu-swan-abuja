<?php

namespace Tests\Feature;

use App\Models\MembershipLevel;
use App\Models\NotificationSetting;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class MembershipEmailVerificationTest extends TestCase
{
    use UsesMysqlInTransaction;

    public function test_registering_sends_a_verification_email_to_the_registered_address(): void
    {
        Notification::fake();
        $level = MembershipLevel::factory()->create();

        $this->withHeader('referer', 'http://localhost:5176')->postJson('/api/register', [
            'name' => 'Jane Member',
            'email' => 'jane.member@example.com',
            'password' => 'password123',
            'membership_number' => 'ICAN/12345',
            'credential' => $level->name,
            'phone' => '08000000000',
            'residential_address' => '12 Chapter Close, Abuja',
            'place_of_work' => 'Federal Ministry of Finance',
        ])->assertCreated();

        $user = User::where('email', 'jane.member@example.com')->firstOrFail();

        Notification::assertSentTo($user, VerifyEmail::class);
    }

    public function test_verifying_email_activates_membership_when_dues_are_already_paid(): void
    {
        $user = User::factory()->unverified()->create();
        $user->memberProfile()->create(['membership_status' => 'pending']);
        $user->subscriptions()->create([
            'year' => now()->year,
            'subscription_amount' => 5_000,
            'welfare_amount' => 12_000,
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ]);

        $this->get($url)->assertRedirect();

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertSame('active', $user->memberProfile->membership_status);
    }

    public function test_membership_stays_pending_after_verification_if_dues_are_unpaid(): void
    {
        $user = User::factory()->unverified()->create();
        $user->memberProfile()->create(['membership_status' => 'pending']);

        $url = URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
            'id' => $user->id,
            'hash' => sha1($user->getEmailForVerification()),
        ]);

        $this->get($url)->assertRedirect();

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertSame('pending', $user->memberProfile->membership_status);
    }

    public function test_cannot_add_a_second_email_before_the_registered_email_is_verified(): void
    {
        $user = User::factory()->unverified()->create();
        $user->memberProfile()->create(['membership_status' => 'pending']);

        $this->actingAs($user)->putJson('/api/me', [
            'personal_email' => 'personal@example.com',
        ])->assertStatus(422);

        $this->assertNull($user->fresh()->personal_email);
    }

    public function test_can_add_personal_and_official_emails_once_verified(): void
    {
        $user = User::factory()->create();
        $user->memberProfile()->create(['membership_status' => 'active']);

        $this->actingAs($user)->putJson('/api/me', [
            'personal_email' => 'personal@example.com',
            'official_email' => 'official@example.com',
            'notification_email_preference' => 'all',
        ])->assertOk();

        $user->refresh();
        $this->assertSame('personal@example.com', $user->personal_email);
        $this->assertSame('official@example.com', $user->official_email);
        $this->assertSame('all', $user->notification_email_preference);
    }

    public function test_mail_routes_to_the_members_own_preference_over_the_site_default(): void
    {
        NotificationSetting::current()->update(['member_email_default' => 'registered']);

        $user = User::factory()->create([
            'personal_email' => 'personal@example.com',
            'official_email' => 'official@example.com',
            'notification_email_preference' => 'all',
        ]);

        $this->assertSame(
            [$user->email, 'personal@example.com', 'official@example.com'],
            $user->routeNotificationForMail(),
        );
    }

    public function test_mail_falls_back_to_the_site_default_when_the_member_has_no_preference(): void
    {
        NotificationSetting::current()->update(['member_email_default' => 'personal']);

        $user = User::factory()->create([
            'personal_email' => 'personal@example.com',
            'notification_email_preference' => null,
        ]);

        $this->assertSame(['personal@example.com'], $user->routeNotificationForMail());
    }
}
