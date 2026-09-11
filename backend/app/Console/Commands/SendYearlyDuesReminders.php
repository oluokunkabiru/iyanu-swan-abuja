<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\YearlyDuesReminder;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('dues:send-yearly-reminders')]
#[Description('Email every active member a reminder that this year\'s subscription and welfare dues are open')]
class SendYearlyDuesReminders extends Command
{
    public function handle(): void
    {
        $year = now()->year;
        $sent = 0;
        $skipped = 0;

        User::query()
            ->whereHas('memberProfile', fn ($query) => $query->where('membership_status', 'active'))
            ->with(['memberProfile', 'subscriptions' => fn ($query) => $query->latest('year')->limit(1)])
            ->chunkById(100, function ($users) use ($year, &$sent, &$skipped): void {
                foreach ($users as $user) {
                    $subscription = $user->subscriptions()->firstOrNew(['year' => $year]);

                    if (! $subscription->exists) {
                        // Carry the member's most recent level forward at
                        // its current price — there's no flat fee to fall
                        // back to any more, so a member who has never had
                        // a level (shouldn't happen for an active member,
                        // but not impossible) can't be billed automatically.
                        $previousLevel = $user->subscriptions()->latest('year')->first()?->membershipLevel;

                        if (! $previousLevel) {
                            $skipped++;

                            continue;
                        }

                        $subscription->fill([
                            'membership_level_id' => $previousLevel->id,
                            'subscription_amount' => $previousLevel->subscription_amount,
                            'welfare_amount' => $previousLevel->welfare_amount,
                            'status' => 'outstanding',
                        ])->save();
                    }

                    $user->notify(new YearlyDuesReminder($subscription));
                    $sent++;
                }
            });

        $this->info("Queued {$year} dues reminders for {$sent} active member(s).");

        if ($skipped > 0) {
            $this->warn("Skipped {$skipped} member(s) with no prior membership level to renew at.");
        }
    }
}
