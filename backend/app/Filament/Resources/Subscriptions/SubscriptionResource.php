<?php

namespace App\Filament\Resources\Subscriptions;

use App\Filament\Resources\Subscriptions\Pages\ManageSubscriptions;
use App\Models\Subscription;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
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
                Select::make('membership_level_id')
                    ->label('Membership level')
                    ->relationship('membershipLevel', 'name')
                    ->searchable()
                    ->preload(),
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
                        'pending_review' => 'Pending review',
                        'paid' => 'Paid',
                    ])
                    ->default('outstanding')
                    ->required(),
                DateTimePicker::make('paid_at'),
                TextInput::make('reference')->unique(ignoreRecord: true)->maxLength(255),
                TextInput::make('bank_transfer_reference')
                    ->label('Bank transfer reference')
                    ->maxLength(255),
                Textarea::make('review_note')
                    ->label('Review note')
                    ->columnSpanFull(),
            ])
            ->columns(2);
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
                TextColumn::make('membershipLevel.name')->label('Level')->placeholder('—'),
                TextColumn::make('subscription_amount')->label('Subscription')->money('NGN'),
                TextColumn::make('welfare_amount')->label('Welfare')->money('NGN'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending_review' => 'Pending review',
                        default => ucfirst($state),
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'paid' => 'success',
                        'pending_review' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('payment_gateway')->label('Method')->placeholder('—'),
                TextColumn::make('paid_at')->date(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('viewEvidence')
                    ->label('Evidence')
                    ->icon(Heroicon::OutlinedPaperClip)
                    ->url(fn (Subscription $record): ?string => $record->evidence_url)
                    ->openUrlInNewTab()
                    ->visible(fn (Subscription $record): bool => $record->evidence_url !== null),
                Action::make('approve')
                    ->label('Approve')
                    ->icon(Heroicon::OutlinedCheckCircle)
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn (Subscription $record): bool => $record->status === 'pending_review')
                    ->action(function (Subscription $record): void {
                        $record->update(['status' => 'paid', 'paid_at' => now(), 'review_note' => null]);
                        $record->user->activateMembershipIfEligible();

                        Notification::make()->title('Subscription approved')->success()->send();
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->visible(fn (Subscription $record): bool => $record->status === 'pending_review')
                    ->schema([
                        Textarea::make('review_note')
                            ->label('Reason')
                            ->required(),
                    ])
                    ->action(function (Subscription $record, array $data): void {
                        $record->update(['status' => 'outstanding', 'review_note' => $data['review_note']]);

                        Notification::make()->title('Subscription rejected')->warning()->send();
                    }),
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
            'index' => ManageSubscriptions::route('/'),
        ];
    }
}
