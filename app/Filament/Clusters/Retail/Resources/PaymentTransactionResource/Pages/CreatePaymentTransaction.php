<?php

namespace App\Filament\Clusters\Retail\Resources\PaymentTransactionResource\Pages;

use App\Filament\Clusters\Retail\Resources\PaymentTransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreatePaymentTransaction extends CreateRecord
{
    protected static string $resource = PaymentTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
