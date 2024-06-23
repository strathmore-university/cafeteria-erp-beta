<?php

namespace App\Filament\Clusters\Inventory\Pages\Inventory\Resources\StockTakeResource\Pages;

use App\Filament\Clusters\Inventory\Pages\Inventory\Resources\StockTakeResource;
use Filament\Resources\Pages\ListRecords;

class ListStockTakes extends ListRecords
{
    protected static string $resource = StockTakeResource::class;
}
