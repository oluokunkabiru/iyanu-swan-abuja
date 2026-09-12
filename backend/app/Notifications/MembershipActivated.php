<?php

namespace App\Notifications;

use App\Models\SiteSetting;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Fires exactly once — the moment membership_status actually flips to
 * active (User::activateMembershipIfEligible() guards against calling
 * this again on a member who's already active, e.g. a later renewal).
 * Not queued, for the same reason as DuesPaymentConfirmed: no reliable
 * worker on this box to drain a deferred send.
 */
class MembershipActivated extends Notification
{
    /** @return string[] */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $chapterName = SiteSetting::current()->short_name ?? SiteSetting::current()->chapter_name;

        return (new MailMessage)
            ->subject("Welcome to the active roll — {$chapterName}")
            ->greeting("Congratulations, {$notifiable->name}!")
            ->line("Your email is confirmed and your dues are paid — you're now an active member of {$chapterName}.")
            ->line('Active membership unlocks member rates on events, eligibility for committee and executive office, and access to the welfare fund.')
            ->action('Go to your dashboard', rtrim(config('app.frontend_url'), '/').'/members')
            ->line('Welcome aboard.');
    }
}
