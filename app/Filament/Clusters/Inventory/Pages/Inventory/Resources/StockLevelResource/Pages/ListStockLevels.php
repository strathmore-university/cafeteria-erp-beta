<?php

namespace App\Filament\Clusters\Inventory\Pages\Inventory\Resources\StockLevelResource\Pages;

use App\Filament\Clusters\Inventory\Pages\Inventory\Resources\StockLevelResource;
use Filament\Resources\Pages\ListRecords;

class ListStockLevels extends ListRecords
{
    protected static string $resource = StockLevelResource::class;
}
