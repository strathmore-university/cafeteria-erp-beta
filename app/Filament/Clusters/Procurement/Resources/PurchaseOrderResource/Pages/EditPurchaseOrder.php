<?php

namespace App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource\Pages;

use App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource;
use App\Models\Procurement\PurchaseOrder;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPurchaseOrder extends EditRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()->visible(fn (PurchaseOrder $record) => $record->allowEdits()),
            DeleteAction::make()->visible(fn (PurchaseOrder $record) => $record->allowEdits()),
        ];
    }
}
