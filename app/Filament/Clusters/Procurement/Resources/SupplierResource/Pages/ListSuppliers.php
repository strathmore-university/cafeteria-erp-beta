<?php

namespace App\Filament\Clusters\Procurement\Resources\SupplierResource\Pages;

use App\Filament\Clusters\Procurement\Resources\SupplierResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSuppliers extends ListRecords
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
