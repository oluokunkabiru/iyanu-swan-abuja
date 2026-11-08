<?php

namespace Tests\Feature;

use App\Models\MemberProfile;
use App\Models\NotificationSetting;
use App\Models\User;
use App\Notifications\BirthdayGreeting;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\DataProvider;
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
        $this->assertStringContainsString(
            'Happy 40th birthday',
            (string) $this->sentGreetingTo($member)?->getOriginalMessage()->getSubject(),
        );
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
                && Str::contains($message->getOriginalMessage()->getSubject(), 'birthday'),
        );
    }

    #[DataProvider('agesAndOrdinals')]
    public function test_greeting_names_the_birthday_being_celebrated(int $age, string $expectedOrdinal): void
    {
        $mail = (new BirthdayGreeting)->toMail($this->memberBorn(now()->subYears($age)->toDateString()));

        $this->assertStringContainsString("Happy {$expectedOrdinal} birthday", $mail->subject);
        $this->assertStringContainsString("Happy {$expectedOrdinal} birthday", $mail->greeting);
        $this->assertStringContainsString("Your {$expectedOrdinal} Birthday", (string) $mail->render());
        $this->assertStringContainsString("celebrating {$age} wonderful years", (string) $mail->render());
    }

    /** @return array<string, array{int, string}> */
    public static function agesAndOrdinals(): array
    {
        return [
            '21' => [21, '21st'],
            '22' => [22, '22nd'],
            '23' => [23, '23rd'],
            '11' => [11, '11th'],
            '12' => [12, '12th'],
            '13' => [13, '13th'],
            '27' => [27, '27th'],
            '30' => [30, '30th'],
            '40' => [40, '40th'],
            '60' => [60, '60th'],
            '101' => [101, '101st'],
            '111' => [111, '111th'],
        ];
    }

    #[DataProvider('unusableBirthDates')]
    public function test_greeting_omits_the_age_when_the_birth_year_is_unusable(?string $dateOfBirth): void
    {
        $mail = (new BirthdayGreeting)->toMail($this->memberBorn($dateOfBirth));

        $this->assertStringContainsString('Happy birthday', $mail->subject);
        $this->assertStringNotContainsString('wonderful year', (string) $mail->render());
        $this->assertStringNotContainsString('Birthday 🎉', (string) $mail->render());
    }

    /** @return array<string, array{?string}> */
    public static function unusableBirthDates(): array
    {
        return [
            'missing' => [null],
            'placeholder year' => ['1900-01-01'],
        ];
    }

    public function test_sms_greeting_includes_the_age(): void
    {
        $sms = (new BirthdayGreeting)->toSms($this->memberBorn(now()->subYears(52)->toDateString()));

        $this->assertStringContainsString('Happy 52nd birthday', $sms);
    }

    public function test_delivered_email_embeds_the_banner_and_keeps_plain_text_clean(): void
    {
        NotificationSetting::current()->update([
            'email_enabled' => true,
            'birthday_channels' => ['email'],
        ]);

        $member = User::factory()->create(['role' => 'member']);
        $member->memberProfile()->create([
            'membership_status' => 'active',
            'date_of_birth' => now()->subYears(40)->toDateString(),
        ]);

        $member->notifyNow(new BirthdayGreeting);

        $email = $this->sentGreetingTo($member)->getOriginalMessage();
        $attachment = $email->getAttachments()[0];

        $this->assertSame('birthday-banner', $attachment->getName());
        $this->assertStringContainsString('Content-Disposition: inline', $email->toString());
        $this->assertStringContainsString('src="cid:', $email->getHtmlBody());
        $this->assertStringNotContainsString('<img', $email->getTextBody());
    }

    private function memberBorn(?string $dateOfBirth): User
    {
        $member = User::factory()->make(['name' => 'Test Member']);
        $member->setRelation('memberProfile', new MemberProfile(['date_of_birth' => $dateOfBirth]));

        return $member;
    }
}
