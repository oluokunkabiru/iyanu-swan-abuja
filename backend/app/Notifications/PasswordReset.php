<?php

namespace App\Notifications;

use App\Models\SiteSetting;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordReset extends Notification
{
    public function __construct(private readonly string $token) {}

    /** @return string[] */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $chapterName = SiteSetting::current()->short_name ?? SiteSetting::current()->chapter_name;

        return (new MailMessage)
            ->subject("Reset your password — {$chapterName}")
            ->greeting("Hello {$notifiable->name},")
            ->line("We received a request to reset the password for your {$chapterName} account.")
            ->action('Reset my password', $this->resetUrl($notifiable))
            ->line('This link expires in 60 minutes.')
            ->line('If you did not request a password reset, you can safely ignore this email.');
    }

    private function resetUrl(object $notifiable): string
    {
        return rtrim(config('app.frontend_url'), '/').'/reset-password?'.http_build_query([
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);
    }
}
