<?php

namespace App\Filament\Clusters\Core\Resources\UnitConversionResource\Pages;

use App\Filament\Clusters\Core\Resources\UnitConversionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUnitConversions extends ListRecords
{
    protected static string $resource = UnitConversionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
