<?php

namespace App\Filament\Clusters\Production\Resources\StationResource\Pages;

use App\Filament\Clusters\Production\Resources\StationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStations extends ListRecords
{
    protected static string $resource = StationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
