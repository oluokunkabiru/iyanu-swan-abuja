<?php

namespace App\Filament\Resources\JobListings;

use App\Filament\Resources\JobListings\Pages\ManageJobListings;
use App\Models\JobListing;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
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

class JobListingResource extends Resource
{
    protected static ?string $model = JobListing::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBriefcase;

    protected static string|UnitEnum|null $navigationGroup = 'Directories';

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->required()
                    ->maxLength(255),
                TextInput::make('organisation')->required()->maxLength(255),
                TextInput::make('location')->required()->maxLength(255),
                Select::make('employment_type')
                    ->options([
                        'Full-time' => 'Full-time',
                        'Contract' => 'Contract',
                        'Part-time' => 'Part-time',
                    ])
                    ->required(),
                Select::make('seniority_level')
                    ->options([
                        'Entry' => 'Entry',
                        'Mid' => 'Mid',
                        'Senior' => 'Senior',
                        'Executive' => 'Executive',
                    ])
                    ->required(),
                DatePicker::make('posted_at')->required()->default(now()),
                DatePicker::make('closes_at')->required()->afterOrEqual('posted_at'),
                Textarea::make('summary')->required()->rows(4)->columnSpanFull(),
                TextInput::make('application_url')->url()->maxLength(255),
                Toggle::make('is_active')->default(true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('posted_at', 'desc')
            ->recordTitleAttribute('title')
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('organisation')->searchable(),
                TextColumn::make('employment_type')->label('Type')->badge(),
                TextColumn::make('seniority_level')->label('Level'),
                TextColumn::make('closes_at')->date()->sortable(),
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
            'index' => ManageJobListings::route('/'),
        ];
    }
}
