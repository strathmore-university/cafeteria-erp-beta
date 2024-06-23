<?php

namespace App\Filament\Clusters\Production\Resources\CookingShiftResource\Pages;

use App\Filament\Clusters\Production\Resources\CookingShiftResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewCookingShift extends ViewRecord
{
    protected static string $resource = CookingShiftResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view-orders')
                ->url(fn ($record) => $this->ordersUrl($record))
                ->icon('heroicon-o-ticket'),
        ];
    }

    private function ordersUrl($record): string
    {
        return CookingShiftResource::getUrl(
            'orders',
            ['record' => $record]
        );
    }
}
