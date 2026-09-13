<?php

namespace App\Notifications;

use App\Models\SiteSetting;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LegacyMemberImported extends Notification
{
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
            ->subject("Your {$chapterName} member account is ready")
            ->greeting("Hello {$notifiable->name},")
            ->line("We've added your existing membership to {$chapterName}.")
            ->line("Sign in with your email address and this temporary password: {$this->temporaryPassword}")
            ->line('For your security, you will be required to choose a new password immediately after signing in.')
            ->action('Sign in to your account', rtrim(config('app.frontend_url'), '/').'/login')
            ->line('If you were not expecting this email, please contact the chapter office.');
    }
}
