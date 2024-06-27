<?php

namespace App\Filament\Clusters\Production\Resources\ProductDispatchResource\Pages;

use App\Concerns\HasBackRoute;
use App\Filament\Clusters\Production\Resources\ProductDispatchResource;
use App\Models\Production\ProductDispatch;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewProductDispatch extends ViewRecord
{
    use HasBackRoute;

    protected static string $resource = ProductDispatchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            request_review(),
            review_form(),
            Action::make('dispatch')->requiresConfirmation()
                ->visible(fn (ProductDispatch $record) => $record->canDispatch())
                ->action(function (ProductDispatch $record): void {
                    $record->dispatch();
                    $this->back($record);
                })
                ->icon('heroicon-o-check')
                ->label('Execute Dispatch')
                ->color('success'),
            Action::make('receive')->requiresConfirmation()
                ->visible(fn (ProductDispatch $record) => $record->canReceive())
                ->action(function (ProductDispatch $record): void {
                    $record->receive();
                    $this->back($record);
                })
                ->icon('heroicon-o-check')
                ->label('Receive')
                ->color('success'),
            ActionGroup::make([
                EditAction::make()->visible(fn (ProductDispatch $dispatch) => $dispatch->allowEdits()),
                DeleteAction::make()->visible(fn (ProductDispatch $dispatch) => $dispatch->allowEdits()),
            ]),
        ];
    }
}
