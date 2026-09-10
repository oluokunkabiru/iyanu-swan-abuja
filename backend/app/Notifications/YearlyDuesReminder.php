<?php

namespace App\Notifications;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class YearlyDuesReminder extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly Subscription $subscription) {}

    /** @return string[] */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $total = $this->subscription->subscription_amount + $this->subscription->welfare_amount;

        return (new MailMessage)
            ->subject("Your {$this->subscription->year} chapter dues are open")
            ->greeting("Hello {$notifiable->name},")
            ->line("A new subscription year has started. Your {$this->subscription->year} dues are now open:")
            ->line('Subscription: ₦'.number_format($this->subscription->subscription_amount))
            ->line('Welfare levy: ₦'.number_format($this->subscription->welfare_amount))
            ->line('Total due: ₦'.number_format($total))
            ->action('Pay your dues', rtrim(config('app.frontend_url'), '/').'/members/subscription')
            ->line('Paying keeps your member rate on events and your eligibility for committee service.');
    }
}
