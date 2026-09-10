<?php

namespace App\Filament\Pages;

use App\Models\NotificationSetting;
use App\Services\Sms\SmsGatewayFactory;
use App\Services\WhatsApp\WhatsAppGatewayFactory;
use BackedEnum;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageNotificationSettings extends Page
{
    protected string $view = 'filament.pages.manage-notification-settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBellAlert;

    protected static ?string $title = 'Notification Settings';

    protected static ?string $navigationLabel = 'Notifications';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(NotificationSetting::current()->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        $channelOptions = [
            'email' => 'Email',
            'sms' => 'SMS',
            'whatsapp' => 'WhatsApp',
        ];

        return $schema
            ->components([
                Section::make('Channels')
                    ->description('Credentials live in .env — this only turns a channel on and, for SMS/WhatsApp, picks which provider is active.')
                    ->schema([
                        Toggle::make('email_enabled')
                            ->label('Email')
                            ->helperText('Uses the mailer already configured for this app.')
                            ->default(true),
                        Toggle::make('sms_enabled')
                            ->label('SMS')
                            ->live(),
                        Select::make('sms_provider')
                            ->label('SMS provider')
                            ->options(array_combine(SmsGatewayFactory::options(), ['Termii', "Africa's Talking"]))
                            ->native(false)
                            ->visible(fn ($get) => $get('sms_enabled'))
                            ->required(fn ($get) => $get('sms_enabled')),
                        Toggle::make('whatsapp_enabled')
                            ->label('WhatsApp')
                            ->live(),
                        Select::make('whatsapp_provider')
                            ->label('WhatsApp provider')
                            ->options(array_combine(WhatsAppGatewayFactory::options(), ['Termii']))
                            ->native(false)
                            ->visible(fn ($get) => $get('whatsapp_enabled'))
                            ->required(fn ($get) => $get('whatsapp_enabled')),
                    ])
                    ->columns(2),
                Section::make('Notification types')
                    ->description('Pick every channel a notification type should go out through — checking more than one sends through all of them. Yearly dues reminders always go by email only and aren\'t configurable here.')
                    ->schema([
                        CheckboxList::make('birthday_channels')
                            ->label('Birthday greetings')
                            ->options($channelOptions)
                            ->columns(3),
                        CheckboxList::make('event_notification_channels')
                            ->label('Event notifications')
                            ->options($channelOptions)
                            ->columns(3),
                        CheckboxList::make('broadcast_channels')
                            ->label('Broadcasts')
                            ->options($channelOptions)
                            ->columns(3),
                        CheckboxList::make('newsletter_channels')
                            ->label('Newsletters')
                            ->options($channelOptions)
                            ->columns(3),
                    ]),
            ])
            ->statePath('data')
            ->model(NotificationSetting::current());
    }

    public function save(): void
    {
        $setting = NotificationSetting::current();
        $setting->update($this->form->getState());

        Notification::make()
            ->title('Notification settings saved')
            ->success()
            ->send();
    }
}
