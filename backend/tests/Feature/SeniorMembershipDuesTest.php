<?php

namespace Tests\Feature;

use App\Models\MembershipLevel;
use App\Models\SiteSetting;
use App\Models\User;
use App\Notifications\DuesPaymentConfirmed;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class SeniorMembershipDuesTest extends TestCase
{
    use UsesMysqlInTransaction;

    /** @return array<string, array{0: ?string, 1: int, 2: bool}> */
    public static function ageBoundaries(): array
    {
        return [
            'day before 65th birthday' => ['1961-10-26', 5000, false],
            '65th birthday' => ['1961-10-25', 0, true],
            'older than 65' => ['1960-01-01', 0, true],
            'missing date of birth' => [null, 5000, false],
        ];
    }

    #[DataProvider('ageBoundaries')]
    public function test_quote_applies_the_subscription_exemption_at_age_65(?string $birthday, int $subscription, bool $waived): void
    {
        $this->travelTo(Carbon::parse('2026-10-25 12:00:00'));
        $level = MembershipLevel::factory()->create(['subscription_amount' => 5000, 'welfare_amount' => 12000]);
        $member = $this->member($level, $birthday);

        $this->actingAsApi($member)->getJson('/api/me/subscriptions/2026/quote')
            ->assertOk()->assertJsonPath('membershipLevelId', (string) $level->id)
            ->assertJsonPath('subscription', $subscription)->assertJsonPath('welfare', 12000)
            ->assertJsonPath('balanceDue', $waived ? 12000 : 17000)->assertJsonPath('subscriptionWaived', $waived);

        $this->assertDatabaseMissing('subscriptions', ['user_id' => $member->id]);
    }

    public function test_online_payment_charges_seniors_only_the_configured_welfare_levy(): void
    {
        $this->travelTo(Carbon::parse('2026-10-25 12:00:00'));
        $level = MembershipLevel::factory()->create(['subscription_amount' => 5000, 'welfare_amount' => 12000]);
        $member = $this->member($level, '1961-10-25');
        $this->fakeGateway();

        $this->actingAsApi($member)->postJson('/api/me/subscriptions/2026/pay', [
            'membership_level_id' => $level->id, 'subscription_amount' => 1, 'welfare_amount' => 1,
        ])->assertOk()->assertJsonPath('authorizationUrl', 'https://checkout.paystack.com/dues-test');

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $member->id, 'year' => 2026, 'subscription_amount' => 0, 'welfare_amount' => 12000,
        ]);
        Http::assertSent(fn ($request): bool => $request->url() === 'https://api.paystack.co/transaction/initialize' && $request['amount'] === 1200000);
    }

    public function test_members_under_65_cannot_override_their_subscription_or_age_in_a_payment_request(): void
    {
        $this->travelTo(Carbon::parse('2026-10-25 12:00:00'));
        $level = MembershipLevel::factory()->create(['subscription_amount' => 5000, 'welfare_amount' => 12000]);
        $member = $this->member($level, '1985-05-10');
        $this->fakeGateway();

        $this->actingAsApi($member)->postJson('/api/me/subscriptions/2026/pay', [
            'membership_level_id' => $level->id, 'subscription_amount' => 0, 'date_of_birth' => '1950-01-01',
        ])->assertOk();

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $member->id, 'subscription_amount' => 5000, 'welfare_amount' => 12000,
        ]);
        Http::assertSent(fn ($request): bool => $request['amount'] === 1700000);
    }

    public function test_senior_bank_transfers_record_only_welfare_for_review(): void
    {
        $this->travelTo(Carbon::parse('2026-10-25 12:00:00'));
        Storage::fake('public');
        $level = MembershipLevel::factory()->create(['subscription_amount' => 5000, 'welfare_amount' => 12000]);
        $member = $this->member($level, '1950-01-01');

        $this->actingAsApi($member)->post('/api/me/subscriptions/2026/bank-transfer', [
            'membership_level_id' => $level->id, 'reference' => 'SENIOR-WELFARE',
            'evidence' => UploadedFile::fake()->image('receipt.jpg'),
        ], ['Accept' => 'application/json'])->assertOk();

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $member->id, 'subscription_amount' => 0, 'welfare_amount' => 12000, 'status' => 'pending_review',
        ]);
        $this->assertNotNull($member->subscriptions()->first()->evidence_url);
        $this->getJson('/api/me/subscriptions/2026/quote')->assertOk()
            ->assertJsonPath('status', 'pending_review')->assertJsonPath('balanceDue', 12000);
    }

    public function test_paid_dues_show_zero_balance_without_repricing_the_receipt_or_allowing_repayment(): void
    {
        $this->travelTo(Carbon::parse('2026-10-25 12:00:00'));
        Http::preventStrayRequests();
        $level = MembershipLevel::factory()->create(['subscription_amount' => 5000, 'welfare_amount' => 12000]);
        $member = $this->member($level, '1950-01-01');
        $member->subscriptions()->create([
            'year' => 2026, 'membership_level_id' => $level->id,
            'subscription_amount' => 5000, 'welfare_amount' => 12000, 'status' => 'paid', 'paid_at' => now(),
        ]);

        $this->actingAsApi($member)->getJson('/api/me/subscriptions/2026/quote')->assertOk()
            ->assertJsonPath('balanceDue', 0)->assertJsonPath('subscription', 5000)->assertJsonPath('total', 17000);
        $this->postJson('/api/me/subscriptions/2026/pay', ['membership_level_id' => $level->id])->assertUnprocessable();

        $this->assertDatabaseHas('subscriptions', ['user_id' => $member->id, 'subscription_amount' => 5000, 'status' => 'paid']);
        Http::assertNothingSent();
    }

    public function test_pending_review_payments_retain_their_original_amounts_and_cannot_be_paid_again(): void
    {
        $this->travelTo(Carbon::parse('2026-10-25 12:00:00'));
        Http::preventStrayRequests();
        $level = MembershipLevel::factory()->create(['subscription_amount' => 5000, 'welfare_amount' => 12000]);
        $member = $this->member($level, '1950-01-01');
        $member->subscriptions()->create([
            'year' => 2026, 'membership_level_id' => $level->id,
            'subscription_amount' => 5000, 'welfare_amount' => 12000, 'status' => 'pending_review',
        ]);

        $this->actingAsApi($member)->getJson('/api/me/subscriptions/2026/quote')->assertOk()
            ->assertJsonPath('status', 'pending_review')->assertJsonPath('subscription', 5000)->assertJsonPath('balanceDue', 17000);
        $this->postJson('/api/me/subscriptions/2026/pay', ['membership_level_id' => $level->id])->assertUnprocessable();

        $this->assertDatabaseHas('subscriptions', ['user_id' => $member->id, 'subscription_amount' => 5000, 'status' => 'pending_review']);
        Http::assertNothingSent();
    }

    public function test_retrying_an_outstanding_year_applies_the_members_current_age(): void
    {
        $this->travelTo(Carbon::parse('2026-10-25 12:00:00'));
        $level = MembershipLevel::factory()->create(['subscription_amount' => 5000, 'welfare_amount' => 12000]);
        $member = $this->member($level, '1961-10-25');
        $record = $member->subscriptions()->create([
            'year' => 2025, 'membership_level_id' => $level->id,
            'subscription_amount' => 5000, 'welfare_amount' => 12000, 'status' => 'outstanding',
        ]);
        $this->fakeGateway();

        $this->actingAsApi($member)->postJson('/api/me/subscriptions/2025/pay', ['membership_level_id' => $level->id])->assertOk();

        $this->assertSame(0, $record->fresh()->subscription_amount);
        $this->assertSame(12000, $record->fresh()->welfare_amount);
        $this->assertSame(1, $member->subscriptions()->count());
        Http::assertSent(fn ($request): bool => $request['amount'] === 1200000);
    }

    public function test_zero_total_dues_are_settled_without_a_gateway_charge(): void
    {
        $this->travelTo(Carbon::parse('2026-10-25 12:00:00'));
        Http::preventStrayRequests();
        Notification::fake();
        $level = MembershipLevel::factory()->create(['subscription_amount' => 5000, 'welfare_amount' => 0]);
        $member = $this->member($level, '1950-01-01');

        $this->actingAsApi($member)->postJson('/api/me/subscriptions/2026/pay', ['membership_level_id' => $level->id])
            ->assertOk()->assertJsonPath('authorizationUrl', null)->assertJsonPath('status', 'paid');
        $this->getJson('/api/me/subscriptions/2026/quote')->assertOk()->assertJsonPath('balanceDue', 0);

        $record = $member->subscriptions()->first();
        $this->getJson('/api/payments/verify/'.$record->reference)
            ->assertOk()->assertJsonPath('status', 'paid');

        $this->assertDatabaseHas('subscriptions', ['user_id' => $member->id, 'subscription_amount' => 0, 'welfare_amount' => 0, 'status' => 'paid']);
        Http::assertNothingSent();
        Notification::assertSentToTimes($member, DuesPaymentConfirmed::class, 1);
    }

    public function test_unknown_membership_levels_are_not_misrepresented_as_zero_dues(): void
    {
        $level = MembershipLevel::factory()->create(['subscription_amount' => 5000, 'welfare_amount' => 12000]);
        $member = User::factory()->create();
        $member->memberProfile()->create(['credential' => 'Unconfigured level']);

        $this->actingAsApi($member)->getJson('/api/me/subscriptions/'.now()->year.'/quote')
            ->assertOk()->assertJsonPath('balanceDue', null)->assertJsonPath('membershipLevelId', null);
        $this->getJson('/api/me/subscriptions/'.now()->year.'/quote?membership_level_id='.$level->id)
            ->assertOk()->assertJsonPath('balanceDue', 17000);
    }

    public function test_quotes_require_member_authentication(): void
    {
        $this->getJson('/api/me/subscriptions/2026/quote')->assertUnauthorized();
    }

    private function member(MembershipLevel $level, ?string $birthday): User
    {
        $member = User::factory()->create();
        $member->memberProfile()->create([
            'credential' => $level->name, 'date_of_birth' => $birthday, 'membership_status' => 'active',
        ]);

        return $member;
    }

    private function fakeGateway(): void
    {
        SiteSetting::current()->update(['active_payment_gateway' => 'paystack']);
        Http::preventStrayRequests();
        Http::fake([
            'api.paystack.co/transaction/initialize' => Http::response([
                'status' => true, 'data' => ['authorization_url' => 'https://checkout.paystack.com/dues-test'],
            ]),
        ]);
    }
}
