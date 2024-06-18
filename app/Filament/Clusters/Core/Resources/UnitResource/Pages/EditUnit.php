<?php

namespace App\Filament\Clusters\Core\Resources\UnitResource\Pages;

use App\Filament\Clusters\Core\Resources\UnitResource;
use Filament\Resources\Pages\EditRecord;

class EditUnit extends EditRecord
{
    protected static string $resource = UnitResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
