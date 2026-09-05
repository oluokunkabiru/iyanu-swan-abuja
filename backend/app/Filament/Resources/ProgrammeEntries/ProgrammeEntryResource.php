<?php

namespace App\Filament\Resources\ProgrammeEntries;

use App\Filament\Resources\ProgrammeEntries\Pages\ManageProgrammeEntries;
use App\Models\ProgrammeEntry;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ProgrammeEntryResource extends Resource
{
    protected static ?string $model = ProgrammeEntry::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Site Content';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('date_label')
                    ->helperText('Human-readable date shown on the website, including date ranges.')
                    ->required()
                    ->maxLength(100),
                DateTimePicker::make('starts_at'),
                DateTimePicker::make('ends_at'),
                TextInput::make('venue')->maxLength(255),
                TextInput::make('href')->maxLength(255),
                TextInput::make('sort_order')->numeric()->minValue(0)->default(0)->required(),
                Toggle::make('is_active')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('date_label')->label('Date'),
                TextColumn::make('venue')->searchable(),
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
            'index' => ManageProgrammeEntries::route('/'),
        ];
    }
}
