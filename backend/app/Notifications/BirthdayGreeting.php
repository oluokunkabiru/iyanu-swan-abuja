<?php

namespace App\Notifications;

use App\Models\NotificationSetting;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BirthdayGreeting extends Notification implements ShouldQueue
{
    use Queueable;

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return NotificationSetting::current()->resolveChannels('birthday_channels');
    }

    public function toMail(object $notifiable): MailMessage
    {
        $chapterName = SiteSetting::current()->chapter_name;

        return (new MailMessage)
            ->subject('Happy birthday from '.$chapterName.'!')
            ->greeting("Happy birthday, {$notifiable->name}!")
            ->line("The entire {$chapterName} chapter wishes you a wonderful year ahead.")
            ->line('Thank you for being part of this community of women in the profession.');
    }

    public function toSms(object $notifiable): string
    {
        return "Happy birthday, {$notifiable->name}! Wishing you a wonderful year ahead — from all of us at ".SiteSetting::current()->chapter_name.'.';
    }

    public function toWhatsApp(object $notifiable): string
    {
        return $this->toSms($notifiable);
    }
}
