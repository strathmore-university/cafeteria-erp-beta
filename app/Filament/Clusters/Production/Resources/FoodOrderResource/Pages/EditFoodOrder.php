<?php

namespace App\Filament\Clusters\Production\Resources\FoodOrderResource\Pages;

use App\Filament\Clusters\Production\Resources\FoodOrderResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditFoodOrder extends EditRecord
{
    protected static string $resource = FoodOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
