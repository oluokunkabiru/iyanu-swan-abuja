<?php

namespace App\Filament\Resources\Firms\Pages;

use App\Filament\Resources\Firms\FirmResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageFirms extends ManageRecords
{
    protected static string $resource = FirmResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
