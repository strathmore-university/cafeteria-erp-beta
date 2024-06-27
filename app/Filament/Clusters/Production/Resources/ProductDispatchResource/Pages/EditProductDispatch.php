<?php

namespace App\Filament\Clusters\Production\Resources\ProductDispatchResource\Pages;

use App\Filament\Clusters\Production\Resources\ProductDispatchResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditProductDispatch extends EditRecord
{
    protected static string $resource = ProductDispatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
