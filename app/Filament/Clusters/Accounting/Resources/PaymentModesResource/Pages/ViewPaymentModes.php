<?php

namespace App\Filament\Clusters\Accounting\Resources\PaymentModesResource\Pages;

use App\Filament\Clusters\Accounting\Resources\PaymentModesResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPaymentModes extends ViewRecord
{
    protected static string $resource = PaymentModesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
