<?php

namespace App\Filament\Resources\Subscriptions;

use App\Filament\Resources\Subscriptions\Pages\ManageSubscriptions;
use App\Models\Subscription;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|UnitEnum|null $navigationGroup = 'Member Records';

    protected static ?string $recordTitleAttribute = 'year';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('year')
                    ->numeric()
                    ->minValue(2000)
                    ->maxValue(2100)
                    ->required(),
                TextInput::make('subscription_amount')->numeric()->minValue(0)->prefix('₦')->required(),
                TextInput::make('welfare_amount')->numeric()->minValue(0)->prefix('₦')->required(),
                Select::make('status')
                    ->options([
                        'outstanding' => 'Outstanding',
                        'paid' => 'Paid',
                    ])
                    ->default('outstanding')
                    ->required(),
                DateTimePicker::make('paid_at'),
                TextInput::make('reference')->unique(ignoreRecord: true)->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('year', 'desc')
            ->recordTitleAttribute('year')
            ->columns([
                TextColumn::make('year')
                    ->sortable(),
                TextColumn::make('user.name')->label('Member')->searchable(),
                TextColumn::make('subscription_amount')->label('Subscription')->money('NGN'),
                TextColumn::make('welfare_amount')->label('Welfare')->money('NGN'),
                TextColumn::make('status')->badge(),
                TextColumn::make('paid_at')->date(),
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
            'index' => ManageSubscriptions::route('/'),
        ];
    }
}
