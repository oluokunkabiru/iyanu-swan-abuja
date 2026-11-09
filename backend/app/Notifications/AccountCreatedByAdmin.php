<?php

namespace App\Notifications;

use App\Models\SiteSetting;
use App\Models\User;
use App\Notifications\Concerns\ShowsLoginDetails;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Deliberately NOT queued: it carries a plain-text password, which must
 * not sit in the jobs table waiting for a worker.
 */
class AccountCreatedByAdmin extends Notification
{
    use ShowsLoginDetails;

    public function __construct(private readonly string $password) {}

    /**
     * @return string[]
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        $chapterName = SiteSetting::current()->short_name ?? SiteSetting::current()->chapter_name;
        $mail = (new MailMessage)
            ->subject("Welcome to {$chapterName} — your login details")
            ->greeting("Welcome, {$notifiable->name}!")
            ->line("We're delighted to welcome you to {$chapterName}. The chapter office has created an account for you.");

        if ($notifiable->role === 'admin') {
            return $mail
                ->line($this->loginDetails($notifiable->email, $this->password, 'Password'))
                ->line('We have also sent you a separate email to confirm your address. Please open it and select the link inside.')
                ->action('Sign in to the admin panel', route('filament.admin.auth.login'))
                ->line('If you were not expecting this email, please contact the chapter office.');
        }

        return $mail
            ->line($this->loginDetails($notifiable->email, $this->password))
            ->line('For your security, you will be asked to replace this temporary password with your own personal password as soon as you sign in.')
            ->line('We have also sent you a separate email to confirm your address. To become an active member, please confirm your email and pay this year\'s dues from your member portal.')
            ->action('Sign in to your account', rtrim(config('app.frontend_url'), '/').'/login')
            ->line('If you were not expecting this email, please contact the chapter office.');
    }
}
