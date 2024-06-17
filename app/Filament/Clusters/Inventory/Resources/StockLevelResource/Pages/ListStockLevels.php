<?php

namespace App\Filament\Clusters\Inventory\Resources\StockLevelResource\Pages;

use App\Filament\Clusters\Inventory\Resources\StockLevelResource;
use Filament\Resources\Pages\ListRecords;

class ListStockLevels extends ListRecords
{
    protected static string $resource = StockLevelResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
