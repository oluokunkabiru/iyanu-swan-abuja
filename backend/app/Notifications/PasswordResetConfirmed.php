<?php

namespace App\Notifications;

use App\Models\SiteSetting;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PasswordResetConfirmed extends Notification
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
            ->subject("Your password was reset — {$chapterName}")
            ->greeting("Hello {$notifiable->name},")
            ->line("The password for your {$chapterName} account was just reset.")
            ->line('If you made this change, no further action is needed.')
            ->line('If you did not make this change, use the button below immediately to reset your password again and secure your account. Then contact the chapter office.')
            ->action('Secure my account', rtrim(config('app.frontend_url'), '/').'/forgot-password');
    }
}
