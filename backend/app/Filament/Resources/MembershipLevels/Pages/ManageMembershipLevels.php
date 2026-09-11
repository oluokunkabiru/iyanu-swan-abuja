<?php

namespace App\Filament\Resources\MembershipLevels\Pages;

use App\Filament\Resources\MembershipLevels\MembershipLevelResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageMembershipLevels extends ManageRecords
{
    protected static string $resource = MembershipLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
