<?php

namespace App\Notifications;

use App\Models\SiteSetting;
use App\Services\Email\ProvisionedMailbox;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Fires exactly once — the moment membership_status actually flips to
 * active (User::activateMembershipIfEligible() guards against calling
 * this again on a member who's already active, e.g. a later renewal).
 * Not queued, for the same reason as DuesPaymentConfirmed: no reliable
 * worker on this box to drain a deferred send.
 */
class MembershipActivated extends Notification
{
    /**
     * $officialMailbox is null whenever cPanel provisioning was skipped or
     * failed (see User::provisionOfficialMailbox()) — this notification
     * still sends either way, just without that section.
     */
    public function __construct(private readonly ?ProvisionedMailbox $officialMailbox = null) {}

    /** @return string[] */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $chapterName = SiteSetting::current()->short_name ?? SiteSetting::current()->chapter_name;

        $mail = (new MailMessage)
            ->subject("Welcome to the active roll — {$chapterName}")
            ->greeting("Congratulations, {$notifiable->name}!")
            ->line("Your email is confirmed and your dues are paid — you're now an active member of {$chapterName}.")
            ->line('Active membership unlocks member rates on events, eligibility for committee and executive office, and access to the welfare fund.');

        if ($this->officialMailbox) {
            $mail->line("We've also set up your official {$chapterName} email address:")
                ->line("Address: {$this->officialMailbox->address}")
                ->line("Temporary password: {$this->officialMailbox->password}")
                ->line('Web: select “Open your official mailbox” below, then sign in with the address and temporary password above.')
                ->line('Android phone: in Gmail or your Email app, choose Add account, then Other and IMAP. Enter your full email address and the temporary password above. If it asks for a server, enter '.$this->mailboxHost().' and choose port 993 with SSL/TLS.')
                ->line('If IMAP is not available, choose POP instead and use the same details with port 995 and SSL/TLS.')
                ->line('Change this temporary password immediately after signing in.')
                ->action('Open your official mailbox', $this->mailboxLoginUrl());
        } else {
            $mail->action('Go to your dashboard', rtrim(config('app.frontend_url'), '/').'/members');
        }

        return $mail
            ->line('Welcome aboard.');
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
