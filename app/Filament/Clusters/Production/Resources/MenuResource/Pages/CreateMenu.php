<?php

namespace App\Filament\Clusters\Production\Resources\MenuResource\Pages;

use App\Filament\Clusters\Production\Resources\MenuResource;
use Filament\Resources\Pages\CreateRecord;

class CreateMenu extends CreateRecord
{
    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
