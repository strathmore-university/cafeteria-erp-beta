<?php

namespace App\Filament\Clusters\Procurement\Resources\SupplierResource\Pages;

use App\Filament\Clusters\Procurement\Resources\SupplierResource;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSupplier extends EditRecord
{
    protected static string $resource = SupplierResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            //            todo: deleting
        ];
    }
}
