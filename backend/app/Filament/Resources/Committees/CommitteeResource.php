<?php

namespace App\Filament\Resources\Committees;

use App\Filament\Resources\Committees\Pages\ManageCommittees;
use App\Models\Committee;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use UnitEnum;

class CommitteeResource extends Resource
{
    protected static ?string $model = Committee::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static string|UnitEnum|null $navigationGroup = 'Site Content';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state)))
                    ->maxLength(255),
                TextInput::make('slug')->required()->unique(ignoreRecord: true)->maxLength(255),
                Textarea::make('remit')->required()->rows(4)->columnSpanFull(),
                TextInput::make('chair')->required()->maxLength(255),
                TextInput::make('meeting_cadence')->required()->maxLength(100),
                TagsInput::make('focus_areas')->required()->columnSpanFull(),
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
                    ->searchable(),
                TextColumn::make('chair')->searchable(),
                TextColumn::make('meeting_cadence')->label('Meetings'),
                TextColumn::make('sort_order')->label('Order')->sortable(),
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
            'index' => ManageCommittees::route('/'),
        ];
    }
}
