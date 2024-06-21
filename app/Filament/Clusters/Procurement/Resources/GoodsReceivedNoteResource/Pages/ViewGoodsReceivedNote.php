<?php

namespace App\Filament\Clusters\Procurement\Resources\GoodsReceivedNoteResource\Pages;

use App\Filament\Clusters\Procurement\Resources\GoodsReceivedNoteResource;
use App\Filament\Clusters\Procurement\Resources\PurchaseOrderResource;
use App\Models\Procurement\GoodsReceivedNote;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ViewRecord;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ViewGoodsReceivedNote extends ViewRecord
{
    protected static string $resource = GoodsReceivedNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                ActionGroup::make([
                    Action::make('execute-receipt')->color('success')
                        ->action(fn (GoodsReceivedNote $record, array $data) => $record->receive($data))
                        ->visible(fn (GoodsReceivedNote $record) => $record->canBeReceived())
                        ->requiresConfirmation()->icon('heroicon-o-check')->form([
                            TextInput::make('delivery_note_number')
                                ->rules('required_if:invoice_number,null|string'),
                            TextInput::make('invoice_number')
                                ->rules('required_if:delivery_note_number,null|string'),
                            // todo: move this to a secure folder
                            SpatieMediaLibraryFileUpload::make('attachments')
                                ->downloadable()->multiple()->visibility('private')
                                ->getUploadedFileNameForStorageUsing(
                                    function (TemporaryUploadedFile $file, $record): string {
                                        $prefix = $record->code . '-';

                                        return str($file->getClientOriginalName())->prepend($prefix);
                                    }
                                ),

                            //                            FileUpload::make('attachments')->multiple()->nullable()
                            //                                ->acceptedFileTypes(['application/pdf'])
                            //                                ->maxSize(10 * 2048)
                            //                                ->maxFiles(10)
                            //                                ->directory('app/Attachments/Procurement')
                            //                                ->uploadingMessage('Uploading attachment...')
                            //                                ->moveFiles()
                            //                                ->visibility('private')
                            //                                ->getUploadedFileNameForStorageUsing(
                            //                                    function (TemporaryUploadedFile $file, $record): string {
                            //                                        $prefix = $record->code.'-';
                            //
                            //                                        return str($file->getClientOriginalName())->prepend($prefix);
                            //                                    })
                            //                                ->storeFileNamesIn()
                            //                                ->preserveFilenames()
                            //                            ,
                        ]),
                    Action::make('download')->label('Download')
                        ->visible(fn (GoodsReceivedNote $record) => $record->canBeDownload())
                        ->url(fn (GoodsReceivedNote $record) => $record->downloadLink())
                        ->icon('heroicon-o-arrow-down-tray'),
                    Action::make('view-purchase-order')->icon('heroicon-o-eye')
                        ->url(fn ($record) => PurchaseOrderResource::getUrl('view', [
                            'record' => $record->purchase_order_id,
                        ])),
                ])->dropdown(false),
                Action::make('delete')->requiresConfirmation()
                    ->visible(fn (GoodsReceivedNote $record) => $record->canBeReceived())
                    ->action(fn (GoodsReceivedNote $record) => $record->deleteGrn())
                    ->icon('heroicon-o-trash')->color('danger'),
            ]),
        ];
    }
}
