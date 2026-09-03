<?php

namespace App\Filament\Resources\ExecutiveMembers\Pages;

use App\Filament\Resources\ExecutiveMembers\ExecutiveMemberResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageExecutiveMembers extends ManageRecords
{
    protected static string $resource = ExecutiveMemberResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
