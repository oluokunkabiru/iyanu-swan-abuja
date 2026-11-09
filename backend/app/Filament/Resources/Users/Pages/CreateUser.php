<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\MembershipLevel;
use App\Models\User;
use App\Notifications\AccountCreatedByAdmin;
use App\Notifications\LegacyMemberImported;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use Throwable;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    private ?int $legacyMembershipLevelId = null;

    private string $initialPassword = '';

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $isLegacyMember = (bool) ($data['is_legacy_member'] ?? false);
        $this->legacyMembershipLevelId = $isLegacyMember ? (int) $data['legacy_membership_level_id'] : null;

        unset($data['is_legacy_member'], $data['legacy_membership_level_id']);

        if (! $isLegacyMember) {
            // $data['password'] is already hashed here; the welcome email needs what the admin typed.
            $this->initialPassword = (string) $this->data['password'];

            return [
                ...$data,
                'must_change_password' => $data['role'] === 'member',
            ];
        }

        $this->initialPassword = Str::password(16);

        return [
            ...$data,
            'email_verified_at' => now(),
            'must_change_password' => true,
            'password' => $this->initialPassword,
            'role' => 'member',
        ];
    }

    protected function afterCreate(): void
    {
        /** @var User $user */
        $user = $this->getRecord();

        if ($this->legacyMembershipLevelId) {
            $this->recordLegacyMembership($user);
        }

        $this->sendWelcomeEmails($user);
    }

    private function recordLegacyMembership(User $member): void
    {
        $level = MembershipLevel::query()->findOrFail($this->legacyMembershipLevelId);

        $member->forceFill(['email_verified_at' => now()])->save();

        $member->memberProfile()->updateOrCreate([], [
            'membership_status' => 'active',
            'joined_at' => $member->memberProfile?->joined_at ?? now()->toDateString(),
        ]);
        $member->subscriptions()->create([
            'membership_level_id' => $level->id,
            'year' => now()->year,
            'subscription_amount' => $level->subscription_amount,
            'welfare_amount' => $level->welfare_amount,
            'status' => 'paid',
            'paid_at' => now(),
            'payment_gateway' => 'legacy_import',
        ]);
    }

    /**
     * The account already exists by now, so a mail outage is reported to
     * the admin rather than thrown: retrying the form would only fail on
     * the email address being taken.
     */
    private function sendWelcomeEmails(User $user): void
    {
        try {
            if ($this->legacyMembershipLevelId) {
                $user->notify(new LegacyMemberImported($this->initialPassword));

                return;
            }

            $user->notify(new AccountCreatedByAdmin($this->initialPassword));
            $user->sendEmailVerificationNotification();
        } catch (Throwable $exception) {
            report($exception);

            Notification::make()
                ->title('User created, but the email could not be sent')
                ->body('Once email is working, ask them to use "Forgot password" on the sign-in page.')
                ->warning()
                ->persistent()
                ->send();
        }
    }
}
