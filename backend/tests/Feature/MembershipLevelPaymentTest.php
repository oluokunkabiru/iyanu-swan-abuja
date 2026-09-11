<?php

namespace Tests\Feature;

use App\Filament\Resources\Subscriptions\Pages\ManageSubscriptions;
use App\Models\MembershipLevel;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Livewire\Livewire;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class MembershipLevelPaymentTest extends TestCase
{
    use UsesMysqlInTransaction;

    public function test_public_endpoint_lists_only_active_levels_in_order(): void
    {
        MembershipLevel::factory()->create(['name' => 'Test Hidden Level', 'is_active' => false, 'sort_order' => 0]);
        MembershipLevel::factory()->create(['name' => 'Test Second Level', 'sort_order' => 99]);
        MembershipLevel::factory()->create(['name' => 'Test First Level', 'sort_order' => 1]);

        $response = $this->getJson('/api/membership-levels');

        $response->assertOk();
        $names = collect($response->json())->pluck('name')->all();

        $this->assertNotContains('Test Hidden Level', $names);
        $this->assertLessThan(
            array_search('Test Second Level', $names, true),
            array_search('Test First Level', $names, true),
        );
    }

    public function test_submitting_a_bank_transfer_sets_the_subscription_to_pending_review(): void
    {
        $user = User::factory()->create();
        $level = MembershipLevel::factory()->create(['subscription_amount' => 5_000, 'welfare_amount' => 12_000]);

        $response = $this->actingAs($user)->post('/api/me/subscriptions/'.now()->year.'/bank-transfer', [
            'membership_level_id' => $level->id,
            'reference' => 'GTB-REF-001',
            'evidence' => UploadedFile::fake()->image('receipt.jpg'),
        ]);

        $response->assertOk();

        $subscription = $user->subscriptions()->first();
        $this->assertSame('pending_review', $subscription->status);
        $this->assertSame('bank_transfer', $subscription->payment_gateway);
        $this->assertSame('GTB-REF-001', $subscription->bank_transfer_reference);
        $this->assertSame($level->id, $subscription->membership_level_id);
        $this->assertNotNull($subscription->evidence_url);
    }

    public function test_cannot_submit_another_bank_transfer_while_one_is_already_pending_review(): void
    {
        $user = User::factory()->create();
        $level = MembershipLevel::factory()->create();
        $user->subscriptions()->create([
            'year' => now()->year,
            'membership_level_id' => $level->id,
            'subscription_amount' => $level->subscription_amount,
            'welfare_amount' => $level->welfare_amount,
            'status' => 'pending_review',
            'payment_gateway' => 'bank_transfer',
        ]);

        $response = $this->actingAs($user)->post('/api/me/subscriptions/'.now()->year.'/bank-transfer', [
            'membership_level_id' => $level->id,
            'reference' => 'GTB-REF-002',
            'evidence' => UploadedFile::fake()->image('receipt.jpg'),
        ]);

        $response->assertStatus(422);
    }

    public function test_admin_approving_a_pending_bank_transfer_activates_an_eligible_member(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create(); // email_verified_at set by default factory state
        $member->memberProfile()->create(['membership_status' => 'pending']);
        $level = MembershipLevel::factory()->create();
        $subscription = $member->subscriptions()->create([
            'year' => now()->year,
            'membership_level_id' => $level->id,
            'subscription_amount' => $level->subscription_amount,
            'welfare_amount' => $level->welfare_amount,
            'status' => 'pending_review',
            'payment_gateway' => 'bank_transfer',
            'bank_transfer_reference' => 'GTB-REF-003',
        ]);

        Livewire::actingAs($admin)
            ->test(ManageSubscriptions::class)
            ->callTableAction('approve', $subscription);

        $subscription->refresh();
        $this->assertSame('paid', $subscription->status);
        $this->assertNotNull($subscription->paid_at);
        $this->assertSame('active', $member->memberProfile->fresh()->membership_status);
    }

    public function test_admin_rejecting_a_pending_bank_transfer_returns_it_to_outstanding(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $member = User::factory()->create();
        $level = MembershipLevel::factory()->create();
        $subscription = $member->subscriptions()->create([
            'year' => now()->year,
            'membership_level_id' => $level->id,
            'subscription_amount' => $level->subscription_amount,
            'welfare_amount' => $level->welfare_amount,
            'status' => 'pending_review',
            'payment_gateway' => 'bank_transfer',
        ]);

        Livewire::actingAs($admin)
            ->test(ManageSubscriptions::class)
            ->callTableAction('reject', $subscription, ['review_note' => 'Reference does not match our bank statement.']);

        $subscription->refresh();
        $this->assertSame('outstanding', $subscription->status);
        $this->assertSame('Reference does not match our bank statement.', $subscription->review_note);
    }
}
