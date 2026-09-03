<?php

namespace App\Filament\Resources\Events\RelationManagers;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TicketTypesRelationManager extends RelationManager
{
    protected static string $relationship = 'ticketTypes';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('label')
                    ->required()
                    ->placeholder('Member Physical, Non-Member Virtual...')
                    ->maxLength(255),
                Select::make('audience')
                    ->options([
                        'member' => 'Member',
                        'non-member' => 'Non-member',
                    ])
                    ->required(),
                Select::make('mode')
                    ->options([
                        'physical' => 'Physical',
                        'virtual' => 'Virtual',
                    ])
                    ->required(),
                TextInput::make('price')
                    ->numeric()
                    ->required()
                    ->prefix('₦'),
                Select::make('currency')
                    ->options(['NGN' => 'NGN', 'USD' => 'USD'])
                    ->default('NGN')
                    ->required(),
                TagsInput::make('includes')->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('label')
            ->columns([
                TextColumn::make('label')->searchable(),
                TextColumn::make('audience')->badge(),
                TextColumn::make('mode')->badge(),
                TextColumn::make('price')->money('NGN', divideBy: 1),
                TextColumn::make('currency'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make(),
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
}
