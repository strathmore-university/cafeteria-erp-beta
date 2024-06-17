<?php

namespace App\Filament\Clusters\Inventory\Resources\StoreResource\Pages;

use App\Filament\Clusters\Inventory\Resources\StoreResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStore extends CreateRecord
{
    protected static string $resource = StoreResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
