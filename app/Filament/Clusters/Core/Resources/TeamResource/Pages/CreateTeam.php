<?php

namespace App\Filament\Clusters\Core\Resources\TeamResource\Pages;

use App\Filament\Clusters\Core\Resources\TeamResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTeam extends CreateRecord
{
    protected static string $resource = TeamResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
