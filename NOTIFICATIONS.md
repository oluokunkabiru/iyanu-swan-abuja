# Notifications

Every notification class lives in `backend/app/Notifications`. Each one is either
**scheduled** (a cron entry fires it for every eligible member, on a timer),
**event-triggered** (application code fires it inline, the moment something happens),
or **admin-triggered** (an admin composes and sends it on demand, to a chosen
audience). There is no other mechanism — nothing polls outside these three paths.

## Scheduled

Registered in `backend/routes/console.php` via the `Schedule` facade. Requires
`php artisan schedule:run` to actually be cronned (see "Why some are queued and
others aren't" below).

| Command | Notification | Schedule | Recipients |
|---|---|---|---|
| `dues:send-yearly-reminders` | `YearlyDuesReminder` | `yearlyOn(1, 1, '06:00')` | Every member with an outstanding subscription for the new year |
| `birthdays:send-greetings` | `BirthdayGreeting` | `dailyAt('07:00')` | Every member whose date of birth matches today |

Command classes: `backend/app/Console/Commands/SendYearlyDuesReminders.php`,
`SendBirthdayGreetings.php`.

## Event-triggered

Fired inline, synchronously, from the code path that causes them — no cron involved.

| Notification | Fired from | When |
|---|---|---|
| `VerifyEmail` | `User::sendEmailVerificationNotification()` | Right after registration (Laravel's `MustVerifyEmail` contract calls this automatically) |
| `DuesPaymentConfirmed` | `PaymentProcessor::finalize()`, and `SubscriptionResource`'s admin "Approve" action | The moment a subscription's dues actually settle — online gateway success or an admin approving bank-transfer evidence. Guarded against firing twice for the same payment (webhook + frontend callback can both call `finalize()`) |
| `MembershipActivated` | `User::activateMembershipIfEligible()` | The moment a member has *both* a verified email and paid current-year dues, whichever of the two completes it. Guarded so a later year's renewal (paid while already active) never re-fires it |
| `EventRegistrationConfirmed` | `EventRegistrationController::store()` (free tickets) and `PaymentProcessor::finalize()` (paid tickets) | Right away for a free ticket; on successful payment verification for a paid one. Guarded the same way as `DuesPaymentConfirmed` against a webhook/frontend double-verify. Sent to the `EventRegistration` itself (it's `Notifiable`), not the logged-in user — works the same for guests and members |

`EventRegistrationConfirmed`'s email includes a QR code (an email attachment, `ticket-qr.png`, generated via `App\Services\QrCodeGenerator`) and a "View my ticket" link, both encoding
`{frontend}/tickets/verify/{reference}`. That link is public — no login required, same
trust model as this app's payment-verification links (the reference is an
unguessable bearer token) — and hits `GET /api/tickets/{reference}/verify`
(`EventRegistrationController::verifyTicket()`). The first scan of a paid
ticket marks `checked_in_at`; later scans report "already checked in" with
the original time instead of moving it. An unpaid ticket reports invalid
without checking anyone in.

## Admin-triggered

Composed and sent on demand from an admin page, to a chosen audience — not a fixed
recipient list decided by application state.

| Notification | Admin page | Audience |
|---|---|---|
| `AdminBroadcast` | `/admin/send-broadcast` (`SendBroadcast`) | Admin picks: all members, active members only, or pending/expired members only |
| `ContactMessageReplied` | `/admin/contact-messages`'s "Reply" row action | Whoever submitted that one contact form message |

The admin writes a subject and a plain-text message for a broadcast; it goes out
through whichever channels the "Broadcasts" setting has picked (see Channels below).
A contact-message reply is mail-only (unconditional, like `VerifyEmail`) and includes
a `replyTo` back to the chapter's own contact email (`SiteSetting::email`) so a
follow-up doesn't land on the app's no-reply address. The reply text and who sent it
are saved on the `ContactMessage` itself (`reply_message`, `replied_at`,
`replied_by_user_id`), so replying again overwrites the record of the previous reply
rather than keeping a thread.

## Why some are queued and others aren't

`YearlyDuesReminder`, `BirthdayGreeting`, and `AdminBroadcast` implement
`ShouldQueue`. `VerifyEmail`, `DuesPaymentConfirmed`, `MembershipActivated`, and
`EventRegistrationConfirmed` deliberately do not.

The un-queued ones fire from an interactive HTTP request for a single recipient
(registering, paying dues, registering for an event). This box doesn't reliably run a
queue worker outside `composer run dev` — if one of these were queued and nothing was
draining the `jobs` table, the email would silently never send. The queued ones all
address many recipients at once (the whole roll, or however many an admin selects for
a broadcast) — sending that many synchronously inside one request would time it out,
so they're queued despite the same worker caveat: **they require an actual queue
worker running to be delivered.** On this box today, that means running
`composer run dev` (or `php artisan queue:work`) while a scheduled batch or a
broadcast is expected to go out — otherwise it sits in the `jobs` table until one runs.

If this box ever gets a permanent queue worker (systemd unit, Supervisor, etc.), the
un-queued four can switch to `ShouldQueue` too — nothing else about them needs to
change.

## Channels

Every event-triggered/scheduled notification above sends `mail` only, except
`BirthdayGreeting` and `EventRegistrationConfirmed`, which can also send SMS and
WhatsApp; `AdminBroadcast` can too.

- `App\Notifications\Channels\SmsChannel` → `App\Services\Sms\SmsGatewayFactory`
  (`termii`, `africas_talking`)
- `App\Notifications\Channels\WhatsAppChannel` → `App\Services\WhatsApp\WhatsAppGatewayFactory`
  (`termii`)

Both channels resolve a phone number generically — `$notifiable->memberProfile?->phone`
for a member, falling back to `$notifiable->phone` directly (how a guest
`EventRegistration` holds its own) — no-op if neither resolves, and swallow (log,
don't throw) a gateway failure. One bad number or a provider outage never breaks the
rest of a batch send.

Which channels a given notification type actually uses per recipient is computed by
`NotificationSetting::resolveChannels(string $settingKey)`: the admin's per-type pick
(`birthday_channels`, `event_notification_channels`, or `broadcast_channels`),
narrowed to whichever channels are globally enabled
(`email_enabled`/`sms_enabled`/`whatsapp_enabled`). `VerifyEmail`, `DuesPaymentConfirmed`,
and `MembershipActivated` don't call this at all — they're unconditionally mail.

Which of a member's own email addresses mail goes to (registered / personal /
official / all) is separately decided by `User::routeNotificationForMail()`, which
reads the member's own `notification_email_preference` if set, else the site-wide
`NotificationSetting::member_email_default`. This only applies to notifications sent
to a `User`; `EventRegistrationConfirmed` goes to the email given at checkout instead,
since a ticket confirmation isn't tied to a member's account routing preference.

## Admin config: what's live vs. dead

`/admin/manage-notification-settings` (`ManageNotificationSettings`):

| Setting | Used by |
|---|---|
| `email_enabled`, `sms_enabled`, `whatsapp_enabled` | `NotificationSetting::resolveChannels()`, called by every type below |
| `birthday_channels` | `BirthdayGreeting::via()` |
| `event_notification_channels` | `EventRegistrationConfirmed::via()` |
| `broadcast_channels` | `AdminBroadcast::via()` |
| `member_email_default` | `User::routeNotificationForMail()` |
| `sms_provider`, `whatsapp_provider` | `SmsGatewayFactory` / `WhatsAppGatewayFactory` |
| `newsletter_channels` | **Nothing.** No code reads this. |

`newsletter_channels` is still scaffolding for a feature that was never built — an
admin sending a news post (or a digest of them) to members, as distinct from an
ad-hoc `AdminBroadcast` message. Toggling it today has no effect. If it gets built,
wire its `via()` to `NotificationSetting::resolveChannels('newsletter_channels')` the
same way the others do.

## Testing your mail setup

The "Send test email" button in the header of `/admin/manage-notification-settings`
sends `TestEmail` to any address the admin types in — not tied to a `User`, so it
works even before any real member exists. It reports the currently configured
mailer (`config('mail.default')`) in the email body, and shows a Filament error toast
with the underlying exception message if sending fails (bad SMTP credentials, etc.)
instead of a generic failure. Not queued — the admin expects an immediate answer.

## Gaps

- `MembershipActivated` has no admin control at all: no channel toggle (it's
  unconditionally mail, unlike `BirthdayGreeting`/`EventRegistrationConfirmed`/
  `AdminBroadcast`), and no way to manually resend it to a member who says they never
  got it. `VerifyEmail` and `DuesPaymentConfirmed` are the same — mail-only, no resend.
- No newsletter/digest sender exists — `newsletter_channels` is dead config (see
  above). The general-purpose `AdminBroadcast` tool covers most of what a newsletter
  would, short of a "send this news post" button on `NewsPostResource`.
- No "reject" notice — when an admin rejects a bank-transfer subscription payment
  (`SubscriptionResource`'s "Reject" action), the member isn't emailed; they only find
  out by checking their subscription status/`review_note` on the site.
