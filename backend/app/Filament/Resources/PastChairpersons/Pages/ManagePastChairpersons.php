<?php

namespace App\Filament\Resources\PastChairpersons\Pages;

use App\Filament\Resources\PastChairpersons\PastChairpersonResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManagePastChairpersons extends ManageRecords
{
    protected static string $resource = PastChairpersonResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateDataUsing(fn (array $data): array => $data + [
                    'position' => 'Chairperson',
                    'is_past_chairperson' => true,
                    'is_active' => false,
                    'is_principal' => false,
                ]),
        ];
    }
}
