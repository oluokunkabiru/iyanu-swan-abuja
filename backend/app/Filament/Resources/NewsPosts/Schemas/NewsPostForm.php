<?php

namespace App\Filament\Resources\NewsPosts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class NewsPostForm
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
                Textarea::make('excerpt')
                    ->rows(2)
                    ->columnSpanFull(),
                Select::make('category')
                    ->options([
                        'Chapter' => 'Chapter',
                        'ICAN' => 'ICAN',
                        'Profession' => 'Profession',
                        'Advocacy' => 'Advocacy',
                    ])
                    ->required(),
                TextInput::make('author')->required()->maxLength(255),
                Repeater::make('body')
                    ->simple(Textarea::make('paragraph')->required())
                    ->columnSpanFull(),
                SpatieMediaLibraryFileUpload::make('cover')
                    ->collection('cover')
                    ->disk('public')
                    ->image()
                    ->columnSpanFull(),
                DateTimePicker::make('published_at')
                    ->default(now()),
                Toggle::make('is_published')
                    ->default(false),
            ]);
    }
}
