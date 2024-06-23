<?php

namespace App\Filament\Clusters\Production\Resources\FoodOrderResource\Pages;

use App\Concerns\HasBackRoute;
use App\Filament\Clusters\Production\Resources\FoodOrderResource;
use App\Models\Production\FoodOrder;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;

class ViewFoodOrder extends ViewRecord
{
    use HasBackRoute;

    protected static string $resource = FoodOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('request_ingredients')
                ->visible(fn(FoodOrder $record) => $record->canRequestIngredients())
                ->icon('heroicon-o-inbox-arrow-down')
                ->requiresConfirmation()->button()
                ->action(function (FoodOrder $record): void {
                    $record->requestIngredients();
                    $this->back($record);
                }),
            Action::make('populate-dispatch')
                ->visible(fn(FoodOrder $record) => $record->canPopulateDispatch())
                ->icon('heroicon-o-bolt')->requiresConfirmation()->button()
                ->action(function (FoodOrder $record): void {
                    $record->populateDispatch();
                    $this->back($record);
                }),
            Action::make('execute-dispatch')->requiresConfirmation()
                ->action(fn(FoodOrder $record) => $record->executeIngredientDispatch())
                ->visible(fn(FoodOrder $record) => $record->canExecuteDispatch())
                ->icon('heroicon-o-check')->color('success')->button(),
            Action::make('initiate-preparation')->button()
                ->visible(fn(FoodOrder $record) => $record->canBeInitiated())
                ->icon('heroicon-o-sparkles')->requiresConfirmation()
                ->action(function (FoodOrder $record) {
                    $record->initiate();
                    $this->back($record);
                }),
            Action::make('record-remaining-stock')
                ->visible(fn(FoodOrder $record) => $record->canRecordRemainingStock())
                ->url(fn(FoodOrder $record) => $record->remainingStockUrl())
                ->icon('heroicon-o-pencil-square')->button(),
            Action::make('record-by-products')
                ->visible(fn(FoodOrder $record) => $record->canRecordByProductsStock())
                ->url(fn(FoodOrder $record) => $record->recordByProductUrl())
                ->icon('heroicon-o-pencil-square')->button(),
            Action::make('complete')->color('success')
                ->visible(fn(FoodOrder $record) => $record->canBeCompleted())
                ->action(fn(FoodOrder $record, array $data) => $record->complete($data))
                ->icon('heroicon-o-check')->button()->form([
                    TextInput::make('produced_portions')
                        ->required()->numeric()
                ]),


            //            Action::make('dispatch-products')
            //                ->requiresConfirmation()
            //                ->button(),
            //            Action::make('receive')
            //                ->requiresConfirmation()
            //                ->button(),
            //            EditAction::make(), // todo: allow chef to edit? before ingredients are requested
        ];
    }
}
