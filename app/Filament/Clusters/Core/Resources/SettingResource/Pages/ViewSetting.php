<?php

namespace App\Filament\Clusters\Core\Resources\SettingResource\Pages;

use App\Filament\Clusters\Core\Resources\SettingResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\EditRecord;

class ViewSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
