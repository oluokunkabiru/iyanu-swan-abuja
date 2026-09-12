<?php

namespace Tests\Feature;

use App\Filament\Resources\ContactMessages\Pages\ManageContactMessages;
use App\Models\ContactMessage;
use App\Models\User;
use App\Notifications\ContactMessageReplied;
use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Tests\Concerns\UsesMysqlInTransaction;
use Tests\TestCase;

class ContactMessageReplyTest extends TestCase
{
    use UsesMysqlInTransaction;

    public function test_admin_can_reply_to_a_contact_message(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $message = ContactMessage::create([
            'name' => 'Jane Visitor',
            'email' => 'jane.visitor@example.com',
            'subject' => 'A question about membership',
            'message' => 'How do I join?',
        ]);

        Livewire::actingAs($admin)
            ->test(ManageContactMessages::class)
            ->callAction(TestAction::make('reply')->table($message), data: [
                'reply_message' => 'You can register at swanabuja.org/membership/register.',
            ])
            ->assertHasNoActionErrors();

        $message->refresh();

        $this->assertSame('You can register at swanabuja.org/membership/register.', $message->reply_message);
        $this->assertNotNull($message->replied_at);
        $this->assertSame($admin->id, $message->replied_by_user_id);
        $this->assertTrue($message->is_read);

        Notification::assertSentTo($message, ContactMessageReplied::class);
    }

    public function test_reply_message_is_required(): void
    {
        Notification::fake();

        $admin = User::factory()->admin()->create();
        $message = ContactMessage::create([
            'name' => 'Jane Visitor',
            'email' => 'jane.visitor@example.com',
            'message' => 'How do I join?',
        ]);

        Livewire::actingAs($admin)
            ->test(ManageContactMessages::class)
            ->callAction(TestAction::make('reply')->table($message), data: [
                'reply_message' => '',
            ])
            ->assertHasActionErrors(['reply_message']);

        $this->assertNull($message->fresh()->replied_at);
        Notification::assertNothingSent();
    }
}
