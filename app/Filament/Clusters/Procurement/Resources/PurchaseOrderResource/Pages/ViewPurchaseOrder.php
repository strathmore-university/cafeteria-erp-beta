<?php

namespace App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource\Pages;

use App\Filament\Clusters\Procurement\Resources\GoodsReceivedNoteResource;
use App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;

class ViewPurchaseOrder extends ViewRecord
{
    protected static string $resource = PurchaseOrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()->visible(fn ($record) => $record->allowEdits()),
            DeleteAction::make()->visible(fn ($record) => $record->allowEdits()),
            Action::make('submit_for_review')
                ->label('Submit for review')
                ->requiresConfirmation()
                ->visible(fn ($record) => $record->canBeSubmittedForReview())
                ->action(fn ($record) => $record->requestReview()),
            Action::make('review')->label('Submit review')
                ->form([
                    TextInput::make('comment'),
                    Radio::make('status')->default('approve')
                        ->columns(3)
                        ->options([
                            'approved' => 'Approve',
                            'rejected' => 'Reject',
                            'returned' => 'Return',
                        ]),
                ])
                ->action(fn ($record, $data) => $record->submitReview($data))
                ->visible(fn ($record) => $record->canBeReviewed())
                ->requiresConfirmation(),
            Action::make('receive')->requiresConfirmation()
                ->button()
                ->visible(fn ($record) => $record->canBeDownload())
                ->action(function ($record): void {
                    $grn = $record->fetchOrCreateGrn();
                    redirect(GoodsReceivedNoteResource::getUrl('view', ['record' => $grn]));
                })
//                    ->authorize('receive')
                ->color('success')
                ->icon('heroicon-o-shopping-cart'),
            Action::make('download-lpo')
                ->label('Download LPO')
                ->color('success')
                ->visible(fn ($record) => $record->canBeDownload())
                ->url(fn ($record) => route('download.purchase-order', ['purchaseOrder' => $record])),
        ];
    }
}
