<?php

namespace App\Filament\Resources\ProgrammeEntries\Pages;

use App\Filament\Resources\ProgrammeEntries\ProgrammeEntryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageProgrammeEntries extends ManageRecords
{
    protected static string $resource = ProgrammeEntryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
