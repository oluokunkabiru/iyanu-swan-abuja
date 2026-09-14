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
                ->line('Use the button below to sign in to your official mailbox, then change this password as soon as possible.')
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
}
