<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Filament\Resources\Users\Schemas\UserForm;
use App\Filament\Resources\Users\Schemas\UserInfolist;
use App\Filament\Resources\Users\Tables\UsersTable;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUsers;

    protected static string|UnitEnum|null $navigationGroup = 'Membership';

    public static function form(Schema $schema): Schema
    {
        return UserForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return UserInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return UsersTable::configure($table);
    }

    public static function retryOfficialMailboxAction(): Action
    {
        return Action::make('retryOfficialMailbox')
            ->label('Retry official mailbox')
            ->icon(Heroicon::OutlinedEnvelope)
            ->color('warning')
            ->requiresConfirmation()
            ->modalDescription('This creates the member’s official mailbox and emails new mailbox credentials.')
            ->authorize('update')
            ->visible(fn (User $record): bool => $record->role === 'member' && blank($record->official_email))
            ->action(function (User $record): void {
                try {
                    $mailbox = $record->retryOfficialMailboxProvisioning();
                    $record->notifyOfficialMailboxProvisioned($mailbox);

                    Notification::make()
                        ->title('Official mailbox created and credentials emailed')
                        ->success()
                        ->send();
                } catch (\RuntimeException $exception) {
                    Notification::make()
                        ->title('Official mailbox could not be created')
                        ->body($exception->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view' => ViewUser::route('/{record}'),
            'edit' => EditUser::route('/{record}/edit'),
        ];
    }
}
