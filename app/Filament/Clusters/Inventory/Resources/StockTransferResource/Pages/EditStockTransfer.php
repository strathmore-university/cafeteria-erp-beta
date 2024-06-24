<?php

namespace App\Filament\Clusters\Inventory\Resources\StockTransferResource\Pages;

use App\Filament\Clusters\Inventory\Resources\StockTransferResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStockTransfer extends EditRecord
{
    protected static string $resource = StockTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
