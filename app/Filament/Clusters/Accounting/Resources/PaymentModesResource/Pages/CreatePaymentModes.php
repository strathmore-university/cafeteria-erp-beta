<?php

namespace App\Filament\Clusters\Accounting\Resources\PaymentModesResource\Pages;

use App\Filament\Clusters\Accounting\Resources\PaymentModesResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentModes extends CreateRecord
{
    protected static string $resource = PaymentModesResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
