<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\MembershipLevel;
use App\Services\PasswordPolicy;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                TextInput::make('password')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => filled($state) ? bcrypt($state) : null)
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $operation, $get): bool => $operation === 'create' && ! $get('is_legacy_member'))
                    ->rules(fn (string $operation, $get): array => app(PasswordPolicy::class)->rules(
                        required: $operation === 'create' && ! $get('is_legacy_member'),
                    ))
                    ->hidden(fn ($get): bool => (bool) $get('is_legacy_member'))
                    ->helperText('Members created here must change this password on their first sign-in. Legacy-member imports receive a generated temporary password by email.')
                    ->maxLength(255),
                Select::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'member' => 'Member',
                    ])
                    ->default('member')
                    ->required()
                    ->live(),
                Toggle::make('is_legacy_member')
                    ->label('Import an existing member')
                    ->helperText('Marks the member active with this year\'s dues already paid, then emails a temporary password that must be changed at first sign-in.')
                    ->visible(fn ($get): bool => $get('role') === 'member')
                    ->live(),
                Select::make('legacy_membership_level_id')
                    ->label('Paid membership level')
                    ->options(fn (): array => MembershipLevel::active()->orderBy('sort_order')->pluck('name', 'id')->all())
                    ->native(false)
                    ->required(fn ($get): bool => (bool) $get('is_legacy_member'))
                    ->visible(fn ($get): bool => (bool) $get('is_legacy_member'))
                    ->helperText('Creates a paid subscription for the current year without using a payment gateway.'),
                Select::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->label('Admin panel roles')
                    ->helperText('What this admin can actually do once signed in — separate from the Admin/Member toggle above, which only controls whether they can sign in to the panel at all.')
                    ->visible(fn ($get) => $get('role') === 'admin'),
                Fieldset::make('Member profile')
                    ->relationship('memberProfile')
                    ->schema([
                        SpatieMediaLibraryFileUpload::make('photo')
                            ->collection('photo')
                            ->disk('public')
                            ->image()
                            ->avatar()
                            ->columnSpanFull(),
                        TextInput::make('membership_number')
                            ->maxLength(50),
                        Select::make('credential')
                            ->label('ICAN level')
                            ->options(fn (): array => MembershipLevel::query()->orderBy('sort_order')->pluck('name', 'name')->all())
                            ->native(false),
                        Select::make('membership_status')
                            ->options([
                                'pending' => 'Pending',
                                'active' => 'Active',
                                'expired' => 'Expired',
                            ])
                            ->default('pending')
                            ->required(),
                        TextInput::make('phone')
                            ->label('WhatsApp telephone')
                            ->maxLength(50),
                        DatePicker::make('date_of_birth')
                            ->native(false)
                            ->displayFormat('d M'),
                        Textarea::make('residential_address')
                            ->columnSpanFull(),
                        TextInput::make('place_of_work')
                            ->maxLength(255),
                        Select::make('sector')
                            ->options([
                                'Public practice' => 'Public practice',
                                'Public sector' => 'Public sector',
                                'Financial services' => 'Financial services',
                                'Industry' => 'Industry',
                                'Academia' => 'Academia',
                                'Consulting' => 'Consulting',
                            ]),
                        TextInput::make('specialisation')->maxLength(255),
                        TextInput::make('year_admitted')
                            ->numeric()
                            ->minValue(1950)
                            ->maxValue(2100),
                        TextInput::make('chapter_role')->maxLength(255),
                        TextInput::make('cpd_target')
                            ->numeric()
                            ->minValue(0)
                            ->default(120),
                        Toggle::make('is_directory_listed')
                            ->label('Show in public directory')
                            ->default(true),
                        DatePicker::make('joined_at'),
                    ])
                    ->columns(2),
            ]);
    }
}
