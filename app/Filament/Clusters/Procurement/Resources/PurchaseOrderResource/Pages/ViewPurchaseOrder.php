<?php

namespace App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource\Pages;

use App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource;
use App\Models\Procurement\PurchaseOrder;
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
            Action::make('submit_for_review')->requiresConfirmation()
                ->visible(fn (PurchaseOrder $record) => $record->canBeSubmittedForReview())
                ->action(fn (PurchaseOrder $record) => $record->requestReview())
                ->label('Submit for review'),
            Action::make('review')->label('Submit review')
                ->form([
                    TextInput::make('comment')->label('comments')
                        ->required()->string()->maxLength(255),
                    Radio::make('status')->default('approve')
                        ->required()->columns(3)->options([
                            'approved' => 'Approve',
                            'rejected' => 'Reject',
                            'returned' => 'Return',
                        ]),
                ])
                ->action(fn (PurchaseOrder $record, $data) => $record->submitReview($data))
                ->visible(fn (PurchaseOrder $record) => $record->canBeReviewed())
                ->requiresConfirmation(),
            Action::make('receive')->requiresConfirmation()->button()
                ->visible(fn (PurchaseOrder $record) => $record->canBeReceived())
                ->action(function ($record): void {
                    redirect(get_record_url($record->fetchGrn()));
                })
                ->icon('heroicon-o-truck'),
            Action::make('generate-credit-note')->requiresConfirmation()
                ->action(function (PurchaseOrder $record): void {
                    redirect(get_record_url($record->generateCrn()));
                })
                ->visible(fn (PurchaseOrder $record) => $record->canGeneratedCrn())
                ->icon('heroicon-o-receipt-percent'),
            Action::make('download')
                ->visible(fn (PurchaseOrder $record) => $record->canBeDownloaded())
                ->url(fn (PurchaseOrder $record) => $record->downloadLink())
                ->icon('heroicon-o-arrow-down-tray'),
        ];
    }
}
