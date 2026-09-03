<?php

namespace App\Filament\Resources\CpdRecords\Pages;

use App\Filament\Resources\CpdRecords\CpdRecordResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageCpdRecords extends ManageRecords
{
    protected static string $resource = CpdRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
