<?php

namespace App\Filament\Resources\ContactMessages;

use App\Filament\Resources\ContactMessages\Pages\ManageContactMessages;
use App\Models\ContactMessage;
use App\Notifications\ContactMessageReplied;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Throwable;
use UnitEnum;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedEnvelope;

    protected static string|UnitEnum|null $navigationGroup = 'Inbox';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')->disabled(),
                TextInput::make('email')->disabled(),
                TextInput::make('phone')->disabled(),
                TextInput::make('subject')->disabled(),
                Textarea::make('message')->disabled()->rows(5)->columnSpanFull(),
                Toggle::make('is_read')->label('Read'),
                Textarea::make('reply_message')
                    ->label('Our reply')
                    ->disabled()
                    ->rows(5)
                    ->columnSpanFull()
                    ->visible(fn (?ContactMessage $record): bool => (bool) $record?->replied_at),
                Placeholder::make('replied_at_display')
                    ->label('Replied')
                    ->content(fn (?ContactMessage $record): ?string => $record?->replied_at?->format('j M Y, g:i A').($record?->repliedBy ? " by {$record->repliedBy->name}" : ''))
                    ->visible(fn (?ContactMessage $record): bool => (bool) $record?->replied_at),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                IconColumn::make('is_read')->boolean()->label('Read'),
                IconColumn::make('replied')->boolean()->label('Replied')->state(fn (ContactMessage $record): bool => (bool) $record->replied_at),
                TextColumn::make('name')->searchable(),
                TextColumn::make('email')->searchable(),
                TextColumn::make('subject')->limit(40),
                TextColumn::make('created_at')->dateTime()->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                Action::make('reply')
                    ->label(fn (ContactMessage $record): string => $record->replied_at ? 'Reply again' : 'Reply')
                    ->icon(Heroicon::OutlinedArrowUturnLeft)
                    ->color('primary')
                    ->schema([
                        Textarea::make('reply_message')
                            ->label('Your reply')
                            ->required()
                            ->rows(6),
                    ])
                    ->action(function (ContactMessage $record, array $data): void {
                        $record->update([
                            'reply_message' => $data['reply_message'],
                            'replied_at' => now(),
                            'replied_by_user_id' => auth()->id(),
                            'is_read' => true,
                        ]);

                        try {
                            $record->notify(new ContactMessageReplied($record));

                            Notification::make()
                                ->title("Reply sent to {$record->email}")
                                ->success()
                                ->send();
                        } catch (Throwable $e) {
                            report($e);

                            Notification::make()
                                ->title('Reply saved, but the email could not be sent')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                EditAction::make()->label('View'),
                DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageContactMessages::route('/'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_read', false)->count() ?: null;
    }
}
