<?php

namespace App\Filament\Clusters\Production\Resources\MenuResource\Pages;

use App\Filament\Clusters\Production\Resources\MenuResource;
use App\Models\Production\Menu;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewMenu extends ViewRecord
{
    protected static string $resource = MenuResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
            Action::make('view')->icon('heroicon-o-eye')
                ->url(fn (Menu $record) => get_record_url($record->owner))
                ->label(fn (Menu $record) => $record->ownerName()),
        ];
    }
}
