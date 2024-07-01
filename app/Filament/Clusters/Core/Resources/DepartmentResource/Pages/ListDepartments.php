<?php

namespace App\Filament\Clusters\Core\Resources\DepartmentResource\Pages;

use App\Filament\Clusters\Core\Resources\DepartmentResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDepartments extends ListRecords
{
    protected static string $resource = DepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('sync')->color('gray')
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation() // todo:
//                ->action(fn () => $this->synchronize())->label('Sync from PnC'),
        ];
    }
}
