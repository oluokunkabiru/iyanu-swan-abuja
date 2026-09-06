<?php

namespace App\Filament\Resources\GalleryImages;

use App\Filament\Resources\GalleryImages\Pages\ManageGalleryImages;
use App\Models\Event;
use App\Models\GalleryImage;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class GalleryImageResource extends Resource
{
    protected static ?string $model = GalleryImage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedPhoto;

    protected static string|UnitEnum|null $navigationGroup = 'Site Content';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                SpatieMediaLibraryFileUpload::make('image')
                    ->collection('image')
                    ->disk('public')
                    ->image()
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('caption')
                    ->maxLength(255),
                TextInput::make('album')
                    ->required()
                    ->maxLength(255),
                TextInput::make('year')
                    ->numeric()
                    ->minValue(2000)
                    ->maxValue(2100)
                    ->required(),
                Select::make('event_id')
                    ->label('Related event')
                    ->options(fn () => Event::query()->pluck('title', 'id'))
                    ->searchable(),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')->collection('image'),
                TextColumn::make('caption'),
                TextColumn::make('album')->searchable(),
                TextColumn::make('year')->sortable(),
                TextColumn::make('event.title')->label('Event'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageGalleryImages::route('/'),
        ];
    }
}
