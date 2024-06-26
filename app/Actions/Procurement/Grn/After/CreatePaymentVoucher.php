<?php

namespace App\Actions\Procurement\Grn\After;

use App\Models\Procurement\GoodsReceivedNote;
use Illuminate\Support\Collection;

class CreatePaymentVoucher
{
    public function execute(
        GoodsReceivedNote $grn,
        Collection $items
    ): void {
        // todo: create payment voucher
    }
}
