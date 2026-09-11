<?php

namespace App\Console\Commands;

use App\Models\SiteSetting;
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
        $settings = SiteSetting::current();
        $year = now()->year;
        $sent = 0;

        User::query()
            ->whereHas('memberProfile', fn ($query) => $query->where('membership_status', 'active'))
            ->with('memberProfile')
            ->chunkById(100, function ($users) use ($settings, $year, &$sent): void {
                foreach ($users as $user) {
                    $subscription = $user->subscriptions()->firstOrCreate(
                        ['year' => $year],
                        [
                            'subscription_amount' => $settings->membership_subscription_fee ?? 0,
                            'welfare_amount' => $settings->membership_welfare_fee ?? 0,
                            'status' => 'outstanding',
                        ],
                    );

                    $user->notify(new YearlyDuesReminder($subscription));
                    $sent++;
                }
            });

        $this->info("Queued {$year} dues reminders for {$sent} active member(s).");
    }
}
