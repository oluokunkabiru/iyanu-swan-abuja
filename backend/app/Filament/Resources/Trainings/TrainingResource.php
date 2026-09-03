<?php

namespace App\Filament\Resources\Trainings;

use App\Filament\Resources\Trainings\Pages\ManageTrainings;
use App\Models\Training;
use BackedEnum;
use UnitEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TrainingResource extends Resource
{
    protected static ?string $model = Training::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static string|UnitEnum|null $navigationGroup = 'Site Content';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                Select::make('provider')
                    ->options([
                        'SWAN Abuja' => 'SWAN Abuja',
                        'ICAN MPD' => 'ICAN MPD',
                        'Faculty' => 'Faculty',
                    ])
                    ->required(),
                Select::make('delivery_mode')
                    ->options([
                        'Physical' => 'Physical',
                        'Virtual' => 'Virtual',
                        'Hybrid' => 'Hybrid',
                    ])
                    ->required(),
                DatePicker::make('starts_on')->required(),
                TextInput::make('cpd_hours')->numeric()->minValue(0)->required(),
                TextInput::make('fee')->numeric()->minValue(0)->prefix('₦')->required(),
                TextInput::make('member_fee')->numeric()->minValue(0)->prefix('₦')->required(),
                TextInput::make('seats_available')->numeric()->minValue(0)->required(),
                Toggle::make('is_active')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('starts_on')
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('provider')->badge(),
                TextColumn::make('delivery_mode')->label('Mode'),
                TextColumn::make('starts_on')->date()->sortable(),
                TextColumn::make('member_fee')->money('NGN'),
                TextColumn::make('seats_available')->label('Seats'),
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
            'index' => ManageTrainings::route('/'),
        ];
    }
}
use Filament\Tables\Columns\IconColumn;
