<?php

namespace App\Notifications;

use App\Models\SiteSetting;
use App\Services\Email\ProvisionedMailbox;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OfficialMailboxProvisioned extends Notification
{
    public function __construct(private readonly ProvisionedMailbox $mailbox) {}

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
            ->subject("Your official {$chapterName} mailbox is ready")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your official {$chapterName} mailbox has been created.")
            ->line("Address: {$this->mailbox->address}")
            ->line("Temporary password: {$this->mailbox->password}")
            ->line('Please sign in to the mailbox and change this password immediately.')
            ->line('If you did not expect this email, please contact the chapter office.');
    }
}
