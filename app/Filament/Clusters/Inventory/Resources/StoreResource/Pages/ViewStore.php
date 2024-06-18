<?php

namespace App\Filament\Clusters\Inventory\Resources\StoreResource\Pages;

use App\Filament\Clusters\Core\Resources\ReviewResource\Pages\ViewReview;
use App\Filament\Clusters\Inventory\Resources\StoreResource;
use App\Models\Inventory\Store;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;

class ViewStore extends ViewReview
{
    protected static string $resource = StoreResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('perform-stock-take')
                ->requiresConfirmation()
                ->form([
                    TextInput::make('description')
                        ->label('Description')
                        ->nullable(),
                ])
                ->action(function (Store $record): void {
                    redirect(get_record_url($record->performStockTake()));
                }),
            EditAction::make()->visible(fn ($record) => $record->canBeModified()),
        ];
    }
}
