<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use App\Models\SiteSetting;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Fires once, when an admin replies to a contact form submission from
 * /admin/contact-messages. Sent to the ContactMessage itself (it's
 * Notifiable) rather than a User, since whoever wrote in almost never has
 * an account. Not queued: it's triggered from an interactive admin click
 * and this box has no reliably running queue worker to drain a deferred
 * send.
 */
class ContactMessageReplied extends Notification
{
    public function __construct(private readonly ContactMessage $contactMessage) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $settings = SiteSetting::current();
        $chapterName = $settings->short_name ?? $settings->chapter_name;
        $originalSubject = $this->contactMessage->subject ?: 'your message';

        $mail = (new MailMessage)
            ->subject("Re: {$originalSubject} — {$chapterName}")
            ->greeting("Hello {$this->contactMessage->name},")
            ->line("Thanks for reaching out to {$chapterName}. Here's our reply:")
            ->line($this->contactMessage->reply_message);

        if ($settings->email) {
            $mail->replyTo($settings->email, $chapterName)
                ->line("If you have any follow-up questions, just reply to this email or write to {$settings->email}.");
        } else {
            $mail->line('If you have any follow-up questions, just reply to this email.');
        }

        return $mail;
    }
}
