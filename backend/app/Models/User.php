<?php

namespace App\Models;

use App\Notifications\MembershipActivated;
use App\Notifications\VerifyEmail;
use App\Services\Email\CpanelEmailProvisioner;
use App\Services\Email\ProvisionedMailbox;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Throwable;

#[Fillable(['name', 'email', 'personal_email', 'official_email', 'notification_email_preference', 'password', 'must_change_password', 'role'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, MustVerifyEmail
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->role === 'admin';
    }

    public function memberProfile(): HasOne
    {
        return $this->hasOne(MemberProfile::class);
    }

    public function eventRegistrations(): HasMany
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function cpdRecords(): HasMany
    {
        return $this->hasMany(CpdRecord::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function firms(): HasMany
    {
        return $this->hasMany(Firm::class, 'principal_user_id');
    }

    /**
     * Overrides the trait's default so this sends our own branded
     * notification (frontend-facing link, chapter styling) instead of
     * Laravel's plain default.
     */
    public function sendEmailVerificationNotification(): void
    {
        $this->notify(new VerifyEmail);
    }

    /**
     * Flip the member's profile to active once they've both paid the
     * current year's dues and verified their registered email — either
     * event can be the one that completes this, so both call it. Guards
     * on the profile already being active so a later year's renewal
     * (paid while already active from a prior year) never re-sends the
     * "welcome" email — that only makes sense the first time.
     */
    public function activateMembershipIfEligible(): void
    {
        if (! $this->hasVerifiedEmail()) {
            return;
        }

        $profile = $this->memberProfile;

        if (! $profile || $profile->membership_status === 'active') {
            return;
        }

        $hasPaidCurrentYear = $this->subscriptions()
            ->where('year', now()->year)
            ->where('status', 'paid')
            ->exists();

        if (! $hasPaidCurrentYear) {
            return;
        }

        $profile->update(['membership_status' => 'active']);
        $this->notify(new MembershipActivated($this->provisionOfficialMailbox()));
    }

    /**
     * Best-effort: a cPanel outage, misconfiguration, or naming collision
     * that exhausts every fallback must never block the activation this is
     * called from, so every failure is caught and logged rather than
     * thrown. Skips entirely if the member already has an official email
     * (e.g. a later renewal), cPanel isn't configured on this environment,
     * or an admin has switched it off from Notification Settings.
     */
    private function provisionOfficialMailbox(): ?ProvisionedMailbox
    {
        if (filled($this->official_email)) {
            return null;
        }

        if (! NotificationSetting::current()->cpanel_email_provisioning_enabled) {
            return null;
        }

        $provisioner = app(CpanelEmailProvisioner::class);

        if (! $provisioner->isConfigured()) {
            return null;
        }

        try {
            $mailbox = $provisioner->provisionFor($this);
        } catch (Throwable $e) {
            report($e);

            return null;
        }

        $this->update(['official_email' => $mailbox->address]);

        return $mailbox;
    }

    /**
     * Which of this member's email addresses mail notifications go to —
     * their own preference if they've set one, otherwise the site-wide
     * default from NotificationSetting.
     *
     * @return string[]
     */
    public function routeNotificationForMail(): array
    {
        $preference = $this->notification_email_preference ?: NotificationSetting::current()->member_email_default;

        $addresses = match ($preference) {
            'personal' => [$this->personal_email],
            'official' => [$this->official_email],
            'all' => [$this->email, $this->personal_email, $this->official_email],
            default => [$this->email],
        };

        $addresses = array_values(array_unique(array_filter($addresses)));

        return $addresses !== [] ? $addresses : [$this->email];
    }
}
