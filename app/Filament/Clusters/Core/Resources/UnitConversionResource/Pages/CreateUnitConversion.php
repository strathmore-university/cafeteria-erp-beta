<?php

namespace App\Filament\Clusters\Core\Resources\UnitConversionResource\Pages;

use App\Filament\Clusters\Core\Resources\UnitConversionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUnitConversion extends CreateRecord
{
    protected static string $resource = UnitConversionResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
