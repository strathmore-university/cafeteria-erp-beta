<?php

namespace App\Filament\Clusters\Inventory\Pages\Inventory\Resources\StoreResource\Pages;

use App\Filament\Clusters\Inventory\Pages\Inventory\Resources\StoreResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStores extends ListRecords
{
    protected static string $resource = StoreResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()]; // todo: creating stores against the owners. not from here
    }
}
