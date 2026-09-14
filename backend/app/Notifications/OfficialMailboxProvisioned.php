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
            ->line('Android (recommended: IMAP): use your full email address as both the email address and username; use the temporary password above; incoming IMAP server: '.$this->mailboxHost().', port 993, SSL/TLS; outgoing SMTP server: '.$this->mailboxHost().', port 465, SSL/TLS, with authentication required.')
            ->line('Android POP alternative: use the same email address, username, password, and SMTP settings; incoming POP server: '.$this->mailboxHost().', port 995, SSL/TLS.')
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
