<?php

namespace Tests\Feature;

use App\Models\NotificationSetting;
use App\Models\User;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Symfony\Component\Mailer\SentMessage;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class BirthdayGreetingDeliveryTest extends TestCase
{
    use UsesMysqlInTransaction;

    public function test_scheduled_queue_worker_delivers_a_queued_birthday_greeting(): void
    {
        config(['queue.default' => 'database']);

        NotificationSetting::current()->update([
            'email_enabled' => true,
            'birthday_channels' => ['email'],
        ]);

        $member = User::factory()->create(['role' => 'member']);
        $member->memberProfile()->create([
            'membership_status' => 'active',
            'date_of_birth' => now()->subYears(40)->toDateString(),
        ]);

        $queuedJobsBefore = DB::table('jobs')->count();

        Artisan::call('birthdays:send-greetings');

        $this->assertGreaterThan($queuedJobsBefore, DB::table('jobs')->count());
        $this->assertNull($this->sentGreetingTo($member));

        Artisan::call($this->scheduledQueueWorkerCommand());

        $this->assertSame(0, DB::table('jobs')->count());
        $this->assertNotNull($this->sentGreetingTo($member));
    }

    private function scheduledQueueWorkerCommand(): string
    {
        $worker = collect(app(Schedule::class)->events())
            ->first(fn (Event $event): bool => Str::contains((string) $event->command, 'queue:work'));

        $this->assertNotNull($worker, 'No queue worker is scheduled, so queued notifications are never sent.');
        $this->assertStringContainsString('--stop-when-empty', $worker->command);

        return Str::after($worker->command, 'artisan\' ');
    }

    private function sentGreetingTo(User $member): ?SentMessage
    {
        return Mail::mailer()->getSymfonyTransport()->messages()->first(
            fn (SentMessage $message): bool => collect($message->getEnvelope()->getRecipients())
                ->contains(fn ($recipient): bool => $recipient->getAddress() === $member->email)
                && Str::startsWith($message->getOriginalMessage()->getSubject(), 'Happy birthday'),
        );
    }
}
