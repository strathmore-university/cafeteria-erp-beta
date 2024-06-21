<?php

namespace App\Filament\Clusters\Procurement\Resources\CreditNoteResource\Pages;

use App\Filament\Clusters\Procurement\Resources\CreditNoteResource;
use App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource;
use App\Models\Procurement\CreditNote;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Resources\Pages\ViewRecord;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ViewCreditNote extends ViewRecord
{
    protected static string $resource = CreditNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                ActionGroup::make([
                    Action::make('execute')->color('success')
                        ->action(fn (CreditNote $record, array $data) => $record->issueCrn())
                        ->visible(fn (CreditNote $record) => $record->canBeIssued())
                        ->requiresConfirmation()->icon('heroicon-o-check')->form([
                            // todo: move this to a secure folder
                            SpatieMediaLibraryFileUpload::make('attachments')
                                ->downloadable()->multiple()->visibility('private')
                                ->getUploadedFileNameForStorageUsing(
                                    function (TemporaryUploadedFile $file, $record): string {
                                        $prefix = $record->code.'-';

                                        return str($file->getClientOriginalName())->prepend($prefix);
                                    }
                                ),
                        ]),
                    Action::make('download')->label('Download')
                        ->visible(fn (CreditNote $record) => $record->preventEdit())
                        ->url(fn (CreditNote $record) => $record->downloadLink())
                        ->icon('heroicon-o-arrow-down-tray'),
                    Action::make('view-purchase-order')->icon('heroicon-o-eye')
                        ->url(fn($record) => PurchaseOrderResource::getUrl('view', [
                            'record' => $record->purchase_order_id,
                        ])),
                ])->dropdown(false),
                Action::make('delete')->requiresConfirmation()
                    ->visible(fn (CreditNote $record) => $record->canBeIssued())
                    ->action(fn (CreditNote $record) => $record->deleteCrn())
                    ->icon('heroicon-o-trash')->color('danger'),
            ]),
        ];
    }
}
