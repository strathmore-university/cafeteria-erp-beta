<?php

namespace App\Filament\Clusters\Core\Resources\SettingResource\Pages;

use App\Filament\Clusters\Core\Resources\SettingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSettings extends ListRecords
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
