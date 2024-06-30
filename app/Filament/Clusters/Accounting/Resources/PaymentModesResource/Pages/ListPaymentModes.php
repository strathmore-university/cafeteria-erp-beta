<?php

namespace App\Filament\Clusters\Accounting\Resources\PaymentModesResource\Pages;

use App\Filament\Clusters\Accounting\Resources\PaymentModesResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPaymentModes extends ListRecords
{
    protected static string $resource = PaymentModesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
