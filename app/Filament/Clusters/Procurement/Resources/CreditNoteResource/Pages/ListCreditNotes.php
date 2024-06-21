<?php

namespace App\Filament\Clusters\Procurement\Resources\CreditNoteResource\Pages;

use App\Filament\Clusters\Procurement\Resources\CreditNoteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCreditNotes extends ListRecords
{
    protected static string $resource = CreditNoteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
