<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Notifications\AdminBroadcast;
use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use UnitEnum;

class SendBroadcast extends Page
{
    protected string $view = 'filament.pages.send-broadcast';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedMegaphone;

    protected static string|UnitEnum|null $navigationGroup = 'Communications';

    protected static ?string $navigationLabel = 'Send Broadcast';

    protected static ?string $title = 'Send a broadcast';

    /** @var array<string, mixed>|null */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(['audience' => 'all']);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('audience')
                    ->label('Send to')
                    ->options([
                        'all' => 'All members',
                        'active' => 'Active members only',
                        'not_active' => 'Pending or expired members only',
                    ])
                    ->default('all')
                    ->native(false)
                    ->required(),
                TextInput::make('subject')
                    ->required()
                    ->maxLength(255),
                Textarea::make('body')
                    ->label('Message')
                    ->required()
                    ->rows(6)
                    ->columnSpanFull()
                    ->helperText('Sent as plain text — this may also go out by SMS/WhatsApp, depending on the "Broadcasts" channels picked in Notification Settings.'),
            ])
            ->statePath('data');
    }

    private function audience(string $key): Collection
    {
        $query = User::query()->where('role', 'member');

        return match ($key) {
            'active' => $query->whereHas('memberProfile', fn ($q) => $q->where('membership_status', 'active'))->get(),
            'not_active' => $query->whereHas('memberProfile', fn ($q) => $q->where('membership_status', '!=', 'active'))->get(),
            default => $query->get(),
        };
    }

    public function send(): void
    {
        $data = $this->form->getState();

        $recipients = $this->audience($data['audience']);

        if ($recipients->isEmpty()) {
            Notification::make()
                ->title('No matching members to send to')
                ->warning()
                ->send();

            return;
        }

        NotificationFacade::send($recipients, new AdminBroadcast($data['subject'], $data['body']));

        Notification::make()
            ->title("Broadcast queued for {$recipients->count()} member(s)")
            ->success()
            ->send();

        $this->form->fill(['audience' => 'all', 'subject' => null, 'body' => null]);
    }
}
