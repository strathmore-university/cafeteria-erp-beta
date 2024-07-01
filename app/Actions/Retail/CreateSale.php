<?php

namespace App\Actions\Retail;

use App\Models\Core\Wallet;
use App\Models\Retail\Sale;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;

class CreateSale
{
    public function execute(
        Collection $items,
        Collection $payments,
        ?Wallet $wallet = null,
    ): Sale {
        $totalTendered = $payments->sum('tendered_amount');
        $saleValue = $items->sum('price');

        $narration = build_string([
            'Cash sale worth ' . Number::spell($saleValue) .
            ' shillings by cashier:' . auth()->user()->name,
        ]);

        if (filled($wallet)) {
            $narration .= ' for customer:' . $wallet->getAttribute('name');
        }

        return Sale::create([
            'retail_session_id' => retail_session()->id,
            'customer_type' => $wallet?->owner_type,
            'tendered_amount' => $totalTendered,
            'customer_id' => $wallet?->owner_id,
            'sale_value' => $saleValue,
            'narration' => $narration,
            'cashier_id' => auth_id(),
        ]);
    }
}
