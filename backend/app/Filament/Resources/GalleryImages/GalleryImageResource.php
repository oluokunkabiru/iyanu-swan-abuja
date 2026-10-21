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
use Filament\Actions\ViewAction;
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
                    ->label('Photo or video')
                    ->helperText('JPG, PNG or WebP photo, or MP4, WebM or MOV video up to 100 MB.')
                    ->collection('image')
                    ->disk('public')
                    ->acceptedFileTypes([...GalleryImage::IMAGE_MIME_TYPES, ...GalleryImage::VIDEO_MIME_TYPES])
                    ->maxSize(GalleryImage::MAX_UPLOAD_KB)
                    ->required()
                    ->columnSpanFull(),
                TextInput::make('caption')
                    ->maxLength(255),
                Select::make('event_id')
                    ->label('Event')
                    ->options(fn () => Event::query()->orderByDesc('starts_at')->pluck('title', 'id'))
                    ->searchable()
                    ->placeholder('General (no event)')
                    ->helperText('The gallery is organised by event — this appears in that event\'s album. Leave empty to post it under General.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')->collection('image')
                    ->defaultImageUrl(asset('images/logo.png')),
                TextColumn::make('media_type')
                    ->label('Type')
                    ->badge()
                    ->state(fn (GalleryImage $record): string => $record->is_video ? 'Video' : 'Photo'),
                TextColumn::make('caption'),
                TextColumn::make('event.title')->label('Event')->placeholder('General')->searchable(),
                TextColumn::make('event.category')->label('Event type')->badge(),
                TextColumn::make('album')->label('Old album')->placeholder('—')->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
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
