<?php

namespace App\Filament\Clusters\Accounting\Resources\PaymentModesResource\Pages;

use App\Filament\Clusters\Accounting\Resources\PaymentModesResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditPaymentModes extends EditRecord
{
    protected static string $resource = PaymentModesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
