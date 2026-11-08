<?php

namespace App\Notifications;

use App\Models\NotificationSetting;
use App\Models\SiteSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Symfony\Component\Mime\Email;

class BirthdayGreeting extends Notification implements ShouldQueue
{
    use Queueable;

    private const BANNER_CONTENT_ID = 'birthday-banner';

    private const OLDEST_PLAUSIBLE_AGE = 120;

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return NotificationSetting::current()->resolveChannels('birthday_channels');
    }

    public function toMail(object $notifiable): MailMessage
    {
        $chapterName = SiteSetting::current()->chapter_name;
        $birthday = $this->birthdayPhrase($notifiable);
        $age = $this->celebratedAge($notifiable);

        return (new MailMessage)
            ->subject("🎉 {$birthday} from {$chapterName}!")
            ->greeting("🎂 {$birthday}, {$notifiable->name}!")
            ->line(new HtmlString('<img src="cid:'.self::BANNER_CONTENT_ID.'" alt="Happy Birthday!" width="100%" style="display:block;width:100%;max-width:570px;height:auto;border-radius:6px;margin:0 0 20px;">'))
            ->when($age !== null, fn (MailMessage $mail): MailMessage => $mail->line($this->ageBadge($age)))
            ->line($age !== null
                ? "🎈 Today you are celebrating {$age} wonderful ".Str::plural('year', $age).", and the entire {$chapterName} family celebrates with you. We are so glad you are one of us."
                : "🎈 Today, the entire {$chapterName} family celebrates you, and we are so glad you are one of us.")
            ->line('🌟 Your dedication, your excellence and the example you set continue to inspire the women around you, and our profession is stronger for it.')
            ->line('🙏 May this new year of your life bring you good health, joy, peace and success in everything you set your hands to.')
            ->line('🎁 Take a moment today to rest, to celebrate, and to enjoy the people who love you. You have earned it.')
            ->salutation(new HtmlString('With love and warm wishes,<br>'.e($chapterName)))
            ->withSymfonyMessage(function (Email $message): void {
                $message->embedFromPath(resource_path('images/birthday-banner.png'), self::BANNER_CONTENT_ID, 'image/png');
            });
    }

    public function toSms(object $notifiable): string
    {
        return "{$this->birthdayPhrase($notifiable)}, {$notifiable->name}! Wishing you a wonderful year ahead — from all of us at ".SiteSetting::current()->chapter_name.'.';
    }

    public function toWhatsApp(object $notifiable): string
    {
        return $this->toSms($notifiable);
    }

    private function birthdayPhrase(object $notifiable): string
    {
        $age = $this->celebratedAge($notifiable);

        return $age !== null ? 'Happy '.$this->ordinal($age).' birthday' : 'Happy birthday';
    }

    /**
     * Null when the stored birth year can't be real, so a placeholder date
     * never produces a greeting like "Happy 126th birthday".
     */
    private function celebratedAge(object $notifiable): ?int
    {
        $dateOfBirth = $notifiable->memberProfile?->date_of_birth;
        $age = $dateOfBirth ? now()->year - $dateOfBirth->year : 0;

        return $age >= 1 && $age <= self::OLDEST_PLAUSIBLE_AGE ? $age : null;
    }

    private function ageBadge(int $age): HtmlString
    {
        return new HtmlString('<div style="text-align:center;margin:0 0 20px;"><span style="display:inline-block;background-color:#ffff00;color:#000066;font-size:28px;font-weight:bold;line-height:1.2;padding:12px 28px;border-radius:6px;">🎉 Your '.$this->ordinal($age).' Birthday 🎉</span></div>');
    }

    private function ordinal(int $number): string
    {
        $lastTwoDigits = $number % 100;

        $suffix = match (true) {
            $lastTwoDigits >= 11 && $lastTwoDigits <= 13 => 'th',
            $number % 10 === 1 => 'st',
            $number % 10 === 2 => 'nd',
            $number % 10 === 3 => 'rd',
            default => 'th',
        };

        return $number.$suffix;
    }
}
