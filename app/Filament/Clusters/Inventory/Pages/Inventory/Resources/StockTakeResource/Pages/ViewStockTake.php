<?php

namespace App\Filament\Clusters\Inventory\Pages\Inventory\Resources\StockTakeResource\Pages;

use App\Filament\Clusters\Core\Resources\ReviewResource\Pages\ViewReview;
use App\Filament\Clusters\Inventory\Pages\Inventory\Resources\StockTakeResource;
use App\Models\Inventory\StockTake;
use Filament\Actions\Action;

class ViewStockTake extends ViewReview
{
    protected static string $resource = StockTakeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('complete')
                ->visible(fn (StockTake $record) => $record->allowEdits())
                ->requiresConfirmation()
                ->action(
                    fn (StockTake $record) => $record->adjustStock()
                ),
        ];
    }
}
