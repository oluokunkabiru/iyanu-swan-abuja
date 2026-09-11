<?php

namespace App\Filament\Resources\ExecutiveMembers;

use App\Filament\Resources\ExecutiveMembers\Pages\ManageExecutiveMembers;
use App\Models\ExecutiveMember;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ExecutiveMemberResource extends Resource
{
    protected static ?string $model = ExecutiveMember::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Site Content';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                SpatieMediaLibraryFileUpload::make('photo')
                    ->collection('photo')
                    ->disk('public')
                    ->image()
                    ->avatar()
                    ->columnSpanFull(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('credential')
                    ->placeholder('ACA, FCA...')
                    ->maxLength(50),
                TextInput::make('position')
                    ->required()
                    ->maxLength(255),
                Textarea::make('bio')
                    ->rows(3)
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->label('Currently serving')
                    ->default(true)
                    ->live(),
                Toggle::make('is_principal')
                    ->label('Principal officer')
                    ->default(false),
                TextInput::make('term_start_year')
                    ->label('Term start year')
                    ->numeric()
                    ->minValue(1950)
                    ->maxValue(2100)
                    ->visible(fn ($get) => ! $get('is_active')),
                TextInput::make('term_end_year')
                    ->label('Term end year')
                    ->numeric()
                    ->minValue(1950)
                    ->maxValue(2100)
                    ->visible(fn ($get) => ! $get('is_active')),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->columns([
                SpatieMediaLibraryImageColumn::make('photo')->collection('photo')->circular(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('position')->searchable(),
                TextColumn::make('credential'),
                IconColumn::make('is_active')->boolean(),
                IconColumn::make('is_principal')->label('Principal')->boolean(),
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
            'index' => ManageExecutiveMembers::route('/'),
        ];
    }
}
