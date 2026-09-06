<?php

namespace App\Filament\Pages;

use App\Models\SiteSetting;
use App\Services\Payments\PaymentGatewayFactory;
use BackedEnum;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
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
                            ->disk('public')
                            ->image()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->maxSize(5120)
                            ->columnSpanFull(),
                        TextInput::make('chapter_name')->required()->maxLength(255),
                        TextInput::make('short_name')->required()->maxLength(100),
                        TextInput::make('parent_body')->required()->maxLength(255),
                        TextInput::make('tagline')->maxLength(255),
                        Textarea::make('vision')->rows(3)->columnSpanFull(),
                        Textarea::make('aims')->rows(3)->columnSpanFull(),
                        Repeater::make('mission_items')
                            ->label('Mission statements')
                            ->simple(Textarea::make('mission')->required())
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Chairperson welcome')
                    ->schema([
                        TextInput::make('chairperson_heading')->maxLength(255),
                        Repeater::make('chairperson_message')
                            ->simple(Textarea::make('paragraph')->required())
                            ->columnSpanFull(),
                    ]),
                Section::make('Homepage and membership content')
                    ->schema([
                        Repeater::make('chapter_stats')
                            ->schema([
                                TextInput::make('label')->required(),
                                TextInput::make('value')->required(),
                                TextInput::make('note')->required(),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                        Repeater::make('registration_steps')
                            ->schema([
                                TextInput::make('step')->numeric()->required(),
                                TextInput::make('title')->required(),
                                Textarea::make('description')->required()->columnSpanFull(),
                            ])
                            ->columns(2)
                            ->columnSpanFull(),
                        Repeater::make('member_benefits')
                            ->schema([
                                TextInput::make('title')->required(),
                                Textarea::make('description')->required()->columnSpanFull(),
                            ])
                            ->columnSpanFull(),
                        Repeater::make('aims_objectives')
                            ->label('Aims and objectives')
                            ->simple(Textarea::make('objective')->required())
                            ->columnSpanFull(),
                    ]),
                Section::make('Contact & social')
                    ->schema([
                        TextInput::make('address')->maxLength(255),
                        TextInput::make('phone')->tel()->maxLength(50),
                        TextInput::make('email')->email()->maxLength(255),
                        TextInput::make('facebook_url')->url()->maxLength(255),
                        TextInput::make('instagram_url')->url()->maxLength(255),
                        TextInput::make('twitter_url')->url()->maxLength(255),
                        TextInput::make('linkedin_url')->url()->maxLength(255),
                        TextInput::make('hero_video_url')->url()->maxLength(255),
                        Repeater::make('social_links')
                            ->schema([
                                TextInput::make('label')->required(),
                                Select::make('network')
                                    ->options([
                                        'facebook' => 'Facebook',
                                        'twitter' => 'X / Twitter',
                                        'instagram' => 'Instagram',
                                        'linkedin' => 'LinkedIn',
                                        'youtube' => 'YouTube',
                                    ])
                                    ->required(),
                                TextInput::make('url')->url()->required(),
                            ])
                            ->columns(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
                Section::make('Membership fees (₦)')
                    ->schema([
                        TextInput::make('membership_subscription_fee')->numeric()->required(),
                        TextInput::make('membership_welfare_fee')->numeric()->required(),
                    ])
                    ->columns(2),
                Section::make('Payments')
                    ->description('Both gateways can hold API keys in .env at once — this is only which one is actually used to take payment.')
                    ->schema([
                        Select::make('active_payment_gateway')
                            ->label('Active payment gateway')
                            ->options([
                                'paystack' => 'Paystack',
                                'flutterwave' => 'Flutterwave',
                            ])
                            ->native(false)
                            ->required()
                            ->default(PaymentGatewayFactory::options()[0] ?? 'paystack'),
                    ]),
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
