<?php

namespace App\Filament\Clusters\Production\Resources\RestaurantResource\Pages;

use App\Filament\Clusters\Production\Resources\RestaurantResource;
use Filament\Resources\Pages\CreateRecord;

class CreateRestaurant extends CreateRecord
{
    protected static string $resource = RestaurantResource::class;
}
