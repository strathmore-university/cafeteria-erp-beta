<?php

namespace App\Filament\Clusters\Production\Resources\FoodOrderResource\Pages;

use App\Filament\Clusters\Production\Resources\FoodOrderResource;
use App\Models\Production\FoodOrder;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;

class ViewFoodOrder extends ViewRecord
{
    protected static string $resource = FoodOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('request_ingredients')
                ->action(fn (FoodOrder $record) => $record->requestIngredients())
                ->visible(fn ($record) => $record->canRequestIngredients())
                ->requiresConfirmation()
                ->button(),
            Action::make('populate-dispatch')->requiresConfirmation()
                ->visible(fn (FoodOrder $record) => $record->canPopulateDispatch())
                ->action(fn (FoodOrder $record) => $record->populateDispatch())
                ->button(),
            Action::make('execute-dispatch')->requiresConfirmation()
                ->action(fn (FoodOrder $record) => $record->executeIngredientDispatch())
                ->visible(fn (FoodOrder $record) => $record->canExecuteDispatch())
                ->button(),
            //            Action::make('initiate-preparation')
            //                ->requiresConfirmation()
            //                ->action(fn (FoodOrder $record) => $record->initiate())
            //                ->visible(fn (FoodOrder $record) => $record->has_dispatched_ingredients) // todo: change! add a bool column to indicate that dispatch was done and should allow initiation with or without adequate stock
            //                ->button(),
            //            Action::make('complete-preparation')
            //                ->requiresConfirmation()
            //                ->button(),
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
