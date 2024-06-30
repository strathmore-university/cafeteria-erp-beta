<?php

namespace App\Actions\Retail;

use App\Models\Retail\Sale;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Number;

class CreateSale
{
    public function execute(
        Collection $items,
        Collection $payments,
        ?User $user = null,
    ): Sale {
        $totalTendered = $payments->sum('tendered_amount');
        $saleValue = $items->sum('price');

        $narration = build_string([
            'Cash sale worth ' . Number::spell($saleValue) .
            ' shillings by cashier:' . auth()->user()->name,
        ]);

        if (filled($user)) {
            $narration .= ' for customer:' . $user->name;
        }

        return Sale::create([
            'retail_session_id' => retail_session()->id,
            'customer_type' => $user?->getMorphClass(),
            'tendered_amount' => $totalTendered,
            'customer_id' => $user?->getKey(),
            'sale_value' => $saleValue,
            'narration' => $narration,
            'cashier_id' => auth_id(),
        ]);
    }
}
