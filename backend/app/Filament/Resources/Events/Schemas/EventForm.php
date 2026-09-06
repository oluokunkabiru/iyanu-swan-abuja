<?php

namespace App\Filament\Resources\Events\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class EventForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->live(onBlur: true)
                    ->maxLength(255)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                Textarea::make('summary')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
                Repeater::make('body')
                    ->label('Event details')
                    ->simple(Textarea::make('paragraph')->required())
                    ->columnSpanFull(),
                Select::make('category')
                    ->options([
                        'Seminar' => 'Seminar',
                        'Outreach' => 'Outreach',
                        'Training' => 'Training',
                        'Meeting' => 'Meeting',
                        'Conference' => 'Conference',
                    ])
                    ->required(),
                SpatieMediaLibraryFileUpload::make('cover')
                    ->collection('cover')
                    ->disk('public')
                    ->image()
                    ->columnSpanFull(),
                TextInput::make('location')
                    ->maxLength(255),
                TextInput::make('video_url')
                    ->url()
                    ->maxLength(255),
                DateTimePicker::make('starts_at')
                    ->required(),
                DateTimePicker::make('ends_at'),
                TextInput::make('cpd_hours')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),
                Repeater::make('speakers')
                    ->schema([
                        TextInput::make('name')->required(),
                        TextInput::make('role')->required(),
                    ])
                    ->columns(2)
                    ->columnSpanFull(),
                Select::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                    ])
                    ->default('draft')
                    ->required(),
                Toggle::make('is_featured'),
            ]);
    }
}
