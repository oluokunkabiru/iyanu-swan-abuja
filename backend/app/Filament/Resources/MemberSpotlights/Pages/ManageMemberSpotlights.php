<?php

namespace App\Filament\Resources\MemberSpotlights\Pages;

use App\Filament\Resources\MemberSpotlights\MemberSpotlightResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageMemberSpotlights extends ManageRecords
{
    protected static string $resource = MemberSpotlightResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
