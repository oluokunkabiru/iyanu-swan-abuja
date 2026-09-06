<?php

namespace App\Filament\Resources\Sliders\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class SliderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('badge')
                    ->maxLength(120),
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->rows(3)
                    ->columnSpanFull(),
                TextInput::make('cta_label')
                    ->label('Primary button label')
                    ->maxLength(80),
                TextInput::make('cta_link')
                    ->label('Primary button link')
                    ->maxLength(255),
                TextInput::make('secondary_cta_label')
                    ->label('Secondary button label')
                    ->maxLength(80),
                TextInput::make('secondary_cta_link')
                    ->label('Secondary button link')
                    ->maxLength(255),
                SpatieMediaLibraryFileUpload::make('image')
                    ->collection('image')
                    ->image()
                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                    ->maxSize(5120)
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('image_alt')
                    ->label('Image description')
                    ->helperText('Describe the image for visitors using a screen reader.')
                    ->required()
                    ->maxLength(255),
                Select::make('image_position')
                    ->options([
                        'center' => 'Centre',
                        'top' => 'Top',
                        'bottom' => 'Bottom',
                    ])
                    ->default('center')
                    ->required(),
                Toggle::make('is_active')
                    ->default(true),
            ]);
    }
}
