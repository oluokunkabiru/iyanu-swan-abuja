<?php

namespace App\Filament\Resources\PastChairpersons;

use App\Filament\Resources\PastChairpersons\Pages\ManagePastChairpersons;
use App\Models\ExecutiveMember;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use UnitEnum;

/**
 * The chapter's past chairpersons, shown on the Governance page. Same
 * table as executive members (so photos and permissions are shared),
 * scoped to rows flagged is_past_chairperson.
 */
class PastChairpersonResource extends Resource
{
    protected static ?string $model = ExecutiveMember::class;

    protected static ?string $modelLabel = 'past chairperson';

    protected static ?string $slug = 'past-chairpersons';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTrophy;

    protected static string|UnitEnum|null $navigationGroup = 'Site Content';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('is_past_chairperson', true);
    }

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
                    ->label('Tenure position')
                    ->placeholder('e.g. 1st Chairperson')
                    ->helperText('Use the archive title, such as Founding Mother, Co-ordinator, 1st Chairperson or 8th Chairperson.')
                    ->default('Chairperson')
                    ->required()
                    ->maxLength(255),
                TextInput::make('term_start_year')
                    ->label('Term start year')
                    ->numeric()
                    ->required()
                    ->minValue(1950)
                    ->maxValue(2100),
                TextInput::make('term_end_year')
                    ->label('Term end year')
                    ->numeric()
                    ->minValue(1950)
                    ->maxValue(2100)
                    ->gte('term_start_year'),
                RichEditor::make('bio')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('term_start_year')
            ->columns([
                SpatieMediaLibraryImageColumn::make('photo')->collection('photo')->circular(),
                TextColumn::make('name')->searchable(),
                TextColumn::make('credential'),
                TextColumn::make('position')->label('Tenure position')->searchable(),
                TextColumn::make('term_start_year')->label('From')->sortable(),
                TextColumn::make('term_end_year')->label('To')->placeholder('—')->sortable(),
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
            'index' => ManagePastChairpersons::route('/'),
        ];
    }
}
