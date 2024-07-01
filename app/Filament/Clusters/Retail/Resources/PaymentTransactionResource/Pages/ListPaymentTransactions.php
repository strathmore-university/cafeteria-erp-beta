<?php

namespace App\Filament\Clusters\Retail\Resources\PaymentTransactionResource\Pages;

use App\Filament\Clusters\Retail\Resources\PaymentTransactionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPaymentTransactions extends ListRecords
{
    protected static string $resource = PaymentTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
