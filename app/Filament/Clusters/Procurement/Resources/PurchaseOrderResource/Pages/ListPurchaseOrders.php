<?php

namespace App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource\Pages;

use App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPurchaseOrders extends ListRecords
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
