<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Account')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email'),
                        TextEntry::make('role')->badge(),
                        TextEntry::make('created_at')->label('Account created')->dateTime(),
                    ])
                    ->columns(3),

                Section::make('Member profile')
                    ->schema([
                        ImageEntry::make('memberProfile.photo_url')
                            ->label('Photo')
                            ->circular()
                            ->columnSpanFull(),
                        TextEntry::make('memberProfile.membership_number')->label('Membership number'),
                        TextEntry::make('memberProfile.credential')->label('Credential'),
                        TextEntry::make('memberProfile.membership_status')->label('Status')->badge(),
                        TextEntry::make('memberProfile.phone')->label('Phone'),
                        TextEntry::make('memberProfile.date_of_birth')->label('Date of birth')->date('d M'),
                        TextEntry::make('memberProfile.sector')->label('Sector'),
                        TextEntry::make('memberProfile.specialisation')->label('Specialisation'),
                        TextEntry::make('memberProfile.year_admitted')->label('Year admitted'),
                        TextEntry::make('memberProfile.chapter_role')->label('Chapter role')->placeholder('—'),
                        TextEntry::make('memberProfile.cpd_target')->label('CPD target (hrs)'),
                        IconEntry::make('memberProfile.is_directory_listed')->label('In public directory')->boolean(),
                        TextEntry::make('memberProfile.joined_at')->label('Joined')->date(),
                    ])
                    ->columns(3),

                Section::make('CPD records')
                    ->schema([
                        RepeatableEntry::make('cpdRecords')
                            ->label('')
                            ->schema([
                                TextEntry::make('activity'),
                                TextEntry::make('activity_type')->label('Type')->badge(),
                                TextEntry::make('activity_date')->label('Date')->date(),
                                TextEntry::make('hours')->suffix(' hrs'),
                                IconEntry::make('is_verified')->label('Verified')->boolean(),
                            ])
                            ->columns(5),
                    ])
                    ->collapsible(),

                Section::make('Subscriptions and welfare')
                    ->schema([
                        RepeatableEntry::make('subscriptions')
                            ->label('')
                            ->schema([
                                TextEntry::make('year'),
                                TextEntry::make('subscription_amount')->label('Subscription')->money('NGN'),
                                TextEntry::make('welfare_amount')->label('Welfare')->money('NGN'),
                                TextEntry::make('status')->badge(),
                                TextEntry::make('paid_at')->label('Paid')->dateTime()->placeholder('—'),
                            ])
                            ->columns(5),
                    ])
                    ->collapsible(),

                Section::make('Event tickets')
                    ->schema([
                        RepeatableEntry::make('eventRegistrations')
                            ->label('')
                            ->schema([
                                TextEntry::make('event.title')->label('Event'),
                                TextEntry::make('ticketType.label')->label('Tier'),
                                TextEntry::make('payment_status')->label('Status')->badge(),
                                TextEntry::make('amount')->money('NGN'),
                                TextEntry::make('issued_at')->label('Issued')->dateTime()->placeholder('—'),
                            ])
                            ->columns(5),
                    ])
                    ->collapsible(),
            ]);
    }
}
