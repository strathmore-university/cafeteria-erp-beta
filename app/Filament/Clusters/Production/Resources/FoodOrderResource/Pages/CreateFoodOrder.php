<?php

namespace App\Filament\Clusters\Production\Resources\FoodOrderResource\Pages;

use App\Filament\Clusters\Production\Resources\FoodOrderResource;
use Filament\Resources\Pages\CreateRecord;

class CreateFoodOrder extends CreateRecord
{
    protected static string $resource = FoodOrderResource::class;
}
