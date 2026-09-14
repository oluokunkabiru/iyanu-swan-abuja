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
            ->line('Web: select “Open your official mailbox” below, then sign in with the address and temporary password above.')
            ->line('Android phone: in Gmail or your Email app, choose Add account, then Other and IMAP. Enter your full email address and the temporary password above. If it asks for a server, enter '.$this->mailboxHost().' and choose port 993 with SSL/TLS.')
            ->line('If IMAP is not available, choose POP instead and use the same details with port 995 and SSL/TLS.')
            ->line('Change this temporary password immediately after signing in.')
            ->action('Open your official mailbox', $this->mailboxLoginUrl())
            ->line('If you did not expect this email, please contact the chapter office.');
    }

    private function mailboxLoginUrl(): string
    {
        $configuredUrl = config('services.cpanel.webmail_url');

        return filled($configuredUrl)
            ? rtrim((string) $configuredUrl, '/')
            : 'https://'.config('services.cpanel.host').':2096';
    }

    private function mailboxHost(): string
    {
        return (string) config('services.cpanel.host');
    }
}
