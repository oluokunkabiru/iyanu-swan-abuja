<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\BirthdayGreeting;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('birthdays:send-greetings')]
#[Description('Send a birthday greeting, over whichever channels the admin has enabled, to every active member whose birthday is today')]
class SendBirthdayGreetings extends Command
{
    public function handle(): void
    {
        $today = now();
        $sent = 0;

        User::query()
            ->whereHas('memberProfile', function ($query) use ($today) {
                $query->where('membership_status', 'active')
                    ->whereNotNull('date_of_birth')
                    ->whereMonth('date_of_birth', $today->month)
                    ->whereDay('date_of_birth', $today->day);
            })
            ->with('memberProfile')
            ->chunkById(100, function ($users) use (&$sent): void {
                foreach ($users as $user) {
                    $user->notify(new BirthdayGreeting);
                    $sent++;
                }
            });

        $this->info("Queued birthday greetings for {$sent} member(s).");
    }
}
