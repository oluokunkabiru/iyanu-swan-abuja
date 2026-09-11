<?php

namespace App\Filament\Resources\Users\Schemas;

use App\Models\User;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\RepeatableEntry\TableColumn;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->schema([
                        Grid::make(['default' => 1, 'sm' => 4])
                            ->schema([
                                ImageEntry::make('memberProfile.photo_url')
                                    ->label('')
                                    ->circular()
                                    ->size(96)
                                    ->defaultImageUrl(asset('images/logo.png'))
                                    ->columnSpan(1),
                                Grid::make(1)
                                    ->schema([
                                        TextEntry::make('name')
                                            ->weight('bold')
                                            ->size('lg'),
                                        TextEntry::make('email')
                                            ->icon('heroicon-m-envelope')
                                            ->copyable(),
                                        Grid::make(4)
                                            ->schema([
                                                TextEntry::make('role')->badge(),
                                                TextEntry::make('memberProfile.membership_status')
                                                    ->label('Membership')
                                                    ->badge()
                                                    ->color(fn (?string $state): string => match ($state) {
                                                        'active' => 'success',
                                                        'pending' => 'warning',
                                                        'expired' => 'danger',
                                                        default => 'gray',
                                                    }),
                                                TextEntry::make('memberProfile.membership_number')
                                                    ->label('Membership number')
                                                    ->placeholder('Not yet assigned'),
                                                IconEntry::make('email_verified_at')
                                                    ->label('Email verified')
                                                    ->boolean(),
                                            ]),
                                    ])
                                    ->columnSpan(3),
                            ]),
                    ]),

                Tabs::make('Details')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Member profile')
                            ->icon('heroicon-m-identification')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('personal_email')->label('Personal email')->placeholder('—'),
                                        TextEntry::make('official_email')->label('Official email')->placeholder('—'),
                                        TextEntry::make('notification_email_preference')
                                            ->label('Notification email')
                                            ->placeholder('Site default')
                                            ->formatStateUsing(fn (?string $state): string => match ($state) {
                                                'personal' => 'Personal email only',
                                                'official' => 'Official email only',
                                                'all' => 'All emails on file',
                                                'registered' => 'Registered email only',
                                                default => 'Site default',
                                            }),
                                        TextEntry::make('memberProfile.credential')->label('ICAN level'),
                                        TextEntry::make('memberProfile.phone')->label('WhatsApp telephone')->placeholder('—'),
                                        TextEntry::make('memberProfile.date_of_birth')
                                            ->label('Date of birth')
                                            ->date('d M')
                                            ->placeholder('—'),
                                        TextEntry::make('memberProfile.place_of_work')->label('Place of work')->placeholder('—'),
                                        TextEntry::make('memberProfile.residential_address')
                                            ->label('Residential address')
                                            ->placeholder('—')
                                            ->columnSpanFull(),
                                        TextEntry::make('memberProfile.sector')->label('Sector')->placeholder('—'),
                                        TextEntry::make('memberProfile.specialisation')->label('Specialisation')->placeholder('—'),
                                        TextEntry::make('memberProfile.year_admitted')->label('Year admitted')->placeholder('—'),
                                        TextEntry::make('memberProfile.chapter_role')->label('Chapter role')->placeholder('—'),
                                        TextEntry::make('memberProfile.cpd_target')->label('CPD target (hrs)'),
                                        IconEntry::make('memberProfile.is_directory_listed')
                                            ->label('In public directory')
                                            ->boolean(),
                                        TextEntry::make('memberProfile.joined_at')->label('Joined')->date()->placeholder('—'),
                                        TextEntry::make('created_at')->label('Account created')->dateTime(),
                                    ]),
                            ]),

                        Tab::make('CPD records')
                            ->icon('heroicon-m-academic-cap')
                            ->badge(fn (User $record): int => $record->cpdRecords()->count())
                            ->schema([
                                RepeatableEntry::make('cpdRecords')
                                    ->label('')
                                    ->table([
                                        TableColumn::make('Activity'),
                                        TableColumn::make('Type'),
                                        TableColumn::make('Date'),
                                        TableColumn::make('Hours')->alignEnd(),
                                        TableColumn::make('Verified')->alignCenter(),
                                    ])
                                    ->schema([
                                        TextEntry::make('activity')->hiddenLabel()->weight('medium'),
                                        TextEntry::make('activity_type')->hiddenLabel()->badge(),
                                        TextEntry::make('activity_date')->hiddenLabel()->date(),
                                        TextEntry::make('hours')->hiddenLabel()->suffix(' hrs'),
                                        IconEntry::make('is_verified')->hiddenLabel()->boolean(),
                                    ])
                                    ->placeholder('No CPD records logged yet.'),
                            ]),

                        Tab::make('Subscriptions')
                            ->icon('heroicon-m-banknotes')
                            ->badge(fn (User $record): int => $record->subscriptions()->count())
                            ->schema([
                                RepeatableEntry::make('subscriptions')
                                    ->label('')
                                    ->table([
                                        TableColumn::make('Year'),
                                        TableColumn::make('Subscription')->alignEnd(),
                                        TableColumn::make('Welfare')->alignEnd(),
                                        TableColumn::make('Status'),
                                        TableColumn::make('Paid'),
                                    ])
                                    ->schema([
                                        TextEntry::make('year')->hiddenLabel(),
                                        TextEntry::make('subscription_amount')->hiddenLabel()->money('NGN'),
                                        TextEntry::make('welfare_amount')->hiddenLabel()->money('NGN'),
                                        TextEntry::make('status')
                                            ->hiddenLabel()
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'paid' => 'success',
                                                'outstanding' => 'warning',
                                                default => 'gray',
                                            }),
                                        TextEntry::make('paid_at')->hiddenLabel()->dateTime()->placeholder('—'),
                                    ])
                                    ->placeholder('No subscription records yet.'),
                            ]),

                        Tab::make('Event tickets')
                            ->icon('heroicon-m-ticket')
                            ->badge(fn (User $record): int => $record->eventRegistrations()->count())
                            ->schema([
                                RepeatableEntry::make('eventRegistrations')
                                    ->label('')
                                    ->table([
                                        TableColumn::make('Event'),
                                        TableColumn::make('Tier'),
                                        TableColumn::make('Status'),
                                        TableColumn::make('Amount')->alignEnd(),
                                        TableColumn::make('Issued'),
                                    ])
                                    ->schema([
                                        TextEntry::make('event.title')->hiddenLabel()->weight('medium'),
                                        TextEntry::make('ticketType.label')->hiddenLabel(),
                                        TextEntry::make('payment_status')
                                            ->hiddenLabel()
                                            ->badge()
                                            ->color(fn (string $state): string => match ($state) {
                                                'confirmed', 'paid' => 'success',
                                                'pending' => 'warning',
                                                default => 'gray',
                                            }),
                                        TextEntry::make('amount')->hiddenLabel()->money('NGN'),
                                        TextEntry::make('issued_at')->hiddenLabel()->dateTime()->placeholder('—'),
                                    ])
                                    ->placeholder('No event tickets yet.'),
                            ]),
                    ]),
            ]);
    }
}
