<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use BackedEnum;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;

class ManageSiteSettings extends Page
{
    protected string $view = 'filament.pages.manage-site-settings';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $title = 'Site Settings';

    protected static ?string $navigationLabel = 'Settings';

    /**
     * @var array<string, mixed>|null
     */
    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill(SiteSetting::current()->attributesToArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Chapter identity')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('logo')
                            ->collection('logo')
                            ->image()
                            ->columnSpanFull(),
                        TextInput::make('chapter_name')->required()->maxLength(255),
                        TextInput::make('tagline')->maxLength(255),
                        Textarea::make('mission')->rows(3)->columnSpanFull(),
                        Textarea::make('vision')->rows(3)->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Contact & social')
                    ->schema([
                        TextInput::make('address')->maxLength(255),
                        TextInput::make('phone')->tel()->maxLength(50),
                        TextInput::make('email')->email()->maxLength(255),
                        TextInput::make('facebook_url')->url()->maxLength(255),
                        TextInput::make('instagram_url')->url()->maxLength(255),
                        TextInput::make('twitter_url')->url()->maxLength(255),
                        TextInput::make('hero_video_url')->url()->maxLength(255),
                    ])
                    ->columns(2),
                Section::make('Membership fees (₦)')
                    ->schema([
                        TextInput::make('membership_subscription_fee')->numeric()->required(),
                        TextInput::make('membership_welfare_fee')->numeric()->required(),
                    ])
                    ->columns(2),
            ])
            ->statePath('data')
            ->model(SiteSetting::current());
    }

    public function save(): void
    {
        $setting = SiteSetting::current();
        $setting->update($this->form->getState());

        Notification::make()
            ->title('Settings saved')
            ->success()
            ->send();
    }
}
