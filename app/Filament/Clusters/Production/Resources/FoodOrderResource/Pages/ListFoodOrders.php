<?php

namespace App\Filament\Clusters\Production\Resources\FoodOrderResource\Pages;

use App\Filament\Clusters\Production\Resources\FoodOrderResource;
use Filament\Resources\Pages\ListRecords;

class ListFoodOrders extends ListRecords
{
    protected static string $resource = FoodOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //            CreateAction::make(),
        ];
    }
}
