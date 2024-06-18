<?php

namespace App\Filament\Clusters\Production\Resources\StationResource\Pages;

use App\Filament\Clusters\Production\Resources\StationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStation extends EditRecord
{
    protected static string $resource = StationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
