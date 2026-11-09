<?php

namespace Tests\Feature;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Models\MembershipLevel;
use App\Models\User;
use App\Notifications\AccountCreatedByAdmin;
use App\Notifications\LegacyMemberImported;
use App\Notifications\VerifyEmail;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use RuntimeException;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class LegacyMemberImportTest extends TestCase
{
    use UsesMysqlInTransaction;

    public function test_member_created_by_an_admin_must_change_their_password_on_first_sign_in(): void
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();

        Livewire::actingAs($admin)
            ->test(CreateUser::class)
            ->fillForm([
                'name' => 'New Member',
                'email' => 'new.member@example.com',
                'password' => 'AdminSetPassword1!',
                'role' => 'member',
                'memberProfile' => ['date_of_birth' => '1985-03-14'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $member = User::query()->where('email', 'new.member@example.com')->firstOrFail();

        $this->assertTrue($member->must_change_password);
        $this->assertTrue(Hash::check('AdminSetPassword1!', $member->password));
    }

    public function test_admin_can_import_an_existing_member_with_paid_current_year_dues(): void
    {
        Notification::fake();
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        $admin = User::factory()->admin()->create();
        $level = MembershipLevel::factory()->create([
            'subscription_amount' => 5_000,
            'welfare_amount' => 12_000,
        ]);

        Livewire::actingAs($admin)
            ->test(CreateUser::class)
            ->fillForm([
                'name' => 'Legacy Member',
                'email' => 'legacy.member@example.com',
                'role' => 'member',
                'is_legacy_member' => true,
                'legacy_membership_level_id' => $level->id,
                'memberProfile' => [
                    'membership_number' => 'ICAN/LEGACY-001',
                    'credential' => $level->name,
                    'phone' => '08000000000',
                    'date_of_birth' => '1985-03-14',
                ],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $member = User::query()->where('email', 'legacy.member@example.com')->firstOrFail();

        $this->assertTrue($member->hasVerifiedEmail());
        $this->assertTrue($member->must_change_password);
        $this->assertSame('active', $member->memberProfile->membership_status);
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $member->id,
            'membership_level_id' => $level->id,
            'year' => now()->year,
            'subscription_amount' => 5_000,
            'welfare_amount' => 12_000,
            'status' => 'paid',
            'payment_gateway' => 'legacy_import',
        ]);
        Notification::assertSentTo($member, LegacyMemberImported::class);
    }

    public function test_member_created_by_an_admin_is_welcomed_and_asked_to_verify_without_dues_being_cleared(): void
    {
        Notification::fake();

        $this->createUserThroughTheAdminPanel('welcomed.member@example.com', 'member');

        $member = User::query()->where('email', 'welcomed.member@example.com')->firstOrFail();

        $this->assertFalse($member->hasVerifiedEmail());
        $this->assertTrue($member->must_change_password);
        $this->assertSame(0, $member->subscriptions()->count());
        $this->assertNotSame('active', $member->memberProfile?->membership_status);

        Notification::assertSentTo($member, VerifyEmail::class);
        Notification::assertSentTo($member, AccountCreatedByAdmin::class, function (AccountCreatedByAdmin $welcome) use ($member): bool {
            $html = (string) $welcome->toMail($member)->render();

            return str_contains($html, 'Welcome,')
                && str_contains($html, 'welcomed.member@example.com')
                && str_contains($html, 'AdminSetPassword1!')
                && str_contains($html, 'replace this temporary password')
                && str_contains($html, '/login');
        });
    }

    public function test_admin_user_is_welcomed_with_the_admin_panel_sign_in_and_no_forced_change(): void
    {
        Notification::fake();

        $this->createUserThroughTheAdminPanel('new.admin@example.com', 'admin');

        $admin = User::query()->where('email', 'new.admin@example.com')->firstOrFail();

        $this->assertFalse($admin->must_change_password);
        Notification::assertSentTo($admin, VerifyEmail::class);
        Notification::assertSentTo($admin, AccountCreatedByAdmin::class, function (AccountCreatedByAdmin $welcome) use ($admin): bool {
            $html = (string) $welcome->toMail($admin)->render();

            return str_contains($html, route('filament.admin.auth.login'))
                && ! str_contains($html, 'replace this temporary password');
        });
    }

    public function test_imported_member_is_welcomed_with_login_details_and_gets_no_verification_email(): void
    {
        Notification::fake();
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        Livewire::actingAs(User::factory()->admin()->create())
            ->test(CreateUser::class)
            ->fillForm([
                'name' => 'Imported Member',
                'email' => 'imported.member@example.com',
                'role' => 'member',
                'is_legacy_member' => true,
                'legacy_membership_level_id' => MembershipLevel::factory()->create()->id,
                'memberProfile' => ['membership_number' => 'ICAN/LEGACY-002', 'phone' => '08000000000', 'date_of_birth' => '1985-03-14'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $member = User::query()->where('email', 'imported.member@example.com')->firstOrFail();

        Notification::assertNotSentTo($member, VerifyEmail::class);
        Notification::assertSentTo($member, LegacyMemberImported::class, function (LegacyMemberImported $welcome) use ($member): bool {
            $html = (string) $welcome->toMail($member)->render();

            return str_contains($html, 'Welcome,')
                && str_contains($html, 'imported.member@example.com')
                && str_contains($html, 'replace this temporary password');
        });
    }

    #[DataProvider('welcomeNotifications')]
    public function test_passwords_with_symbols_reach_the_member_exactly_as_written(string $notificationClass): void
    {
        $password = '*Ab_c*<d>&e\\f_#`g';
        $member = User::factory()->create(['role' => 'member', 'email' => 'symbols@example.com']);

        $member->notifyNow(new $notificationClass($password));

        $email = Mail::mailer()->getSymfonyTransport()->messages()->last()->getOriginalMessage();

        $this->assertStringContainsString(e($password), $email->getHtmlBody());
        $this->assertStringContainsString($password, $email->getTextBody());
    }

    /** @return array<string, array{class-string}> */
    public static function welcomeNotifications(): array
    {
        return [
            'created by admin' => [AccountCreatedByAdmin::class],
            'legacy import' => [LegacyMemberImported::class],
        ];
    }

    public function test_account_is_kept_and_admin_warned_when_the_welcome_email_fails(): void
    {
        Notification::shouldReceive('send')->andThrow(new RuntimeException('SMTP is down'));
        $this->withoutExceptionHandling();

        $this->createUserThroughTheAdminPanel('no.mail@example.com', 'member')
            ->assertNotified('User created, but the email could not be sent');

        $this->assertDatabaseHas('users', ['email' => 'no.mail@example.com']);
    }

    private function createUserThroughTheAdminPanel(string $email, string $role): Testable
    {
        Filament::setCurrentPanel(Filament::getPanel('admin'));

        return Livewire::actingAs(User::factory()->admin()->create())
            ->test(CreateUser::class)
            ->fillForm([
                'name' => 'Created User',
                'email' => $email,
                'password' => 'AdminSetPassword1!',
                'role' => $role,
                'memberProfile' => ['date_of_birth' => '1985-03-14'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();
    }

    public function test_member_with_a_temporary_password_cannot_access_member_data_until_it_is_changed(): void
    {
        $member = User::factory()->create([
            'password' => Hash::make('temporary-password'),
            'must_change_password' => true,
        ]);

        $this->actingAsApi($member)
            ->getJson('/api/me/subscriptions')
            ->assertStatus(423)
            ->assertJsonPath('mustChangePassword', true);

        $this->actingAsApi($member)
            ->putJson('/api/me/password', [
                'current_password' => 'temporary-password',
                'password' => 'NewSecurePassword1!',
                'password_confirmation' => 'NewSecurePassword1!',
            ])
            ->assertOk()
            ->assertJsonPath('user.mustChangePassword', false);

        $this->assertFalse($member->fresh()->must_change_password);
        $this->assertTrue(Hash::check('NewSecurePassword1!', $member->fresh()->password));
    }
}
