<?php

namespace App\Filament\Resources\ResourceItems;

use App\Filament\Resources\ResourceItems\Pages\ManageResourceItems;
use App\Models\ResourceItem;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ResourceItemResource extends Resource
{
    protected static ?string $model = ResourceItem::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentArrowDown;

    protected static string|UnitEnum|null $navigationGroup = 'Site Content';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')->rows(3)->columnSpanFull(),
                Select::make('category')
                    ->options([
                        'Form' => 'Form',
                        'Guide' => 'Guide',
                        'Policy' => 'Policy',
                        'Template' => 'Template',
                        'Syllabus' => 'Syllabus',
                    ])
                    ->required(),
                Select::make('format')
                    ->options([
                        'PDF' => 'PDF',
                        'DOCX' => 'DOCX',
                        'XLSX' => 'XLSX',
                    ])
                    ->required(),
                SpatieMediaLibraryFileUpload::make('file')
                    ->collection('file')
                    ->disk('public')
                    ->acceptedFileTypes([
                        'application/pdf',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    ])
                    ->maxSize(10_240)
                    ->required()
                    ->openable()
                    ->downloadable()
                    ->columnSpanFull(),
                Toggle::make('is_active')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at')
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('category')->badge(),
                TextColumn::make('format'),
                IconColumn::make('is_active')->boolean(),
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
            'index' => ManageResourceItems::route('/'),
        ];
    }
}
