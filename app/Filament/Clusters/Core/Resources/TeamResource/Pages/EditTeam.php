<?php

namespace App\Filament\Clusters\Core\Resources\TeamResource\Pages;

use App\Filament\Clusters\Core\Resources\TeamResource;
use Filament\Resources\Pages\EditRecord;

class EditTeam extends EditRecord
{
    protected static string $resource = TeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //            DeleteAction::make(),
            //            ForceDeleteAction::make(),
            //            RestoreAction::make(),
        ];
    }
}
