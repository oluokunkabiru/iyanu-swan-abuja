<?php

namespace App\Filament\Resources\CpdRecords;

use App\Filament\Resources\CpdRecords\Pages\ManageCpdRecords;
use App\Models\CpdRecord;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class CpdRecordResource extends Resource
{
    protected static ?string $model = CpdRecord::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClock;

    protected static string|UnitEnum|null $navigationGroup = 'Member Records';

    protected static ?string $recordTitleAttribute = 'activity';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('event_id')
                    ->relationship('event', 'title')
                    ->searchable()
                    ->preload(),
                TextInput::make('activity')
                    ->required()
                    ->maxLength(255),
                DatePicker::make('activity_date')->required(),
                TextInput::make('hours')->numeric()->minValue(0.25)->step(0.25)->required(),
                Select::make('activity_type')
                    ->options([
                        'Structured' => 'Structured',
                        'Unstructured' => 'Unstructured',
                    ])
                    ->required(),
                Toggle::make('is_verified')->label('Verified'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('activity_date', 'desc')
            ->recordTitleAttribute('activity')
            ->columns([
                TextColumn::make('activity')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('user.name')->label('Member')->searchable(),
                TextColumn::make('activity_date')->date()->sortable(),
                TextColumn::make('hours')->numeric(decimalPlaces: 2),
                TextColumn::make('activity_type')->label('Type')->badge(),
                IconColumn::make('is_verified')->label('Verified')->boolean(),
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
            'index' => ManageCpdRecords::route('/'),
        ];
    }
}
