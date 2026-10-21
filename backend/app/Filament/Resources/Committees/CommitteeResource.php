<?php

namespace App\Filament\Resources\Committees;

use App\Filament\Resources\Committees\Pages\ManageCommittees;
use App\Models\Committee;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Repeater;
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
                TextInput::make('subtitle')
                    ->helperText('Optional line shown under the name, e.g. "Muslim and Christian Thanksgiving".')
                    ->maxLength(255),
                TextInput::make('chair')
                    ->helperText('Leave blank until a chair is appointed.')
                    ->maxLength(255),
                Textarea::make('purpose')->required()->rows(4)->columnSpanFull(),
                Repeater::make('objectives')
                    ->simple(Textarea::make('objective')->required()->rows(2))
                    ->required()
                    ->reorderable()
                    ->addActionLabel('Add objective')
                    ->columnSpanFull(),
                Textarea::make('structure')->label('Structure & membership')->required()->rows(3)->columnSpanFull(),
                Textarea::make('meetings')->required()->rows(2)->columnSpanFull(),
                Textarea::make('reporting')->required()->rows(3)->columnSpanFull(),
                Textarea::make('note')
                    ->helperText('Optional closing provision, shown highlighted at the end of the mandate.')
                    ->rows(2)
                    ->columnSpanFull(),
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
                TextColumn::make('chair')->searchable()->placeholder('Not appointed'),
                TextColumn::make('objectives')
                    ->label('Objectives')
                    ->state(fn (Committee $record): int => count($record->objectives ?? [])),
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
            'index' => ManageCommittees::route('/'),
        ];
    }
}
