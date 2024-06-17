<?php

namespace App\Filament\Clusters\Core\Resources\CategoryResource\Pages;

use App\Filament\Clusters\Core\Resources\CategoryResource;
use Filament\Resources\Pages\EditRecord;

class EditCategory extends EditRecord
{
    protected static string $resource = CategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //            DeleteAction::make(),
        ];
    }
}
