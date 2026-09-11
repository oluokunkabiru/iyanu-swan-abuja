<?php

namespace App\Filament\Resources\MembershipLevels;

use App\Filament\Resources\MembershipLevels\Pages\ManageMembershipLevels;
use App\Models\MembershipLevel;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class MembershipLevelResource extends Resource
{
    protected static ?string $model = MembershipLevel::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static string|UnitEnum|null $navigationGroup = 'Member Records';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->maxLength(500)
                    ->columnSpanFull(),
                TextInput::make('subscription_amount')->numeric()->minValue(0)->prefix('₦')->required(),
                TextInput::make('welfare_amount')->numeric()->minValue(0)->prefix('₦')->required(),
                TextInput::make('sort_order')
                    ->numeric()
                    ->default(0)
                    ->helperText('Lower numbers appear first when members choose a level.'),
                Toggle::make('is_active')
                    ->label('Offered to members')
                    ->default(true)
                    ->helperText('Turn off to hide this level from new registrations without deleting past records that used it.'),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('subscription_amount')->label('Subscription')->money('NGN'),
                TextColumn::make('welfare_amount')->label('Welfare')->money('NGN'),
                TextColumn::make('total')
                    ->label('Total')
                    ->state(fn (MembershipLevel $record): int => $record->subscription_amount + $record->welfare_amount)
                    ->money('NGN'),
                IconColumn::make('is_active')->label('Active')->boolean(),
                TextColumn::make('sort_order')->label('Order')->sortable(),
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
            'index' => ManageMembershipLevels::route('/'),
        ];
    }
}
