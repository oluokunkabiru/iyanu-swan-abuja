<?php

namespace App\Notifications;

use App\Models\NotificationSetting;
use App\Models\SiteSetting;
use App\Notifications\Channels\SmsChannel;
use App\Notifications\Channels\WhatsAppChannel;
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
        $settings = NotificationSetting::current();
        $requested = $settings->birthday_channels ?? [];
        $available = array_intersect($requested, $settings->enabledChannels());

        return array_values(array_filter(array_map(
            fn (string $channel): ?string => match ($channel) {
                'email' => 'mail',
                'sms' => SmsChannel::class,
                'whatsapp' => WhatsAppChannel::class,
                default => null,
            },
            $available,
        )));
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
