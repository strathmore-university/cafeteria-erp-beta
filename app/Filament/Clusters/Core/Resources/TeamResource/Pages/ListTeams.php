<?php

namespace App\Filament\Clusters\Core\Resources\TeamResource\Pages;

use App\Filament\Clusters\Core\Resources\TeamResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListTeams extends ListRecords
{
    protected static string $resource = TeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
