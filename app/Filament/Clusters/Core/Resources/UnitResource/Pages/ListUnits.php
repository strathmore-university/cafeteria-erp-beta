<?php

namespace App\Filament\Clusters\Core\Resources\UnitResource\Pages;

use App\Filament\Clusters\Core\Resources\UnitResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUnits extends ListRecords
{
    protected static string $resource = UnitResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
