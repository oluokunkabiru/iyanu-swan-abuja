<?php

namespace App\Notifications;

use App\Models\NotificationSetting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * An admin-composed message sent to a chosen set of members from the
 * "Send Broadcast" admin page. Queued, unlike the interactive
 * notifications elsewhere in this app: this one can go out to the entire
 * membership roll at once, and sending hundreds of emails synchronously
 * inside the admin's own request would time it out. Requires a queue
 * worker to actually be running to deliver — same as the other batch
 * sends (BirthdayGreeting, YearlyDuesReminder).
 */
class AdminBroadcast extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(private readonly string $subject, private readonly string $body) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return NotificationSetting::current()->resolveChannels('broadcast_channels');
    }

    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->subject($this->subject)
            ->greeting("Hello {$notifiable->name},");

        foreach (preg_split('/\n{2,}/', trim($this->body)) as $paragraph) {
            $mail->line(trim($paragraph));
        }

        return $mail;
    }

    public function toSms(object $notifiable): string
    {
        return "{$this->subject}\n\n{$this->body}";
    }

    public function toWhatsApp(object $notifiable): string
    {
        return $this->toSms($notifiable);
    }
}
