<?php

namespace App\Notifications;

use App\Models\EventRegistration;
use App\Models\NotificationSetting;
use App\Services\QrCodeGenerator;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * Fires once per registration — right away for a free ticket, or on
 * successful payment verification for a paid one (PaymentProcessor guards
 * against firing twice for the same reference). Not queued: it's always
 * triggered from an interactive request (registering, or the payment
 * callback), and this box has no reliably running queue worker to drain a
 * deferred send.
 */
class EventRegistrationConfirmed extends Notification
{
    public function __construct(private readonly EventRegistration $registration) {}

    /** @return array<int, string> */
    public function via(object $notifiable): array
    {
        return NotificationSetting::current()->resolveChannels('event_notification_channels');
    }

    public function toMail(object $notifiable): MailMessage
    {
        $event = $this->registration->event;
        $ticketType = $this->registration->ticketType;
        $verificationUrl = $this->verificationUrl();

        $mail = (new MailMessage)
            ->subject("You're registered — {$event->title}")
            ->greeting("Hello {$this->registration->name},")
            ->line("Your ticket for \"{$event->title}\" is confirmed.")
            ->line('Ticket: '.$ticketType->label)
            ->line('When: '.$event->starts_at->format('l, j F Y \a\t g:i A'));

        if ($event->location) {
            $mail->line('Where: '.$event->location);
        }

        if ($this->registration->amount > 0) {
            $mail->line('Amount paid: ₦'.number_format($this->registration->amount));
        }

        $mail->line('Show the QR code attached to this email, or the link below, at the door — either one lets us verify your ticket on the spot.')
            ->action('View my ticket', $verificationUrl)
            ->attachData(QrCodeGenerator::png($verificationUrl), 'ticket-qr.png', ['mime' => 'image/png']);

        if ($this->registration->user_id) {
            $mail->line('You can also find this ticket any time from your member dashboard.');
        }

        return $mail->line('See you there!');
    }

    private function verificationUrl(): string
    {
        return rtrim(config('app.frontend_url'), '/').'/tickets/verify/'.$this->registration->reference;
    }

    public function toSms(object $notifiable): string
    {
        $event = $this->registration->event;

        return "You're registered for \"{$event->title}\" on {$event->starts_at->format('j M Y, g:i A')}. See you there!";
    }

    public function toWhatsApp(object $notifiable): string
    {
        return $this->toSms($notifiable);
    }
}
