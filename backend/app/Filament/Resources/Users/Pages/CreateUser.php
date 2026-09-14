<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\MembershipLevel;
use App\Models\User;
use App\Notifications\LegacyMemberImported;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    private ?int $legacyMembershipLevelId = null;

    private ?string $temporaryPassword = null;

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
            return [
                ...$data,
                'must_change_password' => $data['role'] === 'member',
            ];
        }

        $this->temporaryPassword = Str::password(16);

        return [
            ...$data,
            'email_verified_at' => now(),
            'must_change_password' => true,
            'password' => $this->temporaryPassword,
            'role' => 'member',
        ];
    }

    protected function afterCreate(): void
    {
        if (! $this->legacyMembershipLevelId || ! $this->temporaryPassword) {
            return;
        }

        /** @var User $member */
        $member = $this->getRecord();
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
        $member->notify(new LegacyMemberImported($this->temporaryPassword));
    }
}
