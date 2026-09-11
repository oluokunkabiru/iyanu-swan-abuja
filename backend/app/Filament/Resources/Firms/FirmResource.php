<?php

namespace App\Filament\Resources\Firms;

use App\Filament\Resources\Firms\Pages\ManageFirms;
use App\Models\Firm;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class FirmResource extends Resource
{
    protected static ?string $model = Firm::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBuildingOffice2;

    protected static string|UnitEnum|null $navigationGroup = 'Directories';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Select::make('principal_user_id')
                    ->label('Linked member')
                    ->relationship('principalUser', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('principal')->required()->maxLength(255),
                TextInput::make('licence_number')->required()->unique(ignoreRecord: true)->maxLength(100),
                TagsInput::make('services')->required()->columnSpanFull(),
                TextInput::make('area')->required()->maxLength(255),
                Select::make('licence_status')
                    ->options([
                        'Active' => 'Active',
                        'Renewal due' => 'Renewal due',
                    ])
                    ->required(),
                Toggle::make('is_active')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name')
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('principal')->searchable(),
                TextColumn::make('licence_number')->label('Licence')->searchable(),
                TextColumn::make('area'),
                TextColumn::make('licence_status')->badge(),
                IconColumn::make('is_active')->boolean(),
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
            'index' => ManageFirms::route('/'),
        ];
    }
}
