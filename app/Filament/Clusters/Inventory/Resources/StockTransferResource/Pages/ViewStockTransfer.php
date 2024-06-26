<?php

namespace App\Filament\Clusters\Inventory\Resources\StockTransferResource\Pages;

use App\Concerns\HasBackRoute;
use App\Filament\Clusters\Inventory\Resources\StockTransferResource;
use App\Models\Inventory\StockTransfer;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewStockTransfer extends ViewRecord
{
    use HasBackRoute;

    protected static string $resource = StockTransferResource::class;

    protected function getHeaderActions(): array
    {
        return [
            request_review(),
            review_form(),
            Action::make('dispatch')->requiresConfirmation()
                ->visible(fn (StockTransfer $record) => $record->canDispatch())
                ->action(function (StockTransfer $record): void {
                    $record->dispatch();
                    $this->back($record);
                })
                ->icon('heroicon-o-check')
                ->label('Execute Dispatch')
                ->color('success'),
            Action::make('receive')->requiresConfirmation()
                ->visible(fn (StockTransfer $record) => $record->canReceive())
                ->action(function (StockTransfer $record): void {
                    $record->receive();
                    $this->back($record);
                })
                ->icon('heroicon-o-check')
                ->label('Receive')
                ->color('success'),
            ActionGroup::make([
                EditAction::make()->visible(fn (StockTransfer $record) => $record->allowEdits()),
                DeleteAction::make()->visible(fn (StockTransfer $record) => $record->allowEdits()),
            ]),
        ];
    }
}
