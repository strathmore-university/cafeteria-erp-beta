<?php

namespace App\Filament\Clusters\Production\Resources\MenuResource\Pages;

use App\Filament\Clusters\Production\Resources\MenuResource;
use Filament\Resources\Pages\ListRecords;

class ListMenus extends ListRecords
{
    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //            CreateAction::make(),
        ];
    }
}
