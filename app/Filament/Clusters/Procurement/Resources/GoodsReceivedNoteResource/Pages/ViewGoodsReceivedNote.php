<?php

namespace App\Filament\Clusters\Procurement\Resources\GoodsReceivedNoteResource\Pages;

use App\Filament\Clusters\Procurement\Resources\GoodsReceivedNoteResource;
use App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource;
use App\Models\Procurement\GoodsReceivedNote;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;

class ViewGoodsReceivedNote extends ViewRecord
{
    protected static string $resource = GoodsReceivedNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('view-purchase-order')
                ->url(fn ($record) => PurchaseOrderResource::getUrl('view', ['record' => $record->purchase_order_id])),
            Action::make('edit')
                ->fillForm(fn ($record) => $record->only(['delivery_note_number', 'invoice_number']))
                ->visible(fn ($record) => $record->allowEdits())
                ->requiresConfirmation()
                ->form([
                    TextInput::make('delivery_note_number')->string(),
                    TextInput::make('invoice_number')
                        ->string()
                        ->afterStateUpdated(fn (GoodsReceivedNote $record) => $record->invoiced_at = now()),
                ])->action(function (GoodsReceivedNote $record, array $data): void {
                    $record->delivery_note_number = $data['delivery_note_number'];
                    $record->invoice_number = $data['invoice_number'];

                    if (filled($record->invoice_number)) {
                        $record->invoiced_at = now();
                    }

                    $record->update();

                    $url = GoodsReceivedNoteResource::getUrl('view', [
                        'record' => $record->id,
                    ]);

                    $this->redirect($url, true);
                }),
            Action::make('execute-receipt')
                ->action(fn ($record) => $record->receive())
                ->visible(fn ($record) => $record->canBeReceived())
                ->color('success'),
        ];
    }
}
