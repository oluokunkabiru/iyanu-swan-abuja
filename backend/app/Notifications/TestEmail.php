<?php

namespace App\Notifications;

use App\Models\SiteSetting;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Sent on demand from the admin's "Send test email" action on Notification
 * Settings — exists purely to confirm the mailer is actually configured and
 * reaching an inbox, not to be triggered by any application event. Not
 * queued: the admin clicking the button expects to know right away whether
 * it went out or failed.
 */
class TestEmail extends Notification
{
    public function __construct(private readonly string $sentBy) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $chapterName = SiteSetting::current()->short_name ?? SiteSetting::current()->chapter_name;

        return (new MailMessage)
            ->subject("Test email — {$chapterName} admin panel")
            ->greeting('This is a test email.')
            ->line("If you're reading this, outgoing mail from the {$chapterName} admin panel is working.")
            ->line('Mailer: '.config('mail.default'))
            ->line("Sent from Notification Settings by {$this->sentBy} at ".now()->format('j M Y, g:i A').'.');
    }
}
