# Notifications

Every notification class lives in `backend/app/Notifications`. Each one is either
**scheduled** (a cron entry fires it for every eligible member, on a timer) or
**event-triggered** (application code fires it inline, the moment something happens).
There is no other mechanism — nothing polls, nothing batches outside these two paths.

## Scheduled

Registered in `backend/routes/console.php` via the `Schedule` facade. Requires
`php artisan schedule:run` to actually be cronned (see the "Queueing" note below —
scheduled notifications are also the queued ones, since a worker draining them a few
seconds late doesn't matter).

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

## Why some are queued and others aren't

`YearlyDuesReminder` and `BirthdayGreeting` implement `ShouldQueue`. `VerifyEmail`,
`DuesPaymentConfirmed`, and `MembershipActivated` deliberately do not.

The three event-triggered ones fire from an interactive HTTP request (registering,
paying dues, an admin clicking Approve). This box doesn't reliably run a queue worker
outside `composer run dev` — if one of these were queued and nothing was draining the
`jobs` table, the email would silently never send. The two scheduled ones are safe to
queue because a batch command already expects a worker to be running to process what
it dispatches.

If this box ever gets a permanent queue worker (systemd unit, Supervisor, etc.), the
interactive three can switch to `ShouldQueue` too — nothing else about them needs to
change.

## Channels

Every notification above sends `mail` only, except `BirthdayGreeting`, which can also
send SMS and WhatsApp:

- `App\Notifications\Channels\SmsChannel` → `App\Services\Sms\SmsGatewayFactory`
  (`termii`, `africas_talking`)
- `App\Notifications\Channels\WhatsAppChannel` → `App\Services\WhatsApp\WhatsAppGatewayFactory`
  (`termii`)

Both channels look up the member's phone from their `MemberProfile`, no-op if there
isn't one, and swallow (log, don't throw) a gateway failure — one bad number or a
provider outage never breaks the rest of a batch send.

Which channels `BirthdayGreeting` actually uses per member is computed from
`NotificationSetting`: the admin's `sms_enabled`/`whatsapp_enabled`/`email_enabled`
toggles, intersected with the `birthday_channels` the admin picked. None of the other
four notifications read `NotificationSetting` at all — they're unconditionally mail.

Which of a member's own email addresses mail goes to (registered / personal /
official / all) is separately decided by `User::routeNotificationForMail()`, which
reads the member's own `notification_email_preference` if set, else the site-wide
`NotificationSetting::member_email_default`.

## Admin config: what's live vs. dead

`/admin/manage-notification-settings` (`ManageNotificationSettings`) exposes more
toggles than the code currently reads:

| Setting | Used by |
|---|---|
| `email_enabled`, `sms_enabled`, `whatsapp_enabled` | `BirthdayGreeting::via()` |
| `birthday_channels` | `BirthdayGreeting::via()` |
| `member_email_default` | `User::routeNotificationForMail()` |
| `sms_provider`, `whatsapp_provider` | `SmsGatewayFactory` / `WhatsAppGatewayFactory` |
| `event_notification_channels` | **Nothing.** No code reads this. |
| `broadcast_channels` | **Nothing.** No code reads this. |
| `newsletter_channels` | **Nothing.** No code reads this. |

The last three look like scaffolding for features that were never built — an event
registration confirmation, and a chapter-wide broadcast/newsletter an admin can
trigger. Toggling them today has no effect. If either feature gets built, wire the new
notification's `via()` to the matching setting the same way `BirthdayGreeting` does.

## Gaps

- No notification exists for a successful event ticket registration/payment — a
  member gets no email confirming their ticket.
- No admin-triggered broadcast/newsletter exists — `broadcast_channels` and
  `newsletter_channels` are dead config (see above).
