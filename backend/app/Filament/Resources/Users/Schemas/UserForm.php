<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
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
                    ->required(fn (string $operation) => $operation === 'create')
                    ->maxLength(255),
                Select::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'member' => 'Member',
                    ])
                    ->default('member')
                    ->required(),
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
                            ->options([
                                'ACA' => 'ACA',
                                'FCA' => 'FCA',
                            ]),
                        Select::make('membership_status')
                            ->options([
                                'pending' => 'Pending',
                                'active' => 'Active',
                                'expired' => 'Expired',
                            ])
                            ->default('pending')
                            ->required(),
                        TextInput::make('phone')
                            ->maxLength(50),
                        DatePicker::make('date_of_birth')
                            ->native(false)
                            ->displayFormat('d M'),
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
