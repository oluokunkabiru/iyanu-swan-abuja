<?php

namespace App\Notifications;

use App\Models\SiteSetting;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * Deliberately NOT queued (unlike BirthdayGreeting/YearlyDuesReminder,
 * which are fired from scheduled batch commands where a queue worker
 * is expected to be running): this fires synchronously from an
 * interactive registration request, and this box doesn't reliably run
 * a queue worker outside `composer run dev`. A queued verification
 * email here would silently never send whenever nothing is draining
 * the queue — worse than the trivial delay of sending it inline.
 */
class VerifyEmail extends BaseVerifyEmail
{
    protected function buildMailMessage($url): MailMessage
    {
        $settings = SiteSetting::current();
        $chapterName = $settings->short_name ?? $settings->chapter_name;

        return (new MailMessage)
            ->subject("Confirm your email — {$chapterName}")
            ->greeting('Welcome to the chapter!')
            ->line("Thanks for registering with {$chapterName}. Confirming your email is the first of the two steps that activate your membership — the other is paying your dues.")
            ->action('Confirm my email', $url)
            ->line('This link expires in 60 minutes.')
            ->line("If you didn't create this account, no further action is required.");
    }

    /**
     * The signature is tied to the exact backend URL Laravel generates
     * here — path and query stay untouched. Only the host the link is
     * shown on changes, from the API to the member-facing site: the
     * frontend page it lands on calls this same backend URL itself to
     * do the actual verifying, so the member never sees a bare API
     * response as their first impression of the chapter.
     */
    protected function verificationUrl($notifiable): string
    {
        $backendUrl = URL::temporarySignedRoute(
            'verification.verify',
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ],
        );

        $frontendPath = Str::after(parse_url($backendUrl, PHP_URL_PATH), '/api');
        $query = parse_url($backendUrl, PHP_URL_QUERY);

        return rtrim(config('app.frontend_url'), '/')."{$frontendPath}?{$query}";
    }
}
