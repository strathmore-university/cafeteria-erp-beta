<?php

namespace App\Filament\Clusters\Production\Resources\RestaurantResource\Pages;

use App\Filament\Clusters\Production\Resources\RestaurantResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditRestaurant extends EditRecord
{
    protected static string $resource = RestaurantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
