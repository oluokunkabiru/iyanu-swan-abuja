<?php

namespace App\Notifications;

use App\Models\SiteSetting;
use App\Models\Subscription;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Fires the moment a subscription actually settles — an online gateway
 * payment or an admin approving bank transfer evidence — regardless of
 * whether that also happens to be what activates the member (that's
 * MembershipActivated's job, sent separately and only once).
 * Deliberately not queued: it's triggered from a real-time flow
 * (payment callback or an admin click), and this box has no reliably
 * running queue worker to drain a deferred send.
 */
class DuesPaymentConfirmed extends Notification
{
    public function __construct(private readonly Subscription $subscription) {}

    /** @return string[] */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $chapterName = SiteSetting::current()->short_name ?? SiteSetting::current()->chapter_name;
        $total = $this->subscription->subscription_amount + $this->subscription->welfare_amount;

        return (new MailMessage)
            ->subject("Payment received — your {$this->subscription->year} dues are confirmed")
            ->greeting("Thank you, {$notifiable->name}!")
            ->line("We've received your {$this->subscription->year} dues in full.")
            ->line('Subscription: ₦'.number_format($this->subscription->subscription_amount))
            ->line('Welfare levy: ₦'.number_format($this->subscription->welfare_amount))
            ->line('Total paid: ₦'.number_format($total))
            ->action('View your dashboard', rtrim(config('app.frontend_url'), '/').'/members/subscription')
            ->line("Thank you for staying current with {$chapterName}.");
    }
}
