<?php

namespace App\Filament\Clusters\Core\Resources\UnitConversionResource\Pages;

use App\Filament\Clusters\Core\Resources\UnitConversionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditUnitConversion extends EditRecord
{
    protected static string $resource = UnitConversionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
