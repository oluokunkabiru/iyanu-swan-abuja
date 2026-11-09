<?php

namespace App\Notifications;

use App\Models\SiteSetting;
use App\Notifications\Concerns\ShowsLoginDetails;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LegacyMemberImported extends Notification
{
    use ShowsLoginDetails;

    public function __construct(private readonly string $temporaryPassword) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return string[]
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $chapterName = SiteSetting::current()->short_name ?? SiteSetting::current()->chapter_name;

        return (new MailMessage)
            ->subject("Welcome to {$chapterName} — your member account is ready")
            ->greeting("Welcome, {$notifiable->name}!")
            ->line("We're delighted to welcome you to {$chapterName}. We've added your existing membership, and your account is ready to use.")
            ->line($this->loginDetails($notifiable->email, $this->temporaryPassword))
            ->line('For your security, you will be asked to replace this temporary password with your own personal password as soon as you sign in.')
            ->action('Sign in to your account', rtrim(config('app.frontend_url'), '/').'/login')
            ->line('If you were not expecting this email, please contact the chapter office.');
    }
}
